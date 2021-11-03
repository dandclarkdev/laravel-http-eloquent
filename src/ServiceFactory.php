<?php

namespace LaravelHttpEloquent;

use LaravelHttpEloquent\Service;
use LaravelHttpEloquent\Types\BaseUrl;
use LaravelHttpEloquent\Types\ServiceConfig;
use LaravelHttpEloquent\Interfaces\ServiceFactory as ServiceFactoryInterface;
use LaravelHttpEloquent\Types\ModelMap;

class ServiceFactory implements ServiceFactoryInterface
{
    public function make(string $serviceName): Service
    {
        $config = config("laravelhttpeloquent.services.$serviceName");

        return new Service(
            new ServiceConfig(
                new BaseUrl(
                    $config['base_url']
                ),
                new ModelMap(
                    $config['models']
                )
            )
        );
    }

    public function __call(string $method, array $parameters): Service
    {
        return $this->make($method);
    }
}