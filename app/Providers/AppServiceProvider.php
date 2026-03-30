<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

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
    }
}
