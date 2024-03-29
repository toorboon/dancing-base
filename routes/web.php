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

        Route::resource('/dashboard', 'DashboardController')->only(['index','update','destroy'])
            ->parameters(['dashboard' => 'tag',])->middleware('can:manage-app'); //admin

        Route::resource('/videos', 'VideoController')->except(['index','show'])->middleware('can:manage-app'); //admin partially
        Route::resource('/videos', 'VideoController')->only(['index','show']);
        Route::post('/videos/rate-video', 'VideoController@rate')->name('videos.rate');
        Route::post('/videos/resetSearch', 'VideoController@resetSearch')->name('reset.search');
        Route::post('/videos/fetchElement', 'VideoController@fetchElement')->name('videos.fetchElement');
        Route::post('/videos/publish', 'VideoController@publish')->name('videos.publish')->middleware('can:manage-app');

        Route::resource('/categories', 'CategoryController')->except(['index', 'show', 'edit'])->middleware('can:manage-app'); //admin
        Route::resource('/users', 'UserController')->only(['update', 'destroy'])->middleware('can:manage-app'); //admin


    });
    Route::get('change-password', 'ChangePasswordController@index');
    Route::post('change-password', 'ChangePasswordController@store')->name('change.password');
});

Auth::routes();


