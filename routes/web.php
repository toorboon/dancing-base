<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'PageController@index')->name('index');
Route::get('/about', 'PageController@about')->name('about');

Route::middleware('auth')->group(function() {
    Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
        Route::get('/videos/index/{category?}', 'VideoController@index')->name('videos.index');
        Route::resource('/videos', 'VideoController')->except('index');

        Route::resource('/categories', 'CategoryController')->except(['index', 'show', 'edit']);
        Route::resource('/users', 'UserController')->only(['update', 'destroy']);

    });
});

Auth::routes();


