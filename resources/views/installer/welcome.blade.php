@extends('installer::layout')

@section('title', 'Welcome - Installation Wizard')

@section('content')
<div class="text-center">
    <div class="mb-8">
        <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Welcome to the Installation Wizard</h2>
        <p class="text-lg text-gray-600 mb-8">
            This wizard will guide you through the installation process in a few simple steps.
        </p>
    </div>

    <div class="bg-gray-50 rounded-lg p-6 mb-8">
        <h3 class="text-xl font-semibold mb-4">What we'll do:</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
            <div class="flex items-start space-x-3">
                <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                    <span class="text-white text-sm font-bold">1</span>
                </div>
                <div>
                    <h4 class="font-semibold">System Check</h4>
                    <p class="text-sm text-gray-600">Verify PHP version, extensions, and permissions</p>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                    <span class="text-white text-sm font-bold">2</span>
                </div>
                <div>
                    <h4 class="font-semibold">License Verification</h4>
                    <p class="text-sm text-gray-600">Validate your license key and activation</p>
                </div>
            </div>
            <div class="flex items-start space-x-3">
                <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                    <span class="text-white text-sm font-bold">3</span>
                </div>
                <div>
                    <h4 class="font-semibold">Database Setup</h4>
                    <p class="text-sm text-gray-600">Configure and initialize your database</p>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('installer.requirements') }}" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-200">
        Start Installation
    </a>
</div>
@endsection