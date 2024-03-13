<?php

namespace RichanFongdasen\Varnishable\Tests;

use GuzzleHttp\Client;
use PHPUnit\Framework\Attributes\Test;
use RichanFongdasen\Varnishable\VarnishableService;

class VarnishableServiceTests extends TestCase
{
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
    public function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('varnishable.varnish_port', 8888);

        $this->service = app(VarnishableService::class);
    }

    #[Test]
    public function it_returns_all_of_configurations_on_empty_key()
    {
        $configs = $this->service->getConfig();

        $this->assertTrue(is_array($configs));
        $this->assertEquals('127.0.0.1', $configs['varnish_hosts']);
        $this->assertEquals('8888', $configs['varnish_port']);
    }

    #[Test]
    public function it_returns_configuration_values_correctly()
    {
        $this->assertEquals(8888, $this->service->getConfig('varnish_port'));
        $this->assertEquals('localhost:8000', $this->service->getConfig('application_hosts'));

        $this->assertEquals('X-Varnish-Cacheable', $this->service->getConfig('cacheable_header'));
    }

    #[Test]
    public function it_returns_guzzle_client_object_as_expected()
    {
        $guzzle = $this->service->getGuzzle();

        $this->assertInstanceOf(Client::class, $guzzle);
    }

    #[Test]
    public function it_can_set_configuration_values_at_runtime()
    {
        $this->service->setConfig('cache_duration', 600);

        $this->assertEquals(600, $this->service->getConfig('cache_duration'));
    }

    #[Test]
    public function it_can_replace_the_guzzle_client_object_with_a_new_one()
    {
        $newGuzzle = new Client(['base_uri' => 'https://laravel.com/', 'timeout' => 10]);

        $this->service->setGuzzle($newGuzzle);

        $guzzle = $this->service->getGuzzle();
        $this->assertInstanceOf(Client::class, $guzzle);

        $this->assertEquals(10, $guzzle->getConfig('timeout'));
        $this->assertEquals('laravel.com', $guzzle->getConfig('base_uri')->getHost());
    }
}
