<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!$this->session->schoolmis_login_level) {
  redirect(base_url('login'));
}
$uri = 'userteacher'; //$this->session->schoolmis_login_uri;
?>
<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-dark navbar-navy">
  <div class="container">
    <a href="#" class="navbar-brand">
      <img src="<?= $system_svg ?>" alt="ANHSIS Logo" class="brand-image img-circle elevation-3">
      <span class="brand-text font-weight-light"><b>ANHS</b>IS</span>
    </a>

    <div class="collapse navbar-collapse order-1" id="navbarCollapse">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
      </ul>
    </div>

    <!-- Right navbar links -->
    <ul class="order-3 order-md-3 navbar-nav navbar-no-expand ml-auto">
      <li class="nav-item">
        <?= $getOnLoad["sy_qrtr_e_g"]; ?>
        </a>
      </li>
    </ul>
  </div>
</nav>
<!-- /.navbar -->

<!-- Main Sidebar Container -->
<aside class="main-sidebar bg-navy">
  <!-- Brand Logo -->
  <a href="#" class="brand-link">
    <img src="<?= $system_svg ?>" alt="Locator Logo" class="brand-image img-circle elevation-3">
    <span class="brand-text font-weight-light"><b>ANHS</b>IS</span>
  </a>

  <div class="text-right pt-2 pr-2" id="navbarCollapse" style="position: relative;z-index: 9999999;">
    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars text-warning"></i></a>
  </div>

  <!-- Sidebar -->
  <div class="sidebar mt-3">
    <!-- Close Button -->

    <!-- Sidebar user (optional) -->
    <div class="user-panel d-flex">
      <div class="image">
        <!-- <i class="fa fa-user fa-4x text-lightblue mt-2"></i> -->
        <img src="<?= $this->session->schoolmis_login_img ?>" alt="Locator Logo" style="width: 3rem;" class="brand-image img-circle elevation-3" />
      </div>
      <div class="info mb-2">
        <a href="#" class="d-block">
          <dt class="mb-n1"><?= $this->session->schoolmis_login_name ?></dt>
          <dd><?= $this->session->schoolmis_login_title ?></dd>
          <!-- <dd class="mt-n1"><?= $this->session->schoolmis_login_uname ?></dd> -->
        </a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        <li class="nav-item">
          <a href="<?= base_url() . $uri ?>/dataentry" class="nav-link dataentry">
            <i class="nav-icon fas fa-edit data_entry"></i>
            <p>
              My Classes
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= base_url() ?>logout" class="nav-link">
            <i class="nav-icon fas fa-sign-out-alt"></i>
            <p>
              Sign Out
            </p>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>