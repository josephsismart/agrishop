<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>
<?php
if (!$this->session->schoolmis_login_level) {
    redirect(base_url('login'));
}
$uri = 'userteacher'; //$this->session->schoolmis_login_uri;
?>

<div class="modal" id="loadingModal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <h4>Loading...</h4>
            </div>
        </div>
    </div>
</div>

<!-- <div class="modal fade show" id="modalEnrollment" aria-modal="true" style="padding-right: 16px; display: block;"> -->
<div class="modal fade" id="modalEnrollment" data-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary p-2 px-3">
                <h5 class="modal-title p-0">
                    Enrollment entry form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mb-n3">
                <div class="card card-primary card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                            <!-- <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-four-search-tab" data-toggle="pill" href="#custom-tabs-four-search" role="tab" aria-controls="custom-tabs-four-search" aria-selected="true">Search</a>
                            </li> -->
                            <li class="nav-item">
                                <a class="nav-link active" id="custom-tabs-four-profile-tab" data-toggle="pill" href="#custom-tabs-four-new" role="tab" aria-controls="custom-tabs-four-new" aria-selected="false">New</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-four-messages-tab" data-toggle="pill" href="#custom-tabs-four-import" role="tab" aria-controls="custom-tabs-four-import" aria-selected="false">Import SF1</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="custom-tabs-four-tabContent">
                            <!-- <div class="tab-pane fade" id="custom-tabs-four-search" role="tabpanel" aria-labelledby="custom-tabs-four-search-tab"> -->
                            <!-- <div class="row">
                                    <div class="col-5">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">by:</span>
                                            </div>
                                            <select name="searchby" id="searchby" class="form-control">
                                                <option value="1">LRN</option>
                                                <option value="2">LAST NAME</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-7">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="keyword" placeholder="INPUT KEYWORD...">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-warning" onclick="tblReload('SearchEnrollLearnersList')"><i class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->

                            <!-- <form id="formSearchEnrollLearnersList">
                                    <div class="card-body p-0 table-responsive mt-3">
                                        <table class="table table-sm table-hover table-striped" id="tblSearchEnrollLearnersList" width="100%">
                                            <thead>
                                                <tr>
                                                    <th width="1">#</th>
                                                    <th width="1">LRN</th>
                                                    <th>Personal Details</th>
                                                    <th>Sex</th>
                                                    <th>Birthdate</th>
                                                    <th>Enrolled</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </form> -->

                            <!-- </div> -->
                            <div class="tab-pane fade active show" id="custom-tabs-four-new" role="tabpanel" aria-labelledby="custom-tabs-four-new-tab">
                                <div class="row mb-n3">
                                    <?= form_open(base_url($uri . '/Dataentry/saveEnrollmentInfo'), 'id=form_save_dataEnrollmentInfo'); ?>
                                    <?php $this->load->view('interface/' . $uri . '/forms/LearnerDetails') ?>
                                    </form>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="custom-tabs-four-import" role="tabpanel" aria-labelledby="custom-tabs-four-import-tab">
                                <div class="overlay-wrapper">
                                    <form method="post" id="import_form" enctype="multipart/form-data">
                                        <div class="overlay text-navy" style="display:none;"><i class="fas fa-3x fa-sync-alt fa-spin-puls fa-spin text-navy"></i>
                                            <div class="text-bold pt-2">Loading...</div>
                                        </div>
                                        <p><button type="submit" name="import" class="btn btn-xs btn-info submitBtnUpload"><i class="fa fa-upload"></i> Import SF1 Excel</button>
                                            <!-- <input type="file" name="file" id="file" required accept=".xlsm" /> -->
                                            <input type="file" name="file" id="file" required accept=".xls" />
                                        <div class="form-check">
                                            <input type="text" class="form-check-input" name="batch_update" id="batch_update" hidden />
                                            <input type="checkbox" onchange="$('#batch_update').val($('#batch_update').val()==''?'a':'');" class="form-check-input" name="batch_update_box" id="batch_update_box" />
                                            <label class="form-check-label" for="batch_update_box">Update only
                                                <span class="badge" data-toggle="tooltip" data-html="true" title="<em>Purpose:</em> By selecting this checkbox, you will have the ability to <b>UPDATE</b> the <b>LEARNERS' INFORMATION</b> only.">
                                                    <i class="fa fa-question-circle"></i>
                                                </span>
                                            </label>

                                        </div>
                                        </p>
                                    </form>
                                </div>
                                <!--
                                <div id="progressBar">
                                    <div id="progress" style="width: 0%;">0%</div>
                                </div> -->
                                <!-- Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus tristique. Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna. -->
                            </div>
                        </div>
                    </div>
                    <!-- <div class="card-footer">
                        <button type="submit" class="btn btn-info submitBtnPrimary">Save Data</button>
                        <button type="button" class="btn btn-default" onclick="clear_form('form_save_dataVariantCategory')"><i class="fa fa-times"></i> Cancel</button>
                    </div> -->
                </div>
                <!-- /.card -->
            </div>
            <div class="card-body p-0" style="overflow: auto;">
            </div>
        </div>
    </div>
    <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->

