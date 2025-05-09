<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light" class="scroll-smooth"
    :class="{ 'theme-dark': dark }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel Presensi') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Remix Icon - Untuk ikon sidebar -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>

<body class="font-sans antialiased">
    @include('admin.layouts.navigation')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
        integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

        <script>
        // Function to update date and time
        function updateDateTime() {
            const date = new Date();
            
            // Format the date (example: Monday, 10 May 2025)
            const dateString = date.toLocaleDateString('id-ID', {
                weekday: 'long', // Day of the week (example: Monday)
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            });

            // Format the time (example: 15:30:45)
            const timeString = date.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
            });

            // Combine date and time
            const dateTimeString = `${dateString}, ${timeString}`;

            // Set the element with ID 'date-time' to the value of dateTimeString
            document.getElementById('date-time').textContent = dateTimeString;
        }

        // Update time every second
        setInterval(updateDateTime, 1000);

        // Call once to show the time immediately when the page loads
        updateDateTime();
    </script>
</body>

</html>
