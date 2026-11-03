<?php

namespace App\Http\Controllers\Asesi;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use App\Rules\ValidSignature;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $profile = auth()->user()->profile;

        return view('asesi.profile', compact('profile'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $currentProfile = UserProfile::where('user_id', $user->id)->first();
        $hasExistingSignature = ! empty($currentProfile?->signature_data);

        // Check if full profile form was submitted by checking presence of profile form fields
        $isFullProfileForm = $request->exists('nik') || $request->exists('alamat_rumah');

        if ($isFullProfileForm) {
            // Pre-fill full_name and phone from $user if not passed in request
            if (! $request->filled('full_name') && ! empty($user->full_name)) {
                $request->merge(['full_name' => $user->full_name]);
            }
            if (! $request->filled('phone') && ! empty($user->phone)) {
                $request->merge(['phone' => $user->phone]);
            }

            $identityType = $request->input('identity_type', $currentProfile?->identity_type ?? 'KTP');

            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'phone' => ['required', 'string', 'max:30', 'regex:/^[\+]?[0-9\s\-\(\)]+$/'],
                'identity_type' => 'nullable|string|in:KTP,PASPOR',
                'nik' => [
                    'required',
                    'string',
                    'max:30',
                    function ($attribute, $value, $fail) use ($identityType) {
                        if ($identityType === 'PASPOR') {
                            if (! preg_match('/^[A-Za-z0-9]+$/', $value)) {
                                $fail('Nomor paspor hanya boleh berupa kombinasi huruf dan angka.');
                            }
                        } else {
                            if (! preg_match('/^[0-9]{16}$/', $value)) {
                                $fail('NIK KTP harus berupa 16 digit angka.');
                            }
                        }
                    },
                ],
                'tempat_lahir' => 'required|string|max:100',
                'tanggal_lahir' => 'required|date',
                'jenis_kelamin' => 'required|in:L,P',
                'kebangsaan' => 'required|string|max:50',
                'pendidikan_terakhir' => 'required|string|max:50',
                'alamat_rumah' => 'required|string',
                'kode_pos' => ['nullable', 'string', 'max:20', 'regex:/^[A-Za-z0-9\s\-]+$/'],
                'no_telepon_rumah' => ['nullable', 'string', 'max:30', 'regex:/^[\+]?[0-9\s\-\(\)]+$/'],
                'status_pekerjaan' => 'nullable|string|max:100',
                'jabatan' => 'required|string|max:100',
                'nama_institusi_perusahaan' => 'nullable|string|max:255',
                'alamat_kantor' => 'nullable|string',
                'kode_pos_kantor' => ['nullable', 'string', 'max:20'],
                'no_telepon_kantor' => ['nullable', 'string', 'max:30', 'regex:/^[\+]?[0-9\s\-\(\)]+$/'],
                'no_fax_kantor' => ['nullable', 'string', 'max:30', 'regex:/^[\+]?[0-9\s\-\(\)]+$/'],
                'email_kantor' => ['nullable', 'email', 'max:255'],
                'signature_data' => $hasExistingSignature
                    ? ['nullable', 'string', new ValidSignature]
                    : ['required', 'string', new ValidSignature],
            ], [
                'full_name.required' => 'Nama lengkap sesuai kartu identitas wajib diisi.',
                'phone.required' => 'Nomor HP / WhatsApp wajib diisi.',
                'phone.regex' => 'Format nomor HP tidak valid.',
                'nik.required' => 'Nomor identitas (NIK/Paspor) wajib diisi.',
                'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
                'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
                'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
                'kebangsaan.required' => 'Kebangsaan wajib diisi.',
                'pendidikan_terakhir.required' => 'Pendidikan terakhir wajib dipilih.',
                'alamat_rumah.required' => 'Alamat rumah tinggal wajib diisi.',
                'jabatan.required' => 'Jabatan / posisi / status pekerjaan saat ini wajib diisi.',
                'signature_data.required' => 'Tanda tangan digital pemohon (asesi) wajib diisi sebelum menyimpan data APL.01.',
            ]);

            $validated['identity_type'] = $identityType;

            // Handle signature versioning
            if (empty($validated['signature_data']) && $hasExistingSignature) {
                $validated['signature_data'] = $currentProfile->signature_data;
                $validated['signature_version'] = $currentProfile->signature_version ?? 1;
            } elseif (! empty($validated['signature_data'])) {
                if ($currentProfile && $currentProfile->signature_data === $validated['signature_data']) {
                    $validated['signature_version'] = $currentProfile->signature_version ?? 1;
                } else {
                    $validated['signature_version'] = ($currentProfile->signature_version ?? 0) + 1;
                }
            }
        } else {
            // Partial update (e.g. testing signature or isolated fields)
            $validated = $request->validate([
                'identity_type' => 'nullable|string|in:KTP,PASPOR',
                'nik' => 'nullable|string|max:30',
                'tempat_lahir' => 'nullable|string|max:100',
                'tanggal_lahir' => 'nullable|date',
                'jenis_kelamin' => 'nullable|in:L,P',
                'kebangsaan' => 'nullable|string|max:50',
                'alamat_rumah' => 'nullable|string',
                'kode_pos' => 'nullable|string|max:20',
                'no_telepon_rumah' => 'nullable|string|max:30',
                'no_telepon_kantor' => 'nullable|string|max:30',
                'pendidikan_terakhir' => 'nullable|string|max:50',
                'nama_institusi_perusahaan' => 'nullable|string|max:255',
                'jabatan' => 'nullable|string|max:100',
                'alamat_kantor' => 'nullable|string',
                'signature_data' => ['nullable', 'string', new ValidSignature],
                'full_name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:30',
            ]);

            if (! empty($validated['signature_data'])) {
                if ($currentProfile && $currentProfile->signature_data === $validated['signature_data']) {
                    $validated['signature_version'] = $currentProfile->signature_version ?? 1;
                } else {
                    $validated['signature_version'] = ($currentProfile->signature_version ?? 0) + 1;
                }
            }
        }

        if (! empty($validated['full_name']) || ! empty($validated['phone'])) {
            $user->update(array_filter([
                'full_name' => $validated['full_name'] ?? null,
                'phone' => $validated['phone'] ?? null,
            ]));
        }

        $profileData = collect($validated)->except(['full_name', 'phone'])->toArray();

        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return redirect()->route('asesi.profile.edit')->with('success', 'Data diri dan tanda tangan APL.01 berhasil disimpan.');
    }
}
