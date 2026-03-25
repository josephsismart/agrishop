<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- <script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script> -->
<?php
// if (!$this->session->agrishop_login_level) {
//     redirect(base_url('login'));
// }
// $uri = $this->session->agrishop_login_uri;
?>
<!-- Modal -->

<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0 rounded-3 overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header bg-primary bg-gradient text-white py-3 px-4">
                <div class="d-flex align-items-center w-100">
                    <div class="flex-grow-1">
                        <h5 class="modal-title fw-bold mb-0">
                            <i class="fas fa-user-circle me-2"></i>My Profile
                        </h5>
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

<!-- <div class="modal fade show" id="registerFarmerModal" tabindex="-1" aria-labelledby="registerFarmerModalLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
<div class="modal fade" id="registerFarmerModal" tabindex="-1" aria-labelledby="registerFarmerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:0;">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="registerFarmerModalLabel">Register as Farmer</h1>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- <form action="<?= base_url() ?>requestprofile" method="post"> -->
            <?= form_open(base_url('/registerfarmer'), 'id=form_save_dataRegisterFarmer'); ?>
            <div class="modal-body">
                <div class="card-body">
                    <div class="row">
                        <h6><i class="fas fa-address-card"></i> Identification</h6>
                        <div class="col-12">
                            <select class="form-control form-control-sm" name="sex">
                                <option value="t">PLEASE SELECT ID TO BE PRESENTED</option>
                                <option value="f">SSS</option>
                                <option value="f">TIN</option>
                                <option value="f">PAG-IBIG</option>
                                <option value="f">PHILHEALTH</option>
                            </select>
                            <div class="col-12">
                                <center class="mt-3">
                                    <div class="form-group">
                                        <img name="previewPic" src="<?= base_url() ?>dist/img/media/icons/1x1.png" onclick="$('[name=pic]').trigger('click')" width="120" height="120" class="border border-white border-2 rounded elevation-2" type="button" alt="User Image">
                                    </div>
                                    <div class="form-group">
                                        <input name="pic" type="file" accept="image/*" onchange="imageView('pic','previewPic','imgtargetLink')" nr="1" hidden="">
                                        <input name="img_path" type="text" nr="1" hidden="">
                                    </div>
                                    <label class="form-label text-xs"><i>ID PICTURE</i></label>
                                </center>
                            </div>
                        </div>
                        <div class="col-12">
                            <h6 class="mt-3"><i class="fas fa-sitemap"></i> Organization Membership</h6>
                            <div class="col-12">
                                <select class="form-control form-control-sm" name="sex">
                                    <option>PLEASE SELECT ORGANIZATION MEMBERSHIP</option>
                                    <option value="t">Federation of Free Farmers (FFF)</option>
                                    <option value="f">Rural Missionaries of the Philippines</option>
                                    <option value="f">AgriCOOPh (Philippine Family Farmers’ Agriculture Fishery Forestry Cooperatives Federation)</option>
                                    <option value="f">Alyansa Agrikultura (AA)</option>
                                    <option value="f">Aniban ng Manggagawa sa Agrikultura (AMA)</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Submit Request</button>
            </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="homeModal" tabindex="-1" aria-labelledby="homeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="border-radius:0;">
            <div class="modal-header">
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <section style="background-image: url('<?php echo base_url(); ?>dist/layout_shop/images/banner-1.jpg');background-repeat: no-repeat;background-size: cover;">
                    <div class="container-lg">
                        <div class="row">
                            <div class="col-lg-6 pt-5 mt-5">
                                <h2 class="display-1 ls-1"><span class="fw-bold text-primary">Farm-Fresh</span> Goodness Delivered <span class="fw-bold">Today</span></h2>
                                <p class="fs-4">Order now to lock in peak freshness before it’s gone.</p>
                                <div class="d-flex gap-3">
                                    <a href="#" class="btn bg-danger text-uppercase fs-6 rounded-pill px-4 py-3 mt-3 text-white">Start Shopping</a>
                                    <?php if (!$this->session->agrishop_login_id) { ?>
                                        <a href="<?= base_url() ?>signup" class="btn btn-dark text-uppercase fs-6 rounded-pill px-4 py-3 mt-3">Sign Up Now</a>
                                    <?php } ?>
                                </div>
                                <div class="row my-5">
                                    <div class="col">
                                        <div class="row text-dark">
                                            <div class="col-auto">
                                                <p class="fs-1 fw-bold lh-sm mb-0">14k+</p>
                                            </div>
                                            <div class="col">
                                                <p class="text-uppercase lh-sm mb-0">Product Varieties</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="row text-dark">
                                            <div class="col-auto">
                                                <p class="fs-1 fw-bold lh-sm mb-0">50k+</p>
                                            </div>
                                            <div class="col">
                                                <p class="text-uppercase lh-sm mb-0">Happy Customers</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="row text-dark">
                                            <div class="col-auto">
                                                <p class="fs-1 fw-bold lh-sm mb-0">10+</p>
                                            </div>
                                            <div class="col">
                                                <p class="text-uppercase lh-sm mb-0">Store Locations</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row row-cols-1 row-cols-sm-3 row-cols-lg-3 g-0 justify-content-center">
                            <div class="col">
                                <div class="card border-0 bg-primary rounded-0 p-4 text-light">
                                    <div class="row">
                                        <div class="col-md-3 text-center">
                                            <svg width="60" height="60">
                                                <use xlink:href="#fresh"></use>
                                            </svg>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="card-body p-0">
                                                <h5 class="text-light">Fresh from farm</h5>
                                                <!-- <p class="card-text">Our produce travels hours, not weeks, guaranteeing you the absolute peak of flavor, texture, and nutritional value.</p> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card border-0 bg-secondary rounded-0 p-4 text-light">
                                    <div class="row">
                                        <div class="col-md-3 text-center">
                                            <svg width="60" height="60">
                                                <use xlink:href="#organic"></use>
                                            </svg>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="card-body p-0">
                                                <h5 class="text-light">100% Organic</h5>
                                                <!-- <p class="card-text">Zero synthetic pesticides. Rest easy knowing you are feeding your family the purest, most transparently grown ingredients possible.</p> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card border-0 bg-danger rounded-0 p-4 text-light">
                                    <div class="row">
                                        <div class="col-md-3 text-center">
                                            <svg width="60" height="60">
                                                <use xlink:href="#delivery"></use>
                                            </svg>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="card-body p-0">
                                                <h5 class="text-light">Free delivery</h5>
                                                <!-- <p class="card-text">Enjoy the convenience of farm-fresh quality delivered right to your kitchen, with no delivery fees on all qualifying orders.</p> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>

                <section class="py-5 overflow-hidden">

                    <div class="container-lg">

                        <div class="row">
                            <div class="col-md-12">

                                <div class="section-header d-flex flex-wrap justify-content-between mb-5">
                                    <h2 class="section-title">Farmer Performance Leaderboard</h2>

                                    <div class="d-flex align-items-center">
                                        <a href="#" class="btn btn-primary me-2">View All</a>
                                        <div class="swiper-buttons">
                                            <button class="swiper-prev category-carousel-prev btn btn-yellow">❮</button>
                                            <button class="swiper-next category-carousel-next btn btn-yellow">❯</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">

                                <div class="category-carousel swiper">
                                    <div class="swiper-wrapper">
                                        <a href="category.html" class="nav-link swiper-slide text-center">
                                            <img src="<?php echo base_url(); ?>dist/layout_shop/images/category-thumb-1.jpg" class="rounded-circle" alt="Category Thumbnail">
                                            <h4 class="fs-6 mt-3 fw-normal category-title">Fruits & Veges</h4>
                                        </a>
                                        <a href="category.html" class="nav-link swiper-slide text-center">
                                            <img src="<?php echo base_url(); ?>dist/layout_shop/images/category-thumb-2.jpg" class="rounded-circle" alt="Category Thumbnail">
                                            <h4 class="fs-6 mt-3 fw-normal category-title">Breads & Sweets</h4>
                                        </a>
                                        <a href="category.html" class="nav-link swiper-slide text-center">
                                            <img src="<?php echo base_url(); ?>dist/layout_shop/images/category-thumb-3.jpg" class="rounded-circle" alt="Category Thumbnail">
                                            <h4 class="fs-6 mt-3 fw-normal category-title">Fruits & Veges</h4>
                                        </a>
                                        <a href="category.html" class="nav-link swiper-slide text-center">
                                            <img src="<?php echo base_url(); ?>dist/layout_shop/images/category-thumb-4.jpg" class="rounded-circle" alt="Category Thumbnail">
                                            <h4 class="fs-6 mt-3 fw-normal category-title">Beverages</h4>
                                        </a>
                                        <a href="category.html" class="nav-link swiper-slide text-center">
                                            <img src="<?php echo base_url(); ?>dist/layout_shop/images/category-thumb-5.jpg" class="rounded-circle" alt="Category Thumbnail">
                                            <h4 class="fs-6 mt-3 fw-normal category-title">Meat Products</h4>
                                        </a>
                                        <a href="category.html" class="nav-link swiper-slide text-center">
                                            <img src="<?php echo base_url(); ?>dist/layout_shop/images/category-thumb-6.jpg" class="rounded-circle" alt="Category Thumbnail">
                                            <h4 class="fs-6 mt-3 fw-normal category-title">Breads</h4>
                                        </a>
                                        <a href="category.html" class="nav-link swiper-slide text-center">
                                            <img src="<?php echo base_url(); ?>dist/layout_shop/images/category-thumb-7.jpg" class="rounded-circle" alt="Category Thumbnail">
                                            <h4 class="fs-6 mt-3 fw-normal category-title">Fruits & Veges</h4>
                                        </a>
                                        <a href="category.html" class="nav-link swiper-slide text-center">
                                            <img src="<?php echo base_url(); ?>dist/layout_shop/images/category-thumb-8.jpg" class="rounded-circle" alt="Category Thumbnail">
                                            <h4 class="fs-6 mt-3 fw-normal category-title">Breads & Sweets</h4>
                                        </a>
                                        <a href="category.html" class="nav-link swiper-slide text-center">
                                            <img src="<?php echo base_url(); ?>dist/layout_shop/images/category-thumb-1.jpg" class="rounded-circle" alt="Category Thumbnail">
                                            <h4 class="fs-6 mt-3 fw-normal category-title">Fruits & Veges</h4>
                                        </a>
                                        <a href="category.html" class="nav-link swiper-slide text-center">
                                            <img src="<?php echo base_url(); ?>dist/layout_shop/images/category-thumb-1.jpg" class="rounded-circle" alt="Category Thumbnail">
                                            <h4 class="fs-6 mt-3 fw-normal category-title">Beverages</h4>
                                        </a>
                                        <a href="category.html" class="nav-link swiper-slide text-center">
                                            <img src="<?php echo base_url(); ?>dist/layout_shop/images/category-thumb-1.jpg" class="rounded-circle" alt="Category Thumbnail">
                                            <h4 class="fs-6 mt-3 fw-normal category-title">Meat Products</h4>
                                        </a>
                                        <a href="category.html" class="nav-link swiper-slide text-center">
                                            <img src="<?php echo base_url(); ?>dist/layout_shop/images/category-thumb-1.jpg" class="rounded-circle" alt="Category Thumbnail">
                                            <h4 class="fs-6 mt-3 fw-normal category-title">Breads</h4>
                                        </a>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </section>

                <footer class="py-5">
                    <div class="container-lg">
                        <div class="row">

                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="footer-menu">
                                    <img src="<?php echo base_url(); ?>dist/layout_shop/images/logo.svg" width="240" height="70" alt="logo">
                                    <div class="social-links mt-3">
                                        <ul class="d-flex list-unstyled gap-2">
                                            <li>
                                                <a href="#" class="btn btn-outline-light">
                                                    <svg width="16" height="16">
                                                        <use xlink:href="#facebook"></use>
                                                    </svg>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="btn btn-outline-light">
                                                    <svg width="16" height="16">
                                                        <use xlink:href="#twitter"></use>
                                                    </svg>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="btn btn-outline-light">
                                                    <svg width="16" height="16">
                                                        <use xlink:href="#youtube"></use>
                                                    </svg>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="btn btn-outline-light">
                                                    <svg width="16" height="16">
                                                        <use xlink:href="#instagram"></use>
                                                    </svg>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="btn btn-outline-light">
                                                    <svg width="16" height="16">
                                                        <use xlink:href="#amazon"></use>
                                                    </svg>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-6">
                                <div class="footer-menu">
                                    <h5 class="widget-title">Organic</h5>
                                    <ul class="menu-list list-unstyled">
                                        <li class="menu-item">
                                            <a href="#" class="nav-link">About us</a>
                                        </li>
                                        <li class="menu-item">
                                            <a href="#" class="nav-link">Conditions </a>
                                        </li>
                                        <li class="menu-item">
                                            <a href="#" class="nav-link">Our Journals</a>
                                        </li>
                                        <li class="menu-item">
                                            <a href="#" class="nav-link">Careers</a>
                                        </li>
                                        <li class="menu-item">
                                            <a href="#" class="nav-link">Affiliate Programme</a>
                                        </li>
                                        <li class="menu-item">
                                            <a href="#" class="nav-link">Ultras Press</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="footer-menu">
                                    <h5 class="widget-title">Quick Links</h5>
                                    <ul class="menu-list list-unstyled">
                                        <li class="menu-item">
                                            <a href="#" class="nav-link">Offers</a>
                                        </li>
                                        <li class="menu-item">
                                            <a href="#" class="nav-link">Discount Coupons</a>
                                        </li>
                                        <li class="menu-item">
                                            <a href="#" class="nav-link">Stores</a>
                                        </li>
                                        <li class="menu-item">
                                            <a href="#" class="nav-link">Track Order</a>
                                        </li>
                                        <li class="menu-item">
                                            <a href="#" class="nav-link">Shop</a>
                                        </li>
                                        <li class="menu-item">
                                            <a href="#" class="nav-link">Info</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div class="footer-menu">
                                    <h5 class="widget-title">Customer Service</h5>
                                    <ul class="menu-list list-unstyled">
                                        <li class="menu-item">
                                            <a href="#" class="nav-link">FAQ</a>
                                        </li>
                                        <li class="menu-item">
                                            <a href="#" class="nav-link">Contact</a>
                                        </li>
                                        <li class="menu-item">
                                            <a href="#" class="nav-link">Privacy Policy</a>
                                        </li>
                                        <li class="menu-item">
                                            <a href="#" class="nav-link">Returns & Refunds</a>
                                        </li>
                                        <li class="menu-item">
                                            <a href="#" class="nav-link">Cookie Guidelines</a>
                                        </li>
                                        <li class="menu-item">
                                            <a href="#" class="nav-link">Delivery Information</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6">
                                <div class="footer-menu">
                                    <h5 class="widget-title">Subscribe Us</h5>
                                    <p>Subscribe to our newsletter to get updates about our grand offers.</p>
                                    <form class="d-flex mt-3 gap-0" action="index.html">
                                        <input class="form-control rounded-start rounded-0 bg-light" type="email" placeholder="Email Address" aria-label="Email Address">
                                        <button class="btn btn-dark rounded-end rounded-0" type="submit">Subscribe</button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </footer>
                <div id="footer-bottom">
                    <div class="container-lg">
                        <div class="row">
                            <div class="col-md-6 copyright">
                                <p>© 2024 Organic. All rights reserved.</p>
                            </div>
                            <div class="col-md-6 credit-link text-start text-md-end">
                                <p>HTML Template by <a href="https://templatesjungle.com/">TemplatesJungle</a> Distributed By <a href="https://themewagon.com">ThemeWagon</a> </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Submit Request</button>
            </div> -->
        </div>
    </div>
