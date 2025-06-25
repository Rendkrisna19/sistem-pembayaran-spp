@extends('layouts.app') {{-- Menggunakan layout utama yang sudah dibuat --}}

@section('title', 'Dashboard Admin') {{-- Judul untuk tab browser --}}
@section('title_page', 'Dashboard Admin') {{-- Judul halaman di navbar --}}

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                Selamat Datang, Admin!
            </div>
            <div class="card-body">
                <h5 class="card-title">Halo, {{ Auth::user()->name }}!</h5>
                <p class="card-text">Anda saat ini masuk sebagai administrator. Gunakan menu sidebar di samping untuk mengelola data siswa, kelas, pengguna, dan melihat laporan pembayaran.</p>
                
                {{-- Tombol untuk membuka modal "Tambah Pengguna" --}}
                <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#createUserModal">
                    <i class="fas fa-plus-circle me-2"></i> Tambah Pengguna Baru
                </button>

                {{-- Bagian ringkasan fitur Admin --}}
                <div class="row text-center mt-4">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-info text-white h-100 shadow-sm">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <i class="fas fa-users fa-3x mb-3"></i>
                                <h5 class="card-title">Manajemen Siswa</h5>
                                <p class="card-text">Kelola data siswa.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white h-100 shadow-sm">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <i class="fas fa-school fa-3x mb-3"></i>
                                <h5 class="card-title">Manajemen Kelas</h5>
                                <p class="card-text">Atur informasi kelas.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-white h-100 shadow-sm">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <i class="fas fa-user-cog fa-3x mb-3"></i>
                                <h5 class="card-title">Manajemen User</h5>
                                <p class="card-text">Kelola akun pengguna.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-danger text-white h-100 shadow-sm">
                            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                <i class="fas fa-file-invoice-dollar fa-3x mb-3"></i>
                                <h5 class="card-title">Laporan Pembayaran</h5>
                                <p class="card-text">Lihat detail transaksi.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal untuk Tambah Pengguna Baru --}}
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white" style="background-color: #00bcd4 !important;">
                <h5 class="modal-title" id="createUserModalLabel">Tambah Pengguna Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    {{-- Nama Pengguna --}}
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Pengguna</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- Konfirmasi Password --}}
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                    {{-- Pilihan Role --}}
                    <div class="mb-3">
                        <label for="role" class="form-label">Pilih Role</label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="">Pilih Role</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="bendahara" {{ old('role') == 'bendahara' ? 'selected' : '' }}>Bendahara</option>
                            <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #00bcd4; border-color: #00bcd4;">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script untuk menampilkan modal jika ada error validasi setelah submit --}}
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var createUserModal = new bootstrap.Modal(document.getElementById('createUserModal'));
            createUserModal.show();
        });
    </script>
@endif
@endsection
