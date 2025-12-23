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
    <link rel="stylesheet" href="<?= base_url() ?>plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url() ?>dist/css/adminlte.min.css?v=3.2.0">
</head>

<body class="register-page" style="min-height:1200px;">
    <div>

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center" style="background-color:#ffffff;">
            <img src="<?php echo base_url(); ?>dist/layout_shop/images/logo.svg" width="240" height="70" alt="logo">
            <!-- Agusan National High School Information System  -->
        </div>
        <!-- /.login-logo -->

        <div class="card p-3">
            <div class="card-header text-center">
                <a href="<?= base_url() ?>index" class="d-block">
                    <img src="<?php echo base_url(); ?>dist/layout_shop/images/logo.svg" width="240" height="70" alt="logo">
                </a>
            </div>
            <div class="card-body register-card-body p-0">
                <?php $signup_attempt = $this->input->get("signup_attempt");
                $firstname = $this->input->get("firstname");
                $lastname = $this->input->get("lastname"); ?>
                <?php if ($signup_attempt == md5(0)) : ?>
                    <p class="text-danger text-center text-sm"><i class="fa fa-exclamation-triangle"></i> Please input all required fields and try again.</p>
                <?php endif ?>
                <?php if ($signup_attempt == md5(1)) : ?>
                    <p class="text-warning text-center text-sm"><i class="fa fa-exclamation-triangle"></i> User already exists. Please try again.</p>
                <?php endif ?>

                <div class="card card-primary card-tabs">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                            <li class="nav-item" style="width: 50%;">
                                <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill" href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home" aria-selected="true"><i class="fa fa-user"></i> Register as Customer</a>
                            </li>
                            <li class="nav-item" style="width: 50%;">
                                <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill" href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false"><i class="fa fa-tractor"></i> Register as Farmer</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="custom-tabs-one-tabContent">
                            <div class="tab-pane fade active show" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
                                <div>
                                    <?= form_open(base_url('/requestsignup'), 'id=form_save_dataRequestSignupCustomer'); ?>

                                    <!-- First & Last Name -->
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <input type="text" name="firstname" class="form-control form-control-sm <?php if ($signup_attempt == md5(0)) : ?>is-invalid<?php endif ?>" placeholder="FIRST NAME" style="text-transform: uppercase;" autocomplete="off" value="<?= ($signup_attempt == md5(1)) ? $firstname : '' ?>" autofocus>
                                                <div class="input-group-append">
                                                    <div class="input-group-text"><span class="fas fa-user"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <input type="text" name="lastname" class="form-control form-control-sm <?php if ($signup_attempt == md5(0)) : ?>is-invalid<?php endif ?>" placeholder="LAST NAME" style="text-transform: uppercase;" autocomplete="off" value="<?= ($signup_attempt == md5(1)) ? $lastname : '' ?>">
                                                <div class="input-group-append">
                                                    <div class="input-group-text"><span class="fas fa-user"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sex & Birth Date -->
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <select name="sex" class="form-control form-select-sm">
                                                    <option selected disabled>GENDER</option>
                                                    <option value="MALE">MALE</option>
                                                    <option value="FEMALE">FEMALE</option>
                                                </select>
                                                <div class="input-group-append">
                                                    <div class="input-group-text"><span class="fas fa-venus-mars"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <input type="date" name="birthDate" class="form-control form-control-sm <?php if ($signup_attempt == md5(0)) : ?>is-invalid<?php endif ?>">
                                                <div class="input-group-append">
                                                    <div class="input-group-text"><span class="fas fa-calendar-alt"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Contact Number & Email -->
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <input type="tel" name="contact" class="form-control form-control-sm" placeholder="CONTACT NUMBER">
                                                <div class="input-group-append">
                                                    <div class="input-group-text"><span class="fas fa-phone"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <input type="email" name="email" class="form-control form-control-sm" placeholder="EMAIL">
                                                <div class="input-group-append">
                                                    <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="input-group mb-3">
                                        <input name="barangay" hidden>
                                        <input type="text" name="type_barangay1" class="form-control form-control-sm barangayInput" placeholder="TYPE BARANGAY (min 3 chars)" autocomplete="off">
                                        <div class="input-group-append">
                                            <div class="input-group-text"><span class="fas fa-home"></span></div>
                                        </div>
                                    </div>
                                    <ul class="list-group barangayResults" style="position:absolute; z-index:9999; width:100%; display:none;cursor:pointer;"></ul>
                                    <hr>

                                    <!-- Username -->
                                    <div class="input-group mb-3">
                                        <input type="text" name="username" class="form-control form-control-sm <?php if ($signup_attempt == md5(0)) : ?>is-invalid<?php elseif ($signup_attempt == md5(1)) : ?>is-warning<?php endif ?>" placeholder="USERNAME" autocomplete="off">
                                        <div class="input-group-append">
                                            <div class="input-group-text"><span class="fas fa-user-circle"></span></div>
                                        </div>
                                    </div>

                                    <!-- Password -->
                                    <div class="input-group mb-2">
                                        <input type="password" name="password" class="form-control form-control-sm password" placeholder="PASSWORD" onkeyup="passwordChecker('RequestSignupCustomer','password','confirmpassword');">
                                        <div class="input-group-append">
                                            <div class="input-group-text"><span class="fas fa-lock"></span></div>
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

                                    <!-- Confirm Password -->
                                    <div class="input-group mb-2">
                                        <input type="password" name="confirmpassword" class="form-control form-control-sm confirmpassword" placeholder="CONFIRM PASSWORD" onkeyup="passwordChecker('RequestSignupCustomer','password','confirmpassword');">
                                        <div class="input-group-append">
                                            <div class="input-group-text"><span class="fas fa-lock"></span></div>
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

                                    <!-- Terms -->
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="agreeTerms1" name="terms" value="agree">
                                        <label class="form-check-label" for="agreeTerms">I agree to the <a href="#">terms</a></label>
                                    </div>

                                    <!-- Buttons -->
                                    <div class="d-grid mb-3">
                                        <button type="submit" class="btn btn-success btn-sm btn-block fw-semibold">Register</button>
                                    </div>

                                    <p class="text-center text-muted mb-2">or</p>

                                    <div class="d-grid">
                                        <a href="<?= base_url() ?>login" class="btn btn-outline-secondary btn-sm btn-block">Login</a>
                                    </div>

                                    <?= form_close(); ?>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
                                <div>
                                    <?= form_open(base_url('/requestsignup'), 'id=form_save_dataRequestSignupFarmer'); ?>

                                    <!-- First & Last Name -->
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <input type="text" name="firstname" class="form-control form-control-sm <?php if ($signup_attempt == md5(0)) : ?>is-invalid<?php endif ?>" placeholder="FIRST NAME" style="text-transform: uppercase;" autocomplete="off" value="<?= ($signup_attempt == md5(1)) ? $firstname : '' ?>" autofocus>
                                                <div class="input-group-append">
                                                    <div class="input-group-text"><span class="fas fa-user"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <input type="text" name="lastname" class="form-control form-control-sm <?php if ($signup_attempt == md5(0)) : ?>is-invalid<?php endif ?>" placeholder="LAST NAME" style="text-transform: uppercase;" autocomplete="off" value="<?= ($signup_attempt == md5(1)) ? $lastname : '' ?>">
                                                <div class="input-group-append">
                                                    <div class="input-group-text"><span class="fas fa-user"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sex & Birth Date -->
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <select name="sex" class="form-control form-select-sm">
                                                    <option selected disabled>GENDER</option>
                                                    <option value="MALE">MALE</option>
                                                    <option value="FEMALE">FEMALE</option>
                                                </select>
                                                <div class="input-group-append">
                                                    <div class="input-group-text"><span class="fas fa-venus-mars"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <input type="date" name="birthDate" class="form-control form-control-sm <?php if ($signup_attempt == md5(0)) : ?>is-invalid<?php endif ?>">
                                                <div class="input-group-append">
                                                    <div class="input-group-text"><span class="fas fa-calendar-alt"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Contact Number & Email -->
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <input type="tel" name="contact" class="form-control form-control-sm" placeholder="CONTACT NUMBER">
                                                <div class="input-group-append">
                                                    <div class="input-group-text"><span class="fas fa-phone"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="input-group mb-3">
                                                <input type="email" name="email" class="form-control form-control-sm" placeholder="EMAIL">
                                                <div class="input-group-append">
                                                    <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-group mb-3">
                                        <input name="barangay" hidden>
                                        <input type="text" name="type_barangay2" class="form-control form-control-sm barangayInput" placeholder="TYPE BARANGAY (min 3 chars)" autocomplete="off">
                                        <div class="input-group-append">
                                            <div class="input-group-text"><span class="fas fa-home"></span></div>
                                        </div>
                                    </div>

                                    <!-- dropdown container -->
                                    <ul class="list-group barangayResults" style="position:absolute; z-index:9999; width:100%; display:none;cursor:pointer;"></ul>

                                    <hr>

                                    <div class="input-group mb-3">
                                        <select name="valid_id" class="form-control form-select-sm" style="width:90%">
                                            <option value="" selected disabled>VALID ID TO BE PRESENTED</option>
                                            <option value="PHILIPPINE NATIONAL ID (PHILSYS)">PHILIPPINE NATIONAL ID (PHILSYS)</option>
                                            <option value="SSS ID">SSS ID</option>
                                            <option value="UMID">UMID</option>
                                            <option value="DRIVER'S LICENSE">DRIVER'S LICENSE</option>
                                            <option value="PASSPORT">PASSPORT</option>
                                            <option value="POSTAL ID">POSTAL ID</option>
                                            <option value="PRC ID">PRC ID</option>
                                            <option value="TIN ID">TIN ID</option>
                                            <option value="VOTER'S ID">VOTER'S ID</option>
                                            <option value="VOTER'S CERTIFICATE">VOTER'S CERTIFICATE</option>
                                            <option value="PHILHEALTH ID">PHILHEALTH ID</option>
                                            <option value="SENIOR CITIZEN ID">SENIOR CITIZEN ID</option>
                                            <option value="PWD ID">PWD ID</option>
                                            <option value="GSIS ID">GSIS ID</option>
                                            <option value="PAG-IBIG ID">PAG-IBIG ID</option>
                                            <option value="STUDENT ID (if minor)">STUDENT ID (if minor)</option>
                                            <option value="AFP ID">AFP ID</option>
                                            <option value="PNP ID">PNP ID</option>
                                        </select>
                                        <div class="input-group-append">
                                            <div class="input-group-text"><span class="fas fa-id-card"></span></div>
                                        </div>
                                    </div>

                                    <center class="mt-3">
                                        <div class="form-group">
                                            <label class="col-form-label">ID Picture</label><br />
                                            <img name="previewPicFarmerID" src="<?= base_url("dist/img/media/icons/id_preview.png"); ?>" onclick="$('[name=picFarmerID]').trigger('click')" width="120" height="120" class="border border-white border-2 rounded elevation-1" type="button" alt="User Image">
                                        </div>

                                        <div class="form-group">
                                            <input name="picFarmerID" type="file" accept="image/*" onchange="imageView('picFarmerID','previewPicFarmerID','imgtargetLink')" nr="1" hidden />
                                        </div>
                                    </center>

                                    <div class="input-group mb-3">
                                        <select name="organization" class="form-control form-select-sm select2" style="width:90%" nr="1">
                                            <option value="" selected disabled>ORGANIZATION MEMBERSHIP (OPTIONAL)</option>
                                            <?php $this->load->view('interface/system/layout/options_select_for_organization') ?>
                                        </select>
                                        <div class="input-group-append">
                                            <div class="input-group-text"><span class="fas fa-id-card"></span></div>
                                        </div>
                                    </div>


                                    <hr>

                                    <!-- Username -->
                                    <div class="input-group mb-3">
                                        <input type="text" name="username" class="form-control form-control-sm <?php if ($signup_attempt == md5(0)) : ?>is-invalid<?php elseif ($signup_attempt == md5(1)) : ?>is-warning<?php endif ?>" placeholder="USERNAME" autocomplete="off">
                                        <div class="input-group-append">
                                            <div class="input-group-text"><span class="fas fa-user-circle"></span></div>
                                        </div>
                                    </div>

                                    <!-- Password -->
                                    <div class="input-group mb-2">
                                        <input type="password" name="password" class="form-control form-control-sm password" placeholder="PASSWORD" onkeyup="passwordChecker('RequestSignupFarmer','password','confirmpassword');">
                                        <div class="input-group-append">
                                            <div class="input-group-text"><span class="fas fa-lock"></span></div>
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

                                    <!-- Confirm Password -->
                                    <div class="input-group mb-2">
                                        <input type="password" name="confirmpassword" class="form-control form-control-sm confirmpassword" placeholder="CONFIRM PASSWORD" onkeyup="passwordChecker('RequestSignupFarmer','password','confirmpassword');">
                                        <div class="input-group-append">
                                            <div class="input-group-text"><span class="fas fa-lock"></span></div>
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

                                    <!-- Terms -->
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="agreeTerms" name="terms" value="agree">
                                        <label class="form-check-label" for="agreeTerms">I agree to the <a href="#">terms</a></label>
                                    </div>

                                    <!-- Buttons -->
                                    <div class="d-grid mb-3">
                                        <button type="submit" class="btn btn-danger btn-sm btn-block fw-semibold">Register as Farmer</button>
                                    </div>

                                    <p class="text-center text-muted mb-2">or</p>

                                    <div class="d-grid">
                                        <a href="<?= base_url() ?>login" class="btn btn-outline-secondary btn-sm btn-block">Login</a>
                                    </div>

                                    <?= form_close(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>

            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="<?= base_url() ?>plugins/jquery/jquery.min.js"></script>
    <script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?= base_url() ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?= base_url() ?>plugins/select2/js/select2.full.min.js"></script>
    <script src="<?= base_url() ?>plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="<?= base_url() ?>dist/js/adminlte.min.js"></script>

    <script type="text/javascript">
        var valid = 0;

        $(document).on('click', '.barangay-item', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');

            // Set selected value to input
            $('.barangayInput').val(name);

            // Store barangay id in hidden input (recommended)
            $('#barangay_id').val(id);

            // Hide dropdown
            $('.barangayResults').hide();
            $('[name=barangay]').val(id)
        });

        $(function() {
            $('.select2').select2()
        });

        $('.barangayInput').on('keyup', function() {
            let keyword = $(this).val();

            if (keyword.length < 3) {
                $('.barangayResults').hide();
                return;
            }

            $.ajax({
                url: "<?= base_url('search-barangay') ?>",
                type: "POST",
                data: {
                    keyword: keyword
                },
                success: function(res) {
                    let data = JSON.parse(res);

                    if (data.length === 0) {
                        $('.barangayResults').hide();
                        return;
                    }

                    let html = "";
                    data.forEach(row => {
                        html += `<li class="list-group-item barangay-item" data-id="${row.id}" data-name="${row.text}">
                    ${row.text}
                </li>`;
                    });

                    $('.barangayResults').html(html).show();
                }
            });
        });

        // when clicked
        $(document).on('click', '.barangay-item', function() {
            let name = $(this).data('name');
            let id = $(this).data('id');

            $('.barangayInput').val(name); // show selected barangay
            $('.barangayResults').hide(); // hide list

            // optional: save to hidden field if needed
            // $("#barangay_id").val(id);
        });

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

            $(
                $("#" + form_id)
                .find("input[type='text'], select")
                .get()
                .reverse()
            ).each(function() {

                let name = clean($(this).attr("name"));
                let nr = $(this).attr("nr");

                // skip if no name or nr == 1
                if (!name || nr == 1) return;

                // 🔴 check empty (works for input & select)
                if (!$(this).val()) {
                    $(this).focus().addClass("is-invalid");
                    $("#" + form_id + " ." + name).addClass("border-danger");
                    invalid++;
                } else {
                    $(this).removeClass("is-invalid");
                    $("#" + form_id + " ." + name).removeClass("border-danger");
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
                        // setTimeout(function() {
                        //     location.reload();
                        // }, 2000)
                        if (d.success) {
                            window.location.href = d.redirect_to;
                        }
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

        saveForm("RequestSignupCustomer", [null], null);
        saveForm("RequestSignupFarmer", [null], null);

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


        function imageView(a, b, c) {
            var fileInput = $("[name=" + a + "]")[0]; // Get the file input element
            var file = fileInput.files[0]; // Get the selected file

            if (file.size > 25 * 1024 * 1024) {
                // Picture size is above 2MB
                alert("Picture must be less than 2MB");
                return; // You can handle this case according to your requirements
            }

            var reader = new FileReader();

            reader.onload = function(e) {
                $("[name=" + b + "]").attr('src', e.target.result); // Set the source of the image element
            };

            reader.readAsDataURL(file);
        }
    </script>

</body>

</html>