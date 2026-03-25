<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if (!$this->session->agrishop_login_level) { redirect(base_url('login')); } ?>

<?php
$name      = $this->session->agrishop_login_first_name . ' ' . $this->session->agrishop_login_last_name;
$default   = ['img_path'=>'dist/img/media/icons/1x1.png','name'=>'—','qty'=>0,'price'=>0,'uom'=>'—'];

// Decode and sanitize — filter rows where name is null (orphaned transactions with no produce)
$p_selling_raw = json_decode($dashboard['p_selling'], true) ?? [];
$p_selling = [];
foreach ($p_selling_raw as $row) {
    if (empty($row['name'])) continue; // skip null-produce rows
    $p_selling[] = [
        'img_path' => $row['img_path'] ?? 'dist/img/media/icons/1x1.png',
        'name'     => $row['name']     ?? '—',
        'qty'      => $row['qty']      ?? 0,
        'price'    => $row['price']    ?? 0,
        'uom'      => $row['uom']      ?? '—',
    ];
}
// Pad to at least 4 entries so the grid always renders
while (count($p_selling) < 4) $p_selling[] = $default;

$top4  = array_slice($p_selling, 0, 4);
$slow2 = array_slice(array_reverse($p_selling), 0, 2);

// pending billing
$hasBilling = isset($billing) && $billing['count'] > 0;
?>

<!-- Page Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mt-2 mb-n1 align-items-center">
            <div class="col-sm-7">
                <h5 class="m-0 font-weight-bold">
                    <i class="fa fa-seedling text-success mr-1"></i>
                    Good day, <span class="text-success"><?= htmlspecialchars($name) ?></span>!
                </h5>
                <small class="text-muted"><?= date('l, F j, Y') ?></small>
            </div>
            <div class="col-sm-5 text-right">
                <?php if ($hasBilling): ?>
                    <a href="<?= base_url($this->session->agrishop_login_uri . '/Billing') ?>"
                       class="btn btn-sm btn-warning">
                        <i class="fa fa-exclamation-triangle mr-1"></i>
                        <?= $billing['count'] ?> Unpaid Invoice<?= $billing['count'] > 1 ? 's' : '' ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="content">
<div class="container-fluid">

