<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>AgriShop – <?= ucfirst($report_type) ?> Report</title>
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Segoe UI',Arial,sans-serif;background:#fff;color:#111;font-size:11.5px;line-height:1.5;}
/* Cover */
.cover{background:linear-gradient(135deg,#1a472a,#2d6a4f,#22c55e);color:#fff;padding:32px 40px;}
.cover h1{font-size:20px;font-weight:800;margin-bottom:4px;}
.cover .sub{font-size:12px;opacity:.85;}
.cover .tags{display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;}
.cover .tag{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);border-radius:20px;padding:3px 14px;font-size:11px;font-weight:600;}
/* No-print bar */
.no-print{padding:10px 40px;background:#f9fafb;border-bottom:1px solid #e5e7eb;display:flex;gap:8px;align-items:center;}
.no-print button{border:none;border-radius:8px;padding:8px 18px;font-size:12px;font-weight:700;cursor:pointer;}
.btn-print{background:#1a472a;color:#fff;}
.btn-close{background:#f3f4f6;color:#374151;border:1.5px solid #e5e7eb !important;}
/* Sections */
.section{padding:18px 40px;}
.section-title{font-size:12px;font-weight:800;color:#166534;text-transform:uppercase;letter-spacing:.06em;border-bottom:2px solid #22c55e;padding-bottom:5px;margin-bottom:12px;}
/* Stat cards */
.stats-row{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px;}
.stat-box{flex:1;min-width:120px;border:1px solid #e5e7eb;border-radius:10px;padding:12px 14px;border-left:4px solid #22c55e;}
.stat-box .val{font-size:18px;font-weight:800;color:#111;}
.stat-box .lbl{font-size:9.5px;font-weight:700;color:#9ca3af;text-transform:uppercase;margin-bottom:2px;}
.stat-box .sub{font-size:10px;color:#6b7280;margin-top:2px;}
/* Tables */
table{width:100%;border-collapse:collapse;font-size:11px;margin-bottom:14px;}
th{background:#f0fdf4;color:#166534;font-weight:700;padding:7px 10px;text-align:left;font-size:10px;text-transform:uppercase;letter-spacing:.05em;border-bottom:2px solid #22c55e;}
td{padding:7px 10px;border-bottom:1px solid #f3f4f6;}
tr:nth-child(even) td{background:#fafafa;}
.badge{padding:2px 7px;border-radius:20px;font-size:10px;font-weight:700;}
.badge-COMPLETED,.badge-success,.badge-Approved{background:#dcfce7;color:#166534;}
.badge-RESERVED,.badge-info{background:#dbeafe;color:#1e40af;}
.badge-PREPARING,.badge-warning{background:#fef9c3;color:#92400e;}
.badge-CANCELLED,.badge-danger{background:#fee2e2;color:#991b1b;}
.badge-secondary{background:#f3f4f6;color:#6b7280;}
.two-col{display:flex;gap:16px;}
.two-col>div{flex:1;}
/* Footer */
.rpt-footer{background:#f0fdf4;border-top:2px solid #22c55e;padding:10px 40px;display:flex;justify-content:space-between;font-size:10px;color:#9ca3af;}
@media print{
  .no-print{display:none!important;}
  body{-webkit-print-color-adjust:exact;print-color-adjust:exact;}
  .cover{-webkit-print-color-adjust:exact;print-color-adjust:exact;}
}
</style>
</head>
<body>

<!-- Cover -->
<div class="cover">
  <h1>🌾 AGRI-SHOP — <?= strtoupper($report_type) ?> REPORT</h1>
  <p class="sub">Generated on <?= htmlspecialchars($generated_at) ?> &nbsp;•&nbsp; By: <?= htmlspecialchars($generated_by) ?></p>
  <div class="tags">
    <span class="tag">📅 Period: <?= date('M j, Y', strtotime($date_from)) ?> – <?= date('M j, Y', strtotime($date_to)) ?></span>
    <span class="tag">📊 <?= ucfirst($report_type) ?> Report</span>
    <span class="tag">🌾 AgriShop Admin</span>
  </div>
</div>

<!-- Print / Close buttons -->
<div class="no-print">
  <button class="btn-print" onclick="window.print()">🖨 Print / Save as PDF</button>
  <button class="btn-close" onclick="window.close()">✕ Close</button>
  <small style="color:#9ca3af;margin-left:8px;font-size:11px;">💡 Use your browser's <strong>Save as PDF</strong> option in the print dialog</small>
</div>

<!-- Summary Stats -->
<div class="section">
<div class="section-title">Summary Overview</div>
<div class="stats-row">
  <?php if (isset($total_orders)): ?>
  <div class="stat-box"><div class="lbl">Total Orders</div><div class="val"><?= number_format($total_orders) ?></div><div class="sub">Completed: <?= number_format($completed ?? 0) ?></div></div>
  <?php endif; ?>
  <?php if (isset($total_revenue)): ?>
  <div class="stat-box" style="border-left-color:#3b82f6;"><div class="lbl">Total Revenue</div><div class="val">₱<?= number_format($total_revenue, 2) ?></div><div class="sub">Avg: ₱<?= number_format($avg_order ?? 0, 2) ?></div></div>
  <?php endif; ?>
  <?php if (isset($system_income)): ?>
  <div class="stat-box" style="border-left-color:#8b5cf6;"><div class="lbl">System Income</div><div class="val">₱<?= number_format($system_income, 2) ?></div><div class="sub">All sources</div></div>
  <?php endif; ?>
  <?php if (isset($active_buyers)): ?>
  <div class="stat-box" style="border-left-color:#f97316;"><div class="lbl">Active Buyers</div><div class="val"><?= number_format($active_buyers) ?></div><div class="sub">Unique buyers</div></div>
  <?php endif; ?>
  <?php if (isset($total_farms)): ?>
  <div class="stat-box" style="border-left-color:#eab308;"><div class="lbl">Total Farms</div><div class="val"><?= number_format($total_farms) ?></div><div class="sub">Registered</div></div>
  <?php endif; ?>
  <?php if (isset($total_farmers)): ?>
  <div class="stat-box"><div class="lbl">Farmers</div><div class="val"><?= number_format($total_farmers) ?></div><div class="sub">Active</div></div>
  <?php endif; ?>
  <?php if (isset($total_units)): ?>
  <div class="stat-box" style="border-left-color:#06b6d4;"><div class="lbl">Units Sold</div><div class="val"><?= number_format($total_units) ?></div><div class="sub">All products</div></div>
  <?php endif; ?>
  <?php if (isset($total_users)): ?>
  <div class="stat-box" style="border-left-color:#3b82f6;"><div class="lbl">New Users</div><div class="val"><?= number_format($total_users) ?></div><div class="sub">In period</div></div>
  <?php endif; ?>
</div>
</div>

<!-- Top Farmers + Fast Products (side by side for comprehensive/sales) -->
<?php if (!empty($top_farmers)): ?>
<div class="section" style="padding-top:0;">
<div class="two-col">
<div>
  <div class="section-title">🏆 Top Performing Farmers</div>
  <table>
    <thead><tr><th>Rank</th><th>Farmer Name</th><th>Orders</th><th>Total Sales</th></tr></thead>
    <tbody>
    <?php $medals=['🥇','🥈','🥉']; foreach ($top_farmers as $i => $f): ?>
    <tr>
      <td><?= $medals[$i] ?? ($i+1) ?></td>
      <td style="font-weight:600;"><?= htmlspecialchars($f['farmer_name'] ?? '—') ?></td>
      <td><?= number_format($f['total_orders'] ?? 0) ?></td>
      <td style="font-weight:700;color:#166534;">₱<?= number_format($f['total_sales'] ?? 0, 2) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php if (!empty($fast_products)): ?>
<div>
  <div class="section-title">⚡ Fast-Moving Products</div>
  <table>
    <thead><tr><th>Product</th><th>Category</th><th>Units Sold</th></tr></thead>
    <tbody>
    <?php foreach ($fast_products as $p): ?>
    <tr>
      <td style="font-weight:600;"><?= htmlspecialchars($p['product_name'] ?? '—') ?></td>
      <td><?= htmlspecialchars($p['category'] ?? '—') ?></td>
      <td><?= number_format($p['units_sold'] ?? 0) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>
</div>
</div>
<?php endif; ?>

<!-- Slow Products -->
<?php if (!empty($slow_products)): ?>
<div class="section" style="padding-top:0;">
  <div class="section-title">🐢 Slow-Moving Products (Needs Promotion)</div>
  <table>
    <thead><tr><th>Product</th><th>Category</th><th>Units Sold</th></tr></thead>
    <tbody>
    <?php foreach ($slow_products as $p): ?>
    <tr>
      <td style="font-weight:600;"><?= htmlspecialchars($p['product_name'] ?? '—') ?></td>
      <td><?= htmlspecialchars($p['category'] ?? '—') ?></td>
      <td><?= number_format($p['units_sold'] ?? 0) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<!-- Orders List -->
<?php if (!empty($orders_list)): ?>
<div class="section" style="padding-top:0;">
  <div class="section-title">📋 Orders Detail (<?= count($orders_list) ?> records)</div>
  <table>
    <thead><tr><th>#</th><th>Date</th><th>Buyer</th><th>Farmer / Farm</th><th>Amount</th><th>To Farmer</th><th>Status</th><th>Delivery</th></tr></thead>
    <tbody>
    <?php foreach ($orders_list as $i => $o): ?>
    <tr>
      <td><?= $i+1 ?></td>
      <td style="white-space:nowrap;"><?= htmlspecialchars($o['txn_date'] ?? '—') ?></td>
      <td style="font-weight:600;"><?= htmlspecialchars($o['buyer'] ?? '—') ?></td>
      <td><?= htmlspecialchars($o['farmer_name'] ?? '—') ?> <small><?= htmlspecialchars($o['farm_name'] ?? '') ?></small></td>
      <td style="font-weight:700;color:#166534;">₱<?= number_format($o['total_payment'] ?? 0, 2) ?></td>
      <td>₱<?= number_format($o['to_farmer'] ?? 0, 2) ?></td>
      <td><span class="badge badge-<?= htmlspecialchars($o['status'] ?? '') ?>"><?= htmlspecialchars($o['status'] ?? '—') ?></span></td>
      <td><span class="badge badge-secondary"><?= htmlspecialchars($o['delivery_status'] ?? '—') ?></span></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<!-- Daily Sales -->
<?php if (!empty($daily_sales)): ?>
<div class="section" style="padding-top:0;">
  <div class="section-title">📅 Daily Sales Breakdown</div>
  <table>
    <thead><tr><th>Date</th><th>Orders</th><th>Revenue</th><th>Admin Income</th><th>Farmer Income</th></tr></thead>
    <tbody>
    <?php foreach ($daily_sales as $d): ?>
    <tr>
      <td><?= $d['sale_date'] ?? '—' ?></td>
      <td><?= number_format($d['orders'] ?? 0) ?></td>
      <td style="font-weight:700;color:#166534;">₱<?= number_format($d['revenue'] ?? 0, 2) ?></td>
      <td>₱<?= number_format($d['admin_income'] ?? 0, 2) ?></td>
      <td>₱<?= number_format($d['farmer_income'] ?? 0, 2) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<!-- Farmers list -->
<?php if (!empty($farmers_list)): ?>
<div class="section" style="padding-top:0;">
  <div class="section-title">👨‍🌾 Farmers Performance (<?= count($farmers_list) ?> farmers)</div>
  <table>
    <thead><tr><th>#</th><th>Farmer</th><th>Email</th><th>Contact</th><th>Farms</th><th>Orders</th><th>Revenue</th><th>Joined</th><th>Status</th></tr></thead>
    <tbody>
    <?php foreach ($farmers_list as $i => $f): ?>
    <tr>
      <td><?= $i+1 ?></td>
      <td style="font-weight:600;"><?= htmlspecialchars($f['farmer_name'] ?? '—') ?></td>
      <td><?= htmlspecialchars($f['email'] ?? '—') ?></td>
      <td><?= htmlspecialchars($f['contact_num'] ?? '—') ?></td>
      <td><?= number_format($f['total_farms'] ?? 0) ?></td>
      <td><?= number_format($f['total_orders'] ?? 0) ?></td>
      <td style="font-weight:700;color:#166534;">₱<?= number_format($f['total_sales'] ?? 0, 2) ?></td>
      <td><?= htmlspecialchars($f['joined_date'] ?? '—') ?></td>
      <td><span class="badge badge-<?= $f['status']==='Approved'?'success':'warning' ?>"><?= $f['status'] ?></span></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<!-- Products list -->
<?php if (!empty($products_list)): ?>
<div class="section" style="padding-top:0;">
  <div class="section-title">🌾 Product Movement (<?= count($products_list) ?> products)</div>
  <table>
    <thead><tr><th>#</th><th>Product</th><th>Category</th><th>Units Sold</th><th>Revenue</th></tr></thead>
    <tbody>
    <?php foreach ($products_list as $i => $p): ?>
    <tr>
      <td><?= $i+1 ?></td>
      <td style="font-weight:600;"><?= htmlspecialchars($p['product_name'] ?? '—') ?></td>
      <td><?= htmlspecialchars($p['category'] ?? '—') ?></td>
      <td><?= number_format($p['units_sold'] ?? 0) ?></td>
      <td style="font-weight:700;color:#166534;">₱<?= number_format($p['revenue'] ?? 0, 2) ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<!-- Users list -->
<?php if (!empty($users_list)): ?>
<div class="section" style="padding-top:0;">
  <div class="section-title">👥 Users (<?= count($users_list) ?> records)</div>
  <table>
    <thead><tr><th>#</th><th>Full Name</th><th>Email</th><th>Contact</th><th>Role</th><th>Status</th><th>Joined</th></tr></thead>
    <tbody>
    <?php foreach ($users_list as $i => $u): ?>
    <tr>
      <td><?= $i+1 ?></td>
      <td style="font-weight:600;"><?= htmlspecialchars($u['full_name'] ?? '—') ?></td>
      <td><?= htmlspecialchars($u['email'] ?? '—') ?></td>
      <td><?= htmlspecialchars($u['contact_num'] ?? '—') ?></td>
      <td><?= htmlspecialchars($u['role'] ?? '—') ?></td>
      <td><span class="badge badge-<?= $u['status']==1?'success':'secondary' ?>"><?= $u['status']==1?'Active':'Inactive' ?></span></td>
      <td><?= htmlspecialchars($u['joined_date'] ?? '—') ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<!-- Footer -->
<div class="rpt-footer">
  <span>🌾 AgriShop &copy; <?= date('Y') ?></span>
  <span>Generated by <?= htmlspecialchars($generated_by) ?> on <?= htmlspecialchars($generated_at) ?></span>
  <span>Period: <?= date('M j, Y', strtotime($date_from)) ?> – <?= date('M j, Y', strtotime($date_to)) ?></span>
</div>

</body>
</html>
