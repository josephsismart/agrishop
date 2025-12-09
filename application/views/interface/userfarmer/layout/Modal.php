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
                                    <input type="text" class="form-control border-primary" name="farmName" placeholder="Farm Name">
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
                                    <select class="form-control border-primary" name="barangay">
                                        <option value="">SEARCH LOCATION</option>
                                        <option value="AGAO">AGAO</option>
                                        <option value="AMPAYON">AMPAYON</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6 col-4">
                                <div class="form-group">
                                    <label class="col-form-label">Organization</label>
                                    <select style="width:80%" class="form-control form-control-sm text-uppercase select2" name="organization">
                                        <option value="">ORGANIZATION MEMBER</option>
                                        <option value="FFF">Federation of Free Farmers (FFF)</option>
                                        <option value="RMP">Rural Missionaries of the Philippines</option>
                                        <option value="AgriCOOPh">AgriCOOPh (Philippine Family Farmers’ Agriculture Fishery Forestry Cooperatives Federation)</option>
                                        <option value="AA">Alyansa Agrikultura (AA)</option>
                                        <option value="AMA">Aniban ng Manggagawa sa Agrikultura (AMA)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-outline card-primary mt-3">
                    <div class="card-header">
                        <h3 class="card-title">Location in map</h3>

                        <div class="card-tools">
                            <!-- <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button> -->
                        </div>
                        <!-- /.card-tools -->
                    </div>
                    <!-- /.card-header -->
                    <input id="farmCoordinates" name="coordinates" hidden>
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
                                    <label class="col-form-label"><i class="fas fa-carrot"></i> Produce Name</label>
                                    <input type="text" class="form-control border-primary" name="produceName" placeholder="Produce Name">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-5 col-sm-5 col-5">
                                <div class="form-group">
                                    <label class="col-form-label">Classification</label>
                                    <select class="form-control border-primary" name="classification">
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
                            <!-- <div class="col-lg-3 col-md-6 col-sm-6 col-4">
                                <div class="form-group">
                                    <label class="col-form-label"><i class="fas fa-scale-balanced"></i> UoM</label>
                                    <select class="form-control border-primary" name="unit_of_measure">
                                        <option value="">Select Unit of Measure</option>
                                        <option value="pc">piece (pc)</option>
                                        <option value="kg">kilogram (kg)</option>
                                        <option value="g">gram (g)</option>
                                        <option value="L">liter (L)</option>
                                        <option value="mL">milliliter (mL)</option>
                                        <option value="sack">sack</option>
                                        <option value="bag">bag</option>
                                        <option value="box">box</option>
                                        <option value="tray">tray</option>
                                        <option value="bunch">bunch</option>
                                        <option value="bundle">bundle</option>
                                        <option value="crate">crate</option>
                                        <option value="dozen">dozen</option>
                                        <option value="ton">ton</option>
                                    </select>
                                </div>
                            </div> -->
                            <div class="col-lg-8 col-md-6 col-sm-6 col-8">
                                <div class="form-group">
                                    <label class="col-form-label"><i class="fas fa-info"></i> Description</label>
                                    <input type="text" class="form-control border-primary" name="description" placeholder="Description" nr="1">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6 col-4">
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
<div class="modal fade" id="modalFarmProduceSupply" data-backdrop="static" dism>
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

            <?= form_open(base_url($uri . '/FarmProduce/saveFarmProduceSupply'), 'id="form_save_dataFarmProduceSupply"'); ?>

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