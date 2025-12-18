<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>
<?php
if (!$this->session->agrishop_login_level) {
    redirect(base_url('login'));
}
$uri = $this->session->agrishop_login_uri;
?>
<!-- <div class="modal fade show" id="modalFarmInfo" data-backdrop="static" style="padding-right: 15px; display: block;" aria-modal="true" role="dialog"> -->
<div class="modal fade" id="modalFarmInfo" data-backdrop="static">
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
            <?= form_open(base_url($uri . '/FarmProduce/saveFarmInfo'), 'id=form_save_dataFarmInfo'); ?>
            <input name="personId" hidden nr="1">

            <!-- /.card-header -->
            <div class="card-body p-2">
                <!-- <label>Basic Information</label> -->
                <div class="row p-2 mt-n3 mb-n3 pb-0">
                    <div class="col-xl-2 col-md-3 col-sm-4 col-xs-12">

                        <center class="mt-3">
                            <div class="form-group">
                                <label class="col-form-label">Picture</label><br />
                                <img name="previewPicFarm" src="<?= base_url("dist/img/media/icons/1x1.png"); ?>" onclick="$('[name=picFarm]').trigger('click')" width="120" height="120" class="border border-white border-2 rounded elevation-1" type="button" alt="User Image">
                            </div>

                            <div class="form-group">
                                <input name="picFarm" type="file" accept="image/*" onchange="imageView('picFarm','previewPicFarm','imgtargetLink')" nr="1" hidden />
                            </div>
                        </center>
                    </div>

                    <div class="col-xl-10 col-md-9 col-sm-8 col-xs-12 pl-xl-3">
                        <div class="row">
                            <div class="col-lg-8 col-md-7 col-sm-7 col-7">
                                <div class="form-group">
                                    <label class="col-form-label"><i class="fas fa-tractor"></i> Farm Name</label>
                                    <input type="text" class="form-control border-primary text-uppercase" name="farmName" placeholder="FARM NAME" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-5 col-sm-5 col-5">
                                <div class="form-group">
                                    <label class="col-form-label">Area(sqm)</label>
                                    <input type="text" class="form-control border-primary" name="totalAreaSqm" placeholder="AREA" autocomplete="off" nr="1">
                                </div>
                            </div>


                            <div class="col-lg-8 col-md-6 col-sm-6 col-8">
                                <div class="form-group">
                                    <label class="col-form-label"><i class="fas fa-house"></i> Location</label>
                                    <input name="barangay" hidden>
                                    <input type="text" class="form-control border-primary barangayInput" name="barangay_" placeholder="TYPE BARANGAY (min 3 chars)" autocomplete="off">
                                </div>
                                <ul class="list-group barangayResults" style="position:absolute; z-index:9999; width:100%; display:none; cursor:pointer"></ul>

                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6 col-4">
                                <div class="form-group">
                                    <label class="col-form-label">Soil Type</label>
                                    <select style="width:100%" class="form-control text-uppercase select2" name="soil_type">
                                        <option value="">SOIL TYPE</option>
                                        <option value="ALLUVIAL SOIL">ALLUVIAL SOIL</option>
                                        <option value="CALICHE">CALICHE</option>
                                        <option value="CHALK">CHALK</option>
                                        <option value="CLAY">CLAY</option>
                                        <option value="CLAY LOAM">CLAY LOAM</option>
                                        <option value="GRAVEL">GRAVEL</option>
                                        <option value="LATERITE">LATERITE</option>
                                        <option value="LOAM">LOAM</option>
                                        <option value="MARL">MARL</option>
                                        <option value="ORGANIC SOIL">ORGANIC SOIL</option>
                                        <option value="PEAT">PEAT</option>
                                        <option value="SAND">SAND</option>
                                        <option value="SANDY CLAY">SANDY CLAY</option>
                                        <option value="SANDY LOAM">SANDY LOAM</option>
                                        <option value="SANDY SILT">SANDY SILT</option>
                                        <option value="SILT">SILT</option>
                                        <option value="SILT LOAM">SILT LOAM</option>
                                        <option value="SILTY CLAY">SILTY CLAY</option>
                                        <option value="SILTY LOAM">SILTY LOAM</option>
                                        <option value="VOLCANIC SOIL (ANDISOL)">VOLCANIC SOIL (ANDISOL)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-primary mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Location in map: </h3>
                        <div class="col-12"> <h6 class="text-blue" id="farmCoordinates"></h6>
                        </div>
                        <div class="card-tools">
                            <!-- <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button> -->
                        </div>
                        <!-- /.card-tools -->
                    </div>
                    <!-- /.card-header -->
                    <!-- <input id="farmCoordinates" name="coordinates" > -->
                    <input id="farmLat" name="lat" hidden>
                    <input id="farmLon" name="lon" hidden>
                    <div class="card-body" style="display: block; height: 250px;" id="map">
                        The body of the card
                    </div>
                    <!-- /.card-body -->
                </div>

                <!-- /.card-body -->
                <div class="card-footer p-1 pr-2 pl-2 mt-2">
                    <button type="submit" class="btn btn-primary btn-xs submitBtnPrimary">Save Data</button>
                    <button type="button" class="btn btn-gray btn-xs clearBtn" onclick="clear_form('FarmInfo')">Clear</button>
                </div>
                <!-- /.card -->
                </form>
            </div>
            <!-- /.modal-content -->

            <!-- /.card -->
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!-- <div class="modal fade show" id="modalProduceInfo" data-backdrop="static" style="padding-right: 15px; display: block;" aria-modal="true" role="dialog"> -->
<div class="modal fade" id="modalProduceInfo" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning p-2">
                <h5 class="modal-title p-0 mt-n3 mb-n3">
                    <!-- <label>XII - DURIAN</label> -->
                    <small> <i class="fas fa-carrot"></i> Produce Information</small>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?= form_open(base_url($uri . '/FarmProduce/saveProduceInfo'), 'id=form_save_dataProduceInfo'); ?>
            <input name="personId" hidden nr="1">

            <!-- /.card-header -->
            <div class="card-body p-2">
                <!-- <label>Basic Information</label> -->
                <div class="row p-2 mt-n3 mb-n3 pb-0">
                    <div class="col-xl-2 col-md-3 col-sm-4 col-xs-12">

                        <center class="mt-3">
                            <div class="form-group">
                                <label class="col-form-label">Picture</label><br />
                                <img name="previewPicProduce" src="<?= base_url("dist/img/media/icons/1x1.png"); ?>" onclick="$('[name=picProduce]').trigger('click')" width="120" height="120" class="border border-white border-2 rounded elevation-1" type="button" alt="User Image">
                            </div>

                            <div class="form-group">
                                <input name="picProduce" type="file" accept="image/*" onchange="imageView('picProduce','previewPicProduce','imgtargetLink')" nr="1" hidden />
                            </div>
                        </center>
                    </div>

                    <div class="col-xl-10 col-md-9 col-sm-8 col-xs-12 pl-xl-3">
                        <div class="row">
                            <div class="col-lg-8 col-md-7 col-sm-7 col-7">
                                <div class="form-group">
                                    <label class="col-form-label"><i class="fas fa-carrot"></i> PRODUCE NAME</label>
                                    <input type="text" class="form-control border-primary text-uppercase" name="produceName" placeholder="Produce Name">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-5 col-sm-5 col-5">
                                <div class="form-group">
                                    <label class="col-form-label">CLASSIFICATION</label>
                                    <select class="form-control border-primary text-uppercase" name="classification">
                                        <option value="">Select Classification</option>
                                        <option value="1">Vegetables</option>
                                        <option value="2">Fruits</option>
                                        <option value="3">Cereals/Grains</option>
                                        <option value="4">Root Crops/Tubers</option>
                                        <option value="5">Spices/Herbs</option>
                                        <option value="6">Legumes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-5 col-md-3 col-sm-3 col-4">
                                <div class="form-group">
                                    <label class="col-form-label"><i class="fas fa-info"></i> DESCRIPTION</label>
                                    <input type="text" class="form-control border-primary text-uppercase" name="description" placeholder="DESCRIPTION" nr="1">
                                </div>
                            </div>
                            <div class="col-lg-5 col-md-3 col-sm-3 col-4">
                                <div class="form-group">
                                    <label class="col-form-label"><i class="fas fa-tag"></i> TAGS</label>
                                    <input type="text" class="form-control border-primary text-uppercase" name="tags" placeholder="TAGS TO BE SEARCHED EASILY" nr="1">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-sm-6 col-4">
                                <div class="form-group">
                                    <label class="col-form-label"> </label>
                                    <div class="custom-control custom-checkbox mt-3">
                                        <input class="custom-control-input" type="checkbox" id="customCheckbox2" checked="" name="seasonal">
                                        <label for="customCheckbox2" class="custom-control-label">Seasonal</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer p-1 pr-2 pl-2 mt-2">
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
</div>

