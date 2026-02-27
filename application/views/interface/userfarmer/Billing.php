<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!$this->session->agrishop_login_level) {
    redirect(base_url('login'));
}
?>
<!-- Highcharts -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mt-2">
            <div class="col-sm-6">
                <h1><i class="nav-icon fas fa-credit-card"></i> Billing </h1>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
<!-- /.content-header -->
<!-- Main content -->
<div class="container mt-4">

    <!-- ================= CURRENT PLAN ================= -->
    <?php if ($subscription["subscription"]) : ?>
        <div class="card mb-4 shadow-sm border-0 rounded-3 p-3" style="background-color: #f8f9fa;">
            <div class="d-flex justify-content-between align-items-center flex-wrap">

                <span class="me-3" style="font-weight: 600; font-size: 1rem;">
                    Plan: <span class="badge bg-primary"> <?= $subscription["subscription"]["subscription_type"]; ?></span>
                </span>

                <span class="me-3">
                    Status:
                    <?php if ($subscription["subscription"]["is_active"]) : ?>
                        <span class="badge bg-success">Active</span>
                    <?php else : ?>
                        <span class="badge bg-danger">Inactive</span>
                    <?php endif; ?>
                </span>

                <span class="me-3">
                    From: <?= date("M j, Y", strtotime($subscription["subscription"]["subscription_from"])); ?>
                </span>

                <span>
                    To: <?= date("M j, Y", strtotime($subscription["subscription"]["subscription_to"])); ?>
                </span>

                <span>
                    Grace Period: 7 days
                </span>

            </div>
        </div>
    <?php endif; ?>
    <!-- ================= INVOICES ================= -->
    <?php foreach (["subscription_invoice", "service_invoice"] as $type) : ?>
        <?php if ($subscription[$type]) :
            $inv = $subscription[$type];
            $label = $type === "subscription_invoice" ? "Subscription" : "Service Fee";
        ?>
            <div class="card mb-3 shadow-sm border-0 rounded-3 p-3 position-relative" style="background-color: #f8f9fa;">

                <!-- PAYMENT FOR Badge Top-Left -->
                <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-warning px-3 py-2 ml-5 mt-2" style="font-size: 0.85rem; z-index: 2;">
                    <?= strtoupper($inv["payment_for"]); ?>
                </span>

                <span class="mt-3 mb-n3" style="font-size: 0.95rem;">
                    Period: <b><?= date("M j, Y", strtotime($inv["billing_period_from"])); ?> - <?= date("M j, Y", strtotime($inv["billing_period_to"])); ?></b>
                </span>

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start flex-wrap gap-2" style="margin-top: 1.5rem;">
                    <!-- Invoice # -->

                    <span class="me-3" style="font-size: 0.95rem;">
                        Invoice #: <b><?= $inv["invoice_no"]; ?></b>
                    </span>

                    <!-- Amount -->
                    <span class="me-3" style="font-size: 0.95rem;">
                        Amount: <span class="badge bg-success">₱<?= number_format($inv["total_payment"], 2); ?></span>
                    </span>
                    <!-- Amount -->
                    <span class="me-3" style="font-size: 0.95rem;">
                        Status: <span class="badge bg-<?= $inv["status"] == 'PAID' ? 'success' : ($inv["status"] == 'PENDING' ? 'warning' : ($inv["status"] == 'REJECTED' ? 'danger' : 'info')) ?>"><?= $inv["status"]; ?></span>
                    </span>

                    <!-- Due Date -->
                    <span class="me-3" style="font-size: 0.95rem;">
                        Due: <span class="badge bg-orange" style="color: #fff !important;"><?= date("M j, Y", strtotime($inv["billing_due_date"])); ?></span>
                    </span>
                    
                    <?php if ($inv["status"] == 'REJECTED') : ?>
                    <span class="me-3" style="font-size: 0.95rem;">
                        Reason: <span class="badge bg-warning"><?= $inv["status_remarks"]; ?></span>
                    </span>
                    <?php endif; ?>

                </div>



                <!-- Pay Button -->
                <?php if ($inv["status"] == 'PENDING' or $inv["status"] == 'REJECTED') : ?>
                    <button class="btn btn-success btn-sm fw-bold payBtn mt-3" data-bs-toggle="modal" data-bs-target="#payModal" style="width: 12rem; font-size: 0.95rem;" onclick="payNow('<?= $inv['payment_for']; ?>','<?= $inv['id']; ?>','<?= $inv['total_payment']; ?>' ,'<?= $inv['invoice_no']; ?>')">
                        <i class="fas fa-credit-card"></i> Pay <?= $label ?>
                    </button>
                <?php endif; ?>
                <?php if ($inv["status"] == 'FOR_APPROVAL') : ?>
                    <button class="btn btn-info btn-sm fw-bold payBtn mt-3" onclick="viewPaymentDetails('<?= $inv['proof_img_path']; ?>')" style="width: 12rem; font-size: 0.95rem;">
                        <i class="fas fa-credit-card"></i> View Payment Details
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <!-- ================= PAYMENT HISTORY ================= -->
    <div class="card">
        <div class="card-body" style="overflow: auto;">
            <h5>Payment History</h5>

            <table class="table table-bordered" id="tblPaymentHistory">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Amount</th>
                    </tr>
                </thead>

                <tbody>
                </tbody>

            </table>
        </div>
    </div>

</div>

<script type="text/javascript">
    $(function() {
        getTable('PaymentHistory', 0, 10);
        saveForm("PayBilling", [null], null);
    });

    function viewPaymentDetails(img) {
        $("#paymentProofImg").attr("src", "<?= base_url(); ?>" + img);
        $("#viewPaymentDetailsModal").modal("show");
    }

    function payNow(paymentFor, id, amount, invoiceNo) {
        // Update modal fields
        $("#hidden_id").val(id);
        $("#paymentFor").text(paymentFor);
        $("#amount").text("₱" + amount);
        $("#reference").text(invoiceNo);
    }
</script>
<!-- /.content -->