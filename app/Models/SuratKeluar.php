<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuratKeluar extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'perihal',
        'ringkasan_isi',
        'tujuan',
        'sifat_surat',
        'keterangan',
        'pengirim_bagian_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
        ];
    }

    /**
     * Get the bagian that owns the surat keluar.
     */
    public function pengirimBagian()
    {
        return $this->belongsTo(Bagian::class, 'pengirim_bagian_id');
    }

    /**
     * Get the user that created the surat keluar.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lampiran for the surat keluar.
     */
    public function lampiran()
    {
        return $this->hasMany(Lampiran::class, 'surat_id')->where('tipe_surat', 'keluar');
    }
}
