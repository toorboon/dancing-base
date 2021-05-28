<?php

namespace App\Http\Controllers;

use App\User;
use App\Video;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Show the index Welcome page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
        return view('pages.index');
    }

    /**
     * Show the about page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function about(){
        $videosWithUsers = Video::with('videocreator')->get();
        $creators = [];
        foreach($videosWithUsers as $user){
            array_push($creators, $user->videocreator->name);
        }
        $creators = array_unique($creators);

        // add Role the moment you introduced a simple role system to the app
        // $adminRole = Role::where('name', 'Admin')->pluck('id');
        $responsibleAdmin = User::first();

        return view('pages.about')
            ->with('responsibleAdmin', $responsibleAdmin)
            ->with('creators', $creators);
    }
}
