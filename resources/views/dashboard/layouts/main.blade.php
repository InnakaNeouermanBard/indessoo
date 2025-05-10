<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light" class="scroll-smooth"
    :class="{ 'theme-dark': dark }" x-data="data()">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" href="{{ asset('img\logo.png') }}" />
    <title>{{ $title }}</title>

    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('img\indesso.png') }}" />

    @include('dashboard.layouts.link')
    @yield('css')
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // Set theme based on localStorage or media preference
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>

<body
    class="leading-default m-0 bg-gray-50 font-sans text-base font-normal text-slate-500 antialiased dark:bg-blue-800 dark:text-white">
    <!-- Body with light mode white and dark mode blue -->
    <div class="min-h-75 absolute w-full bg-white dark:bg-blue-800 top-0"></div>

    @include('dashboard.layouts.sidebar')

    <main class="xl:ml-68 relative h-full max-h-screen rounded-xl transition-all duration-200 ease-in-out">
        @include('dashboard.layouts.navbar')

        <div class="mx-auto w-full px-6 py-6">
            @yield('container')
            @include('dashboard.layouts.footer')
        </div>
    </main>

    <!-- Footer and other sections will also change based on theme -->
    <footer class="bg-white dark:bg-blue-800">
        @include('dashboard.layouts.footer')
    </footer>

    @include('dashboard.layouts.script')
    @yield('js')
    @vite('resources/js/app.js')
</body>

</html>
