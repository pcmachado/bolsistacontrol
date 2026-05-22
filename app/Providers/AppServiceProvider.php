<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\SystemRelease;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FakerGenerator::class, function () {
            $faker = FakerFactory::create('pt_BR'); // Cria uma instância do Faker para o Brasil
            $faker->addProvider(new \Faker\Provider\pt_BR\Person($faker));
            $faker->addProvider(new \Faker\Provider\pt_BR\Address($faker));
            $faker->addProvider(new \Faker\Provider\pt_BR\Company($faker));
            $faker->addProvider(new \Faker\Provider\pt_BR\PhoneNumber($faker));
            return $faker;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.partials._navbar', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();

                $view->with([
                    'navUnreadCount' => $user->unreadNotifications()->count(),
                    'navNotifications' => $user->notifications()->latest()->limit(5)->get(),
                ]);
            }
        });

        View::composer(['layouts.app', 'layouts.guest'], function ($view) {
            // 1. Lê a versão do arquivo txt (gerado pelo deploy). Se não existir, usa v1.0.0
            $versionFile = base_path('version.txt');
            $currentVersion = File::exists($versionFile) ? trim(File::get($versionFile)) : 'v1.0.0';

            // Cria as duas variações possíveis para buscar no banco (ex: "v1.0.0" e "1.0.0")
            $normalizedVersion = SystemRelease::normalizeVersion($currentVersion);
            $versionWithoutV = ltrim($normalizedVersion, 'v');

            // 2. Busca no banco aceitando qualquer uma das duas formas de forma segura
            $release = SystemRelease::query()
                ->whereIn('version', [$normalizedVersion, $versionWithoutV])
                ->latest('created_at')
                ->first();

            // 3. Define se o modal abre automático
            $showModal = false;
            $userSeenVersion = Auth::check() && Auth::user()->last_seen_version
                ? SystemRelease::normalizeVersion(Auth::user()->last_seen_version)
                : null;

            if (Auth::check() && $release && $userSeenVersion !== $normalizedVersion) {
                $showModal = true;
            }

            $view->with(compact('currentVersion', 'release', 'showModal'));
        });
    }
}
