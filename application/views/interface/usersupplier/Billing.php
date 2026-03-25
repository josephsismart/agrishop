<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$uri  = $this->session->agrishop_login_uri;
$sub  = $subscription['subscription'] ?? null;
$unpd = $subscription['unpaid'] ?? [];
?>

<div class="container-fluid py-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="m-0 font-weight-bold"><i class="fa fa-file-invoice text-warning mr-2"></i> Billing</h5>
    </div>

    <!-- ── Subscription info ── -->
    <?php if ($sub) : ?>
        <div class="card border-warning shadow-sm mb-3">
            <div class="card-header bg-warning text-dark font-weight-bold py-2">
                <i class="fa fa-star mr-1"></i> Subscription Status
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 col-md-3 text-center mb-2">
                        <div class="text-muted" style="font-size:12px;">Type</div>
                        <span class="badge badge-warning" style="font-size:14px;"><?= $sub->subscription_type ?></span>
                    </div>
                    <div class="col-6 col-md-3 text-center mb-2">
                        <div class="text-muted" style="font-size:12px;">Status</div>
                        <span class="badge <?= $sub->is_active ? 'badge-success' : 'badge-danger' ?>" style="font-size:14px;">
                            <?= $sub->is_active ? 'ACTIVE' : 'INACTIVE' ?>
                        </span>
                    </div>
                    <div class="col-6 col-md-3 text-center mb-2">
                        <div class="text-muted" style="font-size:12px;">Valid Until</div>
                        <strong><?= date('M d, Y', strtotime($sub->subscription_to)) ?></strong>
                    </div>
                    <div class="col-6 col-md-3 text-center mb-2">
                        <div class="text-muted" style="font-size:12px;">Due Date</div>
                        <strong><?= date('M d, Y', strtotime($sub->billing_due_date)) ?></strong>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ── Unpaid invoices ── -->
    <?php if (!empty($unpd)) : ?>
        <div class="card border-danger shadow-sm mb-3">
            <div class="card-header bg-danger text-white font-weight-bold py-2">
                <i class="fa fa-exclamation-triangle mr-1"></i>
                Unpaid Invoice(s) — <?= count($unpd) ?> pending
            </div>
            <div class="card-body p-2">
                <?php foreach ($unpd as $inv) : ?>
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2 px-2">
                        <div>
                            <div class="font-weight-bold"><?= $inv->payment_for ?></div>
                            <div class="text-muted" style="font-size:12px;">
                                Due: <?= date('M d, Y', strtotime($inv->billing_due_date)) ?>
                                &nbsp;|&nbsp; Ref: <code><?= $inv->invoice_no ?></code>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge badge-warning" style="font-size:14px;">
                                ₱ <?= number_format($inv->total_payment, 2) ?>
                            </span>
                            <button class="btn btn-sm btn-danger ml-2"
                                    onclick="openPayBilling(<?= $inv->id ?>)">
                                <i class="fa fa-upload mr-1"></i> Pay
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else : ?>
        <div class="alert alert-success mb-3">
            <i class="fa fa-check-circle mr-1"></i> All invoices are paid. You're all good!
        </div>
    <?php endif; ?>

    <!-- ── Payment history ── -->
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark font-weight-bold py-2">
            <i class="fa fa-history mr-1"></i> Payment History
        </div>
        <div class="card-body p-2">
            <table id="tblPaymentHistory" class="table table-sm table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th>Date Paid</th>
                        <th>Reference</th>
                        <th>For</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>

<!-- ── Pay billing modal ── -->
<div class="modal fade" id="modalPayBilling" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fa fa-upload mr-2"></i> Submit Payment</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <?= form_open(base_url($uri . '/Billing/savePayBilling'), 'id="form_save_dataPayBilling" enctype="multipart/form-data"') ?>
            <input type="hidden" name="id" id="payBillingId">
            <div class="modal-body">
                <p class="text-muted" style="font-size:13px;">Upload your GCash screenshot as proof of payment.</p>
                <div class="mb-2">
                    <label class="btn btn-outline-warning w-100">
                        <i class="fa fa-image mr-1"></i> Choose GCash Screenshot
                        <input type="file" name="gcash_qr" hidden accept="image/*"
                               onchange="imageView('gcash_qr','previewPayBilling')">
                    </label>
                </div>
                <div class="text-center">
                    <img name="previewPayBilling" src="<?= $system_svg_1x1 ?>"
                         style="max-width:100%;max-height:200px;border-radius:8px;border:1px solid #dee2e6;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning btn-sm submitBtnPrimary">Submit</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script>
    function openPayBilling(id) {
        $('#payBillingId').val(id);
        $('#modalPayBilling').modal('show');
    }

    $(function () {
        getTable('PaymentHistory', 0, 10);
        saveForm("PayBilling", [null], null);
    });
</script>
