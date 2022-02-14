<?php

namespace RichanFongdasen\Varnishable;

use GuzzleHttp\Client;
use RichanFongdasen\Varnishable\Concerns\InvalidateVarnishCache;
use RichanFongdasen\Varnishable\Concerns\ManageEtagHeader;
use RichanFongdasen\Varnishable\Concerns\ManageLastModifiedHeader;
use RichanFongdasen\Varnishable\Concerns\ManipulateHttpResponse;

class VarnishableService
{
    use InvalidateVarnishCache;
    use ManageEtagHeader;
    use ManageLastModifiedHeader;
    use ManipulateHttpResponse;

    /**
     * Varnishable configurations.
     *
     * @var array
     */
    protected array $config;

    /**
     * Guzzle client object.
     *
     * @var \GuzzleHttp\Client
     */
    protected Client $guzzle;

    /**
     * Class constructor.
     *
     * @param \GuzzleHttp\Client $guzzle
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(Client $guzzle)
    {
        $this->guzzle = $guzzle;
        $this->loadConfig();
    }

    /**
     * Get configuration value for a specific key.
     *
     * @param string|null $key
     *
     * @return mixed
     */
    public function getConfig(?string $key = null)
    {
        if ($key === null) {
            return $this->config;
        }

        return data_get($this->config, $key);
    }

    /**
     * Get guzzle client object.
     *
     * @return \GuzzleHttp\Client
     */
    public function getGuzzle(): Client
    {
        return $this->guzzle;
    }

    /**
     * Load the configurations.
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @return void
     */
    public function loadConfig(): void
    {
        $this->config = app('config')->get('varnishable');
    }

    /**
     * Set configuration value for a specific key.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function setConfig(string $key, $value): void
    {
        $this->config[$key] = $value;
    }

    /**
     * Replace Guzzle http client object with
     * a new one.
     *
     * @param \GuzzleHttp\Client $guzzle
     *
     * @return void
     */
    public function setGuzzle(Client $guzzle): void
    {
        $this->guzzle = $guzzle;
    }
}
