<?php

namespace App\Http\Controllers\Asesor;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $asesor = $request->user()->asesorProfile;
        if (! $asesor) {
            abort(404, 'Profil asesor tidak ditemukan.');
        }

        $asesor->load('schemes');

        return view('asesor.profile.show', [
            'asesor' => $asesor,
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $asesor = $request->user()->asesorProfile;
        if (! $asesor) {
            abort(404, 'Profil asesor tidak ditemukan.');
        }

        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'nik' => ['required', 'string', 'size:16'],
        ]);

        $asesor->user->update([
            'phone' => $validated['phone'],
            'nik' => $validated['nik'],
        ]);

        return redirect()->route('asesor.profile.show')
            ->with('success', 'Profil asesor berhasil diperbarui.');
    }
}
