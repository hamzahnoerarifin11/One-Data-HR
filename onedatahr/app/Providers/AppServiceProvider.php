<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Pagination\Paginator;
use App\Models\Kandidat;
use App\Models\Posisi;
use App\Models\InterviewHr;
use App\Observers\KandidatObserver;
use App\Observers\PosisiObserver;
use App\Policies\TempaPesertaPolicy;
use Illuminate\Support\Facades\Gate;

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
        \Carbon\Carbon::setLocale('id');
        date_default_timezone_set('Asia/Jakarta');
        Kandidat::observe(KandidatObserver::class);
        Posisi::observe(PosisiObserver::class);
        Paginator::useTailwind();

        // Register Gates for TEMPA
        Gate::define('viewTempaPeserta', function($user) {
            return $user->hasRole(['admin', 'superadmin', 'ketua_tempa']);
        });
        Gate::define('createTempaPeserta', function($user) {
            return $user->hasRole(['admin', 'superadmin', 'ketua_tempa']);
        });
        Gate::define('editTempaPeserta', function($user) {
            return $user->hasRole(['admin', 'superadmin', 'ketua_tempa']);
        });
        Gate::define('deleteTempaPeserta', function($user) {
            return $user->hasRole(['admin', 'superadmin', 'ketua_tempa']);
        });

        Gate::policy(\App\Models\TempaPeserta::class, TempaPesertaPolicy::class);

        Gate::define('viewTempaAbsensi', [TempaPolicy::class, 'viewTempaAbsensi']);
        Gate::define('createTempaAbsensi', [TempaPolicy::class, 'createTempaAbsensi']);

        Gate::define('viewTempaMonitoring', [TempaPolicy::class, 'viewTempaMonitoring']);
        Gate::define('createTempaMateri', [TempaPolicy::class, 'createTempaMateri']);
    }
}
