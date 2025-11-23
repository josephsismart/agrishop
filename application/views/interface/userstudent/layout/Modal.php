<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>
<?php
if (!$this->session->schoolmis_login_level) {
    redirect(base_url('login'));
}
$uri = $this->session->schoolmis_login_uri;
?>
<!-- <div class="modal fade show" id="modalEnrollment" aria-modal="true" style="padding-right: 16px; display: block;"> -->
<div class="modal fade" id="modalEnrollment">
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
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-four-search-tab" data-toggle="pill" href="#custom-tabs-four-search" role="tab" aria-controls="custom-tabs-four-search" aria-selected="true">Search</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" id="custom-tabs-four-profile-tab" data-toggle="pill" href="#custom-tabs-four-new" role="tab" aria-controls="custom-tabs-four-new" aria-selected="false">New</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-four-messages-tab" data-toggle="pill" href="#custom-tabs-four-import" role="tab" aria-controls="custom-tabs-four-import" aria-selected="false">Import</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="custom-tabs-four-tabContent">
                            <div class="tab-pane fade" id="custom-tabs-four-search" role="tabpanel" aria-labelledby="custom-tabs-four-search-tab">
                                <div class="row">
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
                                </div>

                                <form id="formSearchEnrollLearnersList">
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
                                </form>

                            </div>
                            <div class="tab-pane fade active show" id="custom-tabs-four-new" role="tabpanel" aria-labelledby="custom-tabs-four-new-tab">
                                <div class="row">
                                    <?= form_open(base_url($uri . '/Dataentry/saveEnrollmentInfo'), 'id=form_save_dataEnrollmentInfo'); ?>
                                    <input type="" name="personId" hidden nr="1">
                                    <!-- /.card-header -->
                                    <div class="card-body p-0">
                                        <!-- <label>Basic Information</label> -->
                                        <h6>Basic Information Details</h6>
                                        <div class="row">
                                            <div class="col-lg-3 col-md-12 col-sm-12">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text firstName text-bold text-success">LRN</span>
                                                    </div>
                                                    <input type="text" class="form-control text-uppercase" name="firstName" placeholder="LEARNER'S REFERENCE NUMBER (LRN)" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-12 col-sm-12">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text firstName text-primary"><i class="fas fa-user"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control text-uppercase" name="firstName" placeholder="FIRST NAME" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-md-12 col-sm-12 mb-2">
                                                <input type="text" class="form-control text-uppercase" name="middleName" placeholder="MIDDLE NAME" autocomplete="off" nr="1">
                                            </div>
                                            <div class="col-lg-3 col-md-12 col-sm-12 mb-2">
                                                <input type="text" class="form-control text-uppercase" name="lastName" placeholder="LAST NAME" autocomplete="off">
                                            </div>
                                            <div class="col-lg-1 col-md-12 col-sm-12 mb-2">
                                                <input type="text" class="form-control text-uppercase" name="extName" placeholder="EXTN" autocomplete="off" nr="1">
                                            </div>
                                            <div class="col-lg-2 col-md-12 col-sm-12">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                                                    </div>
                                                    <select class="form-control" name="sex">
                                                        <option value="1">MALE</option>
                                                        <option value="0">FEMALE</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-12 col-sm-12">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text birthdate"><i class="fas fa-birthday-cake"></i></span>
                                                    </div>
                                                    <input type="date" class="form-control" name="birthdate" value="<?= date('Y-m-d'); ?>">
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-12 col-sm-12">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                                    </div>
                                                    <select class="form-control selectCityMunList" onchange="getLocation('CityMunList','BarangayList','PersonnelInfo')" type="select" name="cty">
                                                    </select>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text"><i class="fas fa-edit text-primary"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-md-12 col-sm-12">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text brgy"><i class="fas fa-map-marker-alt"></i></span>
                                                    </div>
                                                    <select class="form-control selectBarangayList" onchange="getLocation('BarangayList','PurokList','PersonnelInfo')" type="select" name="brgy">
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-md-12 col-sm-12">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                                    </div>
                                                    <select class="form-control selectPurokList" type="select" name="prk" nr="1">
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text homeAddress"><i class="fas fa-home"></i></span>
                                                    </div>
                                                    <input type="text" class="form-control text-uppercase" name="homeAddress" placeholder="ADDRESS DETAILS" autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text emailAddress"><i class="fas fa-envelope"></i></span>
                                                    </div>
                                                    <input type="email" class="form-control" name="emailAddress" placeholder="EMAIL ADDRESS" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <h6 class="mt-2">Employment Information Details</h6>
                                        <div class="row">
                                            <div class="col-lg-4 col-md-12 col-sm-12">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                                    </div>
                                                    <select class="form-control selectEmpList" name="emptype" onchange="getFetchList('PersonnelInfo', 'PtitleList', 'PartyList', 0, {v: $('.selectEmpList').val()}, 1);">
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-12 col-sm-12">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                                    </div>
                                                    <select class="form-control selectPtitleList" name="personaltitle">
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-12 col-sm-12">
                                                <div class="input-group mb-2">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                                    </div>
                                                    <select class="form-control selectEStatusList" name="empstatus">
                                                    </select>
                                                </div>
                                            </div>
                                        </div> -->
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="col-12 mt-2">
                                        <button type="button" class="btn btn-default float-right" onclick="clear_form('form_save_dataPersonnelInfo')"><i class="fa fa-times"></i> Cancel</button>
                                        <button type="submit" class="btn btn-info submitBtnPrimary float-right mr-2">Save Data</button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="custom-tabs-four-import" role="tabpanel" aria-labelledby="custom-tabs-four-import-tab">
                                Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus tristique. Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna.
                            </div>
                        </div>
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
<div class="modal fade" id="modalGrades">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary p-2 px-3">
                <h5 class="modal-title p-0">
                    Grades entry form SY: | <b><?= $getOnLoad["sy"]; ?></b> Q: <b><?= $getOnLoad["qrtr"]; ?></b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open(base_url($uri . '/Dataentry/saveGradesList'), 'id=form_save_dataGradesList'); ?>
            <div class="modal-body mb-n3">
                <div class="card-body p-0 table-responsive mt-3">
                    <table class="table table-sm table-hover table-striped" id="tblGradesList" width="100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>LRN</th>
                                <th>Personal Details</th>
                                <th>Sex</th>
                                <th>Status</th>
                                <th>Grades</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-info submitBtnPrimary" onclick="$('#modalGrades').modal('hide');">Save Grades</button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
            </form>
        </div>

        <div class="tab-pane fade" id="custom-tabs-four-import" role="tabpanel" aria-labelledby="custom-tabs-four-import-tab">
            Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus tristique. Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna.
        </div>
    </div>
</div>
<!-- /.card -->