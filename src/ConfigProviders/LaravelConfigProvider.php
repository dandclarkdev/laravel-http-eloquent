<?php

namespace LaravelHttpEloquent\ConfigProviders;

use LaravelHttpEloquent\Interfaces\ConfigProvider;

class LaravelConfigProvider implements ConfigProvider
{
    public function getConfig(string $root): array
    {
        return config("laravelhttpeloquent.$root");
    }
}