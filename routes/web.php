<?php

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

Route::get('/clips', 'PageController@clips')->name('clips')->middleware('auth');

Route::namespace('Admin')->prefix('admin')->name('admin.')->group(function() {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
});

Auth::routes();


