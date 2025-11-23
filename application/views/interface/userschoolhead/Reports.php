<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Highcharts -->
<!-- Content Header (Page header) -->
<script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>
<?php
if (!$this->session->schoolmis_login_level) {
	redirect(base_url('login'));
}
$uri = "reports"; //$this->session->schoolmis_login_uri;
?>
<!-- Content Header (Page header) -->
<section class="content-header">
	<div class="container-fluid">
		<div class="row mt-2">
			<div class="col-sm-6">
				<h1><i class="nav-icon fas fa-folder-open"></i> Reports</h1>
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
			<div class="col-lg-3 col-md-4 col-sm-12 col-xs-12 form_save_dataGradeSecInfo">
				<div class="card card-navy">
					<div class="card-header p-1 pr-2 pl-2">
						<h3 class="card-title"><i class="fa fa-chart-bar"></i> Gender & 4Ps</h3>
						<div class="card-tools">
							<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
						</div>
					</div>
					<div class="card-body p-2">
						<!-- <div class="row">
							<div class="col-lg-12 col-md-12 col-sm-12">
								<button type="submit" class="btn btn-info btn-sm btn-block submitBtnMFGradelvl">VIEW REPORT</button>
								<div class="input-group mb-2">
									<input type="text" class="form-control form-control-sm text-uppercase" name="abbr" placeholder="ABBREVIATION" autocomplete="off">
									<div class="input-group-append">
										<button type="submit" class="btn btn-info btn-sm submitBtnMFGL">VIEW</button>
									</div>
								</div>
							</div>
						</div> -->
						<form id="form_report_dataG4Ps">
							<div class="row">
								<div class="col-12">
									<div class="input-group">
										<div class="input-group-prepend">
											<!-- <span class="input-group-text"><i class="fas fa-briefcase"></i></span> -->
											<small class="input-group-text text-xs text-bold p-1">SY</small>
										</div>
										<select class="form-control form-control-sm selectEStatusList" name="sy">
											<option value="1">2019-2020</option>
											<option value="2">2021-2022</option>
										</select>
										<div class="input-group-append">
											<button type="submit" class="btn btn-warning btn-sm submitBtnMFGradelvl text-white"><i class="fa fa-search"></i></button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<div class="col-lg-3 col-md-4 col-sm-12 col-xs-12 form_save_dataMPS">
				<div class="card card-navy">
					<div class="card-header p-1 pr-2 pl-2">
						<h3 class="card-title"><i class="fa fa-chart-bar"></i> MPS</h3>
						<div class="card-tools">
							<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
						</div>
					</div>
					<div class="card-body p-2">
						<form id="form_report_dataMPS">
							<div class="row">
								<div class="col-6">
									<div class="input-group">
										<div class="input-group-prepend">
											<!-- <span class="input-group-text"><i class="fas fa-briefcase"></i></span> -->
											<small class="input-group-text text-xs text-bold p-1">SY</small>
										</div>
										<select class="form-control form-control-sm selectEStatusList" name="sy" id="sy">
											<option value="1">2019-2020</option>
											<option value="2">2021-2022</option>
										</select>
									</div>
								</div>
								<div class="col-6">
									<div class="input-group">
										<div class="input-group-prepend">
											<small class="input-group-text text-xs text-bold p-1">Q</small>
										</div>
										<select class="form-control form-control-sm selectSchedList" data-placeholder="SCHEDULE" name="qrtr" id="qrtr">
											<option value="q1">1ST</option>
											<option value="q2">2ND</option>
											<option value="q3">3RD</option>
											<option value="q4">4TH</option>
											<option value="avg">AVG</option>
										</select>
										<div class="input-group-append">
											<button type="button" class="btn btn-warning btn-sm submitBtnMPS text-white"><i class="fa fa-search"></i></button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<div class="col-lg-3 col-md-4 col-sm-12 col-xs-12 form_save_dataGPA">
				<div class="card card-navy">
					<div class="card-header p-1 pr-2 pl-2">
						<h3 class="card-title"><i class="fa fa-chart-bar"></i> GPA</h3>
						<div class="card-tools">
							<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
						</div>
					</div>
					<div class="card-body p-2">
						<form id="form_report_dataGPA">
							<div class="row">
								<div class="col-6">
									<div class="input-group">
										<div class="input-group-prepend">
											<!-- <span class="input-group-text"><i class="fas fa-briefcase"></i></span> -->
											<small class="input-group-text text-xs text-bold p-1">SY</small>
										</div>
										<select class="form-control form-control-sm selectEStatusList" name="sy" id="sy">
											<option value="1">2019-2020</option>
											<option value="2">2021-2022</option>
										</select>
									</div>
								</div>
								<div class="col-6">
									<div class="input-group">
										<div class="input-group-prepend">
											<small class="input-group-text text-xs text-bold p-1">Q</small>
										</div>
										<select class="form-control form-control-sm selectSchedList" data-placeholder="SCHEDULE" name="qrtr" id="qrtr">
											<option value="q1">1ST</option>
											<option value="q2">2ND</option>
											<option value="q3">3RD</option>
											<option value="q4">4TH</option>
											<option value="avg">AVG</option>
										</select>
										<div class="input-group-append">
											<button type="button" class="btn btn-warning btn-sm submitBtnGPA text-white"><i class="fa fa-search"></i></button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<!-- <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form_save_dataGradeSecInfo">
				<div class="card card-navy">
					<div class="card-header p-1 pr-2 pl-2">
						<h3 class="card-title"><i class="fa fa-chart-bar"></i> Age Bracket</h3>
						<div class="card-tools">
							<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
						</div>
					</div>
					<div class="card-body p-2">
					</div>
				</div>
			</div> -->

		</div>
	</div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<!-- <script type="text/javascript">
	$(function() {
		let f2 = "GradeSecInfo";
		getTable(f2, 0, 10);
	});
</script> -->