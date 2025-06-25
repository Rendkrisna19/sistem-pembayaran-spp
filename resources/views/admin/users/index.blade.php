@extends('layouts.app') {{-- Menggunakan layout utama yang sudah dibuat --}}

@section('title', 'Manajemen Pengguna') {{-- Judul untuk tab browser --}}
@section('title_page', 'Manajemen Pengguna') {{-- Judul halaman di navbar --}}

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                Daftar Pengguna
                {{-- Tombol untuk membuka modal "Tambah Pengguna" --}}
                <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#createUserModal">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Pengguna
                </button>
            </div>
            <div class="card-body">
                {{-- Pesan Sukses/Error --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td><span class="badge 
                                    @if($user->role === 'admin') bg-danger 
                                    @elseif($user->role === 'bendahara') bg-primary 
                                    @else bg-info 
                                    @endif">{{ ucfirst($user->role) }}</span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-warning me-1 edit-user-btn" 
                                            data-bs-toggle="modal" data-bs-target="#editUserModal" 
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-role="{{ $user->role }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    {{-- Jangan biarkan admin menghapus dirinya sendiri --}}
                                    @if(Auth::user()->id !== $user->id)
                                    <button type="button" class="btn btn-sm btn-danger delete-user-btn" 
                                            data-bs-toggle="modal" data-bs-target="#deleteUserModal" 
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                    @else
                                    <button type="button" class="btn btn-sm btn-secondary" disabled>
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data pengguna.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal untuk Tambah Pengguna Baru (pastikan id dan namanya unik untuk validasi old()) --}}
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white" style="background-color: #00bcd4 !important;">
                <h5 class="modal-title" id="createUserModalLabel">Tambah Pengguna Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="create_name" class="form-label">Nama Pengguna</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="create_name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="create_email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="create_email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="create_password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="create_password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="create_password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="create_password_confirmation" name="password_confirmation" required>
                    </div>
                    <div class="mb-3">
                        <label for="create_role" class="form-label">Pilih Role</label>
                        <select class="form-select @error('role') is-invalid @enderror" id="create_role" name="role" required>
                            <option value="">Pilih Role</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="bendahara" {{ old('role') == 'bendahara' ? 'selected' : '' }}>Bendahara</option>
                            <option value="siswa" {{ old('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" style="background-color: #00bcd4; border-color: #00bcd4;">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal untuk Edit Pengguna --}}
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="editUserModalLabel">Edit Pengguna</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT') {{-- Penting untuk metode PUT --}}
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nama Pengguna</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="edit_name" name="name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="edit_email" name="email" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Password (Kosongkan jika tidak ingin diubah)</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="edit_password" name="password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="edit_password_confirmation" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="edit_password_confirmation" name="password_confirmation">
                    </div>
                    <div class="mb-3">
                        <label for="edit_role" class="form-label">Pilih Role</label>
                        <select class="form-select @error('role') is-invalid @enderror" id="edit_role" name="role" required>
                            <option value="">Pilih Role</option>
                            <option value="admin">Admin</option>
                            <option value="bendahara">Bendahara</option>
                            <option value="siswa">Siswa</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Perbarui Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal untuk Hapus Pengguna --}}
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteUserModalLabel">Konfirmasi Hapus Pengguna</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteUserForm" method="POST">
                @csrf
                @method('DELETE') {{-- Penting untuk metode DELETE --}}
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus pengguna <strong id="deleteUserName"></strong>?
                    Tindakan ini tidak dapat dibatalkan.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script untuk menampilkan modal jika ada error validasi --}}
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fungsi untuk menghapus semua pesan error validasi
        function clearValidationErrors(formElement) {
            formElement.querySelectorAll('.is-invalid').forEach(function(input) {
                input.classList.remove('is-invalid');
            });
            formElement.querySelectorAll('.invalid-feedback').forEach(function(feedback) {
                feedback.textContent = '';
            });
        }

        // --- Logic untuk Modal Tambah Pengguna ---
        var createUserModalElement = document.getElementById('createUserModal');
        var createUserForm = createUserModalElement.querySelector('form');

        createUserModalElement.addEventListener('hidden.bs.modal', function () {
            // Bersihkan form dan error saat modal ditutup
            createUserForm.reset();
            clearValidationErrors(createUserForm);
        });

        @if ($errors->any())
            // Jika ada error validasi, tentukan modal mana yang harus dibuka
            @php
                $showCreateModal = $errors->has('name') || $errors->has('email') || $errors->has('password') || $errors->has('role');
                $showEditModal = (old('_method') == 'PUT' || old('_method') == 'PATCH') && old('user_id');
            @endphp

            @if ($showCreateModal && !$showEditModal)
                // Hanya tampilkan modal buat jika error bukan dari edit
                var createUserModal = new bootstrap.Modal(createUserModalElement);
                createUserModal.show();
            @elseif ($showEditModal)
                // Tampilkan modal edit jika error berasal dari edit
                var editUserModal = new bootstrap.Modal(document.getElementById('editUserModal'));
                editUserModal.show();
                // Isi kembali form edit dengan old data jika memungkinkan
                document.getElementById('edit_user_id').value = "{{ old('user_id') }}";
                document.getElementById('edit_name').value = "{{ old('name') }}";
                document.getElementById('edit_email').value = "{{ old('email') }}";
                document.getElementById('edit_role').value = "{{ old('role') }}";
                // Update form action untuk edit modal agar sesuai dengan user yang gagal
                document.getElementById('editUserForm').action = "/admin/users/" + "{{ old('user_id') }}";
            @endif
        @endif


        // --- Logic untuk Modal Edit Pengguna ---
        var editUserModalElement = document.getElementById('editUserModal');
        var editUserForm = document.getElementById('editUserForm');

        editUserModalElement.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Tombol yang memicu modal
            var userId = button.getAttribute('data-id');
            var userName = button.getAttribute('data-name');
            var userEmail = button.getAttribute('data-email');
            var userRole = button.getAttribute('data-role');

            var modalTitle = editUserModalElement.querySelector('.modal-title');
            var inputId = document.getElementById('edit_user_id');
            var inputName = document.getElementById('edit_name');
            var inputEmail = document.getElementById('edit_email');
            var selectRole = document.getElementById('edit_role');
            var inputPassword = document.getElementById('edit_password');
            var inputPasswordConfirmation = document.getElementById('edit_password_confirmation');

            modalTitle.textContent = 'Edit Pengguna: ' + userName;
            editUserForm.action = `/admin/users/${userId}`; // Atur action form sesuai user ID
            inputId.value = userId;
            inputName.value = userName;
            inputEmail.value = userEmail;
            selectRole.value = userRole;

            // Kosongkan field password saat modal dibuka untuk edit
            inputPassword.value = '';
            inputPasswordConfirmation.value = '';

            // Hapus kelas is-invalid dan pesan feedback dari validasi sebelumnya
            clearValidationErrors(editUserForm);
        });
        editUserModalElement.addEventListener('hidden.bs.modal', function () {
            // Bersihkan form dan error saat modal ditutup
            editUserForm.reset();
            clearValidationErrors(editUserForm);
        });


        // --- Logic untuk Modal Hapus Pengguna ---
        var deleteUserModalElement = document.getElementById('deleteUserModal');
        var deleteUserForm = document.getElementById('deleteUserForm');

        deleteUserModalElement.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Tombol yang memicu modal
            var userId = button.getAttribute('data-id');
            var userName = button.getAttribute('data-name');

            var modalText = document.getElementById('deleteUserName');
            
            modalText.textContent = userName;
            deleteUserForm.action = `/admin/users/${userId}`; // Atur action form sesuai user ID
        });
    });
</script>
@endpush
