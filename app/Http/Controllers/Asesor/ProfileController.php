<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the assessor profile & digital signature edit page.
     */
    public function edit(): View
    {
        $user = auth()->user();
        $profile = $user->profile;

        return view('asesor.profile', compact('user', 'profile'));
    }

    /**
     * Update the assessor profile and signature.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if (! $request->filled('full_name')) {
            $request->merge(['full_name' => $user->full_name ?: $user->name ?: 'Asesor']);
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'assessor_registration_number' => 'nullable|string|max:50',
            'nik' => ['nullable', 'string', 'max:16', 'regex:/^[0-9]+$/'],
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'kebangsaan' => 'nullable|string|max:50',
            'alamat_rumah' => 'nullable|string',
            'kode_pos' => ['nullable', 'string', 'max:10', 'regex:/^[0-9]+$/'],
            'no_telepon_rumah' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'no_telepon_kantor' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'nama_institusi_perusahaan' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:100',
            'alamat_kantor' => 'nullable|string',
            'signature_data' => 'nullable|string',
        ], [
            'nik.regex' => 'NIK hanya boleh berupa deretan angka tanpa spasi atau tanda baca.',
            'kode_pos.regex' => 'Kode pos hanya boleh berupa angka.',
            'phone.regex' => 'Nomor telepon/WhatsApp hanya boleh berupa angka.',
            'no_telepon_rumah.regex' => 'Nomor telepon rumah hanya boleh berupa angka.',
            'no_telepon_kantor.regex' => 'Nomor telepon kantor hanya boleh berupa angka.',
        ]);

        // Update user direct attributes
        $user->update([
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'] ?? $user->phone,
            'assessor_registration_number' => $validated['assessor_registration_number'] ?? $user->assessor_registration_number,
        ]);

        // Separate user profile fields
        $profileData = collect($validated)->except(['full_name', 'phone', 'assessor_registration_number'])->toArray();

        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return redirect()->route('asesor.profile.edit')->with('success', 'Biodata dan tanda tangan asesor berhasil disimpan.');
    }
}
