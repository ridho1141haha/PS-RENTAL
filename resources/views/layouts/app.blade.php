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
        <script>
            // On page load or when changing themes, best to add inline in `head` to avoid FOUC
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark')
            }
        </script>
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-slate-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
        <div class="min-h-screen bg-gray-100 dark:bg-slate-900">
            @include('layouts.navigation')

            @php
                $promoBanner = \App\Models\Setting::where('key', 'promo_text')->first()->value ?? null;
            @endphp
            @if($promoBanner)
            <div class="bg-indigo-600 dark:bg-indigo-500 text-white shadow relative border-b border-indigo-700 dark:border-indigo-400">
                <div class="max-w-7xl mx-auto py-2 px-4 sm:px-6 lg:px-8 text-center text-sm font-bold flex justify-center items-center gap-2">
                    <span class="animate-bounce">📢</span>
                    <span class="animate-pulse">{{ $promoBanner }}</span>
                    <span class="animate-bounce">📢</span>
                </div>
            </div>
            @endif

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-slate-800 shadow dark:shadow-none border-b border-gray-200 dark:border-slate-700">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
