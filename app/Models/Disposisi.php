<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Auditable;

class Disposisi extends Model
{
    use HasFactory, Auditable;

    protected $table = 'disposisi';

    protected $fillable = [
        'surat_masuk_id',
        'tujuan_bagian_id',
        'surat_keluar_id',
        'isi_instruksi',
        'sifat',
        'catatan',
        'status',
        'tanggal_disposisi',
        'batas_waktu',
        'user_id',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_disposisi' => 'date',
            'batas_waktu' => 'date',
        ];
    }

    /**
     * ANCHOR: Get the surat masuk that owns the disposisi.
     */
    public function suratMasuk()
    {
        return $this->belongsTo(SuratMasuk::class, 'surat_masuk_id');
    }

    /**
     * ANCHOR: Get the surat keluar that owns the disposisi.
     */
    public function suratKeluar()
    {
        return $this->belongsTo(SuratKeluar::class, 'surat_keluar_id');
    }

    /**
     * ANCHOR: Get the bagian that owns the disposisi.
     */
    public function tujuanBagian()
    {
        return $this->belongsTo(Bagian::class, 'tujuan_bagian_id');
    }

    /**
     * ANCHOR: Get the user that created the disposisi.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
