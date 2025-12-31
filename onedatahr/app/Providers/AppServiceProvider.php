<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use App\Models\Kandidat;
use App\Models\Posisi;
use App\Models\InterviewHr;
use App\Observers\KandidatObserver;
use App\Observers\PosisiObserver;

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
        \Carbon\Carbon::setLocale('id');
        date_default_timezone_set('Asia/Jakarta');
        Kandidat::observe(KandidatObserver::class);
        Posisi::observe(PosisiObserver::class);
    }
}
