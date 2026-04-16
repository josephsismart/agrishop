<!DOCTYPE html>
<html lang="en">
<?php
$person_id  = $this->session->agrishop_person_id;
$login_name = $this->session->agrishop_login_first_name . ' ' . $this->session->agrishop_login_last_name;
?>
<head>
    <title><?= $system_title ?> | My Orders</title>
    <link rel="icon" type="image/png" href="<?= $system_svg ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url() ?>plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>plugins/bootstrap-alpha3/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>dist/css/adminlte.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <script src="<?= base_url() ?>plugins/jquery/jquery.min.js"></script>

<style>
* { box-sizing: border-box; }
body {
    background: #f8fafc;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    min-height: 100vh;
}

/* ── Top Nav ── */
.co-topnav {
    background: linear-gradient(135deg, #14532d 0%, #166534 50%, #15803d 100%);
    padding: 12px 20px;
    display: flex; align-items: center; gap: 16px;
    position: sticky; top: 0; z-index: 100;
    box-shadow: 0 2px 12px rgba(0,0,0,.18);
}
.co-topnav a.back-btn {
    color: rgba(255,255,255,.8);
    font-size: 14px; text-decoration: none;
    display: flex; align-items: center; gap: 6px;
    transition: color .15s;
}
.co-topnav a.back-btn:hover { color: #fff; }
.co-topnav .title {
    color: #fff; font-size: 18px; font-weight: 700; flex: 1;
}
.co-topnav .user-chip {
    background: rgba(255,255,255,.15);
    color: #fff; font-size: 12px; font-weight: 600;
    padding: 5px 12px; border-radius: 20px;
    display: flex; align-items: center; gap: 6px;
}

/* ── Page wrapper ── */
.co-wrap { max-width: 960px; margin: 0 auto; padding: 24px 16px 60px; }

/* ── Count cards ── */
.co-counts {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px; margin-bottom: 24px;
}
@media (max-width: 640px) { .co-counts { grid-template-columns: repeat(2, 1fr); } }
.co-count-card {
    background: #fff; border-radius: 14px;
    padding: 16px 14px; text-align: center;
    box-shadow: 0 1px 6px rgba(0,0,0,.06);
    cursor: pointer; transition: all .2s; border: 2px solid transparent;
}
.co-count-card:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,.1); }
.co-count-card.active { border-color: #16a34a; }
.co-count-card .num { font-size: 26px; font-weight: 800; line-height: 1; }
.co-count-card .lbl { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; margin-top: 4px; color: #6b7280; }

/* ── Filter tabs ── */
.co-tabs {
    display: flex; gap: 4px; margin-bottom: 16px;
    background: #fff; border-radius: 12px;
    padding: 6px; box-shadow: 0 1px 6px rgba(0,0,0,.06);
    overflow-x: auto;
}
.co-tab {
    flex-shrink: 0; padding: 8px 16px; border-radius: 8px;
    font-size: 13px; font-weight: 600; border: none; background: transparent;
    color: #6b7280; cursor: pointer; transition: all .2s; white-space: nowrap;
}
.co-tab:hover { background: #f3f4f6; color: #374151; }
.co-tab.active { background: #16a34a; color: #fff; }

/* ── Orders table card ── */
.co-card {
    background: #fff; border-radius: 16px;
    box-shadow: 0 1px 8px rgba(0,0,0,.06);
    overflow: hidden;
}
.co-card-head {
    padding: 16px 20px; border-bottom: 1px solid #f3f4f6;
    display: flex; align-items: center; justify-content: space-between; gap: 12px;
    flex-wrap: wrap;
}
.co-card-head h6 { margin: 0; font-weight: 700; font-size: 15px; color: #111827; }
.co-search {
    display: flex; align-items: center; gap: 8px;
    background: #f9fafb; border: 1.5px solid #e5e7eb; border-radius: 10px;
    padding: 7px 12px; max-width: 220px;
}
.co-search input {
    border: none; background: transparent; outline: none;
    font-size: 13px; width: 160px; color: #374151;
}
.co-search i { color: #9ca3af; font-size: 13px; }

/* DataTable overrides */
#tblOrders_wrapper .dataTables_info,
#tblOrders_wrapper .dataTables_paginate { padding: 12px 20px; }
#tblOrders thead th {
    background: #f0fdf4; color: #374151; font-size: 11px;
    font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
    border-bottom: 1px solid #d1fae5; padding: 12px 14px;
    white-space: nowrap;
}
#tblOrders tbody td { padding: 14px; vertical-align: middle; font-size: 13px; }
#tblOrders tbody tr { border-bottom: 1px solid #f9fafb; }
#tblOrders tbody tr:hover { background: #f0fdf4; }
.dataTables_wrapper .dataTables_filter { display: none; } /* using custom search */

/* ── Tracking modal ── */
#modalOrderTracking .modal-content {
    border: none; border-radius: 20px; overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,.2);
}

.trk-header {
    background: linear-gradient(135deg, #14532d, #16a34a);
    padding: 20px; position: sticky; top: 0; z-index: 1;
}
.trk-header .trk-title { color: #fff; font-size: 16px; font-weight: 700; margin: 0; }
.trk-header .trk-sub   { color: rgba(255,255,255,.7); font-size: 12px; margin-top: 2px; }
.trk-close {
    position: absolute; top: 14px; right: 16px;
    background: rgba(255,255,255,.2); border: none; color: #fff;
    width: 30px; height: 30px; border-radius: 50%; font-size: 16px;
    display: flex; align-items: center; justify-content: center; cursor: pointer;
}
.trk-close:hover { background: rgba(255,255,255,.35); }

.trk-body { padding: 20px; }

/* Farm info row */
.trk-farm-row {
    display: flex; align-items: center; gap: 12px;
    background: #f0fdf4; border-radius: 12px; padding: 12px 14px;
    margin-bottom: 16px;
}
.trk-farm-img {
    width: 44px; height: 44px; border-radius: 10px; object-fit: cover;
    flex-shrink: 0;
}
.trk-farm-name { font-weight: 700; font-size: 14px; color: #14532d; }
.trk-order-meta { font-size: 11px; color: #6b7280; margin-top: 2px; }

/* Status badge large */
.trk-status-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px; border-radius: 20px;
    font-size: 13px; font-weight: 700; margin-bottom: 16px;
}

/* ── Shopee-style pipeline ── */
.trk-pipeline {
    display: flex; align-items: flex-start;
    gap: 0; margin-bottom: 20px; overflow-x: auto;
    padding-bottom: 4px;
}
.trk-stage {
    flex: 1; min-width: 70px;
    display: flex; flex-direction: column; align-items: center; text-align: center;
    position: relative;
}
.trk-stage::before {
    content: ''; position: absolute;
    top: 19px; left: calc(-50% + 20px); right: calc(50% + 20px);
    height: 3px; background: #e5e7eb; z-index: 0;
}
.trk-stage:first-child::before { display: none; }
.trk-stage.done::before  { background: #16a34a; }
.trk-stage.curr::before  { background: #16a34a; }

.trk-stage-dot {
    width: 40px; height: 40px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; z-index: 1; position: relative;
    background: #e5e7eb; color: #9ca3af;
    transition: all .2s; border: 3px solid #fff;
    box-shadow: 0 0 0 2px #e5e7eb;
}
.trk-stage.done .trk-stage-dot  { background: #16a34a; color: #fff; box-shadow: 0 0 0 2px #16a34a; }
.trk-stage.curr .trk-stage-dot  { background: #fbbf24; color: #fff; box-shadow: 0 0 0 2px #fbbf24; animation: pulse-ring .9s infinite; }
.trk-stage.cancel .trk-stage-dot { background: #ef4444; color: #fff; box-shadow: 0 0 0 2px #ef4444; }

@keyframes pulse-ring {
    0%   { box-shadow: 0 0 0 2px #fbbf24, 0 0 0 4px rgba(251,191,36,.3); }
    100% { box-shadow: 0 0 0 2px #fbbf24, 0 0 0 10px rgba(251,191,36,0); }
}

.trk-stage-lbl { font-size: 10px; font-weight: 700; color: #6b7280; margin-top: 6px; line-height: 1.3; }
.trk-stage.done .trk-stage-lbl  { color: #16a34a; }
.trk-stage.curr .trk-stage-lbl  { color: #d97706; }
.trk-stage.cancel .trk-stage-lbl{ color: #ef4444; }
.trk-stage-date { font-size: 9px; color: #9ca3af; margin-top: 2px; }

/* ── Order items ── */
.trk-section-title {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .07em; color: #9ca3af; margin: 16px 0 10px;
    display: flex; align-items: center; gap: 6px;
}
.trk-section-title::after {
    content: ''; flex: 1; height: 1px; background: #f3f4f6;
}
.trk-item {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 0; border-bottom: 1px solid #f9fafb;
}
.trk-item:last-child { border-bottom: none; }
.trk-item-img {
    width: 44px; height: 44px; border-radius: 8px;
    object-fit: cover; flex-shrink: 0; background: #f3f4f6;
    display: flex; align-items: center; justify-content: center;
}
.trk-item-name  { font-weight: 600; font-size: 13px; }
.trk-item-meta  { font-size: 11px; color: #9ca3af; margin-top: 2px; }
.trk-item-price { margin-left: auto; font-weight: 700; font-size: 13px; color: #16a34a; white-space: nowrap; }

/* ── Total row ── */
.trk-total-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 14px 0; border-top: 2px solid #f3f4f6; margin-top: 4px;
}
.trk-total-lbl { font-size: 13px; font-weight: 600; color: #374151; }
.trk-total-amt { font-size: 18px; font-weight: 800; color: #16a34a; }

/* ── Info chips row ── */
.trk-info-row {
    display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px;
}
.trk-info-chip {
    background: #f3f4f6; border-radius: 8px; padding: 7px 12px;
    font-size: 11px; color: #374151; display: flex; align-items: center; gap: 5px;
}
.trk-info-chip i { color: #16a34a; }

/* ── Cancel btn ── */
.trk-cancel-btn {
    width: 100%; margin-top: 16px; padding: 12px;
    background: transparent; border: 2px solid #ef4444; color: #ef4444;
    border-radius: 12px; font-size: 14px; font-weight: 700;
    cursor: pointer; transition: all .2s; display: none;
}
.trk-cancel-btn:hover { background: #fef2f2; }
.trk-cancel-btn.visible { display: block; }

/* ── History timeline ── */
.trk-history-item {
    display: flex; gap: 12px; padding: 8px 0; align-items: flex-start;
}
.trk-history-dot {
    width: 10px; height: 10px; border-radius: 50%; background: #e5e7eb;
    flex-shrink: 0; margin-top: 4px;
}
.trk-history-item.latest .trk-history-dot { background: #16a34a; }
.trk-history-info { flex: 1; }
.trk-history-lbl  { font-size: 12px; font-weight: 600; }
.trk-history-meta { font-size: 11px; color: #9ca3af; }

/* Empty state */
.co-empty {
    text-align: center; padding: 60px 20px; color: #9ca3af;
}
.co-empty i { font-size: 48px; margin-bottom: 12px; display: block; }
.co-empty p { font-size: 14px; }
</style>
</head>

<body>

<!-- Top Nav -->
<nav class="co-topnav">
    <a href="<?= base_url('index') ?>" class="back-btn">
        <i class="fa fa-arrow-left"></i> Back
    </a>
    <div class="title"><i class="fa fa-box mr-2" style="font-size:15px;"></i>My Orders</div>
    <div class="user-chip">
        <i class="fa fa-user" style="font-size:11px;"></i>
        <?= htmlspecialchars($login_name) ?>
    </div>
</nav>

<div class="co-wrap">

    <!-- Count cards -->
    <div class="co-counts">
        <div class="co-count-card" onclick="setFilter('ALL')" id="card-ALL">
            <div class="num" style="color:#374151;"><?= ((int)$counts->pending + (int)$counts->to_receive + (int)$counts->completed + (int)$counts->cancelled) ?></div>
            <div class="lbl">All Orders</div>
        </div>
        <div class="co-count-card" onclick="setFilter('TO_RECEIVE')" id="card-TO_RECEIVE">
            <div class="num" style="color:#1e40af;"><?= (int)$counts->to_receive ?></div>
            <div class="lbl">To Receive</div>
        </div>
        <div class="co-count-card" onclick="setFilter('COMPLETED')" id="card-COMPLETED">
            <div class="num" style="color:#14532d;"><?= (int)$counts->completed ?></div>
            <div class="lbl">Completed</div>
        </div>
        <div class="co-count-card" onclick="setFilter('CANCELLED')" id="card-CANCELLED">
            <div class="num" style="color:#991b1b;"><?= (int)$counts->cancelled ?></div>
            <div class="lbl">Cancelled</div>
        </div>
    </div>

    <!-- Filter tabs -->
    <div class="co-tabs">
        <button class="co-tab active" data-filter="ALL"       onclick="setFilter('ALL')">All</button>
        <button class="co-tab"        data-filter="PENDING"    onclick="setFilter('PENDING')">Pending</button>
        <button class="co-tab"        data-filter="TO_RECEIVE" onclick="setFilter('TO_RECEIVE')">To Receive</button>
        <button class="co-tab"        data-filter="COMPLETED"  onclick="setFilter('COMPLETED')">Completed</button>
        <button class="co-tab"        data-filter="CANCELLED"  onclick="setFilter('CANCELLED')">Cancelled</button>
    </div>

    <!-- Orders Table Card -->
    <div class="co-card">
        <div class="co-card-head">
            <h6><i class="fa fa-list mr-2 text-success"></i>Order History</h6>
            <div class="co-search">
                <i class="fa fa-search"></i>
                <input type="text" id="ordersSearch" placeholder="Search orders..." oninput="tblOrders.search(this.value).draw()">
            </div>
        </div>

        <div style="padding: 0 8px 16px;">
            <table id="tblOrders" class="table" style="width:100%;">
                <thead>
                    <tr>
                        <th>Farmer / Supplier</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Amount</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div><!-- .co-wrap -->

<!-- ══════════ Order Tracking Modal ══════════ -->
<div class="modal fade" id="modalOrderTracking" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:560px;">
        <div class="modal-content">

            <div class="trk-header" style="position:relative;">
                <p class="trk-title"><i class="fa fa-route mr-2"></i>Order Tracking</p>
                <p class="trk-sub" id="trkOrderNum">Loading...</p>
                <button class="trk-close" data-dismiss="modal">×</button>
            </div>

            <div class="trk-body" id="trkBody" style="max-height:70vh;overflow-y:auto;">
                <div style="text-align:center;padding:40px;">
                    <i class="fa fa-circle-notch fa-spin fa-2x text-success"></i>
                    <p style="margin-top:12px;color:#9ca3af;">Loading order details...</p>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="<?= base_url() ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>

<script>
let currentFilter = 'ALL';
let tblOrders;

$(function() {
    tblOrders = $('#tblOrders').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('usercustomer/Orders/getOrders') ?>',
            type: 'POST',
            data: function(d) { d.filter = currentFilter; }
        },
        columns: [
            { data: 'farm',   render: function(d) { return '<div style="display:flex;align-items:center;">' + d + '</div>'; } },
            { data: 'date' },
            { data: 'status' },
            { data: 'amount' },
            { data: 'action', orderable: false }
        ],
        language: {
            emptyTable: '<div class="co-empty"><i class="fa fa-box-open"></i><p>No orders found</p></div>',
            processing: '<i class="fa fa-circle-notch fa-spin text-success"></i> Loading...',
        },
        pageLength: 10,
        lengthChange: false,
        dom: '<"row"<"col-sm-12"t>><"row"<"col-sm-5"i><"col-sm-7"p>>',
    });
});

function setFilter(f) {
    currentFilter = f;
    // Update tab
    document.querySelectorAll('.co-tab').forEach(b => {
        b.classList.toggle('active', b.dataset.filter === f);
    });
    // Update cards
    document.querySelectorAll('.co-count-card').forEach(c => {
        c.classList.toggle('active', c.id === 'card-' + f);
    });
    tblOrders.ajax.reload();
}

// ── Tracking Modal ─────────────────────────────────────────
function viewOrderTracking(txId) {
    $('#trkOrderNum').text('Order #' + txId);
    $('#trkBody').html('<div style="text-align:center;padding:40px;"><i class="fa fa-circle-notch fa-spin fa-2x text-success"></i><p style="margin-top:12px;color:#9ca3af;">Loading...</p></div>');
    $('#modalOrderTracking').modal('show');

    $.post('<?= base_url('usercustomer/Orders/getTracking') ?>', { transaction_id: txId }, function(res) {
        if (!res.success) { $('#trkBody').html('<p class="text-center text-muted p-4">Could not load order.</p>'); return; }
        renderTrackingBody(res);
    }, 'json');
}

function renderTrackingBody(d) {
    const si = d.status_info;

    // Pipeline HTML
    let pipelineHtml = '<div class="trk-pipeline">';
    d.pipeline.forEach(function(s) {
        const cls = s.done ? (s.is_current ? 'curr' : 'done') : (s.stage === 'CANCELLED' && s.done ? 'cancel' : '');
        const cancelCls = s.stage === 'CANCELLED' && s.done ? 'cancel' : cls;
        const dotDate = s.date ? '<div class="trk-stage-date">' + s.date.split(' ').slice(0,2).join(' ') + '</div>' : '';
        pipelineHtml += `<div class="trk-stage ${cancelCls}">
            <div class="trk-stage-dot"><i class="fa ${s.icon}"></i></div>
            <div class="trk-stage-lbl">${s.label}</div>
            ${dotDate}
        </div>`;
    });
    pipelineHtml += '</div>';

    // Items HTML
    let itemsHtml = '';
    d.items.forEach(function(i) {
        const imgTag = i.img
            ? `<img src="${i.img}" class="trk-item-img" alt="">`
            : `<div class="trk-item-img"><i class="fa fa-leaf text-success"></i></div>`;
        itemsHtml += `<div class="trk-item">
            ${imgTag}
            <div style="flex:1;">
                <div class="trk-item-name">${i.name}</div>
                <div class="trk-item-meta">${i.qty} ${i.uom} × ₱${i.price}</div>
            </div>
            <div class="trk-item-price">₱${i.subtotal}</div>
        </div>`;
    });

    // History HTML
    let histHtml = '';
    [...d.history].reverse().forEach(function(h) {
        histHtml += `<div class="trk-history-item ${h.is_latest ? 'latest' : ''}">
            <div class="trk-history-dot"></div>
            <div class="trk-history-info">
                <div class="trk-history-lbl">${h.label}</div>
                <div class="trk-history-meta">${h.date}${h.updated_by ? ' · ' + h.updated_by : ''}</div>
            </div>
        </div>`;
    });

    const farmImgTag = d.farm_img
        ? `<img src="${d.farm_img}" class="trk-farm-img" alt="">`
        : `<div class="trk-farm-img" style="background:#d1fae5;display:flex;align-items:center;justify-content:center;"><i class="fa fa-store text-success"></i></div>`;

    const canCancel = d.current_status === 'PENDING';
    const delivMethod = d.delivery_method ? d.delivery_method.toUpperCase() : '–';
    const payMethod   = d.payment_method  ? d.payment_method.toUpperCase()  : '–';

    $('#trkOrderNum').text('Order #' + d.transaction_id + ' · ' + d.date_ordered);

    $('#trkBody').html(`
        <div class="trk-farm-row">
            ${farmImgTag}
            <div>
                <div class="trk-farm-name">${d.farm_name}</div>
                <div class="trk-order-meta">Ordered ${d.date_ordered}</div>
            </div>
        </div>

        <span class="trk-status-badge" style="background:${si.bg};color:${si.color};">
            ${si.icon} ${si.label}
        </span>

        <div class="trk-info-row">
            <div class="trk-info-chip"><i class="fa fa-credit-card"></i> ${payMethod}</div>
            <div class="trk-info-chip"><i class="fa fa-truck"></i> ${delivMethod}</div>
            ${d.delivery_address ? '<div class="trk-info-chip"><i class="fa fa-map-marker-alt"></i> ' + d.delivery_address + '</div>' : ''}
        </div>

        <div class="trk-section-title"><i class="fa fa-route"></i> Order Pipeline</div>
        ${pipelineHtml}

        <div class="trk-section-title"><i class="fa fa-box-open"></i> Items Ordered</div>
        ${itemsHtml}
        <div class="trk-total-row">
            <div>
                <div class="trk-total-lbl">Total Payment</div>
                <div style="font-size:11px;color:#9ca3af;margin-top:2px;">₱${d.subtotal} + ₱${d.fee} service fee (1%)</div>
            </div>
            <div class="trk-total-amt">₱${d.total}</div>
        </div>

        <div class="trk-section-title"><i class="fa fa-history"></i> Activity Log</div>
        <div style="padding: 0 0 8px;">
            ${histHtml || '<p style="color:#9ca3af;font-size:13px;padding:8px 0;">No activity yet.</p>'}
        </div>

        <button class="trk-cancel-btn ${canCancel ? 'visible' : ''}" onclick="cancelOrder(${d.transaction_id})">
            <i class="fa fa-times-circle mr-2"></i>Cancel Order
        </button>
    `);
}

function closeTrackingSheet() {
    $('#modalOrderTracking').modal('hide');
}

function cancelOrder(txId) {
    Swal.fire({
        title: 'Cancel Order?',
        html: '<p style="font-size:14px;color:#374151;">Please enter a reason for cancellation:</p>'
            + '<textarea id="cancelReason" class="swal2-textarea" placeholder="e.g. Changed my mind..." style="font-size:13px;"></textarea>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Cancel Order',
        confirmButtonColor: '#ef4444',
        cancelButtonText: 'No, Keep It',
        preConfirm: () => {
            const reason = document.getElementById('cancelReason').value.trim();
            return reason || 'Cancelled by customer';
        }
    }).then(function(result) {
        if (!result.isConfirmed) return;
        $.post('<?= base_url('usercustomer/Orders/cancelOrder') ?>', {
            transaction_id: txId,
            reason: result.value
        }, function(res) {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Order Cancelled', timer: 2000, showConfirmButton: false });
                closeTrackingSheet();
                tblOrders.ajax.reload();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Could not cancel.' });
            }
        }, 'json');
    });
}
</script>
</body>
</html>
