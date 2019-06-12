<?php

namespace RichanFongdasen\Varnishable\Concerns;

use Carbon\Carbon;
use Exception;
use Symfony\Component\HttpFoundation\Response;

trait ManageLastModifiedHeader
{
    /**
     * Last modified header value.
     *
     * @var \Carbon\Carbon|null
     */
    protected $lastModified = null;

    /**
     * Add Last-Modified header to the current response.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return void
     */
    protected function addLastModifiedHeader(Response $response) :void
    {
        $lastModified = $this->getLastModifiedHeader();

        if ((bool) $this->getConfig('use_last_modified') && ($lastModified !== null)) {
            $response->setLastModified($lastModified);
        }
    }

    /**
     * Disable Last-Modified header for the current response.
     *
     * @return void
     */
    public function disableLastModified() :void
    {
        $this->setConfig('use_last_modified', false);
    }

    /**
     * Enable Last-Modified header for the current response.
     *
     * @return void
     */
    public function enableLastModified() :void
    {
        $this->setConfig('use_last_modified', true);
    }

    /**
     * Get last modified header for the current response.
     *
     * @return \Carbon\Carbon|null
     */
    public function getLastModifiedHeader() :?Carbon
    {
        return $this->lastModified;
    }

    /**
     * Set last modified header for the current response.
     *
     * @param \Carbon\Carbon|string $current
     *
     * @throws Exception
     *
     * @return void
     */
    public function setLastModifiedHeader($current) :void
    {
        if (!($current instanceof Carbon)) {
            $current = new Carbon($current);
        }

        if (($this->lastModified === null) || ($current->getTimestamp() > $this->lastModified->getTimestamp())) {
            $this->lastModified = $current;
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
     * Set configuration value for a specific key.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    abstract public function setConfig($key, $value) :void;
}
