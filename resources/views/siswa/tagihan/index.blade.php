@extends('layouts.app')
@section('title', 'Tagihan Saya')
@section('title_page', 'Tagihan Pembayaran SPP')

@section('content')
<div class="card">
    <div class="card-header">Daftar Tagihan Anda</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Deskripsi Tagihan</th>
                        <th>Jumlah Tagihan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tagihans as $tagihan)
                        <tr>
                            <td>{{ $tagihan->deskripsi }}</td>
                            <td>Rp {{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}</td>
                            <td>
                                @if ($tagihan->status == 'Lunas')
                                    <span class="badge bg-success">Lunas</span>
                                @elseif ($tagihan->status == 'Menunggu Verifikasi')
                                    <span class="badge bg-warning">Menunggu Verifikasi</span>
                                @else
                                    <span class="badge bg-danger">Belum Lunas</span>
                                @endif
                            </td>
                            <td>
                                @if ($tagihan->status == 'Belum Lunas')
                                    {{-- PERBAIKAN: Link sekarang hanya mengarah ke halaman upload umum --}}
                                    <a href="{{ route('siswa.upload.create') }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-upload"></i> Bayar
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">Anda tidak memiliki tagihan saat ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
