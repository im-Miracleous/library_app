@extends('errors.layout')

@section('title', '403 Forbidden')
@section('code', '403')
@section('message_title', 'Akses Ditolak')

@section('message')
    Maaf, Anda tidak memiliki izin untuk mengakses halaman atau sumber daya ini.
    Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.
@endsection

@section('image')
    <div
        class="w-24 h-24 rounded-full flex items-center justify-center bg-white dark:bg-slate-800 border-4 border-rose-100 dark:border-rose-900/30 shadow-xl relative z-10 mx-auto">
        <span class="material-symbols-outlined text-[6rem] text-rose-500 dark:text-rose-400">lock_person</span>
    </div>
@endsection