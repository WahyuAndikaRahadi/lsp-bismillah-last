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
        $currentProfile = $user->profile;
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

            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9]+$/'],
                'nik' => ['required', 'string', 'max:16', 'regex:/^[0-9]+$/'],
                'tempat_lahir' => 'required|string|max:100',
                'tanggal_lahir' => 'required|date',
                'jenis_kelamin' => 'required|in:L,P',
                'kebangsaan' => 'required|string|max:50',
                'pendidikan_terakhir' => 'required|string|max:50',
                'alamat_rumah' => 'required|string',
                'kode_pos' => ['nullable', 'string', 'max:10', 'regex:/^[0-9]+$/'],
                'no_telepon_rumah' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
                // Data pekerjaan: hanya jabatan yang wajib
                'jabatan' => 'required|string|max:100',
                'nama_institusi_perusahaan' => 'nullable|string|max:255',
                'alamat_kantor' => 'nullable|string',
                'no_telepon_kantor' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
                'signature_data' => $hasExistingSignature
                    ? ['nullable', 'string', new ValidSignature]
                    : ['required', 'string', new ValidSignature],
            ], [
                'full_name.required' => 'Nama lengkap sesuai KTP wajib diisi.',
                'phone.required' => 'Nomor HP / WhatsApp wajib diisi.',
                'phone.regex' => 'Nomor HP hanya boleh berupa angka (integer).',
                'nik.required' => 'Nomor Induk Kependudukan (NIK) wajib diisi.',
                'nik.regex' => 'NIK hanya boleh berupa deretan angka (integer).',
                'tempat_lahir.required' => 'Tempat lahir (kota/kabupaten) wajib dipilih.',
                'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
                'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
                'kebangsaan.required' => 'Kebangsaan wajib diisi.',
                'pendidikan_terakhir.required' => 'Pendidikan terakhir wajib dipilih.',
                'alamat_rumah.required' => 'Alamat rumah tinggal wajib diisi.',
                'kode_pos.regex' => 'Kode Pos hanya boleh berupa angka (integer).',
                'no_telepon_rumah.regex' => 'Telepon rumah hanya boleh berupa angka (integer).',
                'jabatan.required' => 'Jabatan / posisi / status pekerjaan saat ini wajib dipilih.',
                'no_telepon_kantor.regex' => 'Telepon kantor hanya boleh berupa angka (integer).',
                'signature_data.required' => 'Tanda tangan digital pemohon (asesi) wajib diisi sebelum menyimpan data APL.01.',
            ]);

            if (empty($validated['signature_data']) && $hasExistingSignature) {
                $validated['signature_data'] = $currentProfile->signature_data;
            }
        } else {
            // Partial update (e.g. testing signature or isolated fields)
            $validated = $request->validate([
                'nik' => 'nullable|string|regex:/^[0-9]+$/|max:20',
                'tempat_lahir' => 'nullable|string|max:100',
                'tanggal_lahir' => 'nullable|date',
                'jenis_kelamin' => 'nullable|in:L,P',
                'kebangsaan' => 'nullable|string|max:50',
                'alamat_rumah' => 'nullable|string',
                'kode_pos' => 'nullable|string|regex:/^[0-9]+$/|max:10',
                'no_telepon_rumah' => 'nullable|string|regex:/^[0-9]+$/|max:30',
                'no_telepon_kantor' => 'nullable|string|regex:/^[0-9]+$/|max:30',
                'pendidikan_terakhir' => 'nullable|string|max:50',
                'nama_institusi_perusahaan' => 'nullable|string|max:255',
                'jabatan' => 'nullable|string|max:100',
                'alamat_kantor' => 'nullable|string',
                'signature_data' => ['nullable', 'string', new ValidSignature],
                'full_name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|regex:/^[0-9]+$/|max:30',
            ], [
                'nik.regex' => 'NIK hanya boleh berupa angka (integer).',
                'kode_pos.regex' => 'Kode Pos hanya boleh berupa angka (integer).',
                'phone.regex' => 'Nomor HP hanya boleh berupa angka (integer).',
                'no_telepon_rumah.regex' => 'Telepon rumah hanya boleh berupa angka (integer).',
                'no_telepon_kantor.regex' => 'Telepon kantor hanya boleh berupa angka (integer).',
            ]);
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
