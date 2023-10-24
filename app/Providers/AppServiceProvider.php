<?php

namespace App\Providers;

use App\Utilities\Concerns\RedisHelper;
use Illuminate\Support\ServiceProvider;
use App\Utilities\Concerns\ElasticsearchHelper;
use App\Utilities\Contracts\RedisHelperInterface;
use App\Utilities\Contracts\ElasticsearchHelperInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(RedisHelperInterface::class, RedisHelper::class);
        $this->app->bind(ElasticsearchHelperInterface::class, ElasticsearchHelper::class);
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
