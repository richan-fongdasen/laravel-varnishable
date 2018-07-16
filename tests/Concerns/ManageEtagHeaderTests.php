<?php

namespace RichanFongdasen\Varnishable\Tests\Concerns;

use Illuminate\Http\Response;
use RichanFongdasen\Varnishable\Tests\TestCase;
use RichanFongdasen\Varnishable\VarnishableService;

class ManageEtagHeaderTests extends TestCase
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
     */
    public function setUp()
    {
        parent::setUp();

        $this->app['config']->set('varnishable.varnish_port', 8888);

        $this->response = new Response;
        $this->service = app(VarnishableService::class);
    }

    /** @test */
    public function it_can_add_an_etag_header_to_the_current_response_object()
    {
        $content = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $this->response->setContent($content);

        $this->invokeMethod($this->service, 'addEtagHeader', [$this->response]);

        $actual = $this->response->headers->get('etag');
        $this->assertEquals('"'. md5($content) .'"', $actual);
    }

    /** @test */
    public function it_can_disable_etag_header_at_runtime()
    {
        $this->service->enableEtag();
        $this->service->disableEtag();

        $this->assertFalse($this->service->getConfig('use_etag'));
    }

    /** @test */
    public function it_can_enable_etag_header_at_runtime()
    {
        $this->service->disableEtag();
        $this->service->enableEtag();

        $this->assertTrue($this->service->getConfig('use_etag'));
    }
}
