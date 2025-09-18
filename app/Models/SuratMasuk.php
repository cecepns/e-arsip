<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuratMasuk extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'tanggal_terima',
        'perihal',
        'ringkasan_isi',
        'pengirim',
        'sifat_surat',
        'keterangan',
        'tujuan_bagian_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
            'tanggal_terima' => 'date',
        ];
    }

    /**
     * Get the bagian that owns the surat masuk.
     */
    public function tujuanBagian()
    {
        return $this->belongsTo(Bagian::class, 'tujuan_bagian_id');
    }

    /**
     * Get the user that created the surat masuk.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the disposisi for the surat masuk.
     */
    public function disposisi()
    {
        return $this->hasMany(Disposisi::class);
    }

    /**
     * Get the lampiran for the surat masuk.
     */
    public function lampiran()
    {
        return $this->hasMany(Lampiran::class, 'surat_id')->where('tipe_surat', 'masuk');
    }
}
