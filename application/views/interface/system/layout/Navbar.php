<?php
if (!$this->session->agrishop_login_level) {
    // redirect(base_url('login'));
}
$uri = $this->session->agrishop_login_uri;
$role_lvl = $this->session->agrishop_login_level;
$dashboard = base_url() . $uri . '/Dashboard';
$profile = base_url() . $uri . '/Profile';
$farm_produce = base_url() . $uri . '/FarmProduce';


?>
<div class="offcanvas-body">
    <?php if ($role_lvl != "") { ?>
        <ul class="navbar-nav justify-content-end menu-list list-unstyled d-flex gap-md-3 mb-0">
            <li class="nav-item border-dashed active">
                <a href="<?php $dashboard; ?>" class="nav-link d-flex align-items-center gap-3 text-dark p-2">
                    <i class="fa fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item border-dashed">
                <a href="#" data-bs-toggle="modal" data-bs-target="#profileModal" class="nav-link d-flex align-items-center gap-3 text-dark p-2">
                    <i class="fa fa-user"></i>
                    <span>My Profile</span>
                </a>
            </li>
            <!-- <?php //if ($role_lvl == 2) { 
                    ?> -->
            <?php if ($role_lvl != 2 && $this->session->agrishop_request_registration == 0) { ?>
                <!-- <li class="nav-item border-dashed">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#registerFarmerModal" class="nav-link d-flex align-items-center gap-3 text-dark p-2 bg-success">
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
                    <a href="<?= $farm_produce; ?>" class="nav-link d-flex align-items-center gap-3 text-dark p-2">
                        <span><i class="fa fa-tractor"></i> Farm Produce</span>
                    </a>
                </li>
            <?php }
            ?>
            <?php if ($role_lvl == 1) {
            ?>
                <li class="nav-item border-dashed">
                    <a href="index.html" class="nav-link d-flex align-items-center gap-3 text-dark p-2">
                        <i class="fa fa-shopping-basket"></i>
                        <span>My Cart</span>
                    </a>
                </li>
                <li class="nav-item border-dashed">
                    <a href="index.html" class="nav-link d-flex align-items-center gap-3 text-dark p-2">
                        <i class="fa fa-table"></i>
                        <span>My Orders</span>
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