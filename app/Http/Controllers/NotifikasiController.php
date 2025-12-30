<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(10);
        return view('notifikasi.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        // Redirect logic with dynamic route regeneration
        if (isset($notification->data['peminjaman_id'])) {
            // Regenerate route to ensure correct domain/port (fixing localhost vs library_app.test issue)
            return redirect()->route('peminjaman.show', $notification->data['peminjaman_id']);
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
