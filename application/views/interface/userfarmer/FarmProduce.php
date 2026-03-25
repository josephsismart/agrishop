<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if (!$this->session->agrishop_login_level) {
    redirect(base_url('login'));
} ?>
<script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>
<?php
$uri       = $this->session->agrishop_login_uri;
$farmer_id = $this->session->agrishop_login_farmer_id;
$person_id = $this->session->agrishop_person_id;
$farms     = $this->db->query("
    SELECT t1.id, t1.farm_name, t1.img_path, t1.total_area_sqm, t1.barangay_id,
        (SELECT COUNT(1) FROM farm_produce WHERE farm_id=t1.id) AS produce_c,
        CONCAT(COALESCE(b.description,''), CASE WHEN b.description IS NOT NULL AND c.description IS NOT NULL THEN ', ' ELSE '' END, COALESCE(c.description,'')) AS farm_address
    FROM farmer_farm t1
    LEFT JOIN tbl_barangay b  ON t1.barangay_id = b.id
    LEFT JOIN tbl_citymun  c  ON b.citymun_id   = c.id
    WHERE t1.farmer_id=$farmer_id ORDER BY t1.farm_name
")->result();
?>

<style>
    /* ── Tabs ─────────────────────────────────────────── */
    .fp-tab {
        border-radius: 50px;
        padding: 7px 20px;
        font-weight: 600;
        font-size: 13px;
        border: 2px solid #dee2e6;
        cursor: pointer;
        transition: all .2s;
        background: #fff;
        color: #6c757d;
    }

    .fp-tab.active {
        background: #28a745;
        color: #fff;
        border-color: #28a745;
    }

    .fp-tab:not(.active):hover {
        background: #f8f9fa;
        color: #333;
    }

    /* ── Stat mini cards ──────────────────────────────── */
    .stat-m {
        border-radius: 14px;
        padding: 14px 16px;
        border: none;
        color: #fff;
    }

    .stat-m .n {
        font-size: 26px;
        font-weight: 900;
        line-height: 1;
    }

    .stat-m .l {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .05em;
        opacity: .85;
    }

    /* ── Farm cards ───────────────────────────────────── */
    .farm-card {
        border-radius: 16px;
        overflow: hidden;
        border: none;
        box-shadow: 0 2px 12px rgba(0, 0, 0, .08);
        transition: transform .2s, box-shadow .2s;
    }

    .farm-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, .14);
    }

    .farm-card .farm-img {
        width: 100%;
        height: 140px;
        object-fit: cover;
    }

    .farm-card .farm-body {
        padding: 14px;
    }

    .farm-card .farm-name {
        font-size: 15px;
        font-weight: 700;
        color: #111;
        margin-bottom: 4px;
    }

    .farm-card .farm-meta {
        font-size: 12px;
        color: #6c757d;
    }

    .farm-card .farm-badge {
        font-size: 11px;
    }

    /* ── Produce catalog cards ────────────────────────── */
    .produce-card {
        border-radius: 14px;
        overflow: hidden;
        border: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .07);
        transition: transform .2s, box-shadow .2s;
    }

    .produce-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, .12);
    }

    .produce-card .prod-img {
        width: 100%;
        height: 120px;
        object-fit: cover;
    }

    .produce-card .prod-body {
        padding: 12px;
    }

    .produce-card .prod-name {
        font-size: 15px;
        font-weight: 700;
        color: #111;
        margin-bottom: 3px;
    }

    .produce-card .prod-cat {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 6px;
    }

    /* ── Section card ─────────────────────────────────── */
    .s-card {
        border-radius: 14px;
        overflow: hidden;
        border: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .07);
    }

    .s-card .s-header {
        padding: 12px 16px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
</style>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mt-2 mb-n1 align-items-center">
            <div class="col">
                <h5 class="m-0 font-weight-bold">
                    <i class="fas fa-tractor text-success mr-1"></i> Farms &amp;
                    <i class="fas fa-carrot text-warning mr-1"></i> Produce
                </h5>
                <small class="text-muted">Manage your farms, listings, and produce catalog</small>
            </div>
            <div class="col-auto">
                <?php if (isset($billing) && $billing['count'] > 0) : ?>
                    <a href="<?= base_url($uri . '/Billing') ?>" class="btn btn-sm btn-warning">
                        <i class="fa fa-exclamation-triangle mr-1"></i><?= $billing['count'] ?> Unpaid
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        <!-- ── Stats ──────────────────────────────────────── -->
        <?php
        $st = $this->db->query("
    SELECT
        (SELECT COUNT(*) FROM farmer_farm WHERE farmer_id=$farmer_id AND is_active=1) AS farms,
        (SELECT COUNT(*) FROM farm_produce fp JOIN farmer_farm ff ON fp.farm_id=ff.id WHERE ff.farmer_id=$farmer_id) AS produce,
        (SELECT COUNT(*) FROM farmer_produce_production fpp
         JOIN farmer_farm ff2 ON fpp.farmer_farm_id=ff2.id
         JOIN (SELECT * FROM farmer_produce_production_status WHERE is_latest=1) fps ON fpp.id=fps.farmer_produce_production_id
         WHERE ff2.farmer_id=$farmer_id AND fps.status NOT IN ('COMPLETED','CANCELLED','DAMAGED')) AS on_prod,
        (SELECT COUNT(*) FROM transaction t JOIN farmer_farm ff3 ON t.farm_id=ff3.id
         JOIN (SELECT * FROM transaction_status WHERE is_latest=1) ts ON t.id=ts.transaction_id
         WHERE ff3.farmer_id=$farmer_id AND ts.status='RESERVED') AS new_orders
")->row();
        $stats = [
            ['n' => $st->farms ?? 0,     'l' => 'Active Farms',    'bg' => 'linear-gradient(135deg,#28a745,#20c997)'],
            ['n' => $st->produce ?? 0,   'l' => 'Listed Produce',  'bg' => 'linear-gradient(135deg,#fd7e14,#ffc107)'],
            ['n' => $st->on_prod ?? 0,   'l' => 'On Production',   'bg' => 'linear-gradient(135deg,#17a2b8,#0d6efd)'],
            ['n' => $st->new_orders ?? 0, 'l' => 'New Orders',       'bg' => 'linear-gradient(135deg,#dc3545,#e83e8c)'],
        ];
        ?>
        <div class="row mb-3">
            <?php foreach ($stats as $s) : ?>
                <div class="col-6 col-md-3 mb-2">
                    <div class="card stat-m" style="background:<?= $s['bg'] ?>;">
                        <div class="n"><?= $s['n'] ?></div>
                        <div class="l"><?= $s['l'] ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- ── Tabs ───────────────────────────────────────── -->
        <div class="d-flex mb-3" style="gap:8px;flex-wrap:wrap;">
            <button class="fp-tab active" id="tabFP" onclick="switchTab('FP')">
                <i class="fa fa-list mr-1"></i> Farm Produce
            </button>
            <button class="fp-tab" id="tabFarms" onclick="switchTab('Farms')">
                <i class="fa fa-tractor mr-1"></i> My Farms
            </button>
            <button class="fp-tab" id="tabCatalog" onclick="switchTab('Catalog')">
                <i class="fa fa-seedling mr-1"></i> Produce Catalog
            </button>
        </div>

        <!-- ══════════════════════════
     TAB 1 — FARM PRODUCE
══════════════════════════ -->
        <div id="paneFP">
            <div class="card s-card">
                <div class="s-header bg-success text-white">
                    <div class="d-flex align-items-center" style="gap:8px;">
                        <i class="fa fa-list mr-1"></i>
                        <select id="farmList" name="farmList" class="form-control form-control-sm" onchange="getTable('FarmProduceInfo',0,10)" style="max-width:240px;font-weight:600;border-radius:8px;">
                            <?php if (empty($farms)) : ?>
                                <option value="">— No farms yet —</option>
                                <?php else : foreach ($farms as $f) : ?>
                                    <option value="<?= $f->id ?>"><?= htmlspecialchars($f->farm_name) ?> (<?= $f->produce_c ?>)</option>
                            <?php endforeach;
                            endif; ?>
                        </select>
                        <span class="font-weight-bold" style="font-size:14px;">Farm Produce</span>
                    </div>
                    <button class="btn btn-sm btn-dark" onclick="var fl=$('#farmList').val();fl?$('#modalFarmProduceSupply').modal('show'):failAlert('Add a farm first.')">
                        <i class="fa fa-plus mr-1"></i> Add Produce
                    </button>
                </div>
                <div class="card-body p-1">
                    <div class="px-2 pt-2 mb-1" style="font-size:12px;color:#17a2b8;">
                        <i class="fa fa-info-circle mr-1"></i>
                        Click <b>🏷️ price</b> to manage pricing.
                        Click <b>👁️ Remove Posting</b> to hide from buyers temporarily.
                    </div>
                    <?php if (empty($farms)) : ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fa fa-tractor fa-3x mb-3 d-block text-muted"></i>
                            <p>No farms yet. <a href="#" onclick="switchTab('Farms')">Add your first farm →</a></p>
                        </div>
                    <?php else : ?>
                        <table id="tblFarmProduceInfo" class="table table-sm table-hover table-striped mb-0" style="width:100%">
                            <thead class="thead-light">
                                <tr>
                                    <th width="120">Action</th>
                                    <th width="60">Img</th>
                                    <th>Produce</th>
                                    <th>Harvest</th>
                                    <th>Stock</th>
                                    <th>Price</th>
                                    <th>Season</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════
     TAB 2 — MY FARMS (card grid)
══════════════════════════ -->
        <div id="paneFarms" style="display:none;">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="m-0 font-weight-bold"><i class="fa fa-tractor text-primary mr-1"></i> My Farms</h6>
                <button class="btn btn-primary btn-sm font-weight-bold" onclick="openFarmModal()">
                    <i class="fa fa-plus mr-1"></i> Add New Farm
                </button>
            </div>

            <div class="row" id="farmCardsContainer">
                <?php if (empty($farms)) : ?>
                    <div class="col-12 text-center py-5 text-muted">
                        <i class="fa fa-tractor fa-4x mb-3 d-block"></i>
                        <h5>No farms yet</h5>
                        <p>Add your first farm to start listing produce.</p>
                        <button class="btn btn-success" onclick="openFarmModal()">
                            <i class="fa fa-plus mr-1"></i> Add Farm
                        </button>
                    </div>
                <?php else : ?>
                    <?php foreach ($farms as $f) :
                        $img_src = (!empty($f->img_path) && file_exists(FCPATH . $f->img_path))
                            ? base_url($f->img_path)
                            : base_url('dist/img/media/icons/1x1.png');
                        $addr = !empty($f->farm_address) ? $f->farm_address : '—';
                    ?>
                        <div class="col-sm-6 col-md-4 col-lg-3 mb-3">
                            <div class="card farm-card">
                                <img src="<?= $img_src ?>" class="farm-img" onerror="this.src='<?= base_url('dist/img/media/icons/1x1.png') ?>'">
                                <div class="farm-body">
                                    <div class="farm-name"><?= htmlspecialchars($f->farm_name) ?></div>
                                    <div class="farm-meta mb-2">
                                        <i class="fa fa-map-marker-alt mr-1 text-danger"></i><?= htmlspecialchars($addr ?: '—') ?><br>
                                        <i class="fa fa-ruler-combined mr-1 text-info"></i><?= number_format($f->total_area_sqm) ?> sqm
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="badge badge-info farm-badge"><?= $f->produce_c ?> produce</span>
                                        <button class="btn btn-sm btn-warning font-weight-bold" onclick="editFarm(<?= $f->id ?>)">
                                            <i class="fa fa-edit mr-1"></i> Edit
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>

        <!-- ══════════════════════════
     TAB 3 — PRODUCE CATALOG (card grid)
══════════════════════════ -->
        <div id="paneCatalog" style="display:none;">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="m-0 font-weight-bold"><i class="fa fa-seedling text-warning mr-1"></i> Produce Catalog</h6>
                <button class="btn btn-warning btn-sm font-weight-bold" data-toggle="modal" data-target="#modalProduceInfo">
                    <i class="fa fa-plus mr-1"></i> Add Custom Produce
                </button>
            </div>

            <!-- Search bar for catalog -->
            <div class="input-group mb-3" style="max-width:380px;">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-white"><i class="fa fa-search text-muted"></i></span>
                </div>
                <input type="text" id="catalogSearch" class="form-control border-left-0"
                       placeholder="Search produce name, category..."
                       oninput="filterProduceCatalog(this.value)">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button"
                            onclick="$('#catalogSearch').val(''); filterProduceCatalog('');">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Produce rendered as cards via AJAX into this container -->
            <div id="produceCardsContainer">
                <div class="text-center py-4 text-muted">
                    <i class="fa fa-spinner fa-spin mr-1"></i> Loading...
                </div>
            </div>
            <!-- Hidden DataTable drives pagination and search -->
            <table id="tblProduceInfo" style="display:none">
                <thead>
                    <tr>
                        <th>img</th>
                        <th>name</th>
                        <th>cat</th>
                        <th>season</th>
                        <th>status</th>
                        <th>actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div id="producePagination" class="d-flex justify-content-center mt-3"></div>

        </div>

    </div>
</section>

<!-- ── Edit Farm Modal ─────────────────────────────────────── -->
<div class="modal fade" id="modalEditFarm" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa fa-edit mr-2"></i><span id="editFarmTitle">Add / Edit Farm</span></h5>
                <button class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="form_save_dataEditFarm" enctype="multipart/form-data">
                    <input type="hidden" name="farm_id" id="editFarmId" value="0">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <div onclick="$('#editFarmImg').click()" style="width:100%;height:150px;border:2px dashed #007bff;border-radius:12px;
                             cursor:pointer;overflow:hidden;background:#f0f8ff;
                             display:flex;align-items:center;justify-content:center;">
                                <img id="editFarmImgPreview" src="<?= base_url('dist/img/media/icons/1x1.png') ?>" style="width:100%;height:100%;object-fit:cover;">
                            </div>
                            <input type="file" id="editFarmImg" name="picFarm" accept="image/*" hidden onchange="previewImg(this,'editFarmImgPreview')">
                            <small class="text-muted">Click to change photo</small>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="font-weight-bold">Farm Name <span class="text-danger">*</span></label>
                                <input type="text" name="farmName" id="editFarmName" class="form-control text-uppercase" placeholder="e.g. MALIGAYA FARM" required>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold">Barangay</label>
                                <div class="position-relative">
                                    <input type="hidden" name="barangay" id="editFarmBarangayId">
                                    <input type="text" class="form-control barangayInput" id="editFarmBarangayText" data-target="editFarmBarangayId" placeholder="Type barangay..." autocomplete="off">
                                    <ul class="list-group barangayResults shadow" style="position:absolute;z-index:9999;width:100%;display:none;cursor:pointer;top:38px;"></ul>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Area (sqm)</label>
                                        <input type="number" name="totalAreaSqm" id="editFarmArea" class="form-control" placeholder="e.g. 5000">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold"><i class="fa fa-map-pin mr-1 text-danger"></i> Location on Map
                            <small class="text-muted font-weight-normal"> — click to set coordinates</small>
                        </label>
                        <div id="editFarmMapDiv" style="height:240px;border-radius:10px;overflow:hidden;"></div>
                        <input type="hidden" name="lat" id="editFarmLat">
                        <input type="hidden" name="lon" id="editFarmLon">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button class="btn btn-primary font-weight-bold" onclick="saveFarmEdit()">
                    <i class="fa fa-save mr-1"></i> Save Farm
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Price History Modal -->
<div class="modal fade" id="modalPriceHistory">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fa fa-tags mr-2"></i> Price Manager — <span id="priceHistoryProduceName"></span></h5>
                <button class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="card border-info mb-3">
                    <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center" style="gap:8px;">
                        <div style="flex:1;">
                            <div class="d-flex align-items-center mb-1" style="gap:6px;">
                                <span style="font-size:18px;">🤖</span>
                                <span class="text-muted" style="font-size:12px;font-weight:600;">Claude AI Suggested Price</span>
                            </div>
                            <div style="font-size:24px;font-weight:700;color:#2980b9;">₱<span id="aiSuggestedPrice">—</span></div>
                            <div id="aiPriceReason" class="text-muted" style="font-size:12px;margin-top:2px;"></div>
                        </div>
                        <button class="btn btn-sm btn-info" id="btnUseAiPrice" disabled>
                            <i class="fa fa-magic mr-1"></i> Use This Price
                        </button>
                    </div>
                </div>
                <div class="card border-success mb-3">
                    <div class="card-header bg-success text-white py-2">
                        <h6 class="m-0"><i class="fa fa-edit mr-1"></i> Update Price</h6>
                    </div>
                    <?= form_open(base_url($uri . '/FarmProduce/updatePrice'), 'id="form_save_dataUpdatePrice"') ?>
                    <input type="hidden" name="fp_id" id="updatePriceFpId">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label style="font-size:13px;">Retail Price (₱) *</label>
                                <input type="number" step="0.01" name="price" id="newRetailPrice" class="form-control form-control-sm" placeholder="0.00" required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label style="font-size:13px;">Wholesale Price (₱)</label>
                                <input type="number" step="0.01" name="price_wholesale" id="newWholesalePrice" class="form-control form-control-sm" placeholder="Optional" nr="1">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label style="font-size:13px;">Min Qty for Wholesale</label>
                                <input type="number" name="wholesale_at_qty" id="newWholesaleQty" class="form-control form-control-sm" placeholder="e.g. 10" nr="1">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer py-2">
                        <button type="submit" class="btn btn-sm btn-success submitBtnPrimary">
                            <i class="fa fa-save mr-1"></i> Update Price
                        </button>
                    </div>
                    <?= form_close() ?>
                </div>
                <h6 class="font-weight-bold"><i class="fa fa-history mr-1"></i> Price History</h6>
                <table class="table table-sm table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Date</th>
                            <th>Retail</th>
                            <th>Wholesale</th>
                            <th>Min Qty</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="priceHistoryBody">
                        <tr>
                            <td colspan="5" class="text-center text-muted">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    var currentFpId = null;
    var editFarmMap = null;
    var editFarmMarker = null;

    $(function() {
        getTable('FarmInfo', 0, 10);
        saveForm('FarmInfo', ['FarmInfo'], null, 0, 10);
        getTable('ProduceInfo', 0, 20);
        saveForm('ProduceInfo', ['ProduceInfo'], null, 0, 20);
        getTable('FarmProduceInfo', 0, 10);
        saveForm('FarmProduceSupply', ['FarmProduceInfo'], null, 0, 10);
        saveForm('AddFarmProduceSupply', ['FarmProduceInfo'], null, 0, 10);
        saveForm('UpdateProfile', [null], null);
        saveForm('UpdatePrice', ['FarmProduceInfo'], null, 0, 10);

        $('#form_save_dataUpdatePrice').ajaxForm({
            beforeSubmit: function() {
                if (!$('#newRetailPrice').val()) {
                    failAlert('Please enter a retail price.');
                    return false;
                }
            },
            success: function(data) {
                var d = JSON.parse(data);
                d.success ? (successAlert(d.message), loadPriceHistory(currentFpId), getTable('FarmProduceInfo', 0, 10)) :
                    failAlert(d.message || 'Error!');
            }
        });

        $('#btnUseAiPrice').click(function() {
            var p = $(this).data('price');
            if (p) $('#newRetailPrice').val(p).focus();
        });

        // Render produce cards when ProduceInfo DataTable draws
        $('#tblProduceInfo').on('draw.dt', function() {
            renderProduceCards();
        });
    });

    // ── Tab switch ────────────────────────────────────
    function switchTab(t) {
        $('#tabFP,#tabFarms,#tabCatalog').removeClass('active');
        $('#paneFP,#paneFarms,#paneCatalog').hide();
        $('#tab' + t).addClass('active');
        $('#pane' + t).show();
    }

    // ── Render produce as cards ───────────────────────
    // ── Produce Catalog search ────────────────────────
    var _catalogSearchTimer = null;
    function filterProduceCatalog(val) {
        clearTimeout(_catalogSearchTimer);
        _catalogSearchTimer = setTimeout(function() {
            // Push the value into the hidden DataTable's search box and redraw
            var dt = $('#tblProduceInfo').DataTable();
            dt.search(val).draw();
        }, 300);
    }

    function renderProduceCards() {
        var rows = $('#tblProduceInfo tbody tr');
        if (!rows.length || rows.first().find('td').length <= 1) {
            $('#produceCardsContainer').html(
                '<div class="text-center py-5 text-muted"><i class="fa fa-seedling fa-3x mb-3 d-block"></i><p>No produce found.</p></div>'
            );
            return;
        }
        var html = '<div class="row">';
        rows.each(function() {
            var tds = $(this).find('td');
            var img = $(tds[0]).find('img').attr('src') || '<?= base_url("dist/img/media/icons/1x1.png") ?>';
            var name = $(tds[1]).text().trim();
            var cat = $(tds[2]).text().trim();
            var season = tds[3] ? $(tds[3]).html() : '';
            var status = tds[4] ? $(tds[4]).html() : '';
            var action = tds[5] ? $(tds[5]).html() : '';
            var isCustom = $(tds[1]).find('.badge-orange,.bg-orange').length > 0;

            html += '<div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">' +
                '<div class="card produce-card">' +
                '<img src="' + img + '" class="prod-img" onerror="this.src=\'<?= base_url("dist/img/media/icons/1x1.png") ?>\'"">' +
                (isCustom ? '<div style="position:absolute;top:6px;right:6px;background:#fd7e14;color:#fff;font-size:9px;font-weight:700;padding:2px 7px;border-radius:10px;">CUSTOM</div>' : '') +
                '<div class="prod-body">' +
                '<div class="prod-name">' + name.replace(/<[^>]*>/g, '') + '</div>' +
                '<div class="prod-cat">' + cat + '</div>' +
                '<div class="d-flex justify-content-between align-items-center" style="gap:4px;">' +
                '<div>' + season + '</div>' +
                '<div>' + status + '</div>' +
                '</div>' +
                (action ? '<div class="mt-2">' + action + '</div>' : '') +
                '</div></div></div>';
        });
        html += '</div>';
        $('#produceCardsContainer').html(html);
    }

    // ── Remove posting ────────────────────────────────
    function removePosting(fpId) {
        Swal.fire({
            title: 'Remove Posting?',
            html: 'This will <b>hide</b> this produce from buyer searches.<br>Your data is NOT deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa fa-eye-slash mr-1"></i> Hide from Buyers'
        }).then(function(r) {
            if (!r.isConfirmed) return;
            $.post("<?= base_url($uri . '/FarmProduce/removePosting') ?>", {
                fp_id: fpId
            }, function(res) {
                var d = JSON.parse(res);
                d.success ? (successAlert(d.message), getTable('FarmProduceInfo', 0, 10)) : failAlert(d.message);
            });
        });
    }

    // ── Farm modal ────────────────────────────────────
    function openFarmModal(id) {
        $('#editFarmTitle').text(id ? 'Edit Farm' : 'Add New Farm');
        $('#editFarmId').val(id || 0);
        if (!id) {
            document.getElementById('form_save_dataEditFarm').reset();
            $('#editFarmId').val(0);
            $('#editFarmImgPreview').attr('src', '<?= base_url("dist/img/media/icons/1x1.png") ?>');
        } else {
            $.get("<?= base_url($uri . '/FarmProduce/getFarmById') ?>?id=" + id, function(res) {
                var d = JSON.parse(res);
                if (!d) return;
                $('#editFarmName').val(d.farm_name);
                $('#editFarmBarangayText').val(d.barangay_text || '');
                $('#editFarmBarangayId').val(d.barangay_id || '');
                $('#editFarmArea').val(d.total_area_sqm);
                $('#editFarmLat').val(d.lat);
                $('#editFarmLon').val(d.lon);
                if (d.img_path) $('#editFarmImgPreview').attr('src', '<?= base_url() ?>' + d.img_path);
                if (d.lat && d.lon && editFarmMap) {
                    var ll = [parseFloat(d.lat), parseFloat(d.lon)];
                    editFarmMap.setView(ll, 14);
                    if (editFarmMarker) editFarmMap.removeLayer(editFarmMarker);
                    editFarmMarker = L.marker(ll).addTo(editFarmMap);
                }
            });
        }
        $('#modalEditFarm').modal('show');
        setTimeout(function() {
            if (!editFarmMap) {
                editFarmMap = L.map('editFarmMapDiv').setView([8.9456, 125.5416], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(editFarmMap);
                editFarmMap.on('click', function(e) {
                    if (editFarmMarker) editFarmMap.removeLayer(editFarmMarker);
                    editFarmMarker = L.marker(e.latlng).addTo(editFarmMap);
                    $('#editFarmLat').val(e.latlng.lat.toFixed(6));
                    $('#editFarmLon').val(e.latlng.lng.toFixed(6));
                });
            }
            editFarmMap.invalidateSize();
        }, 400);
    }

    function editFarm(id) {
        openFarmModal(id);
    }

    function saveFarmEdit() {
        var farmId = parseInt($('#editFarmId').val()) || 0;
        var url = farmId > 0 ?
            "<?= base_url($uri . '/FarmProduce/updateFarmInfo') ?>" :
            "<?= base_url($uri . '/FarmProduce/saveFarmInfo') ?>";
        $('#form_save_dataEditFarm').ajaxSubmit({
            url: url,
            type: 'POST',
            success: function(res) {
                var d = JSON.parse(res);
                if (d.success) {
                    successAlert(d.message);
                    $('#modalEditFarm').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 1200);
                } else failAlert(d.message || 'Something went wrong!');
            },
            error: function() {
                failAlert('Something went wrong!');
            }
        });
    }

    // ── Custom produce edit ───────────────────────────
    function editProduceItem(id) {
        // Reuse the existing modalProduceInfo for edit
        $.get("<?= base_url($uri . '/FarmProduce/getProduceById') ?>?id=" + id, function(res) {
            var d = JSON.parse(res);
            if (!d) {
                failAlert('Produce not found.');
                return;
            }
            $('[name=produce_id_edit]').val(d.id);
            $('[name=produceName]').val(d.name);
            $('[name=classification]').val(d.produce_classification_id);
            $('[name=description]').val(d.description);
            $('[name=seasonal]').prop('checked', d.is_seasonal == 1);
            $('[name=tags]').val(d.tags);
            $('#modalProduceInfo').modal('show');
        });
    }

    // ── Price manager ─────────────────────────────────
    function openPriceManager(fpId, produceName, currentPrice) {
        currentFpId = fpId;
        $('#updatePriceFpId').val(fpId);
        $('#newRetailPrice').val(currentPrice || '');
        $('#newWholesalePrice,#newWholesaleQty').val('');
        $('#priceHistoryProduceName').text(produceName);
        $('#aiSuggestedPrice').text('...');
        $('#aiPriceReason').html('<i class="fa fa-spinner fa-spin mr-1"></i> Claude is thinking...');
        $('#btnUseAiPrice').prop('disabled', true);
        loadPriceHistory(fpId);
        $('#modalPriceHistory').modal('show');

        // 1. Fetch farm stats from backend
        $.post("<?= base_url($uri . '/FarmProduce/getPrice') ?>", { produce_id: fpId }, function(res) {
            var d;
            try { d = JSON.parse(res); } catch(e) { d = null; }

            var statsText = d
                ? 'Produce: ' + produceName +
                  '\nCurrent retail price: ₱' + (d.current_price || currentPrice || 'unknown') +
                  '\nQty sold: ' + (d.qty_sold || 0) +
                  '\nQty remaining in stock: ' + (d.qty_left || 0) +
                  '\nHarvest schedule: ' + (d.harvest_schedule || 'not specified')
                : 'Produce: ' + produceName + '\nCurrent retail price: ₱' + (currentPrice || 'unknown');

            // 2. Call Anthropic API directly from the browser
            fetch('https://api.anthropic.com/v1/messages', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    model: 'claude-sonnet-4-6',
                    max_tokens: 1000,
                    system: 'You are an expert agricultural pricing advisor for small-scale Filipino farmers in the Caraga region. ' +
                            'Analyze the farm data and suggest a fair retail price per unit. ' +
                            'Respond ONLY in this exact JSON format with no extra text: ' +
                            '{"suggested_price": <number>, "reason": "<1-2 sentence explanation in simple English>"}',
                    messages: [{
                        role: 'user',
                        content: 'Based on the following farm produce data, suggest a fair retail price:\n\n' + statsText +
                                 '\n\nConsider: supply vs demand, typical Caraga market prices, and farmer profitability. ' +
                                 'Return only the JSON object.'
                    }]
                })
            })
            .then(function(r) { return r.json(); })
            .then(function(apiData) {
                var text = (apiData.content && apiData.content[0] && apiData.content[0].text) || '';
                // Strip any accidental markdown fences
                text = text.replace(/```json|```/g, '').trim();
                var result;
                try { result = JSON.parse(text); } catch(e) { result = null; }

                if (result && result.suggested_price) {
                    var price = parseFloat(result.suggested_price).toFixed(2);
                    $('#aiSuggestedPrice').text(price);
                    $('#aiPriceReason').text(result.reason || '');
                    $('#btnUseAiPrice').data('price', price).prop('disabled', false);
                } else {
                    $('#aiSuggestedPrice').text('N/A');
                    $('#aiPriceReason').text('Could not generate a suggestion right now.');
                }
            })
            .catch(function() {
                $('#aiSuggestedPrice').text('N/A');
                $('#aiPriceReason').text('AI service unavailable. Check your connection.');
            });
        });
    }

    function loadPriceHistory(fpId) {
        $('#priceHistoryBody').html('<tr><td colspan="5" class="text-center"><i class="fa fa-spinner fa-spin"></i></td></tr>');
        $.post("<?= base_url($uri . '/FarmProduce/getPriceHistory') ?>", {
            fp_id: fpId
        }, function(res) {
            var d = JSON.parse(res);
            if (!d.length) {
                $('#priceHistoryBody').html('<tr><td colspan="5" class="text-center text-muted">No history.</td></tr>');
                return;
            }
            var rows = '';
            d.forEach(function(r, i) {
                rows += '<tr' + (i === 0 ? ' class="table-success"' : '') + '><td>' + r.created_at + '</td>' +
                    '<td>₱' + parseFloat(r.price).toFixed(2) + '</td>' +
                    '<td>' + (r.price_wholesale ? '₱' + parseFloat(r.price_wholesale).toFixed(2) : '—') + '</td>' +
                    '<td>' + (r.wholesale_at_qty || '—') + '</td>' +
                    '<td>' + (i === 0 ? '<span class="badge badge-success">Current</span>' : '<span class="badge badge-secondary">Old</span>') + '</td></tr>';
            });
            $('#priceHistoryBody').html(rows);
        });
    }

    function previewImg(input, previewId) {
        if (!input.files[0]) return;
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#' + previewId).attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
</script>