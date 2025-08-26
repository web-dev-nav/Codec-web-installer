<?php

use YourVendor\LaravelInstaller\Controllers\InstallerController;

Route::middleware(['installer.check'])->group(function () {
    Route::get('/', [InstallerController::class, 'welcome'])->name('installer.welcome');
    Route::get('/requirements', [InstallerController::class, 'requirements'])->name('installer.requirements');
    Route::post('/requirements', [InstallerController::class, 'checkRequirements'])->name('installer.requirements.check');
    Route::get('/license', [InstallerController::class, 'license'])->name('installer.license');
    Route::post('/license', [InstallerController::class, 'verifyLicense'])->name('installer.license.verify');
    Route::get('/database', [InstallerController::class, 'database'])->name('installer.database');
    Route::post('/database', [InstallerController::class, 'setupDatabase'])->name('installer.database.setup');
    Route::get('/complete', [InstallerController::class, 'complete'])->name('installer.complete');
});