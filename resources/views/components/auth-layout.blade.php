<!DOCTYPE html>
<html lang="en" class="h-full">

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

<body class="h-full">

    <div class="flex flex-col justify-center min-h-full px-6 py-12 lg:px-8">

        {{ $slot }}

    </div>

</body>

</html>
