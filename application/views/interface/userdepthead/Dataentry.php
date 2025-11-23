<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>
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
				<h1><i class="nav-icon fas fa-edit"></i> Data Entry</h1>
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

			<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 form_save_dataGradeSecInfo">
				<div class="card card-navy">
					<div class="card-header p-1 pr-2 pl-2">
						<h3 class="card-title"><i class="fa fa-puzzle-piece"></i> <?= $this->session->schoolmis_login_dept_name ?></h3>
						<div class="card-tools">
							<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
						</div>
					</div>
					<div class="card-body p-2" style="overflow: auto;">
						<div class="table-responsive mailbox-messages">
							<table id="tblGradeSecInfo" style="width:100%;" class="table table-sm table-striped table-hover">
								<thead>
									<tr>
										<th width="1">#</th>
										<th><i class='fa fa-user'></i> Teachers Details</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>

					<div class="overlay dark" style="display:none;">
						<i class="fas fa-2x fa-sync-alt fa-spin"></i>
					</div>

				</div>
			</div>

			<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 form_view_dataGradesInfo">
				<div class="card card-success">
					<div class="card-header p-1 pr-2 pl-2">
						<h3 class="card-title"><i class="fa fa-puzzle-piece"></i> List of Sections Handled</h3>
						<div class="card-tools">
							<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
						</div>
					</div>
					<div class="card-body p-1" style="overflow: auto;">
						<div class="card card-navy p-0 table-responsive viewAllGrades" style="overflow: auto;">
						</div>
					</div>
					<div class="overlay dark" style="display:none;">
						<i class="fas fa-2x fa-sync-alt fa-spin"></i>
					</div>
				</div>
			</div>

		</div>
	</div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<script type="text/javascript">
	$(function() {
		let f2 = "GradeSecInfo";
		getTable(f2, 0, 10);
	});
</script>