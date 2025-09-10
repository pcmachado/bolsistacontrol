<?php

namespace App\Providers;

use App\Repositories\Contracts\AnalyzeDivergenceRepositoryInterface;
use App\Repositories\Contracts\ConferenceItemMovRepositoryInterface;
use App\Repositories\Contracts\ConferenceRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Core\Eloquent\EloquentAnalyzeDivergenceRepository;
use App\Repositories\Core\Eloquent\EloquentConferenceItemMovRepository;
use App\Repositories\Core\Eloquent\EloquentConferenceRepository;
use App\Repositories\Core\Eloquent\EloquentOrderRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind(
            ConferenceRepositoryInterface::class,
            EloquentConferenceRepository::class
        );
        $this->app->bind(
            OrderRepositoryInterface::class,                        
            EloquentOrderRepository::class,
            
        );

        $this->app->bind(                         
            ConferenceItemMovRepositoryInterface::class,            
            EloquentConferenceItemMovRepository::class,                  
        );

        $this->app->bind(                         
            AnalyzeDivergenceRepositoryInterface::class,            
            EloquentAnalyzeDivergenceRepository::class,                  
        );


    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
