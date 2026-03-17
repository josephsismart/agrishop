<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>
<?php
if (!$this->session->agrishop_login_level) {
    redirect(base_url('login'));
}
$uri = $this->session->agrishop_login_uri;
?>



<div class="modal fade" id="modalApproveFarmer">
    <!-- <div class="modal fade show" id="modalProductionStatus" tabindex="-1" aria-labelledby="modalCheckoutLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content rounded shadow">
            <!-- HEADER -->
            <div class="modal-header py-2">
                <h5 class="modal-title mb-0">
                    <i class="fas fa-user-check mr-1"></i> Farmer Approval
                </h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-3 py-2">
                <i>Farmer presented ID</i>
                <div class="form-check mt-2">
                    <div class="form-check form-switch" style="overflow: auto;">
                        <img src="" id="idFarmerImg" alt="ID Image" class="img-fluid" style="max-width: 200px;">
                    </div>
                </div>
                <button type="button" class="btn btn-primary btn-sm w-100 mt-3" onclick="approveFarmerNow()"><i class="fas fa-check"></i> Approve Farmer</button>
            </div>

        </div>
    </div>
</div>


<!-- VIEW PAYMENT DETAILS MODAL -->
<div class="modal fade" id="viewPaymentDetailsModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <!-- Header -->
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold">View Payment Details</h5>
                <button class="btn-close" data-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body text-center">

                <!-- QR Payment Section -->
                <div class="card border-0 shadow-sm rounded-4 p-0" style="background:#f8f9fa; align-items: center;">

                    <!-- QR IMAGE -->
                    <img src="" class="img-fluid rounded-3 shadow-sm mb-3" style="max-width:400px;" id="paymentProofImg" alt="Payment Proof">
                </div>

            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0 rounded-3 overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header bg-primary bg-gradient text-white py-3 px-4">
                <div class="d-flex align-items-center w-100">
                    <div class="flex-grow-1">
                        <h1 class="modal-title fw-bold mb-0">
                            <i class="fas fa-user-circle me-2"></i>My Profile
                        </h1>
                    </div>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <?= form_open(base_url('/updateprofile'), 'id="form_save_dataUpdateProfile"'); ?>

            <!-- BODY -->
            <div class="modal-body p-0">
                <div class="row g-0">

                    <!-- LEFT SIDE - PROFILE PHOTO -->
                    <div class="col-lg-4 col-md-5 border-end bg-light">
                        <div class="p-4">
                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block mb-3">
                                    <img name="previewPic" src="<?= $this->session->agrishop_login_img_path ?>" onclick="$('[name=picProfile]').trigger('click')" width="140" height="140" class="border border-3 border-white shadow-lg rounded-circle object-fit-cover" style="cursor: pointer;" alt="Profile Picture">
                                    <div class="position-absolute bottom-0 end-0">
                                        <span class="badge bg-primary rounded-circle p-2 shadow-sm">
                                            <i class="fas fa-camera fa-xs text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <h6 class="text-muted fw-bold mb-2">
                                    <i class="fas fa-camera-retro me-1"></i>Profile Photo
                                </h6>
                                <p class="small text-muted mb-0">Click image to upload new photo</p>
                            </div>
                            <input name="picProfile" type="file" accept="image/*" onchange="imageView('picProfile','previewPic','imgtargetLink')" nr="1" hidden>
                            <input name="img_path" type="text" nr="1" hidden>
                        </div>
                    </div>

                    <!-- RIGHT SIDE - FORM FIELDS -->
                    <div class="col-lg-8 col-md-7">
                        <div class="p-4">

                            <!-- NAME SECTION -->
                            <div class="mb-4">
                                <h6 class="text-primary fw-bold mb-3">
                                    <i class="fas fa-user me-2"></i>Personal Information
                                </h6>
                                <div class="row g-3">
                                    <div class="col-lg-4 col-md-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control border-primary text-uppercase" id="firstName" value="<?= $this->session->agrishop_login_first_name ?>" name="firstName" placeholder="FIRST NAME" autocomplete="off">
                                            <label for="firstName" class="text-muted small">First Name</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control border-primary text-uppercase" id="middleName" value="<?= $this->session->agrishop_login_middle_name ?>" name="middleName" placeholder="MIDDLE NAME" autocomplete="off" nr="1">
                                            <label for="middleName" class="text-muted small">Middle Name</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control border-primary text-uppercase" id="lastName" value="<?= $this->session->agrishop_login_last_name ?>" name="lastName" placeholder="LAST NAME" autocomplete="off">
                                            <label for="lastName" class="text-muted small">Last Name</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BIRTHDATE & GENDER -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-muted fw-bold mb-3">
                                        <i class="fas fa-birthday-cake me-2"></i>Birthdate
                                    </h6>
                                    <div class="form-floating">
                                        <input type="date" class="form-control border-primary" id="birthdate" name="birthdate" nr="1" value="<?= $this->session->agrishop_login_birthdate ?>">
                                        <label for="birthdate" class="text-muted small">Select Birthdate</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted fw-bold mb-3">
                                        <i class="fas fa-venus-mars me-2"></i>Gender
                                    </h6>
                                    <div class="form-floating">
                                        <select class="form-select border-primary" id="gender" name="sex">
                                            <option value="t" <?= $this->session->agrishop_login_sex == 't' ? 'selected' : '' ?>>Male</option>
                                            <option value="f" <?= $this->session->agrishop_login_sex == 'f' ? 'selected' : '' ?>>Female</option>
                                        </select>
                                        <label for="gender" class="text-muted small">Select Gender</label>
                                    </div>
                                </div>
                            </div>

                            <!-- CONTACT INFORMATION -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-muted fw-bold mb-3">
                                        <i class="fas fa-envelope me-2"></i>Email Address
                                    </h6>
                                    <div class="form-floating">
                                        <input type="email" class="form-control border-primary" id="email" placeholder="EMAIL" name="email" value="<?= $this->session->agrishop_login_email_address ?>" autocomplete="off">
                                        <label for="email" class="text-muted small">Email Address</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted fw-bold mb-3">
                                        <i class="fas fa-phone me-2"></i>Contact Number
                                    </h6>
                                    <div class="form-floating">
                                        <input type="text" class="form-control border-primary text-uppercase" id="contactNumber" placeholder="Contact Number" name="contactNumber" value="<?= $this->session->agrishop_login_contact_num ?>">
                                        <label for="contactNumber" class="text-muted small">Contact Number</label>
                                    </div>
                                </div>
                            </div>

                            <!-- ADDRESS -->
                            <div class="mb-4">
                                <h6 class="text-muted fw-bold mb-3">
                                    <i class="fas fa-map-marker-alt me-2"></i>Address
                                </h6>
                                <div class="position-relative">
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-primary">
                                            <i class="fas fa-map-pin text-primary"></i>
                                        </span>
                                        <div class="form-floating flex-grow-1">
                                            <input type="text" class="form-control border-start-0 border-primary text-uppercase barangayInput" id="barangayInput" value="<?= $this->session->agrishop_login_address_text ?>" placeholder="TYPE BARANGAY (min 3 chars)" autocomplete="off" name="barangay_text">
                                            <label for="barangayInput" class="text-muted small">Barangay Name</label>
                                        </div>
                                    </div>
                                    <input name="barangayAll" type="hidden" value="<?= $this->session->agrishop_login_barangay_id ?>">
                                    <ul class="list-group barangayResults shadow-lg border-0" style="position:absolute; z-index:9999; width:100%; display:none; cursor:pointer; max-height: 200px; overflow-y: auto;"></ul>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i> Type at least 3 characters to search barangay
                                    </small>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer bg-light py-3 px-4 border-top">
                <div class="d-flex w-100 gap-3">
                    <button type="button" class="btn btn-outline-secondary btn-lg flex-fill rounded-pill" data-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg flex-fill rounded-pill fw-bold shadow-sm update_profile">
                        <i class="fas fa-save me-2"></i>Update Profile
                    </button>
                </div>
            </div>

            </form>
        </div>
    </div>
