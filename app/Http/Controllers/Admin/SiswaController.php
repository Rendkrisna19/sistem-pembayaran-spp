<?php

// DIUBAH: Namespace disesuaikan dengan lokasi file di folder Admin
namespace App\Http\Controllers\Admin;

// DIUBAH: Menambahkan 'use' untuk base Controller
use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

// DIUBAH: Memastikan class extends ke base Controller
class SiswaController extends Controller
{
    /**
     * Menampilkan halaman berdasarkan peran pengguna.
     * - Jika Admin: Menampilkan daftar semua siswa (Manajemen Siswa).
     * - Jika Siswa: Menampilkan dashboard untuk siswa yang login.
     */
    public function index()
    {
        $user = Auth::user();

        // Cek jika peran adalah 'admin'
        if ($user->role === 'admin') {
            // Logika untuk Admin: Tampilkan halaman manajemen semua siswa
            $siswas = Siswa::with(['user', 'kelas'])->orderBy('nama_lengkap')->get();
            $kelas = Kelas::orderBy('nama_kelas')->get(); // Diperlukan untuk modal tambah/edit siswa
            
            // Menggunakan view yang ada di folder admin/siswa
            return view('admin.siswa.index', compact('siswas', 'kelas'));
        }

        // Cek jika peran adalah 'siswa'
        if ($user->role === 'siswa') {
            // Logika untuk Siswa: Tampilkan dashboard siswa itu sendiri
            $siswa = $user->siswa()->with('kelas')->first();

            // Penanganan jika data siswa tidak ditemukan (seharusnya tidak terjadi)
            if (!$siswa) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Profil siswa tidak ditemukan. Hubungi admin.');
            }
            
            // Menggunakan view yang ada di folder siswa
            return view('siswa.index', compact('siswa'));
        }

        // Fallback jika peran tidak dikenali
        return redirect()->route('login');
    }

    /**
     * Method-method berikut (store, update, destroy) hanya akan dipanggil
     * oleh route admin (Route::resource) dan tidak akan terpengaruh.
     * Logikanya tidak perlu diubah.
     */

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'nisn' => ['required', 'string', 'max:20', 'unique:'.Siswa::class],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'alamat' => ['nullable', 'string'],
        ]);

        // Buat user terlebih dahulu
        $user = User::create([
            'name' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'siswa', // Otomatis set role sebagai siswa
        ]);

        // Kemudian buat data siswa yang berelasi dengan user
        Siswa::create([
            'user_id' => $user->id,
            'kelas_id' => $request->kelas_id,
            'nisn' => $request->nisn,
            'nama_lengkap' => $request->nama_lengkap,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('admin.siswa.index')->with('success', 'Akun dan data siswa berhasil dibuat.');
    }

    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'nisn' => ['required', 'string', 'max:20', 'unique:siswas,nisn,'.$siswa->id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$siswa->user_id],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'alamat' => ['nullable', 'string'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()], // Password opsional
        ]);
        
        // Update data di tabel user
        $user = $siswa->user;
        $user->name = $request->nama_lengkap;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        // Update data di tabel siswa
        $siswa->update($request->all());
        
        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        // Hapus user terkait, data siswa akan terhapus otomatis karena onDelete('cascade')
        $siswa->user->delete();
        
        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil dihapus.');
    }
}