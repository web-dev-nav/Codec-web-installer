<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Installer Customizer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-red-600 text-white p-4">
            <div class="max-w-6xl mx-auto flex justify-between items-center">
                <h1 class="text-2xl font-bold">🔧 Installer Customizer</h1>
                <div class="text-sm bg-red-800 px-3 py-1 rounded">
                    ⚠️ DEVELOPMENT ONLY - Will self-destruct after use
                </div>
            </div>
        </header>

        <!-- Navigation -->
        <nav class="bg-white shadow">
            <div class="max-w-6xl mx-auto px-4">
                <div class="flex space-x-8">
                    <a href="{{ route('installer.customize.dashboard') }}" 
                       class="py-4 border-b-2 {{ request()->routeIs('installer.customize.dashboard') ? 'border-red-500 text-red-600' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('installer.customize.requirements') }}" 
                       class="py-4 border-b-2 {{ request()->routeIs('installer.customize.requirements') ? 'border-red-500 text-red-600' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
                        Requirements
                    </a>
                    <a href="{{ route('installer.customize.branding') }}" 
                       class="py-4 border-b-2 {{ request()->routeIs('installer.customize.branding') ? 'border-red-500 text-red-600' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
                        Branding
                    </a>
                    <a href="{{ route('installer.customize.export') }}" 
                       class="py-4 border-b-2 {{ request()->routeIs('installer.customize.export') ? 'border-red-500 text-red-600' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
                        Export & Finalize
                    </a>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <main class="max-w-6xl mx-auto p-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>