<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>
<?php
if (!$this->session->schoolmis_login_level) {
    redirect(base_url('login'));
}
$uri = 'userteacher'; //$this->session->schoolmis_login_uri;
?>
<style>
    @media (min-width: 992px) {
        .modal-xxl {
            max-width: 90%;
        }
    }
</style>

<!-- <div class="modal fade show" id="modal-default" aria-modal="true" style="padding-right: 16px; display: block;"> -->
<div class="modal fade" id="modalDLLearnerGradesSP" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- <div class="modal-header no-print">
                <h4 class="modal-title text-md">Grades and SMEA Form SY:<?= $getOnLoad["sy"]; ?> | Q-<?= $getOnLoad["qrtr"]; ?></h4>
                <div class='radioBtn btn-group pull-right content'>
                    <button type="submit" onclick="downloadExcel('tblGradesSMEAList','Grades SP Form');" class="btn btn-xs bg-info submitBtnPrimary"><i class="fas fa-download"></i> Download</button>
                    <button type="button" class="btn btn-xs btn-sm btn-default" data-dismiss="modal"><i class="fa fa-times"></i></button>
                </div>
            </div> -->
            <div class="modal-body p-0 content" id="printConsoGrades">
                <!-- <form id="formGradesSMEAList"> -->
                <!-- Note: Please don't alter the <b>LRN</b>. Thank You<br/>
                                                                SY: <?= $getOnLoad["sy"]; ?> | QUARTER: <?= $getOnLoad["qrtr"]; ?><br/>
                                                                <p class="description"></p> -->

                <table class="" style="border:1px solid black;" id="tblGradesSMEAList" width="100%">
                    <thead>
                        <tr>
                            <th rowspan="2" class="th-color" width="1">No</th>
                            <th rowspan="2" class="th-color" width="1">Lrn</th>
                            <th rowspan="2" class="th-color" width="1">Name</th>
                            <th colspan="4" class="th-color-navy" width="1">Grades_Entry_Data</th>
                            <th class="th-color">|</th>
                            <th colspan="4" class="th-color-pink" width="1">Percentage_Score_Data</th>
                        </tr>
                        <tr>
                            <th class="th-color-navy"> Q1 </th>
                            <th class="th-color-navy"> Q2 </th>
                            <th class="th-color-navy"> Q3 </th>
                            <th class="th-color-navy"> Q4 </th>

                            <th class="th-color">|</th>

                            <th class="th-color-pink"> Q1 </th>
                            <th class="th-color-pink"> Q2 </th>
                            <th class="th-color-pink"> Q3 </th>
                            <th class="th-color-pink"> Q4 </th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <!-- </form> -->
            </div>
            <div class="modal-footer justify-content-between content p-1">
                <button type="submit" onclick="downloadExcel('tblGradesSMEAList','Grades SP Form');" class="btn btn-xs bg-info submitBtnPrimary"><i class="fas fa-download"></i> Download</button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script>
    function getGradesSMEAListFN() {
        $(".downloadform").attr("disabled", true);
        $(".downloadform").html("<span class=\"fa fa-spinner fa-pulse\"></span>");

        $("#modalDLLearnerGradesSP .th-color-navy").addClass("bg-navy text-white")
        $("#modalDLLearnerGradesSP .th-color-pink").addClass("bg-pink text-white")
        filename = "GRADES_FORM_" + sec_name + "_";
        getTable("GradesSMEAList", 0, -1);
        $("#modalDLLearnerGradesSP .description").html($(".form_save_dataPersonnelInfo .personnel").text() + "</br>" + $(".form_save_dataPersonnelInfo .description").text())
    }
</script>