</div>


<!-- <div class="modal fade show" id="modalOrderProduce" tabindex="-1" aria-labelledby="modalOrderProduceLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
<div class="modal fade" id="modalOrderProduce" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow rounded">

            <!-- HEADER -->
            <div class="modal-header bg-success py-2">
                <h5 class="modal-title mb-0 text-white">
                    <i class="fas fa-shopping-basket mr-1"></i> Order Produce
                </h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-3 py-2">


                <!-- FARM OWNER -->
                <div class="d-flex mb-3 border rounded p-2 bg-light">
                    <div name="farmerImage" class="rounded mr-3 border" style="width: 80px; height: 80px;"></div>

                    <div>
                        <h6 name="farmerName" class="font-weight-bold mb-1">Farmer Name</h6>
                        <small class="text-muted d-block" name="farmerExperience">
                            Farmer for X years • Reliable Supplier
                        </small>
                        <small class="text-muted d-block" name="farmerContact">
                            Contact: 09xxxxxxx
                        </small>
                    </div>
                </div>

                <!-- FARM INFO -->
                <div class="d-flex mb-3 border rounded p-2 bg-light">
                    <div name="farmImage"></div>
                    <!-- <img name="farmImage" src="<?= base_url('dist/img/media/icons/1x1.png') ?>" class="rounded mr-3 border" width="80" height="80"> -->

                    <div class="flex-grow-1">
                        <h6 name="farmName" class="font-weight-bold mb-1">Farm Name</h6>
                        <small class="text-muted d-block" name="farmLocation">
                            Location address here
                        </small>

                        <button class="btn btn-outline-success btn-sm mt-1">
                            <i class="fas fa-map-marker-alt"></i> View on Map
                        </button>
                    </div>
                </div>
                <!-- PRODUCE TABLE -->
                <div class="table-responsive border rounded">
                    <table id="tblFarmProduceList" class="table table-sm table-hover mb-0" width="100%">
                        <thead class="bg-success text-white small">
                            <tr>
                                <th width="1"> </th>
                                <th width="40">qty</th>
                                <th>Image</th>
                                <th>Produce</th>
                                <th width="1">Harvest</th>
                                <th width="1">Available</th>
                                <th width="1">Price/UOM</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- dynamic rows -->
                        </tbody>
                    </table>
                </div>

            </div>

            <div class="modal-footer py-2">
                <button class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>


