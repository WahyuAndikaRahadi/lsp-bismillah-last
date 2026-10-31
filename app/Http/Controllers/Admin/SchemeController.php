<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scheme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SchemeController extends Controller
{
    public function index(): View
    {
        $schemes = Scheme::withCount('competencyUnits', 'questionBanks')
            ->latest()
            ->paginate(20);

        return view('admin.schemes.index', compact('schemes'));
    }

    public function show(Scheme $scheme): View
    {
        $scheme->load('competencyUnits.elements.kuks');

        return view('admin.schemes.show', compact('scheme'));
    }

    public function create(): View
    {
        return view('admin.schemes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:m_schemes,code',
            'name' => 'required|string|max:255',
            'scheme_type' => 'nullable|string|max:50',
            'version' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'passing_grade_theory' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        Scheme::create($validated);

        return redirect()->route('admin.schemes.index')
            ->with('success', 'Skema berhasil dibuat.');
    }

    public function edit(Scheme $scheme): View
    {
        return view('admin.schemes.edit', compact('scheme'));
    }

    public function update(Request $request, Scheme $scheme): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('m_schemes')->ignore($scheme->id)],
            'name' => 'required|string|max:255',
            'scheme_type' => 'nullable|string|max:50',
            'version' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'passing_grade_theory' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $scheme->update($validated);

        return redirect()->route('admin.schemes.show', $scheme->id)
            ->with('success', 'Skema berhasil diperbarui.');
    }

    public function destroy(Scheme $scheme): RedirectResponse
    {
        if ($scheme->schedules()->exists()) {
            $scheme->update(['is_active' => false]);

            return redirect()->route('admin.schemes.index')
                ->with('warning', 'Skema tidak dapat dihapus permanen karena sudah digunakan dalam jadwal asesmen. Status skema telah dinonaktifkan.');
        }

        $scheme->delete();

        return redirect()->route('admin.schemes.index')
            ->with('success', 'Skema berhasil dihapus.');
    }
}
