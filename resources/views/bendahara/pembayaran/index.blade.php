@extends('layouts.app')
@section('title', 'Buat Tagihan Siswa')
@section('title_page', 'Buat Tagihan Baru')

@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><i class="fas fa-plus-circle"></i> Form Buat Tagihan</div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <form action="{{ route('bendahara.pembayaran.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="siswa_id" class="form-label">Pilih Siswa</label>
                        <select name="siswa_id" class="form-select" required>
                            <option value="" disabled selected>-- Cari dan Pilih Siswa --</option>
                            @foreach ($siswas as $siswa)
                                <option value="{{ $siswa->id }}">{{ $siswa->nisn }} - {{ $siswa->nama_lengkap }} ({{ $siswa->kelas->nama_kelas }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi Tagihan</label>
                        <input type="text" name="deskripsi" class="form-control" placeholder="Contoh: SPP Bulan Agustus 2024" required>
                    </div>

                    <div class="mb-3">
                        <label for="jumlah_tagihan" class="form-label">Jumlah Tagihan (Rp)</label>
                        <input type="number" name="jumlah_tagihan" class="form-control" placeholder="Contoh: 150000" required>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Buat Tagihan
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-header"><i class="fas fa-history"></i> 10 Tagihan Terakhir Dibuat</div>
            <div class="card-body">
                <table class="table table-sm table-striped">
                    <thead><tr><th>Siswa</th><th>Deskripsi</th><th>Jumlah</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($recentTagihans as $tagihan)
                        <tr>
                            <td>{{ $tagihan->siswa->nama_lengkap }}</td>
                            <td>{{ $tagihan->deskripsi }}</td>
                            <td>Rp {{ number_format($tagihan->jumlah_tagihan) }}</td>
                            <td>
                                @if($tagihan->status == 'Belum Lunas')
                                    <span class="badge bg-danger">Belum Lunas</span>
                                @else
                                    <span class="badge bg-success">{{ $tagihan->status }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center">Belum ada tagihan yang dibuat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
