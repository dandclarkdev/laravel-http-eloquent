<?php

namespace Tests\Unit;

use Mockery;
use stdClass;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;
use LaravelHttpEloquent\Service;
use Illuminate\Support\Collection;
use Illuminate\Http\Client\Response;
use LaravelHttpEloquent\GenericModel;
use LaravelHttpEloquent\Types\BaseUrl;
use LaravelHttpEloquent\Types\ModelMap;
use LaravelHttpEloquent\Types\ServiceConfig;
use LaravelHttpEloquent\Interfaces\HttpClient;

class ServiceTest extends TestCase
{
    /**
     * @var \LaravelHttpEloquent\Service
     */
    protected $service;

    /**
     * @var \LaravelHttpEloquent\Interfaces\HttpClient|\Mockery\Mock
     */
    protected $client;

    public function setUp(): void
    {
        parent::setUp();

        /**
         * @var \LaravelHttpEloquent\Interfaces\HttpClient|\Mockery\Mock
         */
        $this->client = Mockery::mock(HttpClient::class);

        $this->service = new Service(
            new ServiceConfig(
                new BaseUrl('https://foo.com'),
                new ModelMap([
                    'foos' => GenericModel::class,
                ])
            ),
            $this->client
        );
    }

    public function testServiceIsAService(): void
    {
        $this->assertInstanceOf(
            Service::class,
            $this->service
        );
    }

    public function testOneMethodSetsPropertiesCorrectly(): void
    {
        $this->service->one();

        $this->assertEquals(GenericModel::class, $this->service->getResolveTo());

        $this->assertFalse($this->service->getPlural());

        $this->assertFalse($this->service->getImmutableResolveTo());

        $this->service->one(stdClass::class);

        $this->assertTrue($this->service->getImmutableResolveTo());

        $this->assertEquals(stdClass::class, $this->service->getResolveTo());
    }

    public function testPathWithModelNotInMapSetsPropertiesCorrectly(): void
    {
        $this->service->blah();

        $this->assertEquals(GenericModel::class, $this->service->getResolveTo());
    }

    public function testManyMethodSetsPropertiesCorrectly(): void
    {
        $this->service->many();

        $this->assertEquals(GenericModel::class, $this->service->getResolveTo());

        $this->assertTrue($this->service->getPlural());

        $this->assertFalse($this->service->getImmutableResolveTo());

        $this->service->many(stdClass::class);

        $this->assertTrue($this->service->getImmutableResolveTo());

        $this->assertEquals(stdClass::class, $this->service->getResolveTo());
    }

    public function testPaginationSetsPropertiesCorrectly(): void
    {
        $this->assertEquals('', (string) $this->service->getQuery());

        $this->service->page(1);

        $this->assertEquals('page=1', (string) $this->service->getQuery());

        $this->service->perPage(10);

        $this->assertEquals('page=1&per_page=10', (string) $this->service->getQuery());
    }

    public function testFirstMethodWorks(): void
    {
        /**
         * @var \Illuminate\Http\Client\Response|\Mockery\Mock
         */
        $response = Mockery::mock(Response::class);

        $response->shouldReceive([
            'collect' => collect([
                [
                    'foo' => 'bar'
                ]
            ])
        ]);

        $this->client->shouldReceive([
            'get' => $response
        ]);

        $model = $this->service->first();

        $this->assertInstanceOf(GenericModel::class, $model);

        $this->assertEquals('bar', $model->foo);
    }

    public function testCanGetMultipleModels(): void
    {
        /**
         * @var \Illuminate\Http\Client\Response|\Mockery\Mock
         */
        $response = Mockery::mock(Response::class);

        $response->shouldReceive([
            'collect' => collect([
                [
                    'foo' => 'bar'
                ]
            ])
        ]);

        $this->client->shouldReceive([
            'get' => $response
        ]);

        $collection = $this->service->foos()->get();

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertInstanceOf(GenericModel::class, $collection->first());
        $this->assertEquals('bar', $collection->first()->foo);
        $this->assertEquals(1, $collection->count());
    }

    public function testCanGetSingleModel(): void
    {
        /**
         * @var \Illuminate\Http\Client\Response|\Mockery\Mock
         */
        $response = Mockery::mock(Response::class);

        $response->shouldReceive([
            'json' => [
                'foo' => 'bar'
            ]
        ]);

        $this->client->shouldReceive([
            'get' => $response
        ]);

        $model = $this->service->foos(1)->get();

        $this->assertInstanceOf(GenericModel::class, $model);

        $this->assertEquals('bar', $model->foo);
    }

    public function testCanFindModel(): void
    {
        /**
         * @var \Illuminate\Http\Client\Response|\Mockery\Mock
         */
        $response = Mockery::mock(Response::class);

        $response->shouldReceive([
            'json' => [
                'foo' => 'bar'
            ]
        ]);

        $this->client->shouldReceive([
            'get' => $response
        ]);

        $model = $this->service->foos()->find(1);

        $this->assertInstanceOf(GenericModel::class, $model);

        $this->assertEquals('bar', $model->foo);
    }

    public function testCanCreateModel(): void
    {
        /**
         * @var \Illuminate\Http\Client\Response|\Mockery\Mock
         */
        $response = Mockery::mock(Response::class);

        $response->shouldReceive([
            'json' => [
                'foo' => 'bar'
            ]
        ]);

        $this->client->shouldReceive([
            'post' => $response
        ]);

        $model = $this->service->foos()->create([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(GenericModel::class, $model);

        $this->assertEquals('bar', $model->foo);
    }

    public function testCanUpdateModel(): void
    {
        /**
         * @var \Illuminate\Http\Client\Response|\Mockery\Mock
         */
        $response = Mockery::mock(Response::class);

        $response->shouldReceive([
            'json' => [
                'foo' => 'bar'
            ]
        ]);

        $this->client->shouldReceive([
            'patch' => $response
        ]);

        $model = $this->service->foos(1)->update([
            'foo' => 'bar'
        ]);

        $this->assertInstanceOf(GenericModel::class, $model);

        $this->assertEquals('bar', $model->foo);
    }

    public function testCanDeleteModel(): void
    {
        /**
         * @var \Illuminate\Http\Client\Response|\Mockery\Mock
         */
        $response = Mockery::mock(Response::class);

        $response->shouldReceive([
            'json' => [
                'foo' => 'bar'
            ]
        ]);

        $this->client->shouldReceive([
            'delete' => $response
        ]);

        $model = $this->service->foos(1)->delete();

        $this->assertInstanceOf(GenericModel::class, $model);

        $this->assertEquals('bar', $model->foo);
    }

    public function testCanGetModelWithMagicMethod(): void
    {
        /**
         * @var \Illuminate\Http\Client\Response|\Mockery\Mock
         */
        $response = Mockery::mock(Response::class);

        $response->shouldReceive([
            'collect' => collect([
                [
                    'foo' => 'bar'
                ]
            ])
        ]);

        $this->client->shouldReceive([
            'get' => $response
        ]);

        $collection = $this->service->foos;

        $this->assertTrue($this->service->getPlural());
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertInstanceOf(GenericModel::class, $collection->first());
        $this->assertEquals('bar', $collection->first()->foo);
        $this->assertEquals(1, $collection->count());
    }
}
