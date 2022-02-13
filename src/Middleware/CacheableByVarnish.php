<?php

namespace RichanFongdasen\Varnishable\Middleware;

use Closure;
use RichanFongdasen\Varnishable\VarnishableService;
use Symfony\Component\HttpFoundation\Request;

class CacheableByVarnish
{
    /**
     * Varnishable Service Object.
     *
     * @var VarnishableService
     */
    protected VarnishableService $varnishable;

    /**
     * CacheableByVarnish Middleware constructor.
     */
    public function __construct(VarnishableService $service)
    {
        $this->varnishable = $service;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Closure                                  $next
     * @param int                                       $cacheDuration
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, int $cacheDuration = 0)
    {
        $this->varnishable->setRequestHeaders($request->headers);

        if ($cacheDuration > 0) {
            $this->varnishable->setCacheDuration($cacheDuration);
        }

        $response = $next($request);

        return $this->varnishable->manipulate($response);
    }
}
