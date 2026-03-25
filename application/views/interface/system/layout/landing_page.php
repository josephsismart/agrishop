<?php
// Variables passed from Index controller:
// $top_farmers   = array of real farmers from DB
// $active_promos = array of active promo_discount records
// $live_stats    = ['farmers', 'users', 'products', 'orders']
$top_farmers   = $top_farmers   ?? [];
$active_promos = $active_promos ?? [];
$live_stats    = $live_stats    ?? ['farmers'=>0,'users'=>0,'products'=>0,'orders'=>0];
?>

<style>
/* ── Animations ──────────────────────────────────────────── */
.animate-on-load { opacity:0; transform:translateY(40px); }
.fade-slide-up   { animation: fadeSlideUp 1s ease forwards; }
.pop-in          { animation: popIn .6s ease forwards; }
@keyframes fadeSlideUp { to { opacity:1; transform:translateY(0); } }
@keyframes popIn { 0%{opacity:0;transform:scale(.8)} 100%{opacity:1;transform:scale(1)} }
.delay-1 { animation-delay:.2s; } .delay-2 { animation-delay:.4s; } .delay-3 { animation-delay:.6s; }
body { scroll-behavior: smooth; }

/* ── Section headings ────────────────────────────────────── */
.section-pill {
    display: inline-block;
    background: #f0fdf4;
    color: #15803d;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    padding: 4px 14px;
    border-radius: 20px;
    border: 1px solid #bbf7d0;
    margin-bottom: 10px;
}

