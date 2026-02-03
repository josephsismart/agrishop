<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $system_title ?> | <?= $page_title ?></title>

    <link rel="icon" type="image/png" href="<?= $system_svg ?>">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="<?= base_url() ?>dist/css/fonts.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url() ?>plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?= base_url() ?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url() ?>dist/css/adminlte.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>plugins/bootstrap-alpha3/bootstrap.min.css">

</head>
<style>
    .form-floating>label {
        font-size: 0.875rem;
    }
</style>

<body class="hold-transition login-page">
    <div class="login-box">

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center bg-white">
            <!-- <img class="animation__shake" src="<?= $system_svg ?>" alt="AdminLTELogo" height="500" width="500"/> -->
            <img src="<?php echo base_url(); ?>dist/layout_shop/images/logo.svg" width="240" height="70" alt="logo">
            <!-- Agusan National High School Information System  -->
        </div>
        <!-- /.login-logo -->
        <div class="card card-outline card-success">
            <div class="card-header text-center">
                <a href="<?= base_url() ?>index" class="d-block">
                    <img src="<?php echo base_url(); ?>dist/layout_shop/images/logo.svg" width="240" height="70" alt="logo">
                </a>
            </div>
            <div class="card-body">
                <?php if ($this->input->get("login_attempt") == md5(0) || $this->input->get("login_attempt") == md5(1)) : ?>
                    <p class="text-danger text-center text-sm"><i class="fa fa-exclamation-triangle"></i> Invalid Username or Password. Please try again.</p>
                <?php endif ?>
                <?php if ($this->input->get("login_attempt") != md5(0) || $this->input->get("login_attempt") != md5(1)) : ?>
                    <p class="login-box-msg">Sign in to start your session</p>
                <?php endif ?>

                <form action="<?= base_url() ?>requestlogin" method="post" class="needs-validation" novalidate>
                    <!-- Username/Email Field -->
                    <div class="mb-4">
                        <div class="form-floating">
                            <input type="text" name="username" class="form-control form-control border-1 <?= ($this->input->get("login_attempt") == md5(0) || $this->input->get("login_attempt") == md5(1)) ? 'is-invalid border-danger' : 'border-primary' ?>" id="loginUsername" placeholder="Email or Username" autocomplete="off" required autofocus>
                            <label for="loginUsername" class="text-muted">
                                <i class="fas fa-envelope me-2"></i>Email or Username
                            </label>
                            <?php if ($this->input->get("login_attempt") == md5(0) || $this->input->get("login_attempt") == md5(1)) : ?>
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>Invalid username or password
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="mb-4">
                        <div class="form-floating">
                            <input type="password" name="password" class="form-control form-control border-1 <?= ($this->input->get("login_attempt") == md5(0) || $this->input->get("login_attempt") == md5(1)) ? 'is-invalid border-danger' : 'border-primary' ?>" id="loginPassword" placeholder="Password" autocomplete="off" required>
                            <label for="loginPassword" class="text-muted">
                                <i class="fas fa-lock me-2"></i>Password
                            </label>
                        </div>
                    </div>

                    <!-- Login Button -->
                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-success fw-bold py-2 shadow-sm">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In to Account
                        </button>
                    </div>

                    <!-- Divider -->
                    <div class="position-relative my-4">
                        <hr class="border-1">
                        <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">
                            OR
                        </span>
                    </div>

                    <!-- Signup Button -->
                    <div class="d-grid">
                        <a href="<?= base_url() ?>signup" class="btn btn-outline-dark py-2">
                            <i class="fas fa-user-plus me-2"></i>Create New Account
                        </a>
                    </div>
                </form>
                <!-- /.social-auth-links -->

                <!-- <p class="mb-1">
            <a href="forgot-password.html">I forgot my password</a>
          </p> -->

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="<?= base_url() ?>plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?= base_url() ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?= base_url() ?>dist/js/adminlte.min.js"></script>

</body>

</html>