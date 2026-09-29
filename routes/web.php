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

// KHÔNG đăng ký Lfm::routes() ở đây.
//
// Package tự đăng ký route của nó khi config('lfm.use_package_routes') = true,
// tại prefix 'filemanager' với middleware ['web','auth'] (xem config/lfm.php).
// Đăng ký thêm lần nữa ở đây gây ra HAI vấn đề:
//
//   1. Trùng tên route nên `php artisan route:cache` THẤT BẠI — production
//      chạy mà không có route cache.
//   2. Đăng ký thủ công dùng middleware ['web'] KHÔNG CÓ 'auth', nên toàn bộ
//      trình quản lý file mở công khai: /laravel-filemanager trả về HTTP 200
//      cho người chưa đăng nhập, duyệt và tải file lên được.
//
// Đường dẫn đúng là /filemanager. Các view tham chiếu tới nó đã sửa theo.

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
