<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if (!$this->session->agrishop_login_level) { redirect(base_url('login')); } ?>
<?php $uri = $this->session->agrishop_login_uri; ?>
<script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mt-2 mb-n1 align-items-center">
            <div class="col">
                <h5 class="m-0 font-weight-bold">
                    <i class="fa fa-tags text-warning mr-1"></i> Promo &amp; Discounts
                </h5>
                <small class="text-muted">Post limited-time deals to attract more buyers on the homepage</small>
            </div>
            <div class="col-auto">
                <button class="btn btn-warning font-weight-bold" onclick="openPromoModal()">
                    <i class="fa fa-plus mr-1"></i> Add Promo
                </button>
            </div>
        </div>
    </div>
</section>

<section class="content">
<div class="container-fluid">

    <?php if (isset($billing) && $billing['count'] > 0): ?>
    <div class="alert alert-warning d-flex align-items-center mb-3">
        <i class="fa fa-exclamation-triangle mr-2"></i>
        You have <b class="mx-1"><?= $billing['count'] ?></b> unpaid invoice(s).
        <a href="<?= base_url($uri . '/Billing') ?>" class="btn btn-sm btn-warning ml-auto">View</a>
    </div>
    <?php endif; ?>

    <!-- Info banner -->
    <div class="alert alert-success d-flex align-items-center mb-3" style="border-radius:12px;">
        <i class="fa fa-lightbulb fa-lg mr-3 text-warning"></i>
        <div>
            <b>Tip:</b> Active promos appear on the <b>AgriShop homepage</b> and are visible to all shoppers.
            Set a discount, valid dates, and upload a photo to make your promo stand out!
        </div>
    </div>

    <!-- Promo table -->
    <div class="card shadow-sm">
        <div class="card-header bg-warning py-2">
            <h6 class="m-0 font-weight-bold"><i class="fa fa-list mr-1"></i> My Promos</h6>
        </div>
        <div class="card-body p-2">
            <table id="tblPromoList" class="table table-sm table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th width="30">#</th>
                        <th width="60">Image</th>
                        <th>Title / Produce</th>
                        <th>Price / Discount</th>
                        <th>Validity</th>
                        <th width="70">Status</th>
                        <th width="80">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>
</section>

<!-- ── Add/Edit Promo Modal ─────────────────────────────────── -->
<div class="modal fade" id="modalPromo" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#f59e0b,#fbbf24);color:#1a1a1a;">
                <h5 class="modal-title font-weight-bold">
                    <i class="fa fa-tags mr-2"></i><span id="promoModalTitle">Add Promo</span>
                </h5>
                <button class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="form_save_dataPromoList" enctype="multipart/form-data">
                <input type="hidden" name="promo_id" id="promo_id" value="0">

                <div class="row">
                    <!-- Image upload -->
                    <div class="col-md-4 text-center mb-3">
                        <div id="promoImgBox" onclick="$('#promo_img_input').click()"
                             style="width:100%;height:160px;border:2px dashed #fbbf24;border-radius:12px;
                             display:flex;flex-direction:column;align-items:center;justify-content:center;
                             cursor:pointer;background:#fffbeb;overflow:hidden;">
                            <img id="promoImgPreview" src="<?= base_url('dist/img/media/icons/1x1.png') ?>"
                                 style="display:none;width:100%;height:100%;object-fit:cover;">
                            <div id="promoImgPlaceholder">
                                <i class="fa fa-camera fa-2x text-warning mb-2"></i>
                                <div style="font-size:12px;color:#92400e;">Click to upload promo image</div>
                            </div>
                        </div>
                        <input type="file" id="promo_img_input" name="promo_img" accept="image/*" hidden
                               onchange="previewPromoImg(this)">
                        <small class="text-muted">Max 25MB · JPG, PNG</small>
                    </div>

                    <!-- Fields -->
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="font-weight-bold">Promo Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="promo_title" class="form-control"
                                   placeholder="e.g. Weekend Sale — Fresh Tomatoes" required>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Farm Produce</label>
                            <select name="farm_produce_id" id="promo_produce" class="form-control">
                                <option value="">— Select a produce (optional) —</option>
                                <?php
                                $farmer_id = $this->session->agrishop_login_farmer_id;
                                $produces  = $this->db->query("
                                    SELECT fp.id, pr.name, ff.farm_name
                                    FROM farm_produce fp
                                    JOIN farmer_farm ff ON fp.farm_id = ff.id
                                    JOIN produce pr ON fp.produce_id = pr.id
                                    WHERE ff.farmer_id = ?
                                    ORDER BY pr.name
                                ", [$farmer_id])->result();
                                foreach ($produces as $p):
                                ?>
                                <option value="<?= $p->id ?>"><?= htmlspecialchars($p->name) ?> — <?= htmlspecialchars($p->farm_name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Original Price (₱) <span class="text-danger">*</span></label>
                                    <input type="number" name="original_price" id="promo_orig" class="form-control"
                                           step="0.01" min="0" placeholder="0.00" required
                                           oninput="calcDiscount()">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Discounted Price (₱) <span class="text-danger">*</span></label>
                                    <input type="number" name="discounted_price" id="promo_disc" class="form-control"
                                           step="0.01" min="0" placeholder="0.00" required
                                           oninput="calcDiscount()">
                                </div>
                            </div>
                        </div>

                        <!-- Discount preview badge -->
                        <div id="discountPreview" style="display:none;margin-bottom:10px;">
                            <span class="badge badge-danger" style="font-size:14px;padding:6px 12px;"
                                  id="discountPct"></span>
                            <span class="text-muted ml-2" style="font-size:12px;" id="discountSaved"></span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Description / Notes</label>
                    <textarea name="description" class="form-control" rows="2"
                              placeholder="e.g. Fresh from the farm, organic, limited stocks only!"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Valid From <span class="text-danger">*</span></label>
                            <input type="date" name="valid_from" class="form-control"
                                   value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Valid Until <span class="text-danger">*</span></label>
                            <input type="date" name="valid_until" class="form-control"
                                   value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="font-weight-bold">Max Qty <small class="text-muted">(blank = unlimited)</small></label>
                            <input type="number" name="max_qty" class="form-control" min="1" placeholder="e.g. 50">
                        </div>
                    </div>
                </div>

                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button class="btn btn-warning font-weight-bold" id="btnSavePromo" onclick="savePromo()">
                    <i class="fa fa-save mr-1"></i> Save Promo
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    getTable('PromoList', 0, 10);
});

