<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Rating;
use App\Video;
use Carbon\Carbon;
use Cviebrock\EloquentTaggable\Models\Tag;
use FFMpeg\Format\Video\WebM;
use FFMpeg\Format\Video\X264;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;


class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param null $oldCategory
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ((filled(session('selectedCategory')) || filled(session('selectedProgress'))) && (blank($request['category']) && blank($request['progress_index']) && blank($request['search']) && blank($request['resetSearch']))){
            $selectedCategory = session('selectedCategory');
            $selectedProgress = session('selectedProgress');

            return redirect()->route('admin.videos.index', ['category' => $selectedCategory, 'progress_index' => $selectedProgress] );
        }

        $selectedCategory = $request['category'];
        $selectedProgress = $request['progress_index'];

        $categoryList = Category::all();

        // Get an instance of the videos relationship of the current authenticated user
        // Eager load the videocreator relationship
        $videoQuery = Video::with(['users' => function ($query){
            $query->where('users.id', '=', auth()->user()->id);
            }])->with('videocreator');

        // If category search is required
        if($request->filled('category') && $request['category'] !== 'all') {
            session(['selectedCategory' => $selectedCategory]);
            $videoQuery = $videoQuery->where('category_id', $request['category']);
        }

        // If progress search is required
        if($request->filled('progress_index') && $request->input('progress_index') !== 'all'){
            session(['selectedProgress' => $selectedProgress]);
            // Constrain the query : only get the videos with progress_index equal to $selectedProgress
            $videoQuery = $videoQuery->whereHas('users', function($query) use ($selectedProgress) {
                $query->where('progress_index', '=', $selectedProgress)->where('users.id', '=', auth()->user()->id);
                });
        }

        // Full text search for fields mentioned in Video model
        if ($request->filled('search')) {
            $videoQuery = $videoQuery->search($request->get('search'));
            $request->flash();
        }

        $videos = $videoQuery->latest()->get();

//        $videos = $videos->paginate(1);

        return view('admin.videos.index')
            ->with('videos', $videos)
            ->with('categories', $categoryList)
            ->with('selectedCategory', $selectedCategory)
            ->with('selectedProgress', $selectedProgress)
            ;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
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
            'category' => 'required|integer',
            'video' => 'sometimes|mimes:mp4',
        ]);

        // Handle file upload
        if ($request->hasFile('video')){
            $resultArray = $this->handleVideo($request->file('video'));
            $videoNameToStore = $resultArray['videoNameToStore'];
            $timelapse = $resultArray['timelapse'];
        } else {
            $videoNameToStore = 'novideo.jpg';
            $timelapse = 'novideo.jpg';
        }



        // Create post
        $video = new Video();
        $video->title = $request['title'];
        $video->description = $request['description'];

        $video->category_id = $request['category'];
        $video->video = $videoNameToStore;
        $video->timelapse = $timelapse;
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
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
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
            'category' => 'required|integer',
            'video' => 'sometimes|mimes:mp4',
        ]);

        // Update post
        $video->title = $request['title'];
        $video->description = $request['description'];
        $video->category_id = $request['category'];
        // Handle file upload
        if ($request->hasFile('video')) {
            $resultArray = $this->handleVideo($request->file('video'));
            $video->video = $resultArray['videoNameToStore'];
            $video->timelapse = $resultArray['timelapse'];
        }
        $video->create_user_id = auth()->user()->id;
        // Handle tags
        if ($request['tags']) {
            $video->retag($request['tags']);
        }
        $video->save();

        return redirect()->route('admin.videos.show', $video->id)->with('success', 'Video updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Video $video
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Video $video)
    {
        // Delete also the video files
        Storage::delete('public/videos/'.$video->video);
        Storage::delete('public/videos/'.$video->timelapse);

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
     * @param Request $request
     * @return string
     */
    public function resetSearch()
    {
        session()->forget('selectedCategory');
        session()->forget('selectedProgress');

        return 'SearchSession cleared';
    }
}
