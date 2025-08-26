<?php

namespace YourVendor\LaravelInstaller\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use YourVendor\LaravelInstaller\Services\SystemChecker;
use YourVendor\LaravelInstaller\Services\LicenseValidator;
use YourVendor\LaravelInstaller\Services\DatabaseInstaller;

class InstallerController extends Controller
{
    protected SystemChecker $systemChecker;
    protected LicenseValidator $licenseValidator;
    protected DatabaseInstaller $databaseInstaller;

    public function __construct(
        SystemChecker $systemChecker,
        LicenseValidator $licenseValidator,
        DatabaseInstaller $databaseInstaller
    ) {
        $this->systemChecker = $systemChecker;
        $this->licenseValidator = $licenseValidator;
        $this->databaseInstaller = $databaseInstaller;
    }

    public function welcome()
    {
        Session::put('installer.current_step', 0);
        return view('installer::welcome');
    }

    public function requirements()
    {
        $this->ensureStep(1);
        $results = $this->systemChecker->check();
        return view('installer::requirements', compact('results'));
    }

    public function checkRequirements()
    {
        $results = $this->systemChecker->check();
        
        if ($results['overall']) {
            Session::put('installer.current_step', 2);
            return redirect()->route('installer.license');
        }

        return back()->withErrors(['requirements' => 'Please fix all requirements before proceeding.']);
    }

    public function license()
    {
        $this->ensureStep(2);
        return view('installer::license');
    }

    public function verifyLicense(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string',
            'email' => 'required|email',
        ]);

        $result = $this->licenseValidator->verify(
            $request->license_key,
            $request->email
        );

        if ($result['valid']) {
            Session::put('installer.license', $request->license_key);
            Session::put('installer.email', $request->email);
            Session::put('installer.current_step', 3);
            return redirect()->route('installer.database');
        }

        return back()->withErrors(['license' => $result['message']]);
    }

    public function database()
    {
        $this->ensureStep(3);
        return view('installer::database');
    }

    public function setupDatabase(Request $request)
    {
        $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|integer',
            'db_name' => 'required|string',
            'db_username' => 'required|string',
            'db_password' => 'nullable|string',
        ]);

        $credentials = $request->only(['db_host', 'db_port', 'db_name', 'db_username', 'db_password']);
        
        $result = $this->databaseInstaller->install(
            $credentials,
            Session::get('installer.license'),
            Session::get('installer.email')
        );

        if ($result['success']) {
            $this->createLockFile();
            Session::forget('installer');
            return redirect()->route('installer.complete');
        }

        return back()->withErrors(['database' => $result['message']]);
    }

    public function complete()
    {
        return view('installer::complete');
    }

    protected function ensureStep(int $requiredStep)
    {
        $currentStep = Session::get('installer.current_step', 0);
        
        if ($currentStep < $requiredStep) {
            return redirect()->route('installer.welcome');
        }
    }

    protected function createLockFile()
    {
        file_put_contents(config('installer.lock_file'), date('Y-m-d H:i:s'));
    }
}