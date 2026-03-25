<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $uri = $this->session->agrishop_login_uri; $d = $dashboard; ?>

<style>
.sup-stat { border-radius:16px;padding:20px;background:#fff;border:none;box-shadow:0 2px 14px rgba(0,0,0,.07);transition:transform .18s,box-shadow .18s;position:relative;overflow:hidden; }
.sup-stat:hover { transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,.1); }
.sup-stat .s-val { font-size:28px;font-weight:900;line-height:1.1; }
.sup-stat .s-lbl { font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;margin-top:3px; }
.sup-stat .s-icon { position:absolute;right:18px;top:18px;width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px; }
.sup-stat .s-bar { position:absolute;bottom:0;left:0;right:0;height:3px;border-radius:0 0 16px 16px; }
.sup-card { border-radius:16px;border:none;box-shadow:0 2px 12px rgba(0,0,0,.06);overflow:hidden; }
.sup-card-hdr { padding:13px 18px;font-weight:700;font-size:13px;display:flex;align-items:center;gap:8px;background:#fff;border-bottom:1px solid #f3f4f6; }
.sup-row { display:flex;align-items:center;gap:12px;padding:10px 16px;border-bottom:1px solid #f9fafb;transition:background .15s; }
.sup-row:last-child { border-bottom:none; }
.sup-row:hover { background:#fffbf0; }
</style>

<div class="container-fluid py-3">

<!-- BILLING ALERT -->
<?php if ($billing['count'] > 0): ?>
<div class="alert d-flex align-items-center mb-3" style="border-radius:14px;background:linear-gradient(135deg,#fff3cd,#ffeeba);border:none;">
    <i class="fa fa-exclamation-triangle fa-lg mr-3 text-warning"></i>
    <div class="flex-grow-1">You have <strong><?= $billing['count'] ?></strong> unpaid invoice(s). Pay on time to keep your account active.</div>
    <a href="<?= base_url($uri.'/Billing') ?>" class="btn btn-sm btn-dark font-weight-bold rounded-pill px-3 ml-2">Pay Now</a>
</div>
<?php endif; ?>

<!-- STAT CARDS -->
<div class="row mb-4">

    <div class="col-6 col-md-3 mb-3">
        <div class="sup-stat">
            <div class="s-icon" style="background:#fff7ed;color:#f97316;"><i class="fa fa-money-bill-wave"></i></div>
            <div class="s-val" style="color:#f97316;">&#8369;<?= $d['revenue'] ?></div>
            <div class="s-lbl">Revenue this year</div>
            <div class="s-bar" style="background:#f97316;"></div>
        </div>
    </div>

    <div class="col-6 col-md-3 mb-3">
        <div class="sup-stat">
            <div class="s-icon" style="background:#eff6ff;color:#3b82f6;"><i class="fa fa-shopping-bag"></i></div>
            <div class="s-val" style="color:#3b82f6;"><?= $d['total_orders'] ?></div>
            <div class="s-lbl">Orders this year</div>
            <div class="s-bar" style="background:#3b82f6;"></div>
        </div>
    </div>

    <div class="col-6 col-md-3 mb-3">
        <div class="sup-stat">
            <div class="s-icon" style="background:#f0fdf4;color:#22c55e;"><i class="fa fa-boxes"></i></div>
            <div class="s-val" style="color:#22c55e;"><?= $d['total_supplies'] ?></div>
            <div class="s-lbl">Active Supplies</div>
            <div class="s-bar" style="background:#22c55e;"></div>
        </div>
    </div>

    <div class="col-6 col-md-3 mb-3">
        <div class="sup-stat">
            <div class="s-icon" style="background:#fdf4ff;color:#a855f7;"><i class="fa fa-store"></i></div>
            <div class="s-val" style="color:#a855f7;"><?= $d['total_stores'] ?></div>
            <div class="s-lbl">Stores / Locations</div>
            <div class="s-bar" style="background:#a855f7;"></div>
        </div>
    </div>

</div>

<!-- CHARTS ROW -->
<div class="row mb-4">
    <div class="col-lg-8 mb-3">
        <div class="sup-card card">
            <div class="sup-card-hdr"><i class="fa fa-chart-bar text-warning"></i> Monthly Revenue &amp; Qty Sold</div>
            <div class="card-body p-3"><canvas id="revenueChart" height="95"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4 mb-3">
        <div class="sup-card card h-100">
            <div class="sup-card-hdr"><i class="fa fa-tag text-warning"></i> Supply by Category</div>
            <div class="card-body p-3"><canvas id="categoryChart" height="175"></canvas></div>
        </div>
    </div>
</div>

<!-- TOP SUPPLIES TABLE -->
<div class="sup-card card mb-3">
    <div class="sup-card-hdr"><i class="fa fa-fire text-danger"></i> Top Selling Supplies</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0" style="font-size:13px;">
                <thead>
                    <tr style="background:#f9fafb;">
                        <th style="padding:10px 16px;font-weight:700;">#</th>
                        <th style="padding:10px 16px;font-weight:700;">Supply</th>
                        <th style="padding:10px 16px;font-weight:700;">Category</th>
                        <th style="padding:10px 16px;font-weight:700;">UOM</th>
                        <th class="text-right" style="padding:10px 16px;font-weight:700;">Price</th>
                        <th class="text-right" style="padding:10px 16px;font-weight:700;">Qty Sold</th>
                        <th class="text-right" style="padding:10px 16px;font-weight:700;">Total Sales</th>
                    </tr>
                </thead>
                <tbody id="topSuppliesBody">
                    <tr><td colspan="7" class="text-center text-muted py-4"><i class="fa fa-spinner fa-spin mr-1"></i> Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

</div><!-- /container-fluid -->

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
const ordersRaw  = <?= $d['ordersGraph'] ?>;
const catRaw     = <?= $d['categoryGraph'] ?>;
const topSupplies= <?= $d['top_supplies'] ?>;
Chart.defaults.font.size = 11;

// Monthly Revenue + Qty
new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: ordersRaw.map(r => r.mon),
        datasets: [{
            label: 'Revenue (₱)', data: ordersRaw.map(r => r.revenue || 0),
            backgroundColor: 'rgba(249,115,22,.75)', borderColor: '#f97316',
            borderWidth: 1, borderRadius: 6,
        },{
            label: 'Qty Sold', data: ordersRaw.map(r => r.qty || 0),
            type: 'line', borderColor: '#3b82f6', backgroundColor: 'transparent',
            tension: 0.4, yAxisID: 'y1', pointRadius: 4,
        }]
    },
    options: {
        responsive: true, interaction: { mode: 'index' },
        scales: {
            x: { grid: { display: false }, ticks: { color: '#9ca3af' } },
            y:  { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { color: '#9ca3af', callback: v => '₱'+v.toLocaleString() } },
            y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { color: '#9ca3af' } }
        },
        plugins: { legend: { labels: { boxWidth: 12, font: { size: 11 } } } }
    }
});

