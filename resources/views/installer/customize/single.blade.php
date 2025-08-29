<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installer Customizer - One Time Setup</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; margin-bottom: 30px; border-radius: 4px; color: #856404; }
        .section { margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .section:last-child { border-bottom: none; }
        h1 { color: #333; margin-bottom: 10px; }
        h2 { color: #555; margin-bottom: 15px; font-size: 18px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 10px; box-sizing: border-box; }
        input[type="checkbox"] { width: auto; margin-right: 5px; }
        .checkbox-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; margin-bottom: 15px; }
        .checkbox-item { display: flex; align-items: center; }
        .folder-row { display: flex; gap: 10px; margin-bottom: 10px; }
        .folder-row input { flex: 1; }
        .folder-row select { width: 100px; }
        .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-primary { background: #007bff; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-small { padding: 5px 10px; font-size: 12px; }
        .error { color: red; font-size: 14px; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Installer Customizer</h1>
        
        <div class="warning">
            <strong>⚠️ WARNING:</strong> This is a one-time setup that will permanently modify your installer package and delete this interface. Make sure all settings are correct before submitting.
        </div>

        @if($errors->any())
            <div style="background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; margin-bottom: 20px; border-radius: 4px; color: #721c24;">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('installer.customize.update') }}">
            @csrf

            <!-- PHP Requirements -->
            <div class="section">
                <h2>System Requirements</h2>
                
                <label>Minimum PHP Version:</label>
                <select name="php_version">
                    @php $currentPhp = $currentConfig['requirements']['php'] ?? '8.2.0'; @endphp
                    <option value="7.4.0" {{ $currentPhp == '7.4.0' ? 'selected' : '' }}>PHP 7.4.0</option>
                    <option value="8.0.0" {{ $currentPhp == '8.0.0' ? 'selected' : '' }}>PHP 8.0.0</option>
                    <option value="8.1.0" {{ $currentPhp == '8.1.0' ? 'selected' : '' }}>PHP 8.1.0</option>
                    <option value="8.2.0" {{ $currentPhp == '8.2.0' ? 'selected' : '' }}>PHP 8.2.0</option>
                    <option value="8.3.0" {{ $currentPhp == '8.3.0' ? 'selected' : '' }}>PHP 8.3.0</option>
                    <option value="8.4.0" {{ $currentPhp == '8.4.0' ? 'selected' : '' }}>PHP 8.4.0</option>
                </select>

                <label>Required PHP Extensions:</label>
                <div class="checkbox-grid">
                    @foreach($availableExtensions as $extension)
                        <div class="checkbox-item">
                            <input type="checkbox" name="extensions[]" value="{{ $extension }}" 
                                   {{ in_array($extension, $currentConfig['requirements']['extensions'] ?? []) ? 'checked' : '' }}>
                            <span>{{ $extension }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Folder Permissions -->
            <div class="section">
                <h2>Folder Permissions</h2>
                <div id="foldersContainer">
                    @foreach($currentConfig['requirements']['folders'] ?? [] as $path => $permission)
                        <div class="folder-row">
                            <input type="text" name="folders[{{ $loop->index }}][path]" value="{{ $path }}" placeholder="Folder path (e.g., storage/app/)">
                            <select name="folders[{{ $loop->index }}][permission]">
                                <option value="755" {{ $permission == '755' ? 'selected' : '' }}>755</option>
                                <option value="775" {{ $permission == '775' ? 'selected' : '' }}>775</option>
                                <option value="777" {{ $permission == '777' ? 'selected' : '' }}>777</option>
                            </select>
                            <button type="button" onclick="removeFolder(this)" class="btn btn-danger btn-small">Remove</button>
                        </div>
                    @endforeach
                </div>
                <button type="button" onclick="addFolder()" class="btn btn-success btn-small">Add Folder</button>
            </div>

            <!-- Branding -->
            <div class="section">
                <h2>Branding & Appearance</h2>
                
                <label>Application Name:</label>
                <input type="text" name="app_name" value="{{ old('app_name', 'Your Application') }}" placeholder="Your Application Name">

                <label>Primary Color:</label>
                <input type="color" name="primary_color" value="{{ old('primary_color', $currentConfig['theme']['primary_color'] ?? '#3490dc') }}" style="width: 60px; height: 40px;">

                <label>Welcome Page Title:</label>
                <input type="text" name="welcome_title" value="{{ old('welcome_title', 'Welcome to the Installation Wizard') }}" placeholder="Welcome to the Installation Wizard">

                <label>Welcome Page Description:</label>
                <textarea name="welcome_description" rows="3" placeholder="This wizard will guide you through the installation process in a few simple steps.">{{ old('welcome_description', 'This wizard will guide you through the installation process in a few simple steps.') }}</textarea>
            </div>

            <!-- Self-Destruct Option -->
            <div class="section">
                <h2>After Customization</h2>
                <label>
                    <input type="checkbox" name="self_destruct" value="1" checked>
                    <strong>Self-Destruct:</strong> Delete this customization interface after applying changes (recommended for client delivery)
                </label>
                <small style="color: #666; display: block; margin-top: 5px;">
                    Uncheck if you want to keep this interface for future modifications
                </small>
            </div>

            <!-- Submit -->
            <div style="text-align: center; padding-top: 20px;">
                <button type="submit" class="btn btn-danger" style="font-size: 16px; padding: 15px 30px;"
                        onclick="return confirm('This will apply your customization settings. Continue?')">
                    🔥 Apply Customization
                </button>
            </div>
        </form>
    </div>

    <script>
    function addFolder() {
        const container = document.getElementById('foldersContainer');
        const index = container.children.length;
        const div = document.createElement('div');
        div.className = 'folder-row';
        div.innerHTML = `
            <input type="text" name="folders[${index}][path]" placeholder="Folder path (e.g., storage/logs/)">
            <select name="folders[${index}][permission]">
                <option value="755">755</option>
                <option value="775" selected>775</option>
                <option value="777">777</option>
            </select>
            <button type="button" onclick="removeFolder(this)" class="btn btn-danger btn-small">Remove</button>
        `;
        container.appendChild(div);
    }

    function removeFolder(button) {
        button.closest('.folder-row').remove();
    }
    </script>
</body>
</html>