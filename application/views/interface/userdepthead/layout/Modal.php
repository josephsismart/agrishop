<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>
<?php
if (!$this->session->schoolmis_login_level) {
    redirect(base_url('login'));
}
$uri = $this->session->schoolmis_login_uri;
?>

<div class="modal fade mt-5" id="modalLearnersSubmitGrades">
    <div class="modal-dialog modal-sm">
        <form id="form_save_dataSubmitGradesConfirm">
            <div class="modal-content">
                <div class="modal-header bg-info p-2">
                    <h5 class="modal-title p-0"><span class="fa fa-paper-plane"></span> Approve/Recheck Grades<br />
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text text-bold text-xs">STATUS</span>
                        </div>
                        <select class="form-control status" name="status">
                            <option value="APPROVE">APPROVE</option>
                            <option value="RECHECK">RECHECK</option>
                        </select>
                    </div>
                    <!-- <small><b class="detail">9 - MERCURY</b> JHS ARAL PAN | WD | <b>Q1 - 100%</b></small> -->
                    <input type="text" name="qrssa" id="qrssa" hidden />
                    <textarea class="form-control form-control-sm text-uppercase mb-2 remarks" name="remarks" rows="3" placeholder="REMARKS" nr="1"></textarea>
                    <!-- <input type="password" name="password" class="form-control passwordSubmitGrades submitBtnPrimary" placeholder="Enter Password" /> -->
                </div>
                <div class="modal-footer p-1">
                    <button type="button" class="btn btn-info btn-xs btn-block submitBtnPrimary" onclick="submitGrades();">Submit Grades Status</button>
                </div>
            </div>
        </form>
    </div>
</div>