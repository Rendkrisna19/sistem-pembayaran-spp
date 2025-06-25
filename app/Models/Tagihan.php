<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id',
        'pembayaran_id',
        'deskripsi',
        'jumlah_tagihan',
        'status',
        'tanggal_tagihan',
    ];

    /**
     * Relasi ke Siswa yang memiliki tagihan.
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Relasi ke Pembayaran yang melunasi tagihan ini.
     */
    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class);
    }
}