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




Route::get('/snapshot/{uid}','SnapshotController@index'); //
Route::get('/trend/{uid}','TrendController@index');//
Route::get('/cfilters/{uid}','CourseMenuController@index');//
Route::get('/getcate/{id}','CategoryController@getCategory');//
Route::get('/course/{uid}','CourseController@index');//
Route::get('/getcourse/{uid}','CourseController@getCourse');//
Route::get('/lstar/{uid}','StarController@index'); //
Route::get('/time/{uid}','TimeController@index');//
Route::get('/getcmps/{uid}','CompanyController@getcompany');

Route::post('/admin/login','AdminController@login');
Route::post('/admin/reset/{uid}','AdminController@changepassword');
Route::post('/admin/change/{uid}','AdminController@changeAdminInfo');
Route::post('/admin/forget','AdminController@fogetpassword');

Route::post('/user/insert','UserMgrController@insertuser');
Route::post('/user/login','UserMgrController@login');
Route::get('/user/list/{cmpid}','UserMgrController@getlist');
Route::post('/user/reset/{uid}','UserMgrController@resetPassword');
Route::post('/user/delete/{uid}','UserMgrController@deleteuser');
Route::post('/user/status/{uid}','UserMgrController@changestatus');
Route::post('/user/update/{uid}','UserMgrController@updateuser');
Route::post('/user/assignrole/{uid}','UserMgrController@assignrole');
Route::post('/user/assigncmp/{uid}','UserMgrController@assigncompanys');
Route::get('/user/get/{uid}','UserMgrController@getuser');

Route::post('/role/add','RoleController@addrole');
Route::post('/role/update/{roleid}','RoleController@updaterole');
Route::get('/role/list','RoleController@getroles');
Route::post('/role/delete/{roleid}','RoleController@deleterole');

Route::get('/func/list','FunctionController@getfuncs');


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
