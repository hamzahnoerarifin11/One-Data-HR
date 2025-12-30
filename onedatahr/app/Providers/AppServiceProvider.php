<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register middleware alias for role checks
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('role', \App\Http\Middleware\EnsureRole::class);
        Paginator::useTailwind();
    }
}
