<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Utopia\Repositories\Eloquent\ProductRepo;
use App\Utopia\Repositories\Interfaces\ProductRepoInterface;

class RepoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot()
    {
    }

    /**
     * Register services.
     */
    public function register()
    {
        $this->app->bind(ProductRepoInterface::class, ProductRepo::class);
    }
}