<!-- <div class="modal fade show" id="modalEnrollment" aria-modal="true" style="padding-right: 16px; display: block;"> -->
<div class="modal fade" id="modalLearnerSearch" data-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-gradient-warning p-2 px-3">
                <h5 class="modal-title p-0">
                    <i class="fa fa-search"></i> Search Learner
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mb-n3">
                <div class="card card-primary card-outline card-outline-tabs">
                    <div class="card-body p-1 table-responsive">
                        <table class="table table-sm table-hover table-striped" id="tblLearnersListSearch" width="100%">
                            <thead>
                                <tr>
                                    <th width="1"></th>
                                    <th width="1">LRN</th>
                                    <th>Personal Details</th>
                                    <th width="1">Section</th>
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
                </div>
                <!-- /.card -->
            </div>
            <div class="card-body p-0" style="overflow: auto;">
            </div>
        </div>
    </div>
    <!-- /.modal-content -->
</div>
<!-- /.modal-dialog -->


<!-- /.modal -->
<!-- <div class="modal fade show" id="modal-default" aria-modal="true" style="padding-right: 16px; display: block;"> -->
<div class="modal fade" id="modalGradesList" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary p-2 px-3">
                <h5 class="modal-title p-0 text-md">
                    Grades Entry form - SY: <b><?= $getOnLoad["sy"]; ?> | </b> Q: <b><?= $getOnLoad["qrtr"]; ?></b>
                </h5>
                <!-- <div class='radioBtn btn-group pull-right'>
                    <button type="button" class="btn bg-navy btn-xs"><i class="fa fa-upload"></i> Upload Grades Data</button>
                    <button type="button" class="btn btn-xs btn-default" data-dismiss="modal"><i class="fa fa-times"></i></button>
                </div> -->
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="card card-default collapsed-card m-1">
                <!-- <div class="card"> -->
                <div class="card-header cursor-pointer p-2">
                    <div class="row">
                        <div class="col-8">
                            <h3 class="card-title" data-card-widget="collapse" role="button">
                                <i class="fas fa-upload"></i>
                                <u>Upload Grades Data (<i>Click to toggle</i>)</u>
                            </h3>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-xs btn-warning float-right text-white downloadform" onclick="getGradesSMEAListFN()">
                                <i class="fas fa-download"></i>
                                Download Form
                            </button>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body collapse p-1" style="display:none;">
                    <!-- <div class="card-body collapse"> -->
                    <form method="post" enctype="multipart/form-data" id="uploadGrades">
                        <div class="row">
                            <div class="col-6">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="customFile" required accept=".xlsx">
                                    <label class="custom-file-label customFile" for="customFile">Choose file</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-primary" onclick="uploadFile('uploadGrades','customFile','tblGradesPSList')"><i class="fa fa-upload"></i> Upload File</button>
                                <!-- <button type="button" class="btn btn-primary" onclick="uploadFile('uploadGradesPS','customFilePS','tblGradesPSList')"><i class="fa fa-upload"></i> Upload File</button> -->
                            </div>

                            <!-- <input type="file" id="fileUpload" />
                            <input type="button" id="upload" value="Upload" />
                            <hr /> -->
                            <!-- <div id="dvExcel"></div> -->


                        </div>
                    </form>
                </div>
            </div>
            <!-- /.card-body -->

            <div class="modal-body mt-n2 form_save_dataGradesList p-1">

                <div class="card card-default card-tabs">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link bg-navy active" id="custom-tabs-one-grade-tab" data-toggle="pill" href="#custom-tabs-one-grade" role="tab" aria-controls="custom-tabs-one-grade" aria-selected="true">Quarter Grades</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link bg-pink" id="custom-tabs-one-ps-tab" data-toggle="pill" href="#custom-tabs-one-ps" role="tab" aria-controls="custom-tabs-one-ps" aria-selected="false">Quarter Exam/PS</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="custom-tabs-one-tabContent">
                            <div class="tab-pane fade active show" id="custom-tabs-one-grade" role="tabpanel" aria-labelledby="custom-tabs-one-grade-tab">

                                <?= form_open(base_url($uri . '/Dataentry/saveGradesList'), 'id=form_save_dataGradesList'); ?>
                                <div class="row">
                                    <div class="col-md-3 col-sm-6 col-xs-6 p-1"><span class="q1c">-</span></div>
                                    <div class="col-md-3 col-sm-6 col-xs-6 p-1"><span class="q2c">-</span></div>
                                    <div class="col-md-3 col-sm-6 col-xs-6 p-1"><span class="q3c">-</span></div>
                                    <div class="col-md-3 col-sm-6 col-xs-6 p-1"><span class="q4c">-</span></div>
                                </div>

                                <div class="card-body p-0 table-responsive mt-3">
                                    <table class="table-striped table-hover table-bordered" cellspacing="0" id="tblGradesList" width="100%">
                                        <thead width="100%">
                                            <tr style="text-align:center;">
                                                <th align="left">Student</th>
                                                <th width="1">Q1</th>
                                                <th width="1">Q2</th>
                                                <th width="1">Q3</th>
                                                <th width="1">Q4</th>
                                                <th width="1">AVG</th>
                                            </tr>
                                        </thead>
                                        <tbody style="text-align:center">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer p-0 content">
                                    <button type="submit" class="btn btn-info submitBtnPrimary">Save Grades</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                                </div>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="custom-tabs-one-ps" role="tabpanel" aria-labelledby="custom-tabs-one-ps-tab">
                                <?= form_open(base_url($uri . '/Dataentry/saveGradesPSList'), 'id=form_save_dataGradesPSList'); ?>
                                <div class="card-body p-0 table-responsive">
                                    <table class="table-striped table-hover table-bordered" cellspacing="0" id="tblGradesPSList" width="100%">
                                        <thead width="100%">
                                            <tr style="text-align:center;">
                                                <th align="left">Student</th>
                                                <th width="1">Q1</th>
                                                <th width="1">Q2</th>
                                                <th width="1">Q3</th>
                                                <th width="1">Q4</th>
                                                <th width="1">AVG</th>
                                            </tr>
                                        </thead>
                                        <tbody style="text-align:center" class="content">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer p-0 content">
                                    <button type="submit" class="btn btn-info submitBtnPrimary">Save Grades</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>

            </div>
            <!-- <div class="modal-footer content">
                <button type="submit" class="btn btn-info submitBtnPrimary">Save Grades</button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div> -->
            <div class="overlay">
                <i class="fas fa-spin text-white fa-3x fa-circle-notch"></i>
            </div>
        </div>

        <div class="tab-pane fade" id="custom-tabs-four-import" role="tabpanel" aria-labelledby="custom-tabs-four-import-tab">
            Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus tristique. Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna.
        </div>
    </div>
