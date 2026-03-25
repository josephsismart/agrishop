<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!$this->session->agrishop_login_level) { redirect(base_url('login')); }
$uri      = $this->session->agrishop_login_uri;
$role_lvl = $this->session->agrishop_login_level;
?>

<div id="offcanvasNavbar" class="custom-sidebar">
    <div class="sidebar-header d-flex justify-content-between align-items-center p-3">
        <h4 class="fw-normal text-uppercase fs-6 m-0">Menu</h4>
        <button id="sidebar-close" class="close" style="font-size:2.2rem !important;">&times;</button>
    </div>
    <div class="offcanvas-body pt-2 pl-2">
        <?php $this->load->view('interface/' . $uri . '/Navbar') ?>
    </div>
</div>
<div id="sidebar-backdrop"></div>

<header>
    <div class="container p-0" style="margin-bottom:-50px;">
        <div class="row py-3 border-bottom">
            <div class="col-5 text-center text-sm-start d-flex gap-3">
                <a href="<?= base_url() ?>">
                    <img src="<?= base_url() ?>dist/layout_shop/images/logo.svg" alt="logo" class="img-fluid">
                </a>
            </div>
            <div class="col-7">
                <ul class="d-flex justify-content-end list-unstyled m-0">
                    <?php if ($role_lvl != "") : ?>
                        <li>
                            <a class="text-dark" href="#" style="text-decoration:none;font-weight:bold;">
                                <i class="fa fa-store"></i> <?= $this->session->agrishop_login_uname ?>
                                <span class="badge badge-warning" style="font-size:10px;">SUPPLIER</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" id="bars" class="p-2 mx-1 text-dark">
                                <i class="fa fa-bars"></i>
                                <span class="badge bg-warning countBilling"></span>
                            </a>
                        </li>
                    <?php else : ?>
                        <li><a href="<?= base_url() ?>login" class="p-2 mx-1" style="text-decoration:none;"><i class="fa fa-user"></i> Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</header>
