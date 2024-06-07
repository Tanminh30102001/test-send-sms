<?php

namespace App\Providers;

use App\Models\Merchant;
use App\Models\Project;
use App\Repositories\MerchantRepository;
use App\Repositories\ProjectRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProjectRepository::class, function ($app) {
            return new ProjectRepository(new Project());
        });
        $this->app->bind(MerchantRepository::class, function ($app) {
            return new MerchantRepository(new Merchant());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
