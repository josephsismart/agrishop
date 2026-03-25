<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!$this->session->agrishop_login_level) { redirect(base_url('login')); }
$uri = $this->session->agrishop_login_uri;
?>

<!-- ═══════════════════════════════════════════════
     PROFILE MODAL
════════════════════════════════════════════════ -->
<div class="modal fade" id="modalUpdateProfile" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fa fa-user mr-2"></i> My Profile</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <?= form_open(base_url('login/updateprofile'), 'id="form_save_dataUpdateProfile" enctype="multipart/form-data"') ?>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <img name="previewPic"
                         src="<?= $this->session->agrishop_login_img_path ?>"
                         width="90" height="90" class="rounded-circle border"
                         style="object-fit:cover;">
                    <div class="mt-2">
                        <label class="btn btn-sm btn-outline-secondary">
                            Change Photo <input type="file" name="picProfile" hidden
                                               onchange="imageView('picProfile','previewPic')">
                        </label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 mb-2">
                        <label>First Name</label>
                        <input type="text" name="firstName" class="form-control text-uppercase"
                               value="<?= $this->session->agrishop_login_first_name ?>">
                    </div>
                    <div class="col-6 mb-2">
                        <label>Middle Name</label>
                        <input type="text" name="middleName" class="form-control text-uppercase" nr="1"
                               value="<?= $this->session->agrishop_login_middle_name ?>">
                    </div>
                    <div class="col-12 mb-2">
                        <label>Last Name</label>
                        <input type="text" name="lastName" class="form-control text-uppercase"
                               value="<?= $this->session->agrishop_login_last_name ?>">
                    </div>
                    <div class="col-6 mb-2">
                        <label>Sex</label>
                        <select name="sex" class="form-control">
                            <option value="1" <?= ($this->session->agrishop_login_sex == 1 || $this->session->agrishop_login_sex === 't') ? 'selected' : '' ?>>Male</option>
                            <option value="0" <?= ($this->session->agrishop_login_sex == 0 || $this->session->agrishop_login_sex === 'f') ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>
                    <div class="col-6 mb-2">
                        <label>Birthdate</label>
                        <input type="date" name="birthdate" class="form-control"
                               value="<?= $this->session->agrishop_login_birthdate ?>">
                    </div>
                    <div class="col-12 mb-2">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" nr="1"
                               value="<?= $this->session->agrishop_login_email_address ?>">
                    </div>
                    <div class="col-12 mb-2">
                        <label>Contact Number</label>
                        <input type="text" name="contactNumber" class="form-control" nr="1"
                               value="<?= $this->session->agrishop_login_contact_num ?>">
                    </div>
                    <div class="col-12 mb-2">
                        <label>Address</label>
                        <input type="text" class="form-control barangayInputAll text-uppercase"
                               placeholder="Search barangay..."
                               value="<?= $this->session->agrishop_login_address_text ?>">
                        <input type="hidden" name="barangayAll"
                               value="<?= $this->session->agrishop_login_barangay_id ?>">
                        <ul class="list-group barangayResultsAll shadow"
                            style="position:absolute;z-index:9999;display:none;width:90%;"></ul>
                        <input type="hidden" name="barangay_text"
                               value="<?= $this->session->agrishop_login_address_text ?>">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-warning submitBtnPrimary">Save</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════
     SUPPLY INFO MODAL (Add / Edit)
