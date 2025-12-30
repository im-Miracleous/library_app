<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PeminjamanJatuhTempo extends Notification
{
    use Queueable;

    protected $peminjaman;

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
            'title' => 'Peminjaman Jatuh Tempo',
            'message' => "Peminjaman #{$this->peminjaman->id_peminjaman} telah melewati tanggal jatuh tempo.",
            'link' => route('peminjaman.show', $this->peminjaman->id_peminjaman),
            'type' => 'warning',
            'peminjaman_id' => $this->peminjaman->id_peminjaman,
        ];
    }
}
