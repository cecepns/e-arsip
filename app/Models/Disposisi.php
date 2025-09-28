<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Disposisi extends Model
{
    use HasFactory;

    protected $table = 'disposisi';

    protected $fillable = [
        'surat_masuk_id',
        'tujuan_bagian_id',
        'isi_instruksi',
        'sifat',
        'catatan',
        'status',
        'tanggal_disposisi',
        'batas_waktu',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_disposisi' => 'date',
            'batas_waktu' => 'date',
        ];
    }

    /**
     * Get the surat masuk that owns the disposisi.
     */
    public function suratMasuk()
    {
        return $this->belongsTo(SuratMasuk::class);
    }

    /**
     * Get the bagian that owns the disposisi.
     */
    public function tujuanBagian()
    {
        return $this->belongsTo(Bagian::class, 'tujuan_bagian_id');
    }

    /**
     * Get the user that created the disposisi.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
