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
        
        $this->app['router']->aliasMiddleware('installer.check', Middleware\InstallerMiddleware::class);
    }

    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views/installer', 'installer');
        
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
            // Admin customization routes (self-destructing)
            if (class_exists('\Codelone\CodecWebInstaller\Controllers\CustomizerController')) {
                Route::prefix(config('installer.route_prefix', 'installer') . '/customize')
                    ->middleware(['web'])
                    ->group(function() {
                        $customizer = \Codelone\CodecWebInstaller\Controllers\CustomizerController::class;
                        
                        Route::get('/', [$customizer, 'dashboard'])->name('installer.customize.dashboard');
                        Route::get('/requirements', [$customizer, 'requirements'])->name('installer.customize.requirements');
                        Route::post('/requirements', [$customizer, 'updateRequirements'])->name('installer.customize.requirements.update');
                        Route::get('/branding', [$customizer, 'branding'])->name('installer.customize.branding');
                        Route::post('/branding', [$customizer, 'updateBranding'])->name('installer.customize.branding.update');
                        Route::get('/export', [$customizer, 'export'])->name('installer.customize.export');
                        Route::post('/download', [$customizer, 'download'])->name('installer.customize.download');
                    });
            }
            
            // Standard installer routes
            Route::prefix(config('installer.route_prefix', 'installer'))
                ->middleware(['web', 'installer.check'])
                ->group(function() {
                    $controller = \Codelone\CodecWebInstaller\Controllers\InstallerController::class;
                    
                    Route::get('/', [$controller, 'welcome'])->name('installer.welcome');
                    Route::get('/requirements', [$controller, 'requirements'])->name('installer.requirements');
                    Route::post('/requirements', [$controller, 'checkRequirements'])->name('installer.requirements.check');
                    Route::get('/license', [$controller, 'license'])->name('installer.license');
                    Route::post('/license', [$controller, 'verifyLicense'])->name('installer.license.verify');
                    Route::get('/database', [$controller, 'database'])->name('installer.database');
                    Route::post('/database', [$controller, 'setupDatabase'])->name('installer.database.setup');
                    Route::get('/complete', [$controller, 'complete'])->name('installer.complete');
                });
        }
    }
}