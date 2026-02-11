<?php
    // $this->redirect();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $system_title ?> | <?= $page_title ?></title>

    <link rel="icon" type="image/png" href="<?= $system_svg ?>">
    <!-- BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- BOOTSTRAP ICONS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- GOOGLE FONT -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url() ?>plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e8f5e9, #ffffff);
            min-height: 100vh;
        }

        .subscription-card {
            border-radius: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .subscription-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .price-tag {
            font-size: 3rem;
            font-weight: 700;
            color: #198754;
        }

        .badge-popular {
            position: absolute;
            top: -12px;
            right: 20px;
            background: #ffc107;
            color: #000;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.85rem;
        }

        .feature-item {
            font-size: 1.05rem;
            margin-bottom: 0.6rem;
        }

        .subscribe-btn {
            font-size: 1.15rem;
            padding: 14px;
            border-radius: 50px;
        }

        .trust-text {
            font-size: 0.85rem;
            color: #6c757d;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <nav class="navbar navbar-light bg-white shadow-sm">
        <div class="container d-flex align-items-center justify-content-between">

            <!-- LEFT: LOGO -->
            <a class="navbar-brand fw-bold text-success d-flex align-items-center" href="#">
                <img src="<?php echo base_url(); ?>dist/layout_shop/images/logo.svg" alt="logo" class="img-fluid" style="height:40px;">
            </a>

            <!-- RIGHT: LOGOUT -->
            <div>
                <a href="<?php echo base_url('logout'); ?>" class="btn bg-black text-white btn-sm fw-semibold">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>

        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <div class="container d-flex align-items-center justify-content-center py-5">
        <div class="col-lg-6 col-md-8">

            <div class="card subscription-card shadow border-0 position-relative p-4">

                <span class="badge-popular">⭐ MOST POPULAR</span>

                <div class="text-center mb-3">
                    <h2 class="fw-bold text-success">
                        Keep Selling Without Limits
                    </h2>
                    <p class="text-muted">
                        Your farms & produce are safe.
                        Renew your subscription and continue growing 🌾
                    </p>
                </div>

                <div class="bg-light rounded-4 text-center py-4 mb-4">
                    <div class="price-tag">
                        ₱99
                        <span class="fs-5 fw-normal">/ month</span>
                    </div>
                    <div class="text-muted">
                        Less than <b>₱4 per day</b>
                    </div>
                </div>

                <!-- FEATURES -->
                <div class="row mb-4">
                    <div class="col-6 feature-item">✅ Unlimited Farms</div>
                    <div class="col-6 feature-item">✅ Unlimited Produce</div>
                    <div class="col-6 feature-item">🔍 Priority Search</div>
                    <div class="col-6 feature-item">📈 Higher Visibility</div>
                    <div class="col-6 feature-item">💬 Direct Buyer Access</div>
                    <div class="col-6 feature-item">🛡️ Data Protection</div>
                </div>

                <!-- CTA -->
                <button class="btn btn-success subscribe-btn fw-bold" data-bs-toggle="modal" data-bs-target="#gcashPaymentModal">
                    Subscribe Now 🌾
                </button>

                <div class="text-center trust-text">
                    No contracts • Cancel anytime • Secure payment
                </div>

            </div>

            <!-- FOOTER TEXT -->
            <div class="text-center mt-4 text-muted" style="font-size:0.85rem;">
                ❤️ Thank you for supporting local farmers
            </div>

        </div>
    </div>


    <div class="modal fade" id="gcashPaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">

                <!-- HEADER -->
                <div class="modal-header bg-primary text-white text-center">
                    <h5 class="modal-title w-100 fw-bold">
                        💳 GCash Payment
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body text-center px-4 py-4">

                    <p class="text-muted mb-3">
                        Scan the QR code below and upload your proof of payment.
                    </p>

                    <!-- QR CODE -->
                    <div class="mb-3">
                        <img src="<?php echo base_url(); ?>dist/images/gcash_qr.png" alt="GCash QR" class="img-fluid rounded shadow" style="max-width:220px;">
                    </div>

                    <!-- ACCOUNT DETAILS -->
                    <div class="bg-light rounded-3 p-3 mb-3 text-start text-center">
                        <div class="mb-2">
                            <strong>Account Name:</strong><br>
                            Maria Santos
                        </div>
                        <div class="mb-2">
                            <strong>GCash Number:</strong><br>
                            0934-123-3214
                        </div>
                        <div>
                            <strong>Amount:</strong>
                            <span class="text-success fw-bold">₱99.00</span>
                        </div>
                    </div>

                    <!-- UPLOAD PROOF -->
                    <form id="gcashProofForm" enctype="multipart/form-data">

                        <div class="mb-3 text-start">
                            <label class="form-label fw-semibold">
                                📎 Upload Proof of Payment
                            </label>
                            <input type="file" class="form-control" name="proof_payment" accept="image/*" required>
                            <small class="text-muted">
                                Screenshot or photo of your GCash receipt
                            </small>
                        </div>

                        <!-- FOOTER ACTION -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success fw-bold">
                                Submit for Verification ✅
                            </button>
                        </div>

                    </form>

                    <p class="text-muted mt-3" style="font-size:0.85rem;">
                        Your subscription will be activated after admin verification.
                    </p>

                </div>
            </div>
        </div>
    </div>

    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Hook this to your payment logic
        //document.querySelector('.subscribe-btn').addEventListener('click', function() {
        //alert('Redirecting to payment gateway...');
        // window.location.href = '/payment';
        //});
    </script>

</body>

<script src="<?= base_url() ?>plugins/jquery/jquery.min.js"></script>
<script src="<?= base_url() ?>plugins/sweetalert2/sweetalert2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#gcashProofForm').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            $.ajax({
                url: '<?= base_url() ?>subscribe_application',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Optionally show a success message or redirect
                    let res = JSON.parse(response);
                    successAlert(res.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1500);

                },
                error: function(xhr, status, error) {
                    console.log(error);
                    failAlert(res.message);
                }
            });
        });

    });

    const Toast = Swal.mixin({
        toast: true,
        position: 'center',
        showConfirmButton: false,
        timer: 3000
    });

    function successAlert(a) {
        Toast.fire({
            icon: 'success',
            title: '  ' + a
        })
    }

    function failAlert(a) {
        Toast.fire({
            icon: 'error',
            title: '  ' + a
        })
    }
</script>

</html>