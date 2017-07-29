<?php

use Illuminate\Http\Request;

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

Route::post('/register','Auth\RegisterController@create')->middleware('guest');
Route::post('/login','Auth\LoginController@authenticate')->middleware('guest');
Route::post('/logout','Auth\LoginController@logout')->middleware('jwt.auth');
Route::get('/user', 'UserController@currentUser')->middleware('jwt.auth');
Route::get('login/github', 'Auth\LoginController@redirectToProvider')->middleware('guest');
Route::get('login/github/callback', 'Auth\LoginController@handleProviderCallback')->middleware('guest');

Route::get('/tasks','TaskController@index')->middleware('guest');
Route::get('/tasks/feed','TaskController@feed');
Route::post('/tasks','TaskController@store')->middleware('jwt.auth');

Route::get('/tasks/{task}','TaskController@show')->middleware('jwt.auth');
Route::post('/tasks/{task}/setPublic','TaskController@setPublic')->middleware('jwt.auth');
Route::post('/tasks/{task}/setPrivate','TaskController@setPrivate')->middleware('jwt.auth');
Route::post('/tasks/{task}/setComplete','TaskController@setComplete')->middleware('jwt.auth');
Route::post('/tasks/{task}/setIncomplete','TaskController@setIncomplete')->middleware('jwt.auth');
Route::post('/tasks/{task}/toggle','TaskController@toggleComplete')->middleware('jwt.auth');
Route::post('/tasks/{task}/setDeadline','TaskController@setDeadline')->middleware('jwt.auth');
Route::delete('/tasks/{task}','TaskController@destroy')->middleware('jwt.auth');

Route::post('/tasks/{task}/follow','FollowTaskController@follow')->middleware('jwt.auth');
Route::post('/tasks/{task}/invite/{invited}','FollowTaskController@invite')->middleware('jwt.auth');
Route::post('invitations/{invitation}/accept','FollowTaskController@acceptInvitation')->middleware('jwt.auth');
Route::post('invitations/{invitation}/reject','FollowTaskController@rejectInvitation')->middleware('jwt.auth');

Route::put('/password','UserController@updatePassword')->middleware('jwt.auth');