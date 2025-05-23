<aside
    class="dark:bg-slate-850 max-w-64 ease-nav-brand z-990 fixed inset-y-0 my-4 block w-full -translate-x-full flex-wrap items-center justify-between overflow-y-auto rounded-2xl border-0 bg-white p-0 antialiased shadow-xl transition-transform duration-200 dark:shadow-none xl:left-0 xl:ml-6 xl:translate-x-0"
    aria-expanded="false">
    <div class="h-19">
        <i class="ri-close-large-fill absolute right-0 top-0 cursor-pointer p-4 text-slate-400 opacity-50 dark:text-white xl:hidden"
            sidenav-close></i>
        <a class="m-0 block whitespace-nowrap px-8 py-6 text-sm text-slate-700 dark:text-white"
            href="{{ route('karyawan.dashboard') }}">
            <!-- Logo untuk mode terang -->
            <img src="{{ asset('img/logo-fix.png') }}"
                class="ease-nav-brand inline max-h-12 w-auto transition-all duration-200 dark:hidden" alt="main_logo" />
            <!-- Logo untuk mode gelap -->
            <img src="{{ asset('img/logo-fix.png') }}"
                class="ease-nav-brand hidden max-h-12 w-auto transition-all duration-200 dark:inline" alt="main_logo" />
        </a>
    </div>



    <hr
        class="mt-0 h-px bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent dark:bg-gradient-to-r dark:from-transparent dark:via-white dark:to-transparent" />

    <div class="h-sidenav block max-h-screen w-auto grow basis-full items-center overflow-auto">
        <ul class="mb-0 flex flex-col pl-0">
            <li class="mt-0.5 w-full">
                <a class="py-2.7 ease-nav-brand mx-2 my-0 flex items-center whitespace-nowrap px- 4 text-sm transition-colors dark:text-white dark:opacity-80 {{ Request::routeIs(['karyawan.dashboard']) ? 'rounded-lg font text-slate-700 bg-blue-500/13' : '' }}"
                    href="{{ route('karyawan.dashboard') }}">
                    <div
                        class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                        <i class="ri-tv-2-line relative top-0 text-lg leading-normal text-blue-500"></i>
                    </div>
                    <span class="ease pointer-events-none ml-1 opacity-100 duration-300">Dashboard</span>
                </a>
            </li>

            <li class="mt-0.5 w-full">
                <a class="py-2.7 ease-nav-brand mx-2 my-0 flex items-center whitespace-nowrap px-4 text-sm transition-colors dark:text-white dark:opacity-80 {{ Request::routeIs(['karyawan.presensi']) ? 'rounded-lg font text-slate-700 bg-blue-500/13' : '' }}"
                    href="{{ route('karyawan.presensi') }}">
                    <div
                        class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                        <i class="ri-camera-fill relative top-0 text-lg leading-normal text-purple-500"></i>
                    </div>
                    <span class="ease pointer-events-none ml-1 opacity-100 duration-300">Presensi</span>
                </a>
            </li>

            {{-- <li class="mt-0.5 w-full">
                <a class="py-2.7 ease-nav-brand mx-2 my-0 flex items-center whitespace-nowrap px-4 text-sm transition-colors dark:text-white dark:opacity-80 {{ Request::routeIs(['karyawan.history']) ? 'rounded-lg font text-slate-700 bg-blue-500/13' : '' }}"
                    href="{{ route('karyawan.history') }}">
                    <div
                        class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                        <i class="ri-history-fill relative top-0 text-lg leading-normal text-gray-500"></i>
                    </div>
                    <span class="ease pointer-events-none ml-1 opacity-100 duration-300">History</span>
                </a>
            </li> --}}

            <li class="mt-0.5 w-full">
                <a class="py-2.7 ease-nav-brand mx-2 my-0 flex items-center whitespace-nowrap px-4 text-sm transition-colors dark:text-white dark:opacity-80 {{ Request::routeIs(['karyawan.izin', 'karyawan.izin.create']) ? 'rounded-lg font text-slate-700 bg-blue-500/13' : '' }}"
                    href="{{ route('karyawan.izin') }}">
                    <div
                        class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                        <i class="ri-calendar-close-fill relative top-0 text-lg leading-normal text-red-500"></i>
                    </div>
                    <span class="ease pointer-events-none ml-1 opacity-100 duration-300">Izin</span>
                </a>
            </li>
            <li class="mt-0.5 w-full">
                <a class="py-2.7 ease-nav-brand mx-2 my-0 flex items-center whitespace-nowrap px-4 text-sm transition-colors dark:text-white dark:opacity-80 {{ Request::routeIs(['karyawan.form-lembur.index']) ? 'rounded-lg font text-slate-700 bg-blue-500/13' : '' }}"
                    href="{{ route('karyawan.form-lembur.index') }}">
                    <div
                        class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                        <i class="ri-calendar-close-fill relative top-0 text-lg leading-normal text-red-500"></i>
                    </div>
                    <span class="ease pointer-events-none ml-1 opacity-100 duration-300">Lembur</span>
                </a>
            </li>
            <li class="mt-0.5 w-full">
                <a class="py-2.7 ease-nav-brand mx-2 my-0 flex items-center whitespace-nowrap px-4 text-sm transition-colors dark:text-white dark:opacity-80 {{ Request::routeIs(['karyawan.form-lembur.index']) ? 'rounded-lg font text-slate-700 bg-blue-500/13' : '' }}"
                    href="{{ route('karyawan.jadwalkerja.index') }}">
                    <div
                        class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                        <i class="ri-calendar-close-fill relative top-0 text-lg leading-normal text-red-500"></i>
                    </div>
                    <span class="ease pointer-events-none ml-1 opacity-100 duration-300">Jadwal</span>
                </a>
            </li>

            <li class="mt-0.5 w-full">
                <a class="py-2.7 ease-nav-brand mx-2 my-0 flex items-center whitespace-nowrap px-4 text-sm transition-colors dark:text-white dark:opacity-80 {{ Request::routeIs(['karyawan.profile']) ? 'rounded-lg font text-slate-700 bg-blue-500/13' : '' }}"
                    href="{{ route('karyawan.profile') }}">
                    <div
                        class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                        <i class="ri-user-3-fill relative top-0 text-lg leading-normal text-blue-500"></i>
                    </div>
                    <span class="ease pointer-events-none ml-1 opacity-100 duration-300">Profile</span>
                </a>
            </li>


            <li class="mt-0.5 w-full">
                <a class="py-2.7 ease-nav-brand mx-2 my-0 flex items-center whitespace-nowrap px-4 text-sm transition-colors dark:text-white dark:opacity-80 {{ Request::routeIs(['karyawan.profile']) ? 'rounded-lg font text-slate-700 bg-blue-500/13' : '' }}"
                    href="{{ route('karyawan.laporan') }}">
                    <div
                        class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                        <i class="ri-user-3-fill relative top-0 text-lg leading-normal text-blue-500"></i>
                    </div>
                    <span class="ease pointer-events-none ml-1 opacity-100 duration-300">Laporan</span>
                </a>
            </li>

            {{-- <li class="mt-0.5 w-full">
                <form id="logout-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="button" onclick="openLogoutModal()"
                        class="py-2.7 ease-nav-brand mx-2 my-0 flex items-center whitespace-nowrap px-4 text-sm transition-colors dark:text-white dark:opacity-80 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg">
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center stroke-0 text-center xl:p-2.5">
                            <i class="ri-logout-box-line relative top-0 text-lg leading-normal text-red-500"></i>
                        </div>
                        <span class="ease pointer-events-none ml-1 opacity-100 duration-300">Logout</span>
                    </button>
                </form>
            </li> --}}
        </ul>
    </div>
