<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Cegah MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Cegah Clickjacking
        $response->headers->set('X-Frame-Options', 'DENY');

        // Proteksi XSS browser lama
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Paksa HTTPS
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Content Security Policy — cegah XSS injection
    $response->headers->set('Content-Security-Policy',
        "default-src 'self'; " .
        "script-src 'self' 'unsafe-inline' 'unsafe-eval' " .
            "https://cdnjs.cloudflare.com " .
            "https://cdn.jsdelivr.net " .
            "https://cdn.tailwindcss.com; " .
        "style-src 'self' 'unsafe-inline' " .
            "https://cdnjs.cloudflare.com " .
            "https://cdn.jsdelivr.net " .
            "https://fonts.googleapis.com " .
            "https://cdn.tailwindcss.com; " .
        "font-src 'self' " .
            "https://fonts.gstatic.com " .
            "https://cdnjs.cloudflare.com; " .
        "img-src 'self' data: blob: " .
            "https://quickchart.io; " .
        "connect-src 'self' " .
            "https://cdn.tailwindcss.com;"
    );

        // Cache Control
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');

        // Sembunyikan versi server
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}