</div>


<!-- /.modal -->
<!-- <div class="modal fade show" id="modal-default" aria-modal="true" style="padding-right: 16px; display: block;"> -->
<div class="modal fade" id="modalGradesPSList" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-pink p-2 px-3">
                <h5 class="modal-title p-0 text-md">
                    Periodical Exam / Percentage Score form - SY: <b><?= $getOnLoad["sy"]; ?> | </b> Q: <b><?= $getOnLoad["qrtr"]; ?></b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="card card-default collapsed-card m-1">
                <!-- <div class="card"> -->
                <div class="card-header cursor-pointer p-2" data-card-widget="collapse" role="button">
                    <h3 class="card-title">
                        <i class="fas fa-upload"></i>
                        <u>Upload Grades Data (<i>Click to toggle</i>)</u>
                    </h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body collapse p-1" style="display:none;">
                    <!-- <div class="card-body collapse"> -->
                    <form method="post" enctype="multipart/form-data" id="uploadGradesPS">
                        <div class="row">
                            <div class="col-6">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="customFilePS" required accept=".xlsx">
                                    <label class="custom-file-label" for="customFilePS">Choose file</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-primary" onclick="uploadFile('uploadGradesPS','customFilePS','tblGradesPSList')"><i class="fa fa-upload"></i> Upload File</button>
                            </div>

                            <!-- <input type="file" id="fileUpload" />
                            <input type="button" id="upload" value="Upload" />
                            <hr /> -->
                            <!-- <div id="dvExcel"></div> -->


                        </div>
                    </form>
                </div>
            </div>
            <!-- /.card-body -->



        </div>

        <div class="tab-pane fade" id="custom-tabs-four-import" role="tabpanel" aria-labelledby="custom-tabs-four-import-tab">
            Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus tristique. Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna.
        </div>
    </div>
