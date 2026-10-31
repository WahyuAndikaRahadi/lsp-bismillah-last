@extends('errors.layout')

@section('title', '500 - Kesalahan Server')
@section('code', '500')
@section('code-badge-classes', 'bg-red-50 text-lsp-red border border-red-200')
@section('icon-bg', 'bg-red-50 text-lsp-red border border-red-100')

@section('icon')
<svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
</svg>
@endsection

@section('headline', 'Terjadi Kesalahan Server')

@section('message')
Sistem mengalami kendala saat memproses permintaan Anda. Tim teknis LSP telah mencatat kendala ini untuk segera ditangani.
@endsection

@section('action-details')
<p class="font-medium text-slate-700 mb-1">Langkah yang dapat Anda coba:</p>
<ul class="list-disc list-inside space-y-0.5 text-slate-500">
    <li>Muat ulang (*refresh*) halaman beberapa saat lagi.</li>
    <li>Periksa kembali kelengkapan formulir sebelum mengirim ulang data.</li>
    <li>Jika kendala berlanjut, hubungi administrator sistem dengan menyertakan waktu kejadian.</li>
</ul>
@endsection
