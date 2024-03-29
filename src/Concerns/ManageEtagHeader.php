<?php

namespace RichanFongdasen\Varnishable\Concerns;

use Symfony\Component\HttpFoundation\Response;

trait ManageEtagHeader
{
    /**
     * Add an ETag header to the current response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return void
     */
    protected function addEtagHeader(Response $response): void
    {
        $useEtag = (bool) $this->getConfig('use_etag');
        $content = $response->getContent();

        if (($content !== false) && $useEtag) {
            $response->setEtag(md5($content));
        }
    }

    /**
     * Disable Etag for current request.
     *
     * @return void
     */
    public function disableEtag(): void
    {
        $this->setConfig('use_etag', false);
    }

    /**
     * Enable Etag for current request.
     *
     * @return void
     */
    public function enableEtag(): void
    {
        $this->setConfig('use_etag', true);
    }

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
