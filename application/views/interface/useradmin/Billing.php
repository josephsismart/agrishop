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

    <!-- SUMMARY CARDS -->

    <div class="row mb-3">

        <div class="col-md-3 col-6 ">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fa fa-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Paid</span>
                    <span class="info-box-number fs-4"><?= $initial_data["paid"] ?></span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6 ">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fa fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pending</span>
                    <span class="info-box-number fs-4"><?= $initial_data["pending"] ?></span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fa fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">For Approval</span>
                    <span class="info-box-number fs-4"><?= $initial_data["for_approval"] ?></span>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-6 ">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fa fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Rejected</span>
                    <span class="info-box-number fs-4"><?= $initial_data["rejected"] ?></span>
                </div>
            </div>
        </div>

    </div>

</div>


<section class="content">
    <div class="container-fluid">


        <div class="row">

            <div class="col-md-12">
                <div class="card">

                    <div class="card-header">
                        <h5 class="card-title"><span class="fa fa-credit-card"></span> Farmer Billing Management</h5>
                    </div>

                    <div class="card-body p-3" style="overflow-x: auto;">

                        <table class="table table-striped table-hover" id="tblBillingFarmers">
                            <thead class="table">
                                <tr>
                                    <th>Date</th>
                                    <th>Farmer</th>
                                    <th>Reference</th>
                                    <th>Payment For</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>




            <div class="col-md-12">
                <div class="card">

                    <div class="card-header  bg-dark text-white">
                        <h5 class="card-title"><span class="fa fa-credit-card"></span> Billing History</h5>
                    </div>

                    <div class="card-body p-3" style="overflow-x: auto;">

                        <table class="table table-striped table-hover" id="tblBillingHistory">
                            <thead class="table-dark">
                                <tr>
                                    <th>Billing</th>
                                    <th>Farmer</th>
                                    <th>Reference</th>
                                    <th>Payment For</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>View</th>
                                </tr>
                            </thead>

                            <tbody>

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

        </div>

    </div>

</section>




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


<script type="text/javascript">
    $(function() {
        getTable('BillingFarmers', 0, 10);
        getTable('BillingHistory', 0, 5);
    });


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

    function viewPaymentDetails(img) {
        $("#paymentProofImg").attr("src", "<?= base_url(); ?>" + img);
        $("#viewPaymentDetailsModal").modal("show");
    }

    function rejectPayment(billing_id) {
        let pay = $('input[name="payment_method"]:checked').val();
        let trans_id = $("#pay_cash").data('trans_id');

        Swal.fire({
            title: '<label style="font-size:24px;">Reject Payment</label>',
            html: `
            <textarea id="rejection_reason"
                class="swal2-textarea mt-n1 mb-n2"
                placeholder="Please tell us your reason for rejecting..."
                style="font-size:13px;"></textarea>
        `,
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {
                popup: 'swal-mini',
                icon: 'no-border'
            },
            preConfirm: () => {
                let reason = document.getElementById('rejection_reason').value.trim();
                if (!reason) {
                    Swal.showValidationMessage('Rejection reason is required');
                    return false;
                }
                return reason;
            }
        }).then(r => {

            if (!r.isConfirmed) return;

            let reason = r.value; // 👈 from textarea

            let fd = new FormData();
            fd.append('billing_id', billing_id);
            fd.append('rejection_reason', reason); // 👈 PASS TO CONTROLLER

            $.ajax({
                url: "<?= base_url('useradmin/Billing/reject_payment') ?>",
                type: "POST",
                data: fd,
                processData: false,
                contentType: false,
                success: res => {
                    let j = JSON.parse(res);
                    if (j.success == true) {
                        successAlert(j.message);
                        getTable('BillingFarmers', 0, 10);
                    } else {
                        failAlert(j.message);
                    }
                }
            });
        });
    }


    function approvePayment(billing_id, total_payment, payment_for, reference) {

        Swal.fire({
            title: '<label style="font-size: 18px;">Confirm GCash Payment</label>',
            html: 'Amount: <b>₱ ' + total_payment + '</b><br>Reference: <b>' + reference + '</b><br>Payment For: <b>' + payment_for + '</b>',
            iconHtml: '<img src="<?= base_url('dist/img/credit/gcash_50x50.png') ?>" width="100">',
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#28a745', // green
            cancelButtonColor: '#dc3545', // red
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {
                popup: 'swal-mini',
                icon: 'no-border'
            }

        }).then(r => {
            if (!r.isConfirmed) return;
            let fd = new FormData();
            fd.append('billing_id', billing_id);
            $.ajax({
                url: "<?= base_url('useradmin/Billing/accept_payment') ?>",
                type: "POST",
                data: fd,
                processData: false,
                contentType: false,
                success: res => {
                    let j = JSON.parse(res);
                    if (j.success == true) {
                        successAlert(j.message);
                        getTable('BillingFarmers', 0, 10);
                        getTable('BillingHistory', 0, 5);
                    } else {
                        failAlert(j.message);
                    }

                }
            });
        });
    }
</script>

<!-- /.content -->