</div>

<!-- <div class="modal fade show" id="viewGcashModal" tabindex="-1" aria-labelledby="viewGcashModalLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
<div class="modal fade" id="viewGcashModal" tabindex="-1" aria-labelledby="viewGcashModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-3 overflow-hidden border-0">

            <!-- HEADER -->
            <div class="modal-header bg-primary bg-gradient text-white py-3 px-4">
                <div class="d-flex align-items-center w-100">
                    <div class="flex-grow-1">
                        <h6 class="modal-title fw-bold mb-0">
                            <i class="fas fa-wallet me-2"></i>Proof of payment
                        </h6>
                    </div>
                    <button type="button" class="btn-close btn-close-white shadow-none m-0" data-dismiss="modal"></button>
                </div>
            </div>

            <!-- BODY -->
            <div class="modal-body p-4">
                <div class="text-center proof_payment">
                </div>
            </div>
        </div>
    </div>
</div>


<!-- <div class="modal fade show" id="gcashModal" tabindex="-1" aria-labelledby="gcashModalLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
<div class="modal fade" id="gcashModal" tabindex="-1" aria-labelledby="gcashModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-3 overflow-hidden border-0">

            <!-- HEADER -->
            <div class="modal-header bg-primary bg-gradient text-white py-3 px-4">
                <div class="d-flex align-items-center w-100">
                    <div class="flex-grow-1">
                        <h6 class="modal-title fw-bold mb-0">
                            <i class="fas fa-wallet me-2"></i>My GCash Account
                        </h6>
                    </div>
                    <button type="button" class="btn-close btn-close-white shadow-none m-0" data-dismiss="modal"></button>
                </div>
            </div>

            <?= form_open(base_url('/updategcash'), 'id="form_save_dataUpdateGcash"'); ?>

            <!-- BODY -->
            <div class="modal-body p-4">

                <!-- QR UPLOAD -->
                <div class="text-center mb-n1">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <span class="badge bg-light text-primary rounded-pill px-3 py-2">
                            <i class="fas fa-qrcode me-2"></i>
                            <span class="fw-bold">Upload QR Code</span>
                        </span>
                    </div>

                    <div class="position-relative d-inline-block">
                        <img name="previewPic" src="<?= $this->session->agrishop_login_gcash_qr ?>" onclick="$('[name=picGcash]').trigger('click')" width="160" height="160" class="rounded-3 border border-3 border-primary shadow" style="cursor:pointer; object-fit:cover;" alt="GCash QR Code" nr="1">
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-primary rounded-circle p-2">
                                <i class="fas fa-camera fa-sm"></i>
                            </span>
                        </div>
                    </div>

                    <input name="picGcash" type="file" accept="image/*" onchange="imageView('picGcash','previewPic','imgtargetLink')" hidden nr="1">
                    <input name="img_path" type="text" hidden nr="1">

                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Click on QR to upload new image
                        </small>
                    </div>
                </div>

                <hr />

                <!-- ACCOUNT NUMBER -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-muted mb-n1 d-flex align-items-center justify-content-center">
                        <span class="bg-light rounded-circle p-2 me-2">
                            <i class="fas fa-phone text-primary"></i>
                        </span>
                        <span>Account Number</span>
                    </label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-light border-primary">
                            <i class="fas fa-mobile-alt text-primary"></i>
                        </span>
                        <input type="text" class="form-control form-control-lg text-center fw-bold border-primary" name="accountNumber" placeholder="09XXXXXXXXX" autocomplete="off" value="<?= $this->session->agrishop_login_gcash_account_num ?>">
                    </div>
                </div>

                <!-- ACCOUNT NAME -->
                <div>
                    <label class="form-label fw-bold text-muted mb-n1 d-flex align-items-center justify-content-center">
                        <span class="bg-light rounded-circle p-2 me-2">
                            <i class="fas fa-user text-primary"></i>
                        </span>
                        <span>Account Name</span>
                    </label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-light border-primary">
                            <i class="fas fa-id-card text-primary"></i>
                        </span>
                        <input type="text" class="form-control form-control-lg text-center fw-bold border-primary text-uppercase" name="accountName" placeholder="ACCOUNT NAME" autocomplete="off" value="<?= $this->session->agrishop_login_gcash_account_name ?>">
                    </div>
                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer bg-light py-3 px-4">
                <div class="d-flex w-100 gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-lg flex-fill rounded-pill" data-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg flex-fill rounded-pill fw-bold update_gcash shadow-sm">
                        <i class="fas fa-save me-2"></i>Update GCash
                    </button>
                </div>
            </div>

            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPaymentStatus">
    <!-- <div class="modal fade show" id="modalPaymentStatus" tabindex="-1" aria-labelledby="modalCheckoutLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content rounded shadow">
            <!-- HEADER -->
            <div class="modal-header py-2">
                <h5 class="modal-title mb-0">
                    <i class="fas fa-money-bill-wave mr-1"></i> Payment Status
                </h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-3 py-2">
                <i>Please select the payment status</i>
                <select id="paymentStatus" class="form-control form-control-sm fs-5 text-uppercase status-select text-center" data-type="payment">
                    <option value="UNPAID">UNPAID</option>
                    <option value="VERIFYING">VERIFYING</option>
                    <option value="PAID">PAID</option>
                    <option value="FAILED">FAILED</option>
                </select>
                <button type="button" class="btn btn-success btn-sm w-100 mt-3" onclick="updateStatus('payment',$('#paymentStatus').val())"><i class="fas fa-check"></i> Update Status</button>
            </div>

        </div>
    </div>
</div>