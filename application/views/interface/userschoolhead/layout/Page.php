<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!$this->session->schoolmis_login_level) {
    redirect(base_url('login'));
}
$uri = $this->session->schoolmis_login_uri;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view('interface/' . $uri . '/layout/Css'); ?>
    <!-- <script src="<?= base_url() ?>bower_components/jquery/dist/jquery.min.js"></script> -->
    <script src="<?= base_url() ?>plugins/jquery/jquery.min.js"></script>
</head>

<style type="text/css">
    .back-to-top {
        position: fixed;
        bottom: 25px;
        right: 25px;
        display: none;
    }
</style>

<body class="hold-transition layout-fixed layout-navbar-fixed sidebar-collapse layout-top-nav text-sm">
    <div class="wrapper">
        <?php $this->load->view('interface/' . $uri . '/layout/Header') ?>
        <div class="content-wrapper">
            <div class="content">
                <div class="container p-0">
                    <?php foreach ($content as $data) : ?>
                        <?= $data ?>
                    <?php endforeach ?>
                </div>
            </div>
        </div>
        <?php $this->load->view('interface/global/Footer') ?>
        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
            <div class="p-3">
                <h5>Title</h5>
                <p>Sidebar content</p>
            </div>
        </aside>
        <!-- /.control-sidebar -->
        <?php $this->load->view('interface/' . $uri . '/layout/Modal') ?>
        <?php $this->load->view('interface/' . $uri . '/layout/ModalReport') ?>
    </div>
    <?php $this->load->view('interface/' . $uri . '/layout/Js'); ?>
    <a id="back-to-top" href="#" class="btn btn-primary btn-lg back-to-top" role="button"><i class="fas fa-chevron-up"></i></a>

    <script type="text/javascript">
        $(".<?= $current_location ?>").addClass("active");
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