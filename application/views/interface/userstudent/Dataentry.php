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
				<!-- <h1><i class="nav-icon fas fa-edit"></i> Data Entry</h1> -->
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
			<div class="col-lg-3 col-sm-0">
			</div>
			<div class="col-lg-6 col-sm-12 form_save_dataPersonnelInfo">
				<div class="row">
					<div class="col-md-12">
						<!-- <div class="alert alert-info alert-dismissible">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
							<h5><i class="icon fas fa-info"></i> Alert!</h5>
							Info alert preview. This alert is dismissable.
						</div> -->
						<div class="callout callout-info">
							<h5>Notice!</h5>

							<p><?= $getOnLoad["v_grades"] == 't' ? "Viewing of Grades is available until" : "Viewing of Grades is not available this time"; ?> <b><?= $getOnLoad["vgd"]; ?></b>.</p>
						</div>
						<div class="card card-navy">
							<div class="card-header">
								<h3 class="card-title"><i class="fa fa-list"></i> My Subjects and Grades</h3>
								<!-- <div class="card-tools">
									<button type="button" class="btn btn-tool collapseAssignedSectionList" data-card-widget="collapse"><i class="fas fa-plus"></i>
									</button>
								</div> -->
								<div class="float-right">
									<button type="button" onclick="vdetails();" class="btn btn-default btn-xs"><i class="fas fa-eye"></i> View Details</button>
								</div>
							</div> <!-- /.card -->
							<!-- /.card-header -->
							<div class="card-body">
								<div class="row">
									<div class="col-12 mt-1">
										<div class="card">
											<!-- <div class="card-header">
												<h4 class="card-title w-100 my-n1">
													My Subjects and Grades
												</h4>
											</div> -->
											<div class="card-body p-1 table-responsive">
												<table class="table table-sm table-hover table-striped table-bordered" id="tblGradesList" width="100%">
													<thead style="text-align: center;font-weight: bold;">
														<tr>
															<td rowspan="2" class="pt-3" width="400">Learing Areas</td>
															<td colspan="4">Quarter</td>
															<td rowspan="2" width="1">Final Grade</td>
															<td rowspan="2" class="pt-3" width="1">Remarks</td>
														</tr>
														<tr>
															<td>1</td>
															<td>2</td>
															<td>3</td>
															<td>4</td>
														</tr>

													</thead>
													<tbody>
														<!-- <tr>
													<td>a</td>
													<td>a</td>
													<td>a</td>
													<td>a</td>
												</tr> -->
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- /.card-body -->
						</div>
						<!-- /.card -->
					</div>
					<!-- /.col -->
				</div>
				<!-- /.row -->
				<!-- END ACCORDION & CAROUSEL-->
			</div>
			<div class="col-lg-3 col-sm-0">
			</div>
		</div>
	</div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<script type="text/javascript">
	$(function() {
		// let f1 = "EnrollmentInfo";
		// let f2 = "GradeSecInfo";
		// getTable("AssignedSectionList", 1, -1);
		// getTable("LearnersList", 0, -1);
		// getTable("SearchEnrollLearnersList", 0, 10);
		getTable("GradesList", 1, -1);
		// getFetchList(f1, "CityMunList", null, 1, {
		// 	v: 1602
		// }, 1);
		// getFetchList(f1, "BarangayList", null, 1, {
		// 	v: 160201
		// }, 0);
		// getFetchList(f1, "PurokList", null, 1, {
		// 	v: null
		// }, 0);
		// saveForm(f1, [f1], null);
		// saveForm("GradesList", [null], null, 1, -1);
	});
</script>