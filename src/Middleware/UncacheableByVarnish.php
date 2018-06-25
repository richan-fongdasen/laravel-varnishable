<?php

namespace RichanFongdasen\Varnishable\Middleware;

use Closure;

class UncacheableByVarnish
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        \Varnishable::addUncacheableHeader($response);

        return $response;
    }
}
