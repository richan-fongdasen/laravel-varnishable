<?php

namespace RichanFongdasen\Varnishable\Middleware;

use Closure;

class CacheableByVarnish
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next, int $cacheDuration = null)
    {
        \Varnishable::setRequestHeaders($request->headers);

        $response = $next($request);

        return \Varnishable::manipulate($response, $cacheDuration);
    }
}
