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
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow">

            <!-- MODAL HEADER -->
            <div class="modal-header bg-success py-2">
                <h5 class="modal-title text-white mb-0">
                    <i class="fas fa-tractor mr-1"></i> Farm Information
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <?= form_open(base_url($uri . '/FarmProduce/saveFarmInfo'), 'id=form_save_dataFarmInfo'); ?>
            <input type="hidden" name="personId" nr="1">

            <!-- MODAL BODY -->
            <div class="modal-body p-3">

                <!-- BASIC INFO -->
                <div class="row">

                    <!-- FARM IMAGE -->
                    <div class="col-md-3 text-center">
                        <label class="col-form-label">Farm Picture</label>
                        <div class="mb-2">
                            <img name="previewPicFarm" src="<?= base_url("dist/img/media/icons/1x1.png"); ?>" onclick="$('[name=picFarm]').trigger('click')" class="img-fluid rounded border elevation-1" style="width:120px;height:120px;cursor:pointer;">
                        </div>
                        <input type="file" name="picFarm" accept="image/*" hidden onchange="imageView('picFarm','previewPicFarm','imgtargetLink')" nr="1">
                    </div>

                    <!-- FARM DETAILS -->
                    <div class="col-md-9">
                        <div class="row">

                            <div class="col-md-8">
                                <div class="form-group">
                                    <label><i class="fas fa-tractor"></i> Farm Name</label>
                                    <input type="text" class="form-control text-uppercase border-primary" name="farmName" placeholder="FARM NAME" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Area (sqm)</label>
                                    <input type="text" class="form-control border-primary" name="totalAreaSqm" placeholder="AREA" autocomplete="off" nr="1">
                                </div>
                            </div>

                            <div class="col-md-8 position-relative">
                                <div class="form-group">
                                    <label><i class="fas fa-house"></i> Barangay</label>
                                    <input type="hidden" name="barangay">
                                    <input type="text" class="form-control border-primary barangayInput" name="barangay_" placeholder="TYPE BARANGAY (min 3 chars)" autocomplete="off">
                                </div>
                                <ul class="list-group barangayResults" style="position:absolute;z-index:9999;width:100%;display:none;cursor:pointer">
                                </ul>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Soil Type</label>
                                    <select class="form-control text-uppercase" name="soil_type">
                                        <option value="">SOIL TYPE</option>
                                        <option value="ALLUVIAL SOIL">ALLUVIAL SOIL</option>
                                        <option value="CLAY">CLAY</option>
                                        <option value="CLAY LOAM">CLAY LOAM</option>
                                        <option value="LOAM">LOAM</option>
                                        <option value="SAND">SAND</option>
                                        <option value="SANDY LOAM">SANDY LOAM</option>
                                        <option value="SILT">SILT</option>
                                        <option value="SILT LOAM">SILT LOAM</option>
                                        <option value="SILTY CLAY">SILTY CLAY</option>
                                        <option value="PEAT">PEAT</option>
                                        <option value="ORGANIC SOIL">ORGANIC SOIL</option>
                                        <option value="VOLCANIC SOIL (ANDISOL)">VOLCANIC SOIL (ANDISOL)</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- MAP -->
                <div class="card card-outline card-primary mt-3">
                    <div class="card-header py-2">
                        <h6 class="mb-0">📍 Farm Location Map</h6>
                        <small class="text-primary" id="farmCoordinates"></small>
                    </div>

                    <input type="hidden" id="farmLat" name="lat">
                    <input type="hidden" id="farmLon" name="lon">

                    <div class="card-body p-0" style="height:250px;" id="map"></div>
                </div>

            </div>

            <!-- MODAL FOOTER -->
            <div class="modal-footer py-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-save"></i> Save
                </button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="clear_form('FarmInfo')">
                    Clear
                </button>
            </div>

            </form>

        </div>
    </div>
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
                                    <?php
                                    $query = $this->db->query("SELECT id, name, abbr FROM public.uom ORDER BY order_by");
                                    $uoms = $query->result();
                                    foreach ($uoms as $uom) {
                                        echo '<option value="' . $uom->abbr . '"> ' . $uom->name . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <hr>

                            <div class="form-group">
                                <div class="custom-control custom-switch" style="cursor: pointer;">
                                    <input type="checkbox" class="custom-control-input" id="enableWholesale" name="enable_wholesale">
                                    <label class="custom-control-label font-weight-bold" for="enableWholesale">
                                        Enable Wholesale Pricing
                                    </label>
                                </div>
                            </div>

                            <div id="wholesaleFields" style="display:none;">

                                <div class="row">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label class="form-label text-dark font-weight-bold mb-2 d-block" style="font-size: 1.1rem;">
                                            <i class="fas fa-calendar-day mr-2"></i>Minimum Quantity
                                        </label>
                                        <div class="form-group mb-0">
                                            <div class="input-group input-group-lg">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-white border-success" style="font-size: 1.2rem;">
                                                        <i class="fas fa-calendar-check text-success"></i>
                                                    </span>
                                                </div>
                                                <input type="number" class="form-control form-control-lg border-success wholesale-input" name="wholesale_min_qty" min="1" placeholder="Example: 10" nr="1" style="font-size: 1.1rem;">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label class="form-label text-dark mb-2" style="font-size: 1.1rem;">
                                            <i class="fas fa-hashtag mr-2"></i>Wholesale Price
                                        </label>
                                        <div class="input-group input-group-lg">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text bg-white border-success" style="font-size: 1.2rem;">
                                                    <i class="fas fa-weight text-success"></i>
                                                </span>
                                            </div>
                                            <input type="number" class="form-control  form-control-lg border-success wholesale-input" name="wholesale_price" min="1" placeholder="Enter wholesale price" nr="1">

                                        </div>
                                    </div>
                                </div>

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
    <div class="modal-dialog modal-sm">
        <div class="modal-content shadow rounded">

            <!-- HEADER -->
            <div class="modal-header p-2 bg-navy">
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
            <div class="modal-body">

                <!-- IMAGE + NAME -->
                <div class="text-center mb-n2 p-1">
                    <img name="previewPicProduce" src="<?= base_url('dist/img/media/icons/1x1.png') ?>" class="rounded shadow-sm border mb-2" style="width:130px;height:130px;object-fit:cover;">

                    <h4 class="font-weight-bold mb-1" name="show_produceName"></h4>
                    <div class="text-success font-weight-bold" name="show_classification"></div>
                </div>


                <!-- ADD QUANTITY CARD -->
                <div class="card border-0 shadow-sm mb-1">
                    <div class="card-body">

                        <input type="number" class="form-control form-control border-primary text-center" name="qty_add" min="1" placeholder="Enter quantity (ex: 10)">

                    </div>
                </div>


                <!-- PRODUCE INFORMATION CARD -->
                <div class="card border-0 shadow-sm mb-n3">

                    <div class="card-body p-2">
                        <!-- SEASONAL -->
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="font-weight-bold" name="show_seasonal"></span>
                        </div>
                        <!-- STOCK -->
                        <div class="text-center rounded bg-light mb-1">
                            <div class="text-muted small">Stock Left</div>

                            <div class="font-weight-bold text-black" style="font-size:1.5rem;">
                                <span name="show_qty_left"></span>
                            </div>
                        </div>

                        <!-- PRICE -->
                        <div class="text-center rounded bg-light">
                            <div class="text-muted small">Current Price</div>

                            <div class="font-weight-bold text-primary" style="font-size:1.8rem;">
                                ₱ <span name="price"></span>/<span name="show_uom"></span>
                            </div>
                        </div>

                        <!-- WHOLESALE PRICE -->
                        <div class="text-center rounded bg-light wholesale-price">
                            <div class="text-muted small">Wholesale Price</div>

                            <div class="badge bg-gray" style="font-size:1rem;">
                                ₱ <span name="wholesale_price"></span> @ <span name="wholesale_qty"></span> qty
                            </div>
                        </div>

                    </div>
                </div>

            </div>


            <!-- FOOTER -->
            <div class="modal-footer px-3 py-2 d-flex flex-column">

                <button type="submit" class="btn bg-green btn-lg btn-block shadow-sm p-0" style="font-size: 1.2rem !important;">
                    <i class="fas fa-plus"></i> Add Supply
                </button>
            </div>

            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0 rounded-3 overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header bg-primary bg-gradient text-white py-3 px-4">
                <div class="d-flex align-items-center w-100">
                    <div class="flex-grow-1">
                        <h1 class="modal-title fw-bold mb-0">
                            <i class="fas fa-user-circle me-2"></i>My Profile
                        </h1>
                    </div>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <?= form_open(base_url('/updateprofile'), 'id="form_save_dataUpdateProfile"'); ?>

            <!-- BODY -->
            <div class="modal-body p-0">
                <div class="row g-0">

                    <!-- LEFT SIDE - PROFILE PHOTO -->
                    <div class="col-lg-4 col-md-5 border-end bg-light">
                        <div class="p-4">
                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block mb-3">
                                    <img name="previewPic" src="<?= $this->session->agrishop_login_img_path ?>" onclick="$('[name=picProfile]').trigger('click')" width="140" height="140" class="border border-3 border-white shadow-lg rounded-circle object-fit-cover" style="cursor: pointer;" alt="Profile Picture">
                                    <div class="position-absolute bottom-0 end-0">
                                        <span class="badge bg-primary rounded-circle p-2 shadow-sm">
                                            <i class="fas fa-camera fa-xs text-white"></i>
                                        </span>
                                    </div>
                                </div>
                                <h6 class="text-muted fw-bold mb-2">
                                    <i class="fas fa-camera-retro me-1"></i>Profile Photo
                                </h6>
                                <p class="small text-muted mb-0">Click image to upload new photo</p>
                            </div>
                            <input name="picProfile" type="file" accept="image/*" onchange="imageView('picProfile','previewPic','imgtargetLink')" nr="1" hidden>
                            <input name="img_path" type="text" nr="1" hidden>
                        </div>
                    </div>

                    <!-- RIGHT SIDE - FORM FIELDS -->
                    <div class="col-lg-8 col-md-7">
                        <div class="p-4">

                            <!-- NAME SECTION -->
                            <div class="mb-4">
                                <h6 class="text-primary fw-bold mb-3">
                                    <i class="fas fa-user me-2"></i>Personal Information
                                </h6>
                                <div class="row g-3">
                                    <div class="col-lg-4 col-md-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control border-primary text-uppercase" id="firstName" value="<?= $this->session->agrishop_login_first_name ?>" name="firstName" placeholder="FIRST NAME" autocomplete="off">
                                            <label for="firstName" class="text-muted small">First Name</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control border-primary text-uppercase" id="middleName" value="<?= $this->session->agrishop_login_middle_name ?>" name="middleName" placeholder="MIDDLE NAME" autocomplete="off" nr="1">
                                            <label for="middleName" class="text-muted small">Middle Name</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-12">
                                        <div class="form-floating">
                                            <input type="text" class="form-control border-primary text-uppercase" id="lastName" value="<?= $this->session->agrishop_login_last_name ?>" name="lastName" placeholder="LAST NAME" autocomplete="off">
                                            <label for="lastName" class="text-muted small">Last Name</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BIRTHDATE & GENDER -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-muted fw-bold mb-3">
                                        <i class="fas fa-birthday-cake me-2"></i>Birthdate
                                    </h6>
                                    <div class="form-floating">
                                        <input type="date" class="form-control border-primary" id="birthdate" name="birthdate" nr="1" value="<?= $this->session->agrishop_login_birthdate ?>">
                                        <label for="birthdate" class="text-muted small">Select Birthdate</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted fw-bold mb-3">
                                        <i class="fas fa-venus-mars me-2"></i>Gender
                                    </h6>
                                    <div class="form-floating">
                                        <select class="form-select border-primary" id="gender" name="sex">
                                            <option value="t" <?= $this->session->agrishop_login_sex == 't' ? 'selected' : '' ?>>Male</option>
                                            <option value="f" <?= $this->session->agrishop_login_sex == 'f' ? 'selected' : '' ?>>Female</option>
                                        </select>
                                        <label for="gender" class="text-muted small">Select Gender</label>
                                    </div>
                                </div>
                            </div>

                            <!-- CONTACT INFORMATION -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-muted fw-bold mb-3">
                                        <i class="fas fa-envelope me-2"></i>Email Address
                                    </h6>
                                    <div class="form-floating">
                                        <input type="email" class="form-control border-primary" id="email" placeholder="EMAIL" name="email" value="<?= $this->session->agrishop_login_email_address ?>" autocomplete="off">
                                        <label for="email" class="text-muted small">Email Address</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted fw-bold mb-3">
                                        <i class="fas fa-phone me-2"></i>Contact Number
                                    </h6>
                                    <div class="form-floating">
                                        <input type="text" class="form-control border-primary text-uppercase" id="contactNumber" placeholder="Contact Number" name="contactNumber" value="<?= $this->session->agrishop_login_contact_num ?>">
                                        <label for="contactNumber" class="text-muted small">Contact Number</label>
                                    </div>
                                </div>
                            </div>

                            <!-- ADDRESS -->
                            <div class="mb-4">
                                <h6 class="text-muted fw-bold mb-3">
                                    <i class="fas fa-map-marker-alt me-2"></i>Address
                                </h6>
                                <div class="position-relative">
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-light border-primary">
                                            <i class="fas fa-map-pin text-primary"></i>
                                        </span>
                                        <div class="form-floating flex-grow-1">
                                            <input type="text" class="form-control border-start-0 border-primary text-uppercase barangayInput" id="barangayInput" value="<?= $this->session->agrishop_login_address_text ?>" placeholder="TYPE BARANGAY (min 3 chars)" autocomplete="off" name="barangay_text">
                                            <label for="barangayInput" class="text-muted small">Barangay Name</label>
                                        </div>
                                    </div>
                                    <input name="barangayAll" type="hidden" value="<?= $this->session->agrishop_login_barangay_id ?>">
                                    <ul class="list-group barangayResults shadow-lg border-0" style="position:absolute; z-index:9999; width:100%; display:none; cursor:pointer; max-height: 200px; overflow-y: auto;"></ul>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i> Type at least 3 characters to search barangay
                                    </small>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer bg-light py-3 px-4 border-top">
                <div class="d-flex w-100 gap-3">
                    <button type="button" class="btn btn-outline-secondary btn-lg flex-fill rounded-pill" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg flex-fill rounded-pill fw-bold shadow-sm update_profile">
                        <i class="fas fa-save me-2"></i>Update Profile
                    </button>
                </div>
            </div>

            </form>
        </div>
    </div>
</div>

<!-- <div class="modal fade show" id="viewGcashModal" tabindex="-1" aria-labelledby="viewGcashModalLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
<div class="modal fade" id="viewGcashModal" tabindex="-1" aria-labelledby="viewGcashModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-3 overflow-hidden border-0">

            <!-- HEADER -->
            <div class="modal-header bg-primary bg-gradient text-white py-3 px-4">
                <div class="d-flex align-items-center w-100">
                    <div class="flex-grow-1">
                        <h6 class="modal-title fw-bold mb-0">
                            <i class="fas fa-wallet me-2"></i>Proof of payment
                        </h6>
                    </div>
                    <button type="button" class="btn-close btn-close-white shadow-none m-0" data-bs-dismiss="modal"></button>
                </div>
            </div>

            <!-- BODY -->
            <div class="modal-body p-4">
                <div class="text-center proof_payment">
                </div>
            </div>
        </div>
    </div>
</div>


<!-- <div class="modal fade show" id="gcashModal" tabindex="-1" aria-labelledby="gcashModalLabel" aria-hidden="true" style="display: block; padding-left: 0px;"> -->
<div class="modal fade" id="gcashModal" tabindex="-1" aria-labelledby="gcashModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow-lg rounded-3 overflow-hidden border-0">

            <!-- HEADER -->
            <div class="modal-header bg-primary bg-gradient text-white py-3 px-4">
                <div class="d-flex align-items-center w-100">
                    <div class="flex-grow-1">
                        <h6 class="modal-title fw-bold mb-0">
                            <i class="fas fa-wallet me-2"></i>My GCash Account
                        </h6>
                    </div>
                    <button type="button" class="btn-close btn-close-white shadow-none m-0" data-bs-dismiss="modal"></button>
                </div>
            </div>

            <?= form_open(base_url('/updategcash'), 'id="form_save_dataUpdateGcash"'); ?>

            <!-- BODY -->
            <div class="modal-body p-4">

                <!-- QR UPLOAD -->
                <div class="text-center mb-n1">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <span class="badge bg-light text-primary rounded-pill px-3 py-2">
                            <i class="fas fa-qrcode me-2"></i>
                            <span class="fw-bold">Upload QR Code</span>
                        </span>
                    </div>

                    <div class="position-relative d-inline-block">
                        <img name="previewPic" src="<?= $this->session->agrishop_login_gcash_qr ?>" onclick="$('[name=picGcash]').trigger('click')" width="160" height="160" class="rounded-3 border border-3 border-primary shadow" style="cursor:pointer; object-fit:cover;" alt="GCash QR Code" nr="1">
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-primary rounded-circle p-2">
                                <i class="fas fa-camera fa-sm"></i>
                            </span>
                        </div>
                    </div>

                    <input name="picGcash" type="file" accept="image/*" onchange="imageView('picGcash','previewPic','imgtargetLink')" hidden nr="1">
                    <input name="img_path" type="text" hidden nr="1">

                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Click on QR to upload new image
                        </small>
                    </div>
                </div>

                <hr />

                <!-- ACCOUNT NUMBER -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-muted mb-n1 d-flex align-items-center justify-content-center">
                        <span class="bg-light rounded-circle p-2 me-2">
                            <i class="fas fa-phone text-primary"></i>
                        </span>
                        <span>Account Number</span>
                    </label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-light border-primary">
                            <i class="fas fa-mobile-alt text-primary"></i>
                        </span>
                        <input type="text" class="form-control form-control-lg text-center fw-bold border-primary" name="accountNumber" placeholder="09XXXXXXXXX" autocomplete="off" value="<?= $this->session->agrishop_login_gcash_account_num ?>">
                    </div>
                </div>

                <!-- ACCOUNT NAME -->
                <div>
                    <label class="form-label fw-bold text-muted mb-n1 d-flex align-items-center justify-content-center">
                        <span class="bg-light rounded-circle p-2 me-2">
                            <i class="fas fa-user text-primary"></i>
                        </span>
                        <span>Account Name</span>
                    </label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-light border-primary">
                            <i class="fas fa-id-card text-primary"></i>
                        </span>
                        <input type="text" class="form-control form-control-lg text-center fw-bold border-primary text-uppercase" name="accountName" placeholder="ACCOUNT NAME" autocomplete="off" value="<?= $this->session->agrishop_login_gcash_account_name ?>">
                    </div>
                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer bg-light py-3 px-4">
                <div class="d-flex w-100 gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-lg flex-fill rounded-pill" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg flex-fill rounded-pill fw-bold update_gcash shadow-sm">
                        <i class="fas fa-save me-2"></i>Update GCash
                    </button>
                </div>
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