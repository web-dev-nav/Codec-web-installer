@extends('installer::layout')

@section('title', 'License Verification - Installation Wizard')

@section('content')
@php
    $step = 2;
@endphp

<h2 class="text-2xl font-bold mb-6">License Verification</h2>

<form action="{{ route('installer.license.verify') }}" method="POST" class="space-y-6">
    @csrf
    
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <h3 class="text-sm font-semibold text-blue-800">License Required</h3>
                <p class="text-sm text-blue-700 mt-1">
                    Please enter your valid license key and email address to continue with the installation.
                </p>
            </div>
        </div>
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
            Email Address <span class="text-red-500">*</span>
        </label>
        <input 
            type="email" 
            id="email" 
            name="email" 
            value="{{ old('email') }}"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
            placeholder="Enter your email address"
        >
        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="license_key" class="block text-sm font-medium text-gray-700 mb-2">
            License Key <span class="text-red-500">*</span>
        </label>
        <input 
            type="text" 
            id="license_key" 
            name="license_key" 
            value="{{ old('license_key') }}"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('license_key') border-red-500 @enderror"
            placeholder="Enter your license key"
        >
        @error('license_key')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-sm text-gray-500">
            Your license key should be in the format: XXXX-XXXX-XXXX-XXXX
        </p>
    </div>

    <div class="bg-gray-50 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-gray-800 mb-2">Need a License?</h3>
        <p class="text-sm text-gray-600">
            If you don't have a license key yet, you can purchase one from our website. 
            Contact support if you have any issues with your license.
        </p>
    </div>

    <div class="flex justify-between pt-4">
        <a href="{{ route('installer.requirements') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
            Back
        </a>
        
        <button 
            type="submit" 
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
            Verify License
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitButton = form.querySelector('button[type="submit"]');
    
    form.addEventListener('submit', function() {
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Verifying...
        `;
    });
});
</script>
@endsection