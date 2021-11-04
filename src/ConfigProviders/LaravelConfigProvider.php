<?php

namespace LaravelHttpEloquent\ConfigProviders;

use HttpEloquent\Interfaces\ConfigProvider;

class LaravelConfigProvider implements ConfigProvider
{
    public function getConfig(string $root): array
    {
        return config("laravelhttpeloquent.services.$root");
    }
}