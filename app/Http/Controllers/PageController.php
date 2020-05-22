<?php

namespace App\Http\Controllers;

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
        return view('pages.about');
    }

    /**
     * Show the clips page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function clips(){
        return view('pages.clips');
    }
}
