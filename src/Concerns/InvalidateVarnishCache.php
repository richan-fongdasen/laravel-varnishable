<?php

namespace RichanFongdasen\Varnishable\Concerns;

trait InvalidateVarnishCache
{
    /**
     * Flush entire cache for an application hostname.
     *
     * @param string $hostname
     *
     * @return void
     */
    public function flush($hostname)
    {
        $this->sendBanRequest([
            'X-Ban-Host' => $hostname,
        ], 'FULLBAN');
    }

    /**
     * Generate ban request for an application hostname,
     * specified by a set of regular expression.
     *
     * @param string       $hostname
     * @param string|array $patterns
     *
     * @return void
     */
    public function banByPatterns($hostname, $patterns)
    {
        if (!is_array($patterns)) {
            return $this->sendBanRequest([
                'X-Ban-Host'  => $hostname,
                'X-Ban-Regex' => $patterns,
            ]);
        }

        foreach ($patterns as $pattern) {
            $this->banByPatterns($hostname, $pattern);
        }
    }

    /**
     * Generate ban request for an application hostname,
     * specified by a set of urls.
     *
     * @param string       $hostname
     * @param string|array $urls
     *
     * @return void
     */
    public function banByUrls($hostname, $urls)
    {
        if (!is_array($urls)) {
            return $this->sendBanRequest([
                'X-Ban-Host' => $hostname,
                'X-Ban-Url'  => $urls,
            ]);
        }

        foreach ($urls as $url) {
            $this->banByUrls($hostname, $url);
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
    protected function getVarnishUrl($varnishHost)
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
     */
    protected function sendBanRequest(array $headers, $method = 'BAN')
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
    abstract public function getGuzzle();
}
