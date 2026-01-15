<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function () {
            if (
                Auth::check() &&
                Auth::user()->profile_completed == 0 &&
                ! request()->is('profile')
            ) {
                redirect()->route('profile.edit')->send();
            }
        });
    }
}
