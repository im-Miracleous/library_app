<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index()
    {
        $pendingVerificationCount = 0;
        if (auth()->check() && in_array(auth()->user()->peran, ['admin', 'petugas', 'owner'])) {
            $pendingVerificationCount = \App\Models\Peminjaman::where('status_transaksi', 'menunggu_verifikasi')->count();
        }

        $notifications = auth()->user()->notifications()->paginate(10);
        return view('notifikasi.index', compact('notifications', 'pendingVerificationCount'));
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        // Redirect logic with dynamic route regeneration
        if (isset($notification->data['peminjaman_id'])) {
            $routeName = (auth()->user()->peran === 'anggota') ? 'member.peminjaman.show' : 'peminjaman.show';
            return redirect()->route($routeName, $notification->data['peminjaman_id']);
        }

        if (isset($notification->data['link'])) {
            return redirect($notification->data['link']);
        }

        return back();
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