════════════════════════════════════════════════ -->
<div class="modal fade" id="modalSupplyInfo" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fa fa-boxes mr-2"></i> Supply Info</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <?= form_open(base_url($uri . '/Supplies/saveSupply'), 'id="form_save_dataSupplyInfo" enctype="multipart/form-data"') ?>
            <input type="hidden" name="id">
            <div class="modal-body" style="overflow-y:auto;max-height:68vh;">

                <!-- Image preview -->
                <div class="text-center mb-3">
                    <img name="previewSupplyImg" src="<?= $system_svg_1x1 ?>"
                         width="90" height="90" class="rounded border" style="object-fit:cover;">
                    <div class="mt-1">
                        <label class="btn btn-sm btn-outline-secondary">
                            Photo <input type="file" name="picSupply" hidden
                                         onchange="imageView('picSupply','previewSupplyImg')">
                        </label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-2">
                        <label>Supply Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control text-uppercase" placeholder="e.g. AMMONIUM SULFATE">
                    </div>
                    <div class="col-6 mb-2">
                        <label>Brand</label>
                        <input type="text" name="brand" class="form-control text-uppercase" nr="1" placeholder="e.g. FERTIPHIL">
                    </div>
                    <div class="col-6 mb-2">
                        <label>Category <span class="text-danger">*</span></label>
                        <select name="supply_category_id" class="form-control select2">
                            <option value="">-- Select --</option>
                            <?php if (isset($categories)) foreach ($categories as $cat) : ?>
                                <option value="<?= $cat->id ?>"><?= $cat->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 mb-2">
                        <label>Unit of Measure <span class="text-danger">*</span></label>
                        <select name="uom" class="form-control">
                            <option value="PCS">PCS</option>
                            <option value="KG">KG</option>
                            <option value="BAG">BAG</option>
                            <option value="L">L (Liter)</option>
                            <option value="BOTTLE">BOTTLE</option>
                            <option value="SACK">SACK</option>
                            <option value="BOX">BOX</option>
                            <option value="PACK">PACK</option>
                        </select>
                    </div>
                    <div class="col-6 mb-2">
                        <label>Store</label>
                        <select name="store_id" class="form-control select2" nr="1">
                            <option value="">-- No store --</option>
                            <?php if (isset($stores)) foreach ($stores as $store) : ?>
                                <option value="<?= $store->id ?>"><?= $store->store_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6 mb-2">
                        <label>Price (₱) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00">
                    </div>
                    <div class="col-6 mb-2">
                        <label>Qty Available</label>
                        <input type="number" name="qty_available" class="form-control" nr="1" placeholder="0">
                    </div>
                    <div class="col-12 mb-2">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="2" nr="1"
                                  placeholder="Short description..."></textarea>
                    </div>
                    <div class="col-12 mb-2">
                        <label>Tags</label>
                        <input type="text" name="tags" class="form-control" nr="1"
                               placeholder="e.g. fertilizer, nitrogen, foliar">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning submitBtnPrimary">Save Supply</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════
     STORE INFO MODAL (Add / Edit)
════════════════════════════════════════════════ -->
<div class="modal fade" id="modalStoreInfo" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fa fa-store mr-2"></i> Store / Location</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <?= form_open(base_url($uri . '/Supplies/saveStore'), 'id="form_save_dataStoreInfo" enctype="multipart/form-data"') ?>
            <input type="hidden" name="id">
            <input type="hidden" name="lat">
            <input type="hidden" name="lon">
            <div class="modal-body" style="overflow-y:auto;max-height:75vh;">
                <div class="row mb-2">
                    <div class="col-12 mb-2">
                        <label>Store Name <span class="text-danger">*</span></label>
                        <input type="text" name="store_name" class="form-control text-uppercase"
                               placeholder="e.g. AGRI SUPPLY BUTUAN">
                    </div>
                    <div class="col-12 mb-2">
                        <label>Address</label>
                        <input type="text" class="form-control text-uppercase" nr="1"
                               placeholder="Search barangay..." id="storeBarangayInput">
                        <input type="hidden" name="barangay_id">
                        <input type="hidden" name="address_text">
                        <ul class="list-group shadow"
                            id="storeBarangayResults"
                            style="position:absolute;z-index:9999;display:none;width:90%;"></ul>
                    </div>
                </div>
                <!-- Leaflet map for pin drop -->
                <label>Pin Store Location <small class="text-muted">(click map to pin)</small></label>
                <div id="storeMap" style="height:320px;width:100%;border-radius:8px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning submitBtnPrimary">Save Store</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════
     ORDER DETAIL MODAL
════════════════════════════════════════════════ -->
<div class="modal fade" id="modalOrderDetail" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fa fa-shopping-bag mr-2"></i> Order Details</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-0">
                <table id="tblOrderDetails" class="table table-sm" style="width:100%">
                    <thead><tr><th></th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    /* Barangay search for store modal */
    $('#storeBarangayInput').on('keyup', function () {
        let keyword = $(this).val();
        if (keyword.length < 3) { $('#storeBarangayResults').hide(); return; }
        $.ajax({
            url: "<?= base_url('search-barangay') ?>",
            type: "POST",
            data: { keyword: keyword },
            success: function (res) {
                let data = JSON.parse(res);
                if (!data.length) { $('#storeBarangayResults').hide(); return; }
                let html = "";
                data.forEach(row => {
                    html += `<li class="list-group-item store-barangay-item" style="cursor:pointer;"
                        data-id="${row.id}" data-name="${row.text}">${row.text}</li>`;
                });
                $('#storeBarangayResults').html(html).show();
            }
        });
    });

    $(document).on('click', '.store-barangay-item', function () {
        $('#storeBarangayInput').val($(this).data('name'));
        $('[name=barangay_id]').val($(this).data('id'));
        $('[name=address_text]').val($(this).data('name'));
        $('#storeBarangayResults').hide();
    });

    /* profile save init */
    saveForm("UpdateProfile", [null], null);
    saveForm("SupplyInfo", ['SupplyList'], null, 0, 10);
    saveForm("StoreInfo",  [null], null);
</script>
