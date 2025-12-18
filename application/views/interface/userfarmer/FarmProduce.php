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
				<h5><i class="nav-icon fas fa-tractor"></i> Farms & <i class="nav-icon fas fa-carrot"></i> Produce</h5>
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

							<div class="col-lg-4 col-md-4 col-sm-6 col-8">
								<div class="form-group">
									<label class="col-form-label"><i class="fas fa-house"></i> My Farm with Produce</label>
									<select class="form-control border-primary" name="farmList"  id="farmList" onchange="getTable('FarmProduceInfo', 0, 5);">

										<?php
										$person_id = $this->session->agrishop_person_id;

										$query = $this->db->query("SELECT t1.id, t1.farm_name,
																		(SELECT count(1) FROM farm_produce WHERE farm_id=t1.id) AS produce_c
																	FROM farmer_farm t1
																	WHERE created_by_person_id=$person_id");

										foreach ($query->result() as $key => $value) {
											echo "<option value='" . $value->id . "'>" . $value->farm_name . " - (".$value->produce_c.")</option>";
										}
										?>
									</select>
								</div>
							</div>
							<div class="card-header pb-0 bg-success">
								<h1 class="card-title"><i class="fa fa-list"></i> Farm & Produce</h1>
								<div class="card-tools mt-n1">
									<span class='badge bg-navy' data-toggle="modal" data-target="#modalFarmProduceSupply" role="button"><i class="fas fa-plus"></i> Create New Supply</span>
									<!-- <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button> -->
								</div>
							</div>
							<div class="card-body p-2" style="overflow: auto;">
								<table id="tblFarmProduceInfo" class="table table-sm table-striped table-hover">
									<thead>
										<tr>
											<th width="1"></th>
											<th width="50">Image</th>
											<th width="50">Produce</th>
											<th width="30">Harvested</th>
											<th width="1">Qty</th>
											<th width="1">price</th>
											<th width="1">uom</th>
											<th width="1">Class</th>
											<th width="1">Action</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<div class="col-lg-6 col-12 mt-5">
						<div class="card">
							<div class="card-header pb-0">
								<h1 class="card-title"><i class="fa fa-list"></i> List of Produce</h1>
								<div class="card-tools mt-n1">
									<span class='badge bg-orange text-white' data-toggle="modal" data-target="#modalProduceInfo" role="button"><i class="fas fa-plus"></i> Add Custom Produce</span>
									<!-- <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button> -->
								</div>
							</div>
							<div class="card-body p-2" style="overflow: auto;">
								<table id="tblProduceInfo" class="table table-sm table-striped table-hover">
									<thead>
										<tr>
											<!-- <th width="1">#</th> -->
											<th width="50">Image</th>
											<th width="50">Produce</th>
											<th width="1">Class</th>
											<th width="50">Seasonal</th>
											<th width="1">Active</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<div class="col-lg-6 col-12 mt-5">
						<div class="card">
							<div class="card-header pb-0">
								<h1 class="card-title"><i class="fa fa-list"></i> List of Farms</h1>
								<div class="card-tools mt-n1">
									<span class='badge bg-primary' data-toggle="modal" data-target="#modalFarmInfo" role="button"><i class="fas fa-plus"></i> Add Farm</span>
									<!-- <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-remove"></i></button> -->
								</div>
							</div>
							<div class="card-body p-2">
								<table id="tblFarmInfo" class="table table-sm table-striped table-hover">
									<thead>
										<tr>
											<th width="50">Image</th>
											<th width="50">Name</th>
											<th width="80">Location</th>
											<th width="1">Area</th>
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
		getTable(f1, 0, 5);
		saveForm(f1, [f1], null, 0, 5);


		let f2 = "ProduceInfo";
		getTable(f2, 0, 5);
		saveForm(f2, [f2], null, 0, 5);
		let f3 = "FarmProduceInfo";
		getTable(f3, 0, 5);
		// saveForm(f3, [f3], null, 0, 5);
		
		saveForm("FarmProduceSupply", ["FarmProduceInfo"], null, 0, 5);
		saveForm("AddFarmProduceSupply", ["FarmProduceInfo"], null, 0, 5);
		saveForm("UpdateProfile", [null], null);

		
	});
</script>