<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->dashboardRoute());
        }

        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $name = trim($request->name ?? $request->full_name);
        $nik = trim($request->nik);
        $phone = trim($request->phone);
        $email = strtolower(trim($request->email));

        $user = User::create([
            'name' => $name,
            'full_name' => $name,
            'email' => $email,
            'nik' => $nik,
            'phone' => $phone,
            'role' => UserRole::Asesi,
            'status' => 'ACTIVE',
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        // Inisialisasi profil asesi agar langsung terhubung dengan data registrasi
        UserProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'nik' => $nik,
                'no_telepon_rumah' => $phone,
                'kebangsaan' => 'Indonesia',
            ]
        );

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('asesi.dashboard')
            ->with('success', 'Akun calon asesi berhasil dibuat! Selamat datang di portal sertifikasi LSP.');
    }
}
