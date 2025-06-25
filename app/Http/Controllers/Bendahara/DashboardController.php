<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPendapatanBulanIni = Pembayaran::where('status', 'Lunas')
            ->whereYear('tanggal_bayar', Carbon::now()->year)
            ->whereMonth('tanggal_bayar', Carbon::now()->month)
            ->sum('jumlah_bayar');

        $transaksiBulanIni = Pembayaran::whereYear('tanggal_bayar', Carbon::now()->year)
            ->whereMonth('tanggal_bayar', Carbon::now()->month)
            ->count();

        $menungguVerifikasi = Pembayaran::where('status', 'Menunggu Verifikasi')->count();

        return view('bendahara.dashboard', compact('totalPendapatanBulanIni', 'transaksiBulanIni', 'menungguVerifikasi'));
    }
}