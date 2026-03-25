<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $uri = $this->session->agrishop_login_uri; ?>

<div class="container-fluid py-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="m-0 font-weight-bold"><i class="fa fa-shopping-bag text-warning mr-2"></i> Orders</h5>
    </div>

    <!-- ── Billing alert ── -->
    <?php if ($billing['count'] > 0) : ?>
        <div class="alert alert-warning d-flex align-items-center mb-3">
            <i class="fa fa-exclamation-triangle mr-2"></i>
            You have <strong class="mx-1"><?= $billing['count'] ?></strong> unpaid invoice(s).
            <a href="<?= base_url($uri . '/Billing') ?>" class="btn btn-sm btn-warning ml-auto">View Billing</a>
        </div>
    <?php endif; ?>

    <!-- ── Tabs ── -->
    <ul class="nav nav-tabs mb-3" id="orderTabs">
        <li class="nav-item">
            <a class="nav-link active" href="#" onclick="loadOrders('ACTIVE', this)">
                <i class="fa fa-fire text-warning"></i> Active
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" onclick="loadOrders('COMPLETED', this)">
                <i class="fa fa-check-circle text-success"></i> Completed
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" onclick="loadOrders('CANCELLED', this)">
                <i class="fa fa-times-circle text-danger"></i> Cancelled
            </a>
        </li>
    </ul>

    <!-- ── Datatable ── -->
    <div class="card shadow-sm">
        <div class="card-body p-2">
            <table id="tblOrderList" class="table table-sm" style="width:100%">
                <thead><tr><th>Orders</th></tr></thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>

<script>
    window.status_filter_OrderList = 'ACTIVE';

    function loadOrders(status, el) {
        window.status_filter_OrderList = status;
        $('#orderTabs .nav-link').removeClass('active');
        $(el).addClass('active');
        getTable('OrderList', 0, 10);
    }

    $(function () {
        getTable('OrderList', 0, 10);
    });
</script>