<!-- ═══ STAT CARDS ═══════════════════════════════════════════════ -->
<div class="row mb-3">

    <div class="col-6 col-md-4 col-lg-2 mb-2">
        <div class="card shadow-sm h-100 border-0" style="border-left:4px solid #28a745!important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div style="font-size:11px;font-weight:600;color:#6c757d;text-transform:uppercase;letter-spacing:.05em;">Revenue</div>
                        <div style="font-size:22px;font-weight:800;color:#111;">₱<?= $dashboard['revenue'] ?></div>
                    </div>
                    <div style="width:36px;height:36px;border-radius:10px;background:#d4edda;display:flex;align-items:center;justify-content:center;">
                        <i class="fa fa-peso-sign text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-lg-2 mb-2">
        <div class="card shadow-sm h-100 border-0" style="border-left:4px solid #17a2b8!important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div style="font-size:11px;font-weight:600;color:#6c757d;text-transform:uppercase;letter-spacing:.05em;">Total Orders</div>
                        <div style="font-size:22px;font-weight:800;color:#111;"><?= $dashboard['total_orders'] ?></div>
                    </div>
                    <div style="width:36px;height:36px;border-radius:10px;background:#d1ecf1;display:flex;align-items:center;justify-content:center;">
                        <i class="fa fa-basket-shopping text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-lg-2 mb-2">
        <div class="card shadow-sm h-100 border-0" style="border-left:4px solid #ffc107!important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div style="font-size:11px;font-weight:600;color:#6c757d;text-transform:uppercase;letter-spacing:.05em;">Produce</div>
                        <div style="font-size:22px;font-weight:800;color:#111;"><?= $dashboard['products'] ?></div>
                    </div>
                    <div style="width:36px;height:36px;border-radius:10px;background:#fff3cd;display:flex;align-items:center;justify-content:center;">
                        <i class="fa fa-seedling text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-lg-2 mb-2">
        <div class="card shadow-sm h-100 border-0" style="border-left:4px solid #dc3545!important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div style="font-size:11px;font-weight:600;color:#6c757d;text-transform:uppercase;letter-spacing:.05em;">Farms</div>
                        <div style="font-size:22px;font-weight:800;color:#111;"><?= $dashboard['farms'] ?></div>
                    </div>
                    <div style="width:36px;height:36px;border-radius:10px;background:#f8d7da;display:flex;align-items:center;justify-content:center;">
                        <i class="fa fa-house-chimney text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-4 col-lg-2 mb-2">
        <div class="card shadow-sm h-100 border-0" style="border-left:4px solid #20c997!important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div style="font-size:11px;font-weight:600;color:#6c757d;text-transform:uppercase;letter-spacing:.05em;">On Production</div>
                        <div style="font-size:22px;font-weight:800;color:#111;"><?= $dashboard['on_production'] ?></div>
                    </div>
                    <div style="width:36px;height:36px;border-radius:10px;background:#d2f4ea;display:flex;align-items:center;justify-content:center;">
                        <i class="fa fa-leaf" style="color:#20c997;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick actions -->
    <div class="col-6 col-md-4 col-lg-2 mb-2">
        <div class="card shadow-sm h-100 border-0" style="background:linear-gradient(135deg,#28a745,#20c997);">
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div style="font-size:11px;font-weight:700;color:rgba(255,255,255,.85);text-transform:uppercase;letter-spacing:.05em;">Quick Actions</div>
                <div class="d-flex flex-column" style="gap:5px;margin-top:6px;">
                    <a href="<?= base_url($this->session->agrishop_login_uri . '/Orders') ?>"
                       class="btn btn-sm btn-light text-success font-weight-bold" style="font-size:11px;">
                        <i class="fa fa-shopping-basket mr-1"></i> View Orders
                    </a>
                    <a href="<?= base_url($this->session->agrishop_login_uri . '/FarmProduce') ?>"
                       class="btn btn-sm btn-light text-success font-weight-bold" style="font-size:11px;">
                        <i class="fa fa-tractor mr-1"></i> My Farms
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ═══ CHARTS ROW ════════════════════════════════════════════════ -->
<div class="row mb-3">

    <!-- Monthly Orders + Revenue line chart -->
    <div class="col-lg-8 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-header py-2 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold"><i class="fa fa-chart-line text-success mr-1"></i> Orders &amp; Revenue — <?= date('Y') ?></h6>
            </div>
            <div class="card-body p-2">
                <canvas id="ordersRevenueChart" height="90"></canvas>
            </div>
        </div>
    </div>

    <!-- Product Classification doughnut -->
    <div class="col-lg-4 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-header py-2">
                <h6 class="m-0 font-weight-bold"><i class="fa fa-chart-pie text-warning mr-1"></i> Produce by Category</h6>
            </div>
            <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center">
                <canvas id="classificationChart" height="160"></canvas>
            </div>
        </div>
    </div>

</div>

<!-- ═══ PRODUCE PERFORMANCE + WHOLESALE ROW ══════════════════════ -->
<div class="row mb-3">

    <!-- Top Selling Produce -->
    <div class="col-lg-4 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-success text-white py-2">
                <h6 class="m-0 font-weight-bold"><i class="fa fa-arrow-trend-up mr-1"></i> Top Selling Produce</h6>
            </div>
            <div class="card-body p-2">
                <?php foreach ($top4 as $i => $p): ?>
                <div class="d-flex align-items-center p-2 <?= $i < 3 ? 'border-bottom' : '' ?>" style="gap:10px;">
                    <img src="<?= base_url($p['img_path'] ?? 'dist/img/media/icons/1x1.png') ?>" width="42" height="42"
                         class="rounded" style="object-fit:cover;flex-shrink:0;"
                         onerror="this.src='<?= base_url('dist/img/media/icons/1x1.png') ?>'">
                    <div class="flex-grow-1">
                        <div style="font-size:13px;font-weight:600;color:#111;"><?= htmlspecialchars($p['name'] ?? '—') ?></div>
                        <div style="font-size:11px;color:#6c757d;">
                            ₱<?= number_format((float)($p['price'] ?? 0), 2) ?> / <?= htmlspecialchars($p['uom'] ?? '—') ?>
                        </div>
                    </div>
                    <span class="badge badge-success" style="font-size:12px;"><?= (int)($p['qty'] ?? 0) ?> sold</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Sales Volume bar chart -->
    <div class="col-lg-5 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-header py-2">
                <h6 class="m-0 font-weight-bold"><i class="fa fa-chart-bar text-primary mr-1"></i> Produce Sales Volume (Top 6)</h6>
            </div>
            <div class="card-body p-2">
                <canvas id="salesVolumeChart" height="160"></canvas>
            </div>
        </div>
    </div>

    <!-- Wholesale vs Retail -->
    <div class="col-lg-3 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-header py-2">
                <h6 class="m-0 font-weight-bold"><i class="fa fa-pie-chart text-danger mr-1"></i> Wholesale vs Retail</h6>
            </div>
            <div class="card-body p-2 d-flex flex-column align-items-center justify-content-center">
                <canvas id="wholesaleRetailChart" height="160"></canvas>
            </div>
        </div>
    </div>

