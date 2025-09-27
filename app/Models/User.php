<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'nama',
        'email',
        'phone',
        'password',
        'role',
        'bagian_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // Password tidak di-hash sesuai keinginan klien
        ];
    }

    /**
     * Get the bagian that owns the user.
     */
    public function bagian()
    {
        return $this->belongsTo(Bagian::class);
    }

    /**
     * Get the surat masuk created by the user.
     */
    public function suratMasuk()
    {
        return $this->hasMany(SuratMasuk::class);
    }

    /**
     * Get the surat keluar created by the user.
     */
    public function suratKeluar()
    {
        return $this->hasMany(SuratKeluar::class);
    }

    /**
     * Get the disposisi created by the user.
     */
    public function disposisi()
    {
        return $this->hasMany(Disposisi::class);
    }

    /**
     * Get the bagian where this user is kepala bagian.
     */
    public function bagianKepala()
    {
        return $this->hasOne(Bagian::class, 'kepala_bagian_user_id');
    }

    /**
     * Check if this user is kepala bagian of any bagian.
     */
    public function isKepalaBagian(): bool
    {
        return $this->bagianKepala()->exists();
    }
}
