<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<?php $role_lvl = $this->session->agrishop_login_level; ?>

<head>
    <title><?= $system_title ?> | <?= $page_title ?></title>
    <link rel="icon" type="image/png" href="<?= $system_svg ?>">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="author" content="">
    <meta name="keywords" content="">
    <meta name="description" content="">


    <link rel="stylesheet" href="<?= base_url() ?>plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>dist/layout_shop/css/vendor.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>dist/layout_shop/css/style.css">
    <link rel="stylesheet" href="<?= base_url() ?>plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">

    <!-- DataTables -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&family=Open+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <script src="<?= base_url() ?>plugins/jquery/jquery.min.js"></script>


    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- Routing Machine -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        /* The map takes full remaining height */
        #map {
            width: 100%;
            height: calc(100vh - var(--nav-height));

            display: none;
            /* Hidden by default */
            transition: all 0.4s ease-in-out;
        }


        #landing_Page {
            transition: all 0.5s ease-in-out;
            /* transition: 0.6s ease; */
        }

        #landing_Page.hidden {
            opacity: 0;
            transform: translateY(-80px);
            pointer-events: none;
        }

        /* Put your search box on top of the map */
        .search-box {
            position: absolute;
            top: calc(var(--nav-height) + 10px);
            left: 20px;
            z-index: 9999;
        }
    </style>

</head>

