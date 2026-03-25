<?php
if (!$this->session->agrishop_login_level) { redirect(base_url('login')); }
$uri       = $this->session->agrishop_login_uri;
$role_lvl  = $this->session->agrishop_login_level;
$dashboard = base_url() . $uri . '/dashboard';
$supplies  = base_url() . $uri . '/Supplies';
$orders    = base_url() . $uri . '/Orders';
$billing   = base_url() . $uri . '/Billing';
?>
<div class="offcanvas-body">
    <?php if ($role_lvl != "") : ?>
        <ul class="navbar-nav justify-content-end menu-list list-unstyled d-flex gap-md-3 mb-0">

            <li class="nav-item border-dashed dashboard">
                <a href="<?= $dashboard ?>" class="nav-link d-flex align-items-center gap-3 text-dark p-2">
                    <i class="fa fa-chart-line"></i><span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item border-dashed">
                <a href="#" data-toggle="modal" data-target="#modalUpdateProfile"
                   class="nav-link d-flex align-items-center gap-3 text-dark p-2">
                    <i class="fa fa-user"></i><span>My Profile</span>
                </a>
            </li>

            <li class="nav-item border-dashed Supplies">
                <a href="<?= $supplies ?>" class="nav-link d-flex align-items-center gap-3 text-dark p-2">
                    <i class="fa fa-boxes"></i><span>My Supplies</span>
                </a>
            </li>

            <li class="nav-item border-dashed Orders">
                <a href="<?= $orders ?>" class="nav-link d-flex align-items-center gap-3 text-dark p-2">
                    <i class="fa fa-shopping-bag"></i><span>Orders</span> <span class="badge badge-danger countOrders" style="font-size:10px;"></span>
                </a>
            </li>

            <li class="nav-item border-dashed billing">
                <a href="<?= $billing ?>" class="nav-link d-flex align-items-center gap-3 text-dark p-2">
                    <i class="fa fa-file-invoice"></i>
                    <span>Billing
                        <?php if (isset($billing_count) && $billing_count > 0) : ?>
                            <span class="badge bg-danger"><?= $billing_count ?></span>
                        <?php endif; ?>
                    </span>
                </a>
            </li>

            <li class="nav-item border-dashed">
                <a href="<?= base_url() ?>login/request_logout"
                   class="nav-link d-flex align-items-center gap-3 text-dark p-2">
                    <i class="fa fa-sign-out-alt"></i><span>Logout</span>
                </a>
            </li>

        </ul>
    <?php endif; ?>
</div>
