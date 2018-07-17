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
     * @param int                                        $cacheDuration
     */
    protected function addCacheableHeader(Response $response, $cacheDuration)
    {
        $duration = $this->getCacheDuration((int) $cacheDuration);

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
     * Normalize the given cache duration and convert
     * it to seconds.
     *
     * @param int $duration
     *
     * @return int|float
     */
    protected function getCacheDuration($duration)
    {
        $cacheInMinutes = ($duration > 0) ? $duration : $this->getConfig('cache_duration');

        return $cacheInMinutes * 60;
    }

    /**
     * Manipulate the current Http response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param int                                        $cacheDuration
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function manipulate(Response $response, $cacheDuration)
    {
        $this->acknowledgeEsiSupport($response);

        if ($this->shouldNotCache($response)) {
            return $response;
        }

        $this->addCacheableHeader($response, $cacheDuration);
        $this->addLastModifiedHeader($response);
        $this->addEtagHeader($response);

        return $response;
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
}
