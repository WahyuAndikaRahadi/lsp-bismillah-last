<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AsesorProfile;
use App\Models\CertificationScheme;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AsesorManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = AsesorProfile::query()->with(['user', 'schemes']);

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('no_met', 'like', "%{$q}%")
                    ->orWhere('no_blangko', 'like', "%{$q}%")
                    ->orWhere('no_sertifikat', 'like', "%{$q}%")
                    ->orWhere('bidang_keahlian', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($u) use ($q) {
                        $u->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%")
                            ->orWhere('nik', 'like', "%{$q}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $asesors = $query->latest()->paginate(10)->withQueryString();
        $schemes = CertificationScheme::where('is_active', true)->get();

        return view('admin.master.asesors.index', compact('asesors', 'schemes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'nik' => ['required', 'string', 'size:16', 'unique:users,nik'],
            'phone' => ['required', 'string', 'max:25'],
            'password' => ['required', 'string', 'min:8'],
            'no_met' => ['required', 'string', 'max:50', 'unique:asesor_profiles,no_met'],
            'no_blangko' => ['nullable', 'string', 'max:50'],
            'no_sertifikat' => ['nullable', 'string', 'max:100'],
            'masa_berlaku' => ['nullable', 'date'],
            'bidang_keahlian' => ['nullable', 'string', 'max:255'],
            'scheme_ids' => ['nullable', 'array'],
            'scheme_ids.*' => ['exists:certification_schemes,id'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $user = User::create([
                'name' => trim($validated['name']),
                'email' => strtolower(trim($validated['email'])),
                'nik' => trim($validated['nik']),
                'phone' => trim($validated['phone']),
                'role' => UserRole::Asesor,
                'password' => Hash::make($validated['password']),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            $profile = AsesorProfile::create([
                'user_id' => $user->id,
                'no_met' => trim($validated['no_met']),
                'no_blangko' => $validated['no_blangko'] ?? null,
                'no_sertifikat' => $validated['no_sertifikat'] ?? null,
                'masa_berlaku' => $validated['masa_berlaku'] ?? null,
                'bidang_keahlian' => $validated['bidang_keahlian'] ?? null,
                'is_active' => true,
            ]);

            if ($request->filled('scheme_ids')) {
                $profile->schemes()->sync($request->scheme_ids);
            }
        });

        return redirect()->route('admin.asesors.index')
            ->with('success', 'Akun dan profil asesor kompetensi berhasil didaftarkan.');
    }

    public function update(Request $request, AsesorProfile $asesor): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $asesor->user_id],
            'nik' => ['required', 'string', 'size:16', 'unique:users,nik,' . $asesor->user_id],
            'phone' => ['required', 'string', 'max:25'],
            'no_met' => ['required', 'string', 'max:50', 'unique:asesor_profiles,no_met,' . $asesor->id],
            'no_blangko' => ['nullable', 'string', 'max:50'],
            'no_sertifikat' => ['nullable', 'string', 'max:100'],
            'masa_berlaku' => ['nullable', 'date'],
            'bidang_keahlian' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
            'scheme_ids' => ['nullable', 'array'],
            'scheme_ids.*' => ['exists:certification_schemes,id'],
        ]);

        DB::transaction(function () use ($validated, $request, $asesor) {
            $asesor->user->update([
                'name' => trim($validated['name']),
                'email' => strtolower(trim($validated['email'])),
                'nik' => trim($validated['nik']),
                'phone' => trim($validated['phone']),
                'is_active' => $request->boolean('is_active'),
            ]);

            $asesor->update([
                'no_met' => trim($validated['no_met']),
                'no_blangko' => $validated['no_blangko'] ?? null,
                'no_sertifikat' => $validated['no_sertifikat'] ?? null,
                'masa_berlaku' => $validated['masa_berlaku'] ?? null,
                'bidang_keahlian' => $validated['bidang_keahlian'] ?? null,
                'is_active' => $request->boolean('is_active'),
            ]);

            if ($request->has('scheme_ids')) {
                $asesor->schemes()->sync($request->scheme_ids ?? []);
            }
        });

        return back()->with('success', 'Data asesor kompetensi berhasil diperbarui.');
    }

    public function destroy(AsesorProfile $asesor): RedirectResponse
    {
        $asesor->user->delete();

        return back()->with('success', 'Data asesor kompetensi dan akun terkait berhasil dihapus.');
    }
}
