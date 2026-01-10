@extends('errors.layout')

@section('title', '404 Not Found')
@section('code', '404')
@section('message_title', 'Halaman Tidak Ditemukan')

@section('message')
    Halaman yang Anda cari mungkin telah dihapus, namanya diganti, atau untuk sementara tidak tersedia.
    Mohon periksa kembali URL yang Anda tuju.
@endsection

@section('image')
    <div
        class="w-24 h-24 rounded-full flex items-center justify-center bg-white dark:bg-slate-800 border-4 border-orange-100 dark:border-orange-900/30 shadow-xl relative z-10 mx-auto">
        <span class="material-symbols-outlined text-[6rem] text-orange-500 dark:text-orange-400">sentiment_dissatisfied</span>
    </div>
@endsection
