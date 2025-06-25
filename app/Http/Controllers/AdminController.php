<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Pastikan Auth diimpor

class AdminController extends Controller
{
    /**
     * Menampilkan dashboard admin.
     */
    public function index()
    {
        // Pastikan pengguna sudah login dan memiliki peran 'admin'
        // Middleware 'auth' dan 'role:admin' sudah menangani sebagian besar ini,
        // tapi ini bisa jadi pemeriksaan tambahan jika diperlukan.
        if (Auth::check() && Auth::user()->role === 'admin') {
            return view('admin.dashboard');
        }
        
        // Jika tidak berwenang atau belum login, arahkan kembali ke halaman login
        return redirect()->route('login');
    }
}