<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<script src="<?= base_url() ?>assets/plugins/jquery/jquery.form.min.js"></script>
<?php 
    if (!$this->session->schoolmis_login_level) {
        redirect(base_url('login'));
    }
    $uri = $this->session->schoolmis_login_uri;
 ?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mt-2">
      <div class="col-sm-6">
        <h1><i class="nav-icon fas fa-file"></i> Reports</h1>
      </div>
      <!-- <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Advanced Form</li>
        </ol>
      </div> -->
    </div>
  </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-2 col-md-6 col-sm-12 col-xs-12 form_report_dataMemberUser">
        <form id="form_report_dataMemberUser">
          <div class="card card-info">
            <div class="card-header">
              <h3 class="card-title"><i class="fa fa-list"></i> Members & Users</h3>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                <!-- <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button> -->
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-2" style="overflow: auto;">
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="input-group">
                      <select class="form-control" name="filterMemberUser">
                        <option value="0">View All</option>
                        <option value="2">Members</option>
                        <option value="1">Users</option>
                      </select>
                      <div class="input-group-append">
                        <button type="button" onclick="reportForm('MemberUser');" class="btn btn-success submitBtnPrimary">Go!</button>
                      </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
        </form>
      </div>
      
      <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 form_report_dataInvPO">
        <form id="form_report_dataInvPO">
          <div class="card card-info">
            <div class="card-header">
              <h3 class="card-title"><i class="fa fa-list"></i> Inventory details & Purchase Orders</h3>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                <!-- <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button> -->
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-2" style="overflow: auto;">
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="input-group">
                      <input type="date" value="<?= Date('Y-m-d') ?>" class="form-control" name="filterInvPOfromDate">
                      <div class="input-group-prepend input-group-append">
                        <span class="input-group-text">TO</span>
                      </div>
                      <input type="date" value="<?= Date('Y-m-d') ?>" class="form-control" name="filterInvPOtoDate">
                      <div class="input-group-append">
                        <button type="button" onclick="reportForm('InvPO');" class="btn btn-success submitBtnPrimary">Go!</button>
                      </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
        </form>
      </div>
      
      <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 form_report_dataStckPR">
        <form id="form_report_dataStckPR">
          <div class="card card-info">
            <div class="card-header">
              <h3 class="card-title"><i class="fa fa-list"></i> Stocks & Purchase Requests</h3>

              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                <!-- <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button> -->
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-2" style="overflow: auto;">
              <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12">
                  <div class="input-group">
                      <input type="date" value="<?= Date('Y-m-d') ?>" class="form-control" name="filterStckPRfromDate">
                      <div class="input-group-prepend input-group-append">
                        <span class="input-group-text">TO</span>
                      </div>
                      <input type="date" value="<?= Date('Y-m-d') ?>" class="form-control" name="filterStckPRtoDate">
                      <div class="input-group-append">
                        <button type="button" onclick="reportForm('StckPR');" class="btn btn-success submitBtnPrimary">Go!</button>
                      </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
        </form>
      </div>


    </div>
  </div><!-- /.container-fluid -->
</section>
<!-- /.content -->