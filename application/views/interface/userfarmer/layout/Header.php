<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!$this->session->agrishop_login_level) {
  redirect(base_url('login'));
}
$uri = $this->session->agrishop_login_uri;
$role_lvl = $this->session->agrishop_login_level;
$dashboard = base_url() . $uri . '/Dashboard';
$profile = base_url() . $uri . '/Profile';
$farms = base_url() . $uri . '/Farms';


?>
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar">

  <div class="offcanvas-header justify-content-between">
    <h4 class="fw-normal text-uppercase fs-6">Menu</h4>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>

  <div class="offcanvas-body">
    <?php if ($role_lvl!="") { ?>
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
        <li class="nav-item border-dashed">
          <a href="#" data-bs-toggle="modal" data-bs-target="#registerFarmerModal" class="nav-link d-flex align-items-center gap-3 text-dark p-2 bg-success">
            <i class="fa fa-paste"></i>
            <span>Register as Farmer</span>
          </a>
        </li>
      <?php } ?>
      <?php if ($this->session->agrishop_request_registration == 1) { ?>
        <li class="nav-item border-dashed">
          <a href="#" class="nav-link d-flex align-items-center gap-3 text-dark p-2 bg-warning">
            <i class="fa fa-exclamation-triangle"></i>
            <span>Pending Registration as Farmer</span>
          </a>
        </li>
      <?php } ?>
      <!-- <?php //if ($role_lvl == 2) { 
            ?> -->
      <li class="nav-item border-dashed">
        <a href="<?php $farms; ?>" class="nav-link d-flex align-items-center gap-3 text-dark p-2">
          <i class="fa fa-tractor"></i>
          <span>My Farms</span>
        </a>
      </li>
      <li class="nav-item border-dashed">
        <a href="index.html" class="nav-link d-flex align-items-center gap-3 text-dark p-2">
          <i class="fa fa-carrot"></i>
          <span>My Produce</span>
        </a>
      </li>
      <!-- <?php //} 
            ?> -->
      <!-- <?php //if ($role_lvl == 1 or $role_lvl == 2) { 
            ?> -->
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
      <!-- <?php //} 
            ?> -->
      <li class="nav-item border-dashed">
        <a href="<?= base_url() ?>logout" class="nav-link d-flex align-items-center gap-3 text-dark p-2">
          <i class="fa fa-sign-out-alt"></i>
          <span>Logout</span>
        </a>
      </li>
    </ul>
    <?php } ?>

  </div>

</div>

<header>

  <div class="container p-0" style="margin-bottom:-50px;">
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

      <!-- <div class="col-12 mt-3">
        <div class="search-bar row bg-light p-2 rounded-4">

          <div class="col-11">
            <form id="search-form" class="text-center" action="index.html" method="post">
              <input type="text" class="form-control border-0 bg-transparent" placeholder="Search for more than 20,000 products">
            </form>
          </div>
          <div class="col-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
              <path fill="currentColor" d="M21.71 20.29L18 16.61A9 9 0 1 0 16.61 18l3.68 3.68a1 1 0 0 0 1.42 0a1 1 0 0 0 0-1.39ZM11 18a7 7 0 1 1 7-7a7 7 0 0 1-7 7Z" />
            </svg>
          </div>
        </div>
      </div> -->

    </div>
  </div>
</header>