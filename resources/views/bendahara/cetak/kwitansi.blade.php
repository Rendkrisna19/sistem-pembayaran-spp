<!DOCTYPE html>
<html>
<head>
    <title>Kwitansi Pembayaran</title>
    <style>body{font-family:sans-serif}.container{padding:20px}table{width:100%;border-collapse:collapse}td,th{padding:8px;border:1px solid #ddd}.text-center{text-align:center}.footer{margin-top:50px}</style>
</head>
<body onload="window.print()">
    <div class="container">
        <h2 class="text-center">KWITANSI PEMBAYARAN SPP</h2><hr>
        <table>
            <tr><td>No. Kwitansi</td><td>: #{{ $pembayaran->id }}</td></tr>
            <tr><td>Sudah terima dari</td><td>: {{ $pembayaran->siswa->nama_lengkap }} ({{ $pembayaran->siswa->kelas->nama_kelas }})</td></tr>
            <tr><td>Uang sejumlah</td><td>: Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</td></tr>
            <tr><td>Untuk pembayaran</td><td>: SPP Bulan {{ $pembayaran->bulan_dibayar }} {{ $pembayaran->tahun_dibayar }}</td></tr>
        </table>
        <div class="footer text-center">
            <p>Medan, {{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d M Y') }}</p><br><br>
            <p>({{ $pembayaran->bendahara->name ?? 'Bendahara Sekolah' }})</p>
        </div>
    </div>
</body>
</html>
