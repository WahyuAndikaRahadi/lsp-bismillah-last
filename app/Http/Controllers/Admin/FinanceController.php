<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\CertificationScheme;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceController extends Controller
{
    public function bankAccounts(): View
    {
        $accounts = BankAccount::latest()->get();

        return view('admin.finance.bank_accounts', [
            'accounts' => $accounts,
        ]);
    }

    public function storeBankAccount(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bank_name' => ['required', 'string'],
            'account_number' => ['required', 'string'],
            'account_holder' => ['required', 'string'],
            'branch' => ['nullable', 'string'],
        ]);

        BankAccount::create($validated);

        return redirect()->route('admin.finance.bank-accounts.index')
            ->with('success', 'Rekening bank LSP berhasil ditambahkan.');
    }

    public function pricing(): View
    {
        $schemes = CertificationScheme::orderBy('kode_skema')->get();

        return view('admin.finance.pricing', [
            'schemes' => $schemes,
        ]);
    }

    public function transactions(Request $request): View
    {
        $transactions = FinancialTransaction::with('account')->latest('transaction_date')->paginate(20);
        $accounts = FinancialAccount::orderBy('account_code')->get();

        return view('admin.finance.transactions', [
            'transactions' => $transactions,
            'accounts' => $accounts,
        ]);
    }

    public function storeTransaction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'account_id' => ['required', 'exists:financial_accounts,id'],
            'transaction_date' => ['required', 'date'],
            'description' => ['required', 'string'],
            'type' => ['required', 'in:pemasukan,pengeluaran'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        FinancialTransaction::create($validated);

        return redirect()->route('admin.finance.transactions.index')
            ->with('success', 'Transaksi keuangan berhasil dicatat.');
    }

    public function balanceSheet(): View
    {
        $totalPemasukan = FinancialTransaction::where('type', 'pemasukan')->sum('amount');
        $totalPengeluaran = FinancialTransaction::where('type', 'pengeluaran')->sum('amount');
        $saldoKas = $totalPemasukan - $totalPengeluaran;

        return view('admin.finance.balance_sheet', [
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'saldoKas' => $saldoKas,
        ]);
    }

    public function incomeStatement(): View
    {
        $pendapatan = FinancialTransaction::where('type', 'pemasukan')->sum('amount');
        $beban = FinancialTransaction::where('type', 'pengeluaran')->sum('amount');
        $surplus = $pendapatan - $beban;

        return view('admin.finance.income_statement', [
            'pendapatan' => $pendapatan,
            'beban' => $beban,
            'surplus' => $surplus,
        ]);
    }
}
