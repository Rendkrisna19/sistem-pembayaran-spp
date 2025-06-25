<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Kelas;
use Carbon\Carbon; // Untuk format tanggal

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembayaran::with(['siswa.kelas', 'bendahara']);

        // Pastikan filter dipertahankan saat ditampilkan di view untuk form
        $kelas_id = $request->input('kelas_id');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun', date('Y'));

        if ($request->filled('kelas_id')) {
            $query->whereHas('siswa', function ($q) use ($kelas_id) {
                $q->where('kelas_id', $kelas_id);
            });
        }
        if ($request->filled('bulan')) {
            $query->where('bulan_dibayar', $bulan);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun_dibayar', $tahun);
        }

        $pembayarans = $query->where('status', 'Lunas')->latest()->paginate(15);
        $kelas = Kelas::orderBy('nama_kelas')->get();

        return view('bendahara.laporan.index', compact('pembayarans', 'kelas', 'kelas_id', 'bulan', 'tahun'));
    }

    /**
     * Mengambil data pembayaran untuk ditampilkan di modal kwitansi (via Ajax).
     *
     * @param Pembayaran $pembayaran
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKwitansiData(Pembayaran $pembayaran)
    {
        if ($pembayaran->status !== 'Lunas') {
            return response()->json(['error' => 'Kwitansi hanya bisa ditampilkan untuk pembayaran yang sudah lunas.'], 403);
        }

        $pembayaran->load(['siswa.kelas', 'bendahara']);

        // Anda bisa mengembalikan data mentah dan biarkan JS yang merender,
        // atau render sebagian HTML di sini dan kirimkan.
        // Untuk kesederhanaan, kita kirim data dan render di JS.

        return response()->json([
            'pembayaran' => [
                'id' => $pembayaran->id,
                'siswa_nama' => $pembayaran->siswa->nama_lengkap,
                'kelas_nama' => $pembayaran->siswa->kelas->nama_kelas,
                'bulan_dibayar' => $pembayaran->bulan_dibayar,
                'tahun_dibayar' => $pembayaran->tahun_dibayar,
                'jumlah_bayar' => $pembayaran->jumlah_bayar,
                'tanggal_bayar' => Carbon::parse($pembayaran->tanggal_bayar)->format('d F Y'),
                'bendahara_nama' => $pembayaran->bendahara->name ?? 'Bendahara',
                'tanggal_cetak' => Carbon::now()->format('d F Y H:i:s'),
                'nama_aplikasi' => config('app.name', 'Aplikasi Pembayaran Sekolah'),
                // Anda bisa menambahkan helper terbilang di sini juga jika mau mengirimkan teks terbilang dari server
                // 'jumlah_terbilang' => terbilang($pembayaran->jumlah_bayar), // Pastikan helper terbilang tersedia
            ]
        ]);
    }

    /**
     * Export laporan pembayaran ke CSV.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCsv(Request $request)
    {
        $kelas_id = $request->input('kelas_id');
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

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

        $pembayarans = $query->where('status', 'Lunas')->latest()->get(); // Ambil semua data tanpa paginate

        $fileName = 'Laporan_Pembayaran_' . Carbon::now()->format('Ymd_His') . '.csv';

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
            fputcsv($file, $columns); // Tulis header

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
                fputcsv($file, $row); // Tulis baris data
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}