<?php

namespace LaravelHttpEloquent\Facades;

use Illuminate\Support\Facades\Facade;
use LaravelHttpEloquent\Interfaces\ServiceFactory;

class Service extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ServiceFactory::class;
    }
}
