<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php 
    if (!$this->session->schoolmis_login_level) {
        redirect(base_url('login'));
    }
    $uri = $this->session->schoolmis_login_uri;
?>
<div style="text-align:left;border-bottom:2px solid #17a2b8;margin-bottom:5px;padding-bottom:5px;">
    <div class="row">
        <div class="col-1 pt-2 pr-0 pl-4">
            <i><img src="<?= $system_svg ?>" width="70" height="70"></i>
        </div>
        <div class="col-11 pull-left pl-2 pt-3">
            Republic of the Philippines<br>
            <strong>AGUSAN NATIONAL HIGH SCHOOL</strong><br>
            Butuan City
        </div>
    </div>
</div>