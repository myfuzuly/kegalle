<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CachePublicResponse
{
    public function handle(Request $request, Closure $next, int $ttl = 300): Response
    {
        // Only cache plain GET requests from unauthenticated guests; skip admin/api paths
        if (!$request->isMethod('GET') || auth()->check() || $request->is('admin/*', 'api/*')) {
            return $next($request);
        }

        // Skip if there's flash data (form errors, success messages shown after redirect)
        // Laravel always stores an empty _flash bag, so has('_flash') was true for every returning visitor
        if ($request->hasSession() && ($request->session()->get('_flash.old') || $request->session()->get('_flash.new'))) {
            return $next($request);
        }

        // Key: path + query only — immune to http/https mismatch from proxy
        $qs  = $request->getQueryString();
        $key = 'pagecache_' . sha1($request->path() . ($qs ? '?' . $qs : ''));
        $dir = storage_path('framework/pagecache');

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $file = $dir . '/' . $key . '.html';

        if (file_exists($file)) {
            $age = time() - filemtime($file);
            if ($age < $ttl) {
                return response($this->personalise(file_get_contents($file)), 200, [
                    'Content-Type' => 'text/html; charset=UTF-8',
                    'X-Cache'      => 'HIT',
                ]);
            }
            // Stale: only ONE request regenerates; all others get old content instantly
            $lock = $file . '.lock';
            if (!file_exists($lock) || (time() - filemtime($lock)) > 60) {
                touch($lock);
                $response = $next($request);
                if ($response->getStatusCode() === 200 && !auth()->check()) {
                    file_put_contents($file, $response->getContent(), LOCK_EX);
                }
                @unlink($lock);
                return $response;
            }
            return response($this->personalise(file_get_contents($file)), 200, [
                'Content-Type' => 'text/html; charset=UTF-8',
                'X-Cache'      => 'STALE',
            ]);
        }

        $response = $next($request);

        if ($response->getStatusCode() === 200 && !auth()->check()) {
            file_put_contents($file, $response->getContent(), LOCK_EX);
        }

        return $response;
    }

    /**
     * A cached page carries the first visitor's CSP nonce and CSRF token. Swap in this request's,
     * otherwise every inline script is blocked by CSP and forms fail with 419.
     */
    private function personalise(string $html): string
    {
        $nonce = SecurityHeaders::nonce();
        $html  = preg_replace('/\bnonce="[^"]*"/', 'nonce="' . $nonce . '"', $html);

        $token = csrf_token();
        $html  = preg_replace('/(<meta name="csrf-token" content=")[^"]*"/', '${1}' . $token . '"', $html);
        return preg_replace('/(name="_token" value=")[^"]*"/', '${1}' . $token . '"', $html);
    }
}
