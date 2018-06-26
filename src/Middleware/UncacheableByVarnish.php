<?php

namespace RichanFongdasen\Varnishable\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Request;

class UncacheableByVarnish
{
    /**
     * Handle an incoming request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Closure                                  $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        \Varnishable::addUncacheableHeader($response);

        return $response;
    }
}
