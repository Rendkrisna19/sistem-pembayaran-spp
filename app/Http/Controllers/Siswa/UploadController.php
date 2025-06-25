<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage; // Pastikan ini ada

class UploadController extends Controller
{
    public function create()
    {
        $tagihans = Tagihan::where('siswa_id', Auth::user()->siswa->id)
            ->where('status', 'Belum Lunas')
            ->get();

        return view('siswa.upload.create', compact('tagihans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tagihan_id' => 'required|exists:tagihans,id',
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $tagihan = Tagihan::findOrFail($request->tagihan_id);

        if ($tagihan->siswa_id !== Auth::user()->siswa->id || $tagihan->status !== 'Belum Lunas') {
            abort(403);
        }

        DB::beginTransaction();
        try {
            // *** PERBAIKAN PENTING DI SINI ***
            // Mengunggah file ke disk 'public' di dalam folder 'bukti_pembayaran'
            $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
            // $path sudah berisi 'bukti_pembayaran/nama_file_unik.png'
            $publicPath = $path; // Ini akan menjadi path yang disimpan di database

            $tahunDibayar = Carbon::parse($tagihan->tanggal_tagihan)->year;

            $pembayaran = Pembayaran::create([
                'siswa_id' => $tagihan->siswa_id,
                'user_id' => null, // Diisi oleh bendahara saat verifikasi
                'tanggal_bayar' => now(),
                'bulan_dibayar' => Carbon::parse($tagihan->tanggal_tagihan)->translatedFormat('F'), // Saran perbaikan
                'tahun_dibayar' => $tahunDibayar,
                'jumlah_bayar' => $tagihan->jumlah_tagihan,
                'bukti_pembayaran' => $publicPath, // Simpan path yang benar
                'status' => 'Menunggu Verifikasi',
                'keterangan' => 'Pembayaran via transfer oleh siswa.',
            ]);

            $tagihan->update([
                'status' => 'Menunggu Verifikasi',
                'pembayaran_id' => $pembayaran->id,
            ]);

            DB::commit();
            return redirect()->route('siswa.riwayat.index')->with('success', 'Bukti pembayaran berhasil diunggah!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Upload Gagal: ' . $e->getMessage()); 
            return back()->with('error', 'Gagal mengunggah bukti pembayaran. Silakan coba lagi.');
        }
    }
}