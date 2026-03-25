<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $system_title ?> | <?= $page_title ?></title>
    <link rel="icon" type="image/png" href="<?= $system_svg ?>">
    <link rel="stylesheet" href="<?= base_url() ?>plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>plugins/bootstrap-alpha3/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>dist/css/adminlte.min.css">
    <script src="<?= base_url() ?>plugins/jquery/jquery.min.js"></script>
</head>

<style>
* { box-sizing: border-box; }

body {
    background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 50%, #f0fdf4 100%);
    min-height: 100vh;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.signup-wrapper {
    max-width: 640px;
    margin: 0 auto;
    padding: 24px 16px 48px;
}

/* ── Logo area ── */
.signup-logo {
    text-align: center;
    margin-bottom: 28px;
}
.signup-logo img { width: 160px; }
.signup-logo p {
    color: #6b7280;
    font-size: 14px;
    margin-top: 8px;
    margin-bottom: 0;
}

/* ── Role selector cards ── */
.role-selector {
    display: flex;
    gap: 10px;
    margin-bottom: 24px;
    background: #fff;
    border-radius: 16px;
    padding: 8px;
    box-shadow: 0 1px 8px rgba(0,0,0,.07);
}
.role-btn {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: 14px 8px;
    border: 2px solid transparent;
    border-radius: 12px;
    background: transparent;
    cursor: pointer;
    transition: all .2s ease;
    font-size: 13px;
    font-weight: 600;
    color: #9ca3af;
}
.role-btn .role-icon {
    width: 40px; height: 40px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    background: #f3f4f6;
    transition: all .2s;
}
.role-btn:hover { color: #374151; }
.role-btn:hover .role-icon { background: #e5e7eb; }

.role-btn.active-customer { border-color: #059669; color: #059669; }
.role-btn.active-customer .role-icon { background: #d1fae5; color: #059669; }

.role-btn.active-farmer { border-color: #dc2626; color: #dc2626; }
.role-btn.active-farmer .role-icon { background: #fee2e2; color: #dc2626; }

.role-btn.active-supplier { border-color: #d97706; color: #d97706; }
.role-btn.active-supplier .role-icon { background: #fef3c7; color: #d97706; }

/* ── Form card ── */
.form-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 2px 16px rgba(0,0,0,.08);
    overflow: hidden;
}
.form-card-header {
    padding: 20px 24px 16px;
    border-bottom: 1px solid #f3f4f6;
    display: flex;
    align-items: center;
    gap: 12px;
}
.form-card-header .header-icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
}
.header-icon-customer { background: #d1fae5; color: #059669; }
.header-icon-farmer   { background: #fee2e2; color: #dc2626; }
.header-icon-supplier { background: #fef3c7; color: #d97706; }

.form-card-header h5 { margin: 0; font-weight: 700; font-size: 17px; color: #111827; }
.form-card-header p  { margin: 0; font-size: 12px; color: #9ca3af; }

.form-body { padding: 24px; }

/* ── Section label ── */
.section-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #9ca3af;
    margin: 20px 0 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.section-label::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #f3f4f6;
}
.section-label:first-child { margin-top: 0; }

/* ── Input fields ── */
.field-group { margin-bottom: 14px; }
.field-row { display: flex; gap: 12px; }
.field-row .field-group { flex: 1; }

.field-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 5px;
}
.field-label .req { color: #ef4444; margin-left: 2px; }

.field-input {
    width: 100%;
    padding: 10px 13px;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    color: #111827;
    background: #fafafa;
    transition: all .2s;
    outline: none;
}
.field-input:focus {
    border-color: #10b981;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(16,185,129,.1);
}
.field-input.farmer-focus:focus  { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,.1); }
.field-input.supplier-focus:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,.1); }

select.field-input { cursor: pointer; }

/* ── Barangay dropdown ── */
.barangay-wrap { position: relative; }
.barangay-results {
    position: absolute;
    top: calc(100% + 4px);
    left: 0; right: 0;
    background: #fff;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
    z-index: 9999;
    display: none;
    max-height: 200px;
    overflow-y: auto;
}
.barangay-results li {
    list-style: none;
    padding: 9px 13px;
    font-size: 13px;
    cursor: pointer;
    border-bottom: 1px solid #f9fafb;
    color: #374151;
}
.barangay-results li:last-child { border-bottom: none; }
.barangay-results li:hover { background: #f0fdf4; color: #059669; }

/* ── Password strength ── */
.pw-hints { display: flex; gap: 8px; margin-top: 6px; flex-wrap: wrap; }
.pw-hint {
    font-size: 11px; padding: 3px 8px;
    border-radius: 20px; font-weight: 600;
    display: none;
}
.pw-hint.bad  { background: #fee2e2; color: #dc2626; display: inline-block; }
.pw-hint.ok   { background: #d1fae5; color: #059669; display: inline-block; }

/* ── Show/hide password ── */
.pw-wrap { position: relative; }
.pw-toggle {
    position: absolute; right: 12px; top: 50%;
    transform: translateY(-50%);
    color: #9ca3af; cursor: pointer; font-size: 14px;
}
.pw-toggle:hover { color: #374151; }

/* ── ID Photo upload ── */
.id-upload-box {
    border: 2px dashed #d1fae5;
    border-radius: 14px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all .2s;
    background: #f9fafb;
}
.id-upload-box:hover { border-color: #10b981; background: #f0fdf4; }
.id-preview {
    width: 100px; height: 70px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #e5e7eb;
    margin-bottom: 8px;
}
.id-upload-box p { font-size: 12px; color: #9ca3af; margin: 0; }

/* ── Terms ── */
.terms-row {
    display: flex; align-items: flex-start;
    gap: 10px; margin: 18px 0;
}
.terms-row input[type=checkbox] { margin-top: 2px; flex-shrink: 0; width: 16px; height: 16px; cursor: pointer; }
.terms-row label { font-size: 13px; color: #6b7280; cursor: pointer; line-height: 1.5; }
.terms-row a { color: #059669; text-decoration: none; font-weight: 600; }
.terms-row a:hover { text-decoration: underline; }

/* ── Submit button ── */
.btn-signup {
    width: 100%; padding: 13px;
    border: none; border-radius: 12px;
    font-size: 15px; font-weight: 700;
    cursor: pointer; transition: all .2s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    margin-bottom: 12px;
}
.btn-signup:disabled { opacity: .55; cursor: not-allowed; }
.btn-signup:not(:disabled):hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(0,0,0,.15); }

.btn-customer { background: #059669; color: #fff; }
.btn-farmer   { background: #dc2626; color: #fff; }
.btn-supplier { background: #d97706; color: #fff; }

.btn-login-link {
    width: 100%; padding: 12px;
    border: 1.5px solid #e5e7eb;
    border-radius: 12px;
    background: transparent;
    font-size: 14px; color: #374151;
    font-weight: 600; text-align: center;
    text-decoration: none;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: all .2s;
}
.btn-login-link:hover { background: #f9fafb; color: #111827; border-color: #d1d5db; }

/* ── Error alert ── */
.alert-signup {
    background: #fee2e2; color: #dc2626;
    border-radius: 10px; padding: 12px 16px;
    font-size: 13px; font-weight: 600;
    margin-bottom: 16px;
    display: flex; align-items: center; gap: 8px;
}
.alert-signup.warning { background: #fef3c7; color: #92400e; }

/* ── Tab panels ── */
.tab-pane { display: none; }
.tab-pane.active { display: block; }

/* ── Responsive ── */
@media (max-width: 480px) {
    .field-row { flex-direction: column; gap: 0; }
    .role-btn { font-size: 12px; padding: 12px 4px; }
    .role-btn .role-icon { width: 34px; height: 34px; font-size: 15px; }
    .form-body { padding: 16px; }
}
</style>

<body>
<div class="signup-wrapper">

    <!-- Logo -->
    <div class="signup-logo">
        <a href="<?= base_url() ?>index">
            <img src="<?= base_url() ?>dist/layout_shop/images/logo.svg" alt="AgriShop">
        </a>
        <p>Create your account to get started</p>
    </div>

    <?php
    $signup_attempt = $this->input->get("signup_attempt");
    if ($signup_attempt == md5(0)): ?>
        <div class="alert-signup">
            <i class="fa fa-exclamation-triangle"></i> Please fill in all required fields.
        </div>
    <?php elseif ($signup_attempt == md5(1)): ?>
        <div class="alert-signup warning">
            <i class="fa fa-exclamation-triangle"></i> Username already exists. Please try a different one.
        </div>
    <?php endif; ?>

    <!-- Role selector -->
    <div class="role-selector">
        <button class="role-btn active-customer" onclick="switchRole('customer')" id="btn-customer" type="button">
            <div class="role-icon"><i class="fa fa-user"></i></div>
            Customer
        </button>
        <button class="role-btn" onclick="switchRole('farmer')" id="btn-farmer" type="button">
            <div class="role-icon"><i class="fa fa-tractor"></i></div>
            Farmer
        </button>
        <button class="role-btn" onclick="switchRole('supplier')" id="btn-supplier" type="button">
            <div class="role-icon"><i class="fa fa-store"></i></div>
            Supplier
        </button>
    </div>

    <!-- Form card -->
    <div class="form-card">

        <!-- Card header (changes per role) -->
        <div class="form-card-header">
            <div class="header-icon header-icon-customer" id="card-icon">
                <i class="fa fa-user"></i>
            </div>
            <div>
                <h5 id="card-title">Customer Account</h5>
                <p id="card-sub">Shop farm-fresh produce &amp; agricultural supplies</p>
            </div>
        </div>

        <div class="form-body">

<!-- ══════════════════════════════════════════════════════
     CUSTOMER FORM
══════════════════════════════════════════════════════ -->
<div class="tab-pane active" id="pane-customer">
<?= form_open(base_url('/requestsignup'), 'id="form_save_dataRequestSignupCustomer" enctype="multipart/form-data"') ?>
<input type="hidden" name="signup_type" value="customer">

<div class="section-label"><i class="fa fa-user"></i> Personal Information</div>

<div class="field-row">
    <div class="field-group">
        <label class="field-label">First Name <span class="req">*</span></label>
        <input type="text" name="firstname" class="field-input text-uppercase" placeholder="e.g. JUAN" autocomplete="off" required>
    </div>
    <div class="field-group">
        <label class="field-label">Middle Name</label>
        <input type="text" name="middlename" class="field-input text-uppercase" placeholder="Optional" autocomplete="off" nr="1">
    </div>
</div>
<div class="field-group">
    <label class="field-label">Last Name <span class="req">*</span></label>
    <input type="text" name="lastname" class="field-input text-uppercase" placeholder="e.g. DELA CRUZ" autocomplete="off" required>
</div>
<div class="field-row">
    <div class="field-group">
        <label class="field-label">Gender <span class="req">*</span></label>
        <select name="sex" class="field-input" required>
            <option value="" disabled selected>Select</option>
            <option value="MALE">Male</option>
            <option value="FEMALE">Female</option>
        </select>
    </div>
    <div class="field-group">
        <label class="field-label">Birth Date <span class="req">*</span></label>
        <input type="date" name="birthDate" class="field-input" required>
    </div>
</div>
<div class="field-row">
    <div class="field-group">
        <label class="field-label">Contact Number</label>
        <input type="tel" name="contact" class="field-input" placeholder="09XXXXXXXXX" nr="1">
    </div>
    <div class="field-group">
        <label class="field-label">Email Address</label>
        <input type="email" name="email" class="field-input" placeholder="you@email.com" nr="1">
    </div>
</div>

<div class="section-label"><i class="fa fa-map-marker-alt"></i> Address</div>
<div class="field-group">
    <label class="field-label">House No. / Street / Sitio / Purok</label>
    <input type="text" name="address_info" class="field-input text-uppercase" placeholder="e.g. Blk 1 Lot 2 Sampaguita St." autocomplete="off" nr="1">
</div>
<div class="field-group">
    <label class="field-label">Barangay <span class="req">*</span></label>
    <div class="barangay-wrap">
        <input type="hidden" name="barangay" id="c_barangay_id">
        <input type="text" class="field-input brgy-input" data-hidden="c_barangay_id"
               placeholder="Type at least 3 characters..." autocomplete="off" required>
        <ul class="barangay-results"></ul>
    </div>
</div>

<div class="section-label"><i class="fa fa-lock"></i> Account Setup</div>
<div class="field-group">
    <label class="field-label">Username <span class="req">*</span></label>
    <input type="text" name="username" class="field-input" placeholder="Choose a username" autocomplete="off" required>
</div>
<div class="field-row">
    <div class="field-group">
        <label class="field-label">Password <span class="req">*</span></label>
        <div class="pw-wrap">
            <input type="password" name="password" class="field-input pw-field" placeholder="Min 8 characters" minlength="8" required>
            <span class="pw-toggle" onclick="togglePw(this)"><i class="fa fa-eye"></i></span>
        </div>
    </div>
    <div class="field-group">
        <label class="field-label">Confirm Password <span class="req">*</span></label>
        <div class="pw-wrap">
            <input type="password" name="password2" class="field-input pw-confirm" placeholder="Repeat password" required>
            <span class="pw-toggle" onclick="togglePw(this)"><i class="fa fa-eye"></i></span>
        </div>
    </div>
</div>
<div class="pw-hints" id="c-pw-hints"></div>

<div class="terms-row">
    <input type="checkbox" id="c_terms" name="terms" value="agree" required>
    <label for="c_terms">I agree to the <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a></label>
</div>
<button type="submit" class="btn-signup btn-customer submitBtnPrimary" disabled>
    <i class="fa fa-user-plus"></i> Create Account
</button>
<a href="<?= base_url() ?>login" class="btn-login-link">
    <i class="fa fa-sign-in-alt"></i> Already have an account? Login
</a>
<?= form_close() ?>
</div>


<!-- ══════════════════════════════════════════════════════
     FARMER FORM
══════════════════════════════════════════════════════ -->
<div class="tab-pane" id="pane-farmer">
<?= form_open(base_url('/requestsignup'), 'id="form_save_dataRequestSignupFarmer" enctype="multipart/form-data"') ?>
<input type="hidden" name="signup_type" value="farmer">

<div class="section-label"><i class="fa fa-user"></i> Personal Information</div>
<div class="field-row">
    <div class="field-group">
        <label class="field-label">First Name <span class="req">*</span></label>
        <input type="text" name="firstname" class="field-input farmer-focus text-uppercase" placeholder="First Name" autocomplete="off" required>
    </div>
    <div class="field-group">
        <label class="field-label">Middle Name</label>
        <input type="text" name="middlename" class="field-input farmer-focus text-uppercase" placeholder="Optional" autocomplete="off" nr="1">
    </div>
</div>
<div class="field-group">
    <label class="field-label">Last Name <span class="req">*</span></label>
    <input type="text" name="lastname" class="field-input farmer-focus text-uppercase" placeholder="Last Name" autocomplete="off" required>
</div>
<div class="field-row">
    <div class="field-group">
        <label class="field-label">Gender <span class="req">*</span></label>
        <select name="sex" class="field-input farmer-focus" required>
            <option value="" disabled selected>Select</option>
            <option value="MALE">Male</option>
            <option value="FEMALE">Female</option>
        </select>
    </div>
    <div class="field-group">
        <label class="field-label">Birth Date <span class="req">*</span></label>
        <input type="date" name="birthDate" class="field-input farmer-focus" required>
    </div>
</div>
<div class="field-row">
    <div class="field-group">
        <label class="field-label">Contact Number <span class="req">*</span></label>
        <input type="tel" name="contact" class="field-input farmer-focus" placeholder="09XXXXXXXXX" required>
    </div>
    <div class="field-group">
        <label class="field-label">Email Address</label>
        <input type="email" name="email" class="field-input farmer-focus" placeholder="Optional" nr="1">
    </div>
</div>

<div class="section-label"><i class="fa fa-map-marker-alt"></i> Address</div>
<div class="field-group">
    <label class="field-label">House No. / Street / Sitio / Purok</label>
    <input type="text" name="address_info" class="field-input farmer-focus text-uppercase" placeholder="e.g. Sitio Maligaya" autocomplete="off" nr="1">
</div>
<div class="field-group">
    <label class="field-label">Barangay <span class="req">*</span></label>
    <div class="barangay-wrap">
        <input type="hidden" name="barangay" id="f_barangay_id">
        <input type="text" class="field-input farmer-focus brgy-input" data-hidden="f_barangay_id"
               placeholder="Type at least 3 characters..." autocomplete="off" required>
        <ul class="barangay-results"></ul>
    </div>
</div>

<div class="section-label"><i class="fa fa-tractor"></i> Farmer Details</div>
<div class="field-row">
    <div class="field-group">
        <label class="field-label">Selling Type <span class="req">*</span></label>
        <select name="farmer_selling_type" class="field-input farmer-focus" required>
            <option value="1">Farm Produce</option>
            <option value="2">Farm Tools &amp; Equipment</option>
            <option value="3">Both</option>
        </select>
    </div>
    <div class="field-group">
        <label class="field-label">Valid Government ID <span class="req">*</span></label>
        <select name="valid_id" class="field-input farmer-focus" required>
            <option value="" disabled selected>Select ID</option>
            <option>Philippine National ID (PhilSys)</option>
            <option value="SSS ID">SSS ID</option>
            <option value="UMID">UMID</option>
            <option value="DRIVER'S LICENSE">Driver's License</option>
            <option value="PASSPORT">Passport</option>
            <option value="POSTAL ID">Postal ID</option>
            <option value="PRC ID">PRC ID</option>
            <option value="TIN ID">TIN ID</option>
            <option value="VOTER'S ID">Voter's ID</option>
            <option value="PHILHEALTH ID">PhilHealth ID</option>
            <option value="SENIOR CITIZEN ID">Senior Citizen ID</option>
            <option value="PWD ID">PWD ID</option>
            <option value="GSIS ID">GSIS ID</option>
            <option value="PAG-IBIG ID">Pag-IBIG ID</option>
            <option value="AFP ID">AFP ID</option>
            <option value="STUDENT ID (if minor)">Student ID (if minor)</option>
        </select>
    </div>
</div>
<div class="field-group">
    <label class="field-label">Farmers Organization <small style="color:#9ca3af;font-weight:400;">(Optional)</small></label>
    <select name="organization" class="field-input farmer-focus select2-org" nr="1">
        <option value="">— None —</option>
        <?php $this->load->view('interface/system/layout/options_select_for_organization') ?>
    </select>
</div>

<!-- ID Photo -->
<div class="field-group">
    <label class="field-label">ID Photo <span class="req">*</span></label>
    <div class="id-upload-box" onclick="$('[name=picFarmerID]').click()">
        <img name="previewPicFarmerID"
             src="<?= base_url('dist/img/media/icons/id_preview.png') ?>"
             class="id-preview" alt="ID Preview">
        <p><i class="fa fa-camera mr-1"></i> Click to upload your government ID photo</p>
        <p style="margin-top:4px;font-size:11px;color:#d1d5db;">Max 25MB · JPG, PNG</p>
    </div>
    <input name="picFarmerID" type="file" accept="image/*"
           onchange="previewID('picFarmerID','previewPicFarmerID')" hidden required>
</div>

<div class="section-label"><i class="fa fa-lock"></i> Account Setup</div>
<div class="field-group">
    <label class="field-label">Username <span class="req">*</span></label>
    <input type="text" name="username" class="field-input farmer-focus" placeholder="Choose a username" autocomplete="off" required>
</div>
<div class="field-row">
    <div class="field-group">
        <label class="field-label">Password <span class="req">*</span></label>
        <div class="pw-wrap">
            <input type="password" name="password" class="field-input farmer-focus pw-field" placeholder="Min 8 characters" minlength="8" required>
            <span class="pw-toggle" onclick="togglePw(this)"><i class="fa fa-eye"></i></span>
        </div>
    </div>
    <div class="field-group">
        <label class="field-label">Confirm Password <span class="req">*</span></label>
        <div class="pw-wrap">
            <input type="password" name="password2" class="field-input farmer-focus pw-confirm" placeholder="Repeat password" required>
            <span class="pw-toggle" onclick="togglePw(this)"><i class="fa fa-eye"></i></span>
        </div>
    </div>
</div>
<div class="pw-hints" id="f-pw-hints"></div>

<div class="terms-row">
    <input type="checkbox" id="f_terms" name="terms" value="agree" required>
    <label for="f_terms">I agree to the <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a></label>
</div>
<button type="submit" class="btn-signup btn-farmer submitBtnPrimary" disabled>
    <i class="fa fa-tractor"></i> Register as Farmer
</button>
<a href="<?= base_url() ?>login" class="btn-login-link">
    <i class="fa fa-sign-in-alt"></i> Already have an account? Login
</a>
<?= form_close() ?>
</div>


<!-- ══════════════════════════════════════════════════════
     SUPPLIER FORM
══════════════════════════════════════════════════════ -->
<div class="tab-pane" id="pane-supplier">
<?= form_open(base_url('/requestsignup'), 'id="form_save_dataRequestSignupSupplier" enctype="multipart/form-data"') ?>
<input type="hidden" name="signup_type" value="supplier">

<div class="section-label"><i class="fa fa-user"></i> Personal Information</div>
<div class="field-row">
    <div class="field-group">
        <label class="field-label">First Name <span class="req">*</span></label>
        <input type="text" name="firstname" class="field-input supplier-focus text-uppercase" placeholder="First Name" autocomplete="off" required>
    </div>
    <div class="field-group">
        <label class="field-label">Middle Name</label>
        <input type="text" name="middlename" class="field-input supplier-focus text-uppercase" placeholder="Optional" autocomplete="off" nr="1">
    </div>
</div>
<div class="field-group">
    <label class="field-label">Last Name <span class="req">*</span></label>
    <input type="text" name="lastname" class="field-input supplier-focus text-uppercase" placeholder="Last Name" autocomplete="off" required>
</div>
<div class="field-row">
    <div class="field-group">
        <label class="field-label">Gender <span class="req">*</span></label>
        <select name="sex" class="field-input supplier-focus" required>
            <option value="" disabled selected>Select</option>
            <option value="MALE">Male</option>
            <option value="FEMALE">Female</option>
        </select>
    </div>
    <div class="field-group">
        <label class="field-label">Birth Date <span class="req">*</span></label>
        <input type="date" name="birthDate" class="field-input supplier-focus" required>
    </div>
</div>
<div class="field-row">
    <div class="field-group">
        <label class="field-label">Contact Number <span class="req">*</span></label>
        <input type="tel" name="contact" class="field-input supplier-focus" placeholder="09XXXXXXXXX" required>
    </div>
    <div class="field-group">
        <label class="field-label">Email Address</label>
        <input type="email" name="email" class="field-input supplier-focus" placeholder="Optional" nr="1">
    </div>
</div>

<div class="section-label"><i class="fa fa-map-marker-alt"></i> Address</div>
<div class="field-group">
    <label class="field-label">House No. / Street / Sitio / Purok</label>
    <input type="text" name="address_info" class="field-input supplier-focus text-uppercase" placeholder="e.g. Blk 1 Lot 2 Rizal Ave." autocomplete="off" nr="1">
</div>
<div class="field-group">
    <label class="field-label">Barangay <span class="req">*</span></label>
    <div class="barangay-wrap">
        <input type="hidden" name="barangay" id="s_barangay_id">
        <input type="text" class="field-input supplier-focus brgy-input" data-hidden="s_barangay_id"
               placeholder="Type at least 3 characters..." autocomplete="off" required>
        <ul class="barangay-results"></ul>
    </div>
</div>

<div class="section-label"><i class="fa fa-store"></i> Supplier Details</div>
<div class="field-row">
    <div class="field-group">
        <label class="field-label">Valid Government ID <span class="req">*</span></label>
        <select name="valid_id" class="field-input supplier-focus" required>
            <option value="" disabled selected>Select ID</option>
            <option>Philippine National ID (PhilSys)</option>
            <option value="SSS ID">SSS ID</option>
            <option value="UMID">UMID</option>
            <option value="DRIVER'S LICENSE">Driver's License</option>
            <option value="PASSPORT">Passport</option>
            <option value="POSTAL ID">Postal ID</option>
            <option value="PRC ID">PRC ID</option>
            <option value="TIN ID">TIN ID</option>
            <option value="VOTER'S ID">Voter's ID</option>
            <option value="PHILHEALTH ID">PhilHealth ID</option>
            <option value="GSIS ID">GSIS ID</option>
            <option value="PAG-IBIG ID">Pag-IBIG ID</option>
        </select>
    </div>
    <div class="field-group">
        <label class="field-label">Business / Store Name <small style="color:#9ca3af;font-weight:400;">(Optional)</small></label>
        <input type="text" name="business_name" class="field-input supplier-focus text-uppercase" placeholder="e.g. AGRI SUPPLY STORE" nr="1">
    </div>
</div>

<!-- ID Photo -->
<div class="field-group">
    <label class="field-label">ID Photo <span class="req">*</span></label>
    <div class="id-upload-box" onclick="$('[name=picSupplierID]').click()">
        <img name="previewPicSupplierID"
             src="<?= base_url('dist/img/media/icons/id_preview.png') ?>"
             class="id-preview" alt="ID Preview">
        <p><i class="fa fa-camera mr-1"></i> Click to upload your government ID photo</p>
        <p style="margin-top:4px;font-size:11px;color:#d1d5db;">Max 25MB · JPG, PNG</p>
    </div>
    <input name="picSupplierID" type="file" accept="image/*"
           onchange="previewID('picSupplierID','previewPicSupplierID')" hidden required>
</div>

<div class="section-label"><i class="fa fa-lock"></i> Account Setup</div>
<div class="field-group">
    <label class="field-label">Username <span class="req">*</span></label>
    <input type="text" name="username" class="field-input supplier-focus" placeholder="Choose a username" autocomplete="off" required>
</div>
<div class="field-row">
    <div class="field-group">
        <label class="field-label">Password <span class="req">*</span></label>
        <div class="pw-wrap">
            <input type="password" name="password" class="field-input supplier-focus pw-field" placeholder="Min 8 characters" minlength="8" required>
            <span class="pw-toggle" onclick="togglePw(this)"><i class="fa fa-eye"></i></span>
        </div>
    </div>
    <div class="field-group">
        <label class="field-label">Confirm Password <span class="req">*</span></label>
        <div class="pw-wrap">
            <input type="password" name="password2" class="field-input supplier-focus pw-confirm" placeholder="Repeat password" required>
            <span class="pw-toggle" onclick="togglePw(this)"><i class="fa fa-eye"></i></span>
        </div>
    </div>
</div>
<div class="pw-hints" id="s-pw-hints"></div>

<div class="terms-row">
    <input type="checkbox" id="s_terms" name="terms" value="agree" required>
    <label for="s_terms">I agree to the <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a></label>
</div>
<button type="submit" class="btn-signup btn-supplier submitBtnPrimary" disabled>
    <i class="fa fa-store"></i> Register as Supplier
</button>
<a href="<?= base_url() ?>login" class="btn-login-link">
    <i class="fa fa-sign-in-alt"></i> Already have an account? Login
</a>
<?= form_close() ?>
</div>

        </div><!-- end form-body -->
    </div><!-- end form-card -->
</div><!-- end signup-wrapper -->

<script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>
<script src="<?= base_url() ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url() ?>plugins/sweetalert2/sweetalert2.min.js"></script>

<script>
// ── Role config ──────────────────────────────────────────
const roleConfig = {
    customer: {
        btnClass:   'active-customer',
        iconClass:  'header-icon-customer',
        icon:       '<i class="fa fa-user"></i>',
        title:      'Customer Account',
        sub:        'Shop farm-fresh produce &amp; agricultural supplies',
    },
    farmer: {
        btnClass:   'active-farmer',
        iconClass:  'header-icon-farmer',
        icon:       '<i class="fa fa-tractor"></i>',
        title:      'Farmer Account',
        sub:        'Register to sell your farm produce &amp; products',
    },
    supplier: {
        btnClass:   'active-supplier',
        iconClass:  'header-icon-supplier',
        icon:       '<i class="fa fa-store"></i>',
        title:      'Supplier Account',
        sub:        'Register to sell agricultural supplies &amp; equipment',
    }
};

function switchRole(role) {
    // Update buttons
    ['customer','farmer','supplier'].forEach(r => {
        const btn = document.getElementById('btn-' + r);
        btn.className = 'role-btn';
        if (r === role) btn.classList.add(roleConfig[r].btnClass);
    });

    // Update card header
    const cfg = roleConfig[role];
    const icon = document.getElementById('card-icon');
    icon.className = 'header-icon ' + cfg.iconClass;
    icon.innerHTML = cfg.icon;
    document.getElementById('card-title').textContent = cfg.title.replace('&amp;','&');
    document.getElementById('card-sub').innerHTML = cfg.sub;

    // Show correct pane
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    document.getElementById('pane-' + role).classList.add('active');
}

// ── Show/hide password ───────────────────────────────────
function togglePw(span) {
    const input = span.previousElementSibling;
    const icon  = span.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fa fa-eye';
    }
}

// ── ID photo preview ─────────────────────────────────────
function previewID(inputName, previewName) {
    const file = document.querySelector('[name=' + inputName + ']').files[0];
    if (!file) return;
    if (file.size > 25 * 1024 * 1024) { alert('Image must be less than 25MB'); return; }
    const reader = new FileReader();
    reader.onload = e => document.querySelector('[name=' + previewName + ']').src = e.target.result;
    reader.readAsDataURL(file);
}

// ── Password validation per pane ─────────────────────────
function initPwCheck(paneId, hintsId) {
    const pane  = document.getElementById(paneId);
    const hints = document.getElementById(hintsId);
    const pw    = pane.querySelector('.pw-field');
    const cpw   = pane.querySelector('.pw-confirm');
    const btn   = pane.querySelector('.submitBtnPrimary');

    function check() {
        const val = pw.value, cval = cpw.value;
        const lenOk   = val.length >= 8;
        const matchOk = val === cval && cval !== '';
        hints.innerHTML = '';
        if (val.length > 0 && !lenOk)
            hints.innerHTML += '<span class="pw-hint bad">❌ Min 8 characters</span>';
        if (val.length >= 8 && cval.length > 0 && !matchOk)
            hints.innerHTML += '<span class="pw-hint bad">❌ Passwords don\'t match</span>';
        if (lenOk && matchOk)
            hints.innerHTML += '<span class="pw-hint ok">✅ Passwords match</span>';
        btn.disabled = !(lenOk && matchOk);
    }
    pw.addEventListener('keyup', check);
    cpw.addEventListener('keyup', check);
}

initPwCheck('pane-customer', 'c-pw-hints');
initPwCheck('pane-farmer',   'f-pw-hints');
initPwCheck('pane-supplier', 's-pw-hints');

// ── Barangay search ──────────────────────────────────────
$(document).on('keyup', '.brgy-input', function() {
    const $inp  = $(this);
    const $ul   = $inp.siblings('.barangay-results');
    const kw    = $inp.val();
    if (kw.length < 3) { $ul.hide(); return; }

    $.post("<?= base_url('search-barangay') ?>", { keyword: kw }, function(res) {
        const data = JSON.parse(res);
        if (!data.length) { $ul.hide(); return; }
        $ul.html(data.map(r =>
            `<li data-id="${r.id}" data-name="${r.text}">${r.text}</li>`
        ).join('')).show();
    });
});

$(document).on('click', '.barangay-results li', function() {
    const $li  = $(this);
    const $ul  = $li.closest('.barangay-results');
    const $inp = $ul.siblings('.brgy-input');
    const hid  = $inp.data('hidden');
    $inp.val($li.data('name'));
    $('#' + hid).val($li.data('id'));
    $ul.hide();
});

$(document).on('click', function(e) {
    if (!$(e.target).hasClass('brgy-input')) {
        $('.barangay-results').hide();
    }
});

// ── Toast helpers ────────────────────────────────────────
const Toast = Swal.mixin({ toast:true, position:'center', showConfirmButton:false, timer:3000 });
const successAlert = a => Toast.fire({ icon:'success', title:' '+a });
const failAlert    = a => Toast.fire({ icon:'error',   title:' '+a });
const existAlert   = a => Toast.fire({ icon:'warning', title:' '+a });

// ── Form AJAX submit ─────────────────────────────────────
function bindForm(id) {
    let txt = '';
    $('#form_save_data' + id).ajaxForm({
        clearForm: false,
        resetForm: false,
        beforeSubmit: function() {
            const $form = $('#form_save_data' + id);
            const pw  = $form.find('.pw-field').val();
            const cpw = $form.find('.pw-confirm').val();
            if (pw !== cpw)    { existAlert('Passwords do not match.'); return false; }
            if (pw.length < 8) { existAlert('Password must be at least 8 characters.'); return false; }
            const em = $form.find('[name=email]').val();
            if (em && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) {
                existAlert('Please enter a valid email address.'); return false;
            }
            txt = $form.find('.submitBtnPrimary').text();
            $form.find('.submitBtnPrimary').attr('disabled',true)
                 .html('<i class="fa fa-spinner fa-spin mr-1"></i> Registering...');
        },
        success: function(data) {
            const d = JSON.parse(data);
            if (d.success)          { successAlert('Successfully Registered!'); setTimeout(()=>location.href=d.redirect_to,1500); }
            else if (d.exist)       { existAlert('Username already exists!'); }
            else if (d.fill)        { existAlert('Please fill in all required fields.'); }
            else if (d.email_invalid){ existAlert('Please enter a valid email address.'); }
            else if (d.password)    { existAlert('Passwords do not match.'); }
            else                    { failAlert('Something went wrong!'); }
            const $btn = $('#form_save_data' + id + ' .submitBtnPrimary');
            $btn.attr('disabled', false).html(txt);
        },
        error: function() {
            failAlert('Something went wrong!');
            $('#form_save_data' + id + ' .submitBtnPrimary').attr('disabled',false).html(txt);
        }
    });
}

bindForm('RequestSignupCustomer');
bindForm('RequestSignupFarmer');
bindForm('RequestSignupSupplier');
</script>
</body>
</html>
