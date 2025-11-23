<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!$this->session->schoolmis_login_level) {
    redirect(base_url('login'));
}
?>
<!-- Highcharts -->
<?php $this->load->view('interface/userschooladmin/layout/Dashboard'); ?>
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mt-2">
            <div class="col-sm-6">
                <h1><i class="nav-icon fas fa-edit"></i> Dashboard as of <b><?= Date('F d, Y') ?></b></h1>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <div class="row">

            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= $dashboard["learner"]; ?></h3>

                        <p>Learners</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-people-line"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->

            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?= $dashboard["teaching"]; ?></h3>

                        <p>Teaching</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-person-chalkboard"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->

            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?= $dashboard["nteaching"]; ?></h3>

                        <p>Non-Teaching</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-users-line"></i>
                    </div>
                    <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->

            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= $dashboard["scanned"]; ?></h3>

                        <p>Total Scanned ID</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-qrcode"></i>
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

        <div class="col-md-6 col-sm-12">
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
                    <i style="font-size:100px;color:#fff;" class="fa fa-circle-notch fa-spin"></i>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-sm-12">
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
                            <div id="container2" style="height: 530px;margin:-15px;"></div>
                        </div>
                    </div>
                </div>
                <div class="overlay dark container2">
                    <i style="font-size:100px;color:#fff;" class="fa fa-circle-notch fa-spin"></i>
                </div>
            </div>
        </div>

        <!-- /.col -->

    </div>
</section>
<script type="text/javascript">

</script>

<!-- /.content -->