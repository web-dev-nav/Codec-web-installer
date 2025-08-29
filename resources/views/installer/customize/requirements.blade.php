@extends('installer::customize.layout')

@section('title', 'Requirements')

@section('content')
<form method="POST" action="{{ route('installer.customize.requirements.update') }}">
    @csrf
    
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-2xl font-bold mb-6">System Requirements Configuration</h2>
        
        <!-- PHP Version -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Minimum PHP Version</label>
            <select name="php_version" class="w-full border border-gray-300 rounded-md px-3 py-2">
                <option value="8.1.0" {{ ($currentConfig['php'] ?? '') == '8.1.0' ? 'selected' : '' }}>PHP 8.1.0</option>
                <option value="8.2.0" {{ ($currentConfig['php'] ?? '') == '8.2.0' ? 'selected' : '' }}>PHP 8.2.0</option>
                <option value="8.3.0" {{ ($currentConfig['php'] ?? '') == '8.3.0' ? 'selected' : '' }}>PHP 8.3.0</option>
                <option value="8.4.0" {{ ($currentConfig['php'] ?? '') == '8.4.0' ? 'selected' : '' }}>PHP 8.4.0</option>
            </select>
        </div>

        <!-- Extensions -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Required PHP Extensions</label>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($availableExtensions as $extension)
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="extensions[]" value="{{ $extension }}" 
                               {{ in_array($extension, $currentConfig['extensions'] ?? []) ? 'checked' : '' }}
                               class="rounded border-gray-300">
                        <span class="text-sm">{{ $extension }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- Custom Extension -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Add Custom Extension</label>
            <input type="text" id="customExtension" placeholder="Enter extension name" 
                   class="border border-gray-300 rounded-md px-3 py-2">
            <button type="button" onclick="addCustomExtension()" 
                    class="ml-2 bg-blue-600 text-white px-4 py-2 rounded text-sm">Add</button>
        </div>

        <!-- Folders -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Folder Permissions</label>
            <div id="foldersContainer">
                @foreach($currentConfig['folders'] ?? [] as $path => $permission)
                    <div class="flex space-x-2 mb-2">
                        <input type="text" name="folders[{{ $loop->index }}][path]" value="{{ $path }}" 
                               placeholder="Folder path" class="flex-1 border border-gray-300 rounded-md px-3 py-2">
                        <select name="folders[{{ $loop->index }}][permission]" class="border border-gray-300 rounded-md px-3 py-2">
                            <option value="755" {{ $permission == '755' ? 'selected' : '' }}>755</option>
                            <option value="775" {{ $permission == '775' ? 'selected' : '' }}>775</option>
                            <option value="777" {{ $permission == '777' ? 'selected' : '' }}>777</option>
                        </select>
                        <button type="button" onclick="removeFolder(this)" class="bg-red-600 text-white px-3 py-2 rounded text-sm">Remove</button>
                    </div>
                @endforeach
            </div>
            <button type="button" onclick="addFolder()" class="bg-green-600 text-white px-4 py-2 rounded text-sm">Add Folder</button>
        </div>
    </div>

    <div class="flex justify-between">
        <a href="{{ route('installer.customize.dashboard') }}" class="bg-gray-600 text-white px-6 py-3 rounded-lg">Back to Dashboard</a>
        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg">Save & Continue to Branding</button>
    </div>
</form>

<script>
function addCustomExtension() {
    const input = document.getElementById('customExtension');
    const extension = input.value.trim();
    if (extension) {
        const container = document.querySelector('.grid.grid-cols-2');
        const label = document.createElement('label');
        label.className = 'flex items-center space-x-2';
        label.innerHTML = `
            <input type="checkbox" name="extensions[]" value="${extension}" checked class="rounded border-gray-300">
            <span class="text-sm">${extension}</span>
        `;
        container.appendChild(label);
        input.value = '';
    }
}

function addFolder() {
    const container = document.getElementById('foldersContainer');
    const index = container.children.length;
    const div = document.createElement('div');
    div.className = 'flex space-x-2 mb-2';
    div.innerHTML = `
        <input type="text" name="folders[${index}][path]" placeholder="Folder path" class="flex-1 border border-gray-300 rounded-md px-3 py-2">
        <select name="folders[${index}][permission]" class="border border-gray-300 rounded-md px-3 py-2">
            <option value="755">755</option>
            <option value="775" selected>775</option>
            <option value="777">777</option>
        </select>
        <button type="button" onclick="removeFolder(this)" class="bg-red-600 text-white px-3 py-2 rounded text-sm">Remove</button>
    `;
    container.appendChild(div);
}

function removeFolder(button) {
    button.closest('.flex').remove();
}
</script>
@endsection