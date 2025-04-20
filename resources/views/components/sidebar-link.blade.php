@props(['active'])

@php
    $classes =
        $active ?? false
            ? 'flex items-center w-full px-4 py-2 text-white font-medium bg-gray-700 rounded-md focus:outline-none focus:bg-gray-700 transition duration-150 ease-in-out'
            : 'flex items-center w-full px-4 py-2 text-gray-300 font-medium hover:text-white hover:bg-gray-700 rounded-md focus:outline-none focus:bg-gray-700 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
