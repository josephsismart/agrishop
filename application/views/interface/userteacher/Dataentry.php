<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>
<?php
if (!$this->session->schoolmis_login_level) {
	redirect(base_url('login'));
}
$uri = 'userteacher'; //$this->session->schoolmis_login_uri;
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
			<div class="col-lg-3 col-md-4 col-sm-12 col-xs-12 form_save_dataSectionList">
				<!-- <div class="card card-navy collapsed-card"> -->
				<div class="card card-navy">
					<div class="card-header">
						<h3 class="card-title"><i class="fa fa-puzzle-piece"></i> List of Sections</h3>
						<div class="card-tools">
							<!-- <button type="button" class="btn btn-tool collapseAssignedSectionList" data-card-widget="collapse"><i class="fas fa-plus"></i> -->
							<!-- <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
							</button> -->
						</div>
					</div>
					<!-- /.card-header -->
					<div class="card-body p-0 table-responsive">

						<div class="row">
							<div class="col-12 px-3 pt-2">
								<div class="card card-default collapse-header collapsed-card border border-info">
									<div class="card-header px-2 py-1" role="button" data-card-widget="collapse">
										<h3 class="card-title text-primary"><i class="fa fa-filter"></i> Filter</h3>
										<div class="card-tools">
											<button type="button" class="btn btn-tool text-primary" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
										</div>
									</div>
									<!-- /.card-header -->
									<div class="card-body p-0">
										<div class="row p-1" id="form_save_dataSectionListFilter">
											<div class="col-6 pt-1">
												<div class="input-group">
													<div class="input-group-prepend">
														<small class="input-group-text text-xs text-bold p-1 bg-primary text-white">K 12</small>
													</div>
													<select class="form-control form-control-sm selectK12List" data-placeholder="K 12" onchange="getTable('AssignedSectionList', 0, 10);getFetchList('SectionListFilter', 'GradeLevelList', 'PartyList', 0, {v: $('#form_save_dataSectionListFilter .selectK12List').val()}, 0);"></select>
												</div>
											</div>
											<div class="col-6 pt-1">
												<div class="input-group">
													<div class="input-group-prepend">
														<small class="input-group-text text-xs text-bold p-1 bg-primary text-white">GLVL</small>
													</div>
													<select class="form-control form-control-sm selectGradeLevelList" data-placeholder="GRADE LEVEL" onchange="getTable('AssignedSectionList', 0, 10);"></select>
												</div>
											</div>
											<div class="col-12 pt-1">
												<div class="input-group">
													<div class="input-group-prepend">
														<small class="input-group-text text-xs text-bold p-1 bg-primary text-white">PRGRM/STRND</small>
													</div>
													<select class="form-control form-control-sm selectStrandList" data-placeholder="PROGRAM/STRAND" onchange="getTable('AssignedSectionList', 0, 10);"></select>
												</div>
											</div>
											<div class="col-12 pt-1 mt-3 ">
												<div class="input-group pt-3 border-top">
													<button class="btn btn-xs btn-warning w-100" onclick="getTable('LearnersListSearch', 0, 10);$('#modalLearnerSearch').modal('show');"><i class="fa fa-search"></i> SEARCH LEARNER</button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<table class="table table-sm table-hover table-striped" id="tblAssignedSectionList" width="100%">
							<thead>
								<tr>
									<th></th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
					<!-- /.card-body -->
				</div>
			</div>

			<div class="col-lg-9 col-md-8 col-sm-12 col-xs-12 form_save_dataPersonnelInfo">
				<div class="row">
					<div class="col-md-12">
						<div class="card card-navy">
							<div class="card-header">
								<h3 class="card-title"><i class="fa fa-puzzle-piece"></i> Grade Section Details</h3>
								<!-- <div class="card-tools">
									<button type="button" class="btn btn-tool collapseAssignedSectionList" data-card-widget="collapse"><i class="fas fa-plus"></i>
									</button>
								</div> -->
							</div> <!-- /.card -->

							<!-- <table class="" style="border:1px solid black;" border="1" id="tblGradesSMEAList" width="100%">
								<thead>
									<tr>
										<th rowspan="2" class="th-color" width="1">No</th>
										<th rowspan="2" class="th-color" width="1">Lrn</th>
										<th rowspan="2" class="th-color">Name</th>
										<th colspan="4">Grades Entry Data</th>
									</tr>
									<tr>
										<th class="th-color">Q1</th>
										<th class="th-color">Q2</th>
										<th class="th-color">Q3</th>
										<th class="th-color">Q4</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table> -->
							<!-- /.card-header -->
							<div class="card-body p-2">
								<div class="row">
									<div class="col-12 col-lg-6 col-md-12 col-sm-12 col-xs-12">
										<div class="post">
											<div class="user-block">
												<span class="img_path_personnel">
												</span>
												<span class="username">
													<a href="#" class="personnel">-</a>
												</span>
												<span class="description">-</span>
											</div>
										</div>
									</div>
									<div class="col-12 col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-2">
										<span class="others">
										</span>
										<!-- <span class="downloads">
										</span> -->
										<span class="reports">
										</span>
										<span class="forms">
										</span>
									</div>
									<div class="col-12">
										<div class="list-group">
											<div class="list-group-item">
												<div class="row">
													<!-- <div class="col-lg-4 col-md-12">
													</div> -->

													<div class="col-12 col-lg-12 col-md-12 col-sm-12">
														<!-- <div class="col-lg-4 col-md-4"> -->
														<div class="row">
															<div class="col-12">
																<div class="text-center">
																	<strong>No of learners</strong> <br><span class="small">as of EOSY</span>
																</div>
																<div class="text-center" style="font-size:42px;">
																	<span class="total_enrollee">-</span>
																</div>

																<div class="row">
																	<div class="col-6">
																		<span class="small text-muted float-right">Male</span><br />
																		<span class="float-right male">
																			<span class="male">-</span>
																		</span>
																	</div>
																	<div class="col-6">
																		<span class="small text-muted float-left">Female</span><br>
																		<span class="float-left">
																			<span class="female">-</span>
																		</span>
																	</div>
																</div>
															</div>
														</div>
													</div>

													<div class="col-12 col-xl-5 col-lg-6 col-md-12 col-sm-12 small header1" style="display:none;overflow:auto">
														<!-- <table class="table table-sm" id="tblHonors" width="30">
															<thead>
																<tr>
																	<th width="150">Honors</th>
																	<th>Q1</th>
																	<th>Q2</th>
																	<th>Q3</th>
																	<th>Q4</th>
																	<th>FG</th>
																</tr>
															</thead>
															<tbody>
															</tbody>
															<tfoot>
																<tr>
																	<td colspan="6">
																		<form id="form_report_dataGRADE_SLIP">
																			<div class="input-group input-group-sm">
																				<div class="input-group-prepend">
																					<small class="input-group-text text-xs text-bold">QRTR</small>
																				</div>
																				<select class="select2 form-control form-control-xs selectSchedList" multiple data-placeholder="QUARTER" name="qrtr" id="qrtr" style="width: 60%;">
																					<option value="1">1ST</option>
																					<option value="2">2ND</option>
																					<option value="3">3RD</option>
																					<option value="4">4TH</option>
																				</select>
																				<div class="input-group-append text-sm">
																					<button type="button" class="btn btn-warning btn-xs submitBtnGRADE_SLIP text-white"><i class="fa fa-search"></i></button>
																				</div>
																			</div>
																			<div class="input-group input-group-sm mt-2 w-100">
																				<button type="button" class="btn btn-info btn-xs submitBtnPreviewID text-white"><i class="fa fa-user"></i> Preview ID</button>
																			</div>
																		</form>
																	</td>
																</tr>
															</tfoot>
														</table> -->
													</div>

													<div class="col-lg-4 col-md-4 small table-responsive" hidden>
														<table class="table table-sm">
															<thead>
																<tr>
																	<th style="width:100px">
																		&nbsp;
																	</th>
																	<th class="text-right">
																		Male
																	</th>
																	<th class="text-right">
																		Female
																	</th>
																	<th class="text-right">
																		Total
																	</th>
																</tr>
															</thead>
															<tbody>

																<tr>
																	<td>
																		Transfer-in
																	</td>
																	<td class="text-right">
																		2
																	</td>
																	<td class="text-right">
																		2
																	</td>
																	<td class="text-right">
																		4
																	</td>
																</tr>

																<tr>
																	<td>
																		Balik-aral
																	</td>
																	<td class="text-right">
																		0
																	</td>
																	<td class="text-right">
																		0
																	</td>
																	<td class="text-right">
																		0
																	</td>
																</tr>


																<tr>
																	<td>
																		Repeater
																	</td>
																	<td class="text-right">
																		8
																	</td>
																	<td class="text-right">
																		3
																	</td>
																	<td class="text-right">
																		11
																	</td>
																</tr>



															</tbody>
														</table>
													</div>

													<div class="col-lg-4 col-md-4 small table-responsive" hidden>
														<table class="table table-sm">
															<thead>
																<tr>
																	<th style="width:100px">
																		&nbsp;
																	</th>
																	<th class="text-right">
																		Male
																	</th>
																	<th class="text-right">
																		Female
																	</th>
																	<th class="text-right">
																		Total
																	</th>
																</tr>
															</thead>
															<tbody>

																<tr>
																	<td>
																		<abbr title="Conditional Cash Transfer">CCT</abbr> Recipient

																	</td>
																	<td class="text-right">
																		0
																	</td>
																	<td class="text-right">
																		0
																	</td>
																	<td class="text-right">
																		0
																	</td>
																</tr>

																<tr>
																	<td>
																		<abbr title="Arabic Language and Islamic Values Education">ALIVE</abbr>
																	</td>
																	<td class="text-right">
																		0
																	</td>
																	<td class="text-right">
																		0
																	</td>
																	<td class="text-right">
																		0
																	</td>
																</tr>

																<tr>
																	<td>
																		<abbr title="Alternative Delivery Mode">ADM</abbr>
																	</td>
																	<td class="text-right">
																		0
																	</td>
																	<td class="text-right">
																		0
																	</td>
																	<td class="text-right">
																		0
																	</td>
																</tr>
															</tbody>
														</table>

													</div>
												</div>
											</div>

										</div>
									</div>
									<!-- /.user-block -->

									<!-- <p>
										<a href="#" class="link-black text-sm mr-2"><i class="fas fa-share mr-1"></i> Share</a>
										<a href="#" class="link-black text-sm"><i class="far fa-thumbs-up mr-1"></i> Like</a>
										<span class="float-right">
											<a href="#" class="link-black text-sm">
												<i class="far fa-comments mr-1"></i> Comments (5)
											</a>
										</span>
									</p> -->

									<!-- <input class="form-control form-control-sm" type="text" placeholder="Type a comment"> -->
									<div class="col-12 mt-4">

										<div class="card">
											<div class="card-header">
												<h4 class="card-title w-100 my-n2 ml-n2">
													List of Learners
													<i class="fa fa-plus fa-xs text-success bg-success p-1 ViewButtonsToggle" onclick="$('.viewButtons').slideToggle();logsHideShow();" role="button"></i>
													<row class="viewButtons" style="display:none">

														<i class="fa fa-ellipsis-h text-white bg-navy p-1" data-toggle="dropdown" aria-expanded="true" role="button"></i>
														<div class="dropdown-menu p-0" role="menu">
															<a class='dropdown-item bg-info py-2' onclick="PreviewID();" role='button'><span class='fa fa-address-card'></span> PREVIEW ID</a>
															<a class='dropdown-item bg-warning py-2' onclick="BatchTransfer();" role='button'><span class='fa fa-right-left'></span> TRANSFER</a>
															<a class='dropdown-item bg-danger py-2' onclick="BatchUnenroll();" role='button'><span class='fa fa-xmark'></span> UNENROLL</a>
														</div>
													</row>
												</h4>
											</div>
											<form id="formLearnersList">
												<div class="card-body p-1 table-responsive">
													<table class="table table-sm table-hover table-striped" id="tblLearnersList" width="100%">
														<thead>
															<tr>
																<th width="1"></th>
																<th width="1">
																	<div class="normal_view">LRN</div>
																	<div class="custom-control custom-checkbox logs_account" style="display:none;">
																		<input style="cursor:pointer" class="custom-control-input" id="learnerCheckBox" onclick="check_all('learnerCheckBox')" type="checkbox" />
																		<label style="cursor:pointer" for="learnerCheckBox" class="custom-control-label">Select all</label>
																	</div>
																</th>
																<th>Personal Details</th>
																<th>Sex</th>
																<th>Birthdate</th>
																<th>Address Details</th>
																<th>Status</th>
															</tr>
														</thead>
														<tbody>
														</tbody>
													</table>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
							<!-- /.card-body -->

							<!-- <div class="overlay LearnersList">
								<i class="fas fa-spin text-navy fa-5x fa-circle-notch"></i>
							</div> -->
						</div>
						<!-- /.card -->
					</div>
					<!-- /.col -->
				</div>
				<!-- /.row -->
				<!-- END ACCORDION & CAROUSEL-->
			</div>
		</div>
	</div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<script type="text/javascript">
	$(function() {
		let f1 = "EnrollmentInfo";
		let f2 = "GradeSecInfo";
		let f3 = "UpdateLearnerInfo";
		getTable("AssignedSectionList", 0, 10);
		getTable("LearnersList", 0, -1);
		
		// getTable("Honors", 1, -1);
		// getTable("SearchEnrollLearnersList", 0, 10, "", 1);
		// getFetchList('TransferConfirm', "BarangayList", null, 1, {
		// 	v: 160201
		// }, 0, 1);
		getFetchList(f1, "BarangayList", null, 1, {
			v: 160201
		}, 0, 1);
		getFetchList(f1, "CityMunList", null, 1, {
			v: 1602
		}, 1, 1);
		getFetchList(f3, "BarangayList", null, 1, {
			v: 160201
		}, 0, 1);
		getFetchList(f3, "LearnerStatus", "StatusList", 0, {
			v: 4
		}, 1);
		getFetchList(f1, "LearnerStatus", "StatusList", 0, {
			v: 4
		}, 1);
		getFetchList('SectionListFilter', "K12List", "PartyTypeList", 0, {
			v: 5
		}, 0);
		getFetchList('SectionListFilter', "StrandList", "PartyList", 0, {
			v: 22
		}, 0);


		getFetchList('SectionListFilterTransfer', "K12List", "PartyTypeList", 0, {
			v: 5
		}, 0);
		// getFetchList(f1, "LearnerMT", "PartyList", 1, {
		// 	v: 6
		// }, 1, 0, "MOTHER TONGUE");
		// getFetchList(f1, "LearnerEG", "PartyList", 1, {
		// 	v: 21
		// }, 1, 0, "ETHNIC GROUP");

		saveForm(f1, ["LearnersList"], null, 1, -1);
		saveForm("GradesList", [null], null, 1, -1);
		saveForm("GradesPSList", [null], null, 1, -1);
		saveForm(f3, [null], null, 1, -1);
	});
</script>