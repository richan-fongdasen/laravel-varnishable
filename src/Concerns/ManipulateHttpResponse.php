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
    protected HeaderBag $requestHeaders;

    /**
     * Acknowledge the ESI support and send a specific
     * HTTP header as a reply.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return void
     */
    protected function acknowledgeEsiSupport(Response $response): void
    {
        $esiHeader = $this->requestHeaders->get($this->getConfig('esi_capability_header'));

        if ($esiHeader !== null) {
            $response->headers->set($this->getConfig('esi_reply_header'), $esiHeader);
        }
    }

    /**
     * Add cacheable header so varnish can recognize
     * the response as a cacheable content.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function addCacheableHeader(Response $response): Response
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
     *
     * @return void
     */
    public function addUncacheableHeader(Response $response): void
    {
        $response->headers->set($this->getConfig('uncacheable_header'), '1');
    }

    /**
     * Normalize the cache duration value and convert
     * it to seconds.
     *
     * @return int
     */
    protected function getCacheDuration(): int
    {
        return (int) $this->getConfig('cache_duration') * 60;
    }

    /**
     * Manipulate the current Http response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function manipulate(Response $response): Response
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
     *
     * @return void
     */
    public function setCacheDuration(int $duration): void
    {
        $this->setConfig('cache_duration', $duration);
    }

    /**
     * Set the current Http request headers.
     *
     * @param \Symfony\Component\HttpFoundation\HeaderBag $headers
     *
     * @return void
     */
    public function setRequestHeaders(HeaderBag $headers): void
    {
        $this->requestHeaders = $headers;
    }

    /**
     * Check if the current response shouldn't be cached.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return bool
     */
    protected function shouldNotCache(Response $response): bool
    {
        $headers = (array) $response->headers->get($this->getConfig('uncacheable_header'));

        return count($headers) > 0;
    }

    /**
     * Add an ETag header to the current response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return void
     */
    abstract protected function addEtagHeader(Response $response): void;

    /**
     * Add Last-Modified header to the current response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return void
     */
    abstract protected function addLastModifiedHeader(Response $response): void;

    /**
     * Get configuration value for a specific key.
     *
     * @param string $key
     *
     * @return mixed
     */
    abstract public function getConfig(string $key);

    /**
     * Set configuration value for a specific key.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    abstract public function setConfig(string $key, $value): void;
}
