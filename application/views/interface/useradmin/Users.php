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
			<div class="col-sm-12">
				<h5><i class="nav-icon fas fa-users"></i> Users</h5>
			</div>
		</div>
	</div><!-- /.container-fluid -->
</section>

<section class="content">
	<div class="container-fluid">
		<div class="row">

			<div class="col-12">
				<div class="row">
					<div class="col-12">
						<div class="card">

							<div class="col-lg-4 col-md-4 col-sm-6 col-8">

							</div>
							<div class="card-header bg-navy">
								<h1 class="card-title"><i class="fa fa-list"></i> List of Users</h1>
							</div>
							<div class="card-body p-2" style="overflow: auto;">
								<div class="mb-2 text-primary">
									<i class="fas fa-info-circle"></i> Kindly <b>click the FOR APPROVAL</b> to update the status.
								</div>
								<table id="tblUsersInfo" class="table table-sm table-striped table-hover">
									<thead>
										<tr>
											<th width="1"></th>
											<th width="60">Image</th>
											<th width="1">Name</th>
											<th width="1">Date Joined</th>
											<th width="1">Contact Info</th>
											<th width="120">Address</th>
											<th width="1">Status</th>
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
		let f1 = "UsersInfo";
		getTable(f1, 0, 10);

	});


	function approveFarmer(id) {
		farmer_id_ = id;
		img = $('#approveFarmerBtn' + id).data('img');
		$('#idFarmerImg').attr('src', img);
		$('#modalApproveFarmer').modal('show');
	}

	function approveFarmerNow() {
		$.post("<?= base_url('useradmin/Users/approveFarmer') ?>", {
			farmer_id: farmer_id_,
			status: $('#userStatus').val(),
			note: $('#modalUserStatus #note').val()
		}, function(res) {
			let j = JSON.parse(res);
			if (j.success == true) {
				successAlert(j.message);
				getTable('UsersInfo', 0, 10);
				$("#modalApproveFarmer").modal("hide");

			} else {
				errorAlert(j.message);
			}
		});
	}
</script>