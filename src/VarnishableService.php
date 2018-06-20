<?php

namespace RichanFongdasen\Varnishable;

use GuzzleHttp\Client;
use RichanFongdasen\Varnishable\Concerns\InvalidateVarnishCache;
use RichanFongdasen\Varnishable\Concerns\ManipulateHttpResponse;

class VarnishableService
{
    use InvalidateVarnishCache;
    use ManipulateHttpResponse;

    /**
     * Varnishable configurations.
     *
     * @var array
     */
    protected $config;

    /**
     * Guzzle client object.
     *
     * @var \GuzzleHttp\Client
     */
    protected $guzzle;

    /**
     * Class constructor.
     *
     * @param \GuzzleHttp\Client $guzzle
     */
    public function __construct(Client $guzzle)
    {
        $this->guzzle = $guzzle;
        $this->loadConfig();
    }

    /**
     * Get configuration value for a specific key.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getConfig($key)
    {
        return data_get($this->config, $key);
    }

    /**
     * Get guzzle client object.
     *
     * @return \GuzzleHttp\Client
     */
    public function getGuzzle()
    {
        return $this->guzzle;
    }

    /**
     * Load the configurations.
     *
     * @return void
     */
    public function loadConfig()
    {
        $this->config = app('config')->get('varnishable');
    }

    /**
     * Replace the guzzle http client object with
     * a new one.
     *
     * @param \GuzzleHttp\Client $guzzle
     */
    public function setGuzzle(Client $guzzle)
    {
        $this->guzzle = $guzzle;
    }
}
