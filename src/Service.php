<?php

namespace LaravelHttpEloquent;

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
