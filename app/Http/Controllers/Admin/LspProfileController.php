<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LspProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LspProfileController extends Controller
{
    public function edit(): View
    {
        $profile = LspProfile::firstOrNew();

        return view('admin.master.lsp-profile', compact('profile'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lsp' => ['required', 'string', 'max:255'],
            'kode_lsp' => ['nullable', 'string', 'max:50'],
            'no_lisensi_bnsp' => ['required', 'string', 'max:100'],
            'alamat' => ['nullable', 'string'],
            'telepon' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'nama_ketua' => ['required', 'string', 'max:255'],
            'no_sk_lisensi' => ['nullable', 'string', 'max:100'],
            'masa_berlaku_lisensi' => ['nullable', 'date'],
            'kop_surat' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'ttd_ketua' => ['nullable', 'image', 'max:2048'],
        ]);

        $profile = LspProfile::firstOrNew();

        if ($request->hasFile('logo')) {
            if ($profile->logo) {
                Storage::disk('public')->delete($profile->logo);
            }
            $validated['logo'] = $request->file('logo')->store('lsp', 'public');
        }

        if ($request->hasFile('ttd_ketua')) {
            if ($profile->ttd_ketua) {
                Storage::disk('public')->delete($profile->ttd_ketua);
            }
            $validated['ttd_ketua'] = $request->file('ttd_ketua')->store('lsp', 'public');
        }

        $profile->fill($validated)->save();

        return back()->with('success', 'Profil Lembaga Sertifikasi Profesi (LSP) berhasil diperbarui.');
    }
}
