<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LoanStatusNotification extends Notification
{
    use Queueable;

    private $peminjaman;
    private $status; // 'disetujui' or 'ditolak'

    /**
     * Create a new notification instance.
     */
    public function __construct($peminjaman, $status)
    {
        $this->peminjaman = $peminjaman;
        $this->status = $status;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $statusLabel = $this->status === 'disetujui' ? 'Disetujui' : 'Ditolak';
        $type = $this->status === 'disetujui' ? 'success' : 'warning';
        $icon = $this->status === 'disetujui' ? 'check_circle' : 'cancel';

        return [
            'peminjaman_id' => $this->peminjaman->id_peminjaman,
            'title' => 'Status Peminjaman: ' . $statusLabel,
            'message' => 'Pengajuan peminjaman Anda (' . $this->peminjaman->id_peminjaman . ') telah ' . $this->status . '.',
            'link' => route('member.peminjaman.show', $this->peminjaman->id_peminjaman),
            'type' => $type,
            'icon' => $icon,
        ];
    }
}
