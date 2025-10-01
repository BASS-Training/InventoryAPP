<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_vendor',
        'alamat',
        'kontak_email',
        'kontak_telepon',
        'deskripsi',
        'status', // 'aktif', 'nonaktif'
    ];
}