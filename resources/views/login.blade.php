<!DOCTYPE html>
<html>
<head>
    <title>Login User</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                        <input type="email" id="email" class="form-control" placeholder="Masukkan email">
                    </div>

                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" id="password" class="form-control" placeholder="Masukkan password">
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

    $('#btn-login').on('click', function () {
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
                let message = 'Login gagal';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                $('#alert-login').html(`
                    <div class="alert alert-danger">${message}</div>
                `);
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