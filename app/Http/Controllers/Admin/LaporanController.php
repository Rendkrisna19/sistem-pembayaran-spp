<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Kelas;
use Illuminate\Support\Facades\Storage; // Penting untuk cek/tampilkan file
use Carbon\Carbon; // Pastikan Carbon diimport!

class LaporanController extends Controller
{
    /**
     * Menampilkan daftar laporan pembayaran dengan fitur filter.
     * (Ini adalah fungsi index yang sudah ada di Admin)
     */
    public function index(Request $request)
    {
        $query = Pembayaran::with(['siswa.kelas', 'bendahara']);

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        if ($request->filled('bulan')) {
            $query->where('bulan_dibayar', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun_dibayar', $request->tahun);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pembayarans = $query->latest()->paginate(15);
        $kelas = Kelas::orderBy('nama_kelas')->get();

        return view('admin.laporan.index', compact('pembayarans', 'kelas'));
    }

    /**
     * Menampilkan bukti pembayaran (foto).
     * (Ini adalah fungsi showBukti yang sudah ada di Admin Controller)
     */
    public function showBukti($id)
    {
        $pembayaran = Pembayaran::find($id);

        if (!$pembayaran || !$pembayaran->bukti_pembayaran) {
            return redirect()->back()->with('error', 'Bukti pembayaran tidak ditemukan.');
        }

        $path = 'public/' . $pembayaran->bukti_pembayaran; 

        if (!Storage::exists($path)) {
            return redirect()->back()->with('error', 'File bukti pembayaran tidak ditemukan.');
        }

        return Storage::response($path);
    }

    /**
     * Export laporan pembayaran Admin ke CSV.
     * (Ini adalah metode exportCsv yang sudah kita tambahkan sebelumnya)
     */
    public function exportCsv(Request $request)
    {
        // ... (kode exportCsv Anda yang sudah ada) ...
        $kelas_id = $request->input('kelas_id');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $status = $request->input('status'); 

        $query = Pembayaran::with(['siswa.kelas', 'bendahara']);

        if ($kelas_id) {
            $query->whereHas('siswa', function ($q) use ($kelas_id) {
                $q->where('kelas_id', $kelas_id);
            });
        }
        if ($bulan) {
            $query->where('bulan_dibayar', $bulan);
        }
        if ($tahun) {
            $query->where('tahun_dibayar', $tahun);
        }
        if ($status) {
            $query->where('status', $status);
        }
        // Admin mungkin ingin melihat semua status secara default,
        // jika ingin hanya yang Lunas, uncomment baris ini:
        // $query->where('status', 'Lunas'); 

        $pembayarans = $query->latest()->get(); 

        $fileName = 'Laporan_Pembayaran_Admin_' . Carbon::now()->format('Ymd_His') . '.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = [
            'ID Pembayaran', 'Nama Siswa', 'Kelas', 'Tanggal Bayar',
            'Bulan Dibayar', 'Tahun Dibayar', 'Jumlah Bayar (Rp)',
            'Status', 'Diverifikasi Oleh', 'Keterangan'
        ];

        $callback = function() use ($pembayarans, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns); 

            foreach ($pembayarans as $pembayaran) {
                $row = [
                    $pembayaran->id,
                    $pembayaran->siswa->nama_lengkap,
                    $pembayaran->siswa->kelas->nama_kelas,
                    Carbon::parse($pembayaran->tanggal_bayar)->format('d M Y'),
                    $pembayaran->bulan_dibayar,
                    $pembayaran->tahun_dibayar,
                    $pembayaran->jumlah_bayar,
                    $pembayaran->status,
                    $pembayaran->bendahara->name ?? 'N/A',
                    $pembayaran->keterangan,
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Mengambil data pembayaran untuk ditampilkan di modal kwitansi Admin (via Ajax).
     * Ini adalah metode yang perlu Anda tambahkan.
     *
     * @param Pembayaran $pembayaran
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKwitansiData(Pembayaran $pembayaran)
    {
        // Admin mungkin ingin melihat kwitansi meskipun statusnya belum Lunas
        // Tapi jika hanya Lunas, uncomment ini:
        // if ($pembayaran->status !== 'Lunas') {
        //     return response()->json(['error' => 'Kwitansi hanya bisa ditampilkan untuk pembayaran yang sudah lunas.'], 403);
        // }

        $pembayaran->load(['siswa.kelas', 'bendahara']);

        return response()->json([
            'pembayaran' => [
                'id' => $pembayaran->id,
                'siswa_nama' => $pembayaran->siswa->nama_lengkap ?? 'Tidak Diketahui', // Tambahkan null-safe operator
                'kelas_nama' => $pembayaran->siswa->kelas->nama_kelas ?? 'Tidak Diketahui', // Tambahkan null-safe operator
                'bulan_dibayar' => $pembayaran->bulan_dibayar,
                'tahun_dibayar' => $pembayaran->tahun_dibayar,
                'jumlah_bayar' => $pembayaran->jumlah_bayar,
                'tanggal_bayar' => Carbon::parse($pembayaran->tanggal_bayar)->format('d F Y'),
                'bendahara_nama' => $pembayaran->bendahara->name ?? 'N/A',
                'tanggal_cetak' => Carbon::now()->format('d F Y H:i:s'),
                'nama_aplikasi' => config('app.name', 'Aplikasi Pembayaran Sekolah'),
            ]
        ]);
    }
}