</div>

<!-- ═══ SLOW MOVERS + RECENT ORDERS ══════════════════════════════ -->
<div class="row">

    <!-- Slow Moving Produce -->
    <div class="col-lg-4 mb-3">
        <div class="card shadow-sm">
            <div class="card-header bg-warning py-2">
                <h6 class="m-0 font-weight-bold"><i class="fa fa-arrow-trend-down mr-1"></i> Slow Moving Produce</h6>
            </div>
            <div class="card-body p-2">
                <?php foreach ($slow2 as $i => $p): ?>
                <div class="d-flex align-items-center p-2 <?= $i === 0 ? 'border-bottom' : '' ?>" style="gap:10px;">
                    <img src="<?= base_url($p['img_path'] ?? 'dist/img/media/icons/1x1.png') ?>" width="42" height="42"
                         class="rounded" style="object-fit:cover;flex-shrink:0;"
                         onerror="this.src='<?= base_url('dist/img/media/icons/1x1.png') ?>'">
                    <div class="flex-grow-1">
                        <div style="font-size:13px;font-weight:600;color:#111;"><?= htmlspecialchars($p['name'] ?? '—') ?></div>
                        <div style="font-size:11px;color:#6c757d;">
                            ₱<?= number_format((float)($p['price'] ?? 0), 2) ?> / <?= htmlspecialchars($p['uom'] ?? '—') ?>
                        </div>
                    </div>
                    <span class="badge badge-warning" style="font-size:12px;"><?= (int)($p['qty'] ?? 0) ?> sold</span>
                </div>
                <?php endforeach; ?>
                <div class="text-center p-3 text-muted" style="font-size:12px;">
                    <i class="fa fa-lightbulb mr-1 text-warning"></i>
                    Consider reducing prices or promoting these items.
                </div>
            </div>
        </div>
    </div>

    <!-- Pending orders quick view -->
    <div class="col-lg-8 mb-3">
        <div class="card shadow-sm">
            <div class="card-header py-2 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold"><i class="fa fa-clock text-info mr-1"></i> Incoming Orders</h6>
                <a href="<?= base_url($this->session->agrishop_login_uri . '/Orders') ?>"
                   class="btn btn-sm btn-outline-info" style="font-size:11px;">
                    View All <i class="fa fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="card-body p-1">
                <table id="tblDashRecentOrders" class="table table-sm table-hover mb-0" style="width:100%">
                    <thead class="thead-light">
                        <tr>
                            <th>Customer</th>
                            <th>Farm</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="5" class="text-center text-muted p-3">
                            <i class="fa fa-spinner fa-spin mr-1"></i> Loading...
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

</div><!-- /.container-fluid -->
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const GREEN  = '#28a745', TEAL = '#20c997', BLUE = '#007bff',
      ORANGE = '#fd7e14', RED  = '#dc3545', YELLOW = '#ffc107';

const ordersData          = <?= $dashboard['ordersGraph'] ?>;
const pSelling            = <?= $dashboard['p_selling'] ?>;
const classificationData  = <?= $dashboard['classificationGraph'] ?>;
const wholesaleRetailData = <?= $dashboard['wholesale_retail_graph'] ?>;

// ── 1. Orders + Revenue dual-line ───────────────────────────────
new Chart(document.getElementById('ordersRevenueChart'), {
    type: 'line',
    data: {
        labels: ordersData.map(d => d.mon),
        datasets: [
            {
                label: 'Orders',
                data: ordersData.map(d => d.qty),
                borderColor: GREEN, backgroundColor: 'rgba(40,167,69,.08)',
                borderWidth: 2.5, tension: .4, fill: true, yAxisID: 'y'
            },
            {
                label: 'Revenue (₱)',
                data: ordersData.map(d => d.revenue),
                borderColor: BLUE, backgroundColor: 'rgba(0,123,255,.05)',
                borderWidth: 2.5, tension: .4, fill: true, yAxisID: 'y1', borderDash: [5,3]
            }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { position: 'bottom', labels: { font: { size: 11 } } } },
        scales: {
            y:  { beginAtZero: true, position: 'left',  grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 } } },
            y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { font: { size: 10 }, callback: v => '₱' + v.toLocaleString() } }
        }
    }
});

