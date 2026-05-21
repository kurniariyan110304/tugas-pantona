<!DOCTYPE html>
<html>
<head>
    <title>Login User</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">

            <div class="card shadow">
                <div class="card-header text-center">
                    <h4>Login User</h4>
                </div>

                <div class="card-body">
                    <div id="alert-login"></div>

                    <div class="mb-3">
                        <label>Email</label>

                        <input type="text" id="email" class="form-control" placeholder="admin@gmail.com">

                        <small class="text-danger error-message" id="error-email"></small>
                    </div>

                    <div class="mb-3">
                        <label>Password</label>

                        <div class="position-relative">
                            <input type="password" id="password" class="form-control pe-5" placeholder="Masukkan password">

                            <span id="toggle-password"
                                  class="position-absolute top-50 end-0 translate-middle-y me-3"
                                  style="cursor:pointer;">
                                <i class="bi bi-eye-slash" id="icon-password"></i>
                            </span>
                        </div>

                        <small class="text-danger error-message" id="error-password"></small>
                    </div>

                    <button type="button" id="btn-login" class="btn btn-primary w-100">
                        Login
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
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
        $('#alert-login').html('');
    }

    $('#email').on('input', function () {
        clearError('email');
    });

    $('#password').on('input', function () {
        clearError('password');
    });

    $('#toggle-password').on('click', function () {
        let passwordInput = $('#password');
        let icon = $('#icon-password');

        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        } else {
            passwordInput.attr('type', 'password');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        }
    });

    $('#btn-login').on('click', function () {
        clearAllErrors();

        let email = $('#email').val();
        let password = $('#password').val();

        $.ajax({
            url: "{{ route('login.process') }}",
            type: "POST",
            data: {
                email: email,
                password: password
            },
            beforeSend: function () {
                $('#btn-login').text('Loading...').prop('disabled', true);
            },
            success: function (response) {
                window.location.href = response.redirect;
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;

                    if (errors.email) {
                        showError('email', errors.email[0]);
                    }

                    if (errors.password) {
                        showError('password', errors.password[0]);
                    }
                } else {
                    let message = 'Login gagal';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }

                    $('#alert-login').html(`
                        <div class="alert alert-danger">${message}</div>
                    `);
                }
            },
            complete: function () {
                $('#btn-login').text('Login').prop('disabled', false);
            }
        });
    });
});
</script>

</body>
</html>