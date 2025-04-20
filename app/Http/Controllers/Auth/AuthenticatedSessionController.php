<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Cek apakah user login sebagai admin atau karyawan
        if (Auth::guard('web')->check()) {
            return redirect()->intended(route('admin.dashboard'));
        } elseif (Auth::guard('karyawan')->check()) {
            return redirect()->intended(route('karyawan.dashboard'));
        }

        return redirect()->route('login')->withErrors(['login' => 'Login gagal, periksa kembali kredensial Anda.']);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Logout dari kedua guard
        Auth::guard('web')->logout();
        Auth::guard('karyawan')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
