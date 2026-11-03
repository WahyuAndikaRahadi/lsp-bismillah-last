<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'password' => ['required', 'string', 'min:6'],
            'role' => 'required|in:SUPER_ADMIN,ADMIN_LSP,ASESOR,ASESI',
            'status' => 'required|in:ACTIVE,INACTIVE,SUSPENDED',
            'assessor_registration_number' => 'nullable|string|max:50',
        ], [
            'phone.regex' => 'Nomor telepon/WhatsApp hanya boleh berupa angka.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $validated['name'] = $validated['full_name'];
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        if (in_array(strtoupper($user->role), ['ASESI', 'asesi'], true)) {
            \App\Models\UserProfile::firstOrCreate(['user_id' => $user->id]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dibuat.');
    }

    public function show(User $user): View
    {
        $user->load('profile');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => 'required|in:SUPER_ADMIN,ADMIN_LSP,ASESOR,ASESI',
            'status' => 'required|in:ACTIVE,INACTIVE,SUSPENDED',
            'assessor_registration_number' => 'nullable|string|max:50',
        ], [
            'phone.regex' => 'Nomor telepon/WhatsApp hanya boleh berupa angka.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $validated['name'] = $validated['full_name'];

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->assessments()->exists() || $user->assessorAssessments()->exists() || $user->assignedSchedules()->exists()) {
            $user->update(['status' => 'INACTIVE']);

            return redirect()->route('admin.users.index')
                ->with('warning', 'Pengguna tidak dapat dihapus permanen karena memiliki riwayat asesmen atau jadwal. Status pengguna telah dinonaktifkan.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
