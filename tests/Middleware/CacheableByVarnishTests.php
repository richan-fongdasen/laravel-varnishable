<?php

namespace RichanFongdasen\Varnishable\Tests\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use RichanFongdasen\Varnishable\Middleware\CacheableByVarnish;
use RichanFongdasen\Varnishable\Tests\TestCase;

class CacheableByVarnishTests extends TestCase
{
    /**
     * Cacheable by varnish middleware object.
     *
     * @var \RichanFongdasen\Varnishable\Middleware\CacheableByVarnish
     */
    protected $middleware;

    /**
     * Setup the test environment
     *
     * @return void
     */
    public function setUp() :void
    {
        parent::setUp();

        $this->middleware = app(CacheableByVarnish::class);
    }

    /** @test */
    public function it_can_handle_the_incoming_request_and_manipulate_the_response_headers()
    {
        $content = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $request = new Request;
        $request->headers->set(\Varnishable::getConfig('esi_capability_header'), 'v1.0');

        $response = new Response;
        $response->header(\Varnishable::getConfig('cacheable_header'), '1');
        $response->setContent($content);

        $this->middleware->handle(
            $request,
            function (Request $request) use ($response) { return $response; },
            180
        );

        $actual = $response->headers->get(\Varnishable::getConfig('esi_reply_header'));
        $this->assertEquals('v1.0', $actual);

        $actual = $response->headers->get(\Varnishable::getConfig('cacheable_header'));
        $this->assertEquals('1', $actual);

        $actual = $response->headers->get('Cache-Control');
        $this->assertEquals('max-age=10800, public', $actual);

        $actual = $response->headers->get('etag');
        $this->assertEquals('"'. md5($content) .'"', $actual);
    }
}
