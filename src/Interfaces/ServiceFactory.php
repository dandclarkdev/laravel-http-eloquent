<?php

namespace LaravelHttpEloquent\Interfaces;

use LaravelHttpEloquent\Interfaces\Service;

interface ServiceFactory
{
    public function make(string $serviceName): Service;
    public function __call(string $method, array $parameters): Service;
}