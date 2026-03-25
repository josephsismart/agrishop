<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!$this->session->agrishop_login_level) {
  redirect(base_url('login'));
}
$uri = $this->session->agrishop_login_uri;

$fast_produce  = $dashboard['fast_produce']  ?? [];
$slow_produce  = $dashboard['slow_produce']  ?? [];
$top_farmers   = json_decode($dashboard['top_farmer']);
$remittance    = json_decode($dashboard['farmer_remittance']);
$order_map     = $dashboard['order_status_map'] ?? [];
?>
<style>
  .dash-stat {
    border-radius: 14px;
    padding: 16px 18px;
    border: none;
    background: #fff;
    box-shadow: 0 2px 12px rgba(0, 0, 0, .07);
    transition: transform .18s, box-shadow .18s;
    position: relative;
    overflow: hidden;
  }

  .dash-stat:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, .1);
  }

  .dash-stat .st-stripe {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    border-radius: 14px 0 0 14px;
  }

  .dash-stat .st-val {
    font-size: 24px;
    font-weight: 800;
    color: #111;
    line-height: 1.1;
  }

  .dash-stat .st-lbl {
    font-size: 10px;
    font-weight: 700;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-top: 2px;
  }

  .dash-stat .st-sub {
    font-size: 11px;
    color: #6b7280;
    margin-top: 4px;
  }

  .dash-stat .st-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    flex-shrink: 0;
  }

  .dash-card {
    border-radius: 14px;
    border: none;
    box-shadow: 0 2px 12px rgba(0, 0, 0, .07);
    overflow: hidden;
    background: #fff;
  }

  .dash-card-head {
    padding: 11px 16px;
    font-weight: 700;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
    border-bottom: 1px solid #f3f4f6;
  }

  .ldr-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 14px;
    border-bottom: 1px solid #f9fafb;
    transition: background .15s;
  }

  .ldr-row:last-child {
    border-bottom: none;
  }

  .ldr-row:hover {
    background: #f9fafb;
  }

  .ldr-rank {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 800;
    flex-shrink: 0;
  }

  .rank-1 {
    background: #fef9c3;
    color: #92400e;
  }

  .rank-2 {
    background: #f3f4f6;
    color: #374151;
  }

  .rank-3 {
    background: #fef3e2;
    color: #b45309;
  }

  .rank-n {
    background: #f0fdf4;
    color: #166534;
  }

  .produce-pill {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 6px 9px;
    border-radius: 9px;
    background: #f9fafb;
    border: 1px solid #f3f4f6;
    transition: background .15s;
    margin-bottom: 5px;
  }

  .produce-pill:hover {
    background: #f0fdf4;
  }

  .produce-pill img {
    width: 34px;
    height: 34px;
    border-radius: 7px;
    object-fit: cover;
  }

  .produce-pill .pname {
    font-size: 11.5px;
    font-weight: 600;
    color: #111;
  }

  .produce-pill .pqty {
    font-size: 10.5px;
    color: #6b7280;
    margin-top: 1px;
  }

  @keyframes pulse-badge {

    0%,
    100% {
      box-shadow: 0 0 0 0 rgba(220, 53, 69, .4);
    }

    50% {
      box-shadow: 0 0 0 8px rgba(220, 53, 69, 0);
    }
  }

  .badge-pulse {
    animation: pulse-badge 2s infinite;
  }

  .kpi-mini {
    background: #f9fafb;
    border-radius: 9px;
    padding: 9px 13px;
    border: 1px solid #f3f4f6;
  }

  .kpi-mini .kv {
    font-size: 17px;
    font-weight: 800;
    color: #111;
  }

  .kpi-mini .kl {
    font-size: 9.5px;
    color: #9ca3af;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: .05em;
  }

  .shortcut-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    border-radius: 11px;
    padding: 12px 8px;
    border: 1.5px solid #e5e7eb;
    background: #fff;
    cursor: pointer;
    transition: all .18s;
    text-decoration: none;
    color: inherit;
  }

  .shortcut-btn:hover {
    background: #f0fdf4;
    border-color: #22c55e;
    color: #166534;
    text-decoration: none;
  }

  .shortcut-btn i {
    font-size: 19px;
  }

  .shortcut-btn span {
    font-size: 10.5px;
    font-weight: 600;
  }
