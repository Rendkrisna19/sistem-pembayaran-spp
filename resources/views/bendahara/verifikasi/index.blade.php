@extends('layouts.app')
@section('title', 'Verifikasi Pembayaran')
@section('title_page', 'Verifikasi Pembayaran')

@section('content')
<div class="card">
    <div class="card-header">Daftar Pembayaran Menunggu Verifikasi</div>
    <div class="card-body">
         @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Siswa</th>
                        <th>Tgl. Bayar</th>
                        <th>Jumlah</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pembayarans as $pembayaran)
                    <tr>
                        <td>{{ $pembayaran->siswa->nama_lengkap }}<br><small>{{ $pembayaran->siswa->kelas->nama_kelas }}</small></td>
                        <td>{{ $pembayaran->tanggal_bayar }}</td>
                        <td>Rp {{ number_format($pembayaran->jumlah_bayar) }}</td>
                        <td><a href="{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}" target="_blank" class="btn btn-sm btn-info">Lihat Bukti</a></td>
                        <td>
                            <form action="{{ route('bendahara.verifikasi.update', $pembayaran->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="Lunas">
                                <button type="submit" class="btn btn-sm btn-success mb-1 w-100">Setujui</button>
                            </form>
                            <form action="{{ route('bendahara.verifikasi.update', $pembayaran->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="Ditolak">
                                <button type="submit" class="btn btn-sm btn-danger w-100">Tolak</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">Tidak ada pembayaran yang perlu diverifikasi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $pembayarans->links() }}
    </div>
</div>
@endsection
