<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Solid\Repositories\UomRepository;
use App\Solid\Repositories\EmailRepository;
use App\Solid\Repositories\RapidxRepository;
use App\Solid\Repositories\RequestRepository;
use App\Solid\Repositories\CategoryRepository;
use App\Solid\Repositories\CurrencyRepository;
use App\Solid\Repositories\UserAccessRepository;

use App\Solid\Interfaces\UomRepositoryInterface;
use App\Solid\Interfaces\EmailRepositoryInterface;
use App\Solid\Interfaces\RapidxRepositoryInterface;
use App\Solid\Interfaces\RequestRepositoryInterface;
use App\Solid\Interfaces\CategoryRepositoryInterface;
use App\Solid\Interfaces\CurrencyRepositoryInterface;
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
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(RequestRepositoryInterface::class, RequestRepository::class);
        $this->app->bind(UomRepositoryInterface::class, UomRepository::class);
        $this->app->bind(EmailRepositoryInterface::class, EmailRepository::class);
        $this->app->bind(CurrencyRepositoryInterface::class, CurrencyRepository::class);
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
