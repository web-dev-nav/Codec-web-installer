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

        // Add installer environment variables if not present
        $this->addInstallerEnvVariables();

        $this->registerRoutes();
    }

    protected function addInstallerEnvVariables()
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            return;
        }

        $envContent = file_get_contents($envPath);

        // Check if installer config already exists
        if (strpos($envContent, 'INSTALLER_PRODUCT_ID') !== false) {
            return;
        }

        // Add installer configuration to .env
        $installerConfig = <<<EOT


# Installer Configuration
INSTALLER_PRODUCT_ID=1
INSTALLER_LICENSE_API_URL=https://api.codelone.com/verify-license
INSTALLER_VERIFY_SSL=true

EOT;

        file_put_contents($envPath, $envContent . $installerConfig);
    }

    protected function registerRoutes()
    {
        if (! $this->app->routesAreCached()) {
            // Installer routes
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