<?php
    // $this->redirect();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $system_title ?> | Subscription Pending</title>

    <link rel="icon" type="image/png" href="<?= $system_svg ?>">

    <!-- BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- BOOTSTRAP ICONS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- GOOGLE FONT -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e8f5e9, #ffffff);
            min-height: 100vh;
        }

        .status-card {
            border-radius: 1.5rem;
            padding: 2.5rem;
            text-align: center;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
            100% { transform: translateY(0px); }
        }

        .pending-icon {
            font-size: 4rem;
        }

        .badge-status {
            background: #ffc107;
            color: #000;
            font-weight: 600;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
        }

        .subtle-text {
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <nav class="navbar navbar-light bg-white shadow-sm">
        <div class="container d-flex align-items-center justify-content-between">

            <!-- LEFT: LOGO -->
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="<?= base_url(); ?>dist/layout_shop/images/logo.svg"
                     alt="logo"
                     class="img-fluid"
                     style="height:40px;">
            </a>

            <!-- RIGHT: LOGOUT -->
            <a href="<?= base_url('logout'); ?>" class="btn bg-black text-white btn-sm fw-semibold">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>

        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <div class="container d-flex align-items-center justify-content-center py-5">
        <div class="col-lg-6 col-md-8">

            <div class="card status-card border-0">

                <div class="pending-icon text-warning mb-3">
                    ⏳
                </div>

                <span class="badge-status mb-3 d-inline-block">
                    PAYMENT UNDER REVIEW
                </span>

                <h2 class="fw-bold text-success mt-3">
                    Please Wait for Admin Validation
                </h2>

                <p class="text-muted mt-3">
                    Thank you for submitting your subscription payment 🌾  
                    Our admin is currently verifying your proof of payment.
                </p>

                <div class="bg-light rounded-4 p-4 mt-4">
                    <div class="row text-start">
                        <div class="col-6 subtle-text">💳 Payment Method</div>
                        <div class="col-6 fw-semibold">GCash</div>

                        <div class="col-6 subtle-text mt-2">💰 Amount</div>
                        <div class="col-6 fw-semibold mt-2">₱99.00</div>

                        <div class="col-6 subtle-text mt-2">📌 Status</div>
                        <div class="col-6 fw-semibold text-warning mt-2">
                            Pending Verification
                        </div>
                    </div>
                </div>

                <p class="subtle-text mt-4">
                    ⏱ Verification usually takes a few hours.  
                    You’ll be notified once your subscription is approved.
                </p>

                <div class="mt-3">
                    <i class="bi bi-shield-check text-success"></i>
                    Your farm data is safe and untouched
                </div>

            </div>

            <!-- FOOTER -->
            <div class="text-center mt-4 subtle-text">
                ❤️ Thank you for supporting local farmers
            </div>

        </div>
    </div>

    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
