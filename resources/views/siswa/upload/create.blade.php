@extends('layouts.app')
@section('title', 'Upload Bukti Pembayaran')
@section('title_page', 'Upload Bukti Pembayaran')

@section('content')
<div class="card">
    <div class="card-header">Form Upload</div>
    <div class="card-body">
        {{-- Menampilkan pesan error jika ada --}}
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Error!</strong> Terdapat masalah dengan input Anda.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        {{-- PASTIKAN ADA enctype="multipart/form-data" UNTUK UPLOAD FILE --}}
        <form action="{{ route('siswa.upload.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-3">
                <label for="tagihan_id" class="form-label">Pilih Tagihan yang Akan Dibayar</label>
                <select name="tagihan_id" class="form-select" required>
                    <option value="" disabled selected>-- Pilih Tagihan --</option>
                    @forelse($tagihans as $tagihan)
                        <option value="{{ $tagihan->id }}" {{ old('tagihan_id') == $tagihan->id ? 'selected' : '' }}>
                            {{ $tagihan->deskripsi }} - Rp {{ number_format($tagihan->jumlah_tagihan) }}
                        </option>
                    @empty
                        <option disabled>Tidak ada tagihan yang perlu dibayar.</option>
                    @endforelse
                </select>
            </div>
            <div class="mb-3">
                <label for="bukti_pembayaran" class="form-label">Upload Bukti Anda (Format: JPG, PNG, max: 2MB)</label>
                <input type="file" name="bukti_pembayaran" class="form-control" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary" @if($tagihans->isEmpty()) disabled @endif>
                Kirim Bukti Pembayaran
            </button>
        </form>
    </div>
</div>
@endsection
