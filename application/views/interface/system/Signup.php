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
    <link rel="stylesheet" href="<?= base_url() ?>plugins/bootstrap-alpha3/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>dist/css/adminlte.min.css">

</head>
<style>
    .form-floating>label {
        font-size: 0.875rem;
    }
</style>

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

                                    <!-- Personal Information Section -->
                                    <div class="mb-4">
                                        <!-- First & Last Name -->
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" name="firstname" class="form-control form-control-lg border-1 <?= $signup_attempt == md5(0) ? 'is-invalid border-danger' : 'border-primary' ?> text-uppercase" id="customerFirstname" placeholder="FIRST NAME" autocomplete="off" value="<?= $signup_attempt == md5(1) ? $firstname : '' ?>" autofocus required>
                                                    <label for="customerFirstname" class="text-muted">
                                                        <i class="fas fa-user me-2"></i>First Name
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" name="lastname" class="form-control form-control-lg border-1 <?= $signup_attempt == md5(0) ? 'is-invalid border-danger' : 'border-primary' ?> text-uppercase" id="customerLastname" placeholder="LAST NAME" autocomplete="off" value="<?= $signup_attempt == md5(1) ? $lastname : '' ?>" required>
                                                    <label for="customerLastname" class="text-muted">
                                                        <i class="fas fa-user me-2"></i>Last Name
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Gender & Birth Date -->
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <select name="sex" class="form-select border-1 <?= $signup_attempt == md5(0) ? 'is-invalid border-danger' : 'border-primary' ?>" id="customerGender" required>
                                                        <option value="" selected disabled>Select Gender</option>
                                                        <option value="MALE">Male</option>
                                                        <option value="FEMALE">Female</option>
                                                    </select>
                                                    <label for="customerGender" class="text-muted">
                                                        <i class="fas fa-venus-mars me-2"></i>Gender
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="date" name="birthDate" class="form-control border-1 <?= $signup_attempt == md5(0) ? 'is-invalid border-danger' : 'border-primary' ?>" id="customerBirthDate" required>
                                                    <label for="customerBirthDate" class="text-muted">
                                                        <i class="fas fa-calendar-alt me-2"></i>Birth Date
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Contact Information -->
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="tel" name="contact" class="form-control border-1 border-primary" id="customerContact" placeholder="CONTACT NUMBER" pattern="[0-9\s\-+()]+">
                                                    <label for="customerContact" class="text-muted">
                                                        <i class="fas fa-phone me-2"></i>Contact Number
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="email" name="email" class="form-control border-1 border-primary" id="customerEmail" placeholder="EMAIL">
                                                    <label for="customerEmail" class="text-muted">
                                                        <i class="fas fa-envelope me-2"></i>Email Address
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Address Information -->
                                    <div class="mb-4 mt-n1">

                                        <div class="form-floating">
                                            <input type="hidden" name="barangay">
                                            <input type="text" name="type_barangay1" class="form-control border-1 border-primary barangayInput" id="customerBarangay" placeholder="TYPE BARANGAY (min 3 chars)" autocomplete="off" minlength="3" required>
                                            <label for="customerBarangay" class="text-muted">
                                                <i class="fas fa-home me-2"></i>Barangay (type at least 3 characters)
                                            </label>
                                            <div class="form-text text-muted small">
                                                Start typing your barangay name and select from the dropdown
                                            </div>
                                        </div>
                                        <div class="barangay-results mt-1">
                                            <ul class="list-group barangayResults" style="position:absolute; z-index:9999; width:100%; display:none;cursor:pointer;"></ul>
                                        </div>
                                    </div>

                                    <!-- Account Information Section -->
                                    <div class="mb-4">
                                        <h6 class="text-muted mb-3 border-bottom pb-2">
                                            <i class="fas fa-user-circle me-2"></i>Account Information
                                        </h6>

                                        <!-- Username -->
                                        <div class="form-floating mb-3">
                                            <input type="text" name="username" class="form-control border-1 <?=
                                                                                                            $signup_attempt == md5(0) ? 'is-invalid border-danger' : ($signup_attempt == md5(1) ? 'is-warning border-warning' : 'border-primary')
                                                                                                            ?>" id="customerUsername" placeholder="USERNAME" autocomplete="off" required>
                                            <label for="customerUsername" class="text-muted">
                                                <i class="fas fa-user-circle me-2"></i>Username
                                            </label>
                                            <?php if ($signup_attempt == md5(1)) : ?>
                                                <div class="text-warning small mt-1">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Username may already exist
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Password -->
                                        <div class="row g-3 password-wrapper customer-password">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="password" name="password" class="form-control border-1 border-primary password" placeholder="PASSWORD" minlength="8" required>
                                                    <label class="text-muted">
                                                        <i class="fas fa-lock me-2"></i>Password
                                                    </label>
                                                </div>

                                                <div class="password-feedback mt-2">
                                                    <div class="badge bg-danger atleast d-none">
                                                        ❌ At least 8 characters
                                                    </div>
                                                    <div class="badge bg-success good8 d-none">
                                                        ✅ Minimum length
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="password" name="confirmpassword" class="form-control border-1 border-primary confirmpassword" placeholder="CONFIRM PASSWORD" required>
                                                    <label class="text-muted">
                                                        <i class="fas fa-lock me-2"></i>Confirm Password
                                                    </label>
                                                </div>

                                                <div class="password-feedback mt-2">
                                                    <div class="badge bg-danger bad d-none">
                                                        ❌ Passwords don't match
                                                    </div>
                                                    <div class="badge bg-success good d-none">
                                                        ✅ Passwords match
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Terms Agreement -->
                                    <div class="form-check border-top pt-3 mb-4">
                                        <input type="checkbox" class="form-check-input border-1" id="customerTerms" name="terms" value="agree" required>
                                        <label class="form-check-label text-muted" for="customerTerms">
                                            I agree to the <a href="#" class="text-decoration-none">Terms and Conditions</a> and
                                            <a href="#" class="text-decoration-none">Privacy Policy</a>
                                        </label>
                                        <div class="invalid-feedback">
                                            You must agree to the terms before registering.
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-grid gap-3">
                                        <button type="submit" class="btn btn-success py-2 shadow-sm submitBtnPrimary">
                                            <i class="fas fa-user-plus me-2"></i>Create Customer Account
                                        </button>

                                        <a href="<?= base_url() ?>login" class="btn btn-outline-dark py-2">
                                            <i class="fas fa-sign-in-alt me-2"></i>Already have an account? Login
                                        </a>
                                    </div>

                                    <?= form_close(); ?>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
                                <div>
                                    <?= form_open(base_url('/requestsignup'), 'id=form_save_dataRequestSignupFarmer'); ?>

                                    <!-- Personal Information Section -->
                                    <div class="mb-4">

                                        <!-- First & Last Name -->
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" name="firstname" class="form-control form-control-lg border-1 <?= $signup_attempt == md5(0) ? 'is-invalid border-danger' : 'border-primary' ?> text-uppercase" id="farmerFirstname" placeholder="FIRST NAME" autocomplete="off" value="<?= $signup_attempt == md5(1) ? $firstname : '' ?>" autofocus required>
                                                    <label for="farmerFirstname" class="text-muted">
                                                        <i class="fas fa-user me-2"></i>First Name
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" name="lastname" class="form-control form-control-lg border-1 <?= $signup_attempt == md5(0) ? 'is-invalid border-danger' : 'border-primary' ?> text-uppercase" id="farmerLastname" placeholder="LAST NAME" autocomplete="off" value="<?= $signup_attempt == md5(1) ? $lastname : '' ?>" required>
                                                    <label for="farmerLastname" class="text-muted">
                                                        <i class="fas fa-user me-2"></i>Last Name
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Gender & Birth Date -->
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <select name="sex" class="form-select border-1 <?= $signup_attempt == md5(0) ? 'is-invalid border-danger' : 'border-primary' ?>" id="farmerGender" required>
                                                        <option value="" selected disabled>Select Gender</option>
                                                        <option value="MALE">Male</option>
                                                        <option value="FEMALE">Female</option>
                                                    </select>
                                                    <label for="farmerGender" class="text-muted">
                                                        <i class="fas fa-venus-mars me-2"></i>Gender
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="date" name="birthDate" class="form-control border-1 <?= $signup_attempt == md5(0) ? 'is-invalid border-danger' : 'border-primary' ?>" id="farmerBirthDate" required>
                                                    <label for="farmerBirthDate" class="text-muted">
                                                        <i class="fas fa-calendar-alt me-2"></i>Birth Date
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Contact Information -->
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="tel" name="contact" class="form-control border-1 border-primary" id="farmerContact" placeholder="CONTACT NUMBER" pattern="[0-9\s\-+()]+" required autocomplete="off">
                                                    <label for="farmerContact" class="text-muted">
                                                        <i class="fas fa-phone me-2"></i>Contact Number
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="email" name="email" class="form-control border-1 border-primary" id="farmerEmail" placeholder="EMAIL" required autocomplete="off">
                                                    <label for="farmerEmail" class="text-muted">
                                                        <i class="fas fa-envelope me-2"></i>Email Address
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Address Information -->
                                    <div class="mb-4 mt-n1">

                                        <div class="form-floating">
                                            <input type="hidden" name="barangay">
                                            <input type="text" name="type_barangay2" class="form-control border-1 border-primary barangayInput" id="farmerBarangay" placeholder="TYPE BARANGAY (min 3 chars)" autocomplete="off" minlength="3" required>
                                            <label for="farmerBarangay" class="text-muted">
                                                <i class="fas fa-home me-2"></i>Barangay Location
                                            </label>
                                            <div class="barangay-results mt-1">
                                                <ul class="list-group barangayResults" style="position:absolute; z-index:9999; width:100%; display:none;cursor:pointer;"></ul>
                                            </div>
                                            <div class="form-text text-muted small">
                                                Type your barangay name to search and select from dropdown
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="text-muted mb-3 border-bottom pb-2">
                                            <i class="fas fa-tractor me-2"></i>Farmer Information
                                        </h6>


                                        <!-- <select name="product_type" class="form-control border-1 border-primary mb-3">
                                            <option value="">Select Product Type</option>
                                            <option value="produce">Farm Produce</option>
                                            <option value="tools">Farm Tools</option>
                                        </select> -->

                                        <div class="form-floating mb-3">
                                            <select name="farmer_selling_type" class="form-select border-1 <?= $signup_attempt == md5(0) ? 'is-invalid border-danger' : 'border-primary' ?>" id="farmerSellingType" required>
                                                <option value="1">Farm Produce</option>
                                                <option value="2">Farm Tools & Equipments</option>
                                                <option value="3">Both</option>
                                            </select>
                                            <label for="farmerSellingType" class="text-muted">
                                                <i class="fas fa-venus-mars me-2"></i>Selling Type
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Farmer Verification Section -->
                                    <div class="mb-4">
                                        <!-- Valid ID Selection -->
                                        <div class="form-floating mb-3">
                                            <select name="valid_id" class="form-select border-1 border-primary" id="farmerValidID" required>
                                                <option value="" selected disabled>Select Valid ID</option>
                                                <option value="PHILIPPINE NATIONAL ID (PHILSYS)">Philippine National ID (PhilSys)</option>
                                                <option value="SSS ID">SSS ID</option>
                                                <option value="UMID">UMID</option>
                                                <option value="DRIVER'S LICENSE">Driver's License</option>
                                                <option value="PASSPORT">Passport</option>
                                                <option value="POSTAL ID">Postal ID</option>
                                                <option value="PRC ID">PRC ID</option>
                                                <option value="TIN ID">TIN ID</option>
                                                <option value="VOTER'S ID">Voter's ID</option>
                                                <option value="VOTER'S CERTIFICATE">Voter's Certificate</option>
                                                <option value="PHILHEALTH ID">PhilHealth ID</option>
                                                <option value="SENIOR CITIZEN ID">Senior Citizen ID</option>
                                                <option value="PWD ID">PWD ID</option>
                                                <option value="GSIS ID">GSIS ID</option>
                                                <option value="PAG-IBIG ID">Pag-IBIG ID</option>
                                                <option value="STUDENT ID (if minor)">Student ID (if minor)</option>
                                                <option value="AFP ID">AFP ID</option>
                                                <option value="PNP ID">PNP ID</option>
                                            </select>
                                            <label for="farmerValidID" class="text-muted">
                                                <i class="fas fa-id-card me-2"></i>Valid Government ID
                                            </label>
                                            <div class="form-text text-muted small">
                                                Choose the ID you will present for verification
                                            </div>
                                        </div>

                                        <!-- ID Picture Upload -->
                                        <div class="mb-4 text-center">
                                            <label class="form-label fw-semibold mb-3 d-block">
                                                <i class="fas fa-camera me-2"></i>ID Picture Upload
                                            </label>
                                            <div class="position-relative d-inline-block">
                                                <img name="previewPicFarmerID" src="<?= base_url("dist/img/media/icons/id_preview.png"); ?>" onclick="$('[name=picFarmerID]').click()" width="180" height="180" class="border-3 border-dashed rounded-3 cursor-pointer" alt="ID Preview" style="border-color: #dee2e6; border-style: dashed;">
                                                <div class="position-absolute top-50 start-50 translate-middle">
                                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted"></i>
                                                </div>
                                            </div>
                                            <input name="picFarmerID" type="file" accept="image/*" onchange="imageView('picFarmerID','previewPicFarmerID','imgtargetLink')" hidden />
                                            <div class="mt-3">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>Click to upload clear photo of your ID (Max: 2MB)
                                                </small>
                                            </div>
                                        </div>

                                        <!-- Organization Membership -->
                                        <div class="form-floating">
                                            <select name="organization" class="form-select border-1 border-primary select2" id="farmerOrganization" nr="1">
                                                <option value="" selected>Select Organization (Optional)</option>
                                                <?php $this->load->view('interface/system/layout/options_select_for_organization') ?>
                                            </select>
                                            <!-- <label for="farmerOrganization" class="text-muted">
                                                <i class="fas fa-users me-2"></i>Farm Organization (Optional)
                                            </label> -->
                                            <!-- <div class="form-text text-muted small">
                                                Select if you belong to any farming association or cooperative
                                            </div> -->
                                        </div>
                                    </div>

                                    <!-- Account Information Section -->
                                    <div class="mb-4">
                                        <h6 class="text-muted mb-3 border-bottom pb-2">
                                            <i class="fas fa-user-circle me-2"></i>Account Information
                                        </h6>

                                        <!-- Username -->
                                        <div class="form-floating mb-3">
                                            <input type="text" name="username" class="form-control border-1 <?=
                                                                                                            $signup_attempt == md5(0) ? 'is-invalid border-danger' : ($signup_attempt == md5(1) ? 'is-warning border-warning' : 'border-primary')
                                                                                                            ?>" id="farmerUsername" placeholder="USERNAME" autocomplete="off" required>
                                            <label for="farmerUsername" class="text-muted">
                                                <i class="fas fa-user-circle me-2"></i>Username
                                            </label>
                                            <?php if ($signup_attempt == md5(1)) : ?>
                                                <div class="text-warning small mt-1">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Username may already exist
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Password -->
                                        <div class="row g-3 password-wrapper farmer-password">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="password" name="password" class="form-control border-1 border-primary password" placeholder="PASSWORD" minlength="8" required>
                                                    <label class="text-muted">
                                                        <i class="fas fa-lock me-2"></i>Password
                                                    </label>
                                                </div>

                                                <div class="password-feedback mt-2">
                                                    <div class="badge bg-danger atleast d-none">
                                                        ❌ At least 8 characters
                                                    </div>
                                                    <div class="badge bg-success good8 d-none">
                                                        ✅ Minimum length
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="password" name="confirmpassword" class="form-control border-1 border-primary confirmpassword" placeholder="CONFIRM PASSWORD" required>
                                                    <label class="text-muted">
                                                        <i class="fas fa-lock me-2"></i>Confirm Password
                                                    </label>
                                                </div>

                                                <div class="password-feedback mt-2">
                                                    <div class="badge bg-danger bad d-none">
                                                        ❌ Passwords don't match
                                                    </div>
                                                    <div class="badge bg-success good d-none">
                                                        ✅ Passwords match
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-grid gap-3">
                                        <button type="submit" class="btn btn-danger btn-lg  py-2 shadow-sm submitBtnPrimary">
                                            <i class="fas fa-tractor me-2"></i>Register as Verified Farmer
                                        </button>

                                        <a href="<?= base_url() ?>login" class="btn btn-outline-dark btn-lg py-2">
                                            <i class="fas fa-sign-in-alt me-2"></i>Already have an account? Login
                                        </a>
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

        $(document).ready(function() {

            $('.password-wrapper').each(function() {
                const $wrapper = $(this);

                const $password = $wrapper.find('.password');
                const $confirm = $wrapper.find('.confirmpassword');

                const $atleast = $wrapper.find('.atleast');
                const $good8 = $wrapper.find('.good8');

                const $bad = $wrapper.find('.bad');
                const $good = $wrapper.find('.good');

                $password.on('keyup', function() {
                    if ($password.val().length >= 8) {
                        $atleast.addClass('d-none');
                        $good8.removeClass('d-none');
                    } else {
                        $atleast.removeClass('d-none');
                        $good8.addClass('d-none');
                    }
                    checkMatch();
                });

                $confirm.on('keyup', checkMatch);

                function checkMatch() {
                    if ($confirm.val() === '') {
                        $bad.addClass('d-none');
                        $good.addClass('d-none');
                        return;
                    }

                    if ($password.val() === $confirm.val()) {
                        $bad.addClass('d-none');
                        $good.removeClass('d-none');
                    } else {
                        $bad.removeClass('d-none');
                        $good.addClass('d-none');
                    }
                }
            });

        });

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
                        setTimeout(function() {
                            if (d.success) {
                                window.location.href = d.redirect_to;
                            }
                        }, 1500)

                    } else if (d.exist == true) {
                        existAlert("User already exist!");
                    } else if (d.fill == true) {
                        existAlert("Please fill in the required fields");
                    } else if (d.password == true) {
                        existAlert("Password does not match");
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