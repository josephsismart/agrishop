<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $system_title ?> | Pending Approval</title>
    <link rel="icon" type="image/png" href="<?= $system_svg ?>">
    <link rel="stylesheet" href="<?= base_url() ?>plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>plugins/bootstrap-alpha3/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>dist/css/adminlte.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>plugins/sweetalert2/sweetalert2.min.css">
    <script src="<?= base_url() ?>plugins/jquery/jquery.min.js"></script>
    <script src="<?= base_url() ?>plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="<?= base_url() ?>dist/js/confetti.browser.min.js"></script>
</head>
<body style="background:linear-gradient(135deg,#e8f5e9 0%,#fff8e1 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;">

<div style="max-width:480px;width:100%;padding:20px;">

    <div class="text-center mb-4">
        <img src="<?= base_url() ?>dist/layout_shop/images/logo.svg" width="180" alt="AgriShop">
    </div>

    <div class="card shadow border-0" style="border-radius:18px;overflow:hidden;">
        <div style="height:6px;background:linear-gradient(90deg,#27ae60,#e67e22,#f39c12);"></div>
        <div class="card-body p-4 text-center">

            <div class="pending-icon-wrapper mx-auto mb-3">
                <i class="fa fa-clock" style="font-size:38px;color:#e67e22;"></i>
            </div>

            <h4 style="font-weight:700;color:#2c3e50;">Application Submitted!</h4>
            <p class="text-muted mb-3" style="font-size:15px;">
                Your registration as a
                <strong style="color:#e67e22;">
                    <?= $this->session->agrishop_login_level == 4 ? 'Supplier' : 'Farmer' ?>
                </strong>
                is currently under review.
            </p>

            <!-- Status timeline -->
            <div style="background:#f8f9fa;border-radius:14px;padding:16px;text-align:left;margin-bottom:20px;">
                <div class="d-flex align-items-center mb-3">
                    <div style="width:34px;height:34px;border-radius:50%;background:#27ae60;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa fa-check text-white" style="font-size:14px;"></i>
                    </div>
                    <div class="ml-3">
                        <div style="font-weight:600;font-size:14px;">Account Created</div>
                        <div style="font-size:12px;color:#888;"><?= date('M d, Y h:i A') ?></div>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="pulse-ring" style="width:34px;height:34px;border-radius:50%;background:#e67e22;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa fa-spinner fa-spin text-white" style="font-size:14px;"></i>
                    </div>
                    <div class="ml-3">
                        <div style="font-weight:600;font-size:14px;">Admin Review
                            <span id="pollDot" style="display:inline-block;width:8px;height:8px;
                                border-radius:50%;background:#e67e22;margin-left:6px;
                                transition:opacity .3s;" title="Checking every 15 seconds"></span>
                        </div>
                        <div style="font-size:12px;color:#888;">Your ID and information are being verified — checking automatically</div>
                    </div>
                </div>
                <div class="d-flex align-items-center" style="opacity:0.35;">
                    <div style="width:34px;height:34px;border-radius:50%;background:#dee2e6;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa fa-unlock" style="font-size:14px;color:#666;"></i>
                    </div>
                    <div class="ml-3">
                        <div style="font-weight:600;font-size:14px;">Access Granted</div>
                        <div style="font-size:12px;color:#888;">Full portal access after approval</div>
                    </div>
                </div>
            </div>

            <!-- Free subscription teaser -->
            <div style="background:linear-gradient(135deg,#27ae60,#2ecc71);border-radius:12px;padding:14px 16px;margin-bottom:20px;color:#fff;text-align:left;">
                <div style="font-weight:700;font-size:15px;"><i class="fa fa-gift mr-2"></i> 2 Months FREE Subscription Waiting!</div>
                <div style="font-size:13px;opacity:0.9;margin-top:4px;">Once approved, you'll receive <strong>2 months free</strong> access to all features.</div>
            </div>

            <div style="background:#d1ecf1;border-radius:10px;padding:12px;font-size:13px;color:#0c5460;text-align:left;margin-bottom:20px;">
                <i class="fa fa-info-circle mr-1"></i>
                <strong>What happens next?</strong><br>
                The admin will review your submitted ID and information. You will be notified once your account is approved. This usually takes <strong>1-2 business days</strong>.
            </div>

            <button onclick="checkApproval()" id="checkBtn" class="btn btn-warning btn-block mb-2" style="border-radius:10px;font-weight:600;font-size:15px;">
                <i class="fa fa-sync mr-2"></i> Check Approval Status
            </button>

            <a href="<?= base_url() ?>login/request_logout" class="btn btn-outline-secondary btn-block" style="border-radius:10px;">
                <i class="fa fa-sign-out-alt mr-2"></i> Logout
            </a>
        </div>
    </div>

    <p class="text-center text-muted mt-3" style="font-size:12px;">
        AgriShop &copy; <?= date('Y') ?> &mdash; Connecting Farmers &amp; Suppliers
    </p>
</div>

