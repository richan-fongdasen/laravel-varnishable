<?php

namespace RichanFongdasen\Varnishable\Tests\Concerns;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Container\Container;
use RichanFongdasen\Varnishable\Tests\TestCase;
use RichanFongdasen\Varnishable\VarnishableService;

class InvalidateVarnishCacheTests extends TestCase
{
    /**
     * Mocked guzzle http client object.
     *
     * @var \GuzzleHttp\Client
     */
    protected $guzzle;

    /**
     * Dummy guzzle response
     *
     * @var Response
     */
    protected $response;

    /**
     * Varnishable service object.
     *
     * @var \RichanFongdasen\Varnishable\VarnishableService
     */
    protected $service;

    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp() :void
    {
        parent::setUp();

        $this->app['config']->set('varnishable.varnish_port', 8888);
        $this->app['config']->set('varnishable.varnish_hosts', ['192.168.10.10', '192.168.10.30']);

        $this->guzzle = \Mockery::mock(Client::class);
        $this->service = new VarnishableService($this->guzzle);

        $this->response = Container::getInstance()->make(Response::class, []);
    }

    /** @test */
    public function it_can_send_fullban_request_to_flush_the_entire_cache()
    {
        $options = ['headers' => ['X-Ban-Host' => 'localhost:8000']];
        $this->guzzle->shouldReceive('request')
            ->with('FULLBAN', 'http://192.168.10.10:8888/', $options)
            ->times(1)
            ->andReturn($this->response);
        $this->guzzle->shouldReceive('request')
            ->with('FULLBAN', 'http://192.168.10.30:8888/', $options)
            ->times(1)
            ->andReturn($this->response);

        $this->service->flush('localhost:8000');
    }

    /** @test */
    public function it_can_send_ban_requests_based_on_the_given_regex_patterns()
    {
        $options1 = ['headers' => [
            'X-Ban-Host' => 'localhost:8000',
            'X-Ban-Regex' => '/products/[0-9]*/view'
        ]];
        $options2 = ['headers' => [
            'X-Ban-Host' => 'localhost:8000',
            'X-Ban-Regex' => '/product-news/(.)*'
        ]];

        $this->guzzle->shouldReceive('request')
            ->with('BAN', 'http://192.168.10.10:8888/', $options1)->times(1)
            ->andReturn($this->response);
        $this->guzzle->shouldReceive('request')
            ->with('BAN', 'http://192.168.10.30:8888/', $options1)->times(1)
            ->andReturn($this->response);

        $this->guzzle->shouldReceive('request')
            ->with('BAN', 'http://192.168.10.10:8888/', $options2)->times(1)
            ->andReturn($this->response);
        $this->guzzle->shouldReceive('request')
            ->with('BAN', 'http://192.168.10.30:8888/', $options2)->times(1)
            ->andReturn($this->response);

        $this->service->banByPatterns('localhost:8000', [
            '/products/[0-9]*/view', '/product-news/(.)*'
        ]);
    }

    /** @test */
    public function it_can_send_ban_requests_based_on_the_given_urls()
    {
        $options1 = ['headers' => [
            'X-Ban-Host' => 'localhost:8000',
            'X-Ban-Url' => '/home'
        ]];
        $options2 = ['headers' => [
            'X-Ban-Host' => 'localhost:8000',
            'X-Ban-Url' => '/about-us/company-overview'
        ]];

        $this->guzzle->shouldReceive('request')
            ->with('BAN', 'http://192.168.10.10:8888/', $options1)->times(1)
            ->andReturn($this->response);
        $this->guzzle->shouldReceive('request')
            ->with('BAN', 'http://192.168.10.30:8888/', $options1)->times(1)
            ->andReturn($this->response);

        $this->guzzle->shouldReceive('request')
            ->with('BAN', 'http://192.168.10.10:8888/', $options2)->times(1)
            ->andReturn($this->response);
        $this->guzzle->shouldReceive('request')
            ->with('BAN', 'http://192.168.10.30:8888/', $options2)->times(1)
            ->andReturn($this->response);

        $this->service->banByUrls('localhost:8000', [
            '/home', '/about-us/company-overview'
        ]);
    }

    /** @test */
    public function it_can_generate_varnish_url_correctly()
    {
        $actual = $this->invokeMethod($this->service, 'getVarnishUrl', ['localhost']);
        $this->assertEquals('http://localhost:8888/', $actual);

        $actual = $this->invokeMethod($this->service, 'getVarnishUrl', ['google.com']);
        $this->assertEquals('http://google.com:8888/', $actual);
    }
}
