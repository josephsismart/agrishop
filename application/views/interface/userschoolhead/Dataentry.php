<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Highcharts -->
<!-- Content Header (Page header) -->
<script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>
<?php
if (!$this->session->schoolmis_login_level) {
	redirect(base_url('login'));
}
$uri = $this->session->schoolmis_login_uri;
?>
<!-- Content Header (Page header) -->

<script src="<?= base_url() ?>plugins/highcharts/highcharts.js"></script>
<script src="<?= base_url() ?>plugins/highcharts/highcharts-3d.js"></script>
<script src="<?= base_url() ?>plugins/highcharts/highcharts-more.js"></script>
<!-- <script src="<?= base_url() ?>plugins/highcharts/modules/organization.js"></script>
<script src="<?= base_url() ?>plugins/highcharts/modules/exporting.js"></script>
<script src="<?= base_url() ?>plugins/highcharts/modules/accessibility.js"></script>
<script src="<?= base_url() ?>plugins/highcharts/modules/export-data.js"></script>
<script src="<?= base_url() ?>plugins/highcharts/modules/drilldown.js"></script> -->

<section class="content-header">
	<div class="container-fluid">
		<div class="row mt-2">
			<div class="col-sm-6">
				<h1><i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</h1>
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
			<div class="col-lg-4 col-sm-4 col-12">
				<!-- small card -->
				<div class="small-box bg-info">
					<a href="#" class="small-box-footer">
						Number of Enrollee <i class="fas fa-graduation-cap"></i>
					</a>
					<div class="inner p-1">
						<div class="row mb-n3">
							<div class="col-lg-4 col-sm-12 col-4">
								<div class="small-box bg-info">
									<div class="inner text-center">
										<h3><?= $getSHdboard["emale"]; ?></h3>
									</div>
									<a href="#" class="small-box-footer">
										Male <i class="fas fa-male"></i>
									</a>
								</div>
							</div>
							<div class="col-lg-4 col-sm-12 col-4">
								<div class="small-box bg-info">
									<div class="inner text-center">
										<h3><?= $getSHdboard["efmale"]; ?></h3>
									</div>
									<a href="#" class="small-box-footer">
										Female <i class="fas fa-female"></i>
									</a>
								</div>
							</div>
							<div class="col-lg-4 col-sm-12 col-4">
								<div class="small-box bg-info">
									<div class="inner text-center">
										<h3><?= $getSHdboard["tenroll"]; ?></h3>
									</div>
									<a href="#" class="small-box-footer">
										Total
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- ./col -->
			<div class="col-lg-4 col-sm-4 col-12">
				<!-- small card -->
				<div class="small-box bg-success">
					<a href="#" class="small-box-footer">
						Teaching Personnel <i class="fas fa-user-tie"></i>
					</a>
					<div class="inner p-1">
						<div class="row mb-n3">
							<div class="col-lg-4 col-sm-12 col-4">
								<div class="small-box bg-success">
									<div class="inner text-center">
										<h3><?= $getSHdboard["tpmale"]; ?></h3>
									</div>
									<a href="#" class="small-box-footer">
										Male <i class="fas fa-male"></i>
									</a>
								</div>
							</div>
							<div class="col-lg-4 col-sm-12 col-4">
								<div class="small-box bg-success">
									<div class="inner text-center">
										<h3><?= $getSHdboard["tpfemale"]; ?></h3>
									</div>
									<a href="#" class="small-box-footer">
										Female <i class="fas fa-female"></i>
									</a>
								</div>
							</div>
							<div class="col-lg-4 col-sm-12 col-4">
								<div class="small-box bg-success">
									<div class="inner text-center">
										<h3><?= $getSHdboard["ttpenroll"]; ?></h3>
									</div>
									<a href="#" class="small-box-footer">
										Total
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- ./col -->
			<div class="col-lg-4 col-sm-4 col-12">
				<!-- small card -->
				<div class="small-box bg-warning">
					<a href="#" class="small-box-footer">
						User Account <i class="fas fa-users"></i>
					</a>
					<div class="inner p-1">
						<div class="row mb-n3">
							<div class="col-lg-4 col-sm-12 col-4">
								<div class="small-box bg-warning">
									<div class="inner text-center">
										<h3><?= $getSHdboard["dephead"]; ?></h3>
									</div>
									<a href="#" class="small-box-footer">
										Dept. Head
									</a>
								</div>
							</div>
							<div class="col-lg-4 col-sm-12 col-4">
								<div class="small-box bg-warning">
									<div class="inner text-center">
										<h3><?= $getSHdboard["teacher"]; ?></h3>
									</div>
									<a href="#" class="small-box-footer">
										Teacher
									</a>
								</div>
							</div>
							<div class="col-lg-4 col-sm-12 col-4">
								<div class="small-box bg-warning">
									<div class="inner text-center">
										<h3><?= $getSHdboard["learner"]; ?></h3>
									</div>
									<a href="#" class="small-box-footer">
										Learner
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- ./col -->

			<!-- <div class="col-lg-4 col-sm-4 col-12">
				<div class="small-box bg-warning">
					<div class="inner text-white">
						<div class="row">
							<div class="col-lg-3 col-sm-4 col-4">
								<h3>150</h3>
								<p>Male</p>
							</div>
							<div class="col-lg-3 col-sm-4 col-4">
								<h3>150</h3>
								<p>Female</p>
							</div>
							<div class="col-lg-6 col-sm-4 col-4">
								<h3>150</h3>
								<p>Total</p>
							</div>
						</div>
					</div>
					<div class="icon">
						<i class="fas fa-users"></i>
					</div>
					<a href="#" class="small-box-footer">
						Active User <i class="fas fa-users"></i>
					</a>
				</div>
			</div> -->
			<!-- ./col -->
			<!-- <div class="col-lg-3 col-6">
				<div class="small-box bg-danger">
					<div class="inner">
						<h3>53<sup style="font-size: 20px">%</sup></h3>

						<p>Dropout Percentage</p>
					</div>
					<div class="icon">
						<i class="fas fa-chart-pie"></i>
					</div>
					<a href="#" class="small-box-footer"> 
						More info <i class="fas fa-arrow-circle-right"></i>
					</a>
				</div>
			</div> -->
			<!-- ./col -->
		</div>


		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form_save_dataGradeSecInfo">
				<div class="card card-navy">
					<div class="card-header p-1 pr-2 pl-2">
						<h3 class="card-title"><i class="fa fa-chart-bar"></i> Male & Female Grade level</h3>
						<div class="card-tools">
							<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
						</div>
					</div>
					<div class="card-body p-2">
						<div class="row">
							<!-- <div class="col-lg-3 col-md-5 col-sm-3 col-xs-3">
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<small class="input-group-text text-xs text-bold p-1">S.Y.</small>
									</div>
									<select class="form-control form-control-sm" name="sex">
										<option value="t">2021-2022</option>
										<option value="f">FEMALE</option>
									</select>
								</div>
							</div> -->
							<!-- <div class="col-lg-3 col-md-5 col-sm-3 col-xs-3">
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<small class="input-group-text text-xs text-bold p-1">QRTR</small>
									</div>
									<select class="form-control form-control-sm" name="sex">
										<option value="t">Q1</option>
										<option value="f">FEMALE</option>
									</select>
								</div>
							</div> -->
						</div>
						<!-- <figure class="highcharts-figure"> -->
						<div id="container1"></div>
						<!-- <p class="highcharts-description p-2 mb-n2">
							A basic column chart compares rainfall values between four cities.
							Tokyo has the overall highest amount of rainfall, followed by New York.
							The chart is making use of the axis crosshair feature, to highlight
							months as they are hovered over.
						</p> -->
						<!-- </figure> -->
					</div>
				</div>
			</div>


			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 form_save_dataGradeSecInfo">
				<div class="card card-navy">
					<div class="card-header p-1 pr-2 pl-2">
						<h3 class="card-title"><i class="fa fa-chart-bar"></i> Age Bracket</h3>
						<div class="card-tools">
							<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
						</div>
					</div>
					<div class="card-body p-2">
						<div id="container2"></div>
					</div>
					<!-- <figure class="highcharts-figure">
						<p class="highcharts-description p-2 mb-n2">
							A basic column chart compares rainfall values between four cities.
							Tokyo has the overall highest amount of rainfall, followed by New York.
							The chart is making use of the axis crosshair feature, to highlight
							months as they are hovered over.
						</p>
					</figure> -->
				</div>
			</div>

		</div>
	</div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<script type="text/javascript">
    
    $.post("<?= base_url($uri . '/dashboard/getMFGradelvl') ?>",
        function(data) {
            var result = JSON.parse(data);
            // console.log(result)
            // console.log(data)
    Highcharts.chart('container1', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Male & Female per Grade Level'
        },
        subtitle: {
            text: 'School Year <?= $getOnLoad["sy"]; ?> as of <?= Date("M d, Y"); ?>'
        },
        xAxis: {
            categories: result["categories"],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Male/Female Count'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.0f}</b></td> <td style="padding:0"><b>{series.y}</b></td></tr>'+
                '</tr>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: result["series"]
    });
        }
    ).then(function() {
    });


    $.post("<?= base_url($uri . '/dashboard/getMFAgebracket') ?>",
        function(data) {
            var result = JSON.parse(data);
            // console.log(result)
            // console.log(data)

    Highcharts.chart('container2', {
        chart: {
            type: 'column'
        },
        title: {
            text: 'Male & Female per Age Bracket'
        },
        subtitle: {
            text: 'School Year <?= $getOnLoad["sy"]; ?> as of <?= Date("M d, Y"); ?>'
        },
        xAxis: {
            categories: result["categories"],
            crosshair: true
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Male/Female Count'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.0f}</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: result["series"]
    });
        }
    ).then(function() {
    });
</script>