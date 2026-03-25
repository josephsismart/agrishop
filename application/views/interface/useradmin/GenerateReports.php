<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
if (!$this->session->agrishop_login_level) {
    redirect(base_url('login'));
}
$uri = $this->session->agrishop_login_uri;
?>

<!-- Content Header -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mt-2">
      <div class="col-sm-6">
        <h1><i class="nav-icon fas fa-chart-bar text-success"></i> Generate Reports</h1>
        <small class="text-muted">Create and customize reports by selecting the type of report, date range, and additional filters.</small>
      </div>
      <div class="col-sm-6 text-right d-flex align-items-center justify-content-end">
        <span class="badge badge-light border px-3 py-2" style="font-size:11px;">
          <i class="fa fa-calendar-alt text-success mr-1"></i><?= date('F j, Y') ?>
        </span>
      </div>
    </div>
  </div>
</section>

<section class="content">
<div class="container-fluid">

<!-- ═══════════════════════════════════════════════════
     FILTER PANEL
════════════════════════════════════════════════════ -->
<div class="card card-success card-outline">
  <div class="card-header">
    <h3 class="card-title"><i class="fa fa-filter mr-1"></i> <strong>FILTER PANEL</strong></h3>
    <div class="card-tools">
      <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
    </div>
  </div>
  <div class="card-body">
    <div class="row">

      <!-- Report Type -->
      <div class="col-md-4">
        <div class="form-group">
          <label class="text-muted font-weight-bold" style="font-size:11px;text-transform:uppercase;letter-spacing:.06em;">
            Report Type <span class="text-danger">*</span>
          </label>
          <select id="rptType" class="form-control form-control-sm">
            <option value="comprehensive">Comprehensive System Report</option>
            <option value="sales">Sales Report</option>
            <option value="farmers">Farmers Performance Report</option>
            <option value="products">Product Movement Report</option>
            <option value="orders">Orders Report</option>
            <option value="users">Users Report</option>
          </select>
          <small class="text-muted">Select the type of report you want to generate</small>
        </div>
      </div>

      <!-- Date Range -->
      <div class="col-md-8">
        <div class="form-group">
          <label class="text-muted font-weight-bold" style="font-size:11px;text-transform:uppercase;letter-spacing:.06em;">
            Date Range <span class="text-danger">*</span>
          </label>
          <div class="d-flex align-items-center" style="gap:8px;">
            <div class="input-group input-group-sm">
              <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
              <input type="date" id="dateFrom" class="form-control form-control-sm" value="<?= date('Y-m-01') ?>" placeholder="Start Date">
            </div>
            <span class="font-weight-bold text-muted px-1">-</span>
            <div class="input-group input-group-sm">
              <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
              <input type="date" id="dateTo" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" placeholder="End Date">
            </div>
          </div>
          <div class="mt-1 d-flex" style="gap:6px;flex-wrap:wrap;">
            <button class="btn btn-xs btn-outline-secondary rpt-quick active" onclick="setQuick('today',this)">Today</button>
            <button class="btn btn-xs btn-outline-secondary rpt-quick" onclick="setQuick('week',this)">This Week</button>
            <button class="btn btn-xs btn-outline-success rpt-quick" onclick="setQuick('month',this)">This Month</button>
            <button class="btn btn-xs btn-outline-secondary rpt-quick" onclick="setQuick('year',this)">This Year</button>
          </div>
        </div>
      </div>

      <!-- Additional Filters -->
      <div class="col-md-12"><label class="text-muted font-weight-bold" style="font-size:11px;text-transform:uppercase;letter-spacing:.06em;">Additional Filters</label><small class="text-muted ml-2">These filters help refine the report data</small></div>

      <div class="col-md-4">
        <div class="form-group">
          <select id="rptProduceType" class="form-control form-control-sm">
            <option value="all">All Product Types</option>
            <?php foreach ($produce_types as $pt): ?>
            <option value="<?= $pt['id'] ?>"><?= htmlspecialchars($pt['class_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          <select id="rptFarmer" class="form-control form-control-sm">
            <option value="all">All Farmers</option>
            <?php foreach ($farmers as $f): ?>
            <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['full_name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="col-md-4">
        <div class="form-group">
          <select id="rptOrderStatus" class="form-control form-control-sm">
            <option value="all">All Order Status</option>
            <option value="COMPLETED">Completed</option>
            <option value="RESERVED">Reserved</option>
            <option value="PREPARING">Preparing</option>
            <option value="TO_PICKUP">Ready for Pickup</option>
            <option value="TO_DELIVER">Out for Delivery</option>
          </select>
        </div>
      </div>

      <!-- Output Format -->
      <div class="col-md-4">
        <div class="form-group">
          <label class="text-muted font-weight-bold" style="font-size:11px;text-transform:uppercase;letter-spacing:.06em;">Output Format</label>
          <select id="rptFormat" class="form-control form-control-sm">
            <option value="pdf">PDF - Portable Document Format</option>
            <option value="print">Print / Preview</option>
            <option value="excel">Excel Spreadsheet</option>
          </select>
          <small class="text-muted">Choose the export format for your report</small>
        </div>
      </div>

    </div><!-- /row -->

    <div class="row mt-2">
      <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
          <button class="btn btn-success btn-sm font-weight-bold" onclick="generateReport()">
            <i class="fa fa-chart-bar mr-1"></i> Generate Report
          </button>
          <button class="btn btn-default btn-sm ml-2" onclick="resetFilters()">
            <i class="fa fa-undo mr-1"></i> Reset
          </button>
        </div>
        <div id="exportDropWrap" style="display:none;">
          <div class="dropdown">
            <button class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-download mr-1"></i> Export
            </button>
            <div class="dropdown-menu dropdown-menu-right">
              <a class="dropdown-item" href="#" onclick="doPrint()"><i class="fa fa-print mr-2 text-primary"></i>Print Report</a>
              <a class="dropdown-item" href="#" onclick="doPDF()"><i class="fa fa-file-pdf mr-2 text-danger"></i>Download PDF</a>
              <a class="dropdown-item" href="#" onclick="doExcel()"><i class="fa fa-file-excel mr-2 text-success"></i>Export to Excel</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div><!-- /card-body -->
</div><!-- /card -->

<!-- Loading -->
<div id="rptLoading" style="display:none;" class="text-center py-5">
  <div class="spinner-border text-success" role="status" style="width:3rem;height:3rem;"></div>
  <div class="mt-2 text-muted font-weight-bold">Generating report, please wait…</div>
</div>

<!-- ═══════════════════════════════════════════════════
     REPORT PREVIEW
════════════════════════════════════════════════════ -->
<div id="rptPreviewWrap" style="display:none;">

  <!-- Preview bar -->
  <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap" style="gap:8px;">
    <div class="d-flex align-items-center" style="gap:10px;">
      <i class="fa fa-eye text-success fa-lg"></i>
      <span class="font-weight-bold" style="font-size:14px;">REPORT PREVIEW</span>
      <span id="rptReadyBadge" class="badge badge-success px-3 py-1" style="font-size:11px;"></span>
    </div>
    <div class="d-flex" style="gap:8px;flex-wrap:wrap;">
      <button class="btn btn-sm btn-primary" onclick="doPrint()"><i class="fa fa-print mr-1"></i>Print Report</button>
      <button class="btn btn-sm btn-danger"  onclick="doPDF()"><i class="fa fa-file-pdf mr-1"></i>Download PDF</button>
      <button class="btn btn-sm btn-success" onclick="doExcel()"><i class="fa fa-file-excel mr-1"></i>Export to Excel</button>
    </div>
  </div>

  <!-- ─── PRINTABLE REPORT CONTENT ─── -->
  <div class="card" id="rptContent">

    <!-- Report title header -->
    <div style="background:linear-gradient(135deg,#1a472a,#15803d,#22c55e);color:#fff;padding:24px 28px;border-radius:4px 4px 0 0;">
      <div class="d-flex justify-content-between align-items-start flex-wrap" style="gap:12px;">
        <div>
          <div style="font-size:17px;font-weight:800;letter-spacing:-.3px;">
            🌾 AGRI-SHOP &nbsp;<span id="rptTitleType">COMPREHENSIVE</span> REPORT
          </div>
          <div style="font-size:12px;opacity:.85;margin-top:4px;">
            Period: <span id="rptTitlePeriod">—</span> &nbsp;|&nbsp;
            Generated: <span id="rptTitleGenAt">—</span> &nbsp;|&nbsp;
            Report ID: <span id="rptTitleId">—</span>
          </div>
          <div style="font-size:11px;opacity:.75;margin-top:2px;">
            Generated by: <?= $this->session->agrishop_login_first_name . ' ' . $this->session->agrishop_login_last_name ?>
          </div>
        </div>
        <span id="rptTypeBadge" style="background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;padding:5px 16px;font-size:12px;font-weight:700;white-space:nowrap;"></span>
      </div>
    </div>

    <div class="card-body">

      <!-- SUMMARY STAT CARDS -->
      <div class="row mb-4" id="rptSummaryRow"><!-- JS fills --></div>

      <!-- CHARTS ROW -->
      <div class="row mb-4" id="rptChartsRow" style="display:none;">
        <div class="col-lg-7 mb-3">
          <div class="card card-outline card-success">
            <div class="card-header py-2">
              <h3 class="card-title" style="font-size:13px;"><i class="fa fa-chart-line text-success mr-1"></i> Orders Trend (Monthly)</h3>
              <div class="card-tools"><span class="badge badge-light border" id="rptPeakBadge" style="font-size:10px;"></span></div>
            </div>
            <div class="card-body p-2"><canvas id="rptTrendChart" height="100"></canvas></div>
          </div>
        </div>
        <div class="col-lg-5 mb-3" id="rptTopFarmersCol">
          <div class="card card-outline card-warning h-100">
            <div class="card-header py-2">
              <h3 class="card-title" style="font-size:13px;"><i class="fa fa-trophy text-warning mr-1"></i> Top Performing Farmers</h3>
            </div>
            <div class="card-body p-0" id="rptTopFarmersList"><!-- JS fills --></div>
          </div>
        </div>
      </div>

      <!-- PRODUCT MOVEMENT ROW -->
      <div class="row mb-4" id="rptProductsRow" style="display:none;">
        <div class="col-lg-6 mb-3">
          <div class="card card-outline card-danger">
            <div class="card-header py-2">
              <h3 class="card-title" style="font-size:13px;"><i class="fa fa-fire text-danger mr-1"></i> ⚡ Fast-Moving Products <small class="text-muted">High demand</small></h3>
            </div>
            <div class="card-body p-0">
              <table class="table table-sm table-hover mb-0" id="tblFast">
                <thead class="bg-light"><tr><th>Product</th><th>Category</th><th class="text-right">Units Sold</th></tr></thead>
                <tbody id="rptFastBody"></tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="col-lg-6 mb-3">
          <div class="card card-outline card-info">
            <div class="card-header py-2">
              <h3 class="card-title" style="font-size:13px;"><i class="fa fa-icicles text-info mr-1"></i> 🐢 Slow-Moving Products <small class="text-muted">Needs promotion</small></h3>
            </div>
            <div class="card-body p-0">
              <table class="table table-sm table-hover mb-0" id="tblSlow">
                <thead class="bg-light"><tr><th>Product</th><th>Category</th><th class="text-right">Units Sold</th></tr></thead>
                <tbody id="rptSlowBody"></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- MAIN DATA TABLE -->
      <div class="card card-outline card-secondary mb-0" id="rptMainTableCard">
        <div class="card-header py-2 d-flex align-items-center justify-content-between">
          <h3 class="card-title font-weight-bold" style="font-size:13px;" id="rptMainTableTitle"><i class="fa fa-table mr-1"></i> Detailed Records</h3>
          <span class="badge badge-light border" id="rptMainTableCount" style="font-size:11px;"></span>
        </div>
        <div class="card-body p-0">
          <div style="overflow-x:auto;">
            <table class="table table-sm table-striped table-hover mb-0" id="rptMainTable">
              <thead class="bg-light" id="rptMainHead"></thead>
              <tbody id="rptMainBody"></tbody>
            </table>
          </div>
          <div id="rptMainEmpty" style="display:none;text-align:center;padding:32px;color:#9ca3af;font-size:13px;">
            <i class="fa fa-inbox fa-2x mb-2 d-block"></i>No records found for the selected filters.
          </div>
        </div>
      </div>

    </div><!-- /card-body -->
  </div><!-- /#rptContent -->

  <!-- Bottom export bar -->
  <div class="text-center mt-3 mb-2">
    <button class="btn btn-sm btn-primary mr-2" onclick="doPrint()"><i class="fa fa-print mr-1"></i>Print Report</button>
    <button class="btn btn-sm btn-danger mr-2"  onclick="doPDF()"><i class="fa fa-file-pdf mr-1"></i>Download PDF Report</button>
    <button class="btn btn-sm btn-success"      onclick="doExcel()"><i class="fa fa-file-excel mr-1"></i>Export to Excel</button>
    <div class="text-muted mt-2" style="font-size:11px;">
      <i class="fa fa-lightbulb mr-1"></i>Tip: Use the filters above to customize your report &bull; Reports are automatically saved in your download history
    </div>
  </div>

</div><!-- /#rptPreviewWrap -->

</div><!-- /container-fluid -->
</section>

<!-- Print CSS -->
<style>
@media print {
  .main-sidebar,.main-header,.content-header,.card-tools,
  button,.btn,#exportDropWrap,.rpt-quick { display:none !important; }
  #rptContent { box-shadow:none !important; }
  body { font-size:11px; }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
var _trendChart = null;
var _lastData   = null;

var REPORT_LABELS = {
  comprehensive : 'COMPREHENSIVE SYSTEM',
  sales         : 'SALES',
  farmers       : 'FARMERS PERFORMANCE',
  products      : 'PRODUCT MOVEMENT',
  orders        : 'ORDERS',
  users         : 'USERS'
};

/* ── Quick date ──────────────────────────────────────────── */
function setQuick(period, btn) {
  document.querySelectorAll('.rpt-quick').forEach(function(b){ b.classList.remove('active','btn-outline-success'); b.classList.add('btn-outline-secondary'); });
  btn.classList.add('active','btn-outline-success'); btn.classList.remove('btn-outline-secondary');
  var now = new Date(), from, to = now.toISOString().slice(0,10);
  if (period==='today') { from = to; }
  else if (period==='week') { var d=new Date(now); d.setDate(d.getDate()-d.getDay()); from=d.toISOString().slice(0,10); }
  else if (period==='month') { from = now.getFullYear()+'-'+String(now.getMonth()+1).padStart(2,'0')+'-01'; }
  else { from = now.getFullYear()+'-01-01'; }
  document.getElementById('dateFrom').value = from;
  document.getElementById('dateTo').value   = to;
}

function resetFilters() {
  document.getElementById('rptType').value        = 'comprehensive';
  document.getElementById('dateFrom').value       = new Date().getFullYear()+'-'+String(new Date().getMonth()+1).padStart(2,'0')+'-01';
  document.getElementById('dateTo').value         = new Date().toISOString().slice(0,10);
  document.getElementById('rptProduceType').value = 'all';
  document.getElementById('rptFarmer').value      = 'all';
  document.getElementById('rptOrderStatus').value = 'all';
  document.getElementById('rptFormat').value      = 'pdf';
  document.getElementById('rptPreviewWrap').style.display = 'none';
  document.getElementById('exportDropWrap').style.display = 'none';
}

/* ── Generate ────────────────────────────────────────────── */
function generateReport() {
  var df = document.getElementById('dateFrom').value;
  var dt = document.getElementById('dateTo').value;
  if (!df || !dt) { toastr.warning('Please select a date range.'); return; }
  if (df > dt)    { toastr.warning('Start date must be before end date.'); return; }

  document.getElementById('rptLoading').style.display  = 'block';
  document.getElementById('rptPreviewWrap').style.display = 'none';
  document.getElementById('exportDropWrap').style.display = 'none';

  $.ajax({
    url    : '<?= base_url('useradmin/Reports/getReportData') ?>',
    method : 'POST',
    data   : {
      report_type  : document.getElementById('rptType').value,
      date_from    : df,
      date_to      : dt,
      farmer_id    : document.getElementById('rptFarmer').value,
      produce_type : document.getElementById('rptProduceType').value,
      order_status : document.getElementById('rptOrderStatus').value,
    },
    success: function(resp) {
      resp = JSON.parse(resp)
      document.getElementById('rptLoading').style.display = 'none';
      if (!resp.success) { toastr.error(resp.message || 'Could not generate report.'); return; }
      _lastData = resp;
      renderReport(resp);
    },
    error: function() {
      document.getElementById('rptLoading').style.display = 'none';
      toastr.error('Server error. Please try again.');
    }
  });
}

/* ── Render ──────────────────────────────────────────────── */
function renderReport(d) {
  var type  = d.report_type;
  var label = REPORT_LABELS[type] || type.toUpperCase();

  document.getElementById('rptTitleType').textContent   = label;
  document.getElementById('rptTitlePeriod').textContent = fmtDate(d.date_from) + ' – ' + fmtDate(d.date_to);
  document.getElementById('rptTitleGenAt').textContent  = d.generated_at;
  document.getElementById('rptTitleId').textContent     = d.report_id;
  document.getElementById('rptTypeBadge').textContent   = label.charAt(0) + label.slice(1).toLowerCase() + ' Report';
  document.getElementById('rptReadyBadge').textContent  = '✓ Generated ' + d.generated_at;

  renderSummary(d, type);

  var showChart    = ['comprehensive','sales','orders'].includes(type);
  var showProducts = ['comprehensive','products'].includes(type);

  document.getElementById('rptChartsRow').style.display   = showChart    ? '' : 'none';
  document.getElementById('rptProductsRow').style.display = showProducts ? '' : 'none';

  if (showChart)    { renderTrend(d.trend_rows || d.daily_sales || []); renderTopFarmers(d.top_farmers || []); }
  if (showProducts) { renderProductRows(d.fast_products || [], d.slow_products || []); }

  renderMainTable(d, type);

  document.getElementById('rptPreviewWrap').style.display = '';
  document.getElementById('exportDropWrap').style.display = '';
  document.getElementById('rptPreviewWrap').scrollIntoView({ behavior:'smooth', block:'start' });
  toastr.success('Report generated successfully!');
}

function fmtDate(s) {
  if (!s) return '—';
  var d = new Date(s + 'T00:00:00');
  return d.toLocaleDateString('en-PH', { year:'numeric', month:'long', day:'numeric' });
}
function peso(n) { return '₱' + parseFloat(n||0).toLocaleString('en-PH',{minimumFractionDigits:2,maximumFractionDigits:2}); }

/* ── Summary cards ───────────────────────────────────────── */
function renderSummary(d, type) {
  var cards = [];
  if (type === 'comprehensive') {
    cards = [
      { lbl:'Total Orders',    val: d.total_orders||0,                  sub:'Completed: '+(d.completed||0), color:'#3b82f6', bg:'#eff6ff', icon:'fa-shopping-cart' },
      { lbl:'Total Revenue',   val: peso(d.total_revenue),              sub:'Avg: '+peso(d.avg_order),       color:'#22c55e', bg:'#f0fdf4', icon:'fa-peso-sign' },
      { lbl:'System Income',   val: peso(d.system_income),              sub:'All sources',                   color:'#8b5cf6', bg:'#f5f3ff', icon:'fa-money-bill-wave' },
      { lbl:'Active Buyers',   val: d.active_buyers||0,                 sub:'Unique buyers',                 color:'#f97316', bg:'#fff7ed', icon:'fa-users' },
      { lbl:'Total Farms',     val: d.total_farms||0,                   sub:'Registered farms',              color:'#eab308', bg:'#fefce8', icon:'fa-tractor' },
    ];
  } else if (type === 'sales') {
    cards = [
      { lbl:'Total Revenue',   val: peso(d.total_revenue), sub:'Period total',         color:'#22c55e', bg:'#f0fdf4', icon:'fa-peso-sign' },
      { lbl:'Total Orders',    val: d.total_orders||0,     sub:'Transactions',          color:'#3b82f6', bg:'#eff6ff', icon:'fa-shopping-cart' },
      { lbl:'Avg Order Value', val: peso(d.avg_order),     sub:'Per transaction',       color:'#8b5cf6', bg:'#f5f3ff', icon:'fa-chart-line' },
    ];
  } else if (type === 'farmers') {
    cards = [
      { lbl:'Total Farmers',   val: d.total_farmers||0,   sub:'Active farmers',        color:'#22c55e', bg:'#f0fdf4', icon:'fa-tractor' },
      { lbl:'Combined Revenue',val: peso(d.total_revenue), sub:'From all farmers',      color:'#3b82f6', bg:'#eff6ff', icon:'fa-peso-sign' },
      { lbl:'Total Orders',    val: d.total_orders||0,     sub:'Orders handled',        color:'#f97316', bg:'#fff7ed', icon:'fa-shopping-cart' },
    ];
  } else if (type === 'products') {
    cards = [
      { lbl:'Products Tracked',val: (d.products_list||[]).length, sub:'Unique products', color:'#22c55e', bg:'#f0fdf4', icon:'fa-seedling' },
      { lbl:'Units Sold',      val: d.total_units||0,             sub:'All products',    color:'#3b82f6', bg:'#eff6ff', icon:'fa-boxes' },
      { lbl:'Products Revenue',val: peso(d.total_revenue),        sub:'Period total',    color:'#8b5cf6', bg:'#f5f3ff', icon:'fa-peso-sign' },
    ];
  } else if (type === 'orders') {
    cards = [
      { lbl:'Total Orders',    val: d.total_orders||0,    sub:'In period',             color:'#3b82f6', bg:'#eff6ff', icon:'fa-shopping-cart' },
      { lbl:'Total Revenue',   val: peso(d.total_revenue), sub:'From orders',           color:'#22c55e', bg:'#f0fdf4', icon:'fa-peso-sign' },
      { lbl:'Avg Order Value', val: peso(d.avg_order),    sub:'Per transaction',        color:'#8b5cf6', bg:'#f5f3ff', icon:'fa-chart-bar' },
    ];
  } else if (type === 'users') {
    cards = [
      { lbl:'New Users',       val: d.total_users||0,    sub:'In period',             color:'#3b82f6', bg:'#eff6ff', icon:'fa-user-plus' },
      { lbl:'Active',          val: d.active_users||0,   sub:'Verified accounts',     color:'#22c55e', bg:'#f0fdf4', icon:'fa-user-check' },
      { lbl:'Inactive',        val: d.pending_users||0,  sub:'Not yet active',        color:'#f97316', bg:'#fff7ed', icon:'fa-user-clock' },
    ];
  }

  var cols = Math.max(2, Math.floor(12 / cards.length));
  var html = cards.map(function(c) {
    return '<div class="col-6 col-md-' + cols + ' mb-3">' +
      '<div class="info-box mb-0" style="border-left:4px solid '+c.color+';min-height:70px;">' +
        '<span class="info-box-icon d-flex align-items-center justify-content-center" style="background:'+c.bg+';color:'+c.color+';font-size:22px;width:60px;"><i class="fa '+c.icon+'"></i></span>' +
        '<div class="info-box-content">' +
          '<span class="info-box-text" style="font-size:10px;text-transform:uppercase;letter-spacing:.05em;font-weight:700;color:#9ca3af;">'+c.lbl+'</span>' +
          '<span class="info-box-number" style="font-size:20px;font-weight:800;color:#111;line-height:1.2;">'+c.val+'</span>' +
          '<span style="font-size:11px;color:#9ca3af;">'+c.sub+'</span>' +
        '</div>' +
      '</div>' +
    '</div>';
  }).join('');
  document.getElementById('rptSummaryRow').innerHTML = html;
}

/* ── Trend chart ─────────────────────────────────────────── */
function renderTrend(rows) {
  if (_trendChart) { _trendChart.destroy(); _trendChart = null; }
  if (!rows || !rows.length) return;
  var labels = rows.map(function(r){ return r.month || r.sale_date || ''; });
  var vals   = rows.map(function(r){ return parseFloat(r.orders || 0); });
  var peak   = Math.max.apply(null, vals);
  var peakI  = vals.indexOf(peak);
  document.getElementById('rptPeakBadge').textContent = '📈 Peak Month: ' + (labels[peakI] || '');
  var ctx = document.getElementById('rptTrendChart').getContext('2d');
  var grad = ctx.createLinearGradient(0,0,0,200);
  grad.addColorStop(0,'rgba(34,197,94,.25)'); grad.addColorStop(1,'rgba(34,197,94,0)');
  _trendChart = new Chart(ctx, {
    type:'line',
    data:{ labels:labels, datasets:[{ label:'Orders', data:vals, borderColor:'#22c55e', backgroundColor:grad, borderWidth:2.5, tension:.4, fill:true, pointBackgroundColor:'#22c55e', pointRadius:5, pointHoverRadius:7 }] },
    options:{ responsive:true, maintainAspectRatio:false, plugins:{ legend:{display:false} }, scales:{ x:{grid:{display:false},ticks:{font:{size:11}}}, y:{beginAtZero:true,grid:{color:'#f3f4f6'},ticks:{font:{size:11}}} } }
  });
}

/* ── Top farmers ─────────────────────────────────────────── */
function renderTopFarmers(farmers) {
  var medals = ['🥇','🥈','🥉'];
  var el = document.getElementById('rptTopFarmersList');
  if (!farmers.length) { el.innerHTML = '<div class="text-center text-muted p-4" style="font-size:13px;">No data</div>'; return; }
  el.innerHTML = farmers.slice(0,5).map(function(f,i){
    return '<div class="d-flex align-items-center p-2 border-bottom" style="gap:10px;">' +
      '<div style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;">'+(medals[i]||(i+1))+'</div>' +
      '<div style="flex:1;font-size:13px;font-weight:600;color:#111;">'+f.farmer_name+'</div>' +
      (i===0 ? '<span class="badge badge-success" style="font-size:10px;">Top</span>' : '') +
      '<div style="font-size:13px;font-weight:700;color:#22c55e;">'+peso(f.total_sales)+'</div>' +
    '</div>';
  }).join('');
}

/* ── Product rows ────────────────────────────────────────── */
function renderProductRows(fast, slow) {
  function rowHtml(p) {
    return '<tr><td style="font-weight:600;">'+p.product_name+'</td><td>'+p.category+'</td><td class="text-right"><strong>'+p.units_sold+'</strong></td></tr>';
  }
  document.getElementById('rptFastBody').innerHTML = fast.length ? fast.map(rowHtml).join('') : '<tr><td colspan="3" class="text-center text-muted">No data</td></tr>';
  document.getElementById('rptSlowBody').innerHTML = slow.length ? slow.map(rowHtml).join('') : '<tr><td colspan="3" class="text-center text-muted">No data</td></tr>';
}

/* ── Main table ──────────────────────────────────────────── */
function renderMainTable(d, type) {
  var head = document.getElementById('rptMainHead');
  var body = document.getElementById('rptMainBody');
  var title = document.getElementById('rptMainTableTitle');
  var count = document.getElementById('rptMainTableCount');
  var empty = document.getElementById('rptMainEmpty');
  var table = document.getElementById('rptMainTable');

  var headers = [], rows = [], records = [];

  if (type === 'comprehensive' || type === 'orders') {
    records = d.orders_list || [];
    title.innerHTML = '<i class="fa fa-table mr-1"></i> Orders Detail';
    headers = ['#','Date','Buyer','Farmer / Farm','Amount','To Farmer','Status','Delivery'];
    rows = records.map(function(r,i){
      return '<tr>' +
        '<td>'+(i+1)+'</td>' +
        '<td style="white-space:nowrap;">'+r.txn_date+'</td>' +
        '<td style="font-weight:600;">'+r.buyer+'</td>' +
        '<td>'+(r.farmer_name||'—')+' <small class="text-muted">'+(r.farm_name||'')+'</small></td>' +
        '<td style="font-weight:700;color:#166534;">'+peso(r.total_payment)+'</td>' +
        '<td>'+peso(r.to_farmer)+'</td>' +
        '<td>' + statusBadge(r.status) + '</td>' +
        '<td>' + statusBadge(r.delivery_status) + '</td>' +
      '</tr>';
    });
  } else if (type === 'sales') {
    records = d.daily_sales || [];
    title.innerHTML = '<i class="fa fa-table mr-1"></i> Daily Sales Breakdown';
    headers = ['Date','Orders','Revenue','Admin Income','Farmer Income'];
    rows = records.map(function(r){
      return '<tr>' +
        '<td>'+r.sale_date+'</td>' +
        '<td>'+r.orders+'</td>' +
        '<td style="font-weight:700;color:#166534;">'+peso(r.revenue)+'</td>' +
        '<td>'+peso(r.admin_income)+'</td>' +
        '<td>'+peso(r.farmer_income)+'</td>' +
      '</tr>';
    });
  } else if (type === 'farmers') {
    records = d.farmers_list || [];
    title.innerHTML = '<i class="fa fa-table mr-1"></i> Farmers Performance';
    headers = ['#','Farmer Name','Email','Contact','Farms','Orders','Total Sales','Joined','Status'];
    rows = records.map(function(r,i){
      return '<tr>' +
        '<td>'+(i+1)+'</td>' +
        '<td style="font-weight:600;">'+r.farmer_name+'</td>' +
        '<td>'+r.email+'</td>' +
        '<td>'+r.contact_num+'</td>' +
        '<td class="text-center">'+r.total_farms+'</td>' +
        '<td class="text-center">'+r.total_orders+'</td>' +
        '<td style="font-weight:700;color:#166534;">'+peso(r.total_sales)+'</td>' +
        '<td style="white-space:nowrap;">'+r.joined_date+'</td>' +
        '<td><span class="badge badge-'+(r.status==='Approved'?'success':'warning')+'">'+r.status+'</span></td>' +
      '</tr>';
    });
  } else if (type === 'products') {
    records = d.products_list || [];
    title.innerHTML = '<i class="fa fa-table mr-1"></i> Product Movement Detail';
    headers = ['#','Product','Category','Units Sold','Revenue'];
    rows = records.map(function(r,i){
      return '<tr>' +
        '<td>'+(i+1)+'</td>' +
        '<td style="font-weight:600;">'+r.product_name+'</td>' +
        '<td>'+r.category+'</td>' +
        '<td>'+r.units_sold+'</td>' +
        '<td style="font-weight:700;color:#166534;">'+peso(r.revenue)+'</td>' +
      '</tr>';
    });
  } else if (type === 'users') {
    records = d.users_list || [];
    title.innerHTML = '<i class="fa fa-table mr-1"></i> Users List';
    headers = ['#','Full Name','Email','Contact','Role','Status','Joined'];
    rows = records.map(function(r,i){
      return '<tr>' +
        '<td>'+(i+1)+'</td>' +
        '<td style="font-weight:600;">'+r.full_name+'</td>' +
        '<td>'+r.email+'</td>' +
        '<td>'+r.contact_num+'</td>' +
        '<td>'+r.role+'</td>' +
        '<td><span class="badge badge-'+(r.status==1?'success':'secondary')+'">'+(r.status==1?'Active':'Inactive')+'</span></td>' +
        '<td style="white-space:nowrap;">'+r.joined_date+'</td>' +
      '</tr>';
    });
  }

  head.innerHTML = '<tr>' + headers.map(function(h){ return '<th>'+h+'</th>'; }).join('') + '</tr>';
  body.innerHTML = rows.join('');
  count.textContent = records.length + ' records';

  if (!records.length) {
    empty.style.display = 'block';
    table.style.display = 'none';
  } else {
    empty.style.display = 'none';
    table.style.display = '';
  }
}

function statusBadge(s) {
  if (!s) return '<span class="badge badge-secondary">—</span>';
  var map = {
    COMPLETED:'success', RESERVED:'info', PREPARING:'warning',
    TO_PICKUP:'primary', TO_DELIVER:'info', CANCELLED:'danger',
    ORDER_IS_READY:'success'
  };
  var cls = map[s] || 'secondary';
  return '<span class="badge badge-'+cls+'" style="font-size:10px;">'+s+'</span>';
}

/* ── Print / PDF / Excel ─────────────────────────────────── */
function doPrint() { window.print(); }

function doPDF() {
  if (!_lastData) { toastr.warning('Please generate a report first.'); return; }
  var form = document.createElement('form');
  form.method = 'POST';
  form.action = '<?= base_url('useradmin/Reports/exportPDF') ?>';
  form.target = '_blank';
  var fields = {
    report_type  : document.getElementById('rptType').value,
    date_from    : document.getElementById('dateFrom').value,
    date_to      : document.getElementById('dateTo').value,
    farmer_id    : document.getElementById('rptFarmer').value,
    produce_type : document.getElementById('rptProduceType').value,
    order_status : document.getElementById('rptOrderStatus').value,
  };
  Object.entries(fields).forEach(function(kv){
    var i = document.createElement('input'); i.type='hidden'; i.name=kv[0]; i.value=kv[1]; form.appendChild(i);
  });
  document.body.appendChild(form); form.submit(); document.body.removeChild(form);
}

function doExcel() {
  if (!_lastData) { toastr.warning('Please generate a report first.'); return; }
  var wb  = XLSX.utils.book_new();
  var tbl = document.getElementById('rptMainTable');
  var ws  = XLSX.utils.table_to_sheet(tbl);
  XLSX.utils.book_append_sheet(wb, ws, 'Report');
  XLSX.writeFile(wb, 'AgriShop_'+(_lastData.report_type||'report')+'_'+_lastData.date_from+'_to_'+_lastData.date_to+'.xlsx');
}
</script>
