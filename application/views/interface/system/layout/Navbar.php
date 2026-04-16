<?php
$s           = $this->session;
$uri         = $s->agrishop_login_uri ?: '';
$role_lvl    = (int)$s->agrishop_login_level;
$is_farmer   = (int)$s->agrishop_is_approved_farmer;
$is_supplier = (int)$s->agrishop_is_approved_supplier;
$has_manage  = $is_farmer || $is_supplier || $role_lvl === 3;
$login_id    = $s->agrishop_login_id;

$pending_count  = $status['transaction_status_pending']           ?? 0;
$reserved_count = $status['transaction_status_reserved']          ?? 0;
$rate_count     = $status['transaction_ratings']                  ?? 0;
$orders_total   = (int)$reserved_count
                + (int)($status['transaction_delivery_status_preparing'] ?? 0)
                + (int)($status['transaction_delivery_status_pickup']    ?? 0)
                + (int)($status['transaction_delivery_status_delivery']  ?? 0);

$nav_img = $s->agrishop_login_img ?: base_url('dist/img/media/icons/1x1.png');
$nav_name = trim($s->agrishop_login_first_name . ' ' . $s->agrishop_login_last_name);
$role_label = $role_lvl === 3 ? 'Admin'
    : ($is_farmer && $is_supplier ? 'Farmer / Supplier'
    : ($is_farmer   ? 'Farmer'
    : ($is_supplier ? 'Supplier'
    : 'Customer')));
?>
<style>
:root { --agri-nav-h: 64px; }

.agri-nav {
    position: fixed;
    top: 0; left: 0; right: 0;
    height: var(--agri-nav-h);
    background: #fff;
    border-bottom: 2px solid #e8f5e9;
    box-shadow: 0 2px 10px rgba(0,0,0,.08);
    display: flex;
    align-items: center;
    padding: 0 16px;
    gap: 10px;
    z-index: 1030;
}

.agri-nav-spacer { height: var(--agri-nav-h); }

/* Logo */
.agri-nav-logo img { height: 38px; }

/* Right icons row */
.agri-nav-right {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-left: auto;
}

