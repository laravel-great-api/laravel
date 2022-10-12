<?php

namespace LaravelGreatApi\Laravel;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use LaravelGreatApi\Laravel\Console\MakeControllerCommand;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->commands([MakeControllerCommand::class]);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }
}
