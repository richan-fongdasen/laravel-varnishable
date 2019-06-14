<?php

namespace RichanFongdasen\Varnishable\Middleware;

use Closure;
use RichanFongdasen\Varnishable\VarnishableService;
use Symfony\Component\HttpFoundation\Request;

class UncacheableByVarnish
{
    /**
     * Varnishable Service Object.
     *
     * @var \RichanFongdasen\Varnishable\VarnishableService
     */
    protected $varnishable;

    /**
     * UncacheableByVarnish Middleware constructor.
     */
    public function __construct()
    {
        $this->varnishable = app(VarnishableService::class);
    }

    /**
     * Handle an incoming request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Closure                                  $next
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $this->varnishable->addUncacheableHeader($response);

        return $response;
    }
}
