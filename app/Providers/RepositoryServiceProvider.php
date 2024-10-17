<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Solid\Repositories\RapidxRepository;
use App\Solid\Repositories\UserAccessRepository;

use App\Solid\Interfaces\RapidxRepositoryInterface;
use App\Solid\Interfaces\UserAccessRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        
        $this->app->bind(RapidxRepositoryInterface::class, RapidxRepository::class);
        $this->app->bind(UserAccessRepositoryInterface::class, UserAccessRepository::class);
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
