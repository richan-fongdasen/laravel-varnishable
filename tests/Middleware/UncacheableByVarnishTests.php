<?php

namespace RichanFongdasen\Varnishable\Tests\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use RichanFongdasen\Varnishable\Middleware\UncacheableByVarnish;
use RichanFongdasen\Varnishable\Tests\TestCase;

class UncacheableByVarnishTests extends TestCase
{
    /**
     * Cacheable by varnish middleware object.
     *
     * @var \RichanFongdasen\Varnishable\Middleware\UncacheableByVarnish
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

        $this->middleware = app(UncacheableByVarnish::class);
    }

    /** @test */
    public function it_can_handle_the_incoming_request_and_manipulate_the_response_headers()
    {
        $request = new Request;
        $response = new Response;

        $this->middleware->handle(
            $request,
            function (Request $request) use ($response) { return $response; }
        );

        $actual = $response->headers->get(\Varnishable::getConfig('uncacheable_header'));
        $this->assertEquals('1', $actual);
    }
}
