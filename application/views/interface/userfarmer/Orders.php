<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!$this->session->agrishop_login_level) {
    redirect(base_url('login'));
}
$uri = $this->session->agrishop_login_uri;
?>

<!-- Page Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mt-2 mb-n1 align-items-center">
            <div class="col-sm-6">
                <h5 class="m-0">
                    <i class="fas fa-shopping-basket text-success mr-1"></i> Client Orders
                </h5>
            </div>
            <div class="col-sm-6 text-right">
                <?php if ($billing['count'] > 0) : ?>
                    <a href="<?= base_url($uri . '/Billing') ?>" class="btn btn-sm btn-warning">
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

        <!-- Order status tabs -->
        <ul class="nav nav-tabs mb-0" id="orderTabs">
            <li class="nav-item">
                <a class="nav-link active" href="#" onclick="loadOrders('ACTIVE', this); return false;">
                    <i class="fa fa-fire text-warning mr-1"></i>
                    Incoming <span class="badge badge-info countOrders"></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="loadOrders('COMPLETED', this); return false;">
                    <i class="fa fa-check-circle text-success mr-1"></i> Completed
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="loadOrders('CANCELLED', this); return false;">
                    <i class="fa fa-times-circle text-danger mr-1"></i> Cancelled
                </a>
            </li>
        </ul>

        <!-- Orders card -->
        <div class="card shadow-sm" style="border-radius:0 0 8px 8px; border-top:none;">
            <div class="card-body p-2">
                <table id="tblCartListing" class="table table-sm table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Order Details</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <!-- Order detail panel -->
        <div id="orderDetailPanel" class="card shadow-sm mt-3" style="display:none;">
            <div class="card-header bg-success text-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="m-0"><i class="fa fa-receipt mr-1"></i> Order Items</h6>
                <button class="btn btn-sm btn-outline-light" onclick="$('#orderDetailPanel').hide();">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="card-body p-1">
                <table id="tblCartDetails" class="table table-sm" style="width:100%">
                    <thead>
                        <tr>
                            <th>Items</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div>
</section>

<script>
    // ── State ──────────────────────────────────────────────────
    var orderStatus = 'ACTIVE';

    // ── Tab switching ──────────────────────────────────────────
    function loadOrders(status, el) {
        orderStatus = status;
        $('#orderTabs .nav-link').removeClass('active');
        $(el).addClass('active');
        window.status_filter_CartListing = status;
        // Destroy and reload with new status
        getTable('CartListing', 0, 10);
        $('#orderDetailPanel').hide();
    }

    // ── View transaction detail (eye button) ───────────────────
    function viewTransactionDetails(trans_id) {
        // Set the global transaction_id_ used by getTable data function
        transaction_id_ = trans_id;
        $('#orderDetailPanel').show();
        $('html,body').animate({
            scrollTop: $('#orderDetailPanel').offset().top - 60
        }, 300);
        // Reload CartDetails with the new transaction id
        getTable('CartDetails', 0, 100);
    }

    // ── GCash attachment viewer ────────────────────────────────
    function viewGcashAttachment(url) {
        Swal.fire({
            title: 'GCash Payment',
            imageUrl: url,
            imageWidth: 320,
            imageAlt: 'GCash QR / Receipt',
            showCloseButton: true,
            showConfirmButton: false
        });
    }

    // ── Accept / progress order ────────────────────────────────
    function acceptAndPrepare(trans_id, status) {
        $.post("<?= base_url($uri . '/Orders/acceptAndPrepare') ?>", {
                trans_id: trans_id,
                status: status
            },
            function(res) {
                var d = JSON.parse(res);
                d.success ? successAlert(d.message) : failAlert(d.message);
                getTable('CartListing', 0, 10);
                // Refresh detail panel
                getTable('CartDetails', 0, 100);
            }
        );
    }

    // ── Update delivery / payment status modal ─────────────────
    function updateModalStatus(trans_id, current_status, type) {
        var options = type === 'DELIVERY' ?
            ['TO_PICKUP', 'TO_DELIVER', 'ON_THE_WAY', 'DELIVERED'] :
            ['UNPAID', 'VERIFYING', 'PAID', 'FAILED'];

        var btns = options.map(function(s) {
            var active = s === current_status ? ' btn-secondary' : ' btn-outline-secondary';
            return '<button class="btn btn-sm' + active + ' m-1" onclick="doUpdateStatus(' + trans_id + ',\'' + s + '\',\'' + type.toLowerCase() + '\')">' + s + '</button>';
        }).join('');

        Swal.fire({
            title: 'Update ' + type,
            html: '<p class="mb-2">Current: <strong>' + current_status + '</strong></p>' + btns,
            showConfirmButton: false,
            showCloseButton: true,
        });
    }

    function doUpdateStatus(trans_id, status, type) {
        $.post("<?= base_url($uri . '/Orders/updateStatus') ?>", {
                trans_id: trans_id,
                status: status,
                type: type
            },
            function(res) {
                var d = JSON.parse(res);
                d.success ? successAlert(d.message) : failAlert(d.message);
                Swal.close();
                getTable('CartListing', 0, 10);
                // Refresh detail if open
                if ($('#orderDetailPanel').is(':visible')) {
                    getTable('CartDetails', 0, 100);
                }
            }
        );
    }

    // ── Init on page load ──────────────────────────────────────
    $(function() {
        window.status_filter_CartListing = 'ACTIVE';
        getTable('CartListing', 0, 10);
    });
</script>
