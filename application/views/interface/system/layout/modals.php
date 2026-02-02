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
    <div class="modal-dialog modal-xs">
        <div class="modal-content" style="border-radius:0;">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="profileModalLabel">My Profile</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?= form_open(base_url('/updateprofile'), 'id=form_save_dataUpdateProfile'); ?>
            <div class="modal-body">
                <div class="card-body">
                    <h6><i class="fas fa-camera-retro"></i> Photo</h6>
                    <div class="col-12">
                        <center class="p-0 border">
                            <div class="form-group">
                                <img name="previewPic" src="<?= $this->session->agrishop_login_img_path  ?>" onclick="$('[name=picProfile]').trigger('click')" width="120" height="120" class="border border-white border-2 rounded elevation-2" type="button" alt="User Image">
                            </div>
                            <div class="form-group">
                                <input name="picProfile" type="file" accept="image/*" onchange="imageView('picProfile','previewPic','imgtargetLink')" nr="1" hidden="">
                                <!-- <input name="personId" type="text" nr="1" > -->
                                <input name="img_path" type="text" nr="1" hidden="">
                            </div>
                        </center>
                    </div>
                    <div class="row">
                        <h6><i class="fas fa-user"></i> Name</h6>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-6">
                            <input type="text" class="form-control form-control-sm text-uppercase border-primary" value="<?= $this->session->agrishop_login_first_name ?>" name="firstName" placeholder="FIRST NAME" autocomplete="off">
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-6 mb-2">
                            <input type="text" class="form-control form-control-sm text-uppercase border-primary" value="<?= $this->session->agrishop_login_middle_name ?>" name="middleName" placeholder="MIDDLE NAME" autocomplete="off" nr="1">
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-6 mb-2">
                            <input type="text" class="form-control form-control-sm text-uppercase border-primary" value="<?= $this->session->agrishop_login_last_name ?>" name="lastName" placeholder="LAST NAME" autocomplete="off">
                        </div>
                        <div class="col-6">
                            <h6 class="mt-3"><i class="fas fa-birthday-cake"></i> Birthdate</h6>
                            <div class="col-12">
                                <input type="date" class="form-control form-control-sm border-primary" name="birthdate" nr="1" value="<?= $this->session->agrishop_login_birthdate ?>">
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="mt-3"><i class="fas fa-venus-mars"></i> Gender</h6>
                            <div class="col-12">
                                <select class="form-control form-control-sm border-primary" name="sex">
                                    <option value="t" <?= $this->session->agrishop_login_sex == 't' ? 'selected' : '' ?>>MALE</option>
                                    <option value="f" <?= $this->session->agrishop_login_sex == 'f' ? 'selected' : '' ?>>FEMALE</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="mt-3"><i class="fas fa-envelope"></i> Email</h6>
                            <div class="col-12">
                                <input type="email" class="form-control form-control-sm border-primary" placeholder="EMAIL" name="email" value="<?= $this->session->agrishop_login_email_address ?>" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="mt-3"><i class="fas fa-phone"></i> Contact Number</h6>
                            <div class="col-12">
                                <input type="text" class="form-control form-control-sm border-primary text-uppercase" placeholder="Contact Number" name="contactNumber" value="<?= $this->session->agrishop_login_contact_num ?>">
                            </div>
                        </div>

                        <h6 class="mt-3"> <i class="fas fa-map-marker-alt"></i> Address</h6>
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <!-- 160202061 -->
                                <input name="barangayAll" hidden value="<?= $this->session->agrishop_login_barangay_id ?>">
                                <input type="text" class="form-control form-control-sm barangayInput border-primary text-uppercase" value="<?= $this->session->agrishop_login_address_text ?>" placeholder="TYPE BARANGAY (min 3 chars)" autocomplete="off" name="barangay_text">

                            </div>
                            <ul class="list-group barangayResults" style="position:absolute; z-index:9999; width:100%; display:none;cursor:pointer;"></ul>

                        </div>

                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn bg-primary text-white update_profile"> <i class="fas fa-save"></i> Update Profile</button>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>


<div class="modal fade" id="modalCartListing">
    <!-- <div class="modal fade show" id="modalCheckout" tabindex="-1" aria-labelledby="modalCheckoutLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content rounded shadow">
            <!-- HEADER -->
            <div class="modal-header bg-success py-2">
                <h5 class="modal-title mb-0 text-white">
                    <i class="fas fa-shopping-basket mr-1"></i> Cart List
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
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
                <button type="button" class="btn-close" style="filter: invert(1);" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
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