<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureHeaders
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

        // X-Frame-Options: Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        
        // X-Content-Type-Options: Prevent MIME-type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // X-XSS-Protection: Enable XSS protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        // Strict-Transport-Security (HSTS): Only apply in production with HTTPS
        if (app()->environment('production') && $request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }
        
        // Content-Security-Policy: Basic safe policy
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval'; " .
               "style-src 'self' 'unsafe-inline'; " .
               "img-src 'self' data: https:; " .
               "font-src 'self'; " .
               "connect-src 'self'; " .
               "frame-ancestors 'self'; " .
               "base-uri 'self'; " .
               "form-action 'self'";
        
        $response->headers->set('Content-Security-Policy', $csp);
        
        // Referrer-Policy: Control referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Permissions-Policy: Control feature usage
        $permissions = "geolocation=(), " .
                      "microphone=(), " .
                      "camera=(), " .
                      "payment=(), " .
                      "usb=(), " .
                      "magnetometer=(), " .
                      "gyroscope=(), " .
                      "accelerometer=()";
        
        $response->headers->set('Permissions-Policy', $permissions);

        return $response;
    }
}
