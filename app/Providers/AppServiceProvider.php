<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Models\User;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind Intervention Image Manager for Laravel FileManager
        $this->app->bind(
            \Intervention\Image\Interfaces\ImageManagerInterface::class,
            function ($app) {
                return new \Intervention\Image\ImageManager(
                    new \Intervention\Image\Drivers\Gd\Driver()
                );
            }
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('asciiOnly', function($attribute, $value, $parameters)
        {
            return !preg_match('/[^x00-x7F]/i', $value);
        });

        // Nạp cấu hình chung cho mọi view.
        //
        // Phải chịu được trường hợp bảng settings chưa tồn tại: trên một máy chủ
        // mới, `php artisan migrate` cũng phải khởi động ứng dụng, mà lúc đó
        // chưa có bảng nào cả — nếu để lỗi thoát ra thì không thể chạy migration
        // để tạo chính bảng đó.
        $setting = array();

        try {
            if (Schema::hasTable('settings')) {
                foreach (Setting::get()->toArray() as $row) {
                    $setting[$row['key']] = $row['value'];
                }
            }
        } catch (\Throwable $e) {
            // Chưa kết nối được DB (lúc cài đặt ban đầu) — dùng mảng rỗng
        }

        View::share('setting', $setting);

        //AuthUser
        View::share('authUser', Auth::user());
    }
}
