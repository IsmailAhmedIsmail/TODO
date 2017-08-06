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

Route::post('/register','Auth\RegisterController@register')->middleware('guest')->name('register');
Route::post('/login','Auth\LoginController@login')->middleware('guest')->name('login');
Route::post('/logout','Auth\LoginController@logout')->middleware('auth:api')->name('logout');

Route::get('/user', 'UserController@currentUser')->middleware('auth:api')->name('user');

Route::group(['prefix' => 'users', 'middleware' => 'auth:api'],function(){
    Route::get('/username', [
        'as'=> 'search-username',
        'uses' => 'UserController@searchByUsername'
    ]);
    Route::get('/email',[
        'as' => 'search-email',
        'uses' => 'UserController@searchByEmail'
    ]);
    Route::get('/name',[
       'as' => 'search-name',
       'uses' => 'UserController@searchByName'
    ]);
});
Route::group(['prefix' => 'login/github','middleware' => ['guest','web'] ],function(){
    Route::get('/', [
       'uses' => 'Auth\LoginController@redirectToProvider',
        'as' => 'login-github'
    ]);
    Route::get('/callback', [
       'uses' => 'Auth\LoginController@handleProviderCallback',
        'as' => 'github-callback'
    ]);

});


Route::get('/tasks','TaskController@index')->middleware('guest')->name('tasks');

Route::get('/tasks/feed','TaskController@feed')->middleware('auth:api')->name('feed');
Route::post('/tasks','TaskController@store')->middleware('auth:api')->name('post-task');
Route::get('/tasks/{task}',[
    'uses' => 'TaskController@show',
    'as' => 'get-task'
])->middleware('auth:api');
Route::post('/tasks/{task}/follow',[
    'uses' => 'FollowTaskController@follow',
    'as' => 'follow-task'
])->middleware('auth:api');
Route::group(['prefix' => '/tasks/{task}','middleware' =>['auth:api','TaskOwner']], function (){

    Route::post('/setPublic',[
       'uses' => 'TaskController@setPublic',
        'as' => 'set-public'
    ]);
    Route::post('/setPrivate',[
       'uses' => 'TaskController@setPrivate',
        'as' => 'set-private'
    ]);
    Route::post('/setComplete',[
      'uses' =>  'TaskController@setComplete',
        'as' => 'set-complete'
    ]);
    Route::post('/setIncomplete',[
       'uses' => 'TaskController@setIncomplete',
        'as' => 'set-incomplete'
    ]);
    Route::post('/toggle',[
        'uses' => 'TaskController@toggleComplete',
        'as' => 'toggle-complete'
    ]);
    Route::post('/setDeadline',[
        'uses' => 'TaskController@setDeadline',
        'as' => 'set-deadline'
    ]);
    Route::post('/addFile',[
        'uses' => 'TaskController@addFile',
        'as' => 'set-file'
    ]);
    Route::delete('/',[
        'uses' => 'TaskController@destroy',
        'as' => 'delete-task'
    ]);

    Route::post('/invite/{invited}',[
        'uses' => 'FollowTaskController@invite',
        'as' => 'invite'
    ]);
});


Route::group(['prefix' => 'invitations/{invitation}' , 'middleware' => 'auth:api'],function(){
    Route::post('/accept',[
        'uses' => 'FollowTaskController@acceptInvitation',
        'as' => 'accept-invitation'
    ]);
    Route::post('/reject',[
        'uses' => 'FollowTaskController@rejectInvitation',
        'as' => 'reject-invitation'
    ]);
});


Route::post('/password','UserController@updatePassword')->middleware('auth:api')->name('change-password');

Route::post('/avatar','UserController@avatar')->middleware('auth:api')->name('upload-avatar');