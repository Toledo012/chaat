<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\EnsurePermission;
use App\Http\Middleware\SetDbUserId;

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
        // Alias de middleware de permisos
        $this->app['router']->aliasMiddleware('perm', EnsurePermission::class);

        // Inyectar en el grupo 'web' el contexto de usuario para triggers
        $this->app['router']->pushMiddlewareToGroup('web', SetDbUserId::class);

        // En producción el sistema corre detrás del proxy en https://dominio/servicios:
        // todas las URLs (route, url, redirect, asset) salen de APP_URL, con https.
        if ($this->app->environment('production')) {
            URL::forceRootUrl(rtrim(config('app.url'), '/'));
            URL::forceScheme('https');
        }
    }
}
