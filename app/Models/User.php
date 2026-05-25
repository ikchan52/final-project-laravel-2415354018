<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // Cukup panggil satu kali di sini
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Field yang boleh diisi secara mass-assignment.
     * Kita masukkan role dan status agar bisa diatur saat registrasi.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    /**
     * Field yang disembunyikan saat data dikirim sebagai JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting tipe data agar otomatis terformat saat diakses.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'string',
            'role' => 'string',
        ];
    }
}
