@extends('errors.layout')

@section('title', '500 Server Error')
@section('code', '500')
@section('message_title', 'Server Bermasalah')

@section('message')
    Maaf, terjadi kesalahan pada server kami. Masalah ini sedang kami tangani.
    Silakan coba muat ulang halaman atau kembali lagi nanti.
@endsection

@section('image')
    <div
        class="w-24 h-24 rounded-full flex items-center justify-center bg-white dark:bg-slate-800 border-4 border-purple-100 dark:border-purple-900/30 shadow-xl relative z-10 mx-auto">
        <span class="material-symbols-outlined text-[6rem] text-purple-500 dark:text-purple-400">dns</span>
    </div>
@endsection
