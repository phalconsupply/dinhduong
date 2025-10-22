<?php

use Illuminate\Support\Facades\Route;
use App\Models\Unit;
Route::model('unit', 'App\Models\Unit');

Route::group(['prefix' => 'admin', 'namespace'=>'App\Http\Controllers\Admin'], function () {
    Route::get('/test', 'DashboardController@test')->name('admin.test.index');
    //admin Auth
    Route::match(['get','post'],'/auth/login', 'AuthController@login')->name('admin.auth.login');
    Route::get('/auth/logout', 'AuthController@logout')->name('admin.auth.logout');

    Route::get('/ajax_get_district_by_province', 'AjaxController@ajax_get_district_by_province')->name('admin.ajax_get_district_by_province');
    Route::get('/ajax_get_ward_by_district', 'AjaxController@ajax_get_ward_by_district')->name('admin.ajax_get_ward_by_district');

});

//
Route::group(['prefix' => 'admin', 'namespace'=>'App\Http\Controllers\Admin',  'middleware' => 'auth.adminpanel'], function () {
    //Dashboard
    Route::get('/', 'DashboardController@index')->name('admin.dashboard.index');
    Route::get('/statistics', 'DashboardController@statistics')->name('admin.dashboard.statistics');
    Route::get('/statistics/export-csv', 'DashboardController@exportMeanStatisticsCSV')->name('admin.dashboard.export_mean_csv');
    //Media
    Route::get('/media', 'MediaController@index')->name('admin.media.index');

    //History
    Route::get('/history', 'HistoryController@index')->name('admin.history.index');
    Route::post('/history/export', 'HistoryController@export')->name('admin.history.export');
    Route::post('/history/update_advice', 'HistoryController@update_advice')->name('admin.history.update_advice');
    Route::delete('/history/{history}', 'HistoryController@destroy')->name('admin.history.destroy');


    //Users
    Route::post('/users/{user}/toggle_active', 'UserController@toggle_active')->name('admin.users.toggle_active')->middleware('can:view,user');
    Route::get('/users/{user}/history', 'UserController@showHistory')->name('admin.users.show_history')->middleware('can:view,user');
    Route::get('/users/{user}/roles', 'UserController@show_roles')->name('admin.users.show_roles')->middleware('can:view,user');
    Route::match(['get','post'],'/users/{user}/update_password', 'UserController@update_password')->name('admin.users.update_password')->middleware('can:view,user');
    Route::resource('/users', 'UserController', ['names'=>'admin.users']);

    //Units
    Route::get('/units/{unit}/history', 'UnitController@show_history')->name('admin.units.show_history')->middleware('can:view,unit');
    Route::get('/units/{unit}/users', 'UnitController@show_users')->name('admin.units.show_users')->middleware('can:view_users,unit');
    Route::resource('/units', 'UnitController', ['names'=>'admin.units']);
    //Profile
    Route::get('/profile', 'ProfileController@index')->name('admin.profile.index');
    Route::get('/profile/change-password', 'ProfileController@changepassword')->name('admin.profile.changepassword');
    Route::post('/profile', 'ProfileController@update')->name('admin.profile.update');
    Route::post('/profile/update_password', 'ProfileController@update_password')->name('admin.profile.update_password');


    Route::group(['middleware' => 'auth.admin'], function () {
        //Type
        Route::get('/type', 'TypeController@index')->name('admin.type.index');
        Route::post('/type', 'TypeController@post')->name('admin.type.post');
//        Route::put('/type/{id}', 'TypeController@update')->name('admin.type.update');
        Route::delete('/type/{id}', 'TypeController@destroy')->name('admin.type.destroy');
        //Setting
        Route::get('/setting', 'SettingController@index')->name('admin.setting.index');
        Route::get('/setting/advices', 'SettingController@advices')->name('admin.setting.advices');
        Route::post('/setting/advices', 'SettingController@update_advices')->name('admin.setting.update_advices');
        Route::post('/setting', 'SettingController@update')->name('admin.setting.update');
    });
});


?>
