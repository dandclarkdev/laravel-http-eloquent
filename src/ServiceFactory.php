<?php

namespace LaravelHttpEloquent;

use HttpEloquent\Types\BaseUrl;
use HttpEloquent\Types\ModelMap;
use LaravelHttpEloquent\Service;
use HttpEloquent\Types\ServiceConfig;
use HttpEloquent\ServiceFactory as BaseServiceFactory;

class ServiceFactory extends BaseServiceFactory
{
    public function make(string $serviceName): Service
    {
        $config = $this->configProvider->getConfig(
            $serviceName
        );

        return new Service(
            new ServiceConfig(
                new BaseUrl(
                    $config['base_url']
                ),
                new ModelMap(
                    $config['models'] ?? []
                )
            ),
            $this->getClient()
        );
    }
}
