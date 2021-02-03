<?php

namespace App\Http\Controllers;

use App\User;
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
        // add Role the moment you introduced a simple role system to the app
        // $adminRole = Role::where('name', 'Admin')->pluck('id');
        $responsibleAdmin = User::first();

        return view('pages.about')
            ->with('responsibleAdmin', $responsibleAdmin);;
    }
}
