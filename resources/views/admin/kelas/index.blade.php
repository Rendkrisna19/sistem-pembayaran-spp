@extends('layouts.app')

@section('title', 'Manajemen Kelas')
@section('title_page', 'Manajemen Kelas')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <span>Daftar Kelas</span>
            {{-- Tombol ini sekarang memicu modal --}}
            <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addKelasModal">
                <i class="fas fa-plus"></i> Tambah Kelas
            </button>
        </div>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success" role="alert">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Nama Kelas</th>
                        <th>Jurusan</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kelas as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->nama_kelas }}</td>
                            <td>{{ $item->jurusan }}</td>
                            <td>
                                {{-- Tombol Edit dengan data-attributes untuk JS --}}
                                <button type="button" class="btn btn-sm btn-primary edit-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editKelasModal"
                                        data-id="{{ $item->id }}"
                                        data-nama_kelas="{{ $item->nama_kelas }}"
                                        data-jurusan="{{ $item->jurusan }}"
                                        data-update-url="{{ route('admin.kelas.update', $item->id) }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form action="{{ route('admin.kelas.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada data kelas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addKelasModal" tabindex="-1" aria-labelledby="addKelasModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addKelasModalLabel">Tambah Kelas Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.kelas.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_kelas" class="form-label">Nama Kelas</label>
                        <input type="text" class="form-control" id="nama_kelas" name="nama_kelas" placeholder="Contoh: 10 Akuntansi 1" required>
                    </div>
                    <div class="mb-3">
                        <label for="jurusan" class="form-label">Jurusan</label>
                        <input type="text" class="form-control" id="jurusan" name="jurusan" placeholder="Contoh: Akuntansi" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editKelasModal" tabindex="-1" aria-labelledby="editKelasModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editKelasModalLabel">Edit Data Kelas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- Form action akan diisi oleh JavaScript --}}
            <form id="editKelasForm" method="POST"> 
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nama_kelas" class="form-label">Nama Kelas</label>
                        <input type="text" class="form-control" id="edit_nama_kelas" name="nama_kelas" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_jurusan" class="form-label">Jurusan</label>
                        <input type="text" class="form-control" id="edit_jurusan" name="jurusan" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Script untuk mengisi data ke modal edit
        const editKelasModal = document.getElementById('editKelasModal');
        editKelasModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Tombol yang memicu modal
            
            // Ambil data dari data-* attributes
            const namaKelas = button.getAttribute('data-nama_kelas');
            const jurusan = button.getAttribute('data-jurusan');
            const updateUrl = button.getAttribute('data-update-url');

            // Ambil elemen form dan input di dalam modal
            const form = editKelasModal.querySelector('#editKelasForm');
            const namaKelasInput = editKelasModal.querySelector('#edit_nama_kelas');
            const jurusanInput = editKelasModal.querySelector('#edit_jurusan');
            
            // Set action form dan value dari input
            form.action = updateUrl;
            namaKelasInput.value = namaKelas;
            jurusanInput.value = jurusan;
        });

        // Jika ada error validasi, buka kembali modal tambah
        @if($errors->any())
            var addModal = new bootstrap.Modal(document.getElementById('addKelasModal'), {
                keyboard: false
            });
            addModal.show();
        @endif
    });
</script>
@endpush