<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditInternal;
use App\Models\MutuDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QualityController extends Controller
{
    public function documents(Request $request): View
    {
        $documents = MutuDocument::latest('effective_date')->paginate(15);

        return view('admin.quality.documents', [
            'documents' => $documents,
        ]);
    }

    public function storeDocument(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'document_code' => ['required', 'string', 'unique:mutu_documents,document_code'],
            'title' => ['required', 'string'],
            'category' => ['required', 'string'],
            'version' => ['required', 'string'],
            'effective_date' => ['required', 'date'],
        ]);

        MutuDocument::create($validated);

        return redirect()->route('admin.quality.documents.index')
            ->with('success', 'Dokumen mutu baru berhasil ditambahkan.');
    }

    public function audits(Request $request): View
    {
        $audits = AuditInternal::latest('audit_date')->paginate(15);

        return view('admin.quality.audits', [
            'audits' => $audits,
        ]);
    }

    public function storeAudit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'audit_code' => ['required', 'string', 'unique:audit_internals,audit_code'],
            'audit_date' => ['required', 'date'],
            'auditor_name' => ['required', 'string'],
            'scope' => ['required', 'string'],
            'findings' => ['nullable', 'string'],
        ]);

        AuditInternal::create($validated);

        return redirect()->route('admin.quality.audits.index')
            ->with('success', 'Jadwal audit internal berhasil disimpan.');
    }
}
