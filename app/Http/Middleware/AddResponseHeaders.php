<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AddResponseHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $response->header('X-Custom-Header', 'value');
        $response->header('Content-Type', 'application/json');
        $response->header('Strict-Transport-Security', 'max-age=31536000;preload');
        $response->header('X-Frame-Options', 'DENY');
        $response->header('X-XSS-Protection', '1; mode=block');
        $response->header('X-Content-Type-Options', 'nosniff');
        $response->header('Content-Security-Policy', 'upgrade-insecure-requests');
        $response->header('Server', 'Prohealthi');
        $response->header('Set-Cookie', '$1; HttpOnly; Secure; SameSite=Strict; path=/prohealthi');
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->remove('X-Powered-By');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', '*');
        $response->headers->set('Access-Control-Allow-Headers', '*');
        
        return $response;
    }
}
