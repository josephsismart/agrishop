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
				<h5><i class="nav-icon fas fa-seedling"></i> On Production</h5>
			</div>
		</div>
	</div><!-- /.container-fluid -->
</section>

<section class="content">
	<div class="container-fluid">
		<div class="row">

			<div class="col-12 form_save_dataFarmInfo">
				<div class="row">
					<div class="col-12">
						<div class="card">

							<div class="col-lg-4 col-md-4 col-sm-6 col-8">

							</div>
							<div class="card-header bg-success">
								<h1 class="card-title"><i class="fa fa-list"></i> On Production</h1>
								<div class="card-tools mt-n1">
									<button class="btn btn-default" onclick="$('#modalSearchProduction').modal('show')">
										<i class="fas fa-search"></i> Search Other Production
									</button>

									<span class='btn bg-navy' onclick="$('#modalProductionInfo').modal('show')">
										<i class="fas fa-plus"></i> Add Production
									</span>
								</div>
							</div>
							<div class="card-body p-2" style="overflow: auto;">
								<div class="mb-2 text-primary">
									<i class="fas fa-info-circle"></i> Kindly <b>click the image</b> to update the status.
								</div>
								<table id="tblProductionInfo" class="table table-sm table-striped table-hover">
									<thead>
										<tr>
											<th width="1"></th>
											<th width="60">Image</th>
											<th width="1">Produce and Variety</th>
											<th width="120">Expected</th>
											<th width="1">Plant Date</th>
											<th width="1">Area</th>
											<th width="1">Mrkt Price</th>
											<th width="80">Farm & Location</th>
										</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="col-12">
						<div class="card">

							<div class="col-lg-4 col-md-4 col-sm-6 col-8">

							</div>
							<div class="card-header">
								<h1 class="card-title"><i class="fa fa-check"></i> Production History</h1>
								<div class="card-tools mt-n1">
								</div>
							</div>
							<div class="card-body p-2" style="overflow: auto;">
								<div class="mb-2 text-primary">
									<i class="fas fa-info-circle"></i> Kindly <b>click the image</b> to update the status.
								</div>
								<table id="tblProductionInfoCompleted" class="table table-sm table-striped table-hover">
									<thead>
										<tr>
											<th width="1"></th>
											<th width="60">Image</th>
											<th width="1">Produce and Variety</th>
											<th width="120">Expected</th>
											<th width="1">Plant Date</th>
											<th width="1">Area</th>
											<th width="1">Mrkt Price</th>
											<th width="80">Farm & Location</th>
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
		let f1 = "ProductionInfo";
		let f2 = "ProductionInfoCompleted";
		getTable(f1, 0, 5);
		getTable(f2, 0, 5);
		saveForm(f1, [f1], null, 0, 5);


		// let f2 = "ProductionInfo";
		// getTable(f2, 0, 5);
		// saveForm(f2, [f2], null, 0, 5);
		// let f3 = "FarmProductionInfo";
		// getTable(f3, 0, 5);
		// // saveForm(f3, [f3], null, 0, 5);

		// saveForm("FarmProduceSupply", ["FarmProductionInfo"], null, 0, 5);
		// saveForm("AddFarmProduceSupply", ["FarmProductionInfo"], null, 0, 5);
		// saveForm("UpdateProfile", [null], null);
		$('#viewSwitch').change(function() {

			if ($(this).is(':checked')) {
				$('#mapView').addClass('d-none');
				$('#tableView').removeClass('d-none');
			} else {
				$('#tableView').addClass('d-none');
				$('#mapView').removeClass('d-none');
			}

		});

		function handleProduceCategory(category) {

			if (category == "perennial") {
				$('#lastHarvestWrapper').slideDown();
			} else {
				$('#lastHarvestWrapper').slideUp();
			}

		}

		$('#form_save_dataProductionInfo .produceList').on('click', 'li', function() {

			let produce_id = $(this).data('id');

			$('#form_save_dataProductionInfo input[name=produce_id]').val(produce_id);

			$('#form_save_dataProductionInfo .produceInput').val($(this).text());

			$('#form_save_dataProductionInfo .produceList').hide();

			/* SAVE PRODUCE INFO */

			$('#ProductionInfo')
				.data('days_to_harvest', $(this).data('days_to_harvest'))
				.data('category', $(this).data('category'))
				.data('harvest_frequency', $(this).data('harvest_frequency'))
				.data('yield_per_sqm_as_kg', $(this).data('yield_per_sqm_as_kg')); // NEW


			if ($(this).data('category') == 'Perennial') {
				$('#lastHarvestWrapper').show();
			} else {
				$('#lastHarvestWrapper').hide();
			}

			computeProductionPrediction();

		});

	});


	function computeProductionPrediction() {

		let produce_id = $('#form_save_dataProductionInfo input[name=produce_id]').val();

		let days_to_harvest = parseInt($('#form_save_dataProductionInfo #ProductionInfo').data('days_to_harvest'));
		let category = $('#form_save_dataProductionInfo #ProductionInfo').data('category');
		let harvest_frequency = parseInt($('#form_save_dataProductionInfo #ProductionInfo').data('harvest_frequency'));
		let yield_per_sqm = parseFloat($('#form_save_dataProductionInfo #ProductionInfo').data('yield_per_sqm_as_kg')) || 0;

		let planted_date = $('#form_save_dataProductionInfo input[name=planted_date]').val();
		let last_harvest = $('#form_save_dataProductionInfo input[name=last_harvest_date]').val();
		let area = parseFloat($('#form_save_dataProductionInfo input[name=area_sqm]').val()) || 0;
		let price = parseFloat($('#form_save_dataProductionInfo input[name=market_price_per_kg]').val()) || 0;

		if (!planted_date) return;

		let harvestDate;

		if (category == 'perennial' && last_harvest) {

			harvestDate = new Date(last_harvest);
			harvestDate.setDate(harvestDate.getDate() + harvest_frequency);

		} else {

			harvestDate = new Date(planted_date);
			harvestDate.setDate(harvestDate.getDate() + days_to_harvest);

		}

		let expectedHarvestDate = harvestDate.toISOString().split('T')[0];


		/* YIELD CALCULATION FROM DATABASE */

		let expected_yield = area * yield_per_sqm;

		let expected_volume = expected_yield;

		let expected_revenue = expected_volume * price;

		let expected_yield_format = '~ ' + expected_yield.toFixed(2).toLocaleString() + ' kg';
		let expected_revenue_format = '~ ₱' + expected_revenue.toLocaleString('en-US', {
			minimumFractionDigits: 2,
			maximumFractionDigits: 2
		});

		$('#typicalHarvestDays').text(days_to_harvest);
		$('#yieldPerSqm').text(yield_per_sqm + ' kg');
		$('#areaPlanted').text(area + ' sqm');
		/* DISPLAY RESULT */

		$('#expected_harvest_date').text('~ ' + expectedHarvestDate);
		$('#form_save_dataProductionInfo input[name=expected_harvest_date]').val(expectedHarvestDate);
		$('#expected_yield').text(expected_yield_format);
		$('#form_save_dataProductionInfo input[name=expected_yield]').val(expected_yield_format);
		$('#expected_revenue').text(expected_revenue_format);
		$('#form_save_dataProductionInfo input[name=expected_revenue]').val(expected_revenue_format);

		$('#predictionPanel').fadeIn();
	}

	function updateProductionStatus() {
		$.post("<?= base_url('userfarmer/OnProduction/updateProductionStatus') ?>", {
			production_id: production_id_,
			status: $('#productionStatus').val(),
			note: $('#modalProductionStatus #note').val()
		}, function(res) {
			let j = JSON.parse(res);
			if (j.success == true) {
				successAlert(j.message);
				getTable('ProductionInfo', 0, 5);
				getTable('ProductionInfoCompleted', 0, 5);
				$("#modalProductionStatus").modal("hide");

			} else {
				errorAlert(j.message);
			}
		});
	}
</script>