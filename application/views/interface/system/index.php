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
    <!-- Toastr -->
    <link rel="stylesheet" href="<?= base_url() ?>plugins/toastr/toastr.min.css">
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
            height: calc(100vh - var(--agri-nav-h, 64px));

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
            top: calc(var(--agri-nav-h, 64px) + 10px);
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
            top: 55px;
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
                width: 38%;
                left: 62%;
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

    <?php $this->load->view('interface/system/layout/Navbar') ?>
    <div class="agri-nav-spacer"></div>

    <!-- <div class="buttons" id="cityButtons"></div> -->
    <div id="suppliesShopSection" style="display:none;">
        <div class="container py-3">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h5 class="m-0 font-weight-bold">
                    <i class="fa fa-store text-warning mr-2"></i> Farm Supplies Shop
                </h5>
                <button class="btn btn-sm btn-outline-secondary" onclick="hideSuppliesShop()">
                    <i class="fa fa-times mr-1"></i> Back to Map
                </button>
            </div>

            <!-- Search + Category filter row -->
            <div class="row mb-3">
                <div class="col-md-8 mb-2">
                    <div class="input-group">
                        <input type="text" id="supplySearchInput" class="form-control" placeholder="Search supplies... (fertilizer, seeds, tools...)" onkeyup="if(event.key==='Enter') refreshSuppliesTable()">
                        <div class="input-group-append">
                            <button class="btn btn-warning" onclick="refreshSuppliesTable()">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <select id="supplyCategoryFilter" class="form-control" onchange="refreshSuppliesTable()">
                        <option value="">All Categories</option>
                        <!-- populated via JS -->
                    </select>
                </div>
            </div>

            <!-- Datatable -->
            <style>
                #tblSuppliesShop thead { display: none; }
                #tblSuppliesShop td { padding: 0 !important; border: none; }
                #tblSuppliesShop.dataTable { border-collapse: collapse !important; }
            </style>
            <div class="card shadow-sm" style="border-radius:12px;overflow:hidden;">
                <div class="card-body p-0">
                    <table id="tblSuppliesShop" class="table" style="width:100%;margin:0;">
                        <thead><tr><th>Supply</th></tr></thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <!-- Cart summary badge (if logged in) -->
        </div>
    </div>
    <div class="container-fluid p-0">
        <?php $this->load->view('interface/system/layout/landing_page') ?>

        <div id="map">
            <div id="mapControls" class="map-controls">

                <div class="map-controls-header toggleControls" style="cursor: pointer;">
                    <span style="font-weight: bold;"><i class="fa fa-map-marker-alt"></i> Routes</span>
                    <button class="btn btn-xs btn-light toggleControls_">–</button>
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
    <!-- <script src="<?php echo base_url(); ?>dist/layout_shop/js/bootstrap.bundle.min.js"></script> -->
    <script src="<?= base_url() ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo base_url(); ?>dist/layout_shop/js/plugins.js"></script>
    <!-- Toastr -->
    <script src="<?= base_url() ?>plugins/toastr/toastr.min.js"></script>
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
    <?php if ($this->session->agrishop_login_id) $this->load->view('interface/usercustomer/OrdersModal') ?>

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

            setInterval(function() {
                checkOrderStatus();
            }, 2000);

            let currentCount = "";

            function checkOrderStatus() {
                $.get("<?= base_url('check_order_status') ?>", {
                    interval: 'realtime'
                }, function(res) {
                    let j = JSON.parse(res);

                    $('.pending-order').text(j.transaction_status_pending);
                    $('.reserved-order').text(j.transaction_status_reserved);
                    $('.preparing-order').text(j.transaction_delivery_status_preparing);
                    $('.ready-for-pickup-order').text(j.transaction_delivery_status_pickup);
                    $('.out-for-delivery-order').text(j.transaction_delivery_status_delivery);
                    $('.rate-order').text(j.transaction_ratings);

                    // Track delivery statuses specifically
                    let deliverySnapshot = {
                        preparing: j.transaction_delivery_status_preparing,
                        pickup: j.transaction_delivery_status_pickup,
                        delivery: j.transaction_delivery_status_delivery,
                    };

                    let res_ = JSON.stringify(deliverySnapshot);

                    if (currentCount === "") {
                        currentCount = res_;
                    }

                    if (currentCount !== res_) {
                        let prev = JSON.parse(currentCount);
                        let curr = deliverySnapshot;

                        // Check each delivery status individually
                        if (prev.preparing !== curr.preparing) notifyNewReservation('preparing', prev.preparing, curr.preparing);
                        if (prev.pickup !== curr.pickup) notifyNewReservation('pickup', prev.pickup, curr.pickup);
                        if (prev.delivery !== curr.delivery) notifyNewReservation('delivery', prev.delivery, curr.delivery);
                        currentCount = res_;
                    }

                    lastReservedCount = currentCount;
                });
            }
        });


        function notifyNewReservation(type, prevCount, newCount) {
            if (newCount <= prevCount) return; // ← bail out if not increased

            notifySound();

            const messages = {
                preparing: `🍳 ${newCount} order(s) are now being <b>Prepared</b>`,
                pickup: `📦 ${newCount} order(s) are <b>Ready for Pickup</b>`,
                delivery: `🚗 ${newCount} order(s) are <b>Out for Delivery</b>`,
            };

            toastr.info(messages[type], 'Order Update', {
                closeButton: true,
                timeOut: 5000,
                escapeHtml: false
            });

            if (typeof getTable === 'function') {
                getTable('CartListing', 0, 5);
            }
        }


        /* -------------------------------
   GLOBAL AUDIO SETUP
--------------------------------*/
        window.notifAudio = window.notifAudio || new Audio("<?= base_url('dist/notification/notify.wav') ?>");
        notifAudio.volume = 1.0;
        window.audioUnlocked = false;

        // unlock audio on first user gesture (click anywhere)
        document.addEventListener('click', function unlockAudio() {
            notifAudio.play()
                .then(() => {
                    notifAudio.pause();
                    notifAudio.currentTime = 0;
                    window.audioUnlocked = true;
                    console.log('🔓 Audio unlocked, notifications ready');
                })
                .catch(() => console.warn('❌ Audio blocked until user interacts'));

            document.removeEventListener('click', unlockAudio);
        }, {
            once: true
        });


        /* -------------------------------
           SAFE AUDIO PLAY FUNCTION
        --------------------------------*/
        function notifySound() {
            if (!window.audioUnlocked) return;

            if (!notifAudio.paused) {
                notifAudio.pause();
                notifAudio.currentTime = 0;
            }

            notifAudio.play().catch(err => console.warn('❌ Sound failed:', err));
        }








        /* ── Supplies shop logic ── */
        let suppliesTableInit = false;

        function showSuppliesShop() {
            $('#suppliesShopSection').show();
            $('#map').hide();
            $('#landing_Page').hide();
            $('#mapControls').hide();
            loadSupplyCategories();
            if (!suppliesTableInit) {
                initSuppliesTable();
                suppliesTableInit = true;
            }
        }

        function hideSuppliesShop() {
            $('#suppliesShopSection').hide();
            $('#landing_Page').show();
        }

        function loadSupplyCategories() {
            $.get("<?= base_url('userpublicmap/map/getSupplyCategories') ?>", function(res) {
                let cats = JSON.parse(res);
                let opts = '<option value="">All Categories</option>';
                cats.forEach(c => {
                    opts += `<option value="${c.id}">${c.name}</option>`;
                });
                $('#supplyCategoryFilter').html(opts);
            });
        }

        function initSuppliesTable() {
            $("#tblSuppliesShop").DataTable({
                dom: 'rtip',
                processing: true,
                serverSide: true,
                searching: false,
                order: [],
                ajax: {
                    url: "<?= base_url('userpublicmap/map/getSuppliesShop') ?>",
                    type: "POST",
                    data: function(d) {
                        d.search.value = $('#supplySearchInput').val();
                        d.search.category = $('#supplyCategoryFilter').val();
                    }
                },
                columns: [
                    { data: 'card', orderable: false }
                ],
                lengthMenu: [10, 25, 50],
                pageLength: 10,
            });
        }

        function refreshSuppliesTable() {
            if (!suppliesTableInit) {
                initSuppliesTable();
                suppliesTableInit = true;
                return;
            }
            $("#tblSuppliesShop").DataTable().ajax.reload();
        }

        function viewOnMap(lat, lon, storeName) {
            hideSuppliesShop();
            $('#landing_Page').hide();
            $('#map').show();
            // Use window.map set in map.php; invalidate size after show
            setTimeout(function() {
                if (typeof window.map !== 'undefined') {
                    window.map.invalidateSize();
                    window.map.setView([lat, lon], 16);
                    L.popup()
                        .setLatLng([lat, lon])
                        .setContent('<div style="font-weight:600;font-size:14px;"><i class="fa fa-store mr-1"></i>' + storeName + '</div>')
                        .openOn(window.map);
                }
            }, 150);
        }

        function addSupplyToCartShop(supply_id, price, qtyId, name) {
            <?php if (!$this->session->agrishop_login_id) : ?>
                Swal.fire({
                    icon: 'warning',
                    title: 'Login Required',
                    text: 'Please login to add items to your cart.',
                    confirmButtonText: 'Login',
                    confirmButtonColor: '#e67e22',
                }).then(r => {
                    if (r.isConfirmed) window.location = "<?= base_url('login') ?>";
                });
                return;
            <?php endif; ?>

            let qty = parseInt($('#' + qtyId).val()) || 1;
            $.post("<?= base_url('userpublicmap/map/addSupplyToCart') ?>", {
                    supply_id,
                    qty,
                    price
                },
                function(res) {
                    let d = JSON.parse(res);
                    if (d.success) {
                        if (typeof successAlert === 'function') successAlert(name + ' added to cart!');
                        else alert(name + ' added to cart!');
                        updateSupplyCartBadge();
                    } else {
                        if (typeof failAlert === 'function') failAlert(d.message || 'Failed!');
                        else alert(d.message || 'Failed!');
                    }
                }
            );
        }


    </script>

</body>


</html>