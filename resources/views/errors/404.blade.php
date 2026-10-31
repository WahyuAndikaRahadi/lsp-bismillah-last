@extends('errors.layout')

@section('title', '404 - Halaman Tidak Ditemukan')
@section('code', '404')
@section('code-badge-classes', 'bg-amber-50 text-amber-700 border border-amber-200')
@section('icon-bg', 'bg-amber-50 text-amber-600 border border-amber-100')

@section('icon')
<svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
</svg>
@endsection

@section('headline', 'Halaman Tidak Ditemukan')

@section('message')
Mohon maaf, tautan atau halaman yang Anda tuju tidak tersedia, telah dipindahkan, atau alamat URL yang dimasukkan kurang tepat.
@endsection

@section('action-details')
<p class="font-medium text-slate-700 mb-1">Kemungkinan penyebab:</p>
<ul class="list-disc list-inside space-y-0.5 text-slate-500">
    <li>Tautan yang Anda klik sudah kedaluwarsa atau tidak aktif.</li>
    <li>Ada kesalahan pengetikan pada alamat URL di bilah browser.</li>
    <li>Data atau jadwal yang dicari sudah dihapus dari sistem.</li>
</ul>
@endsection