</style>

<!-- Page Header -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mt-2 mb-n1">
      <div class="col-sm-7">
        <h5 class="m-0 font-weight-bold"><i class="fa fa-chart-pie text-success mr-1"></i> Admin Dashboard</h5>
        <small class="text-muted"><?= date('l, F j, Y') ?> &nbsp;•&nbsp; Welcome back, <strong><?= $this->session->agrishop_login_first_name ?></strong>!</small>
      </div>
      <div class="col-sm-5 text-right d-flex align-items-center justify-content-end" style="gap:8px;">
        <span class="badge badge-light border" style="font-size:10.5px;padding:5px 11px;"><i class="fa fa-circle text-success mr-1" style="font-size:7px;"></i>Live Data</span>
        <a href="<?= base_url('useradmin/Reports') ?>" class="btn btn-sm btn-success font-weight-bold rounded-pill px-3" style="font-size:11.5px;">
          <i class="fa fa-chart-bar mr-1"></i> Generate Reports
        </a>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">

    <!-- PENDING APPROVALS -->
    <?php if (($dashboard['pending_farmers'] + $dashboard['pending_suppliers']) > 0) : ?>
      <div class="alert d-flex align-items-center mb-3" style="border-radius:13px;background:linear-gradient(135deg,#fff3cd,#ffeeba);border:none;box-shadow:0 2px 12px rgba(255,193,7,.2);">
        <div class="badge-pulse rounded-circle d-flex align-items-center justify-content-center mr-3" style="width:38px;height:38px;background:#dc3545;flex-shrink:0;"><i class="fa fa-user-clock text-white"></i></div>
        <div class="flex-grow-1">
          <strong>Pending Approvals</strong>
          <div style="font-size:12px;margin-top:2px;">
            <?php if ($dashboard['pending_farmers'] > 0) : ?><span class="badge badge-danger mr-1"><?= $dashboard['pending_farmers'] ?> Farmer<?= $dashboard['pending_farmers'] > 1 ? 's' : '' ?></span><?php endif; ?>
            <?php if ($dashboard['pending_suppliers'] > 0) : ?><span class="badge badge-warning"><?= $dashboard['pending_suppliers'] ?> Supplier<?= $dashboard['pending_suppliers'] > 1 ? 's' : '' ?></span><?php endif; ?>
            waiting for review.
          </div>
        </div>
        <a href="<?= base_url('useradmin/Users') ?>" class="btn btn-sm btn-dark font-weight-bold rounded-pill px-3">Review <i class="fa fa-arrow-right ml-1"></i></a>
      </div>
    <?php endif; ?>

    <!-- TODAY'S KPI STRIP -->
    <div class="row mb-3">
      <div class="col-12">
        <div class="dash-card">
          <div class="dash-card-head"><i class="fa fa-sun text-warning"></i> Today's Performance <span class="badge badge-success px-2 ml-1" style="font-size:10px;"><i class="fa fa-circle mr-1" style="font-size:7px;"></i>Live</span></div>
          <div class="card-body py-2">
            <div class="row">
              <div class="col-6 col-md-3 mb-1">
                <div class="kpi-mini">
                  <div class="kl">Orders Today</div>
                  <div class="kv"><?= $dashboard['orders_today'] ?></div>
                </div>
              </div>
              <div class="col-6 col-md-3 mb-1">
                <div class="kpi-mini">
                  <div class="kl">Revenue Today</div>
                  <div class="kv text-success">&#8369;<?= $dashboard['revenue_today'] ?></div>
                </div>
              </div>
              <div class="col-6 col-md-3 mb-1">
                <div class="kpi-mini">
                  <div class="kl">Orders This Month</div>
                  <div class="kv"><?= $dashboard['orders_month'] ?></div>
                </div>
              </div>
              <div class="col-6 col-md-3">
                <div class="kpi-mini">
                  <div class="kl">Revenue This Month</div>
                  <div class="kv text-success">&#8369;<?= $dashboard['revenue_month'] ?></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- MAIN STAT CARDS -->
    <?php
    $cards = [
      ['label' => 'Total Users',          'val' => $dashboard['user'],            'sub' => 'Registered accounts',    'icon' => 'fa-users',          'color' => '#3b82f6', 'bg' => '#eff6ff'],
      ['label' => 'Active Farmers',       'val' => $dashboard['farmer'],          'sub' => 'Verified farmers',        'icon' => 'fa-tractor',        'color' => '#22c55e', 'bg' => '#f0fdf4'],
      ['label' => 'Suppliers',            'val' => $dashboard['total_suppliers'], 'sub' => 'Active suppliers',        'icon' => 'fa-store',          'color' => '#f97316', 'bg' => '#fff7ed'],
      ['label' => 'Active Subscriptions', 'val' => $dashboard['subscription'],    'sub' => 'Active subs',             'icon' => 'fa-crown',          'color' => '#eab308', 'bg' => '#fefce8'],
      // ['label' => 'Completed Orders',     'val' => $dashboard['completed_orders'], 'sub' => 'All time',                'icon' => 'fa-check-circle',   'color' => '#14b8a6', 'bg' => '#f0fdfa'],
      // ['label' => 'Cancelled Orders',     'val' => $dashboard['cancelled_orders'], 'sub' => 'Cancelled transactions',  'icon' => 'fa-times-circle',   'color' => '#ef4444', 'bg' => '#fef2f2'],
      // ['label' => 'Subscription Revenue', 'val' => '&#8369;' . $dashboard['revenue'], 'sub' => 'Total collected',      'icon' => 'fa-money-bill-wave', 'color' => '#8b5cf6', 'bg' => '#f5f3ff'],
      ['label' => 'Total Farms',          'val' => $dashboard['total_farms'],     'sub' => 'Registered farms',        'icon' => 'fa-house-chimney',  'color' => '#06b6d4', 'bg' => '#ecfeff'],
      ['label' => 'Produce Types',        'val' => $dashboard['total_produce'],   'sub' => 'Product categories',      'icon' => 'fa-seedling',       'color' => '#84cc16', 'bg' => '#f7fee7'],
      // ['label' => 'Farmers (All)',        'val' => $dashboard['farmer'],          'sub' => 'Including pending',       'icon' => 'fa-id-card',        'color' => '#0ea5e9', 'bg' => '#f0f9ff'],
      // ['label' => 'Pending Farmers',      'val' => $dashboard['pending_farmers'], 'sub' => 'Awaiting approval',       'icon' => 'fa-user-clock',     'color' => '#f59e0b', 'bg' => '#fffbeb'],
      // ['label' => 'Pending Suppliers',    'val' => $dashboard['pending_suppliers'], 'sub' => 'Awaiting approval',      'icon' => 'fa-exclamation-circle', 'color' => '#dc2626', 'bg' => '#fef2f2'],
    ];
    ?>
    <div class="row mb-3">
      <?php foreach ($cards as $c) : ?>
        <div class="col-6 col-md-4 mb-3">
          <div class="dash-stat">
            <div class="st-stripe" style="background:<?= $c['color'] ?>;"></div>
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <div class="st-lbl"><?= $c['label'] ?></div>
                <div class="st-val mt-1"><?= $c['val'] ?></div>
                <div class="st-sub"><?= $c['sub'] ?></div>
              </div>
              <div class="st-icon" style="background:<?= $c['bg'] ?>;color:<?= $c['color'] ?>;"><i class="fa <?= $c['icon'] ?>"></i></div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="row mb-3">
      <div class="col-12">
        <div class="dash-card">
          <div class="dash-card-head"><i class="fa fa-bolt text-warning"></i> Quick Actions</div>
          <div class="card-body py-2">
            <div class="row">
              <?php $shortcuts = [
                ['href' => base_url('useradmin/Users'),   'icon' => 'fa-users',       'label' => 'Manage Users',  'color' => '#3b82f6'],
                ['href' => base_url('useradmin/Reports'), 'icon' => 'fa-chart-bar',   'label' => 'Reports',       'color' => '#22c55e'],
                ['href' => base_url('useradmin/Billing'), 'icon' => 'fa-credit-card', 'label' => 'Billing',       'color' => '#8b5cf6'],
              ]; ?>
              <?php foreach ($shortcuts as $s) : ?>
                <div class="col-4 col-md-2 mb-1">
                  <a href="<?= $s['href'] ?>" class="shortcut-btn w-100">
                    <i class="fa <?= $s['icon'] ?>" style="color:<?= $s['color'] ?>;"></i>
                    <span><?= $s['label'] ?></span>
                  </a>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ORDER STATUS BREAKDOWN -->
    <div class="row mb-3">
      <div class="col-12">
        <div class="dash-card">
          <div class="dash-card-head"><i class="fa fa-shopping-cart text-info"></i> Order Status Breakdown</div>
          <div class="card-body py-2">
            <div class="row">
              <?php
              $status_items = [
                ['key' => 'reserved',       'label' => 'Reserved',        'color' => '#f59e0b', 'icon' => 'fa-clock'],
                ['key' => 'preparing',      'label' => 'Preparing',       'color' => '#3b82f6', 'icon' => 'fa-people-carry'],
                ['key' => 'to_pickup',      'label' => 'Ready Pickup',    'color' => '#8b5cf6', 'icon' => 'fa-box'],
                ['key' => 'to_deliver',     'label' => 'Out for Delivery', 'color' => '#06b6d4', 'icon' => 'fa-truck'],
                ['key' => 'completed',      'label' => 'Completed',       'color' => '#22c55e', 'icon' => 'fa-check-circle'],
                ['key' => 'cancelled',      'label' => 'Cancelled',       'color' => '#ef4444', 'icon' => 'fa-times-circle'],
              ];
              ?>
              <?php foreach ($status_items as $s) : ?>
                <div class="col-6 col-md-2 mb-2">
                  <div style="border-radius:11px;padding:12px;background:#f9fafb;border:1px solid #f3f4f6;text-align:center;">
                    <i class="fa <?= $s['icon'] ?> fa-lg mb-1 d-block" style="color:<?= $s['color'] ?>;"></i>
                    <div style="font-size:18px;font-weight:800;color:#111;"><?= $order_map[strtolower($s['key'])] ?? 0 ?></div>
                    <div style="font-size:9.5px;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.05em;"><?= $s['label'] ?></div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- CHARTS -->
    <div class="row mb-3">
      <div class="col-lg-8 mb-3">
        <div class="dash-card card">
          <div class="dash-card-head"><i class="fa fa-chart-area text-success"></i> Revenue Trend — <?= date('Y') ?> <span class="ml-auto badge badge-light border" style="font-size:10px;">Subscription</span></div>
          <div class="card-body p-2"><canvas id="revenueTrend" height="90"></canvas></div>
        </div>
      </div>
      <div class="col-lg-4 mb-3">
        <div class="dash-card card h-100">
          <div class="dash-card-head"><i class="fa fa-chart-bar text-primary"></i> Orders by Month</div>
          <div class="card-body p-2"><canvas id="ordersAnalytics" height="160"></canvas></div>
        </div>
      </div>
    </div>

    <!-- TOP FARMERS + BILLING -->
    <div class="row mb-3">
      <div class="col-lg-6 mb-3">
        <div class="dash-card card">
          <div class="dash-card-head">
            <i class="fa fa-trophy text-warning"></i> Top Farmers by Sales
            <a href="<?= base_url('useradmin/Reports') ?>" class="ml-auto" style="font-size:11px;color:#22c55e;text-decoration:none;font-weight:600;">View Full Report →</a>
          </div>
          <div class="card-body p-0">
            <?php if (empty($top_farmers)) : ?>
              <div class="text-center text-muted py-4" style="font-size:13px;"><i class="fa fa-tractor fa-2x mb-2 d-block"></i>No data yet</div>
            <?php else : ?>
              <?php $medals = ['🥇', '🥈', '🥉'];
              $i = 0;
              foreach ($top_farmers as $f) : $i++; ?>
                <div class="ldr-row">
                  <div class="ldr-rank <?= $i === 1 ? 'rank-1' : ($i === 2 ? 'rank-2' : ($i === 3 ? 'rank-3' : 'rank-n')) ?>"><?= $medals[$i - 1] ?? $i ?></div>
                  <div class="flex-grow-1">
                    <div style="font-size:12.5px;font-weight:600;color:#111;"><?= htmlspecialchars($f->farmer ?? '—') ?></div>
                    <?php if ($i === 1) : ?><span style="font-size:9.5px;font-weight:700;color:#166534;background:#dcfce7;border-radius:9px;padding:1px 7px;">Top Performer</span><?php endif; ?>
                  </div>
                  <div style="font-size:13px;font-weight:700;color:#22c55e;">&#8369;<?= number_format($f->revenue ?? 0, 2) ?></div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="col-lg-6 mb-3">
        <div class="dash-card card">
          <div class="dash-card-head"><i class="fa fa-file-invoice text-warning"></i> Farmer Subscription Billing</div>
          <div class="card-body p-0">
            <?php if (empty($remittance)) : ?>
              <div class="text-center text-muted py-4" style="font-size:13px;"><i class="fa fa-wallet fa-2x mb-2 d-block"></i>No billing records</div>
            <?php else : ?>
              <?php foreach ($remittance as $r) : ?>
                <div class="ldr-row">
                  <div style="flex:1;">
                    <div style="font-size:12.5px;font-weight:600;color:#111;"><?= htmlspecialchars($r->farmer ?? '—') ?></div>
                  </div>
                  <div style="font-size:13px;font-weight:700;color:#111;margin-right:8px;">&#8369;<?= number_format($r->amount ?? 0, 2) ?></div>
                  <span class="badge <?= $r->status ? 'badge-success' : 'badge-warning' ?>" style="font-size:9.5px;padding:3px 7px;"><?= $r->status ? '✓ PAID' : '⏳ PENDING' ?></span>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- FAST + SLOW PRODUCE -->
    <div class="row mb-3">
      <div class="col-lg-6 mb-3">
        <div class="dash-card card">
          <div class="dash-card-head"><i class="fa fa-fire text-danger"></i> 🔥 Fast Moving Produce <span class="ml-auto text-muted" style="font-size:10.5px;font-weight:400;">Top 6</span></div>
          <div class="card-body p-2">
            <?php if (empty($fast_produce)) : ?>
              <div class="text-center text-muted py-3" style="font-size:13px;">No data yet</div>
            <?php else : ?>
              <div class="row">
                <?php foreach ($fast_produce as $p) : ?>
                  <div class="col-6">
                    <div class="produce-pill">
                      <img src="<?= base_url($p['img_path'] ?? 'dist/img/media/icons/1x1.png') ?>" onerror="this.src='<?= base_url('dist/img/media/icons/1x1.png') ?>'">
                      <div>
                        <div class="pname"><?= htmlspecialchars($p['name'] ?? '—') ?></div>
                        <div class="pqty"><span class="badge badge-success" style="font-size:9.5px;"><?= $p['sum_qty'] ?> sold</span></div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="col-lg-6 mb-3">
        <div class="dash-card card">
          <div class="dash-card-head"><i class="fa fa-icicles text-info"></i> 🧊 Slow Moving Produce <span class="ml-auto text-muted" style="font-size:10.5px;font-weight:400;">Bottom 6</span></div>
          <div class="card-body p-2">
            <?php if (empty($slow_produce)) : ?>
              <div class="text-center text-muted py-3" style="font-size:13px;">No data yet</div>
            <?php else : ?>
              <div class="row">
                <?php foreach ($slow_produce as $p) : ?>
                  <div class="col-6">
                    <div class="produce-pill">
                      <img src="<?= base_url($p['img_path'] ?? 'dist/img/media/icons/1x1.png') ?>" onerror="this.src='<?= base_url('dist/img/media/icons/1x1.png') ?>'">
                      <div>
                        <div class="pname"><?= htmlspecialchars($p['name'] ?? '—') ?></div>
                        <div class="pqty"><span class="badge badge-secondary" style="font-size:9.5px;"><?= $p['sum_qty'] ?> sold</span></div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- REPORT CTA BANNER -->
    <div class="row mb-3">
      <div class="col-12">
        <div style="background:linear-gradient(135deg,#166534,#15803d,#22c55e);border-radius:14px;padding:20px 28px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:14px;">
          <div>
            <div style="font-size:15px;font-weight:800;color:#fff;margin-bottom:3px;"><i class="fa fa-chart-bar mr-2"></i>Generate Detailed Reports</div>
            <div style="font-size:12px;color:rgba(255,255,255,.8);">Filter by period, farmer, or product type — then print or export to PDF/Excel.</div>
          </div>
          <a href="<?= base_url('useradmin/Reports') ?>" class="btn btn-light font-weight-bold rounded-pill px-4" style="font-size:12.5px;color:#166534;">
            <i class="fa fa-chart-bar mr-1"></i> Open Report Builder
          </a>
        </div>
      </div>
    </div>

  </div><!-- /container-fluid -->
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
  const revenueData = <?= $dashboard['revnue_trendGraph'] ?>;
  const orderAnalytics = <?= $dashboard['orderAnalyticsGraph'] ?>;
  Chart.defaults.font.size = 11;

  new Chart(document.getElementById('revenueTrend'), {
    type: 'line',
    data: {
      labels: revenueData.map(d => d.month),
      datasets: [{
        label: 'Revenue (₱)',
        data: revenueData.map(d => d.revenue),
        borderColor: '#22c55e',
        backgroundColor: function(ctx) {
          var g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 220);
          g.addColorStop(0, 'rgba(34,197,94,.22)');
          g.addColorStop(1, 'rgba(34,197,94,0)');
          return g;
        },
        borderWidth: 2.5,
        tension: .42,
        fill: true,
        pointBackgroundColor: '#22c55e',
        pointRadius: 4,
        pointHoverRadius: 6
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          callbacks: {
            label: ctx => ' ₱' + parseFloat(ctx.parsed.y).toLocaleString()
          }
        }
      },
      scales: {
        x: {
          grid: {
            display: false
          },
          ticks: {
            color: '#9ca3af'
          }
        },
        y: {
          beginAtZero: true,
          grid: {
            color: '#f3f4f6'
          },
          ticks: {
            color: '#9ca3af',
            callback: v => '₱' + v.toLocaleString()
          }
        }
      }
    }
  });

  new Chart(document.getElementById('ordersAnalytics'), {
    type: 'bar',
    data: {
      labels: orderAnalytics.map(d => d.month),
      datasets: [{
        label: 'Orders',
        data: orderAnalytics.map(d => d.orders),
        backgroundColor: 'rgba(59,130,246,.75)',
        borderRadius: 6,
        borderSkipped: false
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        x: {
          grid: {
            display: false
          },
          ticks: {
            color: '#9ca3af'
          }
        },
        y: {
          beginAtZero: true,
          grid: {
            color: '#f3f4f6'
          },
          ticks: {
            color: '#9ca3af'
          }
        }
      }
    }
  });
</script>