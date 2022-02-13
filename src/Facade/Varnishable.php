<?php

namespace RichanFongdasen\Varnishable\Facade;

use Illuminate\Support\Facades\Facade;
use RichanFongdasen\Varnishable\VarnishableService;

class Varnishable extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return VarnishableService::class;
    }
}
