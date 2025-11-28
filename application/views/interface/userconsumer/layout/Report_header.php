<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php 
    if (!$this->session->schoolmis_login_level) {
        redirect(base_url('login'));
    }
    $uri = 'userteacher'; //$this->session->schoolmis_login_uri;
?>
<address style="text-align:left;border-bottom:2px solid green;margin-bottom:5px;padding-bottom:5px;">
    <div class="row">
        <div class="col-1 p-2 pl-4">
            <i style="padding-right:12px;"><img src="<?= $system_svg ?>" width="52" height="52"></i>
        </div>
        <div class="col-11 pull-left pl-3">
            Republic of the Philippines<br>
            <strong>RICE INVENTORY SYSTEM</strong><br>
            Butuan City
        </div>
    </div>
</address>