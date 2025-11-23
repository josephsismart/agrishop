<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!$this->session->schoolmis_login_level) {
    redirect(base_url('login'));
}
$uri = $this->session->schoolmis_login_uri;
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $system_title ?> | <?= $page_title ?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="<?= $system_svg ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url() ?>plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <!-- <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"> -->
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="<?= base_url() ?>plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?= base_url() ?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url() ?>dist/css/adminlte.min.css">
    <!-- Google Font: Source Sans Pro -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet"> -->
</head>

<body class="hold-transition login-page" style="background-image : url(<?= $system_op ?> );background-repeat: no-repeat; background-size: 45%;background-position: center center;overflow:hidden;">
    <div class="login-box">
        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <!-- <img class="animation__shake" src="<?= $system_svg ?>" alt="AdminLTELogo" height="500" width="500"/> -->
            <a href="#" class="h1"><b>ANHS</b>IS</a>
        </div>
        <!-- /.login-logo -->
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="#" class="h1"><b>ANHS</b>IS</a>
            </div>

            <?= form_open(base_url($uri . '/Changepassword/saveUpdatePassword'), 'id=form_save_dataUpdatePassword'); ?>

            <!-- <form action="<?= base_url() ?>updatePassword" method="post" id="form_save_dataUpdatePassword"> -->
            <form action="<?= base_url() ?>updatePassword" method="post" id="form_save_dataUpdatePassword">
                <div class="card-body">
                    <?php if ($this->input->get("login_attempt") == md5(0) || $this->input->get("login_attempt") == md5(1)) : ?>
                        <p class="text-danger text-center text-sm"><i class="fa fa-exclamation-triangle"></i> Invalid Username or Password. Please try again.</p>
                    <?php endif ?>
                    <?php if ($this->input->get("login_attempt") != md5(0) || $this->input->get("login_attempt") != md5(1)) : ?>
                    <?php endif ?>
                    <form action="<?= base_url() ?>requestlogin" method="post">
                        <p class='text-center text-sm mb-2'>Create New Password at least <b>8</b> Characters.</p>

                        <div class="input-group mb-2">
                            <span class="badge bg-success crrntgood" style="display:none;"><i class="fa fa-check-circle"></i> CURRENT PASSWORD MATCH</span>
                            <span class="badge bg-danger crrntbad"><i class="fa fa-times-circle"></i> CURRENT PASSWORD MISMATCH</span>
                        </div>

                        <div class="input-group mb-3">
                            <input type="password" name="crrntpwd" onkeyup="passwordChecker('UpdatePassword','pwd','confirmpwd','crrntpwd');" class="form-control crrntpwd <?php if ($this->input->get("login_attempt") == md5(0)) : ?> is-invalid <?php endif ?>" placeholder="Current Password" autofocus autocomplete="off">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-key"></span>
                                </div>
                            </div>
                        </div>
                        <!-- <p class='text-center text-sm mb-1'>New Password at least <b>8</b> Characters.</p> -->
                        <div class="input-group mb-2">
                            <span class="badge bg-danger atleast"><i class="fa fa-times-circle"></i> PASSWORD MUST BE AT LEAST `8` CHARACTERS</span>
                            <span class="badge bg-success good8" style="display:none;"><i class="fa fa-check-circle"></i> PASSWORD AT LEAST `8` CHARACTERS</span>
                        </div>
                        <div class="input-group mb-2">
                            <span class="badge bg-danger bad"><i class="fa fa-times-circle"></i> PASSWORD MISMATCH</span>
                            <span class="badge bg-success good" style="display:none;"><i class="fa fa-check-circle"></i> PASSWORD MATCH</span>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" name="pwd" onkeyup="passwordChecker('UpdatePassword','pwd','confirmpwd',null);" class="form-control pwd <?php if ($this->input->get("login_attempt") == md5(0)) : ?> is-invalid <?php endif ?>" placeholder="New Password" autocomplete="off">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-key"></span>
                                </div>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" name="confirmpwd" onkeyup="passwordChecker('UpdatePassword','confirmpwd','pwd');" class="form-control confirmpwd <?php if ($this->input->get("login_attempt") == md5(0)) : ?> is-invalid <?php endif ?>" placeholder="Confirm Password">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-key"></span>
                                </div>
                            </div>
                        </div>

                        <div class="redirect" style="display:none;">
                            <div class="overlay">
                                <i class="fas fa-3x fa-sync-alt fa-spin"></i>
                            </div>
                            <p class='text-center text-sm mb-2'>Redirecting...</p>
                        </div>
                        <div class="row sbmtbttn">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-block submitBtnPrimary" disabled><i class="fab fa-login mr-2"></i> Change Password</button>
                            </div>
                        </div>
                    </form>
                    <!-- /.social-auth-links -->

                </div>
            </form>
        </div>
        <!-- /.card -->
    </div>

    <!-- jQuery -->
    <script src="<?= base_url() ?>plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?= base_url() ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <!-- SweetAlert2 -->
    <script src="<?= base_url() ?>plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="<?= base_url() ?>dist/js/adminlte.min.js"></script>
    <script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>
    <script src="<?= base_url() ?>plugins/jquery/jquery.md5.js"></script>

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

        function passwordChecker(a, b, c, d) {
            let f = "form_save_data" + a;
            let g = $("#" + f + " ." + b).val();
            let h = $("#" + f + " ." + c).val();
            if (d != null) {
                let i = $("#" + f + " ." + d).val();
                let j = '<?= $this->session->schoolmis_pass; ?>';
                if ($.md5(i) == j) {
                    $("#" + f + " .crrntgood").show();
                    $("#" + f + " .crrntbad").hide();
                } else {
                    $("#" + f + " .crrntgood").hide();
                    $("#" + f + " .crrntbad").show();
                }
            } else {
                if (g.length > 7 || h.length > 7) {
                    $("#" + f + " .atleast").hide();
                    $("#" + f + " .good8").show();
                    if (g != h) {
                        $("#" + f + " .good").hide();
                        $("#" + f + " .bad").show();
                    } else if (!g && !h) {
                        $("#" + f + " .good").hide();
                        $("#" + f + " .bad").hide();
                    } else {
                        $("#" + f + " .good").show();
                        $("#" + f + " .bad").hide();
                    }
                } else {
                    $("#" + f + " .atleast").show();
                    $("#" + f + " .good8").hide();
                }
            }
            if ($("#" + f + " .good8").is(":visible") == true && $("#" + f + " .crrntgood").is(":visible") == true && $("#" + f + " .good").is(":visible") == true) {
                $("#" + f + " .submitBtnPrimary").attr("disabled", false);
            } else {
                $("#" + f + " .submitBtnPrimary").attr("disabled", true);
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
                        successAlert("Successfully Saved!");
                        $(".sbmtbttn").hide();
                        $(".redirect").show();
                        setTimeout(function() {
                            location.reload();
                        }, 2000)
                    } else if (d.exist == true) {
                        existAlert("Person already exist!<br/>You can search and add TEST RESULT");
                    } else if (d.existCode == true) {
                        existAlert("Code already taken!<br/>by: " + d.existPerson);
                    } else {
                        failAlert("Something went wrong!");
                    }
                    $("#form_save_data" + formId + " .submitBtnPrimary").attr("disabled", false);
                    $("#form_save_data" + formId + " .submitBtnPrimary").html(a);
                }
            };
            $("#form_save_data" + formId).ajaxForm(saveData);
        }

        saveForm("UpdatePassword", [null], null);


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