// Category Donut
new Chart(document.getElementById('categoryChart'), {
    type: 'doughnut',
    data: {
        labels: catRaw.map(r => r.category),
        datasets: [{ data: catRaw.map(r => r.count),
            backgroundColor: ['#f97316','#3b82f6','#22c55e','#a855f7','#eab308','#14b8a6','#ef4444'],
            borderWidth: 2, borderColor: '#fff'
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 10, font: { size: 11 } } } } }
});

// Top supplies
const tbody = document.getElementById('topSuppliesBody');
if (!topSupplies.length) {
    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No sales data yet.</td></tr>';
} else {
    tbody.innerHTML = topSupplies.map((s,i) => `
        <tr>
            <td style="padding:10px 16px;color:#9ca3af;">${i+1}</td>
            <td style="padding:10px 16px;font-weight:600;">${s.name}</td>
            <td style="padding:10px 16px;"><span class="badge badge-warning" style="font-size:10px;">${s.category}</span></td>
            <td style="padding:10px 16px;">${s.uom}</td>
            <td class="text-right" style="padding:10px 16px;">&#8369;${parseFloat(s.price).toLocaleString()}</td>
            <td class="text-right" style="padding:10px 16px;font-weight:600;">${parseFloat(s.qty_sold).toLocaleString()}</td>
            <td class="text-right" style="padding:10px 16px;font-weight:700;color:#f97316;">&#8369;${parseFloat(s.total_sales).toLocaleString()}</td>
        </tr>`).join('');
}
</script>
