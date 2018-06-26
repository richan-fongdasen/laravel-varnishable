<?php

namespace RichanFongdasen\Varnishable\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Request;

class CacheableByVarnish
{
    /**
     * Handle an incoming request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Closure                                  $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $cacheDuration = null)
    {
        \Varnishable::setRequestHeaders($request->headers);

        $response = $next($request);

        return \Varnishable::manipulate($response, $cacheDuration);
    }
}
