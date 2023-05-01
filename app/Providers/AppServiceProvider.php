<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Session;
use Auth;

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
        Session::put('name', 'sreenu');
        Session::put('user', Auth::user());
    }
}