/* ── Promo cards ─────────────────────────────────────────── */
.promo-card {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 14px rgba(0,0,0,.08);
    transition: transform .2s, box-shadow .2s;
    background: #fff;
    border: none;
    height: 100%;
}
.promo-card:hover { transform: translateY(-6px); box-shadow: 0 8px 28px rgba(0,0,0,.14); }
.promo-img { width:100%; height:170px; object-fit:cover; }
.promo-badge {
    position: absolute; top: 12px; left: 12px;
    background: #ef4444; color: #fff;
    font-size: 12px; font-weight: 800;
    padding: 4px 10px; border-radius: 20px;
    box-shadow: 0 2px 8px rgba(239,68,68,.4);
}
.promo-timer { font-size: 11px; color: #f59e0b; font-weight: 700; }
.price-original { text-decoration: line-through; color: #9ca3af; font-size: 13px; }
.price-discounted { font-size: 20px; font-weight: 800; color: #059669; }

/* ── Top farmer cards ────────────────────────────────────── */
.farmer-card {
    border-radius: 16px;
    padding: 20px 16px;
    text-align: center;
    background: #fff;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
    transition: transform .2s, box-shadow .2s;
    border: none;
    height: 100%;
}
.farmer-card:hover { transform: translateY(-5px); box-shadow: 0 8px 24px rgba(0,0,0,.13); }
.farmer-rank {
    position: absolute; top: -8px; left: 50%; transform: translateX(-50%);
    width: 26px; height: 26px; border-radius: 50%;
    font-size: 12px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid #fff;
}
.rank-1 { background: #fbbf24; color: #78350f; }
.rank-2 { background: #9ca3af; color: #fff; }
.rank-3 { background: #b45309; color: #fff; }
.rank-other { background: #059669; color: #fff; }
.farmer-avatar {
    width: 80px; height: 80px; border-radius: 50%;
    object-fit: cover; border: 3px solid #d1fae5;
    margin: 0 auto 10px;
}
.farmer-revenue { font-size: 13px; color: #059669; font-weight: 700; }
.farmer-orders  { font-size: 11px; color: #9ca3af; }

/* ── Search bar ──────────────────────────────────────────── */
.hero-search {
    background: #fff;
    border-radius: 50px;
    padding: 6px 6px 6px 20px;
    display: flex; align-items: center;
    box-shadow: 0 4px 20px rgba(0,0,0,.15);
    max-width: 520px;
    margin-top: 20px;
}
.hero-search input {
    flex: 1; border: none; outline: none;
    font-size: 14px; background: transparent;
    color: #111;
}
.hero-search input::placeholder { color: #9ca3af; }
.hero-search button {
    background: #059669; color: #fff;
    border: none; border-radius: 50px;
    padding: 8px 22px; font-weight: 700;
    font-size: 13px; cursor: pointer;
    transition: background .2s;
}
.hero-search button:hover { background: #047857; }

/* ── Stats bar ───────────────────────────────────────────── */
.stat-item { text-align: center; }
.stat-num  { font-size: 28px; font-weight: 900; color: #111; line-height: 1; }
.stat-lbl  { font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .05em; }

/* ── Why shop section ────────────────────────────────────── */
.why-card {
    border-radius: 16px; padding: 28px 20px; text-align: center;
    background: #fff; box-shadow: 0 2px 12px rgba(0,0,0,.06);
    transition: transform .2s;
}
.why-card:hover { transform: translateY(-4px); }
.why-icon {
    width: 60px; height: 60px; border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 26px; margin: 0 auto 14px;
}

/* ── No data state ───────────────────────────────────────── */
.empty-state {
    text-align: center; padding: 40px 20px;
    color: #9ca3af; font-size: 14px;
}
</style>

<div id="landing_Page">

<!-- ══════════════════════════════════════════════════════════
     HERO SECTION
══════════════════════════════════════════════════════════ -->
<section style="background-image:url('<?= base_url() ?>dist/layout_shop/images/banner-1.jpg');
    background-repeat:no-repeat;background-size:cover;min-height:520px;">
    <div class="container-lg">
        <div class="row">
            <div class="col-lg-7 pt-5 mt-4 pb-5">
                <div class="section-pill animate-on-load hero-title" style="background:#d1fae5;border-color:#6ee7b7;">
                    🌱 Farm-to-Table Marketplace
                </div>
                <h2 class="display-3 ls-4 animate-on-load hero-title" style="animation-delay:.1s;">
                    <span style="color:#6aad51;font-weight:900;">Farm-Fresh</span> Goodness<br>
                    Delivered <span style="color:#6bb252;font-weight:900;">Today</span>
                </h2>
                <p class="fs-5 animate-on-load hero-sub text-dark" style="animation-delay:.2s;">
                    Direct from local Caraga farmers. No middlemen. Peak freshness guaranteed.
                </p>

                <!-- Search bar -->
                <div class="hero-search animate-on-load hero-btn" style="animation-delay:.35s;">
                    <i class="fa fa-search mr-2 text-muted"></i>
                    <input type="text" id="heroSearch" placeholder="Search produce, farms, or varieties..."
                           onkeydown="if(event.key==='Enter') doHeroSearch()">
                    <button onclick="doHeroSearch()">🔍 Search</button>
                </div>

                <div class="d-flex gap-3 animate-on-load hero-btn mt-3" style="animation-delay:.5s;">
                    <a href="#" class="btn bg-orange text-uppercase fs-6 rounded-pill px-4 py-2" style="color:#fff!important;">
                        🛒 Start Shopping
                    </a>
                    <?php if (!$this->session->agrishop_login_id): ?>
                    <a href="<?= base_url() ?>signup" class="btn btn-dark text-uppercase fs-6 rounded-pill px-4 py-2">
                        Join Now — It's Free
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Live stats -->
                <div class="row my-4 animate-on-load" style="animation-delay:.6s;max-width:480px;">
                    <div class="col stat-item">
                        <div class="stat-num counter-real"><?= $live_stats['farmers'] ?></div>
                        <div class="stat-lbl">Active Farmers</div>
                    </div>
                    <div class="col stat-item">
                        <div class="stat-num counter-real"><?= $live_stats['products'] ?></div>
                        <div class="stat-lbl">Produce Types</div>
                    </div>
                    <div class="col stat-item">
                        <div class="stat-num counter-real"><?= $live_stats['orders'] ?></div>
                        <div class="stat-lbl">Orders Fulfilled</div>
                    </div>
                    <div class="col stat-item">
                        <div class="stat-num counter-real"><?= $live_stats['users'] ?></div>
                        <div class="stat-lbl">Happy Members</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feature strip -->
        <div class="row row-cols-1 row-cols-sm-3 row-cols-lg-3 g-0 justify-content-center">
            <div class="col">
                <div class="card border-0 bg-success rounded-0 p-4 text-light animate-on-load feature-card delay-1">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <svg width="60" height="60"><use xlink:href="#fresh"></use></svg>
                        </div>
                        <div class="col-md-9">
                            <div class="card-body p-0">
                                <h5 class="text-light">Fresh from Farm</h5>
                                <p class="mb-0" style="font-size:13px;opacity:.85;">Harvested and delivered same day.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 bg-secondary rounded-0 p-4 text-light animate-on-load feature-card delay-2">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <svg width="60" height="60"><use xlink:href="#organic"></use></svg>
                        </div>
                        <div class="col-md-9">
                            <div class="card-body p-0">
                                <h5 class="text-light">100% Organic</h5>
                                <p class="mb-0" style="font-size:13px;opacity:.85;">Zero synthetic pesticides. Pure &amp; natural.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 bg-orange rounded-0 p-4 text-light animate-on-load feature-card delay-3">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <svg width="60" height="60"><use xlink:href="#delivery"></use></svg>
                        </div>
                        <div class="col-md-9">
                            <div class="card-body p-0">
                                <h5 class="text-light">Direct from Farmer</h5>
                                <p class="mb-0" style="font-size:13px;opacity:.85;">Support local, pay fair prices.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ══════════════════════════════════════════════════════════
     🔥 PROMO & DISCOUNTS SECTION
══════════════════════════════════════════════════════════ -->
<?php if (!empty($active_promos)): ?>
<section class="py-5" style="background:linear-gradient(135deg,#fff7ed,#fef3c7);">
    <div class="container-lg">
        <div class="text-center mb-4">
            <div class="section-pill" style="background:#fef3c7;color:#b45309;border-color:#fde68a;">
                🔥 Hot Deals This Week
            </div>
            <h2 class="section-title mb-1">Farmer Promos &amp; Discounts</h2>
            <p class="text-muted">Limited-time offers directly from our local farmers</p>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3">
            <?php foreach ($active_promos as $i => $promo): ?>
            <?php
                $img     = !empty($promo['img_path']) ? base_url($promo['img_path']) : base_url('dist/img/media/icons/1x1.png');
                $f_img   = !empty($promo['farmer_img']) ? base_url($promo['farmer_img']) : base_url('dist/img/media/icons/1x1.png');
                $days    = (int) ceil((strtotime($promo['valid_until']) - time()) / 86400);
                $pct     = $promo['discount_percent'];
            ?>
            <div class="col animate-on-load promo-col" style="animation-delay:<?= $i * 0.08 ?>s;">
                <div class="promo-card position-relative">
                    <div style="position:relative;overflow:hidden;">
                        <img src="<?= $img ?>" class="promo-img"
                             onerror="this.src='<?= base_url('dist/img/media/icons/1x1.png') ?>'">
                        <div class="promo-badge"><?= $pct ?>% OFF</div>
                        <?php if ($days <= 3): ?>
                        <div style="position:absolute;top:12px;right:12px;background:#ef4444;color:#fff;
                            font-size:10px;font-weight:700;padding:3px 8px;border-radius:20px;">
                            ⚡ <?= $days <= 0 ? 'Last Day!' : $days . 'd left' ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div style="padding:14px;">
                        <div style="font-size:10px;font-weight:700;color:#059669;text-transform:uppercase;
                            letter-spacing:.05em;margin-bottom:3px;">
                            <?= htmlspecialchars($promo['produce_name'] ?? 'Farm Produce') ?>
                        </div>
                        <div style="font-size:14px;font-weight:700;color:#111;line-height:1.3;margin-bottom:6px;">
                            <?= htmlspecialchars($promo['title']) ?>
                        </div>
                        <?php if (!empty($promo['description'])): ?>
                        <div style="font-size:12px;color:#6b7280;margin-bottom:8px;line-height:1.4;">
                            <?= htmlspecialchars(substr($promo['description'],0,60)) ?>...
                        </div>
                        <?php endif; ?>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <div class="price-original">₱<?= number_format($promo['original_price'],2) ?></div>
                                <div class="price-discounted">₱<?= number_format($promo['discounted_price'],2) ?></div>
                            </div>
                            <div style="text-align:right;">
                                <img src="<?= $f_img ?>" width="30" height="30"
                                     style="border-radius:50%;object-fit:cover;border:2px solid #d1fae5;"
                                     onerror="this.src='<?= base_url('dist/img/media/icons/1x1.png') ?>'">
                                <div style="font-size:10px;color:#6b7280;margin-top:2px;">
                                    <?= htmlspecialchars(explode(' ', $promo['farmer_name'])[0]) ?>
                                </div>
                            </div>
                        </div>
                        <div class="promo-timer">⏰ Valid until <?= date('M d', strtotime($promo['valid_until'])) ?></div>
                        <a href="#" class="btn btn-success btn-block btn-sm mt-2 rounded-pill font-weight-bold"
                           onclick="openPromoCart(<?= $promo['id'] ?>, <?= htmlspecialchars(json_encode($promo), ENT_QUOTES) ?>); return false;">
                            🛒 Shop Now
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ══════════════════════════════════════════════════════════
     🏆 TOP FARMERS LEADERBOARD
══════════════════════════════════════════════════════════ -->
<section class="py-5" style="background:#f0fdf4;">
    <div class="container-lg">
        <div class="text-center mb-4">
            <div class="section-pill">🏆 Farmer Spotlight</div>
            <h2 class="section-title mb-1">Top Performing Farmers</h2>
            <p class="text-muted">Honoring the hardworking farmers powering our community</p>
        </div>

        <?php if (!empty($top_farmers)): ?>
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-3">
            <?php foreach ($top_farmers as $rank => $farmer): ?>
            <?php
                $f_img = (!empty($farmer['img_path']) && file_exists(FCPATH . $farmer['img_path']))
                    ? base_url($farmer['img_path'])
                    : base_url('dist/img/media/icons/1x1.png');
                $rClass = $rank === 0 ? 'rank-1' : ($rank === 1 ? 'rank-2' : ($rank === 2 ? 'rank-3' : 'rank-other'));
                $medals = ['🥇','🥈','🥉'];
                $medal  = $medals[$rank] ?? '🌱';
            ?>
            <div class="col animate-on-load farmers" style="animation-delay:<?= $rank * 0.07 ?>s;">
                <div class="farmer-card position-relative">
                    <div class="farmer-rank <?= $rClass ?>"><?= $rank + 1 ?></div>
                    <div class="text-center" style="font-size:20px;margin-bottom:6px;"><?= $medal ?></div>
                    <img src="<?= $f_img ?>" class="farmer-avatar d-block"
                         onerror="this.src='<?= base_url('dist/img/media/icons/1x1.png') ?>'">
                    <div style="font-weight:700;font-size:13px;color:#111;line-height:1.3;">
                        <?= htmlspecialchars($farmer['farmer_name']) ?>
                    </div>
                    <?php if (!empty($farmer['barangay'])): ?>
                    <div style="font-size:11px;color:#9ca3af;margin-top:2px;">
                        <i class="fa fa-map-marker-alt mr-1"></i><?= htmlspecialchars($farmer['barangay']) ?>
                    </div>
                    <?php endif; ?>
                    <div class="farmer-revenue mt-2">
                        ₱<?= number_format($farmer['total_revenue'], 0) ?>
                        <span style="font-size:10px;color:#9ca3af;font-weight:400;"> earned</span>
                    </div>
                    <div class="farmer-orders"><?= $farmer['orders_count'] ?> orders</div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <i class="fa fa-tractor fa-3x mb-3 text-success"></i>
            <p>Farmers will appear here once they start completing orders.</p>
        </div>
        <?php endif; ?>
    </div>
</section>


<!-- ══════════════════════════════════════════════════════════
     💡 WHY CHOOSE AGRISHOP
══════════════════════════════════════════════════════════ -->
<section class="py-5" style="background:#fff;">
    <div class="container-lg">
        <div class="text-center mb-4">
            <div class="section-pill">💡 Why AgriShop</div>
            <h2 class="section-title mb-1">The Smarter Way to Buy Fresh</h2>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
            <?php
            $whys = [
                ['icon'=>'🌾','bg'=>'#d1fae5','title'=>'Farm Direct','desc'=>'Buy straight from verified local farmers. No middlemen, no markups.'],
                ['icon'=>'🔍','bg'=>'#dbeafe','title'=>'Full Transparency','desc'=>'See which farm grew your food, where it is, and track production.'],
                ['icon'=>'💰','bg'=>'#fef3c7','title'=>'Best Prices','desc'=>'Direct sourcing means better prices for buyers, better income for farmers.'],
                ['icon'=>'📍','bg'=>'#fce7f3','title'=>'Caraga Local','desc'=>'Supporting Caraga region farmers and boosting local agriculture.'],
            ];
            foreach ($whys as $w):
            ?>
            <div class="col animate-on-load feature-card">
                <div class="why-card">
                    <div class="why-icon" style="background:<?= $w['bg'] ?>;">
                        <?= $w['icon'] ?>
                    </div>
                    <h6 class="font-weight-bold mb-2"><?= $w['title'] ?></h6>
                    <p class="text-muted mb-0" style="font-size:13px;"><?= $w['desc'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ══════════════════════════════════════════════════════════
     📣 FARMER CTA BANNER
══════════════════════════════════════════════════════════ -->
<?php if (!$this->session->agrishop_login_id): ?>
<section class="py-5" style="background:linear-gradient(135deg,#059669,#10b981);">
    <div class="container-lg text-center text-white">
        <h2 class="font-weight-bold mb-2">Are You a Farmer?</h2>
        <p class="mb-4" style="opacity:.9;font-size:16px;">
            Join AgriShop and sell your produce directly to buyers in your area.
            Get 2 months free, post promos, track orders, and grow your income.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="<?= base_url() ?>signup" class="btn btn-light font-weight-bold rounded-pill px-5 py-3"
               style="color:#059669;font-size:15px;">
                🌾 Register as Farmer — Free
            </a>
            <a href="<?= base_url() ?>signup" class="btn btn-outline-light rounded-pill px-5 py-3"
               style="font-size:15px;">
                🏪 Register as Supplier
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<footer class="py-5">
        <div class="container-lg">
            <div class="row">

                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer-menu">
                        <img src="<?php echo base_url(); ?>dist/layout_shop/images/logo.svg" width="240" height="70" alt="logo">
                        <div class="social-links mt-3">
                            <ul class="d-flex list-unstyled gap-2">
                                <li>
                                    <a href="#" class="btn btn-outline-light">
                                        <svg width="16" height="16">
                                            <use xlink:href="#facebook"></use>
                                        </svg>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="btn btn-outline-light">
                                        <svg width="16" height="16">
                                            <use xlink:href="#twitter"></use>
                                        </svg>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="btn btn-outline-light">
                                        <svg width="16" height="16">
                                            <use xlink:href="#youtube"></use>
                                        </svg>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="btn btn-outline-light">
                                        <svg width="16" height="16">
                                            <use xlink:href="#instagram"></use>
                                        </svg>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="btn btn-outline-light">
                                        <svg width="16" height="16">
                                            <use xlink:href="#amazon"></use>
                                        </svg>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <div class="footer-menu">
                        <h5 class="widget-title">Organic</h5>
                        <ul class="menu-list list-unstyled">
                            <li class="menu-item">
                                <a href="#" class="nav-link">About us</a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="nav-link">Conditions </a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="nav-link">Our Journals</a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="nav-link">Careers</a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="nav-link">Affiliate Programme</a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="nav-link">Ultras Press</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="footer-menu">
                        <h5 class="widget-title">Quick Links</h5>
                        <ul class="menu-list list-unstyled">
                            <li class="menu-item">
                                <a href="#" class="nav-link">Offers</a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="nav-link">Discount Coupons</a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="nav-link">Stores</a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="nav-link">Track Order</a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="nav-link">Shop</a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="nav-link">Info</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="footer-menu">
                        <h5 class="widget-title">Customer Service</h5>
                        <ul class="menu-list list-unstyled">
                            <li class="menu-item">
                                <a href="#" class="nav-link">FAQ</a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="nav-link">Contact</a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="nav-link">Privacy Policy</a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="nav-link">Returns & Refunds</a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="nav-link">Cookie Guidelines</a>
                            </li>
                            <li class="menu-item">
                                <a href="#" class="nav-link">Delivery Information</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="footer-menu">
                        <h5 class="widget-title">Subscribe Us</h5>
                        <p>Subscribe to our newsletter to get updates about our grand offers.</p>
                        <form class="d-flex mt-3 gap-0" action="index.html">
                            <input class="form-control rounded-start rounded-0 bg-light" type="email" placeholder="Email Address" aria-label="Email Address">
                            <button class="btn btn-dark rounded-end rounded-0" type="submit">Subscribe</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </footer>
    <div id="footer-bottom">
        <div class="container-lg">
            <div class="row">
                <div class="col-md-6 copyright">
                    <p>© 2025 Agrishop. All rights reserved.</p>
                </div>
                <!-- <div class="col-md-6 credit-link text-start text-md-end">
                    <p>HTML Template by <a href="https://templatesjungle.com/">TemplatesJungle</a> Distributed By <a href="https://themewagon.com">ThemeWagon</a> </p>
                </div> -->
            </div>
        </div>
    </div>
</div>

</div><!-- end landing_Page -->

<script>
$(document).ready(function() {
    /* Hero animation */
    setTimeout(function(){ $('.hero-title').addClass('fade-slide-up'); }, 200);
    setTimeout(function(){ $('.hero-sub').addClass('fade-slide-up'); }, 500);
    setTimeout(function(){ $('.hero-btn').addClass('pop-in'); }, 800);
    setTimeout(function(){ $('.feature-card').addClass('fade-slide-up'); }, 1000);
    setTimeout(function(){ $('.farmers').addClass('fade-slide-up'); }, 1400);
    /* PROMO CARDS */
    setTimeout(function(){ $('.promo-col').addClass('fade-slide-up'); }, 900);
});

function doHeroSearch() {
    var q = $('#heroSearch').val().trim();
    if (!q) return;
    <?php if ($this->session->agrishop_login_id): ?>
    $('#searchProductionProduce').val(q);
    if (typeof searchProductionMap === 'function') {
        $('#modalSearchProduction').modal('show');
        setTimeout(searchProductionMap, 400);
    }
    <?php else: ?>
    window.location.href = '<?= base_url() ?>login';
    <?php endif; ?>
}

function openPromoCart(promoId, promo) {
    <?php if (!$this->session->agrishop_login_id): ?>
    window.location.href = '<?= base_url() ?>login';
    return;
    <?php endif; ?>

    // Populate the promo modal
    var img = promo.img_path ? '<?= base_url() ?>' + promo.img_path : '<?= base_url('dist/img/media/icons/1x1.png') ?>';
    $('#promoCartImg').attr('src', img);
    $('#promoCartTitle').text(promo.title);
    $('#promoCartDesc').text(promo.description || '');
    $('#promoCartProduce').text(promo.produce_name || '—');
    $('#promoCartOrigPrice').text('₱' + parseFloat(promo.original_price).toFixed(2));
    $('#promoCartDiscPrice').text('₱' + parseFloat(promo.discounted_price).toFixed(2));
    $('#promoCartBadge').text(promo.discount_percent + '% OFF');
    $('#promoCartUntil').text('Valid until ' + promo.valid_until);
    $('#promoCartQty').val(1);
    $('#promoCartQty').attr('max', promo.max_qty || 999);
    $('#btnAddPromoToCart').data('promo-id', promoId);
    $('#btnAddPromoToCart').data('price', promo.discounted_price);
    $('#modalPromoCart').modal('show');
}

$(document).on('click', '#btnAddPromoToCart', function() {
    var promoId = $(this).data('promo-id');
    var qty = parseInt($('#promoCartQty').val()) || 1;
    var price = parseFloat($(this).data('price'));

    $(this).prop('disabled', true).text('Adding...');
    var self = this;

    $.post('<?= base_url('userpublicmap/Map/add_promo_to_cart') ?>', {
        promo_id: promoId,
        qty: qty,
        price: price
    }, function(res) {
        var j = JSON.parse(res);
        $(self).prop('disabled', false).html('🛒 Add to Cart');
        if (j.success) {
            $('#modalPromoCart').modal('hide');
            if (typeof successAlert === 'function') successAlert(j.message);
            else alert(j.message);
            if (j.cart_pending !== undefined) $('.pending-order').text(j.cart_pending);
        } else {
            if (typeof failAlert === 'function') failAlert(j.message);
            else alert(j.message);
        }
    });
});
</script>
