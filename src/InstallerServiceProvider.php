<?php

namespace Codelone\CodecWebInstaller;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class InstallerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/installer.php', 'installer');
        
        $this->app->singleton(Services\SystemChecker::class);
        $this->app->singleton(Services\LicenseValidator::class);
        $this->app->singleton(Services\DatabaseInstaller::class);
        
        $this->app->singleton('installer.check', function ($app) {
            return new Middleware\InstallerMiddleware();
        });
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'installer');
        
        $this->publishes([
            __DIR__.'/../config/installer.php' => config_path('installer.php'),
        ], 'installer-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/installer'),
        ], 'installer-views');

        $this->registerRoutes();
    }

    protected function registerRoutes()
    {
        if (! $this->app->routesAreCached()) {
            Route::group([
                'prefix' => config('installer.route_prefix', 'installer'),
                'middleware' => ['web'],
                'namespace' => 'Codelone\CodecWebInstaller\Controllers',
            ], function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/installer.php');
            });
        }
    }
}