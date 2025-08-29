@extends('installer::customize.layout')

@section('title', 'Dashboard')

@section('content')
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h2 class="text-2xl font-bold mb-4">Installer Customization Dashboard</h2>
    
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
        <strong>⚠️ Warning:</strong> This interface will permanently delete itself after customization. Make sure you're ready before proceeding to export.
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-blue-50 p-4 rounded-lg">
            <h3 class="font-semibold text-blue-900">Current PHP Version</h3>
            <p class="text-2xl font-bold text-blue-600">{{ $currentConfig['requirements']['php'] }}</p>
        </div>
        
        <div class="bg-green-50 p-4 rounded-lg">
            <h3 class="font-semibold text-green-900">Extensions Required</h3>
            <p class="text-2xl font-bold text-green-600">{{ count($currentConfig['requirements']['extensions']) }}</p>
        </div>
        
        <div class="bg-purple-50 p-4 rounded-lg">
            <h3 class="font-semibold text-purple-900">Folders to Check</h3>
            <p class="text-2xl font-bold text-purple-600">{{ count($currentConfig['requirements']['folders']) }}</p>
        </div>
        
        <div class="bg-orange-50 p-4 rounded-lg">
            <h3 class="font-semibold text-orange-900">Status</h3>
            <p class="text-sm font-bold text-orange-600">Ready to Customize</p>
        </div>
    </div>

    <div class="space-y-4">
        <h3 class="text-lg font-semibold">Customization Steps:</h3>
        
        <div class="space-y-2">
            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded">
                <span class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">1</span>
                <div>
                    <h4 class="font-semibold">Configure Requirements</h4>
                    <p class="text-sm text-gray-600">Set PHP version, extensions, and folder permissions</p>
                </div>
                <a href="{{ route('installer.customize.requirements') }}" class="ml-auto bg-blue-600 text-white px-4 py-2 rounded text-sm">Configure</a>
            </div>
            
            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded">
                <span class="w-8 h-8 bg-purple-600 text-white rounded-full flex items-center justify-center text-sm font-bold">2</span>
                <div>
                    <h4 class="font-semibold">Customize Branding</h4>
                    <p class="text-sm text-gray-600">Set app name, colors, and welcome messages</p>
                </div>
                <a href="{{ route('installer.customize.branding') }}" class="ml-auto bg-purple-600 text-white px-4 py-2 rounded text-sm">Customize</a>
            </div>
            
            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded">
                <span class="w-8 h-8 bg-red-600 text-white rounded-full flex items-center justify-center text-sm font-bold">3</span>
                <div>
                    <h4 class="font-semibold">Export & Self-Destruct</h4>
                    <p class="text-sm text-gray-600">Download customized files and permanently disable this interface</p>
                </div>
                <a href="{{ route('installer.customize.export') }}" class="ml-auto bg-red-600 text-white px-4 py-2 rounded text-sm">Export</a>
            </div>
        </div>
    </div>
</div>
@endsection