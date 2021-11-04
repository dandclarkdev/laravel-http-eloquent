<?php

namespace Tests\Unit;

use Mockery;
use stdClass;
use HttpEloquent\GenericModel;
use HttpEloquent\Types\BaseUrl;
use PHPUnit\Framework\TestCase;
use HttpEloquent\Types\ModelMap;
use LaravelHttpEloquent\Service;
use HttpEloquent\Types\ServiceConfig;
use HttpEloquent\Interfaces\HttpClient;
use GuzzleHttp\Psr7\Response as Psr7Response;
use HttpEloquent\Types\WrapperProperty;
use Illuminate\Support\Collection;

class ServiceTest extends TestCase
{
    /**
     * @var \HttpEloquent\ServiceInterface
     */
    protected $service;

    /**
     * @var \HttpEloquent\Interfaces\HttpClientInterface|\Mockery\Mock
     */
    protected $client;

    public function setUp(): void
    {
        parent::setUp();

        /**
         * @var \HttpEloquent\Interfaces\HttpClient|\Mockery\Mock
         */
        $this->client = Mockery::mock(HttpClient::class);

        $this->service = new Service(
            new ServiceConfig(
                new BaseUrl('https://foo.com'),
                new ModelMap([
                    'foos' => GenericModel::class,
                ]),
                new WrapperProperty('data')
            ),
            $this->client
        );
    }

    public function testCanGetMultipleModels(): void
    {
        /**
         * @var \Psr\Http\Message\ResponseInterface
         */
        $response = new Psr7Response(200, [], json_encode([
            'data' => [
                [ 'foo' => 'bar' ]
            ]
        ]));

        $this->client->shouldReceive([
            'get' => $response
        ]);

        $results = $this->service->foos()->get();

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertInstanceOf(GenericModel::class, $results->first());
        $this->assertEquals('bar',  $results->first()->foo);
        $this->assertEquals(1, count($results));
    }

    public function testCanGetMultipleModelsWithMagicMethod(): void
    {
        /**
         * @var \Psr\Http\Message\ResponseInterface
         */
        $response = new Psr7Response(200, [], json_encode([
            'data' => [
                [ 'foo' => 'bar' ]
            ]
        ]));

        $this->client->shouldReceive([
            'get' => $response
        ]);

        $results = $this->service->foos;

        $this->assertInstanceOf(Collection::class, $results);
        $this->assertInstanceOf(GenericModel::class, $results->first());
        $this->assertEquals('bar',  $results->first()->foo);
        $this->assertEquals(1, count($results));
    }

    public function testCanGetSingleModel(): void
    {
        /**
         * @var \Psr\Http\Message\ResponseInterface
         */
        $response = new Psr7Response(200, [], json_encode([
            'data' => [ 'foo' => 'bar' ]
        ]));

        $this->client->shouldReceive([
            'get' => $response
        ]);

        $model = $this->service->foos(1)->get();

        $this->assertInstanceOf(GenericModel::class, $model);

        $this->assertEquals('bar', $model->foo);
    }
}
