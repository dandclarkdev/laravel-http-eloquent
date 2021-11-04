<?php

namespace LaravelHttpEloquent;

use Illuminate\Http\Client\Response;
use Psr\Http\Message\ResponseInterface;
use HttpEloquent\Service as BaseService;

class Service extends BaseService
{
    protected function resolve(ResponseInterface $response)
    {
        $class = $this->getResolveTo();

        if ($this->getPlural()) {
            return (new Response($response)) ->collect()
                ->map(function (array $item) use ($class) {
                    return new $class(...$item);
                });
        } else {
            return new $class(...(new Response($response))->json());
        }
    }
}
