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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
        ]);

        // Mengarahkan user yang belum login atau sesinya habis ke halaman signin
        $middleware->redirectGuestsTo(fn () => route('signin'));

        // Exclude CSRF for batch delete route
        // $middleware->validateCsrfTokens(except: [
        //     'karyawan/batch-delete',
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Menangani error CSRF/Session Expired (419)
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->isXmlHttpRequest()) {
                return response()->json(['message' => 'Sesi Anda telah berakhir (Timeout). Silakan muat ulang halaman atau login kembali.'], 419);
            }
            return redirect()->route('signin')->with('error', 'Sesi Anda telah berakhir karena tidak ada aktivitas. Silakan login kembali.');
        });

        // Menangani error Unauthenticated (401)
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->isXmlHttpRequest()) {
                return response()->json(['message' => 'Sesi Anda belum terautentikasi. Silakan login kembali.'], 401);
            }
            return redirect()->guest(route('signin'))->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
        });
    })->create();
