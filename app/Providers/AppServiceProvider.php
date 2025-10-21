<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Models\Setting;
use Illuminate\Support\Facades\View;
use App\Models\User;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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

        //
        $allsetting = Setting::get()->toArray();
        $setting = array();
        foreach($allsetting as $row){
            $setting[$row['key']] =  $row['value'];
        }
        View::share('setting', $setting);

        //AuthUser
        View::share('authUser', Auth::user());
    }
}
