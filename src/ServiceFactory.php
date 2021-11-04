<?php

namespace LaravelHttpEloquent;

use LaravelHttpEloquent\Service;
use HttpEloquent\ServiceFactory as BaseServiceFactory;
class ServiceFactory extends BaseServiceFactory
{
    protected const FALLBACK_SERVICE_CLASS = Service::class;
}
