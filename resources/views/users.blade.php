<!DOCTYPE html>
<html>
<head>
    <title>CMS User</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css" rel="stylesheet">
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
                    <input type="email" id="email" class="form-control" placeholder="Email">
                </div>

                <div class="col-md-4 mb-3">
                    <label>Nama</label>
                    <input type="text" id="nama" class="form-control" placeholder="Nama">
                </div>

                <div class="col-md-4 mb-3">
                    <label>Password</label>
                    <input type="password" id="password" class="form-control" placeholder="Password">
                    <small id="password-help" class="text-muted">Password wajib diisi saat tambah user.</small>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Image Profile</label>
                    <input type="file" id="image" class="form-control" accept="image/*">
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

    function resetForm() {
        $('#user_id').val('');
        $('#email').val('');
        $('#nama').val('');
        $('#password').val('');
        $('#image').val('');
        $('#form-title').text('Tambah User');
        $('#btn-save').text('Simpan');
        $('#password-help').text('Password wajib diisi saat tambah user.');
    }

    function showMessage(type, message) {
        $('#alert-message').html(`
            <div class="alert alert-${type} alert-dismissible fade show">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
    }

    $('#btn-save').on('click', function () {
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
                let message = 'Terjadi kesalahan';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    message = '';

                    $.each(xhr.responseJSON.errors, function (key, value) {
                        message += value[0] + '<br>';
                    });
                }

                showMessage('danger', message);
            },
            complete: function () {
                $('#btn-save').text(userId ? 'Update' : 'Simpan').prop('disabled', false);
            }
        });
    });

    $('#user-table').on('click', '.btn-edit', function () {
        let id = $(this).data('id');

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

                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
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
                showMessage('danger', 'User gagal dihapus');
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
            }
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>