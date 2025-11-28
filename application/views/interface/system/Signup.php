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
    <link rel="stylesheet" href="<?= base_url() ?>plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url() ?>dist/css/adminlte.min.css">
</head>

<body class="hold-transition login-page">
    <div class="login-box">

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center" style="background-color:#ffffff;">
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
                <?php $signup_attempt = $this->input->get("signup_attempt");
                $firstname = $this->input->get("firstname");
                $lastname = $this->input->get("lastname"); ?>
                <?php if ($signup_attempt == md5(0)) : ?>
                    <p class="text-danger text-center text-sm"><i class="fa fa-exclamation-triangle"></i> Please input all required fields and try again.</p>
                <?php endif ?>
                <?php if ($signup_attempt == md5(1)) : ?>
                    <p class="text-warning text-center text-sm"><i class="fa fa-exclamation-triangle"></i> User already exists. Please try again.</p>
                <?php endif ?>
                
                <?= form_open(base_url('/requestsignup'), 'id=form_save_dataRequestSignup'); ?>
                <!-- <form action="<?= base_url() ?>requestsignup" method="post" id="form_save_dataRequestSignup"> -->
                    <div class="input-group mb-3">
                        <input type="text" name="firstname" class="form-control <?php if ($signup_attempt == md5(0)) : ?> is-invalid <?php endif ?>" style="text-transform: uppercase;" placeholder="FIRST NAME" autofocus autocomplete="off" value=<?php if ($signup_attempt == md5(1)) : echo $firstname;
                                                                                                                                                                                                                                                    endif ?>>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="lastname" class="form-control <?php if ($signup_attempt == md5(0)) : ?> is-invalid <?php endif ?>" style="text-transform: uppercase;" placeholder="LAST NAME" autofocus autocomplete="off" value=<?php if ($signup_attempt == md5(1)) : echo $lastname;
                                                                                                                                                                                                                                                    endif ?>>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="input-group mb-3">
                        <input type="date" name="birthdate" class="form-control" placeholder="Birthdate" autofocus autocomplete="off" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-birthday-cake"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="address" class="form-control" placeholder="Address" autofocus autocomplete="off" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-home"></span>
                            </div>
                        </div>
                    </div> -->
                    <div class="input-group mb-3 mt-5">
                        <input type="text" name="username" class="form-control <?php if ($signup_attempt == md5(0)) : ?> is-invalid 
                            <?php elseif ($signup_attempt == md5(1)) : ?> is-warning <?php endif ?>" placeholder="Username" autofocus autocomplete="off">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control password" onkeyup="passwordChecker('RequestSignup','password','confirmpassword');" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>

                    <div class="input-group mb-2">
                        <span class="badge bg-danger atleast" style="display:none;">
                            <i class="fa fa-times-circle"></i> PASSWORD MUST BE AT LEAST `8` CHARACTERS
                        </span>
                        <span class="badge bg-success good8" style="display:none;">
                            <i class="fa fa-check-circle"></i> PASSWORD AT LEAST `8` CHARACTERS
                        </span>
                    </div>

                    <div class="input-group mb-3">
                        <input type="password" name="confirmpassword" class="form-control confirmpassword" onkeyup="passwordChecker('RequestSignup','password','confirmpassword');" placeholder="Confirm Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>

                    <div class="input-group mb-2">
                        <span class="badge bg-danger bad" style="display:none;">
                            <i class="fa fa-times-circle"></i> PASSWORD MISMATCH
                        </span>
                        <span class="badge bg-success good" style="display:none;">
                            <i class="fa fa-check-circle"></i> PASSWORD MATCH
                        </span>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="agreeTerms" name="terms" value="agree">
                                <label for="agreeTerms">
                                    I agree to the <a href="#">terms</a>
                                </label>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Register</button>
                        </div>
                        <!-- /.col -->
                        <div class="col-12 mb-n2">
                            <p class="text-center text-sm text-gray">or</p>
                        </div>
                        <div class="col-12">
                            <a href="<?= base_url() ?>login" type="button" class="btn btn-default btn-block"><i class="fab fa-login mr-2"></i> Login</a>
                        </div>
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
    <script src="<?= base_url() ?>plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="<?= base_url() ?>dist/js/adminlte.min.js"></script>
    <script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>

    <script type="text/javascript">
        var valid = 0;
        function validate(form_id) {
            let invalid = 0;
            $($("#" + form_id).find("input").get().reverse()).each(function() {
                if ($("#" + form_id + ' input[type="search"]')) {
                    // return 0;
                }
                if ($("#" + form_id + ' input[type="text"]')) {
                    var name = clean($(this).attr("name"));
                    var nr = $(this).attr("nr");

                    if (name == null) {} else if (nr != 1) {
                        if (!$(this).val()) {
                            $(this).focus().addClass("is-invalid");
                            $("#" + form_id + " ." + name).addClass('border-danger');
                            invalid++;
                        } else {
                            $(this).removeClass("is-invalid");
                            $("#" + form_id + " ." + name).removeClass('border-danger');
                        }
                    }
                }
            });
            valid = invalid;
        }

        function passwordChecker(a, b, c) {
            let f = "form_save_data" + a;
            let g = $("#" + f + " ." + b).val(); // password
            let h = $("#" + f + " ." + c).val(); // confirm password

            // --- LENGTH CHECK (Password only) ---
            if (g.length >= 8) {
                $("#" + f + " .atleast").hide();
                $("#" + f + " .good8").show();
            } else {
                $("#" + f + " .atleast").show();
                $("#" + f + " .good8").hide();
            }

            // --- MATCH CHECK (always active) ---
            if (!g && !h) {
                // nothing typed yet
                $("#" + f + " .good").hide();
                $("#" + f + " .bad").hide();
            } else if (g === h) {
                // matches
                $("#" + f + " .good").show();
                $("#" + f + " .bad").hide();
            } else {
                // mismatch
                $("#" + f + " .good").hide();
                $("#" + f + " .bad").show();
            }

            // --- ENABLE SUBMIT ---
            if (g.length >= 8 && g === h) {
                $("#" + f + " .submitBtnPrimary").prop("disabled", false);
            } else {
                $("#" + f + " .submitBtnPrimary").prop("disabled", true);
            }
        }

        function saveForm(formId, tblId, tbl, dtd, pl) {
            let a = "";
            var saveData = {
                clearForm: false,
                resetForm: false,
                beforeSubmit: function(e) {
                    validate("form_save_data" + formId);
                    if (valid != 0) {
                        fillIn();
                        return false;
                    }
                    a = $("#form_save_data" + formId + " .submitBtnPrimary").text();
                    $("#form_save_data" + formId + " .submitBtnPrimary").attr("disabled", true);
                    $("#form_save_data" + formId + " .submitBtnPrimary").html("<span class=\"fa fa-spinner fa-pulse\"></span>");
                },
                success: function(data) {
                    var d = JSON.parse(data);
                    if (d.success == true) {
                        successAlert("Successfully Registered!");
                        $(".sbmtbttn").hide();
                        $(".redirect").show();
                        setTimeout(function() {
                            location.reload();
                        }, 2000)
                    } else if (d.exist == true) {
                        existAlert("User already exist!");
                    } else if (d.fill == true) {
                        existAlert("Please fill in the required fields");
                    } else {
                        failAlert("Something went wrong!");
                    }
                    $("#form_save_data" + formId + " .submitBtnPrimary").attr("disabled", false);
                    $("#form_save_data" + formId + " .submitBtnPrimary").html(a);
                }
            };
            $("#form_save_data" + formId).ajaxForm(saveData);
        }

        saveForm("RequestSignup", [null], null);

        function clean(a) {
            var str = a;
            return str === undefined ? null : str.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
        }
        const Toast = Swal.mixin({
            toast: true,
            position: 'center',
            showConfirmButton: false,
            timer: 3000
        });

        function successAlert(a) {
            Toast.fire({
                icon: 'success',
                title: '  ' + a
            })
        }

        function failAlert(a) {
            Toast.fire({
                icon: 'error',
                title: '  ' + a
            })
        }

        function fillIn() {
            Toast.fire({
                icon: 'error',
                title: '  Please fill in all the required fields.'
            })
        }

        function existAlert(a) {
            Toast.fire({
                icon: 'warning',
                title: '  ' + a
            })
        }

        function noData(a) {
            Toast.fire({
                icon: 'warning',
                title: '  ' + a,
            })
        }
    </script>

</body>

</html>