<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\AuditService;

class AuditRequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response->isSuccessful() || $response->isRedirection()) {
            $this->logAction($request);
        }

        return $response;
    }

    protected function logAction(Request $request): void
    {
        $action = null;

        if ($request->is('login') && $request->isMethod('POST')) {
            $action = 'login';
        } elseif ($request->is('logout') && $request->isMethod('POST')) {
            $action = 'logout';
        } elseif ($request->routeIs('*.export')) {
            $action = 'export';
        } elseif ($request->routeIs('*.scan') || str_contains($request->path(), 'scan-qr')) {
            $action = 'scan_qr';
        }

        if ($action) {
            AuditService::log($action);
        }
    }
}
