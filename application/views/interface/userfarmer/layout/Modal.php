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
                        <div class="col-12">
                            <h6 class="text-blue" id="farmCoordinates"></h6>
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
                                        <?php
                                        $query = $this->db->query("SELECT id, class_name FROM public.produce_classification ORDER BY id ASC");
                                        $classifications = $query->result();
                                        foreach ($classifications as $classification) {
                                            echo '<option value="' . $classification->id . '"> ' . $classification->class_name . '</option>';
                                        }
                                        ?>

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
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-lg" style="border: 2px solid #28a745;">

            <!-- HEADER -->
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title mb-0">
                    <i class="fas fa-tractor mr-2"></i> ADD FARM PRODUCE SUPPLY
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" style="font-size: 1.5rem;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <?= form_open(base_url($uri . '/FarmProduce/saveFarmProduce'), 'id="form_save_dataFarmProduceSupply"'); ?>

            <input name="farmId" hidden>
            <input name="produceSelectedId" hidden>

            <!-- BODY -->
            <div class="modal-body p-4">
                <!-- Step 1: Select Produce -->
                <div class="card mb-4 border-success">
                    <div class="card-header bg-light-success py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 text-dark font-weight-bold">
                                <i class="fas fa-seedling mr-2"></i> STEP 1: SELECT PRODUCE
                            </h6>
                            <span class="badge badge-success badge-pill" style="font-size: 1rem; padding: 0.5rem 1rem;">1</span>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <label class="form-label text-dark font-weight-bold mb-2 d-block" style="font-size: 1.1rem;">
                            <i class="fas fa-search mr-2"></i>Search Your Produce
                        </label>
                        <div class="input-group input-group-lg mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-white border-success">
                                    <i class="fas fa-search text-success"></i>
                                </span>
                            </div>
                            <input type="text" name="produceSelected" class="form-control form-control-lg produceInput text-uppercase border-success" placeholder="Type crop or vegetable name here..." autocomplete="off" style="font-size: 1.1rem;">
                        </div>

                        <!-- suggestion list -->
                        <ul class="list-group position-absolute w-100 shadow-lg produceList mt-2" style="z-index: 9999; max-height: 300px; overflow-y: auto; display: none; font-size: 1.1rem;">
                        </ul>

                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label text-dark font-weight-bold mb-2 d-block" style="font-size: 1.1rem;">
                                    <i class="fas fa-calendar-day mr-2"></i>When did you harvest?
                                </label>
                                <div class="form-group mb-0">
                                    <div class="input-group input-group-lg">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-success" style="font-size: 1.2rem;">
                                                <i class="fas fa-calendar-check text-success"></i>
                                            </span>
                                        </div>
                                        <input type="date" class="form-control form-control-lg border-success" name="harvest_date" value="<?= Date('Y-m-d'); ?>" style="font-size: 1.1rem;">
                                    </div>
                                    <!-- <small class="form-text text-muted mt-2" style="font-size: 1rem;">
                                <i class="fas fa-info-circle mr-1"></i> Select the date when you harvested this produce
                            </small> -->
                                </div>
                            </div>
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label text-dark mb-2" style="font-size: 1.1rem;">
                                    <i class="fas fa-hashtag mr-2"></i>Quantity
                                </label>
                                <div class="input-group input-group-lg">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-success" style="font-size: 1.2rem;">
                                            <i class="fas fa-weight text-success"></i>
                                        </span>
                                    </div>
                                    <input type="number" class="form-control form-control-lg border-success" name="qty_add" min="1" value="1" placeholder="Amount" style="font-size: 1.1rem;">
                                </div>
                                <!-- <small class="form-text text-muted mt-2" style="font-size: 1rem;">
                                    <i class="fas fa-info-circle mr-1"></i> Enter the total amount you have
                                </small> -->
                            </div>

                        </div>
                    </div>

                </div>


                <!-- Step 4: Pricing -->
                <div class="card mb-2 border-success">
                    <div class="card-header bg-light-success py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 text-dark font-weight-bold">
                                <i class="fas fa-tag mr-2"></i> STEP 2: SET YOUR PRICE, QUANTITY AND UoM
                            </h6>
                            <span class="badge badge-success badge-pill" style="font-size: 1rem; padding: 0.5rem 1rem;">2</span>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <label class="form-label text-dark font-weight-bold mb-3 d-block" style="font-size: 1.1rem;">
                            <i class="fas fa-money-bill-wave mr-2"></i>What's your selling price?
                        </label>


                        <div class="form-group mb-0">
                            <div class="input-group input-group-lg">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-success" style="font-size: 1.3rem;">
                                        <i class="fas fa-tag text-success"></i>
                                    </span>
                                </div>

                                <input type="number" class="form-control form-control-lg border-success font-weight-bold" name="price" min="1" placeholder="Enter your price" style="font-size: 1.2rem;">

                                <div class="input-group-append">
                                    <span class="input-group-text bg-white border-success font-weight-bold" style="font-size: 1.1rem;">
                                        per
                                    </span>
                                </div>


                                <select class="form-control form-control-lg border-success" name="uom" style="font-size: 1.1rem;">
                                    <option value="KG">Kilograms (KG)</option>
                                    <option value="G">Grams (G)</option>
                                    <option value="LB">Pounds (LB)</option>
                                    <option value="SACK">Sack</option>
                                    <option value="BAG">Bag</option>
                                    <!-- Count -->
                                    <option value="PC">Piece (PC)</option>
                                    <option value="PACK">Pack</option>
                                    <option value="BUNDLE">Bundle</option>
                                    <option value="BUNCH">Bunch</option>
                                    <option value="CLUSTER">Cluster</option>
                                    <option value="DOZEN">Dozen</option>
                                    <option value="TRAY">Tray</option>
                                    <option value="HEAD">Head</option>
                                    <option value="STICK">Stick</option>
                                    <!-- Volume -->
                                    <option value="L">Liters (L)</option>
                                    <option value="ML">Milliliters (ML)</option>
                                </select>
                            </div>

                            <small class="form-text text-muted mt-2" style="font-size: 1rem;">
                                <i class="fas fa-info-circle mr-1"></i>
                                You may follow the suggested price or set your own.
                            </small>

                            <!-- AI PRICE SUGGESTION -->
                            <div class="bg-navy text-white rounded p-3 mb-3">
                                <div class="font-weight-bold mb-1" style="font-size: 1.1rem;">
                                    🤖 Suggested Market Price
                                </div>

                                <div class="h3 mb-1">
                                    ₱ <span id="aiSuggestedPrice">--</span>
                                    <small style="font-size: 1.1rem;"></small>
                                </div>

                                <div class="text-light mb-2" style="font-size: 1rem;" id="aiPriceReason">
                                    Based on recent market prices, demand, and your past sales.
                                </div>

                                <button type="button" class="btn btn-light btn-lg btn-block font-weight-bold" id="btnUseAiPrice">
                                    Use Suggested Price
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer p-1 bg-light" style="border-top: 2px solid #dee2e6;">
                <!-- <button type="button" class="btn btn-outline-secondary btn-lg mr-3 px-4" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i> CANCEL
                </button> -->
                <button type="submit" class="btn btn-success btn-lg px-5 w-100">
                    <i class="fas fa-cloud-upload-alt mr-2 fs-5"></i> ADD SUPPLY
                </button>
            </div>

            </form>
        </div>
    </div>