</div>


<div class="modal fade" id="modalAllGrades" data-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-gradient-success p-2 px-3">
                <h5 class="modal-title p-0">
                    List of Grades - SY: <b><?= $getOnLoad["sy"]; ?> | </b> Q: <b><?= $getOnLoad["qrtr"]; ?></b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mb-n3">
                <div class="card card-navy p-0 table-responsive viewAllGrades">
                </div>
            </div>
            <div class="overlay">
                <i class="fas fa-spin text-white fa-3x fa-circle-notch"></i>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalAllStudentLogs" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-default p-2 px-3">
                <h5 class="modal-title p-0">
                    Learner Logs form - SY: <b><?= $getOnLoad["sy"]; ?> | </b> Q: <b><?= $getOnLoad["qrtr"]; ?></b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mb-n2">
                <!-- <div class="card card-navy p-0 table-responsive viewAllGrades">
                </div> -->

                <div class="card-body p-0 table-responsive mt-3" id="printAllStudentLogs">
                    <table class="table table-sm table-hover table-striped text-xs p-0" id="tblAllStudentLogs" width="100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>LRN & Full name</th>
                                <th>Date & Time</th>
                                <th>Action</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <div class="tab-pane fade" id="custom-tabs-four-import" role="tabpanel" aria-labelledby="custom-tabs-four-import-tab">
            Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus tristique. Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna.
        </div>
    </div>
</div>
<!-- /.card -->

<!-- <div class="modal fade show" id="modalLearnersList" aria-modal="true" style="padding-right: 16px; display: block;"> -->
<div class="modal fade" id="modalLearnersList" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-gradient-navy p-2">
                <h5 class="modal-title p-0"><span class="fa fa-cog"></span> Account Settings </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body p-1 mt-n2 mb-n2">
                    <select class="form-control" name="accountSettings">
                        <option value="create">CREATE ACCOUNT</option>
                        <option value="reset">RESET PASSWORD</option>
                        <option value="disable">DISABLE ACCOUNT</option>
                        <option value="enable">ENABLE ACCOUNT</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer p-1">
                <button type="submit" class="btn btn-info btn-xs submitBtnPrimary" onclick="batchUpdateAccount('LearnersList');">Save Changes</button>
                <!-- <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><i class="fa fa-times"></i> Close</button> -->
            </div>

        </div>
    </div>
</div>

<!-- <div class="modal fade show" id="modalLearnersList" aria-modal="true" style="padding-right: 16px; display: block;"> -->
<div class="modal fade" id="modalLearnersUnenroll" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <form id="form_save_dataUnenrollConfirm">
            <div class="modal-content">
                <div class="modal-header bg-danger p-2">
                    <h5 class="modal-title p-0"><span class="fa fa-trash-alt"></span> Unenroll Confirmation </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5><strong class='lrn'></strong></h5>
                    <input name="details" hidden />
                    <p class="lead">
                        <strong class="last_fullname"></strong>
                    </p>
                    <input type="password" name="password" class="form-control passwordUnenroll submitBtnPrimary" placeholder="Enter Password" />

                    <!-- <div class="alert alert-warning alert-dismissible mt-3 mb-0 pr-3">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-info"></i> Please read!</h5>
                        Inputted <b>GRADES</b> will be <b>DELETED</b> also.
                    </div> -->
                </div>
                <div class="modal-footer p-1">
                    <button type="button" class="btn btn-danger btn-xs btn-block submitBtnPrimary" onclick="unenroll();">Unenroll Student</button>
                    <!-- <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><i class="fa fa-times"></i> Close</button> -->
                </div>
            </div>
        </form>
    </div>
</div>


