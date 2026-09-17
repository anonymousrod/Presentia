<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $trustedProxies = env('TRUSTED_PROXIES');
        if ($trustedProxies !== null && $trustedProxies !== '') {
            $middleware->trustProxies(at: $trustedProxies === '*' ? '*' : array_map('trim', explode(',', $trustedProxies)));
        } else {
            $middleware->trustProxies(at: in_array(env('APP_ENV', 'local'), ['local', 'testing']) ? '*' : []);
        }
        $middleware->appendToGroup('web', \App\Http\Middleware\ForcePasswordChange::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\AuditRequestMiddleware::class);
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckChurchSubscription::class);
        $middleware->alias([
            'super_admin' => \App\Http\Middleware\EnsureUserIsSuperAdmin::class,
            'subscription.active' => \App\Http\Middleware\CheckChurchSubscription::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, \Illuminate\Http\Request $request) {
            if (!$request->expectsJson()) {
                if ($request->is('activities/*')) {
                    return redirect()->route('activities.index')->with('info', 'L\'activité demandée n\'est plus disponible ou a été supprimée.');
                }
                if ($request->is('admin/activities/*')) {
                    return redirect()->route('admin.activities.index')->with('info', 'L\'activité demandée n\'est plus disponible ou a été supprimée.');
                }
                if ($request->is('admin/groups/*')) {
                    return redirect()->route('admin.groups.index')->with('info', 'Le groupe demandé n\'est plus disponible.');
                }
                if ($request->is('admin/users/*')) {
                    return redirect()->route('admin.users.index')->with('info', 'Le compte de ce membre n\'est plus disponible ou a été supprimé.');
                }
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, \Illuminate\Http\Request $request) {
            if (!$request->expectsJson()) {
                if ($request->is('activities/*')) {
                    return redirect()->route('activities.index')->with('warning', $e->getMessage() ?: 'Vous n\'avez pas accès à cette activité.');
                }
            }
        });
    })->create();
