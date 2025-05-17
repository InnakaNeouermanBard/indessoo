<aside
    class="dark:bg-blue-800 max-w-64 ease-nav-brand z-990 fixed inset-0 top-0 left-0 block w-full -translate-x-full flex-wrap items-center justify-between overflow-y-auto bg-white p-0 antialiased shadow-xl transition-transform duration-200 dark:shadow-none xl:translate-x-0"
    aria-expanded="false">

    <!-- Burger Menu Button (untuk tampilan mobile) -->
    <button class="xl:hidden text-white p-4 ml-2" onclick="toggleSidebar()">
        <i class="ri-menu-3-line text-2xl"></i>
    </button>

    <div class="h-19 flex justify-center items-center bg-white">
        <i class="ri-close-large-fill absolute right-0 top-0 cursor-pointer p-4 text-white opacity-50 xl:hidden"
            sidenav-close></i>
        <a class="m-0 block whitespace-nowrap px-8 py-6 text-sm text-white dark:text-black"
            href="{{ route('karyawan.dashboard') }}">
            <!-- Logo untuk mode terang -->
            <img src="{{ asset('img/logo-fix.png') }}"
                class="ease-nav-brand inline max-h-32 w-auto transition-all duration-200 dark:hidden" alt="main_logo" />
            <!-- Logo untuk mode gelap -->
            <img src="{{ asset('img/logo-fix.png') }}"
                class="ease-nav-brand hidden max-h-32 w-auto transition-all duration-200 dark:inline" alt="main_logo" />
        </a>
    </div>

    <hr
        class="mt-0 h-px bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent" />

    <div class="h-sidenav block max-h-screen w-auto bg-blue-800 grow basis-full items-center overflow-auto">
        <ul class="mb-0 flex flex-col pl-0">
            @php
                $isActive = Request::routeIs('karyawan.dashboard');
            @endphp

            <li class="mt-0.5 w-full">
                <a href="{{ route('karyawan.dashboard') }}"
                class="{{ $isActive 
                    ? 'flex items-center w-full px-4 py-2 text-white font-medium bg-gray-700 rounded-md focus:outline-none focus:bg-gray-700 transition duration-150 ease-in-out' 
                    : 'flex items-center w-full px-4 py-2 text-gray-300 font-medium hover:text-white hover:bg-gray-700 rounded-md focus:outline-none focus:bg-gray-700 transition duration-150 ease-in-out' }}">
                    <i class="ri-tv-2-line mr-2 text-lg leading-normal"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            {{-- @php
                $isActive = Request::routeIs('karyawan.dashboard');
            @endphp

            <li class="mt-0.5 w-full">
                <a href="{{ route('karyawan.dashboard') }}"
                class="{{ $isActive 
                    ? 'flex items-center w-full px-4 py-2 text-white font-medium bg-gray-700 rounded-md focus:outline-none focus:bg-gray-700 transition duration-150 ease-in-out' 
                    : 'flex items-center w-full px-4 py-2 text-gray-300 font-medium hover:text-white hover:bg-gray-700 rounded-md focus:outline-none focus:bg-gray-700 transition duration-150 ease-in-out' }}">
                    <i class="ri-tv-2-line mr-2 text-lg leading-normal"></i>
                    <span>Dashboard</span>
                </a>
            </li> --}}

            @php
                $isActive = Request::routeIs('karyawan.presensi');
            @endphp

            <li class="mt-0.5 w-full">
                <a href="{{ route('karyawan.presensi') }}"
                class="{{ $isActive 
                    ? 'flex items-center w-full px-4 py-2 text-white font-medium bg-gray-700 rounded-md focus:outline-none focus:bg-gray-700 transition duration-150 ease-in-out' 
                    : 'flex items-center w-full px-4 py-2 text-gray-300 font-medium hover:text-white hover:bg-gray-700 rounded-md focus:outline-none focus:bg-gray-700 transition duration-150 ease-in-out' }}">
                    <i class="ri-camera-fill mr-2 text-lg leading-normal"></i>
                    <span>Absensi</span>
                </a>
            </li>

            @php
                $isActive = Request::routeIs('karyawan.izin');
            @endphp

            <li class="mt-0.5 w-full">
                <a href="{{ route('karyawan.izin') }}"
                class="{{ $isActive 
                    ? 'flex items-center w-full px-4 py-2 text-white font-medium bg-gray-700 rounded-md focus:outline-none focus:bg-gray-700 transition duration-150 ease-in-out' 
                    : 'flex items-center w-full px-4 py-2 text-gray-300 font-medium hover:text-white hover:bg-gray-700 rounded-md focus:outline-none focus:bg-gray-700 transition duration-150 ease-in-out' }}">
                    <i class="ri-calendar-close-fill mr-2 text-lg leading-normal"></i>
                    <span>Form Cuti</span>
                </a>
            </li>

            @php
                $isActive = Request::routeIs('karyawan.form-lembur.index');
            @endphp

            <li class="mt-0.5 w-full">
                <a href="{{ route('karyawan.form-lembur.index') }}"
                class="{{ $isActive 
                    ? 'flex items-center w-full px-4 py-2 text-white font-medium bg-gray-700 rounded-md focus:outline-none focus:bg-gray-700 transition duration-150 ease-in-out' 
                    : 'flex items-center w-full px-4 py-2 text-gray-300 font-medium hover:text-white hover:bg-gray-700 rounded-md focus:outline-none focus:bg-gray-700 transition duration-150 ease-in-out' }}">
                    <i class="ri-time-fill mr-2 text-lg leading-normal"></i>
                    <span>Form Lembur</span>
                </a>
            </li>

            @php
                $isActive = Request::routeIs('karyawan.profile');
            @endphp

            <li class="mt-0.5 w-full">
                <a href="{{ route('karyawan.profile') }}"
                class="{{ $isActive 
                    ? 'flex items-center w-full px-4 py-2 text-white font-medium bg-gray-700 rounded-md focus:outline-none focus:bg-gray-700 transition duration-150 ease-in-out' 
                    : 'flex items-center w-full px-4 py-2 text-gray-300 font-medium hover:text-white hover:bg-gray-700 rounded-md focus:outline-none focus:bg-gray-700 transition duration-150 ease-in-out' }}">
                    <i class="ri-user-3-fill mr-2 text-lg leading-normal"></i>
                    <span>Profile</span>
                </a>
            </li>

            @php
                $isActive = Request::routeIs('karyawan.jadwalkerja.index');
            @endphp

            <li class="mt-0.5 w-full">
                <a href="{{ route('karyawan.jadwalkerja.index') }}"
                class="{{ $isActive 
                    ? 'flex items-center w-full px-4 py-2 text-white font-medium bg-gray-700 rounded-md focus:outline-none focus:bg-gray-700 transition duration-150 ease-in-out' 
                    : 'flex items-center w-full px-4 py-2 text-gray-300 font-medium hover:text-white hover:bg-gray-700 rounded-md focus:outline-none focus:bg-gray-700 transition duration-150 ease-in-out' }}">
                    <i class="ri-calendar-line mr-2 text-lg leading-normal"></i>
                    <span>Jadwal</span>
                </a>
            </li>

            @php
                $isActive = Request::routeIs('karyawan.laporan');
            @endphp

            <li class="mt-0.5 w-full">
                <a href="{{ route('karyawan.laporan') }}"
                class="{{ $isActive 
                    ? 'flex items-center w-full px-4 py-2 text-white font-medium bg-gray-700 rounded-md focus:outline-none focus:bg-gray-700 transition duration-150 ease-in-out' 
                    : 'flex items-center w-full px-4 py-2 text-gray-300 font-medium hover:text-white hover:bg-gray-700 rounded-md focus:outline-none focus:bg-gray-700 transition duration-150 ease-in-out' }}">
                    <i class="ri-file-list-line mr-2 text-lg leading-normal"></i>
                    <span>Laporan</span>
                </a>
            </li>


        </ul>
    </div>