</div>


<!-- <div class="modal fade show" id="modalFarmProduceSupply" data-backdrop="static" style="padding-right: 15px; display: block;" aria-modal="true" role="dialog"> -->
<div class="modal fade" id="modalAddFarmProduceSupply" data-backdrop="static">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content shadow rounded">

            <!-- HEADER -->
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title mb-0">
                    <i class="fas fa-seedling mr-2"></i> Add Farm Supply
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <?= form_open(base_url($uri . '/FarmProduce/saveAddFarmProduceSupply'), 'id="form_save_dataAddFarmProduceSupply"'); ?>
            <input type="hidden" name="fp_id">

            <!-- BODY -->
            <div class="modal-body p-4">

                <!-- IMAGE + NAME -->
                <div class="text-center mb-2">
                    <img name="previewPicProduce" src="<?= base_url('dist/img/media/icons/1x1.png') ?>" class="rounded border shadow-sm mb-2" width="120" height="120">

                    <div class="h5 font-weight-bold mb-1" name="show_produceName"></div>
                    <div class="text-muted" style="font-size:1rem;" name="show_classification"></div>
                </div>

                <!-- ADD QUANTITY -->
                <div class="bg-light rounded p-2 mb-2">
                    <div class="font-weight-bold mb-2" style="font-size:1.1rem;">
                        👉 Quantity to Add
                    </div>

                    <input type="number" class="form-control form-control-lg border-success" name="qty_add" min="1" placeholder="Sample: 10">
                </div>


                <!-- PRODUCE INFORMATION (RE-ARRANGED) -->
                <div class="bg-light rounded p-2" style="font-size:1.2rem !important;">
                    <div class="font-weight-bold mb-3" style="font-size:1.1rem;">
                        ℹ️ Produce Information
                    </div>

                    <div class="row mb-1">
                        <div class="col-6 text-muted">Unit</div>
                        <div class="col-6 font-weight-bold">
                            <span name="show_uom"></span>
                        </div>
                    </div>

                    <div class="row mb-1">
                        <div class="col-6 text-muted">Seasonal</div>
                        <div class="col-6 font-weight-bold">
                            <span name="show_seasonal"></span>
                        </div>
                    </div>

                    <div class="row mb-1">
                        <div class="col-6 text-muted">Stock Left</div>
                        <div class="col-6 font-weight-bold text-success" style="font-size:1.2rem;">
                            <span name="show_qty_left"></span>
                        </div>
                    </div>

                    <div class="row mb-n4">
                        <div class="col-6 text-muted">Current Price</div>
                        <div class="col-6 font-weight-bold text-primary" style="font-size:1.2rem;">
                            ₱ <span name="price"></span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer px-4 py-3">
                <button type="submit" class="btn btn-success btn-lg btn-block">
                    <i class="fas fa-plus"></i> Add Supply
                </button>
                <button type="button" class="btn btn-secondary btn-sm btn-block" data-dismiss="modal">
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
                                <input type="email" class="form-control form-control-sm border-primary" placeholder="EMAIL" name="email" value="<?= $this->session->agrishop_login_email_address ?>" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="mt-3"><i class="fas fa-phone"></i> Contact Number</h6>
                            <div class="col-12">
                                <input type="text" class="form-control form-control-sm border-primary text-uppercase" placeholder="Contact Number" name="contactNumber" value="<?= $this->session->agrishop_login_contact_num ?>">
                            </div>
                        </div>

                        <h6 class="mt-3"> <i class="fas fa-map-marker-alt"></i> Address</h6>
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <!-- 160202061 -->
                                <input name="barangayAll" hidden value="<?= $this->session->agrishop_login_barangay_id ?>">
                                <input type="text" class="form-control form-control-sm barangayInputAll border-primary text-uppercase" value="<?= $this->session->agrishop_login_address_text ?>" placeholder="TYPE BARANGAY (min 3 chars)" autocomplete="off" name="barangay_text">

                            </div>
                            <ul class="list-group barangayResultsAll" style="position:absolute; z-index:9999; width:100%; display:none;cursor:pointer;"></ul>

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


