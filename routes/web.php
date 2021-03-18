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

Auth::routes();

Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
//activate
Route::get('/activate','Auth\LoginController@activate');

Route::post('/data','DataController@index');

Route::middleware(['auth'])->group(function(){
    Route::get('/', 'MainController@index')->name('main');
    Route::post('/show','MainController@show')->name('show_main');
    Route::post('/last','MainController@last')->name('show_last');
    Route::post('/add-truck','MainController@newTruck')->name('add_truck');
    Route::post('/add-visitor','MainController@newVisitor')->name('add_visitor');
    Route::post('/find-visitor','MainController@findVisitor')->name('find_visitor');

    //doc-types/ группа обработки роутов doc-types
    Route::group(['prefix'=>'doc-types'], function(){
        Route::get('/',['uses'=>'DocTypeController@index','as'=>'doc-types']);
        //doc-types/add
        Route::match(['get','post'],'/add',['uses'=>'DocTypeController@create','as'=>'dtypeAdd']);
        //doc-types/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'DocTypeController@edit','as'=>'dtypeEdit']);
    });

    //reports/ группа обработки роутов reports
    Route::group(['prefix'=>'reports'], function(){
        //reports/traffic-flow
        Route::get('/traffic-flow',['uses'=>'ReportController@trafficFlow','as'=>'traffic_flow']);
        Route::post('/traffic-bar',['uses'=>'ReportController@trafficBar','as'=>'traffic_bar']);
        Route::post('/traffic-tbl',['uses'=>'ReportController@trafficTbl','as'=>'traffic_tbl']);
        Route::post('/analize-bar',['uses'=>'ReportController@analizeBar','as'=>'analize_bar']);
        Route::post('/analize-tbl',['uses'=>'ReportController@analizeTbl','as'=>'analize_tbl']);
        Route::match(['get','post'],'/visitor',['uses'=>'ReportController@visitors','as'=>'visitorsReport']);
    });

    //cars/ группа обработки роутов cars
    Route::group(['prefix'=>'cars'], function(){
        Route::get('/',['uses'=>'CarController@index','as'=>'cars']);
        //cars/add
        Route::match(['get','post'],'/add',['uses'=>'CarController@create','as'=>'carAdd']);
        //cars/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'CarController@edit','as'=>'carEdit']);
    });

    //renters/ группа обработки роутов renters
    Route::group(['prefix'=>'renters'], function(){
        Route::get('/',['uses'=>'RenterController@index','as'=>'renters']);
        //renters/add
        Route::match(['get','post'],'/add',['uses'=>'RenterController@create','as'=>'renterAdd']);
        //renters/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'RenterController@edit','as'=>'renterEdit']);
    });

    //visitors/ группа обработки роутов visitors
    Route::group(['prefix'=>'visitors'], function(){
        Route::get('/',['uses'=>'VisitorController@index','as'=>'visitors']);
        //visitors/add
        Route::match(['get','post'],'/add',['uses'=>'VisitorController@create','as'=>'visitorAdd']);
        //visitors/edit
        Route::match(['get','post','delete'],'/edit/{id}',['uses'=>'VisitorController@edit','as'=>'visitorEdit']);
    });

    //eventlogs/ группа обработки роутов eventlogs
    Route::group(['prefix'=>'eventlogs'], function(){
        Route::get('/',['uses'=>'EventLogController@index','as'=>'eventlogs']);
        //eventlogs/delete
        Route::get('/del-one/{id}',['uses'=>'EventLogController@delOne','as'=>'evlogDelOne']);
        Route::get('/del-log',['uses'=>'EventLogController@delLog','as'=>'evlogDel']);
    });

    //tracelogs/ группа обработки роутов tracelogs
    Route::group(['prefix'=>'tracelogs'], function(){
        Route::get('/',['uses'=>'EventLogController@trace','as'=>'tracelogs']);
        //tracelogs/delete
        Route::get('/del-one/{id}',['uses'=>'EventLogController@traceDelOne','as'=>'tracelogDelOne']);
        Route::get('/del-log',['uses'=>'EventLogController@traceDelLog','as'=>'tracelogDel']);
    });

});
