<?php

namespace Codelone\CodecWebInstaller\Middleware;

use Closure;
use Illuminate\Http\Request;

class InstallerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check if installation is already completed
        if (file_exists(config('installer.lock_file'))) {
            return redirect('/')->with('message', 'Installation already completed');
        }

        return $next($request);
    }
}