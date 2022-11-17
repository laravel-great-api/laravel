<?php

namespace LaravelGreatApi\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Laravel extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'laravel';
    }
}