<div class="modal fade" id="modalCartListing">
    <!-- <div class="modal fade show" id="modalCheckout" tabindex="-1" aria-labelledby="modalCheckoutLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content rounded shadow">
            <!-- HEADER -->
            <div class="modal-header bg-warning py-2">
                <h5 class="modal-title mb-0 text-white">
                    <i class="fas fa-shopping-basket mr-1"></i> Cart List
                </h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-3 py-2">
                <table id="tblCartListing" class="table table-sm table-hover table-striped table-bordered mb-0" width="100%">
                    <thead class="small">
                        <tr>
                            <!-- <th width="1">Image</th> -->
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- dynamic rows -->
                    </tbody>
                </table>
            </div>


            <div class="modal-footer py-2">
                <button class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalOrderListing">
    <!-- <div class="modal fade show" id="modalCheckout" tabindex="-1" aria-labelledby="modalCheckoutLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content rounded shadow">
            <!-- HEADER -->
            <div class="modal-header bg-info py-2">
                <h5 class="modal-title mb-0 text-white">
                    <i class="fas fa-table mr-1"></i> Order List
                </h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-3 py-2">
                <table id="tblOrderListing" class="table table-sm table-hover table-striped table-bordered mb-0" width="100%">
                    <thead class="small">
                        <tr>
                            <!-- <th width="1">Image</th> -->
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- dynamic rows -->
                    </tbody>
                </table>
            </div>


            <div class="modal-footer py-2">
                <button class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCompletedOrderListing">
    <!-- <div class="modal fade show" id="modalCheckout" tabindex="-1" aria-labelledby="modalCheckoutLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content rounded shadow">
            <!-- HEADER -->
            <div class="modal-header bg-info py-2">
                <h5 class="modal-title mb-0 text-white">
                    <i class="fas fa-table mr-1"></i> Order List
                </h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-3 py-2">
                <table id="tblCompletedOrderListing" class="table table-sm table-hover table-striped table-bordered mb-0" width="100%">
                    <thead class="small">
                        <tr>
                            <!-- <th width="1">Image</th> -->
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- dynamic rows -->
                    </tbody>
                </table>
            </div>


            <div class="modal-footer py-2">
                <button class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCancelledOrderListing">
    <!-- <div class="modal fade show" id="modalCheckout" tabindex="-1" aria-labelledby="modalCheckoutLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content rounded shadow">
            <!-- HEADER -->
            <div class="modal-header bg-info py-2">
                <h5 class="modal-title mb-0 text-white">
                    <i class="fas fa-table mr-1"></i> Order List
                </h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-3 py-2">
                <table id="tblCancelledOrderListing" class="table table-sm table-hover table-striped table-bordered mb-0" width="100%">
                    <thead class="small">
                        <tr>
                            <!-- <th width="1">Image</th> -->
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- dynamic rows -->
                    </tbody>
                </table>
            </div>


            <div class="modal-footer py-2">
                <button class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRateOrderListing">
    <!-- <div class="modal fade show" id="modalCheckout" tabindex="-1" aria-labelledby="modalCheckoutLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content rounded shadow">
            <!-- HEADER -->
            <div class="modal-header bg-warning py-2">
                <h5 class="modal-title mb-0 text-white">
                    <i class="fas fa-star mr-1"></i> Rate Completed Orders
                </h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-3 py-2">
                <table id="tblRateOrderListing" class="table table-sm table-hover table-striped table-bordered mb-0" width="100%">
                    <thead class="small">
                        <tr>
                            <!-- <th width="1">Image</th> -->
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- dynamic rows -->
                    </tbody>
                </table>
            </div>


            <div class="modal-footer py-2">
                <button class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalCartDetails">
    <!-- <div class="modal fade show" id="modalCheckout" tabindex="-1" aria-labelledby="modalCheckoutLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content rounded shadow">
            <!-- HEADER -->
            <div class="modal-header bg-dark py-2">
                <h5 class="modal-title mb-0 text-white">
                    <i class="fas fa-shopping-basket mr-1"></i> Cart Details and Checkout
                </h5>
                <button type="button" class="btn-close" style="filter: invert(1);" data-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-3 py-2">
                <table id="tblCartDetails" class="table table-sm table-bordered mb-0" width="100%">
                    <thead class="small">
                        <tr>
                            <!-- <th width="1">Image</th> -->
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- dynamic rows -->
                    </tbody>
                </table>
            </div>


            <div class="modal-footer py-2">
                <button class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalCheckout">
    <!-- <div class="modal fade" id="modalCheckout" tabindex="-1" role="dialog"> -->
    <!-- <div class="modal fade show" id="modalCheckout" tabindex="-1" aria-labelledby="modalCheckoutLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content rounded shadow">

            <div class="modal-header bg-success text-white py-2">
                <h5 class="modal-title mb-0">Checkout</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body p-3">
                <h6 class="font-weight-bold mb-2">Order Summary</h6>
                <div id="checkoutSummary"></div>

                <hr>

                <h6 class="font-weight-bold">Delivery Method</h6>
                <select class="form-control form-control-sm" id="deliveryMethod">
                    <option value="pickup">Pickup</option>
                    <option value="delivery">Delivery</option>
                </select>

                <div class="mt-3">
                    <h6 class="font-weight-bold">Remarks</h6>
                    <textarea id="remarks" class="form-control form-control-sm" rows="2"></textarea>
                </div>

            </div>

            <div class="modal-footer p-2">
                <button class="btn btn-success btn-sm btn-block" onclick="submitCheckout()">
                    <i class="fas fa-check"></i> Confirm Order
                </button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalCheckoutGcash">
    <!-- <div class="modal fade" id="modalCheckout" tabindex="-1" role="dialog"> -->
    <!-- <div class="modal fade show" id="modalCheckout" tabindex="-1" aria-labelledby="modalCheckoutLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content rounded shadow">

            <div class="modal-header bg-success text-white py-2">
                <h5 class="modal-title mb-0">Checkout</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body p-3">
                <h6 class="font-weight-bold mb-2">Order Summary</h6>
                <div id="checkoutSummary"></div>

                <hr>

                <h6 class="font-weight-bold">Delivery Method</h6>
                <select class="form-control form-control-sm" id="deliveryMethod">
                    <option value="pickup">Pickup</option>
                    <option value="delivery">Delivery</option>
                </select>

                <div class="mt-3">
                    <h6 class="font-weight-bold">Remarks</h6>
                    <textarea id="remarks" class="form-control form-control-sm" rows="2"></textarea>
                </div>

            </div>

            <div class="modal-footer p-2">
                <button class="btn btn-success btn-sm btn-block" onclick="submitCheckout()">
                    <i class="fas fa-check"></i> Confirm Order
                </button>
            </div>

        </div>
    </div>
