<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Pastikan model User diimpor
use Illuminate\Support\Facades\Hash; // Untuk meng-hash password
use Illuminate\Validation\Rule; // Untuk validasi unique email saat update

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna.
     */
    public function index()
    {
        $users = User::all(); // Mengambil semua data pengguna
        return view('admin.users.index', compact('users'));
    }

    /**
     * Menyimpan pengguna baru yang dibuat oleh Admin.
     */
    public function store(Request $request)
    {
        // Validasi data input
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', // Email harus unik di tabel users
            'password' => 'required|string|min:8|confirmed', // Password minimal 8 karakter dan harus dikonfirmasi
            'role' => 'required|in:admin,bendahara,siswa', // Role harus salah satu dari ini
        ], [
            // Pesan error kustom untuk validasi
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak valid.'
        ]);

        // Buat pengguna baru
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Hash password sebelum disimpan
            'role' => $request->role,
        ]);

        // Redirect kembali ke halaman daftar pengguna dengan pesan sukses
        return redirect()->route('admin.users.index')->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit pengguna tertentu (akan digunakan di modal).
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        // Mengembalikan data user dalam bentuk JSON untuk diisi ke modal (digunakan AJAX)
        // Atau kamu bisa mengembalikan view jika tidak pakai AJAX
        return response()->json($user);
    }

    /**
     * Memperbarui data pengguna tertentu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        // Validasi data input untuk update
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id), // Email harus unik kecuali untuk user ini sendiri
            ],
            'password' => 'nullable|string|min:8|confirmed', // Password opsional saat update
            'role' => 'required|in:admin,bendahara,siswa',
        ], [
            // Pesan error kustom untuk validasi update
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role yang dipilih tidak valid.'
        ]);

        // Perbarui data pengguna
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        // Hanya perbarui password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save(); // Simpan perubahan

        // Redirect kembali ke halaman daftar pengguna dengan pesan sukses
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui!');
    }

    /**
     * Menghapus pengguna tertentu.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // Pastikan admin tidak bisa menghapus akunnya sendiri
        if (auth()->user()->id === $user->id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri!');
        }

        $user->delete(); // Hapus pengguna

        // Redirect kembali ke halaman daftar pengguna dengan pesan sukses
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus!');
    }
}