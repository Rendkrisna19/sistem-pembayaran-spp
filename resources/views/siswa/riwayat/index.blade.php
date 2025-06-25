@extends('layouts.app')
@section('title', 'Riwayat Pembayaran')
@section('title_page', 'Riwayat Pembayaran Saya')

@section('content')
<div class="card">
    <div class="card-header">Daftar Transaksi</div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead><tr><th>Tgl. Transaksi</th><th>Pembayaran Bulan</th><th>Jumlah</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($pembayarans as $p)
                    <tr>
                        <td>{{ $p->created_at->format('d M Y, H:i') }}</td>
                        <td>{{ $p->bulan_dibayar }} {{ $p->tahun_dibayar }}</td>
                        <td>Rp {{ number_format($p->jumlah_bayar) }}</td>
                        <td>
                            @php
                                $statusClass = ['Lunas' => 'success', 'Ditolak' => 'danger', 'Menunggu Verifikasi' => 'warning'];
                            @endphp
                            <span class="badge bg-{{ $statusClass[$p->status] }}">{{ $p->status }}</span>
                        </td>
                        <td>
                            @if($p->status == 'Lunas')
                                <a href="{{ route('siswa.kwitansi.cetak', $p->id) }}" target="_blank" class="btn btn-sm btn-info"><i class="fas fa-print"></i> Cetak</a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">Anda belum memiliki riwayat pembayaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $pembayarans->links() }}
    </div>
</div>
@endsection
