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


<script type="text/javascript">
    $(function() {
        getTable('BillingFarmers', 0, 10);
        getTable('BillingHistory', 0, 5);
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