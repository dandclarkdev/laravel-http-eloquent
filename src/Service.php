<?php

namespace LaravelHttpEloquent;

use LaravelHttpEloquent\Types\Path;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use LaravelHttpEloquent\Types\Query;
use LaravelHttpEloquent\GenericModel;
use LaravelHttpEloquent\Types\BaseUrl;
use LaravelHttpEloquent\Types\ModelMap;
use LaravelHttpEloquent\Types\ServiceConfig;
use LaravelHttpEloquent\Interfaces\Service as ServiceInterface;

class Service implements ServiceInterface
{
    /**
     * @var \LaravelHttpEloquent\Types\BaseUrl
     */
    protected $baseUrl;

    /**
     * @var \LaravelHttpEloquent\Types\Path
     */
    protected $path;

    /**
     * @var \LaravelHttpEloquent\Types\Query
     */
    protected $query;

    /**
     * @var \LaravelHttpEloquent\Types\ModelMap
     */
    protected $modelMap;

    /**
     * @var string
     */
    protected $resolveTo = GenericModel::class;

    /**
     * @var bool
     */
    protected $immutableResolveTo = false;

    /**
     * @var bool
     */
    protected $plural = false;

    public function __construct(ServiceConfig $config)
    {
        $this->baseUrl = $config->getBaseUrl();
        $this->path = new Path();
        $this->query = new Query();
        $this->modelMap = $config->getModelMap();
    }

    public function one($class = null) {
        $this->plural = false;

        if($class !== null) {
            $this->immutableResolveTo = true;
            $this->resolveTo = $class;
        }

        return $this;
    }

    public function many($class = null) {
        $this->plural = true;

        if($class !== null) {
            $this->immutableResolveTo = true;
            $this->resolveTo = $class;
        }

        return $this;
    }

    public function page($pageNumber): self
    {
        return $this->where('page', $pageNumber);
    }

    public function perPage($perPage): self
    {
        return $this->where('per_page', $perPage);
    }

    protected function resolve(Response $response)
    {
        $class = $this->resolveTo;

        if ($this->plural) {
            return $response->collect()
                ->map(function (array $item) use ($class) {
                    return new $class(...$item);
                });
        } else {
            return new $class(...$response->json());
        }
    }

    public function first()
    {
        $this->plural = true;

        return $this->resolve(
            Http::get($this->getUrl(), $this->query->toArray())
        )->first();
    }

    public function get()
    {
        return $this->resolve(
            Http::get($this->getUrl(), $this->query->toArray())
        );
    }

    public function create(array $params): Response
    {
        $this->plural = false;

        return Http::post($this->getUrl(), $params);
    }

    public function update(array $params): Response
    {
        $this->plural = false;

        return Http::patch($this->getUrl(), $params);
    }

    public function delete(): Response
    {
        $this->plural = false;

        return Http::delete($this->getUrl());
    }

    public function find($id): Response
    {
        $method = (string) $id;

        $this->path->$method();

        $this->plural = false;

        return $this->get();
    }

    public function where($key, $value): self
    {
        $this->query->where($key, $value);

        return $this;
    }

    public function getUrl(): string
    {
        return implode('/', [
            (string) $this->baseUrl,
            (string) $this->path
        ]);
    }

    public function getUrlWithQuery(): string
    {
        return implode('/', [
            (string) $this->baseUrl,
            (string) $this->path
        ]) . '?' . (string) $this->query;
    }

    public function __get(string $property)
    {
        return $this->$property()->get();
    }

    public function __call(string $method, array $params): ServiceInterface
    {
        if (count($params) > 0) {
            $this->path->$method($params[0]);
        } else {
            $this->plural = true;

            $this->path->$method();
        }

        if(!$this->immutableResolveTo) {
            if ($this->modelMap->has($method)) {
                $this->resolveTo = $this->modelMap->get($method);
            } else {
                $this->resolveTo = GenericModel::class;
            }
        }

        return $this;
    }
}
