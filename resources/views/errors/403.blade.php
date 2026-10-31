@extends('errors.layout')

@section('title', '403 - Akses Ditolak')
@section('code', '403')
@section('code-badge-classes', 'bg-rose-50 text-rose-700 border border-rose-200')
@section('icon-bg', 'bg-rose-50 text-lsp-red border border-rose-100')

@section('icon')
<svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
</svg>
@endsection

@section('headline', 'Akses Tidak Diizinkan')

@section('message')
{{ $exception->getMessage() ?: 'Anda tidak memiliki hak akses atau wewenang untuk membuka halaman atau menjalankan tindakan ini.' }}
@endsection

@section('action-details')
<p class="font-medium text-slate-700 mb-1">Panduan Pengguna:</p>
<ul class="list-disc list-inside space-y-0.5 text-slate-500">
    <li>Pastikan Anda telah masuk dengan peran (*role*) yang berwenang (Admin, Asesor, atau Asesi).</li>
    <li>Jika Anda seorang Asesor, pastikan Anda ditugaskan pada jadwal asesmen yang bersangkutan.</li>
    <li>Hubungi Admin LSP jika Anda merasa memerlukan otorisasi tambahan untuk akun ini.</li>
</ul>
@endsection
