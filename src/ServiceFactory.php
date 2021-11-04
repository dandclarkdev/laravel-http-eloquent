<?php

namespace LaravelHttpEloquent;

use HttpEloquent\Types\BaseUrl;
use HttpEloquent\Types\ModelMap;
use LaravelHttpEloquent\Service;
use HttpEloquent\Types\ServiceConfig;
use HttpEloquent\ServiceFactory as BaseServiceFactory;
use HttpEloquent\Interfaces\Service as ServiceInterface;
use HttpEloquent\Types\WrapperProperty;

class ServiceFactory extends BaseServiceFactory
{
    public function make(string $serviceName): ServiceInterface
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
                ),
                isset($config['wrapper']) ? new WrapperProperty(
                    $config['wrapper']
                ) : null
            ),
            $this->getClient()
        );
    }
}
