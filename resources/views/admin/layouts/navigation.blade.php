{{-- Sidebar Navigation with fixed left positioning --}}
<div x-data="{ open: false }">
    <div class="flex min-h-screen bg-gray-100">
        {{-- Desktop Sidebar - Fixed Left --}}
        <div class="hidden sm:block w-64 bg-gray-800 text-white fixed inset-y-0 left-0 z-30 overflow-y-auto">
            {{-- Logo --}}
            <div class="flex items-center justify-center h-16 border-b border-gray-700">
                <a href="{{ route('admin.dashboard') }}">
                    <x-application-logo class="block h-10 w-auto fill-current text-white" />
                </a>
            </div>

            {{-- Admin Title --}}
            <div class="px-4 py-3 border-b border-gray-700">
                <h2 class="text-lg font-bold text-center text-white">Admin Panel</h2>
            </div>

            {{-- Navigation Links --}}
            <div class="py-4 space-y-1">
                <x-sidebar-link :href="route('admin.monitoring-presensi')" :active="request()->routeIs('admin.monitoring-presensi')">
                    <i class="ri-fingerprint-line mr-2"></i> {{ __('Absensi') }}
                </x-sidebar-link>
                {{-- <x-sidebar-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    <i class="ri-dashboard-line mr-2"></i> {{ __('Dashboard') }}
                </x-sidebar-link> --}}
                <x-sidebar-link :href="route('admin.administrasi-presensi')" :active="request()->routeIs('admin.administrasi-presensi')">
                    <i class="ri-settings-line mr-2"></i> {{ __('Form Perizinan') }}
                </x-sidebar-link>
                <x-sidebar-link :href="route('form-lembur.index')" :active="request()->routeIs('form-lembur')">
                    <i class="ri-file-chart-line mr-2"></i> {{ __('Form Lembur') }}
                </x-sidebar-link>
                <x-sidebar-link :href="route('admin-management')" :active="request()->routeIs('admin-management')">
                    <i class="ri-user-line mr-2"></i> {{ __('Data Admin') }}
                </x-sidebar-link>

                <x-sidebar-link :href="route('admin.karyawan')" :active="request()->routeIs('admin.karyawan')">
                    <i class="ri-user-line mr-2"></i> {{ __('Data Karyawan') }}
                </x-sidebar-link>
                <x-sidebar-link :href="route('jadwal-shift.index')" :active="request()->routeIs('admin.jadwal')">
                    <i class="ri-file-chart-line mr-2"></i> {{ __('Jadwal Kerja') }}
                </x-sidebar-link>
                <x-sidebar-link :href="route('admin.laporan.presensi')" :active="request()->routeIs('admin.laporan.presensi')">
                    <i class="ri-file-chart-line mr-2"></i> {{ __('Laporan') }}
                </x-sidebar-link>
                {{-- <x-sidebar-link :href="route('admin.departemen')" :active="request()->routeIs('admin.departemen')">
                    <i class="ri-building-line mr-2"></i> {{ __('Data Departemen') }}
                </x-sidebar-link> --}}
                <x-sidebar-link :href="route('admin.lokasi-kantor')" :active="request()->routeIs('admin.lokasi-kantor')">
                    <i class="ri-map-pin-line mr-2"></i> {{ __('Lokasi Kantor') }}
                </x-sidebar-link>
            </div>
        </div>

        {{-- Main Content Area with left margin for sidebar --}}
        <div class="flex-1 sm:ml-64">
            {{-- Top Navigation Bar (Mobile & User Profile) --}}
            <div class="bg-white border-b border-gray-100 sticky top-0 z-20">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        {{-- Mobile Hamburger --}}
                        <div class="flex items-center sm:hidden">
                            <button @click="open = ! open"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Mobile Logo --}}
                        <div class="sm:hidden flex justify-center flex-1">
                            <a href="{{ route('admin.dashboard') }}" class="text-lg font-semibold text-gray-800">
                                Presensi
                            </a>
                        </div>

                        {{-- Settings Dropdown --}}
                        <div class="flex items-center ml-auto">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                        <div>{{ Auth::user()->name }}</div>

                                        <div class="ml-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>

                                    <!-- Authentication -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf

                                        <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault();
                                                            this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mobile Navigation Dropdown --}}
            <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden bg-white shadow-md z-20">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <x-responsive-sidebar-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        <i class="ri-dashboard-line mr-2"></i> {{ __('Dashboard') }}
                    </x-responsive-sidebar-link>

                    <x-responsive-sidebar-link :href="route('admin.karyawan')" :active="request()->routeIs('admin.karyawan')">
                        <i class="ri-user-line mr-2"></i> {{ __('Data User') }}
                    </x-responsive-sidebar-link>

                    <x-responsive-sidebar-link :href="route('admin.karyawan')" :active="request()->routeIs('admin.karyawan')">
                        <i class="ri-user-line mr-2"></i> {{ __('Data Karyawan') }}
                    </x-responsive-sidebar-link>

                    <x-responsive-sidebar-link :href="route('admin.departemen')" :active="request()->routeIs('admin.departemen')">
                        <i class="ri-building-line mr-2"></i> {{ __('Data Departemen') }}
                    </x-responsive-sidebar-link>

                    <x-responsive-sidebar-link :href="route('admin.monitoring-presensi')" :active="request()->routeIs('admin.monitoring-presensi')">
                        <i class="ri-fingerprint-line mr-2"></i> {{ __('Monitoring Presensi') }}
                    </x-responsive-sidebar-link>

                    <x-responsive-sidebar-link :href="route('admin.laporan.presensi')" :active="request()->routeIs('admin.laporan.presensi')">
                        <i class="ri-file-chart-line mr-2"></i> {{ __('Laporan Presensi') }}
                    </x-responsive-sidebar-link>

                    <x-responsive-sidebar-link :href="route('admin.lokasi-kantor')" :active="request()->routeIs('admin.lokasi-kantor')">
                        <i class="ri-map-pin-line mr-2"></i> {{ __('Lokasi Kantor') }}
                    </x-responsive-sidebar-link>

                    <x-responsive-sidebar-link :href="route('admin.administrasi-presensi')" :active="request()->routeIs('admin.administrasi-presensi')">
                        <i class="ri-settings-line mr-2"></i> {{ __('Administrasi Presensi') }}
                    </x-responsive-sidebar-link>
                </div>
            </div>

            {{-- Page Content --}}
            <div class="flex-1">
                {{-- Page Header --}}
                @if (isset($header))
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <main>
                    {{ $slot ?? '' }}
                </main>
            </div>
        </div>
    </div>
</div>
