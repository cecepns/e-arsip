<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengaturan extends Model
{
    use HasFactory;

    protected $table = 'pengaturan';

    protected $fillable = [
        'nama_instansi',
        'alamat',
        'no_telp',
        'email',
        'logo',
        'nama_pejabat',
        'jabatan_pejabat',
    ];

    /**
     * Get the first (and only) pengaturan record.
     */
    public static function getInstance()
    {
        return static::first() ?? static::create([
            'nama_instansi' => 'Nama Instansi',
            'alamat' => '',
            'no_telp' => '',
            'email' => '',
            'logo' => '',
            'nama_pejabat' => '',
            'jabatan_pejabat' => '',
        ]);
    }
}
