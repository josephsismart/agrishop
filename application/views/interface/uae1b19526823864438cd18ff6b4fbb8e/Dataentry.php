<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>
<?php
if (!$this->session->schoolmis_login_level) {
	redirect(base_url('login'));
}
$this->db->query('REFRESH MATERIALIZED VIEW profile.view_gete_pass;');
$uri = $this->session->schoolmis_login_uri;
?>
<section class="content-header">
	<div class="container-fluid">
		<div class="row mt-2">
			<div class="col-sm-6">
			</div>

		</div>
	</div>
</section>

<section class="content">
	<div class="container-fluid">
		<form id="form_save_dataGateSearch">
			<div class="row">
				<div class="col-1 col-lg-2 col-xl-3 col-sm-1 col-xs-12"></div>
				<div class="col-10 col-lg-8 col-xl-6 col-sm-10 col-xs-12">
					<div class="callout callout-info get_selection">
						<h5>Gate Assignment:</h5>
						<div class="input-group input-group-lg mb-2" style="width: 100%;">
							<select class="form-control form-control-lg selectGateList" placeholder="GATE" name="gate_select" id="gate_select">
							</select>

							<div class="input-group-append">
								<button type="button" class="btn btn-success btn-lg submitBtnGRADE_SLIP text-white text-lg text-bold" onclick="vdetails();"><i class="fa fa-paper-plane"></i> SUBMIT</button>
							</div>
						</div>
						<p><i>Please select the "<b>GATE</b> assignment" option to initiate the scanning process for ID.</i></p>

					</div>
				</div>
				<div class="col-1 col-lg-2 col-xl-3 col-sm-1 col-xs-12"></div>
			</div>

			<div class="row">
				<div class="col-1 col-xs-0 col-sm-0"></div>
				<div class="col-12 col-xs-12 col-sm-12">
					<div class="card card-navy view_details" style="display:none;">
						<div class="card-header">
							<h1 class="card-title header text-lg text-bold"><i class="fa fa-card"></i> SELECT GATE</h1>
							<input type="text" id="qr" hidden />
							<div class="float-right">
								<button type="button" class="btn btn-default btn-xs gateSelect" onclick="vdetails();"><i class="fas fa-door-open"></i> Gate Selection</button>
								<button type="button" class="btn btn-default btn-xs gateSelect" onclick="vdetailszzz();"><i class="fas fa-door-open"></i> test</button>
								<button type="button" class="btn btn-default btn-xs gateSelect" onclick="vdetailszzz1();"><i class="fas fa-door-open"></i> test</button>
								
							</div>
							<div class="card-tools">
							</div>
						</div>
						<div class="card-body scanner_announcement">
							<!-- SCANNING -->
							<div class="card-body text-center scanning">
								<dl>
									<div class="row">
										<div class="col-4 col-lg-4 col-sm-4">
											<div class="image">
												<img name="previewPic" src="<?= base_url("dist/img/media/icons/1x1.png") ?>" style="border:5% solid #63a4ca;cursor:pointer; width:95%; height:27vw;" class="rounded elevation-4 img-fluid" alt="User Image">
											</div>
										</div>
										<div class="col-8 col-lg-8 col-sm-8 text-left">

											<dt style="font-size: 7.1vw;" class="mt-lg-n4 mt-xl-n4 mt-n4 mb-n2 mb-lg-n5 text-lg-lg mb-md-n4 mb-sm-n4 mb-n3" id="lname">-</dt>
											<dd style="font-size: 5.5vw;" class="mb-n2 mb-lg-n4 text-lg-lg mb-md-n2 mb-sm-n2 mb-xs-n4" id="fname_mname">-</dd>
											<dt style="font-size: 5vw;" class="mt-2 mt-xl-4 mt-lg-4 mb-n2 mb-lg-n4 text-lg-lg mb-md-n2 mb-sm-n2 mb-xs-n4" id="assignment">-</dt>
											<dd style="font-size: 5vw;" class="mt-2 text-no-wrap mb-n2 mb-lg-n4 text-lg-lg mb-md-n2 mb-sm-n2 mb-xs-n4" id="io_time"></dd>
										</div>
									</div>

								</dl>
							</div>

							<!-- NEWS/UPDATES/ANNOUNCEMENTS -->
							<div class="content announcements">
								<div class="container">
									<div class="bd-example mb-4">
										<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
											<ol class="carousel-indicators">
												<li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
												<li data-target="#carouselExampleIndicators" data-slide-to="1" class=""></li>
												<li data-target="#carouselExampleIndicators" data-slide-to="2" class=""></li>
											</ol>
											<div class="carousel-inner">
												<div class="carousel-item active">
													<img class="d-block w-100" src="<?= base_url() ?>dist/img/banners/b0_2024.jpg" alt="First slide">
													<!-- <div class="carousel-caption d-none d-md-block">
														<h5>SANHAY Moving Forward</h5>
														<h6>ANHS envisions to produce competent learners who are educationally, technologically, artistically equipped, God-fearing and law-abiding citizens of the country.</h6>
													</div> -->
												</div>
												<div class="carousel-item">
													<img class="d-block w-100" src="<?= base_url() ?>dist/img/banners/b1_2024.jpg" alt="Second slide">
												</div>
												<div class="carousel-item">
													<img class="d-block w-100" src="<?= base_url() ?>dist/img/banners/b2_2024.jpg" alt="Second slide">
												</div>
											</div>
											<a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
												<span class="carousel-control-custom-icon" aria-hidden="true">
													<i class="fas fa-chevron-left"></i>
												</span>
												<span class="sr-only">Previous</span>
											</a>
											<a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
												<span class="carousel-control-custom-icon" aria-hidden="true">
													<i class="fas fa-chevron-right"></i>
												</span>
												<span class="sr-only">Next</span>
											</a>
										</div>
									</div>
								</div><!-- /.container-fluid -->
							</div>

						</div>
					</div>
				</div>
				<div class="col-1 col-xs-0 col-sm-0"></div>
			</div>
		</form>
	</div>
</section>

<script type="text/javascript">
	$(function() {
		let f1 = "GateSearch";
		let intervalId = "";

		$('.carousel').carousel({
            interval: 4100,
        });

        $(window).scroll(function() {
            if ($(this).scrollTop() > 50) {
                $('#back-to-top').fadeIn();
            } else {
                $('#back-to-top').fadeOut();
            }
        });
        // scroll body to 0px on click
        $('#back-to-top').click(function() {
            $('body,html').animate({
                scrollTop: 0
            }, 400);
            return false;
        });

		
		getFetchList(f1, "GateList", "PartyList", 0, {
			v: 20
		}, 0);

		$("document").ready(function() {
			var barcode = "";

			// Listen for keydown event on a specific element or the document
			$(document).on('keydown', function(event) {
				var key = event.key;

				// Check if key is a valid barcode character
				if (key.length === 1 && /^[0-9a-zA-Z]$/.test(key)) {
					barcode += key;
				}
			});

			// Listen for keyup event on a specific element or the document
			$(document).on('keyup', function(event) {
				var key = event.key;

				// Check if key is Enter (carriage return)
				if (key === "Enter") {
					// Barcode scanning is complete
					// Retrieve the scanned value
					var scannedValue = barcode;

					// Do something with the scanned value
					getQRPerson({
						v: scannedValue,
						g_id: $("#gate_select").val(),
						g_nm: $("#gate_select option:selected").text()
					}, f1);

					// Reset the barcode variable for the next scan
					barcode = "";
				}
			});
		});

	});
</script>