</aside>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="fixed inset-0 z-[9999] hidden overflow-y-auto overflow-x-hidden">
    <!-- Modal Background Overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300 ease-in-out" id="modalOverlay">
    </div>

    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative opacity-0 translate-y-10 transition-all duration-300 ease-in-out transform bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full max-w-md mx-auto"
            id="modalContent">
            <!-- Modal Header -->
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

            <!-- Modal Body -->
            <div class="p-5">
                <p class="text-gray-600 dark:text-gray-300 mb-6">
                    Apakah Anda yakin ingin keluar dari sistem presensi? Semua sesi yang sedang berjalan akan berakhir.
                </p>

                <!-- Modal Footer -->
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

        // Show the modal
        modal.classList.remove('hidden');

        // Animate in
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

        // Animate out
        modalContent.classList.remove('opacity-100', 'translate-y-0');
        modalContent.classList.add('opacity-0', 'translate-y-10');
        modalOverlay.classList.remove('opacity-100');

        // Hide the modal after animation completes
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    function confirmLogout() {
        // Visual feedback before submitting
        const logoutButton = document.querySelector('[onclick="confirmLogout()"]');
        logoutButton.innerHTML = '<i class="ri-loader-4-line animate-spin mr-1"></i> Logging out...';
        logoutButton.disabled = true;

        // Submit the form after a brief delay for visual feedback
        setTimeout(() => {
            document.getElementById('logout-form').submit();
        }, 500);
    }
</script>
