<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if (!$this->session->agrishop_login_level) { redirect(base_url('login')); } ?>
<script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>
<?php $uri = $this->session->agrishop_login_uri; ?>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mt-2 mb-n1">
            <div class="col-sm-12">
                <h5>
                    <i class="nav-icon fas fa-seedling text-success"></i> On Production
                    <small class="text-muted ml-2" style="font-size:13px;">
                        <i class="fa fa-info-circle"></i>
                        All farmers' production is visible. Only <strong>your own</strong> records can be updated.
                    </small>
                </h5>
            </div>
        </div>
    </div>
</section>

<section class="content">
<div class="container-fluid">

    <!-- ── Active Productions ───────────────────────────────── -->
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-success text-white py-2 d-flex align-items-center justify-content-between">
            <h6 class="m-0"><i class="fa fa-seedling mr-1"></i> On Production — All Farmers</h6>
            <div>
                <button class="btn btn-sm btn-light mr-1"
                        onclick="$('#modalSearchProduction').modal('show')">
                    <i class="fa fa-map-marked-alt mr-1"></i> Search / Map
                </button>
                <button class="btn btn-sm btn-navy"
                        onclick="$('#modalProductionInfo').modal('show')">
                    <i class="fa fa-plus mr-1"></i> Add Production
                </button>
            </div>
        </div>
        <div class="card-body p-2">
            <div class="mb-2 text-info" style="font-size:12px;">
                <i class="fa fa-mouse-pointer mr-1"></i>
                Click the status to update — <strong>only for your own records</strong>.
                Other farmers are view-only.
            </div>
            <table id="tblProductionInfo" class="table table-sm table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th width="30">#</th>
                        <th width="80">Status</th>
                        <th>Produce / Variety</th>
                        <th width="160">Expected</th>
                        <th>Planted</th>
                        <th>Area</th>
                        <th>Mkt Price</th>
                        <th width="130">Farmer</th>
                        <th>Farm &amp; Location</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- ── Production History ───────────────────────────────── -->
    <div class="card shadow-sm">
        <div class="card-header py-2">
            <h6 class="m-0"><i class="fa fa-history text-info mr-1"></i> Production History — All Farmers</h6>
        </div>
        <div class="card-body p-2">
            <table id="tblProductionInfoCompleted" class="table table-sm table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th width="30">#</th>
                        <th width="80">Status</th>
                        <th>Produce / Variety</th>
                        <th width="160">Expected</th>
                        <th>Planted</th>
                        <th>Area</th>
                        <th>Mkt Price</th>
                        <th width="130">Farmer</th>
                        <th>Farm &amp; Location</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>
</section>

<!-- NOTE: Modal and productionMap are already loaded globally by Page.php — do NOT load again -->

<script>
$(function() {
    // ── Load tables ──────────────────────────────────────────
    getTable('ProductionInfo', 0, 10);
    getTable('ProductionInfoCompleted', 0, 10);
    saveForm('ProductionInfo', ['ProductionInfo'], null, 0, 10);

    // ── Status click handler via data-* (no inline JS) ───────
    $(document).on('click', '.prod-status-btn', function() {
        production_id_ = $(this).data('pid');
        $('#note').val($(this).data('note'));
        $('#productionStatus').val($(this).data('status'));
        $('#modalProductionStatus').modal('show');
    });

    // ── Search input: press Enter to search on map ────────────
    $('#searchProductionProduce').on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchProductionMap();
        }
    });
});

// ── computeProductionPrediction (called by Modal.php form inputs) ──────────
function computeProductionPrediction() {
    var produce_id      = $('#form_save_dataProductionInfo input[name=produce_id]').val();
    var days_to_harvest = parseInt($('#ProductionInfo').data('days_to_harvest')) || 0;
    var category        = $('#ProductionInfo').data('category');
    var harvest_freq    = parseInt($('#ProductionInfo').data('harvest_frequency')) || 0;
    var yield_per_sqm   = parseFloat($('#ProductionInfo').data('yield_per_sqm_as_kg')) || 0;

    var planted_date = $('[name=planted_date]').val();
    var last_harvest = $('[name=last_harvest_date]').val();
    var area         = parseFloat($('[name=area_sqm]').val()) || 0;
    var price        = parseFloat($('[name=market_price_per_kg]').val()) || 0;

    if (!planted_date || !area) return;

    var harvestDate;
    if (category == 'perennial' && last_harvest) {
        harvestDate = new Date(last_harvest);
        harvestDate.setDate(harvestDate.getDate() + harvest_freq);
    } else {
        harvestDate = new Date(planted_date);
        harvestDate.setDate(harvestDate.getDate() + days_to_harvest);
    }

    var expectedHarvestDate = harvestDate.toISOString().split('T')[0];
    var expected_yield      = area * yield_per_sqm;
    var expected_revenue    = expected_yield * price;

    var fmt_yield   = '~ ' + expected_yield.toFixed(2) + ' kg';
    var fmt_revenue = '~ ₱' + expected_revenue.toLocaleString('en-US', {
        minimumFractionDigits: 2, maximumFractionDigits: 2
    });

    // Update display
    $('#typicalHarvestDays').text(days_to_harvest);
    $('#yieldPerSqm').text(yield_per_sqm + ' kg');
    $('#areaPlanted').text(area + ' sqm');
    $('#expected_harvest_date').text('~ ' + expectedHarvestDate);
    $('#form_save_dataProductionInfo input[name=expected_harvest_date]').val(expectedHarvestDate);
    $('#expected_yield').text(fmt_yield);
    $('#form_save_dataProductionInfo input[name=expected_yield]').val(fmt_yield);
    $('#expected_revenue').text(fmt_revenue);
    $('#form_save_dataProductionInfo input[name=expected_revenue]').val(fmt_revenue);
    $('#predictionPanel').fadeIn();
}

// ── Update production status ─────────────────────────────────
function updateProductionStatus() {
    $.post("<?= base_url('userfarmer/OnProduction/updateProductionStatus') ?>", {
        production_id: production_id_,
        status: $('#productionStatus').val(),
        note: $('#modalProductionStatus #note').val()
    }, function(res) {
        var j = JSON.parse(res);
        if (j.success) {
            successAlert(j.message);
            getTable('ProductionInfo', 0, 10);
            getTable('ProductionInfoCompleted', 0, 10);
            $('#modalProductionStatus').modal('hide');
        } else {
            errorAlert(j.message);
        }
    });
}
</script>
