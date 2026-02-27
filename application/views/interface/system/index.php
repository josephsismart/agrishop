<!DOCTYPE html>
<html lang="en">
<?php
$role_lvl = $this->session->agrishop_login_level;
$uri = $this->session->agrishop_login_uri;
$farm_produce = base_url() . $uri . '/FarmProduce'; ?>

<head>
    <title><?= $system_title ?> | <?= $page_title ?></title>
    <link rel="icon" type="image/png" href="<?= $system_svg ?>">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="author" content="">
    <meta name="keywords" content="">
    <meta name="description" content="">


    <link rel="stylesheet" href="<?= base_url() ?>plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>plugins/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>plugins/bootstrap-alpha3/bootstrap.min.css">
    <!-- integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous"> -->
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>dist/layout_shop/css/vendor.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>dist/layout_shop/css/style.css">
    <link rel="stylesheet" href="<?= base_url() ?>dist/css/adminlte.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">

    <!-- DataTables -->
    <!-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> -->
    <link href="<?= base_url() ?>plugins/google-fonts/fonts.css" rel="stylesheet">
    <script src="<?= base_url() ?>plugins/jquery/jquery.min.js"></script>


    <!-- Leaflet CSS & JS -->
    <!-- <link rel="stylesheet" href="<?= base_url() ?>plugins/leaflet/css/leaflet.css" /> -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="<?= base_url() ?>plugins/leaflet/js/leaflet.js"></script>
    <!-- <script src="https://unpkg.com/leaflet@1.2.0/dist/leaflet.js"></script> -->

    <!-- Routing Machine -->
    <!-- <link rel="stylesheet" href="<?= base_url() ?>plugins/leaflet/css/leaflet-routing-machine.css" /> -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />

    <!-- <script src="<?= base_url() ?>plugins/leaflet/js/leaflet-routing-machine.js"></script> -->
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

        .barangayResults {
            position: absolute;
            z-index: 9999;
            width: 100%;
            display: none;
            cursor: pointer;
            max-height: 220px;
            overflow-y: auto;
        }

        /* STRIPED */
        .barangayResults li:nth-child(even) {
            background-color: #efefefff;
        }

        /* HOVER */
        .barangayResults li:hover {
            background-color: #218037ff;
            color: white;
        }

        .menu-list .nav-item {
            transition: background-color 0.2s ease, transform 0.15s ease;
            border-radius: 8px;
        }

        .menu-list .nav-item:hover {
            background-color: #a0d49dff;
            /* light hover bg */
            transform: translateY(-1px);
            color: #fff;
        }

        .menu-list .nav-item:hover .nav-link {
            color: #fff;
            /* bootstrap primary */
        }

        .menu-list .nav-item.active {
            background-color: #dff0ff;
        }


        .swal-mini {
            font-size: 13px !important;
            border-radius: 1px !important;
        }

        /* .swal2-popup {
            width: 19rem !important;
            height: 14rem !important;
        }

        .swal2-header,
        .swal2-title{
            margin-top: -20px;
        }
        .swal2-content{
            margin-top: -5px;
        }

        .swal2-actions {
            margin-top: 10px;
        } */

        .swal2-show {
            animation: swalZoomIn .2s ease-out;
        }

        @keyframes swalZoomIn {
            0% {
                transform: scale(0.85);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .route-active {
            outline: 2px solid #FFD700;
            background-color: #fff9c4 !important;
        }









        .map-controls {
            position: absolute;
            top: 35px;
            right: 15px;
            width: 180px;
            max-height: 75vh;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
            z-index: 1000;
            overflow: hidden;
            font-size: 13px;
        }

        .map-controls-header {
            background: #198754;
            color: #fff;
            padding: 6px 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
        }

        .map-controls-header button {
            padding: 0 8px;
            line-height: 1;
        }

        #mapControlsBody {
            padding: 8px;
            overflow-y: auto;
            max-height: 65vh;
        }

        @media (max-width: 768px) {
            .map-controls {
                width: 90%;
                left: 5%;
                right: auto;
            }
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
                    <span class="text-success">Your cart</span>
                    <span class="badge bg-success rounded-pill">3</span>
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

                <button class="w-100 btn btn-success btn-lg" type="submit">Continue to checkout</button>
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
                    <a href="<?= base_url(); ?>">
                        <img src="<?php echo base_url(); ?>dist/layout_shop/images/logo.svg" alt="logo" class="img-fluid">
                    </a>
                </div>

                <div class="col-7">
                    <ul class="d-flex justify-content-end list-unstyled m-0">
                        <li>
                            <?php if ($role_lvl != "") { ?>
                                <!-- <a href="<?php if ($role_lvl == 0) {
                                                echo base_url(); ?>user_admin<?php } elseif ($role_lvl == 1) {
                                                                                echo '#';
                                                                            } elseif ($role_lvl == 2) {
                                                                                echo $farm_produce;
                                                                            } ?>" class="p-2 mx-1 text-dark" style="text-decoration: none;font-weight: bold">
                                    <i class="fa fa-user"></i> <?php echo $this->session->agrishop_login_uname; ?>
                                </a> -->

                                <?php if ($role_lvl != 2 || $role_lvl != 1) { ?>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#modalCartListing" class="p-2 mx-1 text-dark" style="text-decoration: none;" onclick="getTable('CartListing', 0, 5);">
                                        <i class="fa fa-shopping-basket"></i> Cart<span class="badge bg-warning pending-order" title="pending orders"><?= $this->session->agrishop_pending_trans_count; ?></span>
                                    </a>
                                <?php } ?>

                            <?php } else { ?>
                                <a href="<?php echo base_url(); ?>login" class="p-2 mx-1 text-dark" style="text-decoration: none;">
                                    <i class="fa fa-user"></i> Login
                                </a>
                            <?php } ?>
                        </li>
                        <?php if ($role_lvl == "") { ?>
                            <li><a href="<?php echo base_url(); ?>signup" class="p-2 mx-1 text-dark" style="text-decoration: none;">
                                    <i class="fa fa-user"></i> Sign Up
                                </a>
                            </li>
                        <?php } ?>
                        <?php if ($role_lvl != "") { ?>
                            <li>
                                <a href="#" class="p-2 mx-1 text-dark" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
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
                                <input type="text" class="form-control border-0 bg-transparent" id="searchProduce" placeholder="" autocomplete="off">
                            </form>
                        </div>
                        <div class="col-1" style="text-align: right;">
                            <i class="fa fa-search" onclick="searchProduces()" style="cursor: pointer;"></i>
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

        <div id="map">
            <div id="mapControls" class="map-controls">

                <div class="map-controls-header">
                    <span>🧭 Routes</span>
                    <button id="toggleControls" class="btn btn-xs btn-light">–</button>
                </div>

                <div id="mapControlsBody" style="display: none;">

                    <button id="btnLocateMe" class="btn btn-success btn-sm btn-block mb-1">
                        <i class="fas fa-location-arrow"></i> Get My Location
                    </button>

                    <button id="btnSetManualLocation" class="btn btn-warning btn-sm btn-block mb-2">
                        <i class="fas fa-map-marker-alt"></i> Set Location
                    </button>

                    <button id="btnViewRoutes" class="btn btn-info btn-sm btn-block mb-2">
                        <i class="fas fa-route"></i> View Routes
                    </button>

                    <div id="distanceInfo" class="text-primary small mb-2"></div>

                    <div id="routesContainer" style="display:none;">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Farm</th>
                                    <th>Km</th>
                                </tr>
                            </thead>
                            <tbody id="routesTableBody"></tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
    <!-- <div id="distanceInfo"></div> -->


    <?php $this->load->view('interface/system/layout/modals') ?>
    <?php $this->load->view('interface/system/layout/rating') ?>

    <script src="<?= base_url(); ?>dist/layout_shop/js/jquery-1.11.0.min.js"></script>
    <script src="<?php echo base_url(); ?>dist/layout_shop/js/swiper-bundle.min.js"></script>
    <script src="<?php echo base_url(); ?>dist/layout_shop/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo base_url(); ?>dist/layout_shop/js/plugins.js"></script>
    <script src="<?= base_url(); ?>dist/layout_shop/js/script.js"></script>
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
    <?php $this->load->view('interface/system/layout/map') ?>

    <script>
        $(function() {

            const texts = [
                "Search: Tomato",
                "Cucumber",
                "Kamatis",
                "Kalabasa"
            ];

            const $input = $("#searchProduce");

            let textIndex = 0;
            let charIndex = 0;
            let isDeleting = false;
            let userInteracting = false;

            const typingSpeed = 90;
            const deletingSpeed = 60;
            const pauseAfterTyping = 400;
            const pauseAfterDeleting = 50;

            function startTyping() {

                if (userInteracting) return;

                const currentText = texts[textIndex];

                if (!isDeleting) {
                    // typing
                    $input.attr("placeholder", currentText.substring(0, charIndex + 1));
                    charIndex++;

                    if (charIndex === currentText.length) {
                        setTimeout(() => {
                            isDeleting = true;
                        }, pauseAfterTyping);
                    }

                } else {
                    // deleting
                    $input.attr("placeholder", currentText.substring(0, charIndex - 1));
                    charIndex--;

                    if (charIndex === 0) {
                        isDeleting = false;
                        textIndex = (textIndex + 1) % texts.length;

                        setTimeout(() => {}, pauseAfterDeleting);
                    }
                }

                setTimeout(startTyping, isDeleting ? deletingSpeed : typingSpeed);
            }

            // Pause when user types
            $input.on("focus input", function() {
                userInteracting = true;
            });

            // Resume if empty
            $input.on("blur", function() {
                if ($(this).val() === "") {
                    userInteracting = false;
                }
            });

            startTyping();
        });
    </script>

</body>


</html>