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
<div class="modal fade" id="modalReportConsoGrades" data-backdrop="static">
    <div class="modal-dialog modal-xxl">
        <div class="modal-content">
            <div class="modal-header no-print">
                <h4 class="modal-title">Consolidated Grades <?= $getOnLoad["sy"]; ?></h4>
                <div class='radioBtn btn-group pull-right content'>
                    <button type="submit" onclick="printForm('ConsoGrades','l','Legal','Inventory details & Purchase Order');" class="btn btn-xs btn-primary submitBtnPrimary btn-xs content"><i class="fa fa-print"></i> Print</button>
                    <button type="submit" onclick="downloadExcel('tblReportConsoGrades','Consolidated Grades');" class="btn btn-xs bg-green submitBtnPrimary"><i class="fas fa-file-excel"></i> Export</button>
                    <button type="button" class="btn btn-xs btn-sm btn-default" data-dismiss="modal"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="modal-body p-0 content" id="printConsoGrades">
                <div class="table-responsive col-md-12 text-center">
                    <!-- tblReportConsoGradesz -->
                    <table border="1" id="tblReportConsoGrades" width="100%">
                        <!-- <table class="table table-sm table-bordered table-hover table-striped" id="tblReportConsoGrades" width="100%"> -->
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer justify-content-between content">
                <!-- <button type="submit" onclick="printForm('ConsoGrades','l','Legal','Consolidated Grades');" class="btn btn-primary submitBtnPrimary"><i class="fa fa-print"></i> Print</button> -->
                <button type="submit" onclick="downloadExcel('tblReportConsoGrades','Consolidated Grades');" class="btn btn-xs bg-green submitBtnPrimary"><i class="fas fa-file-excel"></i> Export</button>
                <!-- <button type="submit" onclick="downloadExcel('tblReportConsoGrades','Consolidated Grades');" class="btn bg-green submitBtnPrimary"><i class="fas fa-file-excel"></i> Export</button> -->
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
            <div class="overlay">
                <i class="fas fa-spin text-white fa-3x fa-circle-notch"></i>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="modalGRADE_SLIP">
    <div class="modal-dialog modal-xxl">
        <div class="modal-content">
            <div class="modal-header no-print">
                <h4 class="modal-title header"></h4>
                <div class='radioBtn btn-group pull-right'>
                    <button type="submit" onclick="printForm('GRADE_SLIP','l','Legal','GRADE_SLIP');" class="btn btn-info submitBtnPrimary btn-xs"><i class="fa fa-print"></i> Print</button>
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="modal-body p-2" id="printGRADE_SLIP">
                <div class="card card-navy p-0 mb-1 table-responsive viewGRADE_SLIP" style="overflow: auto;">

                    <table class="table table-sm table-hover table-striped table-bordered" id="tblGradesList" width="100%">
                        <!-- <thead style="text-align: center;font-weight: bold;">
                            <tr>
                                <td rowspan="2" class="pt-3" width="400">Learing Areas</td>
                                <td colspan="4">Quarter</td>
                                <td rowspan="2" width="1">Final Grade</td>
                                <td rowspan="2" class="pt-3" width="1">Remarks</td>
                                <td rowspan="2" class="pt-3" width="400">Learing Areas</td>
                                <td colspan="4">Quarter</td>
                                <td rowspan="2" width="1">Final Grade</td>
                                <td rowspan="2" class="pt-3" width="1">Remarks</td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>2</td>
                                <td>3</td>
                                <td>4</td>
                                <td>1</td>
                                <td>2</td>
                                <td>3</td>
                                <td>4</td>
                            </tr>
                        </thead> -->
                        <!-- <tbody>
                             <tr>
                                <td>a</td>
                                <td>a</td>
                                <td>a</td>
                                <td>a</td>
                            </tr> 
                        </tbody> -->
                    </table>

                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="submit" onclick="printForm('GRADE_SLIP','l','Legal','GRADE_SLIP');" class="btn btn-info submitBtnPrimary"><i class="fa fa-print"></i> Print</button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!-- <div class="modal fade show" id="modal-default" aria-modal="true" style="padding-right: 16px; display: block;"> -->
    <div class="modal fade" id="modalPreviewID" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header no-print">
                <h4 class="modal-title header"></h4>
                <div class='radioBtn btn-group pull-right'>
                    <button type="submit" onclick="printForm('PreviewID','p','A4','PreviewID');" class="btn btn-info submitBtnPrimary btn-xs"><i class="fa fa-print"></i> Print</button>
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="card card-navy py-2 mb-1 viewPreviewID" id="printPreviewID" style="overflow: auto; padding-left: 35px; padding-right: 35px;">
                <table id="tblPreviewID" class="table-responsive" width="100%">
                    <td align="left" width="50%" style="border: 1px dashed #000;padding: 2.5px;">
                        <table cellspacing="0" style="font-size:10px;border:2.5px solid #3786A3;background-image: url(<?= $system_bg_front_id ?>);background-repeat: no-repeat;background-size: 100% 100%;width: 354px;height: 518px;">
                            <tr align="center" style="height:5.5rem; border: 1px solid #000;font-size:12px;color: rgba(0, 0, 0, 0);">
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                            </tr>
                            <!-- <tr align="center" style="border: 1px solid #000;font-size:5px;color: rgba(0, 0, 0, 0);">
                                <td class="border border-warning"> </td>
                                <td colspan="9" class="border border-warning" style="font-family:'Montserrat', sans-serif;font-weight:800;font-size:12px;vertical-align:bottom;">
                                    <p style="padding:0;margin-bottom:-4px;"></p>
                                </td>
                                <td colspan="10" class="border border-warning"> </td>
                            </tr> -->
                            <!-- img -->
                            <tr align="center" style="height:1.4rem; border: 1px solid #000;font-size:5px;">
                                <td class="border border-warning"></td>
                                <td rowspan="4" colspan="10" class="border border-warning" style="padding:0;vertical-align:top;width:1rem;">
                                    <img name="previewPic" src="<?= $system_svg_1x1 ?>" width="100%" class="border border-primary border-2 rounded elevation-1" alt="User Image" style="padding:0;margin:0;vertical-align:top;">
                                </td>
                                <!-- <td class="border border-warning"></td> -->
                                <td class="border border-warning">
                                </td>
                                <td colspan="6" class="border border-warning" style="font-family:'Montserrat', sans-serif;font-weight:bold;font-size:10px;vertical-align:bottom;">
                                    <p style="padding:0;margin-bottom:-4px;">S.Y. 2023-2024</p>
                                    <!-- <img name="previewPic" src="<?= $system_svg_1x1 ?>" width="80" height="80" alt="User Image" style="padding:0;margin:0;vertical-align:top;"> -->
                                </td>
                                <td class="border border-warning">
                                </td>
                                <td class="border border-warning"></td>
                            </tr>
                            <tr align="center" style="height:1rem; border: 1px solid #000;font-size:5px;">
                                <td class="border border-warning"> </td>
                                <td class="border border-warning"> </td>
                                <td colspan="6" class="border border-warning" style="padding-top:0px;">
                                    <img name="previewPic" src="<?= $system_svg_1x1 ?>" width="100%" class="border border-primary border-2 rounded elevation-1 mt-n2" alt="User Image">
                                </td>
                                <td class="border border-warning">
                                </td>
                                <td class="border border-warning"></td>
                            </tr>
                            <!-- barcode -->
                            <tr align="center" style="height:1rem; border: 1px solid #000;font-size:7px;">
                                <td class="border border-warning"> </td>
                                <td class="border border-warning"> </td>
                                <!-- <td colspan="8" class="border border-warning"> </td> -->
                                <td colspan="6" class="border border-warning" style="padding:0;vertical-align: top;width:1rem;">
                                    <!-- <img name="previewPic" src="<?= $system_svg_1x1 ?>" width="100%" height="100%" class="border border-primary border-2 rounded elevation-1" alt="User Image"> -->
                                </td>
                                <td class="border border-warning"> </td>
                                <td class="border border-warning"> </td>
                            </tr>
                            <!-- lrn -->
                            <tr align="center" style="height:1rem; border: 1px solid #000;font-size:7px;">
                                <td class="border border-warning"></td>
                                <td class="border border-warning"></td>
                                <td colspan="7" class="border border-warning" style="font-family:'Montserrat', sans-serif;font-weight:bold;font-size:8px;vertical-align:bottom;">
                                    <p style="padding:0;margin-bottom:-5px;"></p>
                                </td>
                                <td class="border border-warning"></td>
                            </tr>
                            <!-- signature -->
                            <tr align="center" style="height:1.5rem; border: 1px solid #000;font-size:7px;">
                                <td class="border border-warning"> </td>
                                <td colspan="9" class="border border-warning"> </td>
                                <td class="border border-warning"> </td>
                                <td colspan="9" class="border border-warning"> </td>
                            </tr>
                            <!-- lastname -->
                            <tr align="center" style="height:1.4rem; border: 1px solid #000;padding:0">
                                <td class="border border-warning"> </td>
                                <td colspan="18" class="border border-warning text-left" style="font-family:'Montserrat', sans-serif;color:#102C3D;font-weight:800;font-size:1.6rem;padding:0 0 3px 0;">
                                    <p class="mb-n3">AALA</p>
                                </td>
                                <td class="border border-warning"> </td>
                            </tr>
                            <!-- firstname middleinitial -->
                            <tr align="center" style="height:1.3rem; border: 1px solid #000;">
                                <td class="border border-warning"> </td>
                                <!-- <td colspan="18" class="border border-warning"> </td> -->
                                <td colspan="18" class="border border-warning text-left" style="font-family:'Montserrat', sans-serif;color:#102C3D;font-weight:600;font-size:1.3rem;padding:0;">
                                    <p class="mb-n2">MARIE ANGELA GRACE O.</p>
                                </td>
                                <td class="border border-warning"> </td>
                            </tr>
                            <!-- grade section League Gothic -->
                            <tr align="center" style="height:1.3rem; border: 1px solid #000;font-size:7px;">
                                <td class="border border-warning"> </td>
                                <td colspan="5" class="border border-warning text-left" style="font-family:'League Gothic', sans-serif;color:#41A1DF;font-weight:300;font-size:1.1rem;padding:0;">
                                    <p class="mb-n2">Grade & Section:</p>
                                </td>
                                <td colspan="13" class="border border-warning text-left" style="font-family:'League Gothic', sans-serif;font-weight:300;font-size:1.1rem;padding:0;">
                                    <p class="mb-n2">Grade 12 - AQUINAS</p>
                                </td>
                                <td class="border border-warning"> </td>
                            </tr>
                            <!-- program/strand -->
                            <tr align="center" style="height:1.3rem; border: 1px solid #000;font-size:7px;">
                                <td class="border border-warning"> </td>
                                <td colspan="5" class="border border-warning text-left" style="font-family:'League Gothic', sans-serif;color:#41A1DF;font-weight:300;font-size:1.1rem;padding:0;">
                                    <p class="mb-n2">Program/Strand:</p>
                                </td>
                                <td colspan="13" class="border border-warning text-left" style="font-family:'League Gothic', sans-serif;color:forestgreen;font-weight:300;font-size:1.1rem;padding:0;">
                                    <p class="mb-n2">Humanities and Social Sciences</p>
                                </td>
                                <td class="border border-warning"> </td>
                            </tr>
                            <!-- adviser -->
                            <tr align="center" style="height:1.3rem; border: 1px solid #000;font-size:7px;">
                                <td class="border border-warning"> </td>
                                <td colspan="5" class="border border-warning text-left" style="font-family:'League Gothic', sans-serif;color:#41A1DF;font-weight:300;font-size:1.1rem;padding:0;">
                                    <p class="mb-n2">Adviser:</p>
                                </td>
                                <td colspan="13" class="border border-warning text-left" style="font-family:'League Gothic', sans-serif;font-weight:300;font-size:1.1rem;padding:0;">
                                    <p class="mb-n2">JOHN AXEL A. CAÑETE</p>
                                </td>
                                <td class="border border-warning"> </td>
                            </tr>
                            <tr align="center" style="height:1.6rem; border: 1px solid #000;font-size:7px;color: rgba(0, 0, 0, 0);">
                                <td colspan="20" class="border border-warning"> </td>
                            </tr>

                            <!-- school year -->
                            <!-- <tr align="center" style="height:.3rem; border: 1px solid #000;font-size:7px;">
                                <td class="border border-warning"> </td>
                                <td colspan="4" class="border border-warning" style="font-family:'Montserrat', sans-serif;font-weight:800;vertical-align:bottom;">
                                    <p style="font-size:.55rem;margin:-.5rem 0 -.1rem 0;">S.Y. 2023-2024</p>
                                </td>
                                <td colspan="14" class="border border-warning"> </td>
                                <td class="border border-warning"> </td>
                            </tr> -->
                            <!-- qrcode -->
                            <!-- <tr align="center" style="height:4rem; border: 1px solid #000;font-size:7px;">
                                <td class="border border-warning"> </td>
                                <td colspan="4" class="border border-warning" style="padding:0;height:3.8rem;vertical-align: top;width:1rem;">
                                    <img name="previewPic" src="<?= $system_svg_1x1 ?>" width="100%" height="100%" class="border border-primary border-2 rounded elevation-1" alt="User Image">
                                </td>
                                <td colspan="14" class="border border-warning"> </td>
                                <td class="border border-warning"> </td>
                            </tr> -->
                            <tr stye="border: 1px solid #000;font-size:1px;">
                                <td colspan="20"></td>
                            </tr>
                        </table>
                    </td>

                    <td align="right" width="50%" style="border: 1px dashed #000;padding: 2.5px;">
                        <table cellspacing="0" style="font-size:10px;border:2.5px solid #3786A3;background-image: url(<?= $system_bg_back_id ?>);background-repeat: no-repeat;background-size: 100% 100%;width: 354px;height: 518px;">
                            <tr align="center" style="height:5rem; border: 1px solid #000;font-size:12px;color: rgba(0, 0, 0, 0);">
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                                <td class="border border-warning" width="5%">  </td>
                            </tr>
                            <!-- qrcode back -->
                            <tr align="center" style="height:4rem; border: 1px solid #000;font-size:5px;">
                                <td colspan="6" class="border border-warning"></td>
                                <td colspan="8" class="border border-warning" style="padding:0;vertical-align: top;width:1rem;">
                                    <img name="previewPic" src="<?= $system_svg_1x1 ?>" width="100%" height="100%" class="border border-primary border-2 rounded elevation-1" alt="User Image" style="padding:0;margin:0;vertical-align:top;">
                                </td>
                                <td colspan="6" class="border border-warning"></td>
                            </tr>
                            <tr align="center" style="height:2rem; border: 1px solid #000;font-size:7px;">
                                <td colspan="20" class="border border-warning"></td>
                            </tr>
                            <tr align="center" style="height:1.6rem; border: 1px solid #000;font-size:7px;">
                                <td colspan="2" class="border border-warning"></td>
                                <td colspan="16" class="border border-warning" style="font-family:'Montserrat', sans-serif;color:#000;font-weight:800;vertical-align:bottom;">
                                    <p style="font-size:1.4rem;margin:-1.7rem 0 -1.6rem 0;">MARIA CLARA O. AALA</p>
                                </td>
                                <td colspan="2" class="border border-warning"></td>
                            </tr>
                            <tr align="center" style="height:1rem; border: 1px solid #000;font-size:7px;">
                                <td colspan="2" class="border border-warning"></td>
                                <td colspan="16" class="border border-warning" style="font-family:'League Gothic', sans-serif;color:#1E34AB;font-weight:300;vertical-align:bottom;">
                                    <p style="font-size:1rem;margin:-1.3rem 0 -1rem 0;">PARENT/GUARDIAN</p>
                                </td>
                                <td colspan="2" class="border border-warning"></td>
                            </tr>
                            <tr align="center" style="height:1rem; border: 1px solid #000;font-size:7px;">
                                <td colspan="2" class="border border-warning"></td>
                                <td colspan="16" class="border border-warning" style="font-family:'League Gothic', sans-serif;color:#000;font-weight:300;vertical-align:bottom;">
                                    <p style="font-size:1rem;margin:-1.3rem 0 -1rem 0;">09235412547</p>
                                </td>
                                <td colspan="2" class="border border-warning"></td>
                            </tr>
                            <tr align="center" style="height:1rem; border: 1px solid #000;font-size:7px;">
                                <td colspan="2" class="border border-warning"></td>
                                <td colspan="16" class="border border-warning" style="font-family:'League Gothic', sans-serif;color:#000;font-weight:300;vertical-align:bottom;">
                                    <p style="font-size:1rem;margin:-1.3rem 0 -1rem 0;">12321, CHARS, .TR. ESTACIO VILLAGE BUTUAN CITY</p>
                                </td>
                                <td colspan="2" class="border border-warning"></td>
                            </tr>

                            <tr stye="border: 1px solid #000">
                                <td colspan="20"></td>
                            </tr>
                        </table>
                    </td>
                </table>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="submit" onclick="printForm('PreviewID','p','A4','PreviewID');" class="btn btn-info submitBtnPrimary"><i class="fa fa-print"></i> Print</button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="modalLearnersSF1" data-backdrop="static">
    <div class="modal-dialog">
        <form id="form_save_">
            <div class="modal-content">
                <div class="modal-header bg-default p-2">
                    <h5 class="modal-title p-0"><span class="fa fa-folder-open"></span> Form 1 </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- <h5><strong class='lrn'></strong></h5>
                    <input name="details" hidden />
                    <p class="lead">
                        <strong class="last_fullname"></strong>
                    </p>
                    <input type="password" name="password" class="form-control passwordUnenroll submitBtnPrimary" placeholder="Enter Password" />

                    <div class="alert alert-warning alert-dismissible mt-3 mb-0 pr-3">
                        <h5><i class="icon fas fa-info"></i> Please read!</h5>
                        Inputted <b>GRADES</b> will be <b>DELETED</b> also.
                    </div> -->
                </div>
                <div class="modal-footer p-1">
                    <!-- <button type="button" class="btn btn-default btn-xs btn-block submitBtnPrimary" onclick="unenroll();">Unenroll Student</button> -->
                    <!-- <button type="button" class="btn btn-default btn-xs" data-dismiss="modal"><i class="fa fa-times"></i> Close</button> -->
                </div>
            </div>
        </form>
    </div>
</div>