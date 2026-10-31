@extends('errors.layout')

@section('title', '419 - Sesi Kedaluwarsa')
@section('code', '419')
@section('code-badge-classes', 'bg-amber-50 text-amber-700 border border-amber-200')
@section('icon-bg', 'bg-amber-50 text-amber-600 border border-amber-100')

@section('icon')
<svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
</svg>
@endsection

@section('headline', 'Sesi Halaman Berakhir')

@section('message')
Sesi keamanan (*CSRF Token*) Anda telah kedaluwarsa karena halaman dibiarkan terbuka terlalu lama tanpa aktivitas.
@endsection

@section('action-details')
<p class="font-medium text-slate-700 mb-1">Langkah Penyelesaian:</p>
<ul class="list-disc list-inside space-y-0.5 text-slate-500">
    <li>Silakan kembali ke halaman sebelumnya dan muat ulang (*refresh*) halaman.</li>
    <li>Kirim ulang formulir yang sedang Anda kerjakan.</li>
    <li>Bila perlu, silakan masuk (*login*) kembali ke portal LSP PrO Indo.</li>
</ul>
@endsection
