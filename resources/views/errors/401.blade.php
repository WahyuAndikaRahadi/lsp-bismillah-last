@extends('errors.layout')

@section('title', '401 - Belum Terautentikasi')
@section('code', '401')
@section('code-badge-classes', 'bg-blue-50 text-lsp-navy border border-blue-200')
@section('icon-bg', 'bg-blue-50 text-lsp-navy border border-blue-100')

@section('icon')
<svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
</svg>
@endsection

@section('headline', 'Diperlukan Autentikasi')

@section('message')
Anda harus masuk (*login*) terlebih dahulu untuk dapat mengakses fitur dan layanan di halaman ini.
@endsection

@section('action-details')
<p class="font-medium text-slate-700 mb-1">Akses Portal Pengguna:</p>
<ul class="list-disc list-inside space-y-0.5 text-slate-500">
    <li>Gunakan alamat email dan kata sandi yang telah terdaftar.</li>
    <li>Calon asesi baru dapat mendaftar melalui formulir pendaftaran mandiri.</li>
</ul>
@endsection
