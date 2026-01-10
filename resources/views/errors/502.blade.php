@extends('errors.layout')

@section('title', '502 Bad Gateway')
@section('code', '502')
@section('message_title', 'Bad Gateway')

@section('message')
    Server menerima respons yang tidak valid dari server upstream.
    Silakan coba muat ulang halaman atau kembali lagi nanti.
@endsection

@section('image')
    <div
        class="w-24 h-24 rounded-full flex items-center justify-center bg-white dark:bg-slate-800 border-4 border-indigo-100 dark:border-indigo-900/30 shadow-xl relative z-10 mx-auto">
        <span class="material-symbols-outlined text-[6rem] text-indigo-500 dark:text-indigo-400">router</span>
    </div>
@endsection
