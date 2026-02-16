<style>
    /* ===== INITIAL HIDDEN STATE ===== */
    .animate-on-load {
        opacity: 0;
        transform: translateY(40px);
    }

    /* ===== FADE + SLIDE UP ===== */
    .fade-slide-up {
        animation: fadeSlideUp 1s ease forwards;
    }

    @keyframes fadeSlideUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ===== POP EFFECT ===== */
    .pop-in {
        animation: popIn .6s ease forwards;
    }

    @keyframes popIn {
        0% {
            opacity: 0;
            transform: scale(.8);
        }

        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* ===== STAGGER DELAYS ===== */
    .delay-1 {
        animation-delay: .2s;
    }

    .delay-2 {
        animation-delay: .4s;
    }

    .delay-3 {
        animation-delay: .6s;
    }

    /* ===== HOVER CARD EFFECT ===== */
    .card {
        transition: .3s ease;
    }

    .card:hover {
        transform: translateY(-6px);
    }

    /* optional smoother page feel */
    body {
        scroll-behavior: smooth;
    }
</style>

<div id="landing_Page" class="">
    <section style="background-image: url('<?php echo base_url(); ?>dist/layout_shop/images/banner-1.jpg');background-repeat: no-repeat;background-size: cover;">
        <div class="container-lg">
            <div class="row">
                <div class="col-lg-6 pt-5 mt-5">
                    <h2 class="display-3 ls-4 animate-on-load hero-title"><span class="fw-bold" style="color: #6aad51ff;">Farm-Fresh</span> Goodness Delivered <span class="fw-bold" style="color: #6bb252;">Today</span></h2>
                    <p class="fs-4 animate-on-load hero-sub">Order now to lock in peak freshness before it’s gone.</p>
                    <div class="d-flex gap-3 animate-on-load hero-btn">
                        <a href="#" class="btn bg-orange text-uppercase fs-6 rounded-pill px-4 py-3 mt-3" style="color: #fff !important;">Start Shopping</a>
                        <?php if (!$this->session->agrishop_login_id) { ?>
                            <a href="<?= base_url() ?>signup" class="btn btn-dark text-uppercase fs-6 rounded-pill px-4 py-3 mt-3">Sign Up Now</a>
                        <?php } ?>
                    </div>
                    <div class="row my-5">
                        <div class="col">
                            <div class="row text-dark">
                                <div class="col-auto">
                                    <p class="fs-1 fw-bold lh-sm mb-0 counter">14k+</p>
                                </div>
                                <div class="col">
                                    <p class="text-uppercase lh-sm mb-0">Product Varieties</p>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="row text-dark">
                                <div class="col-auto">
                                    <p class="fs-1 fw-bold lh-sm mb-0 counter">50k+</p>
                                </div>
                                <div class="col">
                                    <p class="text-uppercase lh-sm mb-0">Happy Customers</p>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="row text-dark">
                                <div class="col-auto">
                                    <p class="fs-1 fw-bold lh-sm mb-0 counter">10+</p>
                                </div>
                                <div class="col">
                                    <p class="text-uppercase lh-sm mb-0">Store Locations</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-sm-3 row-cols-lg-3 g-0 justify-content-center">
                <div class="col">
                    <div class="card border-0 bg-success rounded-0 p-4 text-light animate-on-load feature-card delay-1">
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <svg width="60" height="60">
                                    <use xlink:href="#fresh"></use>
                                </svg>
                            </div>
                            <div class="col-md-9">
                                <div class="card-body p-0">
                                    <h5 class="text-light">Fresh from farm</h5>
                                    <!-- <p class="card-text">Our produce travels hours, not weeks, guaranteeing you the absolute peak of flavor, texture, and nutritional value.</p> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-0 bg-secondary rounded-0 p-4 text-light animate-on-load feature-card delay-2">
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <svg width="60" height="60">
                                    <use xlink:href="#organic"></use>
                                </svg>
                            </div>
                            <div class="col-md-9">
                                <div class="card-body p-0">
                                    <h5 class="text-light">100% Organic</h5>
                                    <!-- <p class="card-text">Zero synthetic pesticides. Rest easy knowing you are feeding your family the purest, most transparently grown ingredients possible.</p> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-0 bg-orange rounded-0 p-4 text-light animate-on-load feature-card delay-3">
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <svg width="60" height="60">
                                    <use xlink:href="#delivery"></use>
                                </svg>
                            </div>
                            <div class="col-md-9">
                                <div class="card-body p-0">
                                    <h5 class="text-light">Free delivery</h5>
                                    <!-- <p class="card-text">Enjoy the convenience of farm-fresh quality delivered right to your kitchen, with no delivery fees on all qualifying orders.</p> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <section class="py-5 overflow-hidden">

        <div class="container-lg">

            <div class="row">
                <div class="col-md-12">

                    <div class="section-header d-flex flex-wrap justify-content-between mb-5">
                        <h2 class="section-title">Farmer Performance Leaderboard</h2>

                        <div class="d-flex align-items-center">
                            <a href="#" class="btn btn-success me-2">View All</a>
                            <div class="swiper-buttons">
                                <button class="swiper-prev category-carousel-prev btn btn-yellow">❮</button>
                                <button class="swiper-next category-carousel-next btn btn-yellow">❯</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="row">
                <div class="col-md-12">

                    <div class="category-carousel swiper animate-on-load farmers">
                        <div class="swiper-wrapper">
                            <a href="category.html" class="nav-link swiper-slide text-center">
                                <img src="<?php echo base_url(); ?>dist/img/media/person/3.jpg" width="165" height="165" class="rounded-circle" alt="Category Thumbnail">
                                <h4 class="fs-6 mt-3 fw-normal category-title">JUAN DELA CRUZ</h4>
                            </a>
                            <a href="category.html" class="nav-link swiper-slide text-center">
                                <img src="<?php echo base_url(); ?>dist/img/media/person/farmer_3.jpg" width="165" height="165" class="rounded-circle" alt="Category Thumbnail">
                                <h4 class="fs-6 mt-3 fw-normal category-title">ALFREDO ESPERANZA</h4>
                            </a>
                            <a href="category.html" class="nav-link swiper-slide text-center">
                                <img src="<?php echo base_url(); ?>dist/img/media/person/farmer_4.jpg" width="165" height="165" class="rounded-circle" alt="Category Thumbnail">
                                <h4 class="fs-6 mt-3 fw-normal category-title">MARCELO KALAW</h4>
                            </a>
                            <a href="category.html" class="nav-link swiper-slide text-center">
                                <img src="<?php echo base_url(); ?>dist/img/media/person/farmer_5.jpg" width="165" height="165" class="rounded-circle" alt="Category Thumbnail">
                                <h4 class="fs-6 mt-3 fw-normal category-title">DANIELO PLAZA</h4>
                            </a>
                            <a href="category.html" class="nav-link swiper-slide text-center">
                                <img src="<?php echo base_url(); ?>dist/img/media/person/farmer_6.jpg" width="165" height="165" class="rounded-circle" alt="Category Thumbnail">
                                <h4 class="fs-6 mt-3 fw-normal category-title">MEGALO GARCIA</h4>
                            </a>
                            <a href="category.html" class="nav-link swiper-slide text-center">
                                <img src="<?php echo base_url(); ?>dist/img/media/person/farmer_7.jpg" width="165" height="165" class="rounded-circle" alt="Category Thumbnail">
                                <h4 class="fs-6 mt-3 fw-normal category-title">EMILDA SANTOS</h4>
                            </a>
                            <a href="category.html" class="nav-link swiper-slide text-center">
                                <img src="<?php echo base_url(); ?>dist/img/media/person/farmer_8.jpg" width="165" height="165" class="rounded-circle" alt="Category Thumbnail">
                                <h4 class="fs-6 mt-3 fw-normal category-title">ARTURO MENDOZA</h4>
                            </a>
                            <a href="category.html" class="nav-link swiper-slide text-center">
                                <img src="<?php echo base_url(); ?>dist/img/media/person/farmer_9.jpg" width="165" height="165" class="rounded-circle" alt="Category Thumbnail">
                                <h4 class="fs-6 mt-3 fw-normal category-title">CARLO SANTIANA</h4>
                            </a>
                            <a href="category.html" class="nav-link swiper-slide text-center">
                                <img src="<?php echo base_url(); ?>dist/img/media/person/farmer_10.jpg" width="165" height="165" class="rounded-circle" alt="Category Thumbnail">
                                <h4 class="fs-6 mt-3 fw-normal category-title">JACOBO BARCENAS</h4>
                            </a>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

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

<script>
$(document).ready(function(){

    /* HERO ANIMATION */
    setTimeout(()=> $('.hero-title').addClass('fade-slide-up'), 200);
    setTimeout(()=> $('.hero-sub').addClass('fade-slide-up'), 500);
    setTimeout(()=> $('.hero-btn').addClass('pop-in'), 800);

    /* FEATURE CARDS */
    setTimeout(()=> $('.feature-card').addClass('fade-slide-up'), 1000);

    /* FARMERS SECTION */
    setTimeout(()=> $('.farmers').addClass('fade-slide-up'), 1400);

    /* COUNT UP NUMBERS */
    $('.counter').each(function(){
        let $this = $(this);
        let text = $this.text();
        let number = parseInt(text.replace(/\D/g,''));

        $({count:0}).animate({count:number},{
            duration:1500,
            easing:'swing',
            step:function(){
                $this.text(Math.floor(this.count) + '+');
            },
            complete:function(){
                $this.text(number + '+');
            }
        });
    });

});
</script>