<!-- <div class="modal fade show" id="modalLearnersList" aria-modal="true" style="padding-right: 16px; display: block;"> -->
<div class="modal fade" id="modalLearnersBatchUnenroll" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <form id="form_save_dataBatchUnenrollConfirm">
            <div class="modal-content">
                <div class="modal-header bg-danger p-2">
                    <h5 class="modal-title p-0"><span class="fa fa-xmark"></span> Batch Unenroll</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="listBatchUnenroll mt-n2 mb-2">
                    </div>

                    <input type="password" name="password" class="form-control passwordUnenroll submitBtnPrimary" placeholder="Enter Password" />

                    <!-- <div class="alert alert-warning alert-dismissible mt-3 mb-0 pr-3">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-info"></i> Please read!</h5>
                        Inputted <b>GRADES</b> will be <b>DELETED</b> also.
                    </div> -->
                </div>
                <div class="modal-footer p-1">
                    <button type="button" class="btn btn-danger btn-xs btn-block submitBtnPrimary" onclick="BatchUnenroll();">Unenroll Learners</button>
                    <!-- <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><i class="fa fa-times"></i> Close</button> -->
                </div>
            </div>
        </form>
    </div>
</div>

<!-- <div class="modal fade show" id="modalLearnersBatchTransfer" aria-modal="true" style="padding-right: 16px; display: block;"> -->
<div class="modal fade" id="modalLearnersBatchTransfer" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <form id="form_save_dataBatchTransferConfirm">
            <div class="modal-content">
                <div class="modal-header bg-warning p-2">
                    <h5 class="modal-title p-0"><span class="fa fa-right-left"></span> Batch Transfer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="listBatchTransfer mt-n2 mb-2">
                    </div>

                    <div class="row pb-1" id="form_save_dataSectionListFilterTransfer">
                        <div class="col-12 pt-1">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <small class="input-group-text text-xs text-bold p-1 bg-primary text-white">K 12</small>
                                </div>
                                <select class="form-control form-control-sm selectK12List" data-placeholder="K 12" onchange="getFetchList('SectionListFilterTransfer', 'GradeLevelList', 'PartyList', 0, {v: $('#form_save_dataSectionListFilterTransfer .selectK12List').val()}, 0);"></select>
                            </div>
                        </div>
                        <div class="col-12 pt-1">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <small class="input-group-text text-xs text-bold p-1 bg-primary text-white">GRADE LEVEL</small>
                                </div>
                                <select class="form-control form-control-sm selectGradeLevelList" data-placeholder="GRADE LEVEL" onchange="sectionList();"></select>
                            </div>
                        </div>
                        <div class="col-12 pt-1">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <small class="input-group-text text-xs text-bold p-1 bg-primary text-white w-30">SECTION</small>
                                </div>
                                <select class="form-control form-control-sm select2 sectionListTransfer" style="width: 75%;" data-placeholder="SELECT SECTION" onchange=""></select>
                            </div>
                        </div>
                    </div>

                    <input type="password" name="password" class="form-control passwordTransfer submitBtnPrimary" placeholder="Enter Password" />

                    <!-- <div class="alert alert-warning alert-dismissible mt-3 mb-0 pr-3">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-info"></i> Please read!</h5>
                        Inputted <b>GRADES</b> will be <b>DELETED</b> also.
                    </div> -->
                </div>
                <div class="modal-footer p-1">
                    <button type="button" class="btn btn-warning btn-xs btn-block submitBtnPrimary" onclick="BatchTransfer();">Trasfer Learners</button>
                    <!-- <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><i class="fa fa-times"></i> Close</button> -->
                </div>
            </div>
        </form>
    </div>
</div>

<!-- <div class="modal fade show" id="modalLearnersList" aria-modal="true" style="padding-right: 16px; display: block;"> -->
<!-- <div class="modal fade" id="modalLearnersTransfer" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <form id="form_save_dataTransferConfirm">
            <div class="modal-content">
                <div class="modal-header bg-info p-2">
                    <h5 class="modal-title p-0"><span class="fa fa-trash-alt"></span> Transfer Confirmation </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5><strong class='lrn'></strong></h5>
                    <input name="details" hidden />
                    <p class="lead">
                        <strong class="last_fullname"></strong>
                    </p>

                    <div class="input-group mb-2">
                        <select class="form-control form-control-sm select2 selectBarangayList" data-placeholder="SELECT BARANGAY" onchange="getLocation('BarangayList','PurokList','PersonnelInfo')" type="select" name="brgy" style="width:100%;">
                        </select>
                    </div>
                    <input type="password" name="password" class="form-control passwordTransfer submitBtnPrimary" placeholder="Enter Password" />

                    <div class="alert alert-warning alert-dismissible mt-3 mb-0 pr-3">
                        <h5><i class="icon fas fa-info"></i> Please read!</h5>
                        Inputted <b>GRADES</b> will <b>REMAIN</b>.
                    </div>
                </div>
                <div class="modal-footer p-1">
                    <button type="button" class="btn btn-info btn-xs btn-block submitBtnPrimary" onclick="transfer();">Transfer Student</button>
                </div>
            </div>
        </form>
    </div>
