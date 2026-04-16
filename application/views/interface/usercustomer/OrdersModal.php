<?php
$person_id  = $this->session->agrishop_person_id;
$login_name = $this->session->agrishop_login_first_name . ' ' . $this->session->agrishop_login_last_name;
?>

<style>
/* ── Customer Orders Modal ── */
#modalCustomerOrders .modal-content {
    border: none;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 24px 80px rgba(0,0,0,.22);
}
#modalCustomerOrders .modal-header {
    background: linear-gradient(135deg, #14532d 0%, #166534 50%, #15803d 100%);
    padding: 14px 20px;
    border-bottom: none;
}
#modalCustomerOrders .modal-header .modal-title {
    color: #fff; font-size: 17px; font-weight: 700;
    display: flex; align-items: center; gap: 10px;
}
#modalCustomerOrders .modal-header .close {
    color: rgba(255,255,255,.8); opacity: 1; font-size: 22px;
    text-shadow: none;
}
#modalCustomerOrders .modal-header .close:hover { color: #fff; }
#modalCustomerOrders .modal-body {
    background: #f8fafc; padding: 20px;
    max-height: calc(90vh - 60px);
    overflow-y: auto;
}

/* Count cards */
.co-counts {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 10px; margin-bottom: 20px;
}
@media (max-width: 640px) { .co-counts { grid-template-columns: repeat(3, 1fr); } }
.co-count-card {
    background: #fff; border-radius: 14px;
    padding: 14px 12px; text-align: center;
    box-shadow: 0 1px 6px rgba(0,0,0,.06);
    cursor: pointer; transition: all .2s; border: 2px solid transparent;
}
.co-count-card:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,.1); }
.co-count-card.active { border-color: #16a34a; }
.co-count-card .co-num { font-size: 24px; font-weight: 800; line-height: 1; }
.co-count-card .co-lbl { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; margin-top: 4px; color: #6b7280; }

/* Filter tabs */
.co-tabs {
    display: flex; gap: 4px; margin-bottom: 14px;
    background: #fff; border-radius: 12px;
    padding: 6px; box-shadow: 0 1px 6px rgba(0,0,0,.06);
    overflow-x: auto;
}
.co-tab {
    flex-shrink: 0; padding: 7px 14px; border-radius: 8px;
    font-size: 13px; font-weight: 600; border: none; background: transparent;
    color: #6b7280; cursor: pointer; transition: all .2s; white-space: nowrap;
}
.co-tab:hover { background: #f3f4f6; color: #374151; }
.co-tab.active { background: #16a34a; color: #fff; }

/* Orders table card */
.co-card {
    background: #fff; border-radius: 16px;
    box-shadow: 0 1px 8px rgba(0,0,0,.06);
    overflow: hidden;
}
.co-card-head {
    padding: 14px 18px; border-bottom: 1px solid #f3f4f6;
    display: flex; align-items: center; justify-content: space-between; gap: 12px;
    flex-wrap: wrap;
}
.co-card-head h6 { margin: 0; font-weight: 700; font-size: 14px; color: #111827; }
.co-search {
    display: flex; align-items: center; gap: 8px;
    background: #f9fafb; border: 1.5px solid #e5e7eb; border-radius: 10px;
    padding: 6px 12px; max-width: 220px;
}
.co-search input {
    border: none; background: transparent; outline: none;
    font-size: 13px; width: 150px; color: #374151;
}
.co-search i { color: #9ca3af; font-size: 13px; }

/* Table scroll wrapper */
.co-table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; padding: 0 8px 14px; }

/* DataTable overrides */
#tblOrders_wrapper .dataTables_info,
#tblOrders_wrapper .dataTables_paginate { padding: 10px 18px; }
#tblOrders thead th {
    background: #f0fdf4; color: #374151; font-size: 11px;
    font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
    border-bottom: 1px solid #d1fae5; padding: 10px 12px;
    white-space: nowrap;
}
#tblOrders tbody td { padding: 12px; vertical-align: middle; font-size: 13px; white-space: nowrap; }
#tblOrders tbody tr { border-bottom: 1px solid #f9fafb; }
#tblOrders tbody tr:hover { background: #f0fdf4; }
#tblOrders_wrapper .dataTables_filter { display: none; }

/* ── Tracking modal ── */
#modalOrderTracking .modal-content {
    border: none; border-radius: 20px; overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,.25);
}
.trk-header {
    background: linear-gradient(135deg, #14532d, #16a34a);
    padding: 18px 20px; position: relative;
}
.trk-header .trk-title { color: #fff; font-size: 15px; font-weight: 700; margin: 0; }
.trk-header .trk-sub   { color: rgba(255,255,255,.7); font-size: 12px; margin: 2px 0 0; }
.trk-close {
    position: absolute; top: 12px; right: 14px;
    background: rgba(255,255,255,.2); border: none; color: #fff;
    width: 28px; height: 28px; border-radius: 50%; font-size: 15px;
    display: flex; align-items: center; justify-content: center; cursor: pointer;
}
.trk-close:hover { background: rgba(255,255,255,.35); }
.trk-body { padding: 18px; }

.trk-farm-row {
    display: flex; align-items: center; gap: 12px;
    background: #f0fdf4; border-radius: 12px; padding: 12px 14px;
    margin-bottom: 14px;
}
.trk-farm-img {
    width: 42px; height: 42px; border-radius: 10px; object-fit: cover;
    flex-shrink: 0; background: #d1fae5;
    display: flex; align-items: center; justify-content: center;
}
.trk-farm-name  { font-weight: 700; font-size: 14px; color: #14532d; }
.trk-order-meta { font-size: 11px; color: #6b7280; margin-top: 2px; }

.trk-status-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 14px; border-radius: 20px;
    font-size: 13px; font-weight: 700; margin-bottom: 14px;
}

/* Pipeline */
.trk-pipeline {
    display: flex; align-items: flex-start; gap: 0;
    margin-bottom: 18px; overflow-x: auto; padding-bottom: 4px;
}
.trk-stage {
    flex: 1; min-width: 68px;
    display: flex; flex-direction: column; align-items: center; text-align: center;
    position: relative;
}
.trk-stage::before {
    content: ''; position: absolute;
    top: 19px; left: calc(-50% + 20px); right: calc(50% + 20px);
    height: 3px; background: #e5e7eb; z-index: 0;
}
.trk-stage:first-child::before { display: none; }
.trk-stage.done::before, .trk-stage.curr::before { background: #16a34a; }
.trk-stage-dot {
    width: 40px; height: 40px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; z-index: 1; position: relative;
    background: #e5e7eb; color: #9ca3af;
    transition: all .2s; border: 3px solid #fff;
    box-shadow: 0 0 0 2px #e5e7eb;
}
.trk-stage.done .trk-stage-dot  { background: #16a34a; color: #fff; box-shadow: 0 0 0 2px #16a34a; }
.trk-stage.curr .trk-stage-dot  { background: #fbbf24; color: #fff; box-shadow: 0 0 0 2px #fbbf24; animation: trkPulse .9s infinite; }
.trk-stage.cancel .trk-stage-dot{ background: #ef4444; color: #fff; box-shadow: 0 0 0 2px #ef4444; }
@keyframes trkPulse {
    0%   { box-shadow: 0 0 0 2px #fbbf24, 0 0 0 4px rgba(251,191,36,.3); }
    100% { box-shadow: 0 0 0 2px #fbbf24, 0 0 0 10px rgba(251,191,36,0); }
}
.trk-stage-lbl { font-size: 10px; font-weight: 700; color: #6b7280; margin-top: 5px; line-height: 1.3; }
.trk-stage.done .trk-stage-lbl  { color: #16a34a; }
.trk-stage.curr .trk-stage-lbl  { color: #d97706; }
.trk-stage.cancel .trk-stage-lbl{ color: #ef4444; }
.trk-stage-date { font-size: 9px; color: #9ca3af; margin-top: 2px; }

/* Items */
.trk-section-title {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .07em; color: #9ca3af; margin: 14px 0 8px;
    display: flex; align-items: center; gap: 6px;
}
.trk-section-title::after { content: ''; flex: 1; height: 1px; background: #f3f4f6; }
.trk-item {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 0; border-bottom: 1px solid #f9fafb;
}
.trk-item:last-child { border-bottom: none; }
.trk-item-img {
    width: 42px; height: 42px; border-radius: 8px;
    object-fit: cover; flex-shrink: 0; background: #f3f4f6;
    display: flex; align-items: center; justify-content: center;
}
.trk-item-name  { font-weight: 600; font-size: 13px; }
.trk-item-meta  { font-size: 11px; color: #9ca3af; margin-top: 2px; }
.trk-item-price { margin-left: auto; font-weight: 700; font-size: 13px; color: #16a34a; white-space: nowrap; }

.trk-total-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 12px 0; border-top: 2px solid #f3f4f6; margin-top: 4px;
}
.trk-total-lbl { font-size: 13px; font-weight: 600; color: #374151; }
.trk-total-amt { font-size: 18px; font-weight: 800; color: #16a34a; }

.trk-info-row { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 14px; }
.trk-info-chip {
    background: #f3f4f6; border-radius: 8px; padding: 6px 10px;
    font-size: 11px; color: #374151; display: flex; align-items: center; gap: 5px;
}
.trk-info-chip i { color: #16a34a; }

.trk-cancel-btn {
    width: 100%; margin-top: 14px; padding: 11px;
    background: transparent; border: 2px solid #ef4444; color: #ef4444;
    border-radius: 12px; font-size: 14px; font-weight: 700;
    cursor: pointer; transition: all .2s; display: none;
}
.trk-cancel-btn:hover { background: #fef2f2; }
.trk-cancel-btn.visible { display: block; }

.trk-history-item { display: flex; gap: 10px; padding: 7px 0; align-items: flex-start; }
.trk-history-dot  { width: 10px; height: 10px; border-radius: 50%; background: #e5e7eb; flex-shrink: 0; margin-top: 4px; }
.trk-history-item.latest .trk-history-dot { background: #16a34a; }
.trk-history-lbl  { font-size: 12px; font-weight: 600; }
.trk-history-meta { font-size: 11px; color: #9ca3af; }

.co-empty { text-align: center; padding: 50px 20px; color: #9ca3af; }
.co-empty i { font-size: 40px; margin-bottom: 10px; display: block; }

/* ── Rating widget ── */
.co-rating-box {
    background: linear-gradient(135deg, #fffbeb, #fef3c7);
    border: 2px solid #fde68a; border-radius: 16px;
    padding: 18px 20px; margin-top: 16px; text-align: center;
}
.co-rating-box .co-rating-title {
    font-size: 14px; font-weight: 700; color: #92400e; margin-bottom: 12px;
}
.co-rating-box .co-stars { display: flex; justify-content: center; gap: 8px; margin-bottom: 12px; }
.co-rating-box .co-star {
    font-size: 32px; color: #d1d5db; cursor: pointer; transition: color .15s, transform .1s;
}
.co-rating-box .co-star:hover,
.co-rating-box .co-star.sel { color: #f59e0b; transform: scale(1.15); }
.co-rating-box textarea {
    width: 100%; border: 1.5px solid #fde68a; border-radius: 10px; padding: 10px 12px;
    font-size: 13px; resize: none; background: #fff; outline: none; color: #374151;
    margin-bottom: 12px;
}
.co-rating-box textarea:focus { border-color: #f59e0b; }
.co-rating-submit {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #fff; border: none; border-radius: 12px;
    padding: 10px 28px; font-size: 14px; font-weight: 700; cursor: pointer;
    transition: filter .15s; width: 100%;
}
.co-rating-submit:hover { filter: brightness(1.08); }
.co-rating-submit:disabled { opacity: .6; cursor: default; }

/* Already-rated display */
.co-rated-box {
    background: #f0fdf4; border: 2px solid #bbf7d0; border-radius: 16px;
    padding: 16px 20px; margin-top: 16px; text-align: center;
}
.co-rated-box .co-rated-stars { font-size: 22px; margin-bottom: 6px; }
.co-rated-box .co-rated-comment { font-size: 13px; color: #374151; font-style: italic; }
</style>

<!-- ════════════════ Customer Orders Modal ════════════════ -->
<div class="modal fade" id="modalCustomerOrders" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document"
         style="max-width: min(96vw, 1080px);">
        <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-box"></i> My Orders
                    <span style="font-size:12px;font-weight:500;background:rgba(255,255,255,.2);padding:3px 10px;border-radius:12px;margin-left:8px;">
                        <?= htmlspecialchars($login_name) ?>
                    </span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body">

                <!-- Count cards -->
                <div class="co-counts">
                    <div class="co-count-card active" onclick="coSetFilter('ALL')" id="coCard-ALL">
                        <div class="co-num" style="color:#374151;" id="coCountAll">–</div>
                        <div class="co-lbl">All Orders</div>
                    </div>
                    <div class="co-count-card" onclick="coSetFilter('TO_RECEIVE')" id="coCard-TO_RECEIVE">
                        <div class="co-num" style="color:#1e40af;" id="coCountReceive">–</div>
                        <div class="co-lbl">To Receive</div>
                    </div>
                    <div class="co-count-card" onclick="coSetFilter('COMPLETED')" id="coCard-COMPLETED">
                        <div class="co-num" style="color:#14532d;" id="coCountDone">–</div>
                        <div class="co-lbl">Completed</div>
                    </div>
                    <div class="co-count-card" onclick="coSetFilter('CANCELLED')" id="coCard-CANCELLED">
                        <div class="co-num" style="color:#991b1b;" id="coCountCancel">–</div>
                        <div class="co-lbl">Cancelled</div>
                    </div>
                    <div class="co-count-card" onclick="coSetFilter('TO_RATE')" id="coCard-TO_RATE" style="border-color:#fde68a;">
                        <div class="co-num" style="color:#d97706;position:relative;" id="coCountRate">–
                            <span id="coRateBadge" style="display:none;position:absolute;top:-6px;right:-10px;background:#ef4444;color:#fff;font-size:9px;font-weight:800;border-radius:10px;padding:1px 5px;">!</span>
                        </div>
                        <div class="co-lbl"><i class="fa fa-star" style="color:#f59e0b;"></i> To Rate</div>
                    </div>
                </div>

                <!-- Filter tabs -->
                <div class="co-tabs">
                    <button class="co-tab active" data-cofilter="ALL"        onclick="coSetFilter('ALL')">All</button>
                    <button class="co-tab"        data-cofilter="PENDING"    onclick="coSetFilter('PENDING')">Pending</button>
                    <button class="co-tab"        data-cofilter="TO_RECEIVE" onclick="coSetFilter('TO_RECEIVE')">To Receive</button>
                    <button class="co-tab"        data-cofilter="COMPLETED"  onclick="coSetFilter('COMPLETED')">Completed</button>
                    <button class="co-tab"        data-cofilter="CANCELLED"  onclick="coSetFilter('CANCELLED')">Cancelled</button>
                    <button class="co-tab"        data-cofilter="TO_RATE"    onclick="coSetFilter('TO_RATE')" style="color:#d97706;">
                        <i class="fa fa-star mr-1"></i>To Rate
                        <span id="coTabRateBadge" class="badge badge-warning ml-1" style="display:none;background:#ef4444;color:#fff;"></span>
                    </button>
                </div>

                <!-- Orders table card -->
                <div class="co-card">
                    <div class="co-card-head">
                        <h6><i class="fa fa-list mr-2 text-success"></i>Order History</h6>
                        <div class="co-search">
                            <i class="fa fa-search"></i>
                            <input type="text" id="coOrdersSearch" placeholder="Search orders..."
                                   oninput="if(window.coTblOrders) coTblOrders.search(this.value).draw()">
                        </div>
                    </div>

                    <div class="co-table-scroll">
                        <table id="coTblOrders" class="table" style="width:100%;min-width:600px;">
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

            </div><!-- /.modal-body -->
        </div>
    </div>
</div>

<!-- ════════════════ Order Tracking Modal ════════════════ -->
<div class="modal fade" id="modalOrderTracking" tabindex="-1" role="dialog" data-backdrop="false" style="z-index:1060;">
    <div class="modal-dialog modal-dialog-centered" style="max-width:540px;">
        <div class="modal-content">

            <div class="trk-header">
                <p class="trk-title"><i class="fa fa-route mr-2"></i>Order Tracking</p>
                <p class="trk-sub" id="trkOrderNum">Loading...</p>
                <button class="trk-close" data-dismiss="modal">×</button>
            </div>

            <div class="trk-body" id="trkBody" style="max-height:72vh;overflow-y:auto;">
                <div style="text-align:center;padding:40px;">
                    <i class="fa fa-circle-notch fa-spin fa-2x text-success"></i>
                    <p style="margin-top:12px;color:#9ca3af;">Loading order details...</p>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
(function() {
    /* ── state ── */
    var coFilter = 'ALL';
    window.coTblOrders = null;
    var _coInit = false;

    /* ── update count cards from DataTable JSON response ── */
    function coUpdateCounts(json) {
        if (!json || !json.counts) return;
        var c = json.counts;
        var all = (c.pending||0) + (c.to_receive||0) + (c.completed||0) + (c.cancelled||0);
        $('#coCountAll').text(all);
        $('#coCountReceive').text(c.to_receive || 0);
        $('#coCountDone').text(c.completed   || 0);
        $('#coCountCancel').text(c.cancelled  || 0);
        var toRate = c.to_rate || 0;
        $('#coCountRate').text(toRate);
        // show/hide red "!" badge and tab badge
        if (toRate > 0) {
            $('#coRateBadge').show();
            $('#coTabRateBadge').text(toRate).show();
            // also update the navbar Orders badge with to_rate count
            $('.rate-order').text(toRate);
        } else {
            $('#coRateBadge').hide();
            $('#coTabRateBadge').hide();
            $('.rate-order').text('');
        }
    }

    /* ── init DataTable on first modal open ── */
    $('#modalCustomerOrders').on('shown.bs.modal', function() {
        if (!_coInit) {
            window.coTblOrders = $('#coTblOrders').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?= base_url('usercustomer/Orders/getOrders') ?>',
                    type: 'POST',
                    data: function(d) { d.filter = coFilter; },
                    dataSrc: function(json) {
                        coUpdateCounts(json);
                        return json.data;
                    }
                },
                columns: [
                    { data: 'farm',   orderable: true },
                    { data: 'date',   orderable: true },
                    { data: 'status', orderable: false },
                    { data: 'amount', orderable: false },
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
            _coInit = true;
        } else {
            window.coTblOrders.ajax.reload();
        }
    });

    /* ── filter tabs ── */
    window.coSetFilter = function(f) {
        coFilter = f;
        document.querySelectorAll('.co-tab').forEach(function(b) {
            b.classList.toggle('active', b.dataset.cofilter === f);
        });
        document.querySelectorAll('.co-count-card').forEach(function(c) {
            c.classList.toggle('active', c.id === 'coCard-' + f);
        });
        if (window.coTblOrders) window.coTblOrders.ajax.reload();
    };

    /* ── tracking ── */
    window.viewOrderTracking = function(txId) {
        $('#trkOrderNum').text('Order #' + txId);
        $('#trkBody').html('<div style="text-align:center;padding:40px;"><i class="fa fa-circle-notch fa-spin fa-2x text-success"></i><p style="margin-top:12px;color:#9ca3af;">Loading...</p></div>');
        $('#modalOrderTracking').modal('show');

        $.post('<?= base_url('usercustomer/Orders/getTracking') ?>', { transaction_id: txId }, function(res) {
            if (!res.success) {
                $('#trkBody').html('<p class="text-center text-muted p-4">Could not load order.</p>');
                return;
            }
            coRenderTracking(res);
        }, 'json');
    };

    function coRenderTracking(d) {
        var si = d.status_info;

        var pipeHtml = '<div class="trk-pipeline">';
        d.pipeline.forEach(function(s) {
            var cls = s.done ? (s.is_current ? 'curr' : 'done') : '';
            if (s.stage === 'CANCELLED' && s.done) cls = 'cancel';
            var dotDate = s.date ? '<div class="trk-stage-date">' + s.date.split(' ').slice(0,2).join(' ') + '</div>' : '';
            pipeHtml += '<div class="trk-stage ' + cls + '">' +
                '<div class="trk-stage-dot"><i class="fa ' + s.icon + '"></i></div>' +
                '<div class="trk-stage-lbl">' + s.label + '</div>' + dotDate +
                '</div>';
        });
        pipeHtml += '</div>';

        var itemsHtml = '';
        d.items.forEach(function(i) {
            var imgTag = i.img
                ? '<img src="' + i.img + '" class="trk-item-img" alt="">'
                : '<div class="trk-item-img"><i class="fa fa-leaf text-success"></i></div>';
            itemsHtml += '<div class="trk-item">' + imgTag +
                '<div style="flex:1;"><div class="trk-item-name">' + i.name + '</div>' +
                '<div class="trk-item-meta">' + i.qty + ' ' + i.uom + ' × ₱' + i.price + '</div></div>' +
                '<div class="trk-item-price">₱' + i.subtotal + '</div></div>';
        });

        var histHtml = '';
        var histReversed = d.history.slice().reverse();
        histReversed.forEach(function(h) {
            histHtml += '<div class="trk-history-item ' + (h.is_latest ? 'latest' : '') + '">' +
                '<div class="trk-history-dot"></div>' +
                '<div class="trk-history-info">' +
                '<div class="trk-history-lbl">' + h.label + '</div>' +
                '<div class="trk-history-meta">' + h.date + (h.updated_by ? ' · ' + h.updated_by : '') + '</div>' +
                '</div></div>';
        });

        var farmImg = d.farm_img
            ? '<img src="' + d.farm_img + '" class="trk-farm-img" alt="" style="width:42px;height:42px;border-radius:10px;object-fit:cover;">'
            : '<div class="trk-farm-img" style="width:42px;height:42px;border-radius:10px;background:#d1fae5;display:flex;align-items:center;justify-content:center;"><i class="fa fa-store text-success"></i></div>';

        var canCancel   = d.current_status === 'PENDING';
        var delivMethod = d.delivery_method ? d.delivery_method.toUpperCase() : '–';
        var payMethod   = d.payment_method  ? d.payment_method.toUpperCase()  : '–';

        // Build rating section
        var ratingHtml = '';
        if (d.can_rate) {
            var txId = d.transaction_id;
            var starInputs = '';
            for (var si2 = 1; si2 <= 5; si2++) {
                starInputs += '<i class="fa fa-star co-star" data-val="' + si2 + '" onclick="coSelectStar(this,' + txId + ')"></i>';
            }
            ratingHtml = '<div class="co-rating-box" id="coRatingBox-' + txId + '">' +
                '<input type="hidden" id="coRatingVal-' + txId + '" value="0">' +
                '<div class="co-rating-title"><i class="fa fa-star mr-1" style="color:#f59e0b;"></i> Rate this Store</div>' +
                '<div class="co-stars">' + starInputs + '</div>' +
                '<textarea id="coRatingComment-' + txId + '" rows="2" placeholder="Share your experience (optional)..."></textarea>' +
                '<button class="co-rating-submit" id="coRatingSubmitBtn-' + txId + '" onclick="coSubmitRating(' + txId + ')">Submit Rating</button>' +
                '</div>';
        } else if (d.existing_rating) {
            var ratedStars = '';
            for (var ri = 1; ri <= 5; ri++) {
                ratedStars += '<i class="fa fa-star" style="color:' + (ri <= d.existing_rating ? '#f59e0b' : '#d1d5db') + ';font-size:20px;margin:0 2px;"></i>';
            }
            ratingHtml = '<div class="co-rated-box">' +
                '<div style="font-size:12px;font-weight:700;color:#16a34a;margin-bottom:6px;"><i class="fa fa-check-circle mr-1"></i>You rated this order</div>' +
                '<div class="co-rated-stars">' + ratedStars + '</div>' +
                (d.rate_comment ? '<div class="co-rated-comment">"' + d.rate_comment + '"</div>' : '') +
                '</div>';
        }

        $('#trkOrderNum').text('Order #' + d.transaction_id + ' · ' + d.date_ordered);

        var addrChip = d.delivery_address
            ? '<div class="trk-info-chip"><i class="fa fa-map-marker-alt"></i> ' + d.delivery_address + '</div>'
            : '';

        $('#trkBody').html(
            '<div class="trk-farm-row">' + farmImg +
                '<div><div class="trk-farm-name">' + d.farm_name + '</div>' +
                '<div class="trk-order-meta">Ordered ' + d.date_ordered + '</div></div></div>' +

            '<span class="trk-status-badge" style="background:' + si.bg + ';color:' + si.color + ';">' +
                si.icon + ' ' + si.label + '</span>' +

            '<div class="trk-info-row">' +
                '<div class="trk-info-chip"><i class="fa fa-credit-card"></i> ' + payMethod + '</div>' +
                '<div class="trk-info-chip"><i class="fa fa-truck"></i> ' + delivMethod + '</div>' +
                addrChip +
            '</div>' +

            '<div class="trk-section-title"><i class="fa fa-route"></i> Order Pipeline</div>' +
            pipeHtml +

            '<div class="trk-section-title"><i class="fa fa-box-open"></i> Items Ordered</div>' +
            itemsHtml +
            '<div class="trk-total-row">' +
                '<div><div class="trk-total-lbl">Total Payment</div>' +
                '<div style="font-size:11px;color:#9ca3af;margin-top:2px;">₱' + d.subtotal + ' + ₱' + d.fee + ' service fee (1%)</div></div>' +
                '<div class="trk-total-amt">₱' + d.total + '</div>' +
            '</div>' +

            '<div class="trk-section-title"><i class="fa fa-history"></i> Activity Log</div>' +
            '<div style="padding:0 0 8px;">' +
                (histHtml || '<p style="color:#9ca3af;font-size:13px;padding:8px 0;">No activity yet.</p>') +
            '</div>' +

            ratingHtml +
            '<button class="trk-cancel-btn ' + (canCancel ? 'visible' : '') + '" onclick="coCancelOrder(' + d.transaction_id + ')">' +
                '<i class="fa fa-times-circle mr-2"></i>Cancel Order</button>'
        );
    }

    /* ── open rate modal from table button ── */
    window.coOpenRating = function(txId) {
        viewOrderTracking(txId); // opens tracking modal which includes rating widget
    };

    /* ── submit rating ── */
    window.coSubmitRating = function(txId) {
        var val = parseInt($('#coRatingVal-' + txId).val() || 0);
        var comment = $('#coRatingComment-' + txId).val().trim();
        if (!val) { alert('Please select a star rating.'); return; }
        var btn = $('#coRatingSubmitBtn-' + txId);
        btn.prop('disabled', true).text('Saving...');
        $.post('<?= base_url('usercustomer/Orders/saveRating') ?>',
            { transaction_id: txId, rating_value: val, review_comment: comment },
            function(res) {
                if (res.success) {
                    // replace widget with rated display
                    var stars = '';
                    for (var i = 1; i <= 5; i++) {
                        stars += '<i class="fa fa-star" style="color:' + (i <= val ? '#f59e0b' : '#d1d5db') + ';font-size:20px;margin:0 2px;"></i>';
                    }
                    $('#coRatingBox-' + txId).replaceWith(
                        '<div class="co-rated-box">' +
                        '<div style="font-size:12px;font-weight:700;color:#16a34a;margin-bottom:6px;"><i class="fa fa-check-circle mr-1"></i>Thank you for rating!</div>' +
                        '<div class="co-rated-stars">' + stars + '</div>' +
                        (comment ? '<div class="co-rated-comment">"' + comment + '"</div>' : '') +
                        '</div>'
                    );
                    if (window.coTblOrders) window.coTblOrders.ajax.reload();
                } else {
                    btn.prop('disabled', false).text('Submit Rating');
                    alert(res.message || 'Failed to save rating.');
                }
            }, 'json');
    };

    window.coCancelOrder = function(txId) {
        Swal.fire({
            title: 'Cancel Order?',
            html: '<p style="font-size:14px;color:#374151;">Please enter a reason for cancellation:</p>' +
                  '<textarea id="coCancelReason" class="swal2-textarea" placeholder="e.g. Changed my mind..." style="font-size:13px;"></textarea>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Cancel Order',
            confirmButtonColor: '#ef4444',
            cancelButtonText: 'No, Keep It',
            preConfirm: function() {
                var reason = document.getElementById('coCancelReason').value.trim();
                return reason || 'Cancelled by customer';
            }
        }).then(function(result) {
            if (!result.isConfirmed) return;
            $.post('<?= base_url('usercustomer/Orders/cancelOrder') ?>',
                { transaction_id: txId, reason: result.value },
                function(res) {
                    if (res.success) {
                        Swal.fire({ icon: 'success', title: 'Order Cancelled', timer: 2000, showConfirmButton: false });
                        $('#modalOrderTracking').modal('hide');
                        if (window.coTblOrders) window.coTblOrders.ajax.reload();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Could not cancel.' });
                    }
                }, 'json');
        });
    };
    /* ── star highlight on click ── */
    window.coSelectStar = function(el, txId) {
        var val = parseInt(el.getAttribute('data-val'));
        $('#coRatingVal-' + txId).val(val);
        $(el).closest('.co-stars').find('.co-star').each(function() {
            var v = parseInt($(this).data('val'));
            $(this).toggleClass('sel', v <= val);
        });
    };

    /* ── star hover preview ── */
    $(document).on('mouseenter', '.co-star', function() {
        var val = parseInt($(this).data('val'));
        $(this).closest('.co-stars').find('.co-star').each(function() {
            $(this).css('color', parseInt($(this).data('val')) <= val ? '#f59e0b' : '#d1d5db');
        });
    }).on('mouseleave', '.co-stars', function() {
        $(this).find('.co-star').each(function() {
            var txId = $(this).closest('.co-rating-box').attr('id').replace('coRatingBox-', '');
            var sel = parseInt($('#coRatingVal-' + txId).val() || 0);
            $(this).css('color', parseInt($(this).data('val')) <= sel ? '#f59e0b' : '#d1d5db');
        });
    });

})();
</script>
