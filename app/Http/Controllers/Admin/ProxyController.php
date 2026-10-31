<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProxyController extends Controller
{
    public function index(): View
    {
        $users = User::where('role', '!=', 'SUPER_ADMIN')
            ->orderBy('role')
            ->orderBy('full_name')
            ->paginate(20);

        return view('admin.proxy.index', compact('users'));
    }

    public function impersonate(User $user): RedirectResponse
    {
        // Prevent privilege escalation to SUPER_ADMIN
        if ($user->role === 'SUPER_ADMIN') {
            abort(403, 'Tidak diizinkan melakukan impersonasi ke akun Super Admin.');
        }

        // Prevent self-impersonation
        if ($user->id === auth()->id()) {
            return back()->with('warning', 'Tidak dapat melakukan impersonasi ke akun sendiri.');
        }

        // Prevent nested/chained impersonation
        if (session()->has('admin_impersonator_id')) {
            return back()->with('error', 'Selesaikan sesi impersonasi aktif terlebih dahulu.');
        }

        session(['admin_impersonator_id' => auth()->id()]);

        Auth::login($user);

        return match ($user->role) {
            'ASESOR' => redirect()->route('asesor.jadwal.index'),
            'ASESI' => redirect()->route('asesi.dashboard'),
            default => redirect()->route('admin.dashboard'),
        };
    }
}
