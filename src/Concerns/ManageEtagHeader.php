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
    protected function addEtagHeader(Response $response) :void
    {
        if ((bool) $this->getConfig('use_etag')) {
            $response->setEtag(md5($response->getContent()));
        }
    }

    /**
     * Disable Etag for current request.
     *
     * @return void
     */
    public function disableEtag() :void
    {
        $this->setConfig('use_etag', false);
    }

    /**
     * Enable Etag for current request.
     *
     * @return void
     */
    public function enableEtag() :void
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
    abstract public function getConfig($key);

    /**
     * Set configuration value for a specific key.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    abstract public function setConfig($key, $value) :void;
}