</div>


<!-- <div class="modal fade show" id="modalProcessingFeeModal" tabindex="-1" aria-labelledby="modalCheckoutLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
<div class="modal fade" id="modalProcessingFeeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <!-- HEADER -->
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Processing Fee</h5>
                <button type="button" class="btn-close" data-dismiss="modal"></button>
            </div>

            <!-- BODY -->

            <?= form_open(base_url('/payprocessingfee'), 'id="form_save_dataPayProcessingFee"'); ?>
            <div class="modal-body pt-2">

                <!-- Fee Summary Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center">

                        <small class="text-muted">Processing Fee (1%)</small>
                        <h2 class="fw-bold text-success mb-1" id="processingFeeAmount"></h2>
                        <input type="hidden" name="trans_id" id="trans_id">
                        <input type="hidden" name="convenience_fee" id="convenience_fee">

                        <small class="text-muted">
                            Required to process and verify your order
                        </small>

                    </div>
                </div>

                <!-- Step 1 -->
                <div class="mb-3">
                    <h6 class="fw-bold mb-2">Step 1: Pay Processing Fee</h6>
                    <div class="card border-0 bg-light p-3">

                        <div class="row align-items-center">

                            <!-- LEFT: QR -->
                            <div class="col-6 text-center">
                                <img src="<?= base_url('dist/images/gcash_qr.jpg') ?>" width="120" class="img-fluid mb-2">
                                <small class="text-muted d-block">Scan QR code to pay</small>
                            </div>

                            <!-- RIGHT: Account Info -->
                            <div class="col-6">
                                <div class="row text-start">
                                    <div class="col-12 text-muted">Account Name</div>
                                    <div class="col-12 fw-semibold mb-2">AgriShop Admin</div>

                                    <div class="col-12 text-muted mt-2">Account Number</div>
                                    <div class="col-12 fw-semibold">09123456789</div>
                                </div>

                            </div>

                        </div>

                    </div>
                </div>

                <!-- Step 2 -->
                <div class="mb-n2">
                    <h6 class="fw-bold mb-2">Step 2: Upload Proof</h6>

                    <div class="card border-0 bg-light p-3">
                        <input type="file" class="form-control" id="paymentProof" name="paymentProof" accept="image/*">
                        <small class="text-muted mt-1">
                            Upload payment screenshot or receipt
                        </small>
                    </div>
                </div>

                <!-- Agreement -->
                <div class="form-check mt-1 mb-n3 p-0" style="display: flex; align-items: center; gap: 6px;">
                    <input type="checkbox" id="agreeFee">
                    <label for="agreeFee" class="form-check-label small" style="cursor: pointer; margin: 0;">
                        I agree to the 1% processing fee.
                    </label>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer border-0">
                <button class="btn btn-light" data-dismiss="modal">
                    Cancel
                </button>

                <button class="btn btn-success px-4" type="submit" id="btnProceedOrder" disabled>
                    Proceed Order
                </button>
            </div>
            </form>

        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     PROMO CART MODAL
