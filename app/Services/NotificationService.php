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
        $dataWithBagian = $data;
        $dataWithBagian['bagian_id'] = $bagian->id;
        $users = $bagian->users;
        
        foreach ($users as $user) {
            $this->sendToUser($user, $type, $title, $message, $dataWithBagian);
        }
    }

    /**
     * Send notification to all users except the sender.
     */
    public function sendToAllExcept(User $sender, string $type, string $title, string $message, array $data = []): void
    {
        $users = User::where('id', '!=', $sender->id)->get();
        
        foreach ($users as $user) {
            $payload = $data;

            if ($user->role === 'Admin') {
                $payload['bagian_id'] = null;
            }

            $this->sendToUser($user, $type, $title, $message, $payload);
        }
    }

    /**
     * Send surat masuk notification.
     */
    public function sendSuratMasukNotification($suratMasuk, $excludeBagianIds = []): void
    {
        $suratMasuk->loadMissing(['tujuanBagian', 'user.bagian']);

        $title = 'Surat Masuk Baru';
        $message = "Surat masuk dengan nomor {$suratMasuk->nomor_surat} telah ditambahkan";
        
        $data = [
            'surat_id' => $suratMasuk->id,
            'nomor_surat' => $suratMasuk->nomor_surat,
            'perihal' => $suratMasuk->perihal,
            'pengirim' => $suratMasuk->pengirim,
            'tanggal_surat' => $suratMasuk->tanggal_surat,
            'bagian_id' => $suratMasuk->tujuan_bagian_id,
        ];

        $targetBagian = $suratMasuk->tujuanBagian ?? $suratMasuk->user?->bagian;

        if ($targetBagian && !in_array($targetBagian->id, $excludeBagianIds, true)) {
            $this->sendToBagianMembersExceptUser(
                $targetBagian,
                $suratMasuk->user,
                'surat_masuk',
                $title,
                $message,
                $data,
                true
            );
        }

        $this->sendToAdmins('surat_masuk', $title, $message, array_merge($data, ['bagian_id' => null]));
    }

    /**
     * Send surat keluar notification.
     */
    public function sendSuratKeluarNotification($suratKeluar): void
    {
        $suratKeluar->loadMissing(['pengirimBagian', 'user']);

        $title = 'Surat Keluar Baru';
        $message = "Surat keluar dengan nomor {$suratKeluar->nomor_surat} telah ditambahkan";
        
        $data = [
            'surat_id' => $suratKeluar->id,
            'nomor_surat' => $suratKeluar->nomor_surat,
            'perihal' => $suratKeluar->perihal,
            'penerima' => $suratKeluar->penerima,
            'tanggal_surat' => $suratKeluar->tanggal_surat,
            'bagian_id' => $suratKeluar->pengirim_bagian_id,
        ];

        $pengirimBagian = $suratKeluar->pengirimBagian;

        if ($pengirimBagian) {
            $this->sendToBagianMembersExceptUser(
                $pengirimBagian,
                $suratKeluar->user,
                'surat_keluar',
                $title,
                $message,
                $data,
                true
            );
        }

        $this->sendToAdmins('surat_keluar', $title, $message, array_merge($data, ['bagian_id' => null]));
    }

    /**
     * Send disposisi notification.
     */
    public function sendDisposisiNotification($disposisi): void
    {
        // Load necessary relationships
        $disposisi->load(['suratMasuk.tujuanBagian', 'tujuanBagian']);
        
        $title = 'Disposisi Baru';
        $message = "Disposisi untuk surat {$disposisi->suratMasuk->nomor_surat} telah dibuat";
        
        $data = [
            'disposisi_id' => $disposisi->id,
            'surat_id' => $disposisi->surat_masuk_id,
            'nomor_surat' => $disposisi->suratMasuk->nomor_surat,
            'perihal' => $disposisi->suratMasuk->perihal,
            'dari_bagian' => $disposisi->suratMasuk->tujuanBagian->nama_bagian ?? 'Tidak diketahui',
            'ke_bagian' => $disposisi->tujuanBagian->nama_bagian ?? 'Tidak diketahui',
            'tanggal_disposisi' => $disposisi->tanggal_disposisi,
            'bagian_id' => $disposisi->tujuan_bagian_id,
        ];

        // Send to users in the target bagian
        if ($disposisi->tujuanBagian) {
            $this->sendToBagian($disposisi->tujuanBagian, 'disposisi', $title, $message, $data);
        }
    }

    /**
     * ANCHOR: Send notification to admins only.
     * Ensure administrators always receive notifications regardless of bagian.
     */
    private function sendToAdmins(string $type, string $title, string $message, array $data = []): void
    {
        $admins = User::where('role', 'Admin')->get();

        foreach ($admins as $admin) {
            $payload = $data;
            $payload['bagian_id'] = null;

            $this->sendToUser($admin, $type, $title, $message, $payload);
        }
    }

    /**
     * ANCHOR: Send notification to bagian members with optional exclusion of a specific user.
     * Useful to avoid duplicates or include the action initiator when required.
     */
    private function sendToBagianMembersExceptUser(
        Bagian $bagian,
        ?User $excludedUser,
        string $type,
        string $title,
        string $message,
        array $data = [],
        bool $includeExcludedUser = false
    ): void {
        $dataWithBagian = $data;
        $dataWithBagian['bagian_id'] = $bagian->id;

        foreach ($bagian->users as $user) {
            if (!$includeExcludedUser && $excludedUser && $user->id === $excludedUser->id) {
                continue;
            }

            $this->sendToUser($user, $type, $title, $message, $dataWithBagian);
        }
    }
}
