<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // 1. Cegah Clickjacking (Mencegah website kamu di-frame/iframe oleh situs asing)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // 2. Mencegah browser melakukan MIME-type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // 3. Aktifkan XSS Filter bawaan browser tua
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // 4. Batasi informasi referrer saat navigasi keluar dari situs
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 5. Matikan akses fitur hardware sensitif yang tidak dibutuhkan
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // 6. Content Security Policy (CSP) - Mengontrol sumber eksekusi asset/script
        // Catatan: Jika ada CDN external (misal Google Fonts/Tailwind), sesuaikan domainnya di sini
        $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: blob: https:; connect-src 'self' https:;");

        return $response;
    }
}
