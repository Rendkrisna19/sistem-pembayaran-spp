@extends('layouts.app')

@section('title', 'Manajemen Siswa')
@section('title_page', 'Manajemen Siswa')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <span>Daftar Siswa</span>
            <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addSiswaModal">
                <i class="fas fa-plus"></i> Tambah Siswa
            </button>
        </div>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success" role="alert">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Error!</strong>
                <ul>
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
                        <th>#</th>
                        <th>NISN</th>
                        <th>Nama Lengkap</th>
                        <th>Email Akun</th>
                        <th>Kelas</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($siswas as $siswa)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $siswa->nisn }}</td>
                            <td>{{ $siswa->nama_lengkap }}</td>
                            <td>{{ $siswa->user->email }}</td>
                            <td>{{ $siswa->kelas->nama_kelas }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary edit-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editSiswaModal"
                                    data-id="{{ $siswa->id }}"
                                    data-nisn="{{ $siswa->nisn }}"
                                    data-nama_lengkap="{{ $siswa->nama_lengkap }}"
                                    data-email="{{ $siswa->user->email }}"
                                    data-kelas_id="{{ $siswa->kelas_id }}"
                                    data-jenis_kelamin="{{ $siswa->jenis_kelamin }}"
                                    data-alamat="{{ $siswa->alamat }}"
                                    data-update-url="{{ route('admin.siswa.update', $siswa->id) }}">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form action="{{ route('admin.siswa.destroy', $siswa->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin menghapus siswa ini? Akun login siswa juga akan terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">Tidak ada data siswa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addSiswaModal" tabindex="-1" aria-labelledby="addSiswaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.siswa.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addSiswaModalLabel">Tambah Siswa Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Form Fields --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nisn" class="form-label">NISN</label>
                            <input type="text" class="form-control" name="nisn" value="{{ old('nisn') }}" required>
                        </div>
                    </div>
                     <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="kelas_id" class="form-label">Kelas</label>
                            <select name="kelas_id" class="form-select" required>
                                <option value="" disabled selected>Pilih Kelas</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="" disabled selected>Pilih Jenis Kelamin</option>
                                <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                    </div>
                     <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea name="alamat" rows="2" class="form-control">{{ old('alamat') }}</textarea>
                    </div>
                    <hr>
                    <h5>Data Akun Login Siswa</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Alamat Email</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" name="password_confirmation" required>
                        </div>
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

<div class="modal fade" id="editSiswaModal" tabindex="-1" aria-labelledby="editSiswaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editSiswaForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editSiswaModalLabel">Edit Data Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Form Fields --}}
                    <div class="row">
                        <div class="col-md-6 mb-3"><label for="edit_nama_lengkap" class="form-label">Nama Lengkap</label><input type="text" id="edit_nama_lengkap" class="form-control" name="nama_lengkap" required></div>
                        <div class="col-md-6 mb-3"><label for="edit_nisn" class="form-label">NISN</label><input type="text" id="edit_nisn" class="form-control" name="nisn" required></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_kelas_id" class="form-label">Kelas</label>
                            <select name="kelas_id" id="edit_kelas_id" class="form-select" required>
                                <option value="" disabled>Pilih Kelas</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_jenis_kelamin" class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="edit_jenis_kelamin" class="form-select" required>
                                <option value="" disabled>Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3"><label for="edit_alamat" class="form-label">Alamat</label><textarea name="alamat" id="edit_alamat" rows="2" class="form-control"></textarea></div>
                    <hr>
                    <h5>Data Akun Login <small class="text-muted">(Kosongkan password jika tidak ingin diubah)</small></h5>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label for="edit_email" class="form-label">Alamat Email</label><input type="email" id="edit_email" class="form-control" name="email" required></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3"><label for="edit_password" class="form-label">Password Baru</label><input type="password" id="edit_password" class="form-control" name="password"></div>
                        <div class="col-md-6 mb-3"><label for="edit_password_confirmation" class="form-label">Konfirmasi Password Baru</label><input type="password" id="edit_password_confirmation" class="form-control" name="password_confirmation"></div>
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
    const editSiswaModal = document.getElementById('editSiswaModal');
    editSiswaModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        
        // Ambil data dari tombol
        const updateUrl = button.getAttribute('data-update-url');
        const nisn = button.getAttribute('data-nisn');
        const namaLengkap = button.getAttribute('data-nama_lengkap');
        const email = button.getAttribute('data-email');
        const kelasId = button.getAttribute('data-kelas_id');
        const jenisKelamin = button.getAttribute('data-jenis_kelamin');
        const alamat = button.getAttribute('data-alamat');

        // Isi form di modal
        const form = editSiswaModal.querySelector('#editSiswaForm');
        form.action = updateUrl;
        form.querySelector('#edit_nisn').value = nisn;
        form.querySelector('#edit_nama_lengkap').value = namaLengkap;
        form.querySelector('#edit_email').value = email;
        form.querySelector('#edit_kelas_id').value = kelasId;
        form.querySelector('#edit_jenis_kelamin').value = jenisKelamin;
        form.querySelector('#edit_alamat').value = alamat;
    });

    // Jika ada error validasi saat MENAMBAH, buka kembali modal tambah
    @if($errors->any())
        var addSiswaModal = new bootstrap.Modal(document.getElementById('addSiswaModal'), {
            keyboard: false
        });
        addSiswaModal.show();
    @endif
});
</script>
@endpush