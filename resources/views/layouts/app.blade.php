<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-menu')

            @auth
                @can('use-stock')
                    <nav class="border-b border-gray-200 bg-white" aria-label="Inventario">
                        <div class="mx-auto flex max-w-7xl gap-1 overflow-x-auto px-4 sm:px-6 lg:px-8">
                            <a href="{{ route('stock.index') }}" @class(['whitespace-nowrap border-b-2 px-3 py-3 text-sm font-medium', 'border-indigo-500 text-indigo-700' => request()->routeIs('stock.index'), 'border-transparent text-gray-600 hover:border-gray-300 hover:text-gray-900' => ! request()->routeIs('stock.index')])>Dashboard</a>
                            <a href="{{ route('stock.products') }}" @class(['whitespace-nowrap border-b-2 px-3 py-3 text-sm font-medium', 'border-indigo-500 text-indigo-700' => request()->routeIs('stock.products') || request()->routeIs('products.*'), 'border-transparent text-gray-600 hover:border-gray-300 hover:text-gray-900' => ! request()->routeIs('stock.products') && ! request()->routeIs('products.*')])>Productos</a>
                            <a href="{{ route('stock.movements') }}" @class(['whitespace-nowrap border-b-2 px-3 py-3 text-sm font-medium', 'border-indigo-500 text-indigo-700' => request()->routeIs('stock.movements'), 'border-transparent text-gray-600 hover:border-gray-300 hover:text-gray-900' => ! request()->routeIs('stock.movements')])>Movimientos</a>
                        </div>
                    </nav>
                @endcan
            @endauth

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        @livewireScripts
    </body>
</html>
