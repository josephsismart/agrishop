<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>
<?php
if (!$this->session->schoolmis_login_level) {
    redirect(base_url('login'));
}
$uri = $this->session->schoolmis_login_uri;
?>
<style>
    @media (min-width: 992px) {
        .modal-xxl {
            max-width: 90%;
        }
    }
</style>
<!-- <div class="modal fade show" id="modal-default" aria-modal="true" style="padding-right: 16px; display: block;"> -->
<div class="modal fade" id="modalReportMemberUser">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header no-print">
                <h4 class="modal-title">Members & Users List</h4>
                <div class='radioBtn btn-group pull-right'>
                    <button type="submit" onclick="printForm('MemberUser','p','Legal','Members & Users');" class="btn btn-info submitBtnPrimary btn-xs"><i class="fa fa-print"></i> Print</button>
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="modal-body p-0" id="printMemberUser">
                <div class="table-responsive col-md-12 text-center">
                    <!-- <table width="100%">
                        <tr>
                            <td>
                            </td>
                        </tr>
                    </table> -->
                    <center>
                        <table border="1" width="100%" id="tblReportMemberUser">
                            <thead>
                                <tr style="text-align:center;border:1px solid white;">
                                    <td colspan="6"><?php $this->load->view('interface/' . $uri . '/layout/Report_header') ?></td>
                                </tr>
                                <tr style="text-align:center;border:1px solid white;border-bottom: 1px solid gray;">
                                    <td colspan="6"><br />
                                        <h5 id="headerReportMemberUser">Members & Users as of <?= Date('Y-m-d') ?></h5>
                                    </td>
                                </tr>
                                <tr align="center">
                                    <th>#</th>
                                    <th>Date Registered</th>
                                    <th>Full Name</th>
                                    <th>Sex</th>
                                    <th>Address</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </center>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="submit" onclick="printForm('MemberUser','p','Legal','Members & Users');" class="btn btn-info submitBtnPrimary"><i class="fa fa-print"></i> Print</button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

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


<!-- <div class="modal fade show" id="modal-default" aria-modal="true" style="padding-right: 16px; display: block;"> -->
<div class="modal fade" id="modalPersonnelDTR" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-xs">
        <div class="modal-content">
            <div class="modal-header no-print">
                <h4 class="modal-title header">Daily Time Record</h4>
                <div class='radioBtn btn-group pull-right'>
                    <button type="submit" onclick="printForm('PersonnelDTR','p','A4','PersonnelDTR');" class="btn btn-info submitBtnPrimary btn-xs"><i class="fa fa-print"></i> Print</button>
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="card card-navy py-2 mb-1 viewPersonnelDTR" id="printPersonnelDTR" style="overflow: auto; padding-left: 35px; padding-right: 35px;">
                <table id="tblPersonnelDTR" border="1" cellspacing="0" style="font-size:10px;border:1px solid #000;text-align:center;">
                    <tr>
                        <td width="2%">1</td>
                        <td width="5%">2</td>
                        <td width="5%">3</td>
                        <td width="5%">4</td>
                        <td width="5%">5</td>
                        <td width="5%">6</td>
                        <td width="5%">7</td>
                    </tr>
                    <tr>
                        <td colspan="7" style="font-size:10px;text-align:left;font-style:italic;">Civil service Form No. 48</td>
                    </tr>
                    <tr>
                        <td colspan="7" style="font-size:15px;font-weight:bold;">DALY TIME RECORD</td>
                    </tr>
                    <tr>
                        <td colspan="7" style="font-size:15px;border-bottom:1px solid black;"> </td>
                    </tr>
                    <tr>
                        <td colspan="7" style="font-size:10px;">(NAME)</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-size:12px;text-align:left;font-style:italic;">For the month of </td>
                        <td colspan="4" style="font-size:12px;border-bottom:1px solid black;"> NOVERMBER 2023 </td>
                    </tr>
                    <tr>
                        <td colspan="3" rowspan="2" style="font-size:12px;text-align:left;font-style:italic;">Official hours for arrival and departure</td>
                        <td colspan="3" style="font-size:12px;text-align:right;font-style:italic;">Regular days</td>
                        <td style="font-size:12px;border-bottom:1px solid black;"> 2</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="font-size:12px;text-align:right;font-style:italic;">Saturdays</td>
                        <td style="font-size:12px;border-bottom:1px solid black;"> 2</td>
                    </tr>
                    <tr>
                        <td colspan="7" style="font-size:12px;text-align:right;"> </td>
                    </tr>
                    <tr style="font-size:12px;border: 1px solid black;">
                        <td rowspan="2">Day</td>
                        <td colspan="2">A.M.</td>
                        <td colspan="2">P.M.</td>
                        <td colspan="2">Undertime</td>
                    </tr>
                    <tr style="font-size:12px;">
                        <td>IN</td>
                        <td>OUT</td>
                        <td>IN</td>
                        <td>OUT</td>
                        <td>HRS</td>
                        <td>MNS</td>
                    </tr>
                    <tr style="font-size:12px;">
                        <td>1</td>
                        <td>8:00 AM</td>
                        <td>12:00 PM</td>
                        <td>12:05 PM</td>
                        <td>5:03 PM</td>
                        <td>A</td>
                        <td>A</td>
                    </tr>
                    <tr style="font-size:12px;">
                        <td colspan="5" style="text-align: right;">Total</td>
                        <td>A</td>
                        <td>A</td>
                    </tr>
                    <tr style="font-size:12px;">
                        <td colspan="7" style="text-align: right;"> </td>
                    </tr>
                    <tr style="font-size:12px;text-align:justify;font-style:italic;">
                        <td colspan="7" style="padding:0px 5px 0px 5px;">I certify on my honor that the above is a true and correct
                            report of the hours of work performed, record of which was
                            made daily at the time of arrival and departure from office.</td>
                    </tr>
                    <tr style="font-size:12px;border-bottom:2px solid black;">
                        <td colspan="7"> </td>
                    </tr>
                    <tr style="font-size:12px;font-style:italic;">
                        <td colspan="7">VERIFIED as to the prescribed office hours:</td>
                    </tr>
                    <tr style="font-size:12px;">
                        <td colspan="7"> </td>
                    </tr>
                    <tr style="font-size:12px;text-align:center;">
                        <td colspan="7">In Charge</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="submit" onclick="printForm('PersonnelDTR','p','A4','PersonnelDTR');" class="btn btn-info submitBtnPrimary"><i class="fa fa-print"></i> Print</button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>