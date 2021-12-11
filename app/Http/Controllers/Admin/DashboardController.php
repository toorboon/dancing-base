<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\User;
use App\Video;
use Cviebrock\EloquentTaggable\Models\Tag;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $categories = Category::all();
        $users = User::with('role')->get();
        $tags = Tag::all();
        return view('admin.dashboard')
            ->with('categories', $categories)
            ->with('users', $users)
            ->with('tags', $tags)
            ;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Tag $tag
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Tag $tag)
    {
        // Instantiate the service (can also be done via dependency injection)
        $tagService = app(\Cviebrock\EloquentTaggable\Services\TagService::class);

        $tagService ->renameTags($tag->name, $request['tag_name']);
        return redirect()->route('admin.dashboard.index')->with('success', 'Tag updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Tag $tag
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Tag $tag)
    {
        $taggedVideos = Video::withAnyTags($tag->name)->get();
        foreach ($taggedVideos as $taggedvideo) {
            $taggedvideo->untag($tag->name);
        }
        $tag->delete();

        return redirect()->route('admin.dashboard.index')->with('success', 'Tag deleted');
    }
}
