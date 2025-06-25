<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Siswa;

class TagihanController extends Controller
{
    /**
     * Menampilkan form untuk membuat tagihan baru.
     */
    public function index()
    {
        $siswas = Siswa::with('kelas')->orderBy('nama_lengkap')->get();
        // Menampilkan 10 tagihan terakhir yang dibuat sebagai referensi
        $recentTagihans = Tagihan::with('siswa.kelas')->latest()->take(10)->get();

        return view('bendahara.tagihan.index', compact('siswas', 'recentTagihans'));
    }

    /**
     * Menyimpan tagihan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'deskripsi' => 'required|string|max:255',
            'jumlah_tagihan' => 'required|numeric|min:1000',
            'tanggal_tagihan' => 'required|date',
        ]);

        Tagihan::create([
            'siswa_id' => $request->siswa_id,
            'deskripsi' => $request->deskripsi,
            'jumlah_tagihan' => $request->jumlah_tagihan,
            'tanggal_tagihan' => $request->tanggal_tagihan,
            'status' => 'Belum Lunas', // Status awal saat dibuat
        ]);

        return redirect()->route('bendahara.tagihan.index')->with('success', 'Tagihan baru berhasil dibuat.');
    }
}