</aside>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="fixed inset-0 z-[9999] hidden overflow-y-auto overflow-x-hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300 ease-in-out" id="modalOverlay">
    </div>

    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative opacity-0 translate-y-10 transition-all duration-300 ease-in-out transform bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md mx-auto"
            id="modalContent">
            <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex items-center">
                <div class="bg-red-100 dark:bg-red-900/30 rounded-full p-3 mr-3">
                    <i class="ri-logout-box-line text-2xl text-red-500"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Konfirmasi Logout
                </h3>
                <button type="button"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
                    onclick="closeLogoutModal()">
                    <i class="ri-close-line text-2xl"></i>
                </button>
            </div>

            <div class="p-5">
                <p class="text-gray-600 dark:text-gray-300 mb-6">
                    Apakah Anda yakin ingin keluar dari sistem presensi? Semua sesi yang sedang berjalan akan berakhir.
                </p>

                <div class="flex space-x-3 justify-end">
                    <button type="button" onclick="closeLogoutModal()"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-400 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-white">
                        <i class="ri-close-line mr-1"></i> Batal
                    </button>
                    <button type="button" onclick="confirmLogout()"
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-400">
                        <i class="ri-logout-box-line mr-1"></i> Ya, Logout
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openLogoutModal() {
        const modal = document.getElementById('logoutModal');
        const modalContent = document.getElementById('modalContent');
        const modalOverlay = document.getElementById('modalOverlay');

        modal.classList.remove('hidden');
        setTimeout(() => {
            modalOverlay.classList.add('opacity-100');
            modalContent.classList.remove('opacity-0', 'translate-y-10');
            modalContent.classList.add('opacity-100', 'translate-y-0');
        }, 10);
    }

    function closeLogoutModal() {
        const modal = document.getElementById('logoutModal');
        const modalContent = document.getElementById('modalContent');
        const modalOverlay = document.getElementById('modalOverlay');

        modalContent.classList.remove('opacity-100', 'translate-y-0');
        modalContent.classList.add('opacity-0', 'translate-y-10');
        modalOverlay.classList.remove('opacity-100');

        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function confirmLogout() {
        const logoutButton = document.querySelector('[onclick="confirmLogout()"]');
        logoutButton.innerHTML = '<i class="ri-loader-4-line animate-spin mr-1"></i> Logging out...';
        logoutButton.disabled = true;

        setTimeout(() => {
            document.getElementById('logout-form').submit();
        }, 500);
    }

    function toggleSidebar() {
        const sidebar = document.querySelector('aside');
        sidebar.classList.toggle('-translate-x-full');
    }
</script>
