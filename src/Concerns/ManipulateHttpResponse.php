<?php

namespace RichanFongdasen\Varnishable\Concerns;

use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\HeaderBag;

trait ManipulateHttpResponse
{
    /**
     * HTTP Request object.
     *
     * @var \Illuminate\Http\Request
     */
    protected $requestHeaders;

    /**
     * Acknowledge the ESI support and send a specific
     * HTTP header as a reply.
     *
     * @param \Illuminate\Http\Response $response
     * @return void
     */
    protected function acknowledgeEsiSupport(Response $response)
    {
        $esiHeader = $this->getConfig('esi_capability_header');

        if ($esiHeader = $this->requestHeaders->get($esiHeader)) {
            $response->header($this->getConfig('esi_reply_header'), $esiHeader);
        }
    }

    /**
     * Add cacheable header so varnish can recognize
     * the response as a cacheable content.
     *
     * @param \Illuminate\Http\Response $response
     * @param int                       $cacheDuration
     */
    protected function addCacheableHeader(Response $response, $cacheDuration)
    {
        $duration = $this->getCacheDuration($cacheDuration);

        return $response->header($this->getConfig('cacheable_header'), '1')
                ->header('Cache-Control', 'public, max-age=' . $duration);
    }

    /**
     * Add an ETag header to the current response.
     *
     * @param \Illuminate\Http\Response $response
     * @return void
     */
    protected function addEtagHeader(Response $response)
    {
        if ($this->getConfig('use_etag')) {
            $response->setEtag(md5($response->getContent()));
        }
    }

    /**
     * Normalize the given cache duration and convert
     * it to seconds.
     *
     * @param int $duration
     * @return int
     */
    protected function getCacheDuration($duration)
    {
        $cacheInMinutes = ((int) $duration > 0) ? $duration : $this->getConfig('cache_duration');

        return $cacheInMinutes * 60;
    }

    /**
     * Manipulate the current Http response.
     *
     * @param \Illuminate\Http\Response $response
     * @param int                       $cacheDuration
     * @return \Illuminate\Http\Response
     */
    public function manipulate(Response $response, $cacheDuration)
    {
        $this->acknowledgeEsiSupport($response);
        
        if ($this->shouldNotCache($response)) {
            return $response;
        }

        $this->addCacheableHeader($response, (int) $cacheDuration);
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
     * @param  \Illuminate\Http\Response $response [description]
     * @return boolean
     */
    protected function shouldNotCache(Response $response)
    {
        return $response->headers->get($this->getConfig('uncacheable_header'));
    }

    /**
     * Get configuration value for a specific key.
     *
     * @param  string $key
     * @return mixed
     */
    abstract public function getConfig($key);
}
