<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $uri = $this->session->agrishop_login_uri; ?>

<div class="container-fluid py-3">

    <!-- ── Header row ── -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="m-0 font-weight-bold"><i class="fa fa-boxes text-warning mr-2"></i> My Supplies</h5>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm mr-2"
                    onclick="$('#modalStoreInfo').modal('show')">
                <i class="fa fa-store"></i> Add Store
            </button>
            <button class="btn btn-warning btn-sm"
                    onclick="$('#modalSupplyInfo').modal('show')">
                <i class="fa fa-plus"></i> Add Supply
            </button>
        </div>
    </div>

    <!-- ── Billing alert ── -->
    <?php if ($billing['count'] > 0) : ?>
        <div class="alert alert-warning d-flex align-items-center mb-3">
            <i class="fa fa-exclamation-triangle mr-2"></i>
            You have <strong class="mx-1"><?= $billing['count'] ?></strong> unpaid invoice(s).
            <a href="<?= base_url($uri . '/Billing') ?>" class="btn btn-sm btn-warning ml-auto">View Billing</a>
        </div>
    <?php endif; ?>

    <!-- ── Stores quick list ── -->
    <?php if (!empty($stores)) : ?>
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light font-weight-bold py-2">
                <i class="fa fa-map-marker-alt text-warning mr-1"></i> My Stores / Locations
            </div>
            <div class="card-body p-2">
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($stores as $store) : ?>
                        <span class="badge badge-warning p-2" style="font-size:13px;">
                            <i class="fa fa-store mr-1"></i> <?= $store->store_name ?>
                            <?php if ($store->lat) : ?>
                                <small class="text-dark ml-1">📍 <?= $store->lat ?>, <?= $store->lon ?></small>
                            <?php endif; ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ── Supply datatable ── -->
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark font-weight-bold py-2">
            <i class="fa fa-list mr-1"></i> Supply List
        </div>
        <div class="card-body p-2">
            <table id="tblSupplyList" class="table table-sm table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th width="60">Image</th>
                        <th>Name / Brand</th>
                        <th>Category</th>
                        <th>Store</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th width="80">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>

<script>
    $(function () {
        getTable('SupplyList', 0, 10);
        saveForm("SupplyInfo",  ['SupplyList'], null, 0, 10);
        saveForm("StoreInfo",   [null], null);
    });
</script>
