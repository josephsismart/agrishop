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
<div class="modal fade" id="modalMFGradelvl">
    <div class="modal-dialog modal-xxl">
        <div class="modal-content">
            <div class="modal-header no-print">
                <h4 class="modal-title">Male / Female Age Bracket</h4>
                <div class='radioBtn btn-group pull-right'>
                    <button type="submit" onclick="printForm('MFGradelvl','l','Legal','Inventory details & Purchase Order');" class="btn btn-info submitBtnPrimary btn-xs"><i class="fa fa-print"></i> Print</button>
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="modal-body p-0" id="printMFGradelvl">
                <div class="table-responsive col-md-12 text-center">
                    <!-- <table width="100%">
                        <tr>
                            <td>
                            </td>
                        </tr>
                    </table> -->
                    <center>
                        <table border="1" width="100%" id="tblMFGradelvl">
                            <thead>
                                <tr style="text-align:center;border:1px solid white;">
                                    <td colspan="29"><?php $this->load->view('interface/' . $uri . '/layout/Report_header') ?></td>
                                </tr>
                                <tr style="text-align:center;border:1px solid white;border-bottom: 1px solid gray;">
                                    <td colspan="29">
                                        <h5 id="headerMFGradelvl"></h5>
                                    </td>
                                </tr>
                                <tr align="center">
                                    <th rowspan="3">GRADE LVL</br>SECTION</th>
                                    <th colspan="22">AGE</th>
                                    <th rowspan="2" colspan="2">Total</th>
                                    <th rowspan="2" colspan="2">4P'S</th>
                                </tr>
                                <tr align="center">
                                    <th colspan="2" width="1">Below 11</th>
                                    <th colspan="2" width="1">      11      </th>
                                    <th colspan="2" width="1">      12      </th>
                                    <th colspan="2" width="1">      13      </th>
                                    <th colspan="2" width="1">      14      </th>
                                    <th colspan="2" width="1">      15      </th>
                                    <th colspan="2" width="1">      16      </th>
                                    <th colspan="2" width="1">      17      </th>
                                    <th colspan="2" width="1">      18      </th>
                                    <th colspan="2" width="1">      19      </th>
                                    <th colspan="2" width="1">Above 19</th>
                                </tr>
                                <tr align="center">
                                    <th width="1">M</th><!-- BELOW 11 -->
                                    <th width="1">F</th><!-- BELOW 11 -->
                                    <th width="1">M</th><!-- 11 -->
                                    <th width="1">F</th><!-- 11 -->
                                    <th width="1">M</th><!-- 12 -->
                                    <th width="1">F</th><!-- 12 -->
                                    <th width="1">M</th><!-- 13 -->
                                    <th width="1">F</th><!-- 13 -->
                                    <th width="1">M</th><!-- 14 -->
                                    <th width="1">F</th><!-- 14 -->
                                    <th width="1">M</th><!-- 15 -->
                                    <th width="1">F</th><!-- 15 -->
                                    <th width="1">M</th><!-- 16 -->
                                    <th width="1">F</th><!-- 16 -->
                                    <th width="1">M</th><!-- 17 -->
                                    <th width="1">F</th><!-- 17 -->
                                    <th width="1">M</th><!-- 18 -->
                                    <th width="1">F</th><!-- 18 -->
                                    <th width="1">M</th><!-- 19 -->
                                    <th width="1">F</th><!-- 19 -->
                                    <th width="1">M</th><!-- ABOVE 19 -->
                                    <th width="1">F</th><!-- ABOVE 19 -->
                                    <th width="1">M</th><!-- TOTAL -->
                                    <th width="1">F</th><!-- TOTAL -->
                                    <th width="1">M</th><!-- 4P'S -->
                                    <th width="1">F</th><!-- 4P'S -->
                                </tr>
                            </thead>
                            <tbody style="text-align:center">
                            </tbody>
                        </table>
                    </center>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="submit" onclick="printForm('MFGradelvl','l','Legal','Inventory details & Purchase Order');" class="btn btn-info submitBtnPrimary"><i class="fa fa-print"></i> Print</button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="modalMPS">
    <div class="modal-dialog modal-xxl">
        <div class="modal-content">
            <div class="modal-header no-print">
                <h4 class="modal-title header"></h4>
                <div class='radioBtn btn-group pull-right'>
                    <button type="submit" onclick="printForm('MPS','l','Legal','MPS');" class="btn btn-info submitBtnPrimary btn-xs"><i class="fa fa-print"></i> Print</button>
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="modal-body p-2" id="printMPS">
                <div class="card card-navy p-0 mb-1 table-responsive viewMPS" style="overflow: auto;">
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="submit" onclick="printForm('MPS','l','Legal','MPS');" class="btn btn-info submitBtnPrimary"><i class="fa fa-print"></i> Print</button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!-- <div class="modal fade show" id="modal-default" aria-modal="true" style="padding-right: 16px; display: block;"> -->
<div class="modal fade" id="modalGPA">
    <div class="modal-dialog modal-xxl">
        <div class="modal-content">
            <div class="modal-header no-print">
                <h4 class="modal-title header"></h4>
                <div class='radioBtn btn-group pull-right'>
                    <button type="submit" onclick="printForm('GPA','l','Legal','GPA');" class="btn btn-info submitBtnPrimary btn-xs"><i class="fa fa-print"></i> Print</button>
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="modal-body p-2" id="printGPA">
                <div class="card card-navy p-0 mb-1 table-responsive viewGPA" style="overflow: auto;">
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="submit" onclick="printForm('GPA','l','Legal','GPA');" class="btn btn-info submitBtnPrimary"><i class="fa fa-print"></i> Print</button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->