<!-- <div class="modal fade show" id="modalFarmProduceSupply" data-backdrop="static" style="padding-right: 15px; display: block;" aria-modal="true" role="dialog"> -->
<div class="modal fade" id="modalFarmProduceSupply" data-backdrop="static">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow rounded">

            <!-- HEADER -->
            <div class="modal-header bg-success text-white py-2">
                <h6 class="modal-title mb-0">
                    <i class="fas fa-boxes mr-1"></i> Add Supply
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <?= form_open(base_url($uri . '/FarmProduce/saveFarmProduce'), 'id="form_save_dataFarmProduceSupply"'); ?>

            <input name="farmId" hidden>
            <input name="produceSelectedId" hidden>

            <!-- BODY -->
            <div class="modal-body p-2">
                <!-- IMAGE -->
                <div class="text-center mb-2 produceImg">
                </div>
                <div class="input-group mb-3 position-relative">
                    <input type="text" name="produceSelected" class="form-control form-control-sm produceInput text-uppercase border-primary" placeholder="TYPE PRODUCE NAME..." autocomplete="off">

                    <!-- suggestion list -->
                    <ul class="list-group position-absolute w-100 shadow-sm produceList mt-5" style="z-index: 9999; max-height: 250px; overflow-y: auto; display: none;">
                    </ul>
                </div>

                <!-- hidden ID -->

                <!-- Supply Add Section -->
                <div class="bg-light rounded">
                    <div class="small text-muted font-weight-bold mb-1">Add Supply</div>

                    <div class="form-group mb-2">
                        <input type="number" class="form-control form-control-sm border-success" name="qty_add" min="1" value="1" placeholder="Quantity to Add">
                    </div>
                </div>
                <div class="bg-light rounded">
                    <div class="small text-muted font-weight-bold mb-1">Price</div>

                    <div class="form-group mb-2">
                        <input type="number" class="form-control form-control-sm border-success" name="price" min="1" value="1" placeholder="Price">
                    </div>
                </div>
                <div class="bg-light rounded pt-1">
                    <div class="small text-muted font-weight-bold mb-1">UoM</div>

                    <div class="form-group mb-2">
                        <select class="form-control form-control-sm border-success select2" name="uom">
                            <option value="KG">KG</option>
                            <option value="G">G</option>
                            <option value="LB">LB</option>
                            <option value="SACK">SACK</option>
                            <option value="BAG">BAG</option>
                            <!-- Count -->
                            <option value="PC">PC</option>
                            <option value="PACK">PACK</option>
                            <option value="BUNDLE">BUNDLE</option>
                            <option value="BUNCH">BUNCH</option>
                            <option value="CLUSTER">CLUSTER</option>
                            <option value="DOZEN">DOZEN</option>
                            <option value="TRAY">TRAY</option>
                            <option value="HEAD">HEAD</option>
                            <option value="STICK">STICK</option>
                            <!-- Volume -->
                            <option value="L">L</option>
                            <option value="ML">ML</option>
                        </select>
                    </div>
                </div>
                <div class="bg-light rounded pt-1">
                    <div class="small text-muted font-weight-bold mb-1">Harvest Date</div>

                    <div class="form-group mb-2">
                        <input type="date" class="form-control form-control-sm border-success" name="harvest_date" value="<?= Date('Y-m-d'); ?>">
                    </div>
                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer py-1 px-2">
                <button type="submit" class="btn btn-primary btn-sm px-3">
                    <i class="fas fa-save"></i> Save
                </button>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    Close
                </button>
            </div>

            </form>
        </div>
    </div>
