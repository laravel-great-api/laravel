<?php

namespace LaravelGreatApi\Laravel;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use LaravelGreatApi\Laravel\Console\MakeControllerCommand;
use LaravelGreatApi\Laravel\Console\MakeModuleClassCommand;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->commands([
            MakeControllerCommand::class,
            MakeModuleClassCommand::class
        ]);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/laravel-great-api.php' => config_path('laravel-great-api.php'),
            ], 'laravel-great-api.config');
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-great-api.php', 'laravel-great-api');
    }
}
