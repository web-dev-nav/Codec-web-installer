@extends('installer::layout')

@section('title', 'Installation Complete - Installation Wizard')

@section('content')
@php
    $step = 4;
@endphp

<div class="text-center">
    <div class="mb-8">
        <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Installation Complete!</h2>
        <p class="text-lg text-gray-600 mb-8">
            Your application has been successfully installed and configured.
        </p>
    </div>

    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
        <h3 class="text-xl font-semibold text-green-800 mb-4">What's been installed:</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-green-700">System requirements verified</span>
            </div>
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-green-700">License validated and activated</span>
            </div>
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-green-700">Database configured and populated</span>
            </div>
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-green-700">Environment configuration updated</span>
            </div>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
        <h3 class="text-lg font-semibold text-blue-800 mb-3">Important Security Notes:</h3>
        <div class="text-left space-y-2 text-blue-700">
            <p>• The installer has been disabled for security reasons</p>
            <p>• Make sure to set proper file permissions on your server</p>
            <p>• Consider enabling additional security measures in production</p>
            <p>• Keep your application and dependencies up to date</p>
        </div>
    </div>

    <div class="space-y-4">
        <a 
            href="{{ url('/') }}" 
            class="inline-block bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-200"
        >
            Visit Your Application
        </a>
        
        <div class="text-sm text-gray-500">
            <p>If you encounter any issues, please check the documentation or contact support.</p>
        </div>
    </div>
</div>
@endsection