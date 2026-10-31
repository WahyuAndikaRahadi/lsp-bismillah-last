<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\Scheme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionBankController extends Controller
{
    public function index(): View
    {
        $questions = QuestionBank::with('scheme', 'competencyUnit')
            ->latest()
            ->paginate(20);

        return view('admin.question-banks.index', compact('questions'));
    }

    public function show(QuestionBank $questionBank): View
    {
        $questionBank->load('scheme', 'competencyUnit');

        return view('admin.question-banks.show', compact('questionBank'));
    }

    public function create(): View
    {
        $schemes = Scheme::active()->with('competencyUnits')->get();

        return view('admin.question-banks.create', compact('schemes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'scheme_id' => 'required|exists:m_schemes,id',
            'competency_unit_id' => 'nullable|exists:m_competency_units,id',
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D',
        ]);

        QuestionBank::create($validated);

        return redirect()->route('admin.question-banks.index')
            ->with('success', 'Soal berhasil dibuat.');
    }

    public function edit(QuestionBank $questionBank): View
    {
        $schemes = Scheme::active()->with('competencyUnits')->get();

        return view('admin.question-banks.edit', compact('questionBank', 'schemes'));
    }

    public function update(Request $request, QuestionBank $questionBank): RedirectResponse
    {
        $validated = $request->validate([
            'scheme_id' => 'required|exists:m_schemes,id',
            'competency_unit_id' => 'nullable|exists:m_competency_units,id',
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D',
        ]);

        $questionBank->update($validated);

        return redirect()->route('admin.question-banks.show', $questionBank->id)
            ->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(QuestionBank $questionBank): RedirectResponse
    {
        $questionBank->delete();

        return redirect()->route('admin.question-banks.index')
            ->with('success', 'Soal berhasil dihapus.');
    }
}
