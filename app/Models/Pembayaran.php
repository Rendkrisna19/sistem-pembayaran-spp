<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Pembayaran extends Model
    {
        use HasFactory;

        protected $fillable = [
            'siswa_id',
            'user_id',
            'tanggal_bayar',
            'bulan_dibayar',
            'tahun_dibayar',
            'jumlah_bayar',
            'bukti_pembayaran',
            'status',
            'keterangan',
        ];

        /**
         * Relasi ke Siswa yang melakukan pembayaran.
         */
        public function siswa()
        {
            return $this->belongsTo(Siswa::class);
        }

        /**
         * Relasi ke User (Bendahara) yang memverifikasi.
         */
        public function bendahara()
        {
            return $this->belongsTo(User::class, 'user_id');
        }
    }
    