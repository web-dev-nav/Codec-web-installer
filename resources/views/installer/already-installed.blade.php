@extends('installer::layout')

@section('title', 'Already Installed - Installation Wizard')

@section('content')
<div class="text-center">
    <div class="mb-8">
        <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Installation Already Complete</h2>
        <p class="text-lg text-gray-600 mb-8">
            This application has already been installed and configured.
        </p>
    </div>

    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
        <h3 class="text-lg font-semibold text-yellow-800 mb-3">Why am I seeing this page?</h3>
        <div class="text-left text-yellow-700 space-y-2">
            <p>• The installation process has already been completed</p>
            <p>• A lock file prevents re-installation for security reasons</p>
            <p>• This protects your existing data and configuration</p>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
        <h3 class="text-lg font-semibold text-blue-800 mb-3">Need to reinstall?</h3>
        <div class="text-left text-blue-700 space-y-2">
            <p>If you need to reinstall the application:</p>
            <ul class="list-disc list-inside ml-4 space-y-1">
                <li>Delete the <code class="bg-blue-100 px-1 rounded">storage/installer.lock</code> file</li>
                <li>Make sure you have a backup of your data</li>
                <li>Clear your browser cache and cookies</li>
                <li>Return to this page to start fresh installation</li>
            </ul>
        </div>
    </div>

    <div class="space-y-4">
        <a 
            href="{{ url('/') }}" 
            class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-200"
        >
            Go to Application
        </a>
        
        <div class="text-sm text-gray-500">
            <p>If you're experiencing issues, please contact support for assistance.</p>
        </div>
    </div>
</div>
@endsection