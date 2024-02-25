<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->getMethod() === 'OPTIONS') {
            header("Access-Control-Allow-Origin: http://localhost:3000");
            header("Access-Control-Allow-Headers: ACCEPT, CONTENT-TYPE, X-CSRF-TOKEN");
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");
            header("Access-Control-Max-Age: 86400");
            return new Response('');
        }

        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Headers', 'ACCEPT, CONTENT-TYPE, X-CSRF-TOKEN');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, DELETE');

        return $response;
    }
}