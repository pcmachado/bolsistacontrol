<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    // public const HOME = '/dashboard';

    public static function home()
    {
        $user = Auth::user();

        if (! $user) {
            return route('login', absolute: false);
        }

        if ($user->hasRole('superadmin')) {
            return route('superadmin.dashboard', absolute: false);
        }

        if ($user->hasAnyRole([
            'admin',
            'coordenador_geral',
            'coordenador_adjunto_geral',
            'coordenador_adjunto',
        ])) {
            return route('admin.dashboard', absolute: false);
        }

        if ($user->hasRole('professor')) {
            return route('teacher.dashboard', absolute: false);
        }

        if ($user->scholarshipHolder !== null) {
            return route('holder.dashboard', absolute: false);
        }

        return '/'; // fallback
    }

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->mapWebhookRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    protected function mapWebhookRoutes() {}
}
