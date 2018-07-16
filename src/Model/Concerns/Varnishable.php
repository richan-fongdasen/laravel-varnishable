<?php

namespace RichanFongdasen\Varnishable\Model\Concerns;

use RichanFongdasen\Varnishable\VarnishableObserver;

trait Varnishable
{
    /**
     * Boot the Varnishable trait by attaching
     * a new observer to the current model.
     *
     * @return void
     */
    public static function bootVarnishable()
    {
        static::observe(app(VarnishableObserver::class));
    }

    /**
     * When a model is being unserialized, fire eloquent wakeup event.
     *
     * @return void
     */
    public function __wakeup()
    {
        parent::__wakeup();

        event('eloquent.wakeup: ' . get_class($this), $this);
    }
}
