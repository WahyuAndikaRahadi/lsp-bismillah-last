<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LetterController extends Controller
{
    public function index(Request $request): View
    {
        $letters = Letter::when($request->filled('category'), function ($q) use ($request) {
            $q->where('category', $request->category);
        })
            ->latest('letter_date')
            ->paginate(15);

        return view('admin.letters.index', [
            'letters' => $letters,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reference_number' => ['required', 'string', 'unique:letters,reference_number'],
            'category' => ['required', 'string'],
            'subject' => ['required', 'string'],
            'recipient' => ['required', 'string'],
            'letter_date' => ['required', 'date'],
            'content' => ['nullable', 'string'],
        ]);

        Letter::create($validated);

        return redirect()->route('admin.letters.index')
            ->with('success', 'Dokumen persuratan berhasil diterbitkan.');
    }
}
