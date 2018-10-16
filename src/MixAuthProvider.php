<?php

namespace Bnabriss\MixAuth;

use Illuminate\Support\ServiceProvider;


class MixAuthProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        //register config to be published
        $this->publishes([
            __DIR__.'/../config/mix-auth.php' => config_path('mix-auth.php'),
        ]);
        //$this->loadMigrationsFrom(__DIR__.'/../database/migrations');


    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/mix-auth.php', 'mix-auth');

        app('router')->aliasMiddleware('mix.auth', Middleware\Authenticate::class);


    }

}
