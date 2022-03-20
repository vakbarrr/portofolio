<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?= base_url('assets/images/logo.png') ?>">

    <title>Portofolio </title>

    <!-- Custom fonts for this template-->
    <link href="<?= base_url('assets/sbadmin/') ?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?= base_url('assets/sbadmin/') ?>css/sb-admin-2.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/sbadmin/') ?>vendor/sweetalert2/sweetalert2.css" rel="stylesheet">

</head>

<body class="">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block">
                                <img src="<?= base_url(); ?>assets/images/home-about-left.png" alt="" class="img-fluid">
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                    </div>
                                    <form class="user">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" name="username" id="exampleInputEmail" aria-describedby="emailHelp" placeholder="Enter usernme...">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" name="password" id="exampleInputPassword" placeholder="Password">
                                        </div>
                                        <button type="submit" id="submit" class="btn btn-warning btn-user btn-block">
                                            Login
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="forgot-password.html">Forgot Password?</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="<?= base_url('assets/sbadmin/') ?>vendor/jquery/jquery.min.js"></script>
    <script src="<?= base_url('assets/sbadmin/') ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?= base_url('assets/sbadmin/') ?>vendor/jquery-easing/jquery.easing.min.js"></script>

    <script src="<?= base_url('assets/sbadmin/') ?>vendor/sweetalert2/sweetalert2.all.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="<?= base_url('assets/sbadmin/') ?>js/sb-admin-2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('form').on('submit', function(e) {
                e.preventDefault();
                var btn = $('#submit');
                $.ajax({
                    url: '<?= base_url(); ?>admin/login/proseslog', //nama action script php sobat
                    data: new FormData(this),
                    type: 'POST',
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        // btn.button('loading');
                        btn.html('Loading...');
                    },
                    complete: function() {
                        btn.html('Log in');
                    },
                    success: function(data) {
                        console.log(data);
                        $('.form-group').removeClass('has-error');
                        $('.text-danger').remove();
                        if (data['error']) {
                            var text = '';
                            for (i in data['error']) {
                                $('input[name=\'' + i + '\']').closest('.form-group').addClass('has-error');
                                $('input[name=\'' + i + '\']').after('<small class="text-danger"><i>' + data['error'][i] + '</i></small>');
                            }
                        } else if (data['success']) {
                            window.location.replace(data['redirect']);
                        } else if (data['gagal_login']) {
                             Swal.fire("Gagal", "Periksa kembali username & password :(", "warning");
                        } else {
                             Swal.fire("Oops...", "Something went wrong :(", "error");
                        }
                    },
                });
            });
        });
    </script>
</body>

</html>