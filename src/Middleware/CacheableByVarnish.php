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
     * @var \RichanFongdasen\Varnishable\VarnishableService
     */
    protected $varnishable;

    /**
     * CacheableByVarnish Middleware constructor.
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
