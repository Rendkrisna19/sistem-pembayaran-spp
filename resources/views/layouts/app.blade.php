<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pembayaran Sekolah - @yield('title', 'Dashboard')</title>

    <!-- Google Fonts - Poppins untuk tampilan modern -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap CSS CDN untuk styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Font Awesome untuk ikon-ikon yang menarik -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* Penting: Pastikan html dan body mengambil 100% tinggi viewport */
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            /* Mencegah scroll di level html/body untuk layout utama */
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
        }

        #wrapper {
            display: flex;
            min-height: 100vh;
            /* Pastikan wrapper mengambil tinggi penuh viewport */
            width: 100vw;
            /* Pastikan wrapper mengambil lebar penuh viewport */
            overflow: hidden;
            /* Mencegah overflow dari #wrapper itself */
        }

        #sidebar-wrapper {
            min-height: 100vh;
            margin-left: -17rem;
            transition: margin .25s ease-out;
            background-color: #00bcd4;
            color: white;
            width: 17rem;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
            /* Mencegah sidebar menyusut */
            overflow-y: auto;
            /* Sidebar bisa di-scroll vertikal jika kontennya banyak */
        }

        #sidebar-wrapper .sidebar-heading {
            padding: 1.5rem 1.25rem;
            font-size: 1.5rem;
            font-weight: 600;
            text-align: center;
            background-color: rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        #sidebar-wrapper .list-group {
            width: 100%;
        }

        #sidebar-wrapper .list-group-item {
            border: none;
            padding: 1rem 1.25rem;
            background-color: transparent;
            color: white;
            transition: background-color .2s ease-in-out;
            border-radius: 0;
            display: flex;
            align-items: center;
        }

        #sidebar-wrapper .list-group-item i {
            margin-right: 0.8rem;
            font-size: 1.1rem;
        }

        #sidebar-wrapper .list-group-item.active,
        #sidebar-wrapper .list-group-item:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
        }

        #page-content-wrapper {
            flex-grow: 1;
            /* Konten mengisi sisa ruang */
            width: 100%;
            /* Penting untuk memastikan flex-grow bekerja dengan baik */
            overflow-x: hidden;
            /* Mencegah scroll horizontal di area konten utama */
            overflow-y: auto;
            /* Memungkinkan scroll vertikal di area konten utama */
        }

        #wrapper.toggled #sidebar-wrapper {
            margin-left: 0;
        }

        @media (min-width: 768px) {
            #sidebar-wrapper {
                margin-left: 0;
            }

            #page-content-wrapper {
                width: 100%;
            }

            #wrapper.toggled #sidebar-wrapper {
                margin-left: -17rem;
            }
        }

        /* Navbar Styling */
        .navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            padding: 0.8rem 1.5rem;
        }

        .navbar-brand {
            font-weight: 600;
            color: #00bcd4 !important;
        }

        .navbar-toggler {
            border-color: rgba(0, 0, 0, 0.1);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%280, 188, 212, 0.75%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .btn-toggle-sidebar {
            background-color: #00bcd4;
            color: white;
            border-radius: 50px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s ease;
        }

        .btn-toggle-sidebar:hover {
            background-color: #00a4bd;
            color: white;
        }

        /* Styling Card untuk konten */
        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .card-header {
            background-color: #00bcd4;
            color: white;
            font-weight: 500;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            padding: 1rem 1.5rem;
        }

        /* Area konten utama */
        .main-content-area {
            padding: 1.5rem;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        /* Penyesuaian untuk tabel responsif agar tidak menyebabkan overflow global */
        .table-responsive {
            overflow-x: auto;
            /* Memastikan scroll horizontal hanya terjadi di dalam div ini */
            -webkit-overflow-scrolling: touch;
            /* Untuk smooth scrolling di iOS */
        }
    </style>
</head>

<body>
    <div class="d-flex" id="wrapper">

        <!-- Sidebar Dimulai -->
        <div class="border-end" id="sidebar-wrapper">
            <div class="sidebar-heading">Sistem SPP</div>
            <div class="list-group list-group-flush">
                @auth
                    {{-- Sidebar untuk Admin --}}
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}"
                            class="list-group-item list-group-item-action {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-fw fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a href="{{ route('admin.users.index') }}"
                            class="list-group-item list-group-item-action {{ Request::routeIs('admin.users.index') ? 'active' : '' }}">
                            <i class="fas fa-fw fa-user-cog"></i> Manajemen User
                        </a>
                        <a href="{{ route('admin.siswa.index') }}"
                            class="list-group-item list-group-item-action {{ Request::routeIs('admin.siswa.*') ? 'active' : '' }}">
                            <i class="fas fa-fw fa-users"></i> Manajemen Siswa
                        </a>
                        <a href="{{ route('admin.kelas.index') }}"
                            class="list-group-item list-group-item-action {{ Request::routeIs('admin.kelas.*') ? 'active' : '' }}">
                            <i class="fas fa-fw fa-school"></i> Manajemen Kelas
                        </a>
                        <a href="{{ route('admin.laporan.index') }}"
                            class="list-group-item list-group-item-action {{ Request::routeIs('admin.laporan.*') ? 'active' : '' }}">
                            <i class="fas fa-fw fa-file-invoice-dollar"></i> Laporan Pembayaran
                        </a>
                        {{-- <a href="{{ route('admin.tagihan.index') }}"
                            class="list-group-item list-group-item-action {{ Request::routeIs('admin.tagihan.*') ? 'active' : '' }}">
                            <i class="fas fa-fw fa-file-invoice-dollar"></i> Input Tagihan
                        </a> --}}
                        <form action="{{ route('logout') }}" method="POST" class="w-100">
                            @csrf
                            <button type="submit" class="list-group-item list-group-item-action text-start">
                                <i class="fas fa-fw fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    @endif

                    {{-- Sidebar untuk Bendahara (Akan diaktifkan nanti) --}}
                    @if(Auth::user()->role === 'bendahara')
                        <a href="{{ route('bendahara.dashboard') }}"
                            class="list-group-item list-group-item-action {{ Request::routeIs('bendahara.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-fw fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a href="{{ route('bendahara.pembayaran.index') }}"
                            class="list-group-item list-group-item-action {{ Request::routeIs('bendahara.pembayaran.*') ? 'active' : '' }}">
                            <i class="fas fa-fw fa-cash-register"></i> Catat Pembayaran
                        </a>
                        <a href="{{ route('bendahara.verifikasi.index') }}"
                            class="list-group-item list-group-item-action {{ Request::routeIs('bendahara.verifikasi.*') ? 'active' : '' }}">
                            <i class="fas fa-fw fa-check-circle"></i> Verifikasi Pembayaran
                        </a>
                        <a href="{{ route('bendahara.laporan.index') }}"
                            class="list-group-item list-group-item-action {{ Request::routeIs('bendahara.laporan.*') ? 'active' : '' }}">
                            <i class="fas fa-fw fa-chart-line"></i> Laporan Keuangan
                        </a>
                        {{-- Logout Form --}}
                        <form action="{{ route('logout') }}" method="POST" class="w-100">
                            @csrf
                            <button type="submit" class="list-group-item list-group-item-action text-start">
                                <i class="fas fa-fw fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    @endif

                    {{-- Sidebar untuk Siswa (Akan diaktifkan nanti) --}}
                    @if(Auth::user()->role === 'siswa')
                        <a href="{{ route('siswa.dashboard') }}"
                            class="list-group-item list-group-item-action {{ Request::routeIs('siswa.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-fw fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a href="{{ route('siswa.tagihan.index') }}"
                            class="list-group-item list-group-item-action {{ Request::routeIs('siswa.tagihan.*') ? 'active' : '' }}">
                            <i class="fas fa-fw fa-file-invoice"></i> Tagihan Saya
                        </a>
                        <a href="{{ route('siswa.upload.create') }}"
                            class="list-group-item list-group-item-action {{ Request::routeIs('siswa.upload.*') ? 'active' : '' }}">
                            <i class="fas fa-fw fa-upload"></i> Upload Bukti
                        </a>
                        <a href="{{ route('siswa.riwayat.index') }}"
                            class="list-group-item list-group-item-action {{ Request::routeIs('siswa.riwayat.*') ? 'active' : '' }}">
                            <i class="fas fa-fw fa-history"></i> Riwayat Pembayaran
                        </a>
                        {{-- Form Logout --}}
                        <form action="{{ route('logout') }}" method="POST" class="w-100">
                            @csrf
                            <button type="submit" class="list-group-item list-group-item-action text-start">
                                <i class="fas fa-fw fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    @endif
                @else
                    {{-- Tampilkan link login jika belum login --}}
                    <a href="{{ route('login') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-fw fa-sign-in-alt"></i> Login
                    </a>
                @endauth
            </div>
        </div>
        <!-- Sidebar Selesai -->

        <!-- Konten Halaman Dimulai -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white py-4 border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-toggle-sidebar" id="sidebarToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <a class="navbar-brand ms-3" href="#">@yield('title_page', 'Halaman Aplikasi')</a>

                    <div class="ms-auto me-3">
                        @auth
                            <span class="d-none d-md-inline-block">Halo, <strong>{{ Auth::user()->name }}</strong>
                                ({{ Auth::user()->role }})</span>
                            <span class="d-inline-block d-md-none"><i class="fas fa-user-circle fa-lg"></i></span>
                        @endauth
                    </div>
                </div>
            </nav>

            <div class="container-fluid mt-4">
                @yield('content')
            </div>
        </div>
        <!-- Konten Halaman Selesai -->

    </div>
    <!-- Wrapper Selesai -->

    <!-- Bootstrap core JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
        @stack('scripts') {{-- Ini penting untuk menampung script dari halaman lain --}}

    <!-- Script untuk Toggle Sidebar -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function () {
                    document.getElementById('wrapper').classList.toggle('toggled');
                });
            }
        });
    </script>
    @stack('scripts') {{-- Tempat untuk script tambahan dari child views --}}
</body>

</html>