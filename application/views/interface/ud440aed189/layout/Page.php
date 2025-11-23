<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php 
    if (!$this->session->schoolmis_login_level) {
        redirect(base_url('login'));
    }
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php $this->load->view('interface/userpassword/layout/Css'); ?>            
    <!-- <script src="<?= base_url() ?>assets/bower_components/jquery/dist/jquery.min.js"></script> -->
    <script src="<?= base_url() ?>assets/plugins/jquery/jquery.min.js"></script>
</head>
<body class="hold-transition layout-fixed layout-navbar-fixed sidebar-collapse layout-top-nav text-sm">
    <div class="wrapper">
        <div class="content-wrapper" >
            <?php foreach ($content as $data): ?>
                <?= $data ?>    
            <?php endforeach ?> 
        </div>
    </div>
    <?php $this->load->view('interface/userpassword/layout/Js'); ?>
    <script type="text/javascript">
        $(".<?= $current_location ?>").addClass("active");
    </script>
</body>
</html>