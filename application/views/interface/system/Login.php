<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $system_title ?> | <?= $page_title ?></title>
    <link rel="icon" type="image/png" href="<?= $system_svg ?>">
    <link rel="stylesheet" href="<?= base_url() ?>plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>plugins/bootstrap-alpha3/bootstrap.min.css">
</head>

<style>
* { box-sizing: border-box; }
body {
    background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 50%, #f0fdf4 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    padding: 20px;
}

.login-wrapper {
    width: 100%;
    max-width: 420px;
}

.login-logo {
    text-align: center;
    margin-bottom: 28px;
}
.login-logo img { width: 160px; }
.login-logo p {
    color: #6b7280;
    font-size: 14px;
    margin-top: 8px;
    margin-bottom: 0;
}

.login-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 4px 24px rgba(0,0,0,.10);
    overflow: hidden;
}
.login-card-top {
    height: 5px;
    background: linear-gradient(90deg, #059669, #10b981, #34d399);
}
.login-card-body { padding: 32px; }

.login-title {
    font-size: 22px;
    font-weight: 800;
    color: #111827;
    margin: 0 0 4px;
}
.login-sub {
    font-size: 13px;
    color: #9ca3af;
    margin: 0 0 28px;
}

.error-box {
    background: #fee2e2;
    border-radius: 10px;
    padding: 12px 14px;
    font-size: 13px;
    font-weight: 600;
    color: #dc2626;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.field-group { margin-bottom: 16px; }
.field-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
}

.field-wrap { position: relative; }
.field-icon {
    position: absolute;
    left: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    font-size: 14px;
    pointer-events: none;
}
.field-input {
    width: 100%;
    padding: 11px 13px 11px 38px;
    border: 1.5px solid #e5e7eb;
    border-radius: 11px;
    font-size: 14px;
    color: #111827;
    background: #fafafa;
    transition: all .2s;
    outline: none;
}
.field-input:focus {
    border-color: #10b981;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(16,185,129,.12);
}
.field-input.error {
    border-color: #ef4444;
    background: #fff5f5;
}

.pw-toggle {
    position: absolute;
    right: 13px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
    cursor: pointer;
    font-size: 14px;
    background: none;
    border: none;
    padding: 0;
}
.pw-toggle:hover { color: #374151; }

.btn-login {
    width: 100%;
    padding: 13px;
    background: #059669;
    color: #fff;
    border: none;
    border-radius: 11px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: all .2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-bottom: 14px;
}
.btn-login:hover {
    background: #047857;
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(5,150,105,.3);
}
.btn-login:active { transform: translateY(0); }

.divider {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 4px 0 14px;
    color: #d1d5db;
    font-size: 12px;
}
.divider::before, .divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #e5e7eb;
}

.btn-signup {
    width: 100%;
    padding: 12px;
    background: transparent;
    color: #374151;
    border: 1.5px solid #e5e7eb;
    border-radius: 11px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}
.btn-signup:hover {
    background: #f9fafb;
    border-color: #d1d5db;
    color: #111827;
    text-decoration: none;
}

.login-footer {
    text-align: center;
    margin-top: 24px;
    font-size: 12px;
    color: #9ca3af;
}

/* Role hints at bottom */
.role-hints {
    display: flex;
    justify-content: center;
    gap: 16px;
    margin-top: 20px;
}
.role-hint {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #9ca3af;
}
.role-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
}
</style>

<body>
<div class="login-wrapper">

    <!-- Logo -->
    <div class="login-logo">
        <a href="<?= base_url() ?>index">
            <img src="<?= base_url() ?>dist/layout_shop/images/logo.svg" alt="AgriShop">
        </a>
        <p>Farm-fresh produce & agricultural supplies</p>
    </div>

    <!-- Card -->
    <div class="login-card">
        <div class="login-card-top"></div>
        <div class="login-card-body">

            <h4 class="login-title">Welcome back!</h4>
            <p class="login-sub">Sign in to your AgriShop account</p>

            <?php if ($this->input->get("login_attempt") == md5(0) || $this->input->get("login_attempt") == md5(1)): ?>
                <div class="error-box">
                    <i class="fa fa-exclamation-circle"></i>
                    Invalid username or password. Please try again.
                </div>
            <?php endif; ?>

            <form action="<?= base_url() ?>requestlogin" method="post">

                <div class="field-group">
                    <label class="field-label">Username</label>
                    <div class="field-wrap">
                        <i class="fa fa-user field-icon"></i>
                        <input type="text" name="username" class="field-input <?= ($this->input->get("login_attempt") == md5(0) || $this->input->get("login_attempt") == md5(1)) ? 'error' : '' ?>"
                            placeholder="Enter your username"
                            autocomplete="off"
                            autofocus
                            required>
                    </div>
                </div>

                <div class="field-group">
                    <label class="field-label">Password</label>
                    <div class="field-wrap">
                        <i class="fa fa-lock field-icon"></i>
                        <input type="password" name="password" id="loginPassword"
                            class="field-input <?= ($this->input->get("login_attempt") == md5(0) || $this->input->get("login_attempt") == md5(1)) ? 'error' : '' ?>"
                            placeholder="Enter your password"
                            required>
                        <button type="button" class="pw-toggle" onclick="togglePassword()">
                            <i class="fa fa-eye" id="pwIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fa fa-sign-in-alt"></i> Sign In
                </button>

                <div class="divider">or</div>

                <a href="<?= base_url() ?>signup" class="btn-signup">
                    <i class="fa fa-user-plus"></i> Create New Account
                </a>

            </form>
        </div>
    </div>

    <!-- Role hints -->
    <!-- <div class="role-hints">
        <div class="role-hint">
            <div class="role-dot" style="background:#059669;"></div>
            Customer
        </div>
        <div class="role-hint">
            <div class="role-dot" style="background:#dc2626;"></div>
            Farmer
        </div>
        <div class="role-hint">
            <div class="role-dot" style="background:#d97706;"></div>
            Supplier
        </div>
        <div class="role-hint">
            <div class="role-dot" style="background:#7c3aed;"></div>
            Admin
        </div>
    </div> -->

    <p class="login-footer">AgriShop &copy; <?= date('Y') ?> — Connecting Farmers &amp; Suppliers</p>
</div>

<script>
function togglePassword() {
    var input = document.getElementById('loginPassword');
    var icon  = document.getElementById('pwIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fa fa-eye';
    }
}
</script>
</body>
</html>