<!-- <div class="modal fade show" id="gcashModal" tabindex="-1" aria-labelledby="gcashModalLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
<div class="modal fade" id="gcashModal" tabindex="-1" aria-labelledby="gcashModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xs">
        <div class="modal-content" style="border-radius:0;">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="gcashModalLabel">My Gcash</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?= form_open(base_url('/updategcash'), 'id=form_save_dataUpdateGcash'); ?>
            <div class="modal-body">
                <div class="card-body">
                    <h6><i class="fas fa-qrcode"></i> Upload QR Code</h6>
                    <div class="col-12">
                        <center class="p-0 border">
                            <div class="form-group">
                                <img name="previewPic" src="<?= $this->session->agrishop_login_gcash_qr  ?>" onclick="$('[name=picGcash]').trigger('click')" autocomplete="off" width="140" height="140" class="border border-white border-2 rounded elevation-2" type="button" alt="User Image">
                            </div>
                            <div class="form-group">
                                <input name="picGcash" type="file" accept="image/*" onchange="imageView('picGcash','previewPic','imgtargetLink')" nr="1" hidden="">
                                <!-- <input name="personId" type="text" nr="1" > -->
                                <input name="img_path" type="text" nr="1" hidden="">
                            </div>
                        </center>
                    </div>
                    <div class="row">

                        <div class="col-12 text-center">
                            <h6 class="mt-3 text-gray"><i class="fas fa-phone"></i> Number</h6>
                            <div class="col-12">
                                <input type="text" class="form-control form-control-sm border-primary text-uppercase  text-center fs-5 text-black" style="font-weight: bold;" autocomplete="off" placeholder="ACCOUNT NUMBER" name="accountNumber" nr="1" value="<?= $this->session->agrishop_login_gcash_account_num ?>">
                            </div>
                        </div>

                        <div class="col-lg-12 text-center">
                            <h6 class="mt-3 text-gray"><i class="fas fa-user"></i> Name</h6>
                            <div class="col-12">
                                <input type="text" class="form-control form-control-sm text-uppercase  text-center fs-5 text-black border-primary" style="font-weight: bold;" value="<?= $this->session->agrishop_login_gcash_account_name ?>" name="accountName" placeholder="ACCOUNT NAME" autocomplete="off">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn bg-primary text-white update_gcash"> <i class="fas fa-save"></i> Update Gcash</button>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalFarmerSubscription" tabindex="-1" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content shadow-lg rounded">

            <!-- HEADER -->
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    🌾 Grow Your Farm Sales
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <!-- BODY -->
            <div class="modal-body">

                <p class="text-center mb-4">
                    Reach more buyers, list more produce, and sell faster.<br>
                    Choose the plan that fits your farm business.
                </p>

                <!-- FREE TIER -->
                <div class="border rounded p-3 mb-3">
                    <h6 class="text-success mb-2">
                        🌱 Tier 1 – Free Plan
                    </h6>
                    <ul class="small mb-3">
                        <li>✅ Register up to <strong>2 farms only</strong></li>
                        <li>✅ <strong>2 produce</strong> per farm</li>
                        <li>❌ Lower priority in customer search results</li>
                        <li>❌ Limited visibility to buyers</li>
                    </ul>
                    <span class="badge badge-secondary">Good for beginners</span>
                </div>

                <!-- PREMIUM TIER -->
                <div class="border rounded p-3 bg-light">
                    <h6 class="text-warning mb-2">
                        🚜 Tier 2 – Premium Plan
                    </h6>
                    <h4 class="text-success mb-2">
                        ₱99 <small class="text-muted">/ month</small>
                    </h4>
                    <ul class="small mb-3">
                        <li>✅ <strong>Unlimited farm registration</strong></li>
                        <li>✅ <strong>Unlimited produce per farm</strong></li>
                        <li>🔥 <strong>Priority listing</strong> in customer searches</li>
                        <li>📈 Higher chance of getting orders</li>
                        <li>⚡ Faster exposure to buyers</li>
                    </ul>

                    <div class="alert alert-success small mb-0">
                        💡 <strong>Tip:</strong> Farmers on Premium get noticed first by customers.
                    </div>
                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-dismiss="modal">
                    Maybe Later
                </button>
                <button class="btn btn-success">
                    🚀 Upgrade to Premium
                </button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalCartDetails">
    <!-- <div class="modal fade show" id="modalCheckout" tabindex="-1" aria-labelledby="modalCheckoutLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content rounded shadow">
            <!-- HEADER -->
            <div class="modal-header bg-dark py-2">
                <h5 class="modal-title mb-0 text-white">
                    <i class="fas fa-shopping-basket mr-1"></i> Cart Details and Checkout
                </h5>
                <button type="button" class="btn-close" style="filter: invert(1);" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-3 py-2">
                <table id="tblCartDetails" class="table table-sm table-bordered mb-0" width="100%">
                    <thead class="small">
                        <tr>
                            <!-- <th width="1">Image</th> -->
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- dynamic rows -->
                    </tbody>
                </table>
            </div>


            <div class="modal-footer py-2">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDeliveryStatus">
    <!-- <div class="modal fade show" id="modalDeliveryStatus" tabindex="-1" aria-labelledby="modalCheckoutLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content rounded shadow">
            <!-- HEADER -->
            <div class="modal-header py-2">
                <h5 class="modal-title mb-0">
                    <i class="fas fa-truck mr-1"></i> Delivery Status
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-3 py-2">
                <i>Please select the delivery status</i>
                <select id="deliveryStatus" class="form-control form-control-sm fs-5 text-uppercase status-select text-center" data-type="delivery">
                    <option value="TO_PICKUP">TO PICKUP</option>
                    <option value="TO_DELIVER">TO DELIVER</option>
                    <option value="ON_THE_WAY">ON THE WAY</option>
                    <option value="DELIVERED">DELIVERED</option>
                </select>
                <button type="button" class="btn btn-primary btn-sm w-100 mt-3" onclick="updateStatus('delivery',$('#deliveryStatus').val())"><i class="fas fa-check"></i> Update Status</button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalPaymentStatus">
    <!-- <div class="modal fade show" id="modalPaymentStatus" tabindex="-1" aria-labelledby="modalCheckoutLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content rounded shadow">
            <!-- HEADER -->
            <div class="modal-header py-2">
                <h5 class="modal-title mb-0">
                    <i class="fas fa-money-bill-wave mr-1"></i> Payment Status
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body px-3 py-2">
                <i>Please select the payment status</i>
                <select id="paymentStatus" class="form-control form-control-sm fs-5 text-uppercase status-select text-center" data-type="payment">
                    <option value="UNPAID">UNPAID</option>
                    <option value="VERIFYING">VERIFYING</option>
                    <option value="PAID">PAID</option>
                    <option value="FAILED">FAILED</option>
                </select>
                <button type="button" class="btn btn-success btn-sm w-100 mt-3" onclick="updateStatus('payment',$('#paymentStatus').val())"><i class="fas fa-check"></i> Update Status</button>
            </div>

        </div>
    </div>
</div>