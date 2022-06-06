<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Google;
use App\Http\Controllers\Controller;
use App\Video;
use Carbon\Carbon;
use Cviebrock\EloquentTaggable\Models\Tag;
use FFMpeg\Format\Video\WebM;
use FFMpeg\Format\Video\X264;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Illuminate\Support\Facades\Session;


class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param null $oldCategory
     * @return Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if (filled(session('url'))

                && blank($request['category'])
                && blank($request['progress_index'])
                && blank($request['page'])
                && blank($request['search'])
                && blank($request['resetSearch'])
            ){

            return redirect()->route('admin.videos.index', session('url'))
                ->with('success', Session::get('success'))
                ->with('error', Session::get('error'));
        }

        $url = [];

        $selectedCategory = $request['category'];
        $selectedProgress = $request['progress_index'];
        $categoryList = Category::all();

        // Get an instance of the videos relationship of the current authenticated user
        // Eager load the videocreator relationship
        $videoQuery = Video::with(['users' => function ($query){
            $query->where('users.id', '=', auth()->user()->id);
            }])->with('videocreator');

        // If category search is required
        if($request->filled('category')) {
            // Reset session in every request case where this key is set
            $url['category'] = $selectedCategory;
            if($request->input('category') !== 'all') {
                $videoQuery = $videoQuery->where('category_id', $request['category']);
            }
        }

        // If progress search is required
        if($request->filled('progress_index')){
            $url['progress_index'] = $selectedProgress;
            if($request->input('progress_index') !== 'all') {
                // Constrain the query : only get the videos with progress_index equal to $selectedProgress
                $videoQuery = $videoQuery->whereHas('users', function ($query) use ($selectedProgress) {
                    $query->where('progress_index', '=', $selectedProgress)->where('users.id', '=', auth()->user()->id);
                });
            }
        }

        // Full text search for fields mentioned in Video model
        if ($request->filled('search')) {
            $url['search'] = $request['search'];
            $videoQuery = $videoQuery->search($request->get('search'));
            $request->flash();
        }

        // To remember on what page you where if you are coming from a single video
        // Is there a way to get that alternating page=x variable out of the system?
        #dd(session()->all(),$url, $request);
        if ($request->filled('page')) {
            $url['page'] = $request['page'];
        }

        session(['url' => $url]);
        $targetURL = '?';
        if (filled(session('url'))){
            $sessionURL = session('url');

            foreach($sessionURL as $key => $item){
                $targetURL = $targetURL.'&'.$key.'='.$item;
            }
        }

        $videos = $videoQuery->groupBy('videos.id')->latest()->get();
        $videos = $this->paginate($videos, 6, null, ['path'=>url('admin/videos'.$targetURL)]);

        return view('admin.videos.index')
            ->with('videos', $videos)
            ->with('categories', $categoryList)
            ->with('selectedCategory', $selectedCategory)
            ->with('selectedProgress', $selectedProgress)
            ;
    }

    /**
     * Create a paginator after your liking.
     *
     * @return LengthAwarePaginator
     */
    protected function paginate($items, $perPage, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        if ($items->count()/$perPage > $page){
            session()->forget('page');
        }

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $taglist = Tag::all();
        $categories = Category::all();
        return view('admin.videos.create')
            ->with('categories', $categories)
            ->with('tags', $taglist)
            ;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'category' => 'integer',
            'video' => 'sometimes|mimes:mp4',
        ]);

        // Create post
        $video = new Video();
        $video->title = $request['title'];
        $video->description = $request['description'];
        $video->category_id = $request['category'];

        // If tts is set get the .mp3 from Google and store it on disk
        if ($request['tts']) {
            // Call tts() and send the data to Google and get an .mp3 back
            $TTSfileName = $this->tts($request['title']);
            $countCharacters = mb_strlen($request['title']);
            // Save the number of characters you used on the Google API for this run
            $googleBill = new Google();
            $googleBill->characters = $countCharacters;
            $googleBill->save();
            $video->sound = $TTSfileName;
        }

        // Handle file upload
        if ($request->hasFile('video')){
            $resultArray = $this->handleVideo($request->file('video'));
            $video->video = $resultArray['videoNameToStore'];
            $video->timelapse = $resultArray['timelapse'];
            $video->duration = $resultArray['duration'];
        }

        $video->create_user_id = auth()->user()->id;

        $video->save();

        // Handle tags
        if ($request['tags']) {
            $video->tag($request['tags']);
        }
        $video->users()->attach(auth()->user(), ['progress_index' => NULL]);
        $video->save();

        return redirect()->route('admin.videos.index')->with('success', 'Video created');
    }

    /**
     * Handle video files.
     *
     * @param  int  $id
     * @return string[]
     */
    public function handleVideo($video)
    {
        $dateTime = (new Carbon())->format('Ymd_His');
        // Get filename with the extension
        $filenameWithExt = $video->getClientOriginalName();
        // Get just filename
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
        // Get just extension
        $extension = $video->getClientOriginalExtension();
        // Filenamebase for storing the data
        $filenameBaseToStore = $filename.'_'.$dateTime;
        $videoNameToStore = $filenameBaseToStore.'.'.$extension;
        $shortVideoNameToStore = 'short_'.$filenameBaseToStore.'.webm';
        // Upload video
        $path = $video->storeAs('public/videos', $videoNameToStore);

        // Create time-lapse from video
        $media = FFMpeg::fromDisk('videos')->open($videoNameToStore);
        $durationInSeconds = $media->getDurationInSeconds();
        $y = 0;

        for ($i = 1; $i < $durationInSeconds; $i+=4){
            $y++;
            $media = $media->getFrameFromSeconds($i)
                ->export()
                ->toDisk('temp')
                ->save($filenameBaseToStore.'-'.$y.'.png');
        }

        FFMpeg::fromDisk('temp')
            ->open($filenameBaseToStore.'-%d.png')
            ->export()
            ->toDisk('videos')
            ->asTimelapseWithFramerate(2)
            ->addFilter('-r', 30)
            ->inFormat(new WebM())
            ->save($shortVideoNameToStore);

        // Clean temp folder
        Storage::delete(Storage::files('public/temp'));

        return array(
            'timelapse' => $shortVideoNameToStore,
            'videoNameToStore' => $videoNameToStore,
            'duration' => $durationInSeconds,
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $user = auth()->user();
        $progress_index = null;

        foreach ($user->videos as $video)
        {
            if ($video->id == $id){
                $progress_index = $video->pivot->progress_index;
            }
        }

        $video = Video::findOrFail($id);

        return view('admin.videos.show')
            ->with('video', $video)
            ->with('progress_index', $progress_index);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $video = Video::findOrFail($id);
        $tagsMerged = Tag::all()->diff($video->tags);
        $categories = Category::all();

        return view('admin.videos.edit')
            ->with('video', $video)
            ->with('categories', $categories)
            ->with('tagsOption', $tagsMerged);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Video $video
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Video $video)
    {
        $this->validate($request, [
            'title' => 'required',
            'category' => 'integer',
            'video' => 'sometimes|mimes:mp4',
        ]);

        // Update post
        $video->title = $request['title'];
        $video->description = $request['description'];
        $video->category_id = $request['category'];

        // If tts is set get the .mp3 from Google and store it on disk
        if ($request['tts']) {
            // Call tts() and send the data to Google and get an .mp3 back
            $TTSfileName = $this->tts($request['title']);
            $countCharacters = mb_strlen($request['title']);
            // Save the number of characters you used on the Google API for this run
            $googleBill = new Google();
            $googleBill->characters = $countCharacters;
            $googleBill->save();
            // Check if old sound can be deleted
            if ($video->sound){
                $this->checkSound($video);
            }
            $video->sound = $TTSfileName;
        }

        // Handle file upload
        if ($request->hasFile('video')) {
            // First delete the video files stored before
            Storage::delete('public/videos/'.$video->video);
            Storage::delete('public/videos/'.$video->timelapse);
            // Then handle the new ones
            $resultArray = $this->handleVideo($request->file('video'));
            $video->video = $resultArray['videoNameToStore'];
            $video->timelapse = $resultArray['timelapse'];
        }
        $video->create_user_id = auth()->user()->id;

        // Handle tags
        if ($request['tags']) {
            $video->retag($request['tags']);
        } else {
            $video->detag();
        }
        $video->save();

        return redirect()->route('admin.videos.show', $video->id)->with('success', 'Video updated');
    }

    /**
     * Contact Google Cloud TextToSpeech API.
     *
     * @return string
     * @throws \Google\ApiCore\ApiException
     */
    public function tts($word)
    {
        // Check, if TTS is really necessary
        $soundFileName = str_replace(' ', '_',$word.'.mp3');
        $checkDB = Video::where('sound', $soundFileName)->count();
        if ($checkDB === 0) {
            // Check if this month TTS is still possible
            $maxCharacters = 240000;
            $actualYear = date('Y');
            $actualMonth = date('m');
            $googleBillsum = Google::where(DB::raw("YEAR(googles.created_at)"),$actualYear)
                ->where(DB::raw("MONTH(googles.created_at)"),$actualMonth)
                ->sum('characters');

            if ($googleBillsum > $maxCharacters) {
                return back()->with('error', 'TTS not possible in this month! You reached the treshold of '.$maxCharacters.'! Wait one month with more TTS adventures!');
            }

            try {
                $textToSpeechClient = new TextToSpeechClient(['credentials' => env('GOOGLE_APPLICATION_CREDENTIALS')]);

                $input = new SynthesisInput();
                $input->setText($word);
                $voice = new VoiceSelectionParams();
                $voice->setLanguageCode('es-ES');

                // optional language setting
                $voice->setName('es-ES-Wavenet-D');

                $audioConfig = new AudioConfig();
                $audioConfig->setAudioEncoding(AudioEncoding::MP3);

                $resp = $textToSpeechClient->synthesizeSpeech($input, $voice, $audioConfig);

                $path = Storage::put('public/sounds/' . $soundFileName, $resp->getAudioContent());

                $textToSpeechClient->close();

                return $soundFileName;

            } catch (\Exception $e) {

                return back()->with('error', $e->getMessage());
            }
        } else {
            return $soundFileName;
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Video $video
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Video $video)
    {
        // Delete the video files and if present and only single used also the sound file
        Storage::delete('public/videos/'.$video->video);
        Storage::delete('public/videos/'.$video->timelapse);
        if ($video->sound){
            $this->checkSound($video);
        }
        $video->delete();

        return redirect()->route('admin.videos.index')->with('success', 'Video deleted');

    }

    /**
     * Save the rating of a video to storage.
     *
     * @param Request $request
     * @return string
     */
    public function rate(Request $request)
    {
        $user = auth()->user();

        foreach ($user->videos as $video)
        {
            if ($video->id == $request['videoId'] && $video->pivot->progress_index != $request['progressIndex']){
                $user->videos()->updateExistingPivot($video->id, ['progress_index' => $request['progressIndex']]);
                return 'Progress_index updated! ';
            }
        }

        $video = Video::findOrFail($request['videoId']);
        $video->users()->attach($user, ['progress_index' => $request['progressIndex']]);

        return 'Created new progress_index! ';

    }

    /**
     * Clear the session via Ajax if reset_search button was hit
     *
     * @return string
     */
    public function resetSearch()
    {
        session()->forget('progress_index');
        session()->forget('category');
        session()->forget('page');
        session()->forget('search');
        session()->forget('url');

        return 'SearchSession cleared';
    }

    /**
     * Hand the client one mp3 file together with the duration of the connected video
     *
     * @return array
     */
    public function fetchElement(Request $request)
    {
        try {
            // return only the exact requested video information (for sound replay)
            if ($request['mode'] == 'target') {
                $returnedData = Video::where('id', '=', $request['videoId'])->get();
            } else {
                // fetch random video from the ones, which have at least 5 stars ("T")
                $returnedData = Video::with(['users' => function ($query) {
                    $query->where('users.id', '=', auth()->user()->id);
                }])
                    ->whereHas('users', function ($query) {
                        $query->where('progress_index', '>', 4)->where('users.id', '=', auth()->user()->id);
                    })
                    ->whereNotNull('sound')->whereNotNull('duration')->inRandomOrder()->take($request['limit'])->get();


            }
            $dataArray = array();
            foreach ($returnedData as $key => $video){
                $dataArray[$key]['videoPath'] = $video->video;
                $dataArray[$key]['videoId'] = $video->id;
                $dataArray[$key]['soundPath'] = $video->sound;
                $dataArray[$key]['duration'] = $video->duration;
                $dataArray[$key]['title'] = $video->title;
            }

            return  $dataArray;

        } catch (\Exception $e) {
            return $e[0]->getMessage();
        }
    }

    /**
     * Check if the sound file for a video can be deleted, or not
     *
     * @return bool
     */
    public function checkSound($video){
        $soundToBeDeleted = $video->sound;
        $soundCount = Video::where('sound', $soundToBeDeleted)->count();
        if ($soundCount === 1) {
            Storage::delete('public/sounds/' . $soundToBeDeleted);
            return true;
        }
        return false;
    }
}
