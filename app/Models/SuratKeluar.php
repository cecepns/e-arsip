<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class SuratKeluar extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'surat_keluar';

    protected $fillable = [
        'nomor_surat',
        'tanggal_surat',
        'tanggal_keluar',
        'perihal',
        'ringkasan_isi',
        'tujuan',
        'sifat_surat',
        'keterangan',
        'pengirim_bagian_id',
        'user_id',
        'created_by',
        'updated_by',
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
     * ANCHOR: Get the lampiran for the surat keluar.
     */
    public function lampiran()
    {
        return $this->hasMany(Lampiran::class, 'surat_id')->where('tipe_surat', 'keluar');
    }

    /**
     * ANCHOR: Get the disposisi for the surat keluar.
     */
    public function disposisi()
    {
        return $this->hasMany(Disposisi::class, 'surat_keluar_id');
    }
}
