@extends('layouts.app')

@section('title', 'Dashboard')
@section('title_page', 'Dashboard Saya')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Selamat Datang, {{ $siswa->nama_lengkap }}!</h5>
        </div>
        <div class="card-body">
            <p class="card-text">Ini adalah halaman dashboard Anda. Berikut adalah rincian informasi profil Anda saat ini.</p>
            
            <table class="table table-bordered table-striped mt-4">
                <tbody>
                    <tr>
                        <th style="width: 200px;">NISN</th>
                        <td>{{ $siswa->nisn }}</td>
                    </tr>
                    <tr>
                        <th>Nama Lengkap</th>
                        <td>{{ $siswa->nama_lengkap }}</td>
                    </tr>
                     <tr>
                        <th>Kelas</th>
                        <td>{{ $siswa->kelas->nama_kelas }}</td>
                    </tr>
                    <tr>
                        <th>Email Akun</th>
                        <td>{{ $siswa->user->email }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <td>{{ $siswa->alamat ?? 'Tidak ada data' }}</td>
                    </tr>
                </tbody>
            </table>
            
            <hr>

            <div class="alert alert-info mt-4" role="alert">
                <i class="fas fa-info-circle"></i> 
                Fitur untuk melihat tagihan, mengunggah bukti bayar, dan melihat riwayat pembayaran akan tersedia di menu sidebar.
            </div>
        </div>
    </div>
</div>
@endsection
