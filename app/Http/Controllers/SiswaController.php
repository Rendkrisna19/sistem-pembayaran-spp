<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class SiswaController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $siswas = Siswa::with(['user', 'kelas'])->orderBy('nama_lengkap')->get();
            $kelas = Kelas::orderBy('nama_kelas')->get();
            return view('admin.siswa.index', compact('siswas', 'kelas'));
        }

        if ($user->role === 'siswa') {
            $siswa = $user->siswa()->with('kelas')->first();

            if (!$siswa) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Profil siswa tidak ditemukan. Hubungi admin.');
            }
            
            // DIUBAH: Mengarahkan ke view 'siswa.dashboard'
            return view('siswa.dashboard', compact('siswa'));
        }

        return redirect()->route('login');
    }

    // ... (Method store, update, destroy tetap sama)

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

        $user = User::create([
            'name' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'siswa',
        ]);

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
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);
        
        $user = $siswa->user;
        $user->name = $request->nama_lengkap;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        $siswa->update($request->all());
        
        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->user->delete();
        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil dihapus.');
    }
}