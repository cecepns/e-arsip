<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lampiran extends Model
{
    use HasFactory;

    protected $table = 'lampiran';

    protected $fillable = [
        'surat_id',
        'tipe_surat',
        'nama_file',
        'path_file',
        'file_size',
        'tipe_lampiran',
    ];

    /**
     * Get the surat that owns the lampiran (polymorphic relationship).
     */
    public function surat()
    {
        if ($this->tipe_surat === 'masuk') {
            return $this->belongsTo(SuratMasuk::class, 'surat_id');
        } else {
            return $this->belongsTo(SuratKeluar::class, 'surat_id');
        }
    }
}