</div>

<!-- <div class="modal fade show" id="modalFarmProduceSupply" data-backdrop="static" style="padding-right: 15px; display: block;" aria-modal="true" role="dialog"> -->
<div class="modal fade" id="modalAddFarmProduceSupply" data-backdrop="static">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow rounded">

            <!-- HEADER -->
            <div class="modal-header bg-success text-white py-2">
                <h6 class="modal-title mb-0">
                    <i class="fas fa-boxes mr-1"></i> Add Supply
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <?= form_open(base_url($uri . '/FarmProduce/saveAddFarmProduceSupply'), 'id="form_save_dataAddFarmProduceSupply"'); ?>

            <input type="hidden" name="fp_id">

            <!-- BODY -->
            <div class="modal-body p-2">

                <!-- IMAGE -->
                <div class="text-center mb-2">
                    <img name="previewPicProduce" src="<?= base_url('dist/img/media/icons/1x1.png') ?>" class="rounded border shadow-sm" width="100" height="100">
                </div>


                <!-- Supply Add Section -->
                <div class="p-2 bg-light rounded">
                    <div class="small text-muted font-weight-bold mb-1">Add Supply</div>

                    <div class="form-group mb-2">
                        <input type="number" class="form-control form-control-sm border-success" name="qty_add" min="1" placeholder="Quantity to Add">
                    </div>
                </div>
                <!-- Produce Details -->
                <div class="p-2 bg-light rounded mb-2">
                    <div class="small"><b>Name:</b> <span name="show_produceName"></span></div>
                    <div class="small"><b>Classification:</b> <span name="show_classification"></span></div>
                    <div class="small"><b>UoM:</b> <span name="show_uom"></span></div>
                    <div class="small"><b>Seasonal:</b> <span name="show_seasonal"></span></div>
                    <div class="small"><b>Stock Left:</b> <span name="show_qty_left"></span></div>
                    <div class="small"><b>Price:</b> <span name="price"></span></div>

                    <!-- hidden input counterparts -->
                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer py-1 px-2">
                <button type="submit" class="btn btn-primary btn-sm px-3">
                    <i class="fas fa-save"></i> Save
                </button>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    Close
                </button>
            </div>

            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xs">
        <div class="modal-content" style="border-radius:0;">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="profileModalLabel">My Profile</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?= form_open(base_url('/updateprofile'), 'id=form_save_dataUpdateProfile'); ?>
            <div class="modal-body">
                <div class="card-body">
                    <h6><i class="fas fa-camera-retro"></i> Photo</h6>
                    <div class="col-12">
                        <center class="p-0 border">
                            <div class="form-group">
                                <img name="previewPic" src="<?= $this->session->agrishop_login_img_path  ?>" onclick="$('[name=picProfile]').trigger('click')" width="120" height="120" class="border border-white border-2 rounded elevation-2" type="button" alt="User Image">
                            </div>
                            <div class="form-group">
                                <input name="picProfile" type="file" accept="image/*" onchange="imageView('picProfile','previewPic','imgtargetLink')" nr="1" hidden="">
                                <!-- <input name="personId" type="text" nr="1" > -->
                                <input name="img_path" type="text" nr="1" hidden="">
                            </div>
                        </center>
                    </div>
                    <div class="row">
                        <h6><i class="fas fa-user"></i> Name</h6>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-6">
                            <input type="text" class="form-control form-control-sm text-uppercase border-primary" value="<?= $this->session->agrishop_login_first_name ?>" name="firstName" placeholder="FIRST NAME" autocomplete="off">
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-6 mb-2">
                            <input type="text" class="form-control form-control-sm text-uppercase border-primary" value="<?= $this->session->agrishop_login_middle_name ?>" name="middleName" placeholder="MIDDLE NAME" autocomplete="off" nr="1">
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-6 mb-2">
                            <input type="text" class="form-control form-control-sm text-uppercase border-primary" value="<?= $this->session->agrishop_login_last_name ?>" name="lastName" placeholder="LAST NAME" autocomplete="off">
                        </div>
                        <div class="col-6">
                            <h6 class="mt-3"><i class="fas fa-birthday-cake"></i> Birthdate</h6>
                            <div class="col-12">
                                <input type="date" class="form-control form-control-sm border-primary" name="birthdate" nr="1" value="<?= $this->session->agrishop_login_birthdate ?>">
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="mt-3"><i class="fas fa-venus-mars"></i> Gender</h6>
                            <div class="col-12">
                                <select class="form-control form-control-sm border-primary" name="sex">
                                    <option value="t" <?= $this->session->agrishop_login_sex == 't' ? 'selected' : '' ?>>MALE</option>
                                    <option value="f" <?= $this->session->agrishop_login_sex == 'f' ? 'selected' : '' ?>>FEMALE</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="mt-3"><i class="fas fa-envelope"></i> Email</h6>
                            <div class="col-12">
                                <input type="email" class="form-control form-control-sm border-primary" placeholder="EMAIL" name="email" nr="1" value="<?= $this->session->agrishop_login_email_address ?>" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="mt-3"><i class="fas fa-phone"></i> Contact Number</h6>
                            <div class="col-12">
                                <input type="text" class="form-control form-control-sm border-primary text-uppercase" placeholder="Contact Number" name="contactNumber" nr="1" value="<?= $this->session->agrishop_login_contact_num ?>">
                            </div>
                        </div>

                        <h6 class="mt-3"> <i class="fas fa-map-marker-alt"></i> Address</h6>
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <!-- 160202061 -->
                                <input name="barangay" hidden value="<?= $this->session->agrishop_login_barangay_id ?>">
                                <input type="text" class="form-control form-control-sm barangayInput border-primary text-uppercase" value="<?= $this->session->agrishop_login_address_text ?>" placeholder="TYPE BARANGAY (min 3 chars)" autocomplete="off" name="barangay_text">

                            </div>
                            <ul class="list-group barangayResults" style="position:absolute; z-index:9999; width:100%; display:none;cursor:pointer;"></ul>

                        </div>

                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn bg-primary text-white update_profile"> <i class="fas fa-save"></i> Update Profile</button>
            </div>
            </form>
        </div>
    </div>
</div>