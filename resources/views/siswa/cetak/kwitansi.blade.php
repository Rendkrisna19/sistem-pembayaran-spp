<!DOCTYPE html>
<html>
<head>
    <title>Kwitansi Pembayaran</title>
    <style>body{font-family:sans-serif; margin: 0;}.container{padding:20px; border: 1px solid #ccc; margin: 20px;}table{width:100%;border-collapse:collapse;}td,th{padding:8px;text-align: left;}.text-center{text-align:center;}.footer{margin-top:50px;}</style>
</head>
<body onload="window.print()">
    <div class="container">
        <h2 class="text-center">BUKTI PEMBAYARAN SPP</h2>
        <hr>
        <table>
            <tr><td style="width: 150px;">No. Kwitansi</td><td>: #{{ $pembayaran->id }}</td></tr>
            <tr><td>Telah Diterima dari</td><td>: {{ $pembayaran->siswa->nama_lengkap }}</td></tr>
            <tr><td>NISN</td><td>: {{ $pembayaran->siswa->nisn }}</td></tr>
            <tr><td>Kelas</td><td>: {{ $pembayaran->siswa->kelas->nama_kelas }}</td></tr>
            <tr><td>Uang Sejumlah</td><td>: <strong>Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</strong></td></tr>
            <tr><td>Untuk Pembayaran</td><td>: SPP Bulan {{ $pembayaran->bulan_dibayar }} {{ $pembayaran->tahun_dibayar }}</td></tr>
            <tr><td>Status</td><td>: <strong>LUNAS</strong></td></tr>
        </table>
        <div class="footer text-center">
            <p>Medan, {{ $pembayaran->updated_at->format('d F Y') }}</p>
            <p>Telah diverifikasi oleh,</p>
            <br><br><br>
            <p><strong>({{ $pembayaran->bendahara->name ?? 'Bendahara Sekolah' }})</strong></p>
        </div>
    </div>
</body>
</html>
