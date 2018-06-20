<?php

namespace RichanFongdasen\Varnishable\Tests\Concerns;

use Illuminate\Http\Response;
use RichanFongdasen\Varnishable\Tests\TestCase;
use RichanFongdasen\Varnishable\VarnishableService;
use Symfony\Component\HttpFoundation\HeaderBag;

class ManipulateHttpResponseTests extends TestCase
{
    /**
     * Http header bag object.
     *
     * @var \Symfony\Component\HttpFoundation\HeaderBag
     */
    protected $headers;

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

        $this->headers = new HeaderBag;
        $this->response = new Response;
        $this->service = app(VarnishableService::class);
    }

    /** @test */
    public function it_will_acknowledge_esi_supports()
    {
        $this->headers->set(\Varnishable::getConfig('esi_capability_header'), 'v1.0');

        $this->service->setRequestHeaders($this->headers);
        $this->invokeMethod($this->service, 'acknowledgeEsiSupport', [$this->response]);

        $actual = $this->response->headers->get(\Varnishable::getConfig('esi_reply_header'));
        $this->assertEquals('v1.0', $actual);
    }

    /** @test */
    public function it_wont_acknowledge_esi_supports_when_there_was_no_esi_header_specified()
    {
        $this->service->setRequestHeaders($this->headers);

        $this->invokeMethod($this->service, 'acknowledgeEsiSupport', [$this->response]);

        $actual = $this->response->headers->get(\Varnishable::getConfig('esi_reply_header'));
        $this->assertNull($actual);
    }

    /** @test */
    public function it_can_add_cacheable_header_to_the_current_response_object()
    {
        $this->invokeMethod($this->service, 'addCacheableHeader', [$this->response, 60]);

        $cacheable = $this->response->headers->get(\Varnishable::getConfig('cacheable_header'));
        $cacheControl = $this->response->headers->get('Cache-Control');

        $this->assertEquals('1', $cacheable);
        $this->assertEquals('max-age=3600, public', $cacheControl);
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
    public function it_can_calculate_total_cache_duration_in_seconds()
    {
        $data = [5, 15, 30, 60];
        $expected = [300, 900, 1800, 3600];

        for ($i=0; $i<count($data); $i++) {
            $actual = $this->invokeMethod($this->service, 'getCacheDuration', [$data[$i]]);

            $this->assertEquals($expected[$i], $actual);
        }
    }

    /** @test */
    public function it_can_fully_manipulate_http_response_as_expected()
    {
        $content = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $this->headers->set(\Varnishable::getConfig('esi_capability_header'), 'v1.0');
        $this->response->header(\Varnishable::getConfig('cacheable_header'), '1');
        $this->response->setContent($content);

        $this->service->setRequestHeaders($this->headers);

        $this->service->manipulate($this->response, 120);

        $actual = $this->response->headers->get(\Varnishable::getConfig('esi_reply_header'));
        $this->assertEquals('v1.0', $actual);

        $actual = $this->response->headers->get(\Varnishable::getConfig('cacheable_header'));
        $this->assertEquals('1', $actual);

        $actual = $this->response->headers->get('Cache-Control');
        $this->assertEquals('max-age=7200, public', $actual);

        $actual = $this->response->headers->get('etag');
        $this->assertEquals('"'. md5($content) .'"', $actual);
    }

    /** @test */
    public function it_can_partially_manipulate_http_response_object()
    {
        $this->headers->set(\Varnishable::getConfig('esi_capability_header'), 'v1.0');
        $this->response->header(\Varnishable::getConfig('uncacheable_header'), '1');

        $this->service->setRequestHeaders($this->headers);

        $this->service->manipulate($this->response, 120);

        $actual = $this->response->headers->get(\Varnishable::getConfig('esi_reply_header'));
        $this->assertEquals('v1.0', $actual);

        $actual = $this->response->headers->get(\Varnishable::getConfig('cacheable_header'));
        $this->assertNull($actual);

        $actual = $this->response->headers->get('etag');
        $this->assertNull($actual);
    }

    /** @test */
    public function it_can_confirm_if_the_current_response_should_not_be_cached()
    {
        $uncacheableResponse = (new Response)->header(\Varnishable::getConfig('uncacheable_header'), '1');
        $cacheableResponse = (new Response)->header(\Varnishable::getConfig('cacheable_header'), '1');

        $uncacheable = $this->invokeMethod($this->service, 'shouldNotCache', [$uncacheableResponse]);
        $cacheable = $this->invokeMethod($this->service, 'shouldNotCache', [$cacheableResponse]);

        $this->assertEquals('1', $uncacheable);
        $this->assertNull($cacheable);
    }
}
