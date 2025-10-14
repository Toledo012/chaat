<?php

namespace App\Providers;

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
    }
}
