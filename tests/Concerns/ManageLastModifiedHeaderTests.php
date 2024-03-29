<?php

namespace RichanFongdasen\Varnishable\Tests\Concerns;

use Carbon\Carbon;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\Test;
use RichanFongdasen\Varnishable\Tests\TestCase;
use RichanFongdasen\Varnishable\VarnishableService;

class ManageLastModifiedHeaderTests extends TestCase
{
    /**
     * Http response object.
     *
     * @var \Illuminate\Http\Response
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
    public function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('varnishable.varnish_port', 8888);

        $this->response = new Response;
        $this->service = app(VarnishableService::class);
    }

    #[Test]
    public function it_can_add_last_modified_header_to_the_current_response_object()
    {
        $time = new Carbon('2018-07-16 23:00:00');

        $this->service->setLastModifiedHeader('2018-07-16 23:00:00');
        $this->invokeMethod($this->service, 'addLastModifiedHeader', [$this->response]);

        $this->assertEquals($time->toRfc7231String(), $this->response->headers->get('Last-Modified'));
    }

    #[Test]
    public function it_can_disable_last_modified_header_at_runtime()
    {
        $this->service->enableLastModified();
        $this->service->disableLastModified();

        $this->assertFalse($this->service->getConfig('use_last_modified'));
    }

    #[Test]
    public function it_can_enable_last_modified_header_at_runtime()
    {
        $this->service->disableLastModified();
        $this->service->enableLastModified();

        $this->assertTrue($this->service->getConfig('use_last_modified'));
    }

    #[Test]
    public function it_returns_last_modified_value_correctly()
    {
        $time = new Carbon('2018-07-16 23:00:00');

        $this->service->setLastModifiedHeader('2018-07-16 23:00:00');

        $actual = $this->service->getLastModifiedHeader();

        $this->assertInstanceOf(Carbon::class, $actual);
        $this->assertEquals($time->getTimestamp(), $actual->getTimestamp());
    }

    #[Test]
    public function it_can_set_last_modified_header_with_the_newest_timestamp()
    {
        $times = [
            '2016-01-01 00:00:00',
            '2018-01-01 00:00:00',
            '2017-01-01 00:00:00',
            '2015-01-01 00:00:00',
        ];
        $expected = new Carbon($times[1]);

        foreach ($times as $time) {
            $this->service->setLastModifiedHeader($time);
        }

        $actual = $this->service->getLastModifiedHeader();

        $this->assertInstanceOf(Carbon::class, $actual);
        $this->assertEquals($expected->getTimestamp(), $actual->getTimestamp());
    }
}