<!-- Congratulations Overlay -->
<div id="congratsOverlay" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;
     background:rgba(0,0,0,.65);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:22px;padding:36px 28px;max-width:420px;width:90%;
                text-align:center;box-shadow:0 16px 48px rgba(0,0,0,.35);animation:popIn .4s ease;">
        <div style="font-size:60px;margin-bottom:8px;">&#x1F389;</div>
        <h3 style="font-weight:800;color:#27ae60;margin-bottom:6px;">Congratulations!</h3>
        <p style="font-size:15px;color:#555;margin-bottom:18px;">
            Your application has been <strong style="color:#27ae60;">approved</strong>!<br>
            Welcome to AgriShop.
        </p>
        <div style="background:linear-gradient(135deg,#27ae60,#2ecc71);border-radius:14px;padding:18px;color:#fff;margin-bottom:22px;">
            <div style="font-size:30px;font-weight:800;">&#x1F381; 2 Months FREE</div>
            <div style="font-size:14px;opacity:.9;margin-top:6px;">
                Your free subscription has been activated!<br>
                Enjoy full access for the next <strong>2 months</strong>.
            </div>
        </div>
        <button id="btnCongratsContinue" class="btn btn-success btn-block"
                style="border-radius:12px;font-size:16px;font-weight:700;padding:14px;">
            <i class="fa fa-arrow-right mr-2"></i> Go to My Dashboard
        </button>
    </div>
</div>

<style>
@keyframes pulse {
    0%,100% { box-shadow: 0 0 0 0 rgba(230,126,34,.4); }
    50%      { box-shadow: 0 0 0 12px rgba(230,126,34,0); }
}
@keyframes popIn {
    0%   { transform:scale(.8);opacity:0; }
    100% { transform:scale(1);opacity:1;  }
}
.pulse-ring       { animation: pulse 2s ease-in-out infinite; }
.pending-icon-wrapper {
    width:80px;height:80px;border-radius:50%;
    background:#fff3cd;border:3px solid #f39c12;
    display:flex;align-items:center;justify-content:center;
    animation:pulse 2s ease-in-out infinite;
}
</style>

<script>
// ── State ──────────────────────────────────────────────
let redirectUrl  = '';
let pollInterval = null;
let pollCount    = 0;

// ── Manual check button ─────────────────────────────────
function checkApproval() {
    let btn = document.getElementById('checkBtn');
    btn.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i> Checking...';
    btn.disabled  = true;
    doCheck(function(done) {
        if (!done) {
            btn.innerHTML = '<i class="fa fa-sync mr-2"></i> Still pending... Try again';
            btn.disabled  = false;
        }
    });
}

// ── Core check logic ────────────────────────────────────
function doCheck(callback) {
    $.get("<?= base_url('pending/checkApprovalStatus') ?>", function(res) {
        try {
            let d = JSON.parse(res);
            if (d.approved) {
                stopPolling();
                redirectUrl = d.redirect;
                showCongratulations();
                if (callback) callback(true);
            } else if (d.rejected) {
                stopPolling();
                let btn = document.getElementById('checkBtn');
                btn.innerHTML = '<i class="fa fa-times mr-2"></i> Application Rejected';
                btn.className = 'btn btn-danger btn-block';
                btn.disabled  = true;
                Swal.fire({
                    icon: 'error',
                    title: 'Application Rejected',
                    text: 'Reason: ' + (d.reason || 'No reason provided.'),
                    confirmButtonColor: '#e74c3c'
                });
                if (callback) callback(true);
            } else {
                if (callback) callback(false);
            }
        } catch(e) {
            if (callback) callback(false);
        }
    }).fail(function() {
        if (callback) callback(false);
    });
}

// ── Auto-polling every 15 seconds ───────────────────────
function startPolling() {
    if (pollInterval) return;
    pollInterval = setInterval(function() {
        pollCount++;
        doCheck(null);
        // Update pulse indicator
        let dot = document.getElementById('pollDot');
        if (dot) {
            dot.style.opacity = '0.3';
            setTimeout(function() { dot.style.opacity = '1'; }, 500);
        }
    }, 15000);
}

function stopPolling() {
    if (pollInterval) { clearInterval(pollInterval); pollInterval = null; }
}

// Start polling immediately on load
$(function() {
    startPolling();
    // First check after 2s (in case admin already approved during form submit)
    setTimeout(function() { doCheck(null); }, 2000);
});

function showCongratulations() {
    document.getElementById('congratsOverlay').style.display = 'flex';
    fireConfetti();
    document.getElementById('btnCongratsContinue').onclick = function() {
        window.location = redirectUrl;
    };
}

function fireConfetti() {
    if (typeof confetti === 'undefined') {
        setTimeout(function(){ window.location = redirectUrl; }, 1500);
        return;
    }
    let end = Date.now() + 3500;
    let colors = ['#27ae60','#f39c12','#e74c3c','#3498db','#9b59b6','#fff'];
    (function frame() {
        confetti({ particleCount:7, angle:60,  spread:55, origin:{x:0}, colors:colors });
        confetti({ particleCount:7, angle:120, spread:55, origin:{x:1}, colors:colors });
        if (Date.now() < end) requestAnimationFrame(frame);
    }());
}
</script>
</body>
</html>
