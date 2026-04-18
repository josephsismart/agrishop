<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!$this->session->agrishop_login_level) {
    redirect(base_url('login'));
}
$uri = $this->session->agrishop_login_uri;
?>

<style>
/* ── Page wrapper ── */
.orders-page { background: #f4f6fb; min-height: 100vh; padding-bottom: 40px; }

/* ── Hero header ── */
.orders-hero {
    background: linear-gradient(135deg, #1a472a 0%, #2d6a4f 60%, #40916c 100%);
    padding: 24px 28px 18px;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.orders-hero::before {
    content: '';
    position: absolute; top: -40px; right: -40px;
    width: 200px; height: 200px;
    background: rgba(255,255,255,.06);
    border-radius: 50%;
}
.orders-hero::after {
    content: '';
    position: absolute; bottom: -60px; right: 80px;
    width: 140px; height: 140px;
    background: rgba(255,255,255,.04);
    border-radius: 50%;
}
.orders-hero h4 { font-size: 20px; font-weight: 700; margin: 0 0 4px; letter-spacing: -.3px; }
.orders-hero p  { font-size: 13px; margin: 0; opacity: .75; }

/* ── Tab bar ── */
.orders-tabs {
    display: flex; gap: 6px;
    padding: 16px 20px 0;
}
.order-tab-btn {
    flex: 1;
    padding: 10px 8px;
    border: none;
    border-radius: 10px;
    font-size: 13px; font-weight: 700;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 6px;
    transition: all .18s;
    background: #fff;
    color: #9ca3af;
    box-shadow: 0 1px 4px rgba(0,0,0,.07);
}
.order-tab-btn:hover { color: #374151; }
.order-tab-btn.tab-active-incoming  { background: #fef3c7; color: #92400e; box-shadow: 0 2px 8px rgba(245,158,11,.25); }
.order-tab-btn.tab-active-completed { background: #d1fae5; color: #065f46; box-shadow: 0 2px 8px rgba(16,185,129,.2); }
.order-tab-btn.tab-active-cancelled { background: #fee2e2; color: #991b1b; box-shadow: 0 2px 8px rgba(239,68,68,.2); }

/* ── Table wrapper ── */
.orders-table-wrap {
    margin: 14px 20px 0;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    overflow: hidden;
}

/* ── DataTable overrides ── */
#tblCartListing_wrapper .dataTables_filter input {
    border: 1.5px solid #e5e7eb; border-radius: 8px;
    padding: 6px 12px; font-size: 13px;
}
#tblCartListing_wrapper .dataTables_filter { padding: 12px 16px 0; }
#tblCartListing_wrapper .dataTables_info,
#tblCartListing_wrapper .dataTables_paginate { padding: 8px 16px 12px; font-size: 12px; }
#tblCartListing thead { display: none; }
#tblCartListing_wrapper td { padding: 0 !important; border: none !important; }

/* ── Individual order card row ── */
.ocard {
    border-left: 4px solid transparent;
    padding: 14px 16px 12px;
    position: relative;
    transition: background .15s;
}
.ocard:hover { background: #f0fdf4; }
.ocard + .ocard { border-top: 1px solid #f3f4f6; }

.ocard.status-RESERVED       { border-left-color: #3b82f6; }
.ocard.status-PREPARING       { border-left-color: #f59e0b; }
.ocard.status-ORDER_IS_READY  { border-left-color: #10b981; }
.ocard.status-COMPLETED       { border-left-color: #6b7280; }
.ocard.status-CANCELLED       { border-left-color: #ef4444; }

/* avatar */
.ocard-avatar {
    width: 52px; height: 52px;
    border-radius: 12px;
    object-fit: cover;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0,0,0,.12);
}

/* amount badge */
.ocard-amount {
    background: #ecfdf5;
    color: #065f46;
    border-radius: 10px;
    padding: 4px 10px;
    font-size: 15px;
    font-weight: 800;
    white-space: nowrap;
}
.ocard-amount.amt-cancelled { background: #f9fafb; color: #9ca3af; }

/* new badge */
.badge-new {
    background: #16a34a; color: #fff;
    font-size: 9px; font-weight: 800;
    padding: 2px 6px; border-radius: 20px;
    text-transform: uppercase; letter-spacing: .08em;
    animation: pulse-new 1.8s infinite;
    vertical-align: middle;
}
@keyframes pulse-new {
    0%, 100% { box-shadow: 0 0 0 0 rgba(22,163,74,.5); }
    50%       { box-shadow: 0 0 0 6px rgba(22,163,74,0); }
}

/* action buttons */
.ocard-actions { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin-top: 10px; }

.btn-view-order {
    border: none; background: #f3f4f6; color: #374151;
    border-radius: 8px; padding: 5px 12px;
    font-size: 12px; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; gap: 5px;
    transition: all .15s;
}
.btn-view-order:hover { background: #e5e7eb; }

.btn-update-status {
    border: none; background: #2d6a4f; color: #fff;
    border-radius: 8px; padding: 5px 14px;
    font-size: 12px; font-weight: 700; cursor: pointer;
    display: flex; align-items: center; gap: 5px;
    transition: all .15s;
}
.btn-update-status:hover { background: #1a472a; transform: translateY(-1px); }

.btn-gcash {
    border: 1.5px solid #e5e7eb; background: #fff;
    border-radius: 8px; padding: 4px 8px;
    cursor: pointer; transition: all .15s;
}
.btn-gcash:hover { border-color: #3b82f6; transform: translateY(-1px); }

/* mini status pills */
.mini-pill {
    display: inline-flex; align-items: center; gap: 3px;
    font-size: 10px; font-weight: 700;
    padding: 2px 7px; border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.pill-RESERVED      { background: #dbeafe; color: #1d4ed8; }
.pill-PREPARING     { background: #fef3c7; color: #92400e; }
.pill-ORDER_IS_READY{ background: #d1fae5; color: #065f46; }
.pill-COMPLETED     { background: #f3f4f6; color: #6b7280; }
.pill-CANCELLED     { background: #fee2e2; color: #991b1b; }
.pill-TO_PICKUP     { background: #ede9fe; color: #5b21b6; }
.pill-TO_DELIVER    { background: #e0f2fe; color: #0369a1; }
.pill-ON_THE_WAY    { background: #fef9c3; color: #713f12; }
.pill-DELIVERED     { background: #dcfce7; color: #166534; }
.pill-UNPAID        { background: #fee2e2; color: #991b1b; }
.pill-VERIFYING     { background: #fef3c7; color: #92400e; }
.pill-PAID          { background: #dcfce7; color: #166534; }
.pill-FAILED        { background: #fce7f3; color: #9d174d; }

/* billing alert bar */
.billing-bar {
    background: linear-gradient(90deg, #f59e0b, #fbbf24);
    color: #78350f; font-size: 13px; font-weight: 700;
    padding: 8px 20px; display: flex; align-items: center; gap: 8px;
}
.billing-bar a { color: #78350f; text-decoration: underline; margin-left: auto; }

/* ── Inline items section ── */
.oitem-toggle {
    display: flex; align-items: center; justify-content: space-between;
    margin-top: 10px;
    background: #f0fdf4; border: 1.5px solid #bbf7d0;
    border-radius: 8px; padding: 7px 12px;
    cursor: pointer; font-size: 12px; font-weight: 700;
    color: #065f46; transition: background .15s;
    user-select: none;
}
.oitem-toggle:hover { background: #dcfce7; }
.oitem-chevron { font-size: 11px; transition: transform .2s; }
.oitem-toggle.open .oitem-chevron { transform: rotate(180deg); }

.oitem-list {
    display: none;
    margin-top: 6px;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
    background: #fafafa;
}

.oitem-row {
    display: flex; align-items: center; gap: 10px;
    padding: 8px 12px;
    border-bottom: 1px solid #f3f4f6;
}
.oitem-row:last-child { border-bottom: none; }
.oitem-img {
    width: 42px; height: 42px;
    border-radius: 8px; object-fit: cover;
    box-shadow: 0 1px 4px rgba(0,0,0,.1);
    flex-shrink: 0;
}
.oitem-info { flex: 1; }
.oitem-name { font-size: 13px; font-weight: 700; color: #111; }
.oitem-qty  { font-size: 11px; color: #9ca3af; margin-top: 2px; }
.oitem-price { font-size: 13px; font-weight: 800; color: #065f46; white-space: nowrap; }
.oitem-ws-badge {
    font-size: 9px; background: #e5e7eb; color: #374151;
    padding: 1px 5px; border-radius: 10px;
    vertical-align: middle; margin-left: 4px;
}

.oitem-summary {
    background: #f9fafb; border-top: 1.5px dashed #e5e7eb;
    padding: 8px 12px; font-size: 12px;
}
.oitem-summary-row {
    display: flex; justify-content: space-between;
    margin-bottom: 3px; color: #6b7280;
}
.oitem-total {
    font-weight: 800; font-size: 14px;
    color: #065f46; margin-top: 5px; padding-top: 5px;
    border-top: 1.5px solid #d1fae5;
    margin-bottom: 0;
}

.oitem-empty {
    margin-top: 8px; font-size: 12px; color: #9ca3af;
    padding: 6px 10px;
}
</style>

<div class="orders-page">

    <!-- Hero header -->
    <div class="orders-hero">
        <div style="position:relative;z-index:1;">
            <h4><i class="fas fa-shopping-basket mr-2"></i> Client Orders</h4>
            <p>Manage and track your incoming farm produce orders</p>
        </div>
    </div>

    <?php if ($billing['count'] > 0) : ?>
    <div class="billing-bar">
        <i class="fa fa-exclamation-triangle"></i>
        You have <?= $billing['count'] ?> unpaid invoice<?= $billing['count'] > 1 ? 's' : '' ?>
        <a href="<?= base_url($uri . '/Billing') ?>">View Billing →</a>
    </div>
    <?php endif; ?>

    <!-- Tab buttons -->
    <div class="orders-tabs">
        <button class="order-tab-btn tab-active-incoming" id="tab-incoming"
            onclick="loadOrders('ACTIVE', 'incoming')">
            <i class="fa fa-fire"></i> Incoming
            <span class="badge-new" id="badge-incoming" style="display:none;"></span>
        </button>
        <button class="order-tab-btn" id="tab-completed"
            onclick="loadOrders('COMPLETED', 'completed')">
            <i class="fa fa-check-circle"></i> Completed
        </button>
        <button class="order-tab-btn" id="tab-cancelled"
            onclick="loadOrders('CANCELLED', 'cancelled')">
            <i class="fa fa-times-circle"></i> Cancelled
        </button>
    </div>

    <!-- DataTable wrapper -->
    <div class="orders-table-wrap">
        <table id="tblCartListing" class="table table-sm mb-0" style="width:100%">
            <thead><tr><th>Order</th></tr></thead>
            <tbody></tbody>
        </table>
    </div>

</div>

<script>
    var currentTab = 'incoming';

    function loadOrders(status, tab) {
        currentTab = tab;
        // Update tab buttons
        ['incoming','completed','cancelled'].forEach(function(t) {
            var btn = document.getElementById('tab-' + t);
            btn.className = 'order-tab-btn';
        });
        var activeClass = 'tab-active-' + tab;
        document.getElementById('tab-' + tab).classList.add(activeClass);

        window.status_filter_CartListing = status;
        getTable('CartListing', 0, 10);
    }

    // Poll incoming order count for badge
    function refreshIncomingBadge() {
        $.post("<?= base_url($uri . '/Orders/getNewOrderCount') ?>", function(res) {
            try {
                var d = JSON.parse(res);
                var badge = $('#badge-incoming');
                if (d.count > 0) { badge.text(d.count).show(); }
                else { badge.hide(); }
            } catch(e) {}
        });
    }

    function toggleOrderItems(toggleEl) {
        var list = $(toggleEl).next('.oitem-list');
        var isOpen = $(toggleEl).hasClass('open');
        if (isOpen) {
            list.slideUp(180);
            $(toggleEl).removeClass('open');
        } else {
            list.slideDown(220);
            $(toggleEl).addClass('open');
        }
    }

    $(function() {
        window.status_filter_CartListing = 'ACTIVE';
        getTable('CartListing', 0, 10);
        refreshIncomingBadge();
        setInterval(refreshIncomingBadge, 30000);
    });
</script>
