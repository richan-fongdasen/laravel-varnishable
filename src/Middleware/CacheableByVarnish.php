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
     * @param int|null                                  $cacheDuration
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $cacheDuration = null)
    {
        \Varnishable::setRequestHeaders($request->headers);

        if ((int) $cacheDuration > 0) {
            \Varnishable::setCacheDuration($cacheDuration);
        }

        $response = $next($request);

        return \Varnishable::manipulate($response);
    }
}
