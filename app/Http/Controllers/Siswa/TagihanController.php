<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tagihan;

class TagihanController extends Controller
{
    /**
     * Menampilkan data tagihan yang riil dari database.
     */
    public function index()
    {
        // 1. Ambil user yang sedang login
        $user = Auth::user();

        // PENGECEKAN PENTING: Pastikan user ini memiliki profil siswa
        if (!$user->siswa) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Profil siswa untuk akun Anda tidak ditemukan. Silakan hubungi admin.');
        }

        // 2. Ambil ID siswa dari profil yang sudah dipastikan ada
        $siswa_id = $user->siswa->id;

        // 3. Ambil semua data tagihan dari database yang dimiliki oleh siswa ini
        $tagihans = Tagihan::where('siswa_id', $siswa_id)
            ->latest('tanggal_tagihan')
            ->get();

        // 4. Kirim data tagihan yang riil ke view
        return view('siswa.tagihan.index', compact('tagihans'));
    }
}