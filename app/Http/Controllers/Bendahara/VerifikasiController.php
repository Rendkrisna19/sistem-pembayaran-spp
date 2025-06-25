<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VerifikasiController extends Controller
{
    public function index()
    {
        $pembayarans = Pembayaran::with('siswa.kelas')
            ->where('status', 'Menunggu Verifikasi')
            ->latest()
            ->paginate(10);
            
        return view('bendahara.verifikasi.index', compact('pembayarans'));
    }

    public function update(Request $request, Pembayaran $pembayaran)
    {
        $request->validate(['status' => 'required|in:Lunas,Ditolak']);

        $tagihan = Tagihan::where('pembayaran_id', $pembayaran->id)->first();
        
        DB::beginTransaction();
        try {
            $pembayaran->update([
                'status' => $request->status,
                'user_id' => Auth::id(),
            ]);

            if ($tagihan) {
                if ($request->status == 'Lunas') {
                    $tagihan->update(['status' => 'Lunas']);
                } else { // Jika Ditolak
                    $tagihan->update([
                        'status' => 'Belum Lunas',
                        'pembayaran_id' => null // Lepaskan hubungan pembayaran
                    ]);
                }
            }
            DB::commit();
            return redirect()->route('bendahara.verifikasi.index')->with('success', 'Status pembayaran berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui status.');
        }
    }
}