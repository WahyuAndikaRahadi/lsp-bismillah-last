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
    public function index(Request $request): View
    {
        $schemes = Scheme::withCount('questionBanks')
            ->orderBy('code')
            ->get();

        // Build questionBanks collection for package view
        $questionBanks = $schemes->map(function ($scheme) {
            return (object) [
                'id' => $scheme->id,
                'title' => 'Paket Soal Uji Tertulis (CBT) - ' . $scheme->name,
                'name' => 'Paket Soal Uji Tertulis (CBT) - ' . $scheme->name,
                'code' => 'CBT-' . $scheme->code,
                'scheme' => $scheme,
                'type' => 'IA05 (CBT)',
                'questions_count' => $scheme->question_banks_count,
                'is_active' => true,
            ];
        });

        // Filterable questions list
        $query = QuestionBank::with('scheme', 'competencyUnit');

        if ($request->filled('scheme_id')) {
            $query->where('scheme_id', $request->scheme_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('question_text', 'like', '%' . $request->search . '%')
                  ->orWhere('option_a', 'like', '%' . $request->search . '%')
                  ->orWhere('option_b', 'like', '%' . $request->search . '%')
                  ->orWhere('option_c', 'like', '%' . $request->search . '%')
                  ->orWhere('option_d', 'like', '%' . $request->search . '%');
            });
        }

        $questions = $query->latest()->paginate(15)->withQueryString();
        $totalQuestions = QuestionBank::count();
        $selectedScheme = $request->filled('scheme_id') ? Scheme::find($request->scheme_id) : null;

        return view('admin.question-banks.index', compact(
            'questionBanks',
            'questions',
            'schemes',
            'totalQuestions',
            'selectedScheme'
        ));
    }

    public function show(QuestionBank $questionBank): View
    {
        $questionBank->load('scheme', 'competencyUnit');

        return view('admin.question-banks.show', compact('questionBank'));
    }

    public function create(Request $request): View
    {
        $schemes = Scheme::active()->with('competencyUnits')->get();
        $selectedSchemeId = $request->query('scheme_id');

        return view('admin.question-banks.create', compact('schemes', 'selectedSchemeId'));
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

        $qb = QuestionBank::create($validated);

        return redirect()->route('admin.question-banks.index', ['scheme_id' => $qb->scheme_id])
            ->with('success', 'Butir soal CBT berhasil ditambahkan.');
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
            ->with('success', 'Butir soal CBT berhasil diperbarui.');
    }

    public function destroy(QuestionBank $questionBank): RedirectResponse
    {
        $schemeId = $questionBank->scheme_id;
        $questionBank->delete();

        return redirect()->route('admin.question-banks.index', ['scheme_id' => $schemeId])
            ->with('success', 'Butir soal CBT berhasil dihapus.');
    }
}
