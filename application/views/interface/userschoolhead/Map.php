<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Highcharts -->
<!-- Content Header (Page header) -->
<script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>
<?php
if (!$this->session->schoolmis_login_level) {
	redirect(base_url('login'));
}
$uri = "map"; //$this->session->schoolmis_login_uri;
?>
<!-- Content Header (Page header) -->
<section class="content-header">
	<div class="container-fluid">
		<div class="row mt-2">
			<div class="col-sm-6">
				<h1><i class="nav-icon fas fa-globe"></i> Enrollment Map View (Butan City)</h1>
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
			<div class="col-12 form_save_dataGradeSecInfo">
				<div class="card card-navy">
					<div class="card-header p-1 pr-2 pl-2">
						<h3 class="card-title"><i class="fa fa-chart-bar"></i> Male/Female & 4Ps per Grade level</h3>
						<div class="card-tools">
							<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
						</div>
					</div>
					<div class="card-body p-0">
					<!-- <iframe src="https://lodismap.herokuapp.com/" style="height: 2507.47px;"></iframe> -->
						<iframe src="https://lodismap.herokuapp.com/" style="border:none;width:100%;height:1000px" title="Iframe Example"></iframe>
					</div>
				</div>
			</div>
		</div>
	</div><!-- /.container-fluid -->
</section>
<!-- /.content -->