══════════════════════════════════════════════════════════ -->
<div class="modal fade" id="modalPromoCart" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content rounded shadow border-0">
            <div class="modal-header py-2" style="background:linear-gradient(135deg,#f59e0b,#ef4444);">
                <h5 class="modal-title text-white mb-0">
                    <i class="fa fa-tag mr-1"></i> Promo Deal — Add to Cart
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-3">
                <!-- Promo image -->
                <div class="text-center mb-3">
                    <img id="promoCartImg" src="" width="100%" height="180"
                         style="object-fit:cover;border-radius:10px;"
                         onerror="this.src='<?= base_url('dist/img/media/icons/1x1.png') ?>'">
                </div>
                <!-- Badge + Title -->
                <div class="d-flex align-items-center mb-1">
                    <span id="promoCartBadge" class="badge badge-danger mr-2" style="font-size:13px;"></span>
                    <span id="promoCartProduce" class="badge badge-success" style="font-size:11px;"></span>
                </div>
                <h6 id="promoCartTitle" class="font-weight-bold mb-1" style="font-size:15px;"></h6>
                <p id="promoCartDesc" class="text-muted mb-2" style="font-size:12px;"></p>

                <!-- Prices -->
                <div class="d-flex align-items-baseline mb-1" style="gap:10px;">
                    <span id="promoCartOrigPrice" style="text-decoration:line-through;color:#9ca3af;font-size:13px;"></span>
                    <span id="promoCartDiscPrice" style="font-size:22px;font-weight:900;color:#059669;"></span>
                </div>
                <div id="promoCartUntil" class="text-warning" style="font-size:11px;font-weight:700;margin-bottom:14px;"></div>

                <hr class="my-2">

                <!-- Qty -->
                <div class="d-flex align-items-center" style="gap:12px;">
                    <label class="font-weight-bold mb-0" style="font-size:13px;">Quantity:</label>
                    <input type="number" id="promoCartQty" value="1" min="1"
                           class="form-control form-control-sm" style="width:90px;">
                    <span class="text-muted" style="font-size:12px;">units</span>
                </div>
            </div>
            <div class="modal-footer p-2">
                <button class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                <button class="btn btn-success btn-sm px-4 font-weight-bold" id="btnAddPromoToCart">
                    🛒 Add to Cart
                </button>
            </div>
        </div>
    </div>
</div>
