<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/login', 'AuthController@login');

Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::resource('covid_care_users', 'CovidCareUserAPIController');

    Route::resource('posts', 'PostAPIController');

    Route::get('/userposts/{userid}', 'PostAPIController@userPosts');

    Route::post('/posts/statusupdate/{userid}', 'PostAPIController@updateUserPost');

    Route::resource('feeds', 'FeedAPIController');

    Route::resource('suppliers', 'SupplierAPIController');
});