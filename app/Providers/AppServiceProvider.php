<?php

namespace App\Providers;

use App\Domain\Entities\Business;
use App\Domain\Repositories\ClientRepositoryInterface;
use App\Domain\Repositories\BusinessRepositoryInterface;
use App\Infrastructure\Repositories\EloquentClientRepository;
use App\Infrastructure\Repositories\EloquentBusinessRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ClientRepositoryInterface::class, EloquentClientRepository::class);
        $this->app->bind(BusinessRepositoryInterface::class, EloquentBusinessRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}