<?php

namespace LaravelHttpEloquent;

use Illuminate\Support\Str;
use Illuminate\Http\Client\Response;
use Psr\Http\Message\ResponseInterface;
use HttpEloquent\Service as BaseService;

class Service extends BaseService
{
    protected function resolve(ResponseInterface $response)
    {
        $resolved = parent::resolve($response);

        return $this->getPlural() ? collect($resolved) : $resolved;
    }
}
