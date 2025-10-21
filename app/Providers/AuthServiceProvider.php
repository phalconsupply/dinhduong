<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Setting;
use App\Models\Unit;
use App\Policies\SettingPolicy;
use App\Policies\UnitPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Setting::class => SettingPolicy::class,
        Unit::class => UnitPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
