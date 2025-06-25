<?php

// app/Http/Controllers/Admin/KelasController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::orderBy('nama_kelas')->get();
        return view('admin.kelas.index', compact('kelas'));
    }

    public function create()
    {
        return view('admin.kelas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255|unique:kelas,nama_kelas',
            'jurusan' => 'required|string|max:255',
        ]);

        Kelas::create($request->all());

        return redirect()->route('admin.kelas.index')
                         ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function edit(Kelas $kela) // Laravel akan otomatis mencari kelas berdasarkan ID
    {
        return view('admin.kelas.edit', compact('kela'));
    }

    public function update(Request $request, Kelas $kela)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255|unique:kelas,nama_kelas,' . $kela->id,
            'jurusan' => 'required|string|max:255',
        ]);

        $kela->update($request->all());

        return redirect()->route('admin.kelas.index')
                         ->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kela)
    {
        // Tambahkan validasi jika kelas masih memiliki siswa
        if ($kela->siswa()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus kelas karena masih ada siswa terdaftar.');
        }
        
        $kela->delete();

        return redirect()->route('admin.kelas.index')
                         ->with('success', 'Data kelas berhasil dihapus.');
    }
}