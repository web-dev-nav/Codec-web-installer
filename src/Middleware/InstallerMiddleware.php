<?php

namespace YourVendor\LaravelInstaller\Middleware;

use Closure;
use Illuminate\Http\Request;

class InstallerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if installation is already completed
        if (file_exists(config('installer.lock_file'))) {
            return response()->view('installer::already-installed', [], 403);
        }

        return $next($request);
    }
}