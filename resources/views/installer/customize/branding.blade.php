@extends('installer::customize.layout')

@section('title', 'Branding')

@section('content')
<form method="POST" action="{{ route('installer.customize.branding.update') }}">
    @csrf
    
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-2xl font-bold mb-6">Branding & Appearance</h2>
        
        <!-- App Name -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Application Name</label>
            <input type="text" name="app_name" value="{{ old('app_name', 'Your Application') }}" 
                   class="w-full border border-gray-300 rounded-md px-3 py-2" 
                   placeholder="Your Application Name">
        </div>

        <!-- Primary Color -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Primary Color</label>
            <div class="flex space-x-4">
                <input type="color" name="primary_color" value="{{ old('primary_color', $currentConfig['primary_color'] ?? '#3490dc') }}" 
                       class="w-16 h-10 border border-gray-300 rounded">
                <input type="text" name="primary_color_hex" value="{{ old('primary_color', $currentConfig['primary_color'] ?? '#3490dc') }}" 
                       class="flex-1 border border-gray-300 rounded-md px-3 py-2" 
                       placeholder="#3490dc">
            </div>
        </div>

        <!-- Welcome Page Customization -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Welcome Page Title</label>
            <input type="text" name="welcome_title" value="{{ old('welcome_title', 'Welcome to the Installation Wizard') }}" 
                   class="w-full border border-gray-300 rounded-md px-3 py-2" 
                   placeholder="Welcome to the Installation Wizard">
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Welcome Page Description</label>
            <textarea name="welcome_description" rows="3" 
                      class="w-full border border-gray-300 rounded-md px-3 py-2"
                      placeholder="This wizard will guide you through the installation process in a few simple steps.">{{ old('welcome_description', 'This wizard will guide you through the installation process in a few simple steps.') }}</textarea>
        </div>

        <!-- Preview -->
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold mb-4">Preview</h3>
            <div class="bg-gray-50 p-6 rounded-lg border-2" style="border-color: {{ old('primary_color', $currentConfig['primary_color'] ?? '#3490dc') }}">
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" 
                         style="background-color: {{ old('primary_color', $currentConfig['primary_color'] ?? '#3490dc') }}20">
                        <svg class="w-8 h-8" style="color: {{ old('primary_color', $currentConfig['primary_color'] ?? '#3490dc') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2" id="previewTitle">{{ old('welcome_title', 'Welcome to the Installation Wizard') }}</h2>
                    <p class="text-gray-600" id="previewDescription">{{ old('welcome_description', 'This wizard will guide you through the installation process in a few simple steps.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-between">
        <a href="{{ route('installer.customize.requirements') }}" class="bg-gray-600 text-white px-6 py-3 rounded-lg">Back to Requirements</a>
        <button type="submit" class="bg-purple-600 text-white px-6 py-3 rounded-lg">Save & Continue to Export</button>
    </div>
</form>

<script>
// Live preview updates
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.querySelector('input[name="welcome_title"]');
    const descInput = document.querySelector('textarea[name="welcome_description"]');
    const colorInput = document.querySelector('input[name="primary_color"]');
    const previewTitle = document.getElementById('previewTitle');
    const previewDescription = document.getElementById('previewDescription');
    
    titleInput.addEventListener('input', function() {
        previewTitle.textContent = this.value || 'Welcome to the Installation Wizard';
    });
    
    descInput.addEventListener('input', function() {
        previewDescription.textContent = this.value || 'This wizard will guide you through the installation process in a few simple steps.';
    });
    
    colorInput.addEventListener('input', function() {
        const color = this.value;
        document.querySelector('input[name="primary_color_hex"]').value = color;
        document.querySelector('.bg-gray-50.p-6').style.borderColor = color;
        document.querySelector('.w-16.h-16').style.backgroundColor = color + '20';
        document.querySelector('.w-8.h-8').style.color = color;
    });
});
</script>
@endsection