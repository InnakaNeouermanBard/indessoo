<x-guest-layout>
<!-- Session Status -->
<x-auth-session-status class="mb-4" :status="session('status')" />

<!-- Form Login -->
<form method="POST" action="{{ route('login') }}">
    @csrf

    <!-- Username (Email or NIK) -->
    <div>
        <x-input-label for="username" :value="__('Email or NIK')" />
        <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" />
        <x-input-error :messages="$errors->get('username')" class="mt-2" />
    </div>

    <!-- Password -->
    <div class="mt-4">
        <x-input-label for="password" :value="__('Password')" />
        <div class="relative">
            <x-text-input id="password" class="block mt-1 w-full pr-10" type="password" name="password" required autocomplete="current-password" />
            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500">
                <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <!-- Mata terbuka -->
                    <path id="eye-path"
                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path
                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
        </div>
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <!-- Remember Me -->
    <div class="block mt-4">
        <label for="remember_me" class="inline-flex items-center">
            <input id="remember_me" type="checkbox"
                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
            <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
        </label>
    </div>

    <div class="flex items-center justify-end mt-4">
        <x-primary-button class="ms-3">
            {{ __('Log in') }}
        </x-primary-button>
    </div>
</form>

<!-- Modal Pop-Up Error -->
<div id="error-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex justify-center items-center hidden z-50 transition-opacity duration-300">
<div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 ease-in-out">
    <div class="flex flex-col items-center text-center">
        <!-- Blue circular icon container -->
        <div class="bg-blue-100 p-3 rounded-full mb-4">
            <!-- Information icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        
        <h3 class="text-xl font-bold text-gray-800 mb-1">Login Gagal</h3>
        <p class="text-blue-600 font-medium mb-6" id="error-message">Harap Masukkan Data Secara Benar!</p>
        
        <div class="w-full">
            <button onclick="closeModal()" class="bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg w-full font-medium transition-colors duration-200 transform hover:scale-105">
                OK
            </button>
        </div>
    </div>
</div>
</div>

<script>
function closeModal() {
const modal = document.getElementById('error-modal');
modal.classList.add('opacity-0');
setTimeout(() => {
    modal.classList.add('hidden');
    modal.classList.remove('opacity-0');
}, 300);
}

function showModal(message = "Harap Masukkan Data Secara Benar!") {
const modal = document.getElementById('error-modal');
const errorMessage = document.getElementById('error-message');
errorMessage.textContent = message;
modal.classList.remove('hidden');
setTimeout(() => {
    modal.classList.add('opacity-100');
}, 10);
}
</script>

<!-- Toggle Password Visibility Script -->
<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        const isPassword = input.type === 'password';

        input.type = isPassword ? 'text' : 'password';

        // Toggle icon
        eyeIcon.innerHTML = isPassword
            ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.969 9.969 0 012.132-3.568M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 3l18 18" />`
            : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
    }

    function openModal() {
        document.getElementById('error-modal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('error-modal').classList.add('hidden');
    }

    // Check if login failed and show the modal
    @if ($errors->any())
        openModal();
    @endif
</script>
</x-guest-layout>
