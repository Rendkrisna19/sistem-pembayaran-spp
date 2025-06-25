@extends('layouts.app')
@section('title', 'Dashboard Bendahara')
@section('title_page', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">Total Pendapatan Bulan Ini</div>
            <div class="card-body">
                <h5 class="card-title">Rp {{ number_format($totalPendapatanBulanIni, 0, ',', '.') }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">Total Transaksi Bulan Ini</div>
            <div class="card-body">
                <h5 class="card-title">{{ $transaksiBulanIni }} Transaksi</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-warning mb-3">
            <div class="card-header">Menunggu Verifikasi</div>
            <div class="card-body">
                <h5 class="card-title">{{ $menungguVerifikasi }} Pembayaran</h5>
                <a href="{{ route('bendahara.verifikasi.index') }}" class="btn btn-sm btn-light mt-2">Lihat Detail</a>
            </div>
        </div>
    </div>
</div>
@endsection
