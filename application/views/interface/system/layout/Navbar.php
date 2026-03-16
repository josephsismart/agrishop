<?php
if (!$this->session->agrishop_login_level) {
    // redirect(base_url('login'));
}
$uri = $this->session->agrishop_login_uri;
$role_lvl = $this->session->agrishop_login_level;
$dashboard = base_url() . $uri . '/Dashboard';
$subscription = base_url() . $uri . '/Subscription';
$billing = base_url() . $uri . '/Billing';
$profile = base_url() . $uri . '/Profile';
$farm_produce = base_url() . $uri . '/FarmProduce';
$client_orders = base_url() . $uri . '/Orders';
$on_production = base_url() . $uri . '/OnProduction';


?>
<div class="offcanvas-body">
    <?php if ($role_lvl != "") { ?>
        <ul class="navbar-nav justify-content-end menu-list list-unstyled d-flex gap-md-3 mb-0">

            <?php if ($role_lvl == 2 || $role_lvl == 3) { ?>
                <li class="nav-item border-dashed">
                    <a href="<?= $dashboard; ?>" class="nav-link d-flex align-items-center gap-3 text-dark p-2">
                        <i class="fa fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            <?php } ?>
            <li class="nav-item border-dashed">
                <a href="#" data-toggle="modal" data-target="#profileModal" class="nav-link d-flex align-items-center gap-3 text-dark p-2">
                    <i class="fa fa-user"></i>
                    <span><?= $this->session->agrishop_login_first_name; ?> <?= $this->session->agrishop_login_last_name; ?></span>
                </a>
            </li>

            <?php if ($role_lvl == 3) { ?>
                <li class="nav-item border-dashed">
                    <a href="#" data-toggle="modal" data-target="#gcashModal" class="nav-link d-flex align-items-center gap-3 text-dark p-2">
                        <img src="<?= base_url('dist/img/credit/gcash_50x50.png'); ?>" class="mr-n1 ml-n1" height="21" width="21" /> <span>My Gcash</span>
                    </a>
                </li>
            <?php } ?>
            <?php if ($role_lvl == 3 || $role_lvl == 2) { ?>
                <li class="nav-item border-dashed">
                    <a href="<?= $billing; ?>" class="nav-link d-flex align-items-center gap-3 text-dark p-2">
                        <i class="fa fa-credit-card"></i>
                        <span> Billing</span> <span class="badge bg-warning countBilling"></span>
                    </a>
                </li>
            <?php } ?>
            <!-- <?php //if ($role_lvl == 2) { 
                    ?> -->
            <?php if ($role_lvl != 2 && $this->session->agrishop_request_registration == 0) { ?>
                <!-- <li class="nav-item border-dashed">
                    <a href="#" data-toggle="modal" data-target="#registerFarmerModal" class="nav-link d-flex align-items-center gap-3 text-dark p-2 bg-success">
                        <i class="fa fa-paste"></i>
                        <span>Register as Farmer</span>
                    </a>
                </li> -->
            <?php } ?>
            <?php if ($this->session->agrishop_request_registration == 1) { ?>
                <li class="nav-item border-dashed">
                    <a href="#" class="nav-link d-flex align-items-center gap-3 text-dark p-2 bg-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        <span>Pending Registration as Farmer</span>
                    </a>
                </li>
            <?php } ?>
            <?php if ($role_lvl == 2) {
            ?>
                <li class="nav-item border-dashed">
                    <a href="#" data-toggle="modal" data-target="#gcashModal" class="nav-link d-flex align-items-center gap-3 text-dark p-2">
                        <img src="<?= base_url('dist/img/credit/gcash_50x50.png'); ?>" class="mr-n1 ml-n1" height="21" width="21" /> <span>My Gcash</span>
                    </a>
                </li>

                <li class="nav-item border-dashed">
                    <a href="<?= $farm_produce; ?>" class="nav-link d-flex align-items-center gap-3 text-dark p-2">
                        <i class="fa fa-tractor"></i> <span>Farm Produce</span>
                    </a>
                </li>

                <li class="nav-item border-dashed">
                    <a href="<?= $on_production; ?>" class="nav-link d-flex align-items-center gap-3 text-dark p-2">
                        <i class="fa fa-seedling"></i> <span>On Production</span>
                    </a>
                </li>

                <li class="nav-item border-dashed">
                    <a href="<?= $client_orders; ?>" class="nav-link d-flex align-items-center gap-3 text-dark p-2">
                        <i class="fa fa-shopping-basket"></i> <span>Client Orders</span> <span class="badge bg-info countOrders"><?= $this->session->agrishop_reserved_trans_count > 0 ? $this->session->agrishop_reserved_trans_count : ''; ?></span>
                    </a>
                </li>

            <?php }
            ?>
            <?php if ($role_lvl == 1) {
            ?>
                <li class="nav-item border-dashed">
                    <a href="#" class="nav-link d-flex align-items-center gap-3 text-dark p-2" data-toggle="modal" data-target="#modalCartListing" onclick="getTable('CartListing', 0, 5);">
                        <i class="fa fa-shopping-basket"></i>
                        <span>My Cart</span><span class="badge bg-warning pending-order" title="pending orders"><?= $status['transaction_status_pending'] ?></span>
                    </a>
                </li>
                <li class="nav-item border-dashed">

                    <!-- MAIN MENU -->
                    <a class="nav-link d-flex align-items-center gap-3 text-dark p-2" data-toggle="collapse" href="#orderSubMenu">

                        <i class="fa fa-table"></i>
                        <span>My Orders</span>
                        <i class="fa fa-chevron-down ms-auto"></i>
                    </a>

                    <!-- SUB MENU -->
                    <ul class="collapsed list-unstyled ps-4 pl-5" id="orderSubMenu">

                        <li>
                            <a href="#" class="nav-link text-dark"  data-toggle="modal" data-target="#modalOrderListing" onclick="status_='RESERVED';getTable('OrderListing', 0, 5);" title="Reserved orders">
                                <i class="fa fa-clock"></i> Reserved <span class="badge bg-danger reserved-order" title="reserved orders"><?= $status['transaction_status_reserved'] ?></span>
                            </a>
                        </li>

                        <li>
                            <a href="#" class="nav-link text-dark" data-toggle="modal" data-target="#modalOrderListing" onclick="status_='PREPARING';getTable('OrderListing', 0, 5)" title="Preparing orders">
                                <i class="fa fa-people-carry"></i> Preparing <span class="badge bg-danger preparing-order" title="preparing orders"><?= $status['transaction_delivery_status_preparing']; ?></span>
                            </a>
                        </li>

                        <li>
                            <a href="#" class="nav-link text-dark" data-toggle="modal" data-target="#modalOrderListing" onclick="status_='TO_PICKUP';getTable('OrderListing', 0, 5)" title="Ready for Pickup">
                                <i class="fa fa-box"></i> Ready for Pickup <span class="badge bg-danger ready-for-pickup-order" title="ready for pickup orders"><?= $status['transaction_delivery_status_pickup']; ?></span>
                            </a>
                        </li>

                        <li>
                            <a href="#" class="nav-link text-dark" data-toggle="modal" data-target="#modalOrderListing" onclick="status_='TO_DELIVER';getTable('OrderListing', 0, 5)" title="Out for Delivery">
                                <i class="fa fa-truck"></i>   Out for Delivery <span class="badge bg-danger out-for-delivery-order" title="out for delivery orders"><?= $status['transaction_delivery_status_delivery']; ?></span>
                            </a>
                        </li>

                    </ul>

                </li>
                <li class="nav-item border-dashed">
                    <a href="#" class="nav-link d-flex align-items-center gap-3 text-dark p-2" data-toggle="modal" data-target="#modalCompletedOrderListing" onclick="getTable('CompletedOrderListing', 0, 5);" title="Completed Orders">
                        <i class="fa fa-check-circle"></i>
                        <span>Completed Orders</span>
                    </a>
                </li>
                <li class="nav-item border-dashed">
                    <a href="#" class="nav-link d-flex align-items-center gap-3 text-dark p-2" data-toggle="modal" data-target="#modalCancelledOrderListing" onclick="getTable('CancelledOrderListing', 0, 5);" title="Cancelled Orders">
                        <i class="fa fa-times-circle"></i>
                        <span>Cancelled Orders</span>
                    </a>
                </li>
                <li class="nav-item border-dashed">
                    <a href="#" class="nav-link d-flex align-items-center gap-3 text-dark p-2" data-toggle="modal" data-target="#modalRateOrderListing" onclick="getTable('RateOrderListing', 0, 5);" title="Rate Orders">
                        <i class="fa fa-star"></i>
                        <span>Rate Orders</span> <span class="badge bg-primary rate-order" title="rate orders"><?= $status['transaction_ratings']; ?></span>
                    </a>
                </li>
            <?php } ?>

            <li class="nav-item border-dashed">
                <a href="<?= base_url() ?>logout" class="nav-link d-flex align-items-center gap-3 text-dark p-2">
                    <i class="fa fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    <?php } ?>

</div>