function openPromoModal(id) {
    $('#promoModalTitle').text(id ? 'Edit Promo' : 'Add Promo');
    $('#promo_id').val(id || 0);
    $('#promoImgPreview').hide().attr('src', '<?= base_url('dist/img/media/icons/1x1.png') ?>');
    $('#promoImgPlaceholder').show();
    if (!id) {
        document.getElementById('form_save_dataPromoList').reset();
        $('#promo_id').val(0);
        $('#discountPreview').hide();
    } else {
        $.get("<?= base_url($uri . '/Promo/getPromo') ?>?id=" + id, function(res) {
            var d = JSON.parse(res);
            if (!d) return;
            $('#promo_id').val(d.id);
            $('[name=title]').val(d.title);
            $('[name=farm_produce_id]').val(d.farm_produce_id);
            $('[name=original_price]').val(d.original_price);
            $('[name=discounted_price]').val(d.discounted_price);
            $('[name=description]').val(d.description);
            $('[name=valid_from]').val(d.valid_from);
            $('[name=valid_until]').val(d.valid_until);
            $('[name=max_qty]').val(d.max_qty);
            if (d.img_path) {
                $('#promoImgPreview').attr('src', '<?= base_url() ?>' + d.img_path).show();
                $('#promoImgPlaceholder').hide();
            }
            calcDiscount();
        });
    }
    $('#modalPromo').modal('show');
}
function editPromo(id)   { openPromoModal(id); }

function previewPromoImg(input) {
    if (!input.files[0]) return;
    var reader = new FileReader();
    reader.onload = function(e) {
        $('#promoImgPreview').attr('src', e.target.result).show();
        $('#promoImgPlaceholder').hide();
    };
    reader.readAsDataURL(input.files[0]);
}

function calcDiscount() {
    var orig = parseFloat($('#promo_orig').val()) || 0;
    var disc = parseFloat($('#promo_disc').val()) || 0;
    if (orig > 0 && disc > 0 && disc < orig) {
        var pct   = ((orig - disc) / orig * 100).toFixed(1);
        var saved = (orig - disc).toFixed(2);
        $('#discountPct').text(pct + '% OFF');
        $('#discountSaved').text('You save ₱' + saved);
        $('#discountPreview').show();
    } else {
        $('#discountPreview').hide();
    }
}

function savePromo() {
    var btn = $('#btnSavePromo').attr('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Saving...');
    $('#form_save_dataPromoList').ajaxSubmit({
        url: "<?= base_url($uri . '/Promo/savePromo') ?>",
        type: 'POST',
        success: function(res) {
            var d = JSON.parse(res);
            if (d.success) {
                successAlert(d.message);
                $('#modalPromo').modal('hide');
                getTable('PromoList', 0, 10);
            } else {
                failAlert(d.message);
            }
        },
        error: function() { failAlert('Something went wrong!'); },
        complete: function() {
            $('#btnSavePromo').attr('disabled', false).html('<i class="fa fa-save mr-1"></i> Save Promo');
        }
    });
}

function deletePromo(id) {
    Swal.fire({
        title: 'Delete Promo?',
        text: 'This promo will be removed from the homepage.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes, delete it'
    }).then(function(r) {
        if (r.isConfirmed) {
            $.post("<?= base_url($uri . '/Promo/deletePromo') ?>", { id: id }, function(res) {
                var d = JSON.parse(res);
                if (d.success) { successAlert('Promo deleted!'); getTable('PromoList', 0, 10); }
                else failAlert('Failed to delete.');
            });
        }
    });
}
</script>