<body>

    <div class="preloader-wrapper">
        <div class="text-center mt-5">
            <i class="fas fa-circle-notch fa-spin fa-4x" style="color: #4bcf1fff;"></i>
        </div>
        <div class="text-center mt-5">
            <img src="<?php echo base_url(); ?>dist/layout_shop/images/logo.svg" width="240" height="70" alt="logo">
        </div>
    </div>

    <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasCart">
        <div class="offcanvas-header justify-content-center">
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="order-md-last">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-primary">Your cart</span>
                    <span class="badge bg-primary rounded-pill">3</span>
                </h4>
                <ul class="list-group mb-3">
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                        <div>
                            <h6 class="my-0">Growers cider</h6>
                            <small class="text-body-secondary">Brief description</small>
                        </div>
                        <span class="text-body-secondary">$12</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                        <div>
                            <h6 class="my-0">Fresh grapes</h6>
                            <small class="text-body-secondary">Brief description</small>
                        </div>
                        <span class="text-body-secondary">$8</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between lh-sm">
                        <div>
                            <h6 class="my-0">Heinz tomato ketchup</h6>
                            <small class="text-body-secondary">Brief description</small>
                        </div>
                        <span class="text-body-secondary">$5</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>Total (USD)</span>
                        <strong>$20</strong>
                    </li>
                </ul>

                <button class="w-100 btn btn-primary btn-lg" type="submit">Continue to checkout</button>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar">

        <div class="offcanvas-header justify-content-between">
            <h4 class="fw-normal text-uppercase fs-6">Menu</h4>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <?php $this->load->view('interface/system/layout/Navbar') ?>

    </div>

    <header id="topNav">

        <div class="container-lg">
            <div class="row py-3 border-bottom">

                <div class="col-5 text-center text-sm-start d-flex gap-3">
                    <a href="index.html">
                        <img src="<?php echo base_url(); ?>dist/layout_shop/images/logo.svg" alt="logo" class="img-fluid">
                    </a>
                </div>

                <div class="col-7">
                    <ul class="d-flex justify-content-end list-unstyled m-0">
                        <li>
                            <?php if ($role_lvl != "") { ?>
                                <a href="<?php if ($role_lvl == 0) {
                                                echo base_url(); ?>user_admin<?php } elseif ($role_lvl == 1) {
                                                                                echo base_url(); ?>user_consumer<?php } elseif ($role_lvl == 2) {
                                                                                                                echo base_url(); ?>user_farmer<?php } ?>" class="p-2 mx-1" style="text-decoration: none;font-weight: bold">
                                    <i class="fa fa-user"></i> <?php echo $this->session->agrishop_login_uname; ?>
                                </a>
                            <?php } else { ?>
                                <a href="<?php echo base_url(); ?>login" class="p-2 mx-1" style="text-decoration: none;">
                                    <i class="fa fa-user"></i> Login
                                </a>
                            <?php } ?>
                        </li>
                        <?php if ($role_lvl == "") { ?>
                            <li><a href="<?php echo base_url(); ?>signup" class="p-2 mx-1" style="text-decoration: none;">
                                    <i class="fa fa-user"></i> Sign Up
                                </a>
                            </li>
                        <?php } ?>
                        <?php if ($role_lvl != "") { ?>
                            <li>
                                <a href="#" class="p-2 mx-1" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                                    <i class="fa fa-bars"></i>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>

                <div class="col-12 mt-3">
                    <div class="search-bar row bg-light p-2 rounded-4">

                        <div class="col-10">
                            <form id="search-form" class="text-center" action="index.html" method="post">
                                <input type="text" class="form-control border-0 bg-transparent" id="searchProduce" placeholder="Search Produce such as TOMATO, SQUASH, CUCUMBER ..." autocomplete="off">
                            </form>
                        </div>
                        <div class="col-1" style="text-align: right;">
                            <i class="fa fa-search"></i>
                        </div>
                        <div class="col-1" style="text-align: left;">
                            <badge type="button" id="home_click" class="badge bg-success" onclick="
                                $('#map').hide(1000);
                                $('#landing_Page').show(400);$('#home_click').hide();" style="display:none;"><i class="fa fa-home"></i> HOME</badge>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </header>

    <!-- CART SIDEBAR -->
    <div id="cartSidebar" class="cart-sidebar">

        <div class="cart-header bg-success text-white p-2 d-flex justify-content-between">
            <span class="font-weight-bold">My Cart</span>
            <button class="btn btn-sm text-white" onclick="closeCart()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="cart-body p-2" id="cartItems">
            <!-- dynamic items -->
        </div>

        <div class="cart-footer p-2 border-top bg-light">
            <div class="d-flex justify-content-between mb-2">
                <span class="font-weight-bold">Total:</span>
                <span class="font-weight-bold" id="cartTotal">₱0.00</span>
            </div>

            <button class="btn btn-success btn-block btn-sm" onclick="openCheckout()">
                <i class="fas fa-check-circle"></i> Proceed to Checkout
            </button>
        </div>
    </div>

    <style>
        .cart-sidebar {
            position: fixed;
            top: 0;
            right: -350px;
            width: 350px;
            height: 100vh;
            background: #fff;
            border-left: 2px solid #28a745;
            box-shadow: -2px 0 6px rgba(0, 0, 0, 0.2);
            transition: 0.3s;
            z-index: 99999;
            display: flex;
            flex-direction: column;
        }

        .cart-sidebar.open {
            right: 0;
        }

        .cart-body {
            flex: 1;
            overflow-y: auto;
        }
    </style>

    <!-- <div class="buttons" id="cityButtons"></div> -->
    <div class="container-fluid p-0">
        <?php $this->load->view('interface/system/layout/landing_page') ?>
        <div id="map"></div>
    </div>
    <!-- <div id="distanceInfo"></div> -->



    <script src="<?php echo base_url(); ?>dist/layout_shop/js/jquery-1.11.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="<?php echo base_url(); ?>dist/layout_shop/js/plugins.js"></script>
    <script src="<?php echo base_url(); ?>dist/layout_shop/js/script.js"></script>
    <script src="<?= base_url() ?>plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>
    <!-- DataTables -->
    <script src="<?= base_url() ?>plugins/datatables/jquery.dataTables.js"></script>
    <script src="<?= base_url() ?>plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
    <script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/dataTables.buttons.min.js"></script>
    <script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/buttons.print.min.js"></script>
    <script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/jszip.min.js"></script>
    <script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/buttons.flash.min.js"></script>
    <script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/buttons.html5.min.js"></script>
    <script src="<?= base_url() ?>plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/dataTables.buttons.min.js"></script>
    <script src="<?= base_url() ?>plugins/datatables/extensions/responsive/js/dataTables.responsive.min.js"></script>

    <?php $this->load->view('interface/system/layout/script') ?>
    <?php $this->load->view('interface/system/layout/cart_script') ?>


    <?php $this->load->view('interface/system/layout/modals') ?>

</body>


</html>