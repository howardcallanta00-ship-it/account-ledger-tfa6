<?php

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../routes/web.php', commands: __DIR__.'/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware): void {
        // Render terminates TLS. The container is reachable only through its proxy.
        $middleware->trustProxies(at: '*', headers: Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Never log QueryException messages: they can contain bindings or connection details.
        $exceptions->report(function (QueryException $e) {
            Log::error('Directory database operation failed.', ['sql_state' => $e->errorInfo[0] ?? 'unknown']);

            return false;
        });
        $exceptions->render(function (QueryException $e, Request $request) {
            return response()->view('errors.database', [], 503)->header('Retry-After', '30');
        });
    })->create();
