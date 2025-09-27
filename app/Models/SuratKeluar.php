<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratKeluar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'surat_keluar';

    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'tanggal_keluar',
        'perihal',
        'ringkasan_isi',
        'tujuan',
        'keterangan',
        'pengirim_bagian_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
            'tanggal_keluar' => 'date',
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
