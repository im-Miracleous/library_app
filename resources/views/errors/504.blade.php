@extends('errors.layout')

@section('title', '504 Gateway Timeout')
@section('code', '504')
@section('message_title', 'Gateway Timeout')

@section('message')
    Server membutuhkan waktu terlalu lama untuk merespons.
    Silakan periksa koneksi internet Anda atau coba muat ulang halaman.
@endsection

@section('image')
    <div
        class="w-24 h-24 rounded-full flex items-center justify-center bg-white dark:bg-slate-800 border-4 border-cyan-100 dark:border-cyan-900/30 shadow-xl relative z-10 mx-auto">
        <span class="material-symbols-outlined text-[6rem] text-cyan-500 dark:text-cyan-400">timer_off</span>
    </div>
@endsection