/* Icon button */
.agri-nav-btn {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 6px 10px;
    border-radius: 8px;
    color: #374151;
    font-size: 11px;
    cursor: pointer;
    text-decoration: none !important;
    transition: background .15s, color .15s;
    border: none;
    background: none;
    line-height: 1;
    gap: 2px;
}
.agri-nav-btn i { font-size: 17px; }
.agri-nav-btn:hover, .agri-nav-btn.active { background: #f0fdf4; color: #166534; }
.agri-nav-btn .agri-badge {
    position: absolute;
    top: 2px; right: 4px;
    min-width: 16px; height: 16px;
    border-radius: 8px;
    font-size: 10px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    padding: 0 3px;
    pointer-events: none;
}
.agri-badge-amber { background: #f59e0b; color: #fff; }
.agri-badge-red   { background: #ef4444; color: #fff; }

/* Vertical divider */
.agri-nav-divider {
    width: 1px; height: 28px;
    background: #e5e7eb; margin: 0 4px;
}

/* Manage Panel button */
.agri-manage-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border-radius: 20px;
    background: #166534;
    color: #fff !important;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    text-decoration: none !important;
    transition: background .15s;
    white-space: nowrap;
}
.agri-manage-btn:hover { background: #14532d; }

/* Dropdown wrapper */
.agri-dd-wrap { position: relative; }
.agri-dd-menu {
    display: none;
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    min-width: 230px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 12px 40px rgba(0,0,0,.15);
    border: 1px solid #e8f5e9;
    overflow: hidden;
    z-index: 2000;
}
.agri-dd-menu.open { display: block; }
.agri-dd-section {
    padding: 6px 0;
    border-bottom: 1px solid #f1f5f9;
}
.agri-dd-section:last-child { border-bottom: none; }
.agri-dd-label {
    font-size: 10px;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .06em;
    padding: 4px 16px 2px;
}
.agri-dd-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 16px;
    font-size: 13px;
    color: #374151;
    text-decoration: none !important;
    transition: background .12s;
    cursor: pointer;
}
.agri-dd-item:hover { background: #f0fdf4; color: #166534; }
.agri-dd-item i { width: 16px; text-align: center; color: #6b7280; }
.agri-dd-item:hover i { color: #16a34a; }

/* Profile avatar */
.agri-avatar {
    width: 32px; height: 32px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #d1fae5;
}
.agri-profile-head {
    padding: 12px 16px 8px;
    display: flex;
    align-items: center;
    gap: 10px;
    border-bottom: 1px solid #f1f5f9;
}
.agri-profile-head img {
    width: 40px; height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #16a34a;
}
.agri-profile-head .name { font-size: 13px; font-weight: 600; color: #111827; line-height: 1.2; }
.agri-profile-head .role { font-size: 11px; color: #6b7280; }

/* Guest buttons */
.agri-btn-ghost {
    padding: 6px 14px;
    border-radius: 20px;
    color: #166534;
    font-size: 12px;
    font-weight: 600;
    border: 1.5px solid #166534;
    background: transparent;
    text-decoration: none !important;
    transition: background .15s, color .15s;
    white-space: nowrap;
}
.agri-btn-ghost:hover { background: #f0fdf4; }
.agri-btn-solid {
    padding: 6px 14px;
    border-radius: 20px;
    color: #fff !important;
    font-size: 12px;
    font-weight: 700;
    background: #16a34a;
    text-decoration: none !important;
    transition: background .15s;
    white-space: nowrap;
}
.agri-btn-solid:hover { background: #166534; }

/* Farm Supplies link */
.agri-supplies-link {
    color: #374151;
    font-size: 12px;
    font-weight: 500;
    text-decoration: none !important;
    white-space: nowrap;
    padding: 6px 8px;
    border-radius: 8px;
    transition: background .15s, color .15s;
}
.agri-supplies-link:hover { background: #f0fdf4; color: #166534; }

@media (max-width: 576px) {
    .agri-nav-btn span { display: none; }
    .agri-supplies-link span { display: none; }
}
</style>

<nav class="agri-nav">

    <!-- Logo -->
    <a href="<?= base_url() ?>" class="agri-nav-logo">
        <img src="<?= base_url('dist/layout_shop/images/logo.svg') ?>" alt="AgriShop">
    </a>

    <div class="agri-nav-divider"></div>

    <div class="agri-nav-right">

        <!-- Farm Supplies -->
        <a href="#" class="agri-supplies-link" onclick="showSuppliesShop(); return false;">
            <i class="fa fa-store mr-1"></i><span>Farm Supplies</span>
        </a>

        <?php if ($login_id): ?>

            <!-- Cart -->
            <a href="#" class="agri-nav-btn"
               data-toggle="modal" data-target="#modalCartListing"
               onclick="getTable('CartListing',0,5);" title="My Cart">
                <i class="fa fa-shopping-cart"></i>
                <span>Cart</span>
                <span class="agri-badge agri-badge-amber pending-order"><?= $pending_count ?></span>
            </a>

            <!-- Orders (badge shows active deliveries + unrated) -->
            <a href="#" class="agri-nav-btn" title="My Orders"
               data-toggle="modal" data-target="#modalCustomerOrders">
                <i class="fa fa-box"></i>
                <span>Orders</span>
                <?php $nav_order_badge = $orders_total + (int)$rate_count; ?>
                <?php if ($nav_order_badge > 0): ?>
                <span class="agri-badge agri-badge-red"><?= $nav_order_badge ?></span>
                <?php endif; ?>
                <!-- hidden span kept for JS polling updates -->
                <span class="rate-order" style="display:none;"><?= $rate_count ?></span>
            </a>

            <?php if ($has_manage): ?>
            <!-- Manage Panel -->
            <div class="agri-dd-wrap">
                <button class="agri-manage-btn" onclick="agriToggleDd('ddManage')">
                    <i class="fa fa-th-large"></i> Manage
                    <i class="fa fa-chevron-down" style="font-size:10px;"></i>
                </button>
                <div class="agri-dd-menu" id="ddManage">

                    <?php if ($role_lvl === 3): ?>
                    <div class="agri-dd-section">
                        <div class="agri-dd-label">Admin</div>
                        <a href="<?= base_url('useradmin/Dashboard') ?>" class="agri-dd-item">
                            <i class="fa fa-chart-line"></i> Dashboard
                        </a>
                        <a href="<?= base_url('useradmin/Users') ?>" class="agri-dd-item">
                            <i class="fa fa-users"></i> Users
                        </a>
                        <a href="<?= base_url('useradmin/Reports') ?>" class="agri-dd-item">
                            <i class="fa fa-chart-bar"></i> Reports
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if ($is_farmer): ?>
                    <div class="agri-dd-section">
                        <div class="agri-dd-label">Farmer</div>
                        <a href="<?= base_url('userfarmer/Dashboard') ?>" class="agri-dd-item">
                            <i class="fa fa-chart-line"></i> Dashboard
                        </a>
                        <a href="<?= base_url('userfarmer/FarmProduce') ?>" class="agri-dd-item">
                            <i class="fa fa-leaf"></i> Farm Produce
                        </a>
                        <a href="<?= base_url('userfarmer/OnProduction') ?>" class="agri-dd-item">
                            <i class="fa fa-seedling"></i> On Production
                        </a>
                        <a href="<?= base_url('userfarmer/Orders') ?>" class="agri-dd-item">
                            <i class="fa fa-shopping-basket"></i> Client Orders
                            <?php if ($s->agrishop_reserved_trans_count > 0): ?>
                            <span class="badge badge-info ml-auto" style="font-size:10px;"><?= $s->agrishop_reserved_trans_count ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="<?= base_url('userfarmer/Billing') ?>" class="agri-dd-item">
                            <i class="fa fa-credit-card"></i> Billing
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if ($is_supplier): ?>
                    <div class="agri-dd-section">
                        <div class="agri-dd-label">Supplier</div>
                        <a href="<?= base_url('usersupplier/Dashboard') ?>" class="agri-dd-item">
                            <i class="fa fa-chart-line"></i> Dashboard
                        </a>
                        <a href="<?= base_url('usersupplier/Supplies') ?>" class="agri-dd-item">
                            <i class="fa fa-boxes"></i> Inventory
                        </a>
                        <a href="<?= base_url('usersupplier/Orders') ?>" class="agri-dd-item">
                            <i class="fa fa-shopping-basket"></i> Supply Orders
                        </a>
                        <a href="<?= base_url('usersupplier/Billing') ?>" class="agri-dd-item">
                            <i class="fa fa-credit-card"></i> Billing
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if ($is_farmer || $is_supplier): ?>
                    <div class="agri-dd-section">
                        <div class="agri-dd-label">Account</div>
                        <a href="#" class="agri-dd-item" data-toggle="modal" data-target="#profileModal">
                            <i class="fa fa-user-circle"></i> My Profile
                        </a>
                        <a href="<?= base_url('index') ?>" class="agri-dd-item">
                            <i class="fa fa-box"></i> My Orders
                        </a>
                        <a href="<?= base_url('logout') ?>" class="agri-dd-item" style="color:#ef4444;">
                            <i class="fa fa-sign-out-alt" style="color:#ef4444;"></i> Logout
                        </a>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
            <?php endif; ?>

            <!-- Profile -->
            <div class="agri-dd-wrap">
                <button class="agri-nav-btn" onclick="agriToggleDd('ddProfile')" style="padding:4px 8px;">
                    <img src="<?= $nav_img ?>" class="agri-avatar" alt="Profile">
                </button>
                <div class="agri-dd-menu" id="ddProfile" style="min-width:200px;">
                    <div class="agri-profile-head">
                        <img src="<?= $nav_img ?>" alt="Profile">
                        <div>
                            <div class="name"><?= htmlspecialchars($nav_name) ?></div>
                            <div class="role"><?= $role_label ?></div>
                        </div>
                    </div>
                    <div class="agri-dd-section">
                        <a href="#" class="agri-dd-item" data-toggle="modal" data-target="#profileModal">
                            <i class="fa fa-user-edit"></i> My Profile
                        </a>
                        <?php if ($is_farmer || $is_supplier): ?>
                        <a href="#" class="agri-dd-item" data-toggle="modal" data-target="#gcashModal">
                            <img src="<?= base_url('dist/img/credit/gcash_50x50.png') ?>" style="width:16px;height:16px;object-fit:contain;" alt="GCash"> My GCash
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="agri-dd-section">
                        <a href="<?= base_url('logout') ?>" class="agri-dd-item" style="color:#ef4444;">
                            <i class="fa fa-sign-out-alt" style="color:#ef4444;"></i> Logout
                        </a>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Guest -->
            <a href="<?= base_url('login') ?>" class="agri-btn-ghost">
                <i class="fa fa-sign-in-alt mr-1"></i> Login
            </a>
            <a href="<?= base_url('signup') ?>" class="agri-btn-solid">
                Sign Up
            </a>
        <?php endif; ?>
    </div>
</nav>

<script>
/* ── Navbar dropdown toggle ── */
function agriToggleDd(id) {
    var all = document.querySelectorAll('.agri-dd-menu');
    all.forEach(function(el) {
        if (el.id !== id) el.classList.remove('open');
    });
    document.getElementById(id).classList.toggle('open');
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('.agri-dd-wrap')) {
        document.querySelectorAll('.agri-dd-menu').forEach(function(el) {
            el.classList.remove('open');
        });
    }
});

</script>
