<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

$namespace = 'App\Http\Controllers\\';

//web Auth
Route::match(['get','post'],'/auth/login', $namespace.'AuthController@login')->name('auth.login');
Route::get('/auth/logout', $namespace.'AuthController@logout')->name('auth.logout');

//Web
Route::get('/', [WebController::class, 'index'])->name('index');

// Wizard form route (NEW DESIGN)
Route::get('/wizard', [WebController::class, 'formWizard'])->name('form.wizard');

// Specific routes MUST come BEFORE wildcard routes
Route::get('/ketqua', [WebController::class, 'result'])->name('result');
Route::get('/in', [WebController::class, 'print'])->name('print');
Route::get('/ajax/tinh-ngay-sinh', [WebController::class, 'ajax_tinh_ngay_sinh']);
Route::post('/post', [WebController::class, 'form_post'])->name('form.post');

// Wildcard route MUST be at the END
Route::get('/{slug}', [WebController::class, 'form'])->name('form.index');
//Route::get('/run', $namespace.'WebController@run');
//Ajax
Route::get('/web/ajax_get_district_by_province', $namespace.'WebController@ajax_get_district_by_province')->name('web.ajax_get_district_by_province');
Route::get('/web/ajax_get_ward_by_district', $namespace.'WebController@ajax_get_ward_by_district')->name('web.ajax_get_ward_by_district');
