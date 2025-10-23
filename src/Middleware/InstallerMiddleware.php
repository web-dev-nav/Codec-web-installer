<?php

namespace Codelone\CodecWebInstaller\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class InstallerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Always allow access to the complete page
        // This allows users to see completion screen even after lock file is created
        if ($request->routeIs('installer.complete')) {
            return $next($request);
        }

        // Check if installation is already completed via lock file
        if (file_exists(config('installer.lock_file'))) {
            // If user has active installation session, allow them to complete it
            if (Session::has('installer.completed')) {
                return redirect()->route('installer.complete');
            }

            return redirect('/')->with('message', 'Installation already completed');
        }

        return $next($request);
    }
}