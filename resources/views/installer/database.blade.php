@extends('installer::layout')

@section('title', 'Database Setup - Installation Wizard')

@section('content')
@php
    $step = 3;
@endphp

<div class="mb-6">
    <h2 class="text-2xl font-bold mb-2">Database Configuration</h2>
    @if(isset($productName) && $productName)
        <p class="text-gray-600">Installing: <strong>{{ $productName }}</strong> 
        @if(isset($productVersion) && $productVersion)
            (Version {{ $productVersion }})
        @endif
        </p>
    @endif
</div>

<form action="{{ route('installer.database.setup') }}" method="POST" class="space-y-6">
    @csrf
    
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-yellow-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <h3 class="text-sm font-semibold text-yellow-800">Important</h3>
                <p class="text-sm text-yellow-700 mt-1">
                    Make sure your database exists and the user has proper permissions before proceeding.
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="db_host" class="block text-sm font-medium text-gray-700 mb-2">
                Database Host <span class="text-red-500">*</span>
            </label>
            <input 
                type="text" 
                id="db_host" 
                name="db_host" 
                value="{{ old('db_host', 'localhost') }}"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('db_host') border-red-500 @enderror"
                placeholder="localhost"
            >
            @error('db_host')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="db_port" class="block text-sm font-medium text-gray-700 mb-2">
                Database Port <span class="text-red-500">*</span>
            </label>
            <input 
                type="number" 
                id="db_port" 
                name="db_port" 
                value="{{ old('db_port', '3306') }}"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('db_port') border-red-500 @enderror"
                placeholder="3306"
            >
            @error('db_port')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="db_name" class="block text-sm font-medium text-gray-700 mb-2">
            Database Name <span class="text-red-500">*</span>
        </label>
        <input 
            type="text" 
            id="db_name" 
            name="db_name" 
            value="{{ old('db_name') }}"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('db_name') border-red-500 @enderror"
            placeholder="Enter database name"
        >
        @error('db_name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="db_username" class="block text-sm font-medium text-gray-700 mb-2">
            Database Username <span class="text-red-500">*</span>
        </label>
        <input 
            type="text" 
            id="db_username" 
            name="db_username" 
            value="{{ old('db_username') }}"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('db_username') border-red-500 @enderror"
            placeholder="Enter database username"
        >
        @error('db_username')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="db_password" class="block text-sm font-medium text-gray-700 mb-2">
            Database Password
        </label>
        <input 
            type="password" 
            id="db_password" 
            name="db_password" 
            value="{{ old('db_password') }}"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('db_password') border-red-500 @enderror"
            placeholder="Enter database password"
        >
        @error('db_password')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-sm text-gray-500">
            Leave blank if no password is required
        </p>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-blue-800 mb-2">What happens next?</h3>
        <ul class="text-sm text-blue-700 space-y-1">
            <li>• We'll test the database connection</li>
            <li>• Download and install the application data</li>
            <li>• Update your environment configuration</li>
            <li>• Complete the installation process</li>
        </ul>
    </div>

    <div class="flex justify-between pt-4">
        <a href="{{ route('installer.license') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
            Back
        </a>
        
        <button 
            type="submit" 
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
            Install Database
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
            Installing...
        `;
    });
});
</script>
@endsection