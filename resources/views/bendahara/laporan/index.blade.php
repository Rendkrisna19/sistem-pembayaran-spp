@extends('layouts.app')
@section('title', 'Laporan Keuangan')
@section('title_page', 'Laporan Keuangan')

@section('content')
<div class="card mb-4">
    <div class="card-header"><i class="fas fa-filter"></i> Filter Laporan</div>
    <div class="card-body">
        <form method="GET" action="{{ route('bendahara.laporan.index') }}">
            <div class="row align-items-end">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Kelas</label>
                    <select name="kelas_id" class="form-select">
                        <option value="">Semua</option>
                        @foreach($kelas as $k)
                            <option value="{{$k->id}}" {{ (string)$k->id === request('kelas_id') ? 'selected' : '' }}>{{$k->nama_kelas}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Bulan</label>
                    <select name="bulan" class="form-select">
                        <option value="">Semua</option>
                        @foreach(["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"] as $b)
                            <option value="{{$b}}" {{ $b === request('bulan') ? 'selected' : '' }}>{{$b}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Tahun</label>
                    <input type="number" name="tahun" class="form-control" value="{{ request('tahun', date('Y')) }}">
                </div>
                <div class="col-md-2 mb-3 d-flex">
                    <button type="submit" class="btn btn-primary w-100 me-2"><i class="fas fa-search"></i> Cari</button>
                    <a href="{{ route('bendahara.laporan.index') }}" class="btn btn-secondary w-100"><i class="fas fa-sync-alt"></i> Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Hasil Laporan
        <div class="float-end">
            {{-- Tombol Export CSV --}}
            <a href="{{ route('bendahara.laporan.export_csv', request()->query()) }}" class="btn btn-success btn-sm me-2">
                <i class="fas fa-file-csv"></i> Export CSV
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Tgl. Bayar</th>
                        <th>Bulan Pembayaran</th>
                        <th>Jumlah</th>
                        <th>Diverifikasi oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @forelse($pembayarans as $p)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{$p->siswa->nama_lengkap}}</td>
                        <td>{{$p->siswa->kelas->nama_kelas}}</td>
                        <td>{{ \Carbon\Carbon::parse($p->tanggal_bayar)->format('d M Y') }}</td>
                        <td>{{ $p->bulan_dibayar }} {{ $p->tahun_dibayar }}</td>
                        <td>Rp {{number_format($p->jumlah_bayar, 0, ',', '.')}}</td>
                        <td>{{$p->bendahara->name ?? 'N/A'}}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-info btn-print-kwitansi"
                                data-id="{{ $p->id }}"
                                data-bs-toggle="modal"
                                data-bs-target="#kwitansiModal"
                                title="Lihat Kwitansi">
                                <i class="fas fa-print"></i> Lihat Kwitansi
                            </button>
                        </td>
                    </tr>
                    @php $total += $p->jumlah_bayar; @endphp
                    @empty
                    <tr><td colspan="8" class="text-center">Tidak ada data pembayaran lunas yang cocok dengan filter.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-end">Total</th>
                        <th>Rp {{number_format($total, 0, ',', '.')}}</th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="mt-3">
            {{ $pembayarans->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<div class="modal fade" id="kwitansiModal" tabindex="-1" aria-labelledby="kwitansiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kwitansiModalLabel">Kwitansi Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="kwitansiContent">
                <div class="text-center text-muted">Memuat data kwitansi...</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="printKwitansi()">Cetak</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const kwitansiModal = document.getElementById('kwitansiModal');
    const kwitansiContent = document.getElementById('kwitansiContent');

    kwitansiModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const pembayaranId = button.getAttribute('data-id');

        // Reset content modal saat dibuka
        kwitansiContent.innerHTML = '<div class="text-center text-muted">Memuat data kwitansi...</div>';

        // Lakukan request Ajax
        fetch(`/bendahara/laporan/get-kwitansi-data/${pembayaranId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const p = data.pembayaran;
                let kwitansiHtml = `
                    <div class="container-kwitansi" style="border: 1px solid #eee; padding: 20px; font-family: sans-serif; font-size: 10pt;">
                        <div style="text-align: center; margin-bottom: 30px;">
                            <h1 style="margin: 0; padding: 0; font-size: 18pt;">KWITANSI PEMBAYARAN</h1>
                            <p style="margin: 0; padding: 0; font-size: 10pt;">${p.nama_aplikasi}</p>
                            <p style="margin: 0; padding: 0; font-size: 10pt;">Jln. Contoh No. 123, Kota Contoh, Kode Pos 12345</p>
                            <hr>
                        </div>

                        <div style="text-align: right; font-weight: bold; margin-bottom: 10px;">
                            No. Kwitansi: ${p.id}/${new Date().toISOString().slice(0,10).replace(/-/g,"")}
                        </div>

                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                            <tr>
                                <td style="width: 25%;">Telah diterima dari</td>
                                <td style="width: 5%;">:</td>
                                <td style="width: 70%;"><strong>${p.siswa_nama}</strong></td>
                            </tr>
                            <tr>
                                <td>Kelas</td>
                                <td>:</td>
                                <td>${p.kelas_nama}</td>
                            </tr>
                            <tr>
                                <td>Untuk Pembayaran</td>
                                <td>:</td>
                                <td>${p.bulan_dibayar} ${p.tahun_dibayar}</td>
                            </tr>
                            <tr>
                                <td>Jumlah Pembayaran</td>
                                <td>:</td>
                                <td>
                                    <strong>Rp ${p.jumlah_bayar.toLocaleString('id-ID')}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td>Terbilang</td>
                                <td>:</td>
                                <td>
                                    ${numberToWords(p.jumlah_bayar)} Rupiah
                                    {{-- Anda perlu mengimplementasikan fungsi numberToWords di JS --}}
                                </td>
                            </tr>
                            <tr>
                                <td>Tanggal Bayar</td>
                                <td>:</td>
                                <td>${p.tanggal_bayar}</td>
                            </tr>
                        </table>

                        <div style="margin-top: 50px; text-align: right;">
                            <p>Medan, ${new Date().toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}</p>
                            <br><br><br>
                            <div>(${p.bendahara_nama})</div>
                            <p style="font-size: 8pt; margin-top: 5px;">(Nama Jelas & Tanda Tangan)</p>
                        </div>

                        <div style="margin-top: 50px; font-size: 8pt; text-align: center; color: #888;">
                            <p>Kwitansi ini adalah bukti pembayaran yang sah.</p>
                        </div>
                    </div>
                `;
                kwitansiContent.innerHTML = kwitansiHtml;
            })
            .catch(error => {
                console.error('Error fetching kwitansi data:', error);
                kwitansiContent.innerHTML = '<div class="alert alert-danger">Gagal memuat kwitansi. Silakan coba lagi.</div>';
            });
    });
});

// Fungsi untuk mencetak konten modal
function printKwitansi() {
    const printContent = document.getElementById('kwitansiContent').innerHTML;
    const originalBody = document.body.innerHTML;

    // Buat jendela cetak sementara
    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = originalBody; // Kembalikan konten asli
}

// Fungsi helper JavaScript untuk mengubah angka menjadi kata-kata (contoh sederhana)
// Untuk implementasi yang lebih robust, cari library JS "number to words"
function numberToWords(num) {
    const units = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan'];
    const teens = ['sepuluh', 'sebelas', 'dua belas', 'tiga belas', 'empat belas', 'lima belas', 'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas'];
    const tens = ['', '', 'dua puluh', 'tiga puluh', 'empat puluh', 'lima puluh', 'enam puluh', 'tujuh puluh', 'delapan puluh', 'sembilan puluh'];
    const scales = ['', 'ribu', 'juta', 'milyar', 'triliun'];

    if (num === 0) return 'nol';

    function convertLessThanOneThousand(n) {
        let s = '';
        if (n >= 100) {
            s += units[Math.floor(n / 100)] + ' ratus';
            n %= 100;
            if (n > 0) s += ' ';
        }
        if (n >= 20) {
            s += tens[Math.floor(n / 10)];
            n %= 10;
            if (n > 0) s += ' ';
        }
        if (n >= 10) {
            s += teens[n - 10];
        } else if (n > 0) {
            s += units[n];
        }
        return s;
    }

    let result = '';
    let i = 0;
    while (num > 0) {
        const chunk = num % 1000;
        if (chunk > 0) {
            let chunkStr = convertLessThanOneThousand(chunk);
            if (i > 0) {
                result = chunkStr + ' ' + scales[i] + ' ' + result;
            } else {
                result = chunkStr;
            }
        }
        num = Math.floor(num / 1000);
        i++;
    }
    return result.trim();
}
</script>
@endpush