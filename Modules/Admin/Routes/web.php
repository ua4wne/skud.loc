<?php

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

Route::middleware(['auth'])->group(function() {
    Route::prefix('roles')->group(function () {
        Route::get('/',['uses'=>'RoleController@index','as'=>'roles']);
        //roles/add
        Route::match(['get','post'],'/add',['uses'=>'RoleController@create','as'=>'roleAdd']);
        //roles/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'RoleController@edit','as'=>'roleEdit']);
        //roles/ajax/get_action
        Route::post('/ajax/get_action',['uses'=>'Ajax\ActionController@getAction','as'=>'getAction']);
        //roles/ajax/add_action
        Route::post('/ajax/add_action',['uses'=>'Ajax\ActionController@addAction','as'=>'addAction']);
    });

    //actions/ группа обработки роутов actions
    Route::group(['prefix'=>'actions'], function(){
        Route::get('/',['uses'=>'ActionController@index','as'=>'actions']);
        //actions/add
        Route::match(['get','post'],'/add',['uses'=>'ActionController@create','as'=>'actionAdd']);
        //actions/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'ActionController@edit','as'=>'actionEdit']);
    });

    //devices/ группа обработки роутов devices
    Route::group(['prefix'=>'devices'], function(){
        Route::get('/',['uses'=>'DeviceController@index','as'=>'devices']);
        //devices/add
        Route::match(['get','post'],'/add',['uses'=>'DeviceController@create','as'=>'deviceAdd']);
        //devices/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'DeviceController@edit','as'=>'deviceEdit']);
        //devices/clear-card
        Route::get('/clear-card/{id}',['uses'=>'DeviceController@clearCard','as'=>'clearCard']);
    });

    //time-zones/ группа обработки роутов time-zones
    Route::group(['prefix'=>'time-zones'], function(){
        Route::get('/',['uses'=>'TimeZoneController@index','as'=>'time-zones']);
        //time-zones/add
        Route::match(['get','post'],'/add',['uses'=>'TimeZoneController@create','as'=>'tzoneAdd']);
        //time-zones/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'TimeZoneController@edit','as'=>'tzoneEdit']);
    });

    //event-types/ группа обработки роутов event-types
    Route::group(['prefix'=>'event-types'], function(){
        Route::get('/',['uses'=>'EventTypeController@index','as'=>'event-types']);
        //event-types/add
        Route::match(['get','post'],'/add',['uses'=>'EventTypeController@create','as'=>'evtypeAdd']);
        //event-types/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'EventTypeController@edit','as'=>'evtypeEdit']);
    });

    //tasks/ группа обработки роутов tasks
    Route::group(['prefix'=>'tasks'], function(){
        Route::get('/',['uses'=>'TaskController@index','as'=>'tasks']);
        Route::get('/delete-one/{id}',['uses'=>'TaskController@destroy','as'=>'taskDelete']);
        Route::get('/delete',['uses'=>'TaskController@delete','as'=>'tasksDelete']);
    });

    //events/ группа обработки роутов events
    Route::group(['prefix'=>'events'], function(){
        Route::get('/',['uses'=>'EventLogController@index','as'=>'events']);
        //events/delete
        Route::post('/delete',['uses'=>'EventLogController@delete','as'=>'eventDelete']);
        Route::get('/view-requests',['uses'=>'RequestController@index','as'=>'view-requests']);
        Route::post('/delrequest',['uses'=>'RequestController@delete','as'=>'requestDel']);
    });

    //cards/ группа обработки роутов cards
    Route::group(['prefix'=>'cards'], function(){
        Route::get('/',['uses'=>'CardController@index','as'=>'cards']);
        //cards/add
        Route::match(['get','post'],'/add',['uses'=>'CardController@create','as'=>'cardAdd']);
        //cards/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'CardController@edit','as'=>'cardEdit']);
        //cards/load
        Route::get('/load/{id}',['uses'=>'CardController@load','as'=>'cardLoad']);
        //cards/tasks
        Route::post('/tasks',['uses'=>'CardController@tasks','as'=>'cardTask']);
    });

    //users/ группа обработки роутов users
    Route::group(['prefix'=>'users'], function(){
        Route::get('/',['uses'=>'UserController@index','as'=>'users']);
        //users/add
        Route::match(['get','post'],'/add',['uses'=>'UserController@create','as'=>'userAdd']);
        //users/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'UserController@edit','as'=>'userEdit']);
        //users/reset
        Route::get('/reset/{id}',['uses'=>'UserController@resetPass','as'=>'userReset']);
        //users/ajax/edit
        Route::post('/ajax/edit',['uses'=>'Ajax\UserController@switchLogin','as'=>'switchLogin']);
        //users/ajax/edit_login
        Route::post('/ajax/edit_login',['uses'=>'Ajax\UserController@editLogin','as'=>'editLogin']);
        //users/ajax/delete
        Route::post('/ajax/delete',['uses'=>'Ajax\UserController@delete','as'=>'deleteLogin']);
        //users/ajax/add_role
        Route::post('/ajax/add_role',['uses'=>'Ajax\UserController@addRole','as'=>'addRole']);
        //users/ajax/get_role
        Route::post('/ajax/get_role',['uses'=>'Ajax\UserController@getRole','as'=>'getRole']);
    });
});