// ── 2. Sales Volume bar ──────────────────────────────────────────
const top6 = pSelling.slice(0, 6);
new Chart(document.getElementById('salesVolumeChart'), {
    type: 'bar',
    data: {
        labels: top6.map(d => d.name.length > 10 ? d.name.substring(0,10) + '…' : d.name),
        datasets: [{
            label: 'Units Sold',
            data: top6.map(d => d.qty),
            backgroundColor: [GREEN, TEAL, BLUE, ORANGE, RED, YELLOW],
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { title: ctx => top6[ctx[0].dataIndex].name } }
        },
        scales: { y: { beginAtZero: true, grid: { color: '#f3f4f6' } } }
    }
});

// ── 3. Product Classification doughnut ──────────────────────────
const palettePie = [GREEN, TEAL, BLUE, ORANGE, RED, YELLOW, '#6f42c1', '#e83e8c'];
const totalClass = classificationData.reduce((s, d) => s + +d.count, 0);
new Chart(document.getElementById('classificationChart'), {
    type: 'doughnut',
    data: {
        labels: classificationData.map(d => d.class_name),
        datasets: [{ data: classificationData.map(d => d.count), backgroundColor: palettePie, hoverOffset: 6 }]
    },
    options: {
        cutout: '62%',
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 10 }, boxWidth: 12 } },
            tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw} (${((ctx.raw/totalClass)*100).toFixed(1)}%)` } }
        }
    }
});

// ── 4. Wholesale vs Retail pie ───────────────────────────────────
const wrQty  = wholesaleRetailData.map(d => +d.qty);
const wrRev  = wholesaleRetailData.map(d => +d.revenue);
const wrTot  = wrQty.reduce((s,v) => s+v, 0);
new Chart(document.getElementById('wholesaleRetailChart'), {
    type: 'pie',
    data: {
        labels: wholesaleRetailData.map(d => d.w_r),
        datasets: [{ data: wrQty, backgroundColor: [BLUE, GREEN], hoverOffset: 6 }]
    },
    options: {
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 10 }, boxWidth: 12 } },
            tooltip: { callbacks: { label: ctx => [
                ` Qty: ${wrQty[ctx.dataIndex]}`,
                ` Revenue: ₱${(+wrRev[ctx.dataIndex]).toLocaleString()}`,
                ` Share: ${((wrQty[ctx.dataIndex]/wrTot)*100).toFixed(1)}%`
            ]}}
        }
    }
});

// ── 5. Recent incoming orders mini-table ────────────────────────
$(function() {
    const uri = "<?= $this->session->agrishop_login_uri ?>";
    $.post(uri + '/Orders/getCartListing', {
        draw: 1, start: 0, length: 5,
        'search[value]': '', 'search[status]': null
    }, function(res) {
        try {
            var d = JSON.parse(res);
            // build simple rows from controller HTML
            // Actually use a quick dedicated query
        } catch(e) {}
    });

    // Simpler: quick ajax to get 5 most recent RESERVED orders
    $.post("<?= base_url($this->session->agrishop_login_uri . '/Orders/getCartListing') ?>", {
        draw: 1, length: 5, start: 0,
        'search[value]': '', 'search[status]': 'ACTIVE'
    }, function(res) {
        try {
            var d = JSON.parse(res);
            if (d.data && d.data.length > 0) {
                var tbody = $('#tblDashRecentOrders tbody');
                tbody.empty();
                d.data.forEach(function(row) {
                    // row[0] is HTML - extract info via DOM
                    var $el = $(row[0]);
                    tbody.append('<tr><td colspan="5">' + row[0] + '</td></tr>');
                });
            } else {
                $('#tblDashRecentOrders tbody').html(
                    '<tr><td colspan="5" class="text-center text-muted p-3"><i class="fa fa-check-circle text-success mr-1"></i> No incoming orders right now.</td></tr>'
                );
            }
        } catch(e) {}
    });
});
</script>
