<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bagian extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bagian';

    protected $fillable = [
        'nama_bagian',
        'keterangan',
        'kepala_bagian_user_id',
        'status',
    ];

    /**
     * Get the users for the bagian.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the surat masuk for the bagian.
     */
    public function suratMasuk()
    {
        return $this->hasMany(SuratMasuk::class, 'tujuan_bagian_id');
    }

    /**
     * Get the surat keluar for the bagian.
     */
    public function suratKeluar()
    {
        return $this->hasMany(SuratKeluar::class, 'pengirim_bagian_id');
    }

    /**
     * Get the disposisi for the bagian.
     */
    public function disposisi()
    {
        return $this->hasMany(Disposisi::class, 'tujuan_bagian_id');
    }

    /**
     * Get the kepala bagian user for this bagian.
     */
    public function kepalaBagian()
    {
        return $this->belongsTo(User::class, 'kepala_bagian_user_id');
    }
}
