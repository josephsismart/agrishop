<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $system_title ?></title>
    <link rel="icon" type="image/png" href="<?= $system_svg ?>">
    <!-- Google Font: Source Sans Pro -->
    <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"> -->
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="<?= base_url() ?>plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url() ?>dist/css/adminlte.min.css">
</head>

<style>
    .back-to-top {
        position: fixed;
        bottom: 25px;
        right: 25px;
        display: none;
    }
</style>

<body class="hold-transition layout-top-nav">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand-md navbar-light navbar-white">
            <div class="container">
                <a href="#" class="navbar-brand">
                    <img src="<?= $system_svg ?>" alt="ANHSIS Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                    <span class="brand-text font-weight-light">
                        <span class="d-sm-inline d-md-none"><b>ANHS</b></span>
                        <span class="d-none d-sm-inline"><b>A</b>gusan <b>N</b>ational <b>H</b>igh <b>S</b>chool</span>
                        <span class="badge bg-danger text-xs">BETA</span>
                    </span>
                </a>

                <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                    <!-- Left navbar links -->
                    <ul class="navbar-nav">
                        <!-- <li class="nav-item">
                            <a href="index3.html" class="nav-link">Home</a>
                        </li> -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">About</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">Faculty</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">Contact</a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url() ?>/login" class="nav-link text-primary"><b>Portal</b></a>
                        </li>
                    </ul>
                    <!-- SEARCH FORM -->
                </div>

            </div>
        </nav>
        <!-- /.navbar -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container">
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <h1 class="m-0"> Welcome to our webiste <small> Visitor!</small></h1>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->

            <div class="content">
                <div class="container">
                    <div class="bd-example mb-4">
                        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                            <ol class="carousel-indicators">
                                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                                <li data-target="#carouselExampleIndicators" data-slide-to="1" class=""></li>
                                <li data-target="#carouselExampleIndicators" data-slide-to="2" class=""></li>
                            </ol>
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img class="d-block w-100" src="<?= base_url() ?>dist/img/banners/b0_2024.jpg" alt="First slide">
                                    <!-- <div class="carousel-caption d-none d-md-block">
                                        <h5>SANHAY Moving Forward</h5>
                                        <h6>ANHS envisions to produce competent learners who are educationally, technologically, artistically equipped, God-fearing and law-abiding citizens of the country.</h6>
                                    </div> -->
                                </div>
                                <div class="carousel-item">
                                    <img class="d-block w-100" src="<?= base_url() ?>dist/img/banners/b1_2024.jpg" alt="Second slide">
                                </div>
                                <div class="carousel-item">
                                    <img class="d-block w-100" src="<?= base_url() ?>dist/img/banners/b2_2024.jpg" alt="Second slide">
                                </div>
                            </div>
                            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                                <span class="carousel-control-custom-icon" aria-hidden="true">
                                    <i class="fas fa-chevron-left"></i>
                                </span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                                <span class="carousel-control-custom-icon" aria-hidden="true">
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                                <span class="sr-only">Next</span>
                            </a>
                            <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                                <span class="carousel-control-custom-icon" aria-hidden="true">
                                    <i class="fas fa-chevron-left"></i>
                                </span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                                <span class="carousel-control-custom-icon" aria-hidden="true">
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>

                    <div class="row">


                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                            <div class="card card-primary card-outline">

                                <div class="card-body">
                                    <div class="row">
                                        <h5 class="card-title mb-2">School Head</h5>
                                        <div class="col-12"></div>
                                        <div class="col-12 text-center">
                                            <a href="http://ebeis.deped.gov.ph" target="_blank">
                                                <img class="img-fluid pad mb-3 img-circle" width="200" src="<?= base_url() ?>/dist/img/media/personnel/2023/5/4_5.jpg" alt="Photo">
                                            </a>
                                        </div>
                                        <div class="col-12 text-center">
                                            <h5>ELMER M. CATALUÑA</h5>
                                            <span class="float-center text-muted">PRINCIPAL IV</span>
                                            <p class="card-text text-justify font-italic font-weight-lighter mt-3">
                                                &emsp;&emsp;"Creativity is a powerful engine of success and
                                                development for our learners. We must nurture a mindset of creativity in our school to empower students with imagination, critical thinking, and resilience so they can create fresh and impactful solutions. To the devoted teachers of Agusan National High School, inspire wonder in our learners and give them the freedom to discover.
                                                Encourage collaboration and
                                                cooperation in your teaching, and guide them to see failure as a stepping stone toward wisdom and excellence."
                                            </p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>


                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                            <div class="card card-primary card-outline">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <h5 class="card-title mb-2">News</h5>
                                        </div>
                                        <div class="col-12 py-1">
                                            <div class="widget-user-header text-black py-5 my-2" style="background: url('<?= base_url() ?>/dist/img/news/n1.jpg') center center;">
                                                <div class="shadow bg-white py-1">
                                                    <div class="bg-gradient-white py-2 px-1">
                                                        <h5 class="widget-user-username text-right">SPFL-KOREAN Takers</h5>
                                                        <h6 class="text-right font-weight-lighter">Sed ut perspiciatis unde omnis iste natus sit voluptatem accusantium ...</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 py-1">
                                            <div class="widget-user-header text-black py-5 my-2" style="background: url('<?= base_url() ?>/dist/img/news/n2.jpg') center center;">
                                                <div class="shadow bg-white py-1">
                                                    <div class="bg-gradient-white py-2 px-1">
                                                        <h5 class="widget-user-username text-right">𝑪𝑶𝑵𝑮𝑹𝑨𝑻𝑼𝑳𝑨𝑻𝑰𝑶𝑵𝑺! 𝗔𝗕𝗥𝗔𝗠 𝗡𝗢𝗝𝗔 𝗔𝗥𝗘𝗟𝗟𝗔𝗡𝗢</h5>
                                                        <h6 class="text-right font-weight-lighter">Sed ut perspiciatis unde omnis iste natus sit voluptatem accusantium ...</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 py-1">
                                            <div class="widget-user-header py-5 my-2" style="background: url('<?= base_url() ?>/dist/img/news/n3.jpg') center center;">
                                                <div class="shadow bg-white py-1">
                                                    <div class="bg-gradient-white py-2 px-1">
                                                        <h5 class="widget-user-username text-right">𝐀𝐓𝐓𝐄𝐍𝐓𝐈𝐎𝐍! To All Incoming Grade 7 and Grade 11 Learners</h5>
                                                        <h6 class="text-right font-weight-lighter">Sed ut perspiciatis unde omnis iste natus sit voluptatem accusantium ...</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 py-1">
                                            <div class="widget-user-header py-5 my-2" style="background: url('<?= base_url() ?>/dist/img/news/n4.jpg') center center;">
                                                <div class="shadow bg-white py-1">
                                                    <div class="bg-gradient-white py-2 px-1">
                                                        <h5 class="widget-user-username text-right">Screening of Special Programs</h5>
                                                        <h6 class="text-right font-weight-lighter">Sed ut perspiciatis unde omnis iste natus sit voluptatem accusantium ...</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 py-1">
                                            <div class="widget-user-header py-5 my-2" style="background: url('<?= base_url() ?>/dist/img/news/n5.jpg') center center;">
                                                <div class="shadow bg-white py-1">
                                                    <div class="bg-gradient-white py-2 px-1">
                                                        <h5 class="widget-user-username text-right">Congratulations to our SSG President, Shayne Khietly Mellijor</h5>
                                                        <h6 class="text-right font-weight-lighter">Sed ut perspiciatis unde omnis iste natus sit voluptatem accusantium ...</h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 p-0 m-0">
                                        <h6 class="text-right">see more...</h6>
                                    </div>


                                </div>
                            </div><!-- /.card -->
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
                            <div class="card card-primary card-outline">
                                <div class="card-header border-0">
                                    <h3 class="card-title">Available Links</h3>
                                </div>
                                <div class="card-body table-responsive p-0" style="overflow:hidden">
                                    <div class="row text-center">
                                        <div class="col-12 p-1">
                                            <a href="http://lis.deped.gov.ph" target="_blank">
                                                <img src="<?= base_url() ?>/dist/img/linkages/lis.webp" alt="Product 1" height="50">
                                            </a>
                                        </div>
                                        <div class="col-12 p-1">
                                            <a href="http://ebeis.deped.gov.ph" target="_blank">
                                                <img src="<?= base_url() ?>/dist/img/linkages/ebeis.webp" alt="Product 1" height="50">
                                            </a>
                                        </div>
                                        <div class="col-12 p-1">
                                            <a href="http://deped.gov.ph" target="_blank">
                                                <img src="<?= base_url() ?>/dist/img/linkages/deped.webp" alt="Product 1" height="50">
                                            </a>
                                        </div>
                                        <div class="col-12 p-1">
                                            <a href="http://lrmds.deped.gov.ph/" target="_blank">
                                                <img src="<?= base_url() ?>/dist/img/linkages/lrp.webp" alt="Product 1" height="50">
                                            </a>
                                        </div>
                                        <div class="col-12 p-1">
                                            <a href="http://http://ehris.deped.gov.ph/" target="_blank">
                                                <img src="<?= base_url() ?>/dist/img/linkages/ehris.webp" alt="Product 1" height="50">
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card card-primary card-outline">
                                <div class="card-body">
                                    <h5 class="card-title mb-2">About</h5>

                                    <p class="card-text">
                                        Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?
                                    </p>
                                    <a href="#" class="card-link">See more</a>
                                    <!-- <a href="#" class="card-link">Card link</a>
                                    <a href="#" class="card-link">Another link</a> -->
                                </div>
                            </div><!-- /.card -->
                        </div>
                        <div class="col-12">
                            <div class="card card-primary card-outline">
                                <div class="card-body">
                                    <h5 class="card-title mb-2">Faculty</h5>

                                    <p class="card-text">
                                        Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?
                                    </p>
                                    <a href="#" class="card-link">See more</a>
                                    <!-- <a href="#" class="card-link">Card link</a>
                                    <a href="#" class="card-link">Another link</a> -->
                                </div>
                            </div><!-- /.card -->
                        </div>
                        <div class="col-12">
                            <div class="card card-primary card-outline">
                                <div class="card-body">
                                    <h5 class="card-title mb-2">Contact</h5>

                                    <p class="card-text">
                                        At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.
                                    </p>
                                    <a href="#" class="card-link">See more</a>
                                    <!-- <a href="#" class="card-link">Card link</a>
                                    <a href="#" class="card-link">Another link</a> -->
                                </div>
                            </div><!-- /.card -->
                        </div>
                        <!-- /.col-md-6 -->
                        <!-- <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title m-0">Featured</h5>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">Special title treatment</h6>

                                    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                                    <a href="#" class="btn btn-primary">Go somewhere</a>
                                </div>
                            </div>

                            <div class="card card-primary card-outline">
                                <div class="card-header">
                                    <h5 class="card-title m-0">Featured</h5>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title">Special title treatment</h6>

                                    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                                    <a href="#" class="btn btn-primary">Go somewhere</a>
                                </div>
                            </div>
                        </div> -->
                        <!-- /.col-md-6 -->
                    </div>
                    <!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <!-- To the right -->
            <div class="float-right d-none d-sm-inline text-white">
                ramsismar
            </div>
            <!-- Default to the left -->
            <strong>Copyright &copy; 2021-2022 <a href="https://lodis.herokuapp.com/lodis" target="_blank">ANHSIS App</a>.</strong> All rights reserved.
        </footer>
        <div id="gwt-standard-footer"><iframe src="//gwhs.i.gov.ph/gwt-footer/footer-source.html" id="footer-gwt-frame" width="100%" style="border: 0px; min-height: 13rem; margin-bottom: -3px;"></iframe></div>
    </div>
    <a id="back-to-top" href="#" class="btn btn-primary btn-lg back-to-top" role="button"><i class="fas fa-chevron-up"></i></a>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->

    <!-- jQuery -->
    <script src="<?= base_url() ?>plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?= base_url() ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?= base_url() ?>dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="<?= base_url() ?>dist/js/demo.js"></script>

    <script>
        $('.carousel').carousel({
            interval: 4100,
        });

        $(window).scroll(function() {
            if ($(this).scrollTop() > 50) {
                $('#back-to-top').fadeIn();
            } else {
                $('#back-to-top').fadeOut();
            }
        });
        // scroll body to 0px on click
        $('#back-to-top').click(function() {
            $('body,html').animate({
                scrollTop: 0
            }, 400);
            return false;
        });
    </script>

</body>

</html>