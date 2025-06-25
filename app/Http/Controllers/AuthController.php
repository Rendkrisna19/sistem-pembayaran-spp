<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Coba untuk melakukan otentikasi
        if (Auth::attempt($credentials)) {
            // Jika berhasil, buat session baru
            $request->session()->regenerate();

            // 3. Ambil user yang sudah login
            $user = Auth::user();

            // 4. Arahkan berdasarkan peran (INI BAGIAN YANG PALING PENTING)
            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->role === 'bendahara') {
                return redirect()->intended(route('bendahara.dashboard'));
            } elseif ($user->role === 'siswa') {
                return redirect()->intended(route('siswa.dashboard'));
            }

            // Fallback jika peran tidak ada
            return redirect('/login');
        }

        // 5. Jika otentikasi gagal
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // Fungsi register jika diperlukan
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:admin,bendahara,siswa'], // Sesuaikan jika perlu
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    }
}