@extends('installer::customize.layout')

@section('title', 'Export & Finalize')

@section('content')
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h2 class="text-2xl font-bold mb-6">Export Customized Installer</h2>
    
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <strong>⚠️ FINAL WARNING:</strong> Clicking "Download & Self-Destruct" will:
        <ul class="list-disc list-inside mt-2">
            <li>Download your customized installer files</li>
            <li>Permanently delete this customization interface</li>
            <li>Lock the customizer forever (cannot be undone)</li>
        </ul>
        <strong>Make sure you're ready!</strong>
    </div>

    <!-- Configuration Summary -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold mb-4">Configuration Summary</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Requirements -->
            <div class="bg-gray-50 p-4 rounded">
                <h4 class="font-semibold mb-3">System Requirements</h4>
                <div class="space-y-2 text-sm">
                    <div><strong>PHP Version:</strong> {{ $requirements['php'] ?? '8.2.0' }}+</div>
                    <div><strong>Extensions:</strong> {{ count($requirements['extensions'] ?? []) }} required</div>
                    <div><strong>Folder Checks:</strong> {{ count($requirements['folders'] ?? []) }} folders</div>
                </div>
                
                @if(!empty($requirements['extensions']))
                    <div class="mt-3">
                        <strong class="text-xs">Extensions:</strong>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @foreach($requirements['extensions'] as $ext)
                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">{{ $ext }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Branding -->
            <div class="bg-gray-50 p-4 rounded">
                <h4 class="font-semibold mb-3">Branding</h4>
                <div class="space-y-2 text-sm">
                    <div><strong>App Name:</strong> {{ $branding['app_name'] ?? 'Default' }}</div>
                    <div><strong>Primary Color:</strong> 
                        <span class="inline-block w-4 h-4 rounded" style="background-color: {{ $branding['primary_color'] ?? '#3490dc' }}"></span>
                        {{ $branding['primary_color'] ?? '#3490dc' }}
                    </div>
                    @if(!empty($branding['welcome_title']))
                        <div><strong>Welcome Title:</strong> {{ $branding['welcome_title'] }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex space-x-4">
        <a href="{{ route('installer.customize.branding') }}" 
           class="bg-gray-600 text-white px-6 py-3 rounded-lg">
            ← Back to Edit
        </a>
        
        <form method="POST" action="{{ route('installer.customize.download') }}" class="inline">
            @csrf
            <button type="submit" 
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-semibold"
                    onclick="return confirm('This action cannot be undone! Are you sure you want to download and destroy the customization interface?')">
                🔥 Download & Self-Destruct
            </button>
        </form>
    </div>
</div>

<!-- Instructions -->
<div class="bg-blue-50 rounded-lg p-6">
    <h3 class="text-lg font-semibold text-blue-900 mb-3">After Download Instructions</h3>
    <ol class="list-decimal list-inside space-y-2 text-blue-800">
        <li>Extract the downloaded ZIP file</li>
        <li>Replace the files in your package with the customized versions</li>
        <li>Test the installer with your new configuration</li>
        <li>Commit and distribute to clients</li>
        <li>The customization interface will be permanently removed</li>
    </ol>
</div>
@endsection