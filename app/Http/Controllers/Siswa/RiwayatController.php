<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index()
    {
        $pembayarans = Pembayaran::where('siswa_id', Auth::user()->siswa->id)
            ->latest()
            ->paginate(10);
            
        return view('siswa.riwayat.index', compact('pembayarans'));
    }

    public function cetakKwitansi(Pembayaran $pembayaran)
    {
        // Pastikan siswa hanya bisa mencetak kwitansinya sendiri
        if ($pembayaran->siswa_id !== Auth::user()->siswa->id || $pembayaran->status !== 'Lunas') {
            abort(403, 'Akses Ditolak');
        }

        $pembayaran->load(['siswa.kelas', 'bendahara']);
        return view('siswa.cetak.kwitansi', compact('pembayaran'));
    }
}