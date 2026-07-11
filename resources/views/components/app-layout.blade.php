<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        const theme = localStorage.getItem("theme");
        if (theme) {
            document.documentElement.setAttribute("data-theme", theme);
        }
    </script>
</head>

<body class="h-full antialiased">

    <div class="flex h-full">

        {{-- ============ SIDEBAR ============ --}}
        <x-partials.sidebar>
            {{ $slot }}
        </x-partials.sidebar>
    </div>

</body>

</html>
