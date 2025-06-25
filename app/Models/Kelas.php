<?php

// app/Models/Kelas.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas'; // Nama tabel di database

    protected $fillable = [
        'nama_kelas',
        'jurusan',
    ];
}