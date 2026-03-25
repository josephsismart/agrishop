<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$uri  = $this->session->agrishop_login_uri;
$sub  = $subscription['subscription']         ?? null;
$sinv = $subscription['subscription_invoice'] ?? null;
$svc  = $subscription['service_invoice']      ?? null;

// ── FIX: inline function instead of $this->_renderInvoiceCard() ──
function renderInvoiceCard($inv, $uri)
{
    if (!$inv) return;
    $status  = $inv['status']           ?? 'PENDING';
    $remarks = $inv['status_remarks']   ?? '';
    $id      = $inv['id'];
    $for     = $inv['payment_for']      ?? '';
    $due     = $inv['billing_due_date'] ?? '';
    $amount  = $inv['total_payment']    ?? 0;
    $ref     = $inv['invoice_no']       ?? '';
    $bc      = $status === 'REJECTED' ? 'danger' : ($status === 'FOR_APPROVAL' ? 'info' : 'warning');
    echo "<div class='card shadow-sm mb-3 border-{$bc}'>
        <div class='card-header py-2 bg-{$bc} text-white font-weight-bold'>
            <i class='fa fa-file-invoice mr-1'></i> {$for} Invoice
        </div><div class='card-body'>
            <div class='d-flex justify-content-between align-items-center flex-wrap'>
                <div>
                    <div class='text-muted' style='font-size:12px;'>Ref: <code>{$ref}</code></div>";
    if ($due) echo "<div class='text-muted' style='font-size:12px;'>Due: " . date('M d, Y', strtotime($due)) . "</div>";
    if ($status === 'REJECTED')
        echo "<div class='alert alert-danger p-2 mt-2 mb-0' style='font-size:12px;'>
            <i class='fa fa-times-circle mr-1'></i> <strong>Rejected:</strong> " . htmlspecialchars($remarks ?: 'No reason given.') . "
            <br>Please resubmit your proof of payment.</div>";
    elseif ($status === 'FOR_APPROVAL')
        echo "<span class='badge badge-info mt-1'><i class='fa fa-clock mr-1'></i> Waiting for admin verification</span>";
    echo "</div><div class='d-flex flex-column align-items-end mt-2'>
        <span class='badge badge-warning mb-2' style='font-size:16px;'>&#8369; " . number_format($amount, 2) . "</span>";
    if ($status === 'REJECTED')
        echo "<button class='btn btn-sm btn-danger' onclick=\"openPayBilling({$id},'REJECTED','" . addslashes($remarks) . "')\">
            <i class='fa fa-redo mr-1'></i> Resubmit Payment</button>";
    elseif ($status === 'PENDING')
        echo "<button class='btn btn-sm btn-success' onclick=\"openPayBilling({$id},'PENDING','')\">
            <i class='fa fa-upload mr-1'></i> Pay Now</button>";
    else
        echo "<span class='badge badge-info'>Submitted — pending admin review</span>";
    echo "</div></div></div></div>";
}
?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="m-0 font-weight-bold"><i class="fa fa-file-invoice text-success mr-2"></i> Billing</h5>
    </div>

    <?php if ($sub) : ?>
        <div class="card border-success shadow-sm mb-3">
            <div class="card-header bg-success text-white font-weight-bold py-2">
                <i class="fa fa-star mr-1"></i> Subscription
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 col-md-3 mb-2">
                        <div class="text-muted" style="font-size:12px;">Type</div>
                        <span class="badge badge-success" style="font-size:14px;"><?= $sub['subscription_type'] ?></span>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="text-muted" style="font-size:12px;">Status</div>
                        <span class="badge <?= $sub['is_active'] ? 'badge-success' : 'badge-danger' ?>" style="font-size:14px;">
                            <?= $sub['is_active'] ? 'ACTIVE' : 'INACTIVE' ?>
                        </span>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="text-muted" style="font-size:12px;">Valid Until</div>
                        <strong><?= date('M d, Y', strtotime($sub['subscription_to'])) ?></strong>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="text-muted" style="font-size:12px;">Due Date</div>
                        <strong><?= date('M d, Y', strtotime($sub['billing_due_date'])) ?></strong>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php renderInvoiceCard($sinv, $uri); ?>
    <?php renderInvoiceCard($svc,  $uri); ?>

    <?php if (!$sinv && !$svc) : ?>
        <div class="alert alert-success mb-3">
            <i class="fa fa-check-circle mr-1"></i> All invoices are paid. You're all good!
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-success text-white font-weight-bold py-2">
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

<!-- Pay/Resubmit Modal -->
<div class="modal fade" id="modalPayBilling" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="payBillingTitle">
                    <i class="fa fa-upload mr-2"></i> Submit Payment
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <?= form_open(base_url($uri . '/Billing/savePayBilling'), 'id="form_save_dataPayBilling" enctype="multipart/form-data"') ?>
            <input type="hidden" name="id" id="payBillingId">
            <div class="modal-body">
                <div id="rejectedAlert" class="alert alert-danger d-none" style="font-size:13px;">
                    <i class="fa fa-exclamation-triangle mr-1"></i>
                    <strong>Payment was rejected.</strong>
                    <div id="rejectedReason" class="mt-1"></div>
                    <div class="mt-1 text-danger">Please attach a new proof of payment.</div>
                </div>
                <p class="text-muted" style="font-size:13px;">Upload your GCash screenshot.</p>
                <label class="btn btn-outline-success w-100 mb-2">
                    <i class="fa fa-image mr-1"></i> Choose GCash Screenshot
                    <input type="file" name="gcash_qr" hidden accept="image/*" onchange="imageView('gcash_qr','previewPayBilling')">
                </label>
                <div class="text-center">
                    <img name="previewPayBilling" src="<?= $system_svg_1x1 ?? '' ?>" style="max-width:100%;max-height:200px;border-radius:8px;border:1px solid #dee2e6;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success btn-sm submitBtnPrimary">Submit</button>
            </div>
            <?= form_close() ?>
        </div>
    </div>
</div>

<script>
    function openPayBilling(id, status, reason) {
        $('#payBillingId').val(id);
        if (status === 'REJECTED') {
            $('#payBillingTitle').html('<i class="fa fa-redo mr-2"></i> Resubmit Payment');
            $('#rejectedAlert').removeClass('d-none');
            $('#rejectedReason').text(reason || '');
        } else {
            $('#payBillingTitle').html('<i class="fa fa-upload mr-2"></i> Submit Payment');
            $('#rejectedAlert').addClass('d-none');
        }
        $('#modalPayBilling').modal('show');
    }
    $(function() {
        getTable('PaymentHistory', 0, 10);
        saveForm("PayBilling", [null], null);
    });
</script>