<?php

namespace App\Http\Middleware;

use Closure;
use Core\Services\Tenant;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleSubdomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $subdomain = $request->route()->parameter('subdomain');

        if ($subdomain !== null) {
            Tenant::setSubDomain($subdomain, true)->checkTenant();

            $request->route()->forgetParameter('subdomain');
        }

        return $next($request);
    }
}
