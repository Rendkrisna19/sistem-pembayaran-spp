<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Tagihan; // Hanya menggunakan model Tagihan

class PembayaranController extends Controller
{
    /**
     * Menampilkan form untuk MEMBUAT TAGIHAN baru (sebelumnya Catat Pembayaran).
     */
    public function index()
    {
        $siswas = Siswa::with('kelas')->orderBy('nama_lengkap')->get();
        // Menampilkan 10 tagihan terakhir yang dibuat sebagai referensi
        $recentTagihans = Tagihan::with('siswa.kelas')->latest()->take(10)->get();

        return view('bendahara.pembayaran.index', compact('siswas', 'recentTagihans'));
    }
    
    /**
     * Menyimpan data sebagai TAGIHAN BARU dengan status "Belum Lunas".
     * Tidak ada data pembayaran yang dibuat di sini.
     */
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'jumlah_tagihan' => 'required|numeric|min:1000',
            'deskripsi' => 'required|string|max:255',
        ]);

        // HANYA membuat data tagihan baru
        Tagihan::create([
            'siswa_id' => $request->siswa_id,
            'deskripsi' => $request->deskripsi,
            'jumlah_tagihan' => $request->jumlah_tagihan,
            'status' => 'Belum Lunas', // Status awal adalah "Belum Lunas"
            'tanggal_tagihan' => now(),
        ]);
        
        return redirect()->route('bendahara.pembayaran.index')->with('success', 'Tagihan baru dengan status "Belum Lunas" berhasil dibuat untuk siswa.');
    }
}