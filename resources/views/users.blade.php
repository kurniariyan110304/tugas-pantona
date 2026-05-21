<!DOCTYPE html>
<html>
<head>
    <title>CMS User</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>CMS User</h3>

        <button type="button" id="btn-logout" class="btn btn-secondary">
            Logout
        </button>
    </div>

    <div id="alert-message"></div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 id="form-title">Tambah User</h5>
        </div>

        <div class="card-body">
            <input type="hidden" id="user_id">

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Email</label>
                    <input type="text" id="email" class="form-control" placeholder="Email">
                    <small class="text-danger error-message" id="error-email"></small>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Nama</label>
                    <input type="text" id="nama" class="form-control" placeholder="Nama">
                    <small class="text-danger error-message" id="error-nama"></small>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Password</label>

                    <div class="position-relative">
                        <input type="password" id="password" class="form-control pe-5" placeholder="Password">

                        <span id="toggle-user-password"
                              class="position-absolute top-50 end-0 translate-middle-y me-3"
                              style="cursor: pointer;">
                            <i class="bi bi-eye-slash" id="icon-user-password"></i>
                        </span>
                    </div>

                    <small id="password-help" class="text-muted">
                        Password wajib diisi saat tambah user.
                    </small>
                    <br>
                    <small class="text-danger error-message" id="error-password"></small>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Image Profile</label>
                    <input type="file" id="image" class="form-control" accept="image/*">
                    <small class="text-danger error-message" id="error-image"></small>
                </div>
            </div>

            <button type="button" id="btn-save" class="btn btn-primary">
                Simpan
            </button>

            <button type="button" id="btn-reset" class="btn btn-warning">
                Reset
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>List User</h5>
        </div>

        <div class="card-body">
            <table id="user-table" class="table table-bordered table-striped w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Nama</th>
                        <th>Image</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>

<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let table = $('#user-table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ajax: "{{ route('users.datatable') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'email', name: 'email' },
            { data: 'nama', name: 'nama' },
            { data: 'image', name: 'image', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    function clearError(field) {
        $('#error-' + field).text('');
        $('#' + field).removeClass('is-invalid');
    }

    function showError(field, message) {
        $('#error-' + field).text(message);
        $('#' + field).addClass('is-invalid');
    }

    function clearAllErrors() {
        $('.error-message').text('');
        $('.form-control').removeClass('is-invalid');
    }

    function showMessage(type, message) {
        $('#alert-message').html(`
            <div class="alert alert-${type} alert-dismissible fade show">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
    }

    function resetForm() {
        $('#user_id').val('');
        $('#email').val('');
        $('#nama').val('');
        $('#password').val('');
        $('#image').val('');

        $('#form-title').text('Tambah User');
        $('#btn-save').text('Simpan');
        $('#password-help').text('Password wajib diisi saat tambah user.');

        $('#password').attr('type', 'password');
        $('#icon-user-password').removeClass('bi-eye').addClass('bi-eye-slash');

        clearAllErrors();
    }

    $('#email').on('input', function () {
        clearError('email');
    });

    $('#nama').on('input', function () {
        clearError('nama');
    });

    $('#password').on('input', function () {
        clearError('password');
    });

    $('#image').on('change', function () {
        clearError('image');
    });

    $('#toggle-user-password').on('click', function () {
        let passwordInput = $('#password');
        let icon = $('#icon-user-password');

        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        } else {
            passwordInput.attr('type', 'password');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        }
    });

    $('#btn-save').on('click', function () {
        clearAllErrors();

        let userId = $('#user_id').val();

        let formData = new FormData();
        formData.append('email', $('#email').val());
        formData.append('nama', $('#nama').val());
        formData.append('password', $('#password').val());

        if ($('#image')[0].files[0]) {
            formData.append('image', $('#image')[0].files[0]);
        }

        let url = userId
            ? "/users/update/" + userId
            : "{{ route('users.store') }}";

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $('#btn-save').text('Loading...').prop('disabled', true);
            },
            success: function (response) {
                showMessage('success', response.message);
                resetForm();
                table.ajax.reload(null, false);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;

                    if (errors.email) {
                        showError('email', errors.email[0]);
                    }

                    if (errors.nama) {
                        showError('nama', errors.nama[0]);
                    }

                    if (errors.password) {
                        showError('password', errors.password[0]);
                    }

                    if (errors.image) {
                        showError('image', errors.image[0]);
                    }
                } else {
                    showMessage('danger', 'Terjadi kesalahan pada server.');
                }
            },
            complete: function () {
                $('#btn-save').text(userId ? 'Update' : 'Simpan').prop('disabled', false);
            }
        });
    });

    $('#user-table').on('click', '.btn-edit', function () {
        let id = $(this).data('id');

        clearAllErrors();

        $.ajax({
            url: "/users/edit/" + id,
            type: "GET",
            success: function (response) {
                let user = response.data;

                $('#user_id').val(user.id);
                $('#email').val(user.email);
                $('#nama').val(user.nama);
                $('#password').val('');
                $('#image').val('');

                $('#form-title').text('Edit User');
                $('#btn-save').text('Update');
                $('#password-help').text('Kosongkan password jika tidak ingin diubah.');

                $('#password').attr('type', 'password');
                $('#icon-user-password').removeClass('bi-eye').addClass('bi-eye-slash');

                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            },
            error: function () {
                showMessage('danger', 'Gagal mengambil data user.');
            }
        });
    });

    $('#user-table').on('click', '.btn-delete', function () {
        let id = $(this).data('id');

        if (!confirm('Yakin ingin menghapus user ini?')) {
            return;
        }

        $.ajax({
            url: "/users/delete/" + id,
            type: "DELETE",
            success: function (response) {
                showMessage('success', response.message);
                table.ajax.reload(null, false);
            },
            error: function () {
                showMessage('danger', 'User gagal dihapus.');
            }
        });
    });

    $('#btn-reset').on('click', function () {
        resetForm();
    });

    $('#btn-logout').on('click', function () {
        $.ajax({
            url: "{{ route('logout') }}",
            type: "POST",
            success: function (response) {
                window.location.href = response.redirect;
            },
            error: function () {
                showMessage('danger', 'Logout gagal.');
            }
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>