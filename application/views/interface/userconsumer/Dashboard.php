<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- Highcharts -->
<!-- <script src="assets/plugins/Highcharts-8.0.4/code/highcharts.js"></script>
<script src="assets/plugins/Highcharts-8.0.4/code/highcharts-3d.js"></script>
<script src="assets/plugins/Highcharts-8.0.4/code/modules/sankey.js"></script>
<script src="assets/plugins/Highcharts-8.0.4/code/modules/organization.js"></script>
<script src="assets/plugins/Highcharts-8.0.4/code/modules/exporting.js"></script>
<script src="assets/plugins/Highcharts-8.0.4/code/modules/accessibility.js"></script>
<script src="assets/plugins/Highcharts-8.0.4/code/modules/export-data.js"></script>
<script src="assets/plugins/Highcharts-8.0.4/code/modules/drilldown.js"></script>
<script src="assets/plugins/jquery/jquery.form.min.js"></script> -->
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <!-- <h1 class="m-0 text-dark">Dashboard</h1> -->
        <h1 class="m-0 text-dark">Dashboard as of <b><?= Date('F d, Y') ?></b></h1>
      </div><!-- /.col -->
      <!-- <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Dashboard v1</li>
        </ol>
      </div> --><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
  <div class="container-fluid">

    <div class="row">
          
        <div class="col-lg-3 col-6">
          <!-- small box -->
          <div class="small-box bg-info">
            <div class="inner">
              <h3>150</h3>

              <p>New Users</p>
            </div>
            <div class="icon">
              <i class="ion ion-bag"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->

        <div class="col-lg-3 col-6">
          <!-- small box -->
          <div class="small-box bg-success">
            <div class="inner">
              <h3>53<sup style="font-size: 20px">%</sup></h3>

              <p>Sales Rate</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        
        <div class="col-lg-3 col-6">
          <!-- small box -->
          <div class="small-box bg-gray-dark">
            <div class="inner">
              <h3>44</h3>

              <p>User Registrations</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        
        <div class="col-lg-3 col-6">
          <!-- small box -->
          <div class="small-box bg-danger">
            <div class="inner">
              <h3>65</h3>

              <p>Unique Visitors</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->

    </div>

    <!-- /.row -->
    <!-- Main row -->
    <!-- /.row (main row) -->
  </div><!-- /.container-fluid -->
  <div class="row m-0">

    <div class="col-md-6">
      <div class="card">
          <div class="card-header">
            <h5 class="card-title"><i class="fa fa-chart-pie"></i> Graph I</h5>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
              <div class="btn-group show">
                <!-- <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                  <i class="fas fa-wrench"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" role="menu" x-placement="bottom-end">
                  <a href="#" class="dropdown-item">Action</a>
                  <a href="#" class="dropdown-item">Another action</a>
                  <a href="#" class="dropdown-item">Something else here</a>
                  <a class="dropdown-divider"></a>
                  <a href="#" class="dropdown-item">Separated link</a>
                </div> -->
              </div>
              <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-12">
                <!-- <p class="text-center">
                  <strong>Sales: 1 Jan, 2014 - 30 Jul, 2014</strong>
                </p> -->
                <div id="container1" style="height: 530px;margin:-15px;"></div>
              </div>
            </div>
          </div>
          <div class="overlay dark container1">
            <i  style="font-size:100px;color:#fff;" class="fa fa-circle-notch fa-spin"></i>
          </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
          <div class="card-header">
            <h5 class="card-title"><i class="fa fa-chart-bar"></i> Graph II</h5>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
              <div class="btn-group show">
                <!-- <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                  <i class="fas fa-wrench"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" role="menu" x-placement="bottom-end">
                  <a href="#" class="dropdown-item">Action</a>
                  <a href="#" class="dropdown-item">Another action</a>
                  <a href="#" class="dropdown-item">Something else here</a>
                  <a class="dropdown-divider"></a>
                  <a href="#" class="dropdown-item">Separated link</a>
                </div> -->
              </div>
              <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-12">
                <!-- <p class="text-center">
                  <strong>Sales: 1 Jan, 2014 - 30 Jul, 2014</strong>
                </p> -->
                <div id="container1" style="height: 530px;margin:-15px;"></div>
              </div>
            </div>
          </div>
          <div class="overlay dark container1">
            <i  style="font-size:100px;color:#fff;" class="fa fa-circle-notch fa-spin"></i>
          </div>
      </div>
    </div>

    <!-- /.col -->

  </div>
</section>
<script type="text/javascript">
    
</script>

<!-- /.content -->