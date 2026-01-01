<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanRequestNotification extends Notification
{
    use Queueable;

    private $peminjaman; // Passed loan object

    /**
     * Create a new notification instance.
     */
    public function __construct($peminjaman)
    {
        $this->peminjaman = $peminjaman;
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
        return [
            'peminjaman_id' => $this->peminjaman->id_peminjaman,
            'title' => 'Permintaan Peminjaman Baru',
            'message' => 'Anggota ' . $this->peminjaman->pengguna->nama . ' mengajukan peminjaman baru.',
            'link' => route('peminjaman.show', $this->peminjaman->id_peminjaman), // Link to Admin details
            'type' => 'info', // For icon styling
        ];
    }
}
