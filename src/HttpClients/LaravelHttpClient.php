<?php

namespace LaravelHttpEloquent\HttpClients;

use Illuminate\Support\Facades\Http;
use HttpEloquent\Interfaces\HttpClient;
use Psr\Http\Message\ResponseInterface;

class LaravelHttpClient implements HttpClient
{
    public function get(string $url, array $query = []): ResponseInterface
    {
        return Http::get($url, $query)
            ->toPsrResponse();
    }

    public function post(string $url, array $params): ResponseInterface
    {
        return Http::post($url, $params)
            ->toPsrResponse();
    }

    public function patch(string $url, array $params): ResponseInterface
    {
        return Http::patch($url, $params)
            ->toPsrResponse();
    }

    public function delete(string $url): ResponseInterface
    {
        return Http::delete($url)
            ->toPsrResponse();
    }
}
