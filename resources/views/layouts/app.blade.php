<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @yield('styles')
</head>
<body class="bg-gray-100 min-h-screen flex">
    @php
        use App\Models\Setting;
        $setting = Setting::first();    
    @endphp
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg hidden md:flex flex-col">
        <div class="p-6 border-b">

            @if ($setting && $setting->logo_path && file_exists(storage_path('app/public/' . $setting->logo_path)))
                <img src="{{ asset('storage/' . $setting->logo_path) }}" 
                     alt="Logo" 
                     class="mt-4 w-32 h-auto" 
                     style="width: 120px; height: auto;">
            @else
                <span class="mt-4 block text-gray-600">No Logo</span>
            @endif

        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded hover:bg-gray-100 font-medium">Dashboard</a>
            <a href="{{ route('orders.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 font-medium">Orders</a>
            <a href="{{ route('orders.create') }}" class="block px-4 py-2 rounded hover:bg-gray-100 font-medium">+ New Order</a>
            <a href="{{ route('size.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 font-medium">Size Group</a>
            <a href="{{ route('packs.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 font-medium">Packs</a>
            <a href="{{ route('addOn.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 font-medium">Add-Ons</a>
            <a href="{{ route('settings') }}" class="block px-4 py-2 rounded hover:bg-gray-100 font-medium">Settings</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full text-left px-4 py-2 rounded hover:bg-gray-100 font-medium">
                    Logout
                </button>
            </form>
        </nav>
        <div class="p-4 border-t text-gray-600 text-sm">
            &copy; {{ date('Y') }} My App
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <!-- Top Navbar -->
        <header class="bg-white shadow flex justify-between items-center px-6 py-4">
            <h1 class="text-xl font-semibold text-gray-700">Order List</h1>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">Hello, {{ auth()->user()->name ?? 'User' }}</span>
                <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5.121 17.804A12.07 12.07 0 0112 15c2.485 0 4.78.758 6.879 2.048M15 10a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-8 overflow-x-auto">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>

    @yield('scripts')
</body>
</html>