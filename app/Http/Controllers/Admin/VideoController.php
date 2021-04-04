<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Video;
use Carbon\Carbon;
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, $oldCategory = null)
    {

        // noch überarbeiten, je nachdem, wie das Video Model am Ende aussieht!
        $videos = [];
        $categoryList = Category::all();

        if($request->filled('category')) {
            $videos = Video::where('category_id', $request['category'])->get();
            $request->flash();
        } else if ($oldCategory) {
            $videos = Video::where('category_id', $oldCategory)->get();
        }

        return view('admin.videos.index')
            ->with('videos', $videos)
            ->with('categories', $categoryList)
            ->with('oldCategory', $oldCategory)
            ;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {

        $categories = Category::all();
        return view('admin.videos.create')
            ->with('categories', $categories)
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
            'video' => 'mimes:mp4',
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
        $video->tag = $request['tag'];
        $video->category_id = $request['category'];
        $video->video = $videoNameToStore;
        $video->timelapse = $timelapse;
        $video->user_id = auth()->user()->id;
        $video->save();

        return redirect()->route('admin.videos.index', $video->category->id)->with('success', 'Video created');
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
        $video = Video::findOrFail($id);
        return view('admin.videos.show')
            ->with('video', $video);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $categories = Category::all();
        $video = Video::findOrFail($id);
        return view('admin.videos.edit')
            ->with('video', $video)
            ->with('categories', $categories);
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
        $video->tag = $request['tag'];
        $video->category_id = $request['category'];
        // Handle file upload
        if ($request->hasFile('video')) {
            $resultArray = $this->handleVideo($request->file('video'));
            $video->video = $resultArray['videoNameToStore'];
            $video->timelapse = $resultArray['timelapse'];
        }
        $video->user_id = auth()->user()->id;

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
}
