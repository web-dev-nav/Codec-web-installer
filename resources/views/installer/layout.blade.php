<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Installation Wizard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-blue-600 text-white p-6">
                    <h1 class="text-2xl font-bold">Installation Wizard</h1>
                    @isset($step)
                        <div class="mt-4">
                            <div class="flex items-center space-x-4">
                                @for($i = 1; $i <= 4; $i++)
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $i <= $step ? 'bg-white text-blue-600' : 'bg-blue-400 text-white' }}">
                                            {{ $i }}
                                        </div>
                                        @if($i < 4)
                                            <div class="w-12 h-1 {{ $i < $step ? 'bg-white' : 'bg-blue-400' }}"></div>
                                        @endif
                                    </div>
                                @endfor
                            </div>
                        </div>
                    @endisset
                </div>
                
                <div class="p-6">
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</body>
</html>