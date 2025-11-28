<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>
<?php
if (!$this->session->agrishop_login_level) {
    redirect(base_url('login'));
}
$uri = $this->session->agrishop_login_uri;
?>
<div class="modal fade show" id="modalFarmInfo" data-backdrop="static" style="padding-right: 15px; display: block;" aria-modal="true" role="dialog">
<!-- <div class="modal fade" id="modalFarmInfo" data-backdrop="static"> -->
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success p-2">
                <h5 class="modal-title p-0 mt-n3 mb-n3">
                    <!-- <label>XII - DURIAN</label> -->
                    <small class="text-white"> <i class="fas fa-tractor"></i> Farm Information</small>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open(base_url($uri . '/Farms/saveFarmInfo'), 'id=form_save_dataFarmInfo'); ?>
            <input name="personId" hidden nr="1">

            <!-- /.card-header -->
            <div class="card-body p-2">
                <!-- <label>Basic Information</label> -->
                <h6>Basic Information</h6>
                <div class="row p-2 mt-n3 mb-n2">
                    <div class="col-xl-2 col-md-12">
                        <center class="mt-3">
                            <div class="form-group">
                                <img name="previewPic" src="<?= base_url("dist/img/media/icons/1x1.png"); ?>" onclick="$('[name=pic]').trigger('click')" width="120" height="120" class="border border-white border-2 rounded elevation-2" type="button" alt="User Image">
                            </div>
                            
                            <div class="form-group">
                                <input name="pic" type="file" accept="image/*" onchange="imageView('pic','previewPic','imgtargetLink')" nr="1" hidden />
                                <!-- <input name="personId" type="text" nr="1" > -->
                                <input name="img_path" type="text" nr="1" hidden />
                            </div>
                        </center>
                    </div>
                    <div class="col-xl-10 col-md-12 mt-4 pl-xl-3">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                <div class="input-group mb-2 border border-white border-1 rounded elevation-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-tractor"></i></span>
                                    </div>
                                    <input type="text" class="form-control form-control-sm text-uppercase" name="farmName" placeholder="FARM NAME" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-6 mb-2">
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">SQM</span>
                                    </div>
                                    <input type="text" class="form-control form-control-sm text-uppercase" name="totalAreaSqm" placeholder="AREA" autocomplete="off" nr="1">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6 col-6 mb-2">
                                <input type="text" class="form-control form-control-sm text-uppercase" name="lastName" placeholder="LAST NAME" autocomplete="off">
                            </div>
                            <div class="col-lg-2 col-md-6 col-sm-6 col-6 col-6">
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-venus-mars"></i></span>
                                    </div>
                                    <select class="form-control form-control-sm" name="sex">
                                        <option value="t">MALE</option>
                                        <option value="f">FEMALE</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-12 col-sm-12 region_province" >
                                <div class="input-group mb-2">
                                    <select class="form-control selectRegionList" style="width:100%;" onchange="getLocation(['RegionList','ProvinceList','CityMunList','BarangayList'],
																														['ProvinceList','CityMunList','BarangayList','PurokList'],'FarmInfo');" type="select" name="region">
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-12 col-sm-12 region_province" >
                                <div class="input-group mb-2">
                                    <select class="form-control selectProvinceList" style="width:100%;" onchange="getLocation(['ProvinceList','CityMunList','BarangayList'],
																														['CityMunList','BarangayList','PurokList'],'FarmInfo')" type="select" name="province">
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
														<small class="input-group-text text-xs text-bold p-1">CITY</small>
													</div>
                                    <select class="form-control selectCityMunList" style="width:100%" onchange="getLocation(['CityMunList','BarangayList'],
																														['BarangayList','PurokList'],'FarmInfo')" type="select" name="cty">
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                                <div class="input-group mb-2">
                                    <!-- <div class="input-group-prepend">
														<small class="input-group-text text-xs text-bold p-1">BRGY</small>
													</div> -->
                                    <!-- <select class="form-control selectBarangayList" style="width:100%" onchange="getLocation(['BarangayList'],['PurokList'],'FarmInfo')" type="select" name="brgy">
                                    </select> -->
                                </div>
                            </div>
                            <div class="col-9 col-lg-10 address_details">
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text homeAddress"><i class="fas fa-home"></i></span>
                                    </div>
                                    <input type="text" class="form-control form-control-sm text-uppercase" name="homeAddress" placeholder="ADDRESS DETAILS" autocomplete="off" nr="1">
                                </div>
                            </div>
                            <div class="col-3 col-lg-2 address_details">
                                <div class="input-group mb-2">
                                    <select class="form-control form-control-sm" name="is_active">
                                        <option value="1">ACTIVE</option>
                                        <option value="0">INACTIVE</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h6 class="mt-2">Employment Information Details</h6>
                <div class="row">
                    <div class="col-6 col-lg-3 col-md-6 col-sm-6">
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                            </div>
                            <input type="text" class="form-control form-control-sm text-uppercase" name="employeeID" placeholder="Employee ID" autocomplete="off" nr="1">
                        </div>
                    </div>
                    <!-- <div class="col-6 col-lg-3 col-md-6 col-sm-6">
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                            </div>
                            <select class="form-control form-control-sm selectEmpList" name="emptype" onchange="getFetchList('FarmInfo', 'PtitleList', 'PartyList', 0, {v: $('.selectEmpList').val()}, 1);">
                            </select>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6 col-sm-6">
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                            </div>
                            <select class="form-control form-control-sm selectPtitleList" name="personaltitle">
                            </select>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6 col-sm-6">
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                            </div>
                            <select class="form-control form-control-sm selectEStatusList" name="empstatus">
                            </select>
                        </div>
                    </div> -->
                </div>

            </div>
            <!-- /.card-body -->
            <div class="card-footer p-1 pr-2 pl-2">
                <button type="submit" class="btn btn-primary btn-xs submitBtnPrimary">Save Data</button>
                <button type="button" class="btn btn-gray btn-xs clearBtn" onclick="clear_form('FarmInfo')">Clear</button>
            </div>
            <!-- /.card -->
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>