</div> -->

<!-- <div class="modal fade show" id="modalLearnersList" aria-modal="true" style="padding-right: 16px; display: block;"> -->
<div class="modal fade mt-5" id="modalLearnersSubmitGrades" data-backdrop="static">
    <div class="modal-dialog modal-sm">
        <form id="form_save_dataSubmitGradesConfirm">
            <div class="modal-content">
                <div class="modal-header bg-success p-2">
                    <h5 class="modal-title p-0"><span class="fa fa-paper-plane"></span> Submit Confirmation<br />
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <center class="mb-3"><b>NOTE:</b> <i> You will <b>NOT</b> be able to UPDATE the grades after <b>SUBMISSION/APPROVAL</b>.</i></center>

                    <!-- <small><b class="detail">9 - MERCURY</b> JHS ARAL PAN | WD | <b>Q1 - 100%</b></small> -->
                    <input type="text" name="qrssa" id="qrssa" hidden />
                    <textarea class="form-control form-control-sm text-uppercase mb-2 remarks" name="remarks" rows="3" placeholder="REMARKS" nr="1"></textarea>
                    <!-- <input type="password" name="password" class="form-control passwordSubmitGrades submitBtnPrimary" placeholder="Enter Password" /> -->
                </div>
                <div class="modal-footer p-1">
                    <button type="button" class="btn btn-success btn-xs btn-block submitBtnPrimary" onclick="submitGrades();">Submit Student Grades</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- <div class="modal fade show" id="modalLearnersList" aria-modal="true" style="padding-right: 16px; display: block;"> -->
<div class="modal fade" id="modalUpdateLearnerInfo" data-backdrop="static">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary p-2">
                <h5 class="modal-title p-0"><span class="fa fa-pencil-alt"></span> Update Learner Information </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-2 mb-n3">

                <div id="accordion">
                    <div class="card card-white">
                        <div class="card-header p-2">
                            <h4 class="card-title w-100">
                                <!-- <a class="d-block w-100" data-toggle="collapse" href="#collapseOne" aria-expanded="true"> -->
                                <a class="d-block w-100" data-toggle="#" href="#collapseOne" aria-expanded="true">
                                    <span class="fa fa-user"></span> Basic Information Details
                                </a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="collapse show" data-parent="#accordion">
                            <?= form_open(base_url($uri . '/Dataentry/saveEnrollmentInfo'), 'id=form_save_dataUpdateLearnerInfo'); ?>
                            </form>
                        </div>
                    </div>
                    <!-- <div class="card card-white">
                        <div class="card-header p-2">
                            <h4 class="card-title w-100">
                                <a class="d-block w-100 collapsed" data-toggle="collapse" href="#collapseTwo" aria-expanded="false">
                                    <span class="fa fa-user-friends"></span> Parent/Guardian Information
                                </a>
                            </h4>
                        </div>
                        <div id="collapseTwo" class="collapse" data-parent="#accordion">
                            <div class="card-body p-3">
                                Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid.
                                3
                                wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt
                                laborum
                                eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee
                                nulla
                                assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred
                                nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft
                                beer
                                farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus
                                labore sustainable VHS.
                            </div>
                        </div>
                    </div>
                    <div class="card card-white">
                        <div class="card-header p-2">
                            <h4 class="card-title w-100">
                                <a class="d-block w-100" data-toggle="collapse" href="#collapseThree">
                                    <span class="fa fa-info"></span> Other Information
                                </a>
                            </h4>
                        </div>
                        <div id="collapseThree" class="collapse" data-parent="#accordion">
                            <div class="card-body p-3">
                                Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid.
                                3
                                wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt
                                laborum
                                eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee
                                nulla
                                assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred
                                nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft
                                beer
                                farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus
                                labore sustainable VHS.
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHonor" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-navy py-1">
                <h5 class="modal-title p-0"><span class="fa fa-medal"></span> Honors List
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-1">
                <!-- <table class="table table-sm tblHonorsList"> -->
                <table class="table table-striped table-bordered table-sm" cellspacing="0" id="tblHonorsList" width="100%">
                    <thead>
                        <tr>
                            <!-- <th width="1">#</th> -->
                            <th>#   Learner</th>
                            <th>
                                <center>AVG</center>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>