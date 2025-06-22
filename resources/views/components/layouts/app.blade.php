<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Page Title' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css'])
    @livewireStyles

    <style>
        /* Custom transition for smooth sliding */
        .sidebar-transition {
            transition: transform 0.3s ease-in-out, width 0.3s ease-in-out;
        }

        /* Ensure the main content adjusts smoothly */
        .main-transition {
            transition: margin-left 0.3s ease-in-out;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-100">

    @if (request()->route()->getName() == 'login')
        <!-- Login page layout - no sidebar -->
        <main class="w-full">
            <div class="p-4">
                {{ $slot }}
            </div>
        </main>
    @else
        <!-- Dashboard layout - with sidebar -->
        <div class="flex relative">
            <x-sidebar />
            <div class="flex-1">
                <main class="main-transition"
                    x-data="{ sidebarOpen: true }"
                    @sidebar-toggled.window="sidebarOpen = $event.detail.open"
                    :class="{ 'ml-64': sidebarOpen, 'ml-0': !sidebarOpen }">
                    <x-header />

                    <div class="p-4">
                        {{ $slot }}
                    </div>

                </main>
            </div>
        </div>
    @endif

    @livewireScripts
</body>

</html>
