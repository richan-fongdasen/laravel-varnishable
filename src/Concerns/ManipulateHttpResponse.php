<?php

namespace RichanFongdasen\Varnishable\Concerns;

use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Response;

trait ManipulateHttpResponse
{
    /**
     * HTTP Request object.
     *
     * @var \Symfony\Component\HttpFoundation\HeaderBag
     */
    protected $requestHeaders;

    /**
     * Acknowledge the ESI support and send a specific
     * HTTP header as a reply.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return void
     */
    protected function acknowledgeEsiSupport(Response $response)
    {
        $esiHeader = $this->getConfig('esi_capability_header');

        if ($esiHeader = $this->requestHeaders->get($esiHeader)) {
            $response->headers->set($this->getConfig('esi_reply_header'), $esiHeader);
        }
    }

    /**
     * Add cacheable header so varnish can recognize
     * the response as a cacheable content.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    protected function addCacheableHeader(Response $response)
    {
        $duration = $this->getCacheDuration();

        $response->headers->set($this->getConfig('cacheable_header'), '1');
        $response->headers->set('Cache-Control', 'public, max-age='.$duration);

        return $response;
    }

    /**
     * Add uncacheable header so varnish can recognize
     * the response as an uncacheable content.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function addUncacheableHeader(Response $response)
    {
        return $response->headers->set($this->getConfig('uncacheable_header'), '1');
    }

    /**
     * Normalize the cache duration value and convert
     * it to seconds.
     *
     * @return int|float
     */
    protected function getCacheDuration()
    {
        return $this->getConfig('cache_duration') * 60;
    }

    /**
     * Manipulate the current Http response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function manipulate(Response $response)
    {
        $this->acknowledgeEsiSupport($response);

        if ($this->shouldNotCache($response)) {
            return $response;
        }

        $this->addCacheableHeader($response);
        $this->addLastModifiedHeader($response);
        $this->addEtagHeader($response);

        return $response;
    }

    /**
     * Set cache duration value in minutes. This value will
     * be added to the HTTP response's Cache-Control header.
     *
     * @param int $duration [Cache duration value in minutes]
     */
    public function setCacheDuration($duration)
    {
        $this->setConfig('cache_duration', (int) $duration);
    }

    /**
     * Set the current Http request headers.
     *
     * @param \Symfony\Component\HttpFoundation\HeaderBag $headers
     */
    public function setRequestHeaders(HeaderBag $headers)
    {
        $this->requestHeaders = $headers;
    }

    /**
     * Check if the current response shouldn't be cached.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return string|array
     */
    protected function shouldNotCache(Response $response)
    {
        return $response->headers->get($this->getConfig('uncacheable_header'));
    }

    /**
     * Add an ETag header to the current response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return void
     */
    abstract protected function addEtagHeader(Response $response);

    /**
     * Add Last-Modified header to the current response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return void
     */
    abstract protected function addLastModifiedHeader(Response $response);

    /**
     * Get configuration value for a specific key.
     *
     * @param string $key
     *
     * @return mixed
     */
    abstract public function getConfig($key);

    /**
     * Set configuration value for a specific key.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    abstract public function setConfig($key, $value);
}
