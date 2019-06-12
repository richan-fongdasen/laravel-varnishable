<?php

namespace RichanFongdasen\Varnishable\Concerns;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;

trait InvalidateVarnishCache
{
    /**
     * Flush entire cache for an application hostname.
     *
     * @param string $hostname
     *
     * @return void
     *
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function flush(string $hostname) :void
    {
        $this->sendBanRequest([
            'X-Ban-Host' => $hostname,
        ], 'FULLBAN');
    }

    /**
     * Generate ban request for an application hostname,
     * specified by a regular expression.
     *
     * @param string $hostname
     * @param string $pattern
     *
     * @return void
     *
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function banByPattern(string $hostname, string $pattern) :void
    {
        $this->sendBanRequest([
            'X-Ban-Host'  => $hostname,
            'X-Ban-Regex' => $pattern,
        ]);
    }

    /**
     * Generate ban request for an application hostname,
     * specified by a set of regular expression.
     *
     * @param string $hostname
     * @param array  $patterns
     *
     * @return void
     *
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function banByPatterns(string $hostname, array $patterns) :void
    {
        foreach ($patterns as $pattern) {
            $this->banByPattern($hostname, $pattern);
        }
    }

    /**
     * Generate ban request for an application hostname,
     * specified by a single urls.
     *
     * @param string $hostname
     * @param string $url
     *
     * @return void
     *
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function banByUrl(string $hostname, string $url) :void
    {
        $this->sendBanRequest([
            'X-Ban-Host' => $hostname,
            'X-Ban-Url'  => $url,
        ]);
    }

    /**
     * Generate ban request for an application hostname,
     * specified by a set of urls.
     *
     * @param string $hostname
     * @param array  $urls
     *
     * @return void
     *
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    public function banByUrls(string $hostname, array $urls) :void
    {
        foreach ($urls as $url) {
            $this->banByUrl($hostname, $url);
        }
    }

    /**
     * Get a valid varnish host url to send the
     * ban request.
     *
     * @param string $varnishHost
     *
     * @return string
     */
    protected function getVarnishUrl(string $varnishHost) :string
    {
        return 'http://'.$varnishHost.':'.$this->getConfig('varnish_port').'/';
    }

    /**
     * Send the ban request to every varnish hosts.
     *
     * @param array  $headers
     * @param string $method
     *
     * @return void
     *
     * @throws GuzzleException
     * @throws InvalidArgumentException
     */
    protected function sendBanRequest(array $headers, string $method = 'BAN') :void
    {
        $guzzle = $this->getGuzzle();

        foreach ((array) $this->getConfig('varnish_hosts') as $varnishHost) {
            $url = $this->getVarnishUrl($varnishHost);

            $guzzle->request($method, $url, ['headers' => $headers]);
        }
    }

    /**
     * Get configuration value for a specific key.
     *
     * @param string $key
     *
     * @return mixed
     */
    abstract public function getConfig($key);

    /**
     * Get guzzle client object.
     *
     * @return \GuzzleHttp\Client
     */
    abstract public function getGuzzle() :Client;
}
