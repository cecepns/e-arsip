<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Bagian;

class NotificationService
{
    /**
     * Send notification to specific user.
     */
    public function sendToUser(User $user, string $type, string $title, string $message, array $data = []): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Send notification to all users in a bagian.
     */
    public function sendToBagian(Bagian $bagian, string $type, string $title, string $message, array $data = []): void
    {
        $users = $bagian->users;
        
        foreach ($users as $user) {
            $this->sendToUser($user, $type, $title, $message, $data);
        }
    }

    /**
     * Send notification to all users except the sender.
     */
    public function sendToAllExcept(User $sender, string $type, string $title, string $message, array $data = []): void
    {
        $users = User::where('id', '!=', $sender->id)->get();
        
        foreach ($users as $user) {
            $this->sendToUser($user, $type, $title, $message, $data);
        }
    }

    /**
     * Send surat masuk notification.
     */
    public function sendSuratMasukNotification($suratMasuk, $excludeBagianIds = []): void
    {
        $title = 'Surat Masuk Baru';
        $message = "Surat masuk dengan nomor {$suratMasuk->nomor_surat} telah ditambahkan";
        
        $data = [
            'surat_id' => $suratMasuk->id,
            'nomor_surat' => $suratMasuk->nomor_surat,
            'perihal' => $suratMasuk->perihal,
            'pengirim' => $suratMasuk->pengirim,
            'tanggal_surat' => $suratMasuk->tanggal_surat,
        ];

        // Send to all users except the creator and users in excluded bagian
        $users = User::where('id', '!=', $suratMasuk->user_id);
        
        if (!empty($excludeBagianIds)) {
            $users->whereNotIn('bagian_id', $excludeBagianIds);
        }
        
        $users = $users->get();
        
        foreach ($users as $user) {
            $this->sendToUser($user, 'surat_masuk', $title, $message, $data);
        }
    }

    /**
     * Send surat keluar notification.
     */
    public function sendSuratKeluarNotification($suratKeluar): void
    {
        $title = 'Surat Keluar Baru';
        $message = "Surat keluar dengan nomor {$suratKeluar->nomor_surat} telah ditambahkan";
        
        $data = [
            'surat_id' => $suratKeluar->id,
            'nomor_surat' => $suratKeluar->nomor_surat,
            'perihal' => $suratKeluar->perihal,
            'penerima' => $suratKeluar->penerima,
            'tanggal_surat' => $suratKeluar->tanggal_surat,
        ];

        // Send to all users except the creator
        $this->sendToAllExcept($suratKeluar->user, 'surat_keluar', $title, $message, $data);
    }

    /**
     * Send disposisi notification.
     */
    public function sendDisposisiNotification($disposisi): void
    {
        $title = 'Disposisi Baru';
        $message = "Disposisi untuk surat {$disposisi->suratMasuk->nomor_surat} telah dibuat";
        
        $data = [
            'disposisi_id' => $disposisi->id,
            'surat_id' => $disposisi->surat_masuk_id,
            'nomor_surat' => $disposisi->suratMasuk->nomor_surat,
            'perihal' => $disposisi->suratMasuk->perihal,
            'dari_bagian' => $disposisi->dariBagian->nama ?? 'Tidak diketahui',
            'ke_bagian' => $disposisi->keBagian->nama ?? 'Tidak diketahui',
            'tanggal_disposisi' => $disposisi->tanggal_disposisi,
        ];

        // Send to users in the target bagian
        if ($disposisi->keBagian) {
            $this->sendToBagian($disposisi->keBagian, 'disposisi', $title, $message, $data);
        }
    }
}
