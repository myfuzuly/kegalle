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
        if ($request->hasSession() && $request->session()->has('_flash')) {
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
                return response(file_get_contents($file), 200, [
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
            return response(file_get_contents($file), 200, [
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
}
