<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>
<?php
if (!$this->session->agrishop_login_level) {
	redirect(base_url('login'));
}
$uri = $this->session->agrishop_login_uri;
?>
<section class="content-header">
	<div class="container-fluid">
		<div class="row mt-2 mb-n1">
			<div class="col-sm-6">
				<h5><i class="nav-icon fas fa-tractor"></i> My Farms</h5>
			</div>
		</div>
	</div><!-- /.container-fluid -->
</section>
<!-- <div class="table-responsive mailbox-messages">
	<table id="tblProfileSample" style="width:100%;" class="table table-sm table-striped table-hover">
		<thead>
			<tr>
				<th width="1">#</th>
				<th width="80"><i class='fa fa-briefcase'></i> Name</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div> -->
<!-- Main content -->
<section class="content">
	<div class="container-fluid">
		<div class="row">

			<div class="col-12 form_save_dataFarmInfo">
				<div class="row">
					
					<div class="col-12">
						<div class="card">
							<div class="card-header pb-0 bg-success">
								<h1 class="card-title"><i class="fa fa-list"></i> List of Farms</h1>
								<div class="card-tools mt-n1">
									<span class='badge bg-navy' data-toggle="modal" data-target="#modalFarmInfo" role="button"><i class="fas fa-plus"></i>  Add Farm</span>
									<!-- <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button> -->
								</div>
							</div>
							<div class="card-body p-2">
								<table id="tblFarmInfo" class="table table-sm table-striped table-hover">
									<thead>
										<tr>
											<!-- <th width="1">#</th> -->
											<th width="50">Name</th>
											<th width="1">Area</th>
											<th width="80">Location</th>
											<th width="1">Active</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
					</div>

				</div>
			</div>


		</div>
	</div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<script type="text/javascript">
	$(function() {
		let f1 = "FarmInfo";
		let f2 = "GradeSecInfo";
		getTable(f1, 0, 5);
		saveForm(f1, [f1], null, 0, 5);
	});
</script>