<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->dashboardRoute());
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        return redirect()->intended(route($user->dashboardRoute()))
            ->with('success', 'Selamat datang kembali, ' . ($user->full_name ?: $user->name) . '!');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berhasil keluar dari sistem.');
    }

    public function proxySwitchBack(Request $request): RedirectResponse
    {
        if ($originalAdminId = session('admin_impersonator_id')) {
            session()->forget('admin_impersonator_id');
            $admin = \App\Models\User::find($originalAdminId);
            if ($admin && ($admin->isAdmin() || $admin->isRole('admin') || in_array($admin->role, ['SUPER_ADMIN', 'ADMIN_LSP'], true))) {
                $request->session()->regenerate();
                Auth::login($admin);

                return redirect()->route('admin.proxy.index')->with('success', 'Kembali ke sesi Admin.');
            }
        }

        return redirect()->route('login');
    }
}
