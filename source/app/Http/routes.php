<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});



Route::post('/admin/login','AdminController@login');
Route::post('/admin/reset','AdminController@resetPassword');
Route::post('/admin/cmail','AdminController@confirmMail');
Route::get('/snapshot/{uid}','SnapshotController@index');
Route::get('/trend/{uid}','TrendController@index');
Route::get('/cfilters/{uid}','CourseMenuController@index');
Route::get('/getcate/{id}','CategoryController@getCategory');
Route::get('/course/{uid}','CourseController@index');







/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});
