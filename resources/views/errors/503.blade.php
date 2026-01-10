@extends('errors.layout')

@section('title', '503 Service Unavailable')
@section('code', '503')
@section('message_title', 'Layanan Tidak Tersedia')

@section('message')
    Sistem saat ini sedang dalam pemeliharaan (maintenance) untuk meningkatkan performa.
    Kami akan segera kembali. Terima kasih atas kesabaran Anda.
@endsection

@section('image')
    <div
        class="w-24 h-24 rounded-full flex items-center justify-center bg-white dark:bg-slate-800 border-4 border-blue-100 dark:border-blue-900/30 shadow-xl relative z-10 mx-auto">
        <span class="material-symbols-outlined text-[6rem] text-blue-500 dark:text-blue-400">engineering</span>
    </div>
@endsection
