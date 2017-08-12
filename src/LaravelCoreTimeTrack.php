<?php

namespace DeveoDK\CoreTimeTracker;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class CoreTimeTrackerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @param Router $router
     * @return void
     */
    public function boot(Router $router)
    {
        $this->loadRoutesFrom(__DIR__  . '/routes.php');

        if ($this->app->runningInConsole()) {
            $this->registerMigrations();
        }
    }

    /**
     * Register migration files.
     * @return void
     */
    protected function registerMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        return;
    }
}
