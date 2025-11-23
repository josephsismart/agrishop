<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
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
                <div  class='radioBtn btn-group pull-right'>
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
                                <td colspan="6"><?php $this->load->view('interface/'.$uri.'/layout/Report_header')?></td>
                            </tr>
                            <tr style="text-align:center;border:1px solid white;border-bottom: 1px solid gray;">
                                <td colspan="6"><br/><h5 id="headerReportMemberUser">Members & Users as of <?= Date('Y-m-d') ?></h5></td>
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
<div class="modal fade" id="modalReportInvPO">
    <div class="modal-dialog modal-xxl">
        <div class="modal-content">
            <div class="modal-header no-print">
                <h4 class="modal-title">Inventory & Purchase Order List</h4>
                <div  class='radioBtn btn-group pull-right'>
                    <button type="submit" onclick="printForm('InvPO','l','Legal','Inventory details & Purchase Order');" class="btn btn-info submitBtnPrimary btn-xs"><i class="fa fa-print"></i> Print</button>
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="modal-body p-0" id="printInvPO">
                <div class="table-responsive col-md-12 text-center">
                    <!-- <table width="100%">
                        <tr>
                            <td>
                            </td>
                        </tr>
                    </table> -->
                    <center>
                    <table border="1" width="100%" id="tblReportInvPO">
                        <thead>
                            <tr style="text-align:center;border:1px solid white;">
                                <td colspan="13"><?php $this->load->view('interface/'.$uri.'/layout/Report_header')?></td>
                            </tr>
                            <tr style="text-align:center;border:1px solid white;border-bottom: 1px solid gray;">
                                <td colspan="13"><br/><h5 id="headerReportInvPO"></h5></td>
                            </tr>
                            <tr align="center">
                                <th>#</th>
                                <th>INV Code</th>
                                <th>Date</th>
                                <th>Full Name</th>
                                <th>Variety</th>
                                <th>Count</th>
                                <th>Weight</th>
                                <th>Sacks</th>
                                <th>PO Code</th>
                                <th>Date</th>
                                <th>Deduct</th>
                                <th>Kilo</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    </center>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="submit" onclick="printForm('InvPO','l','Legal','Inventory details & Purchase Order');" class="btn btn-info submitBtnPrimary"><i class="fa fa-print"></i> Print</button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!-- <div class="modal fade show" id="modal-default" aria-modal="true" style="padding-right: 16px; display: block;"> -->
<div class="modal fade" id="modalReportStckPR">
    <div class="modal-dialog modal-xxl">
        <div class="modal-content">
            <div class="modal-header no-print">
                <h4 class="modal-title">Stocks & Purchase Request List</h4>
                <div  class='radioBtn btn-group pull-right'>
                    <button type="submit" onclick="printForm('StckPR','l','Legal','Stocks & Purchase Request');" class="btn btn-info submitBtnPrimary btn-xs"><i class="fa fa-print"></i> Print</button>
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="modal-body p-0" id="printStckPR">
                <div class="table-responsive col-md-12 text-center">
                    <!-- <table width="100%">
                        <tr>
                            <td>
                            </td>
                        </tr>
                    </table> -->
                    <center>
                    <table border="1" width="100%" id="tblReportStckPR">
                        <thead>
                            <tr style="text-align:center;border:1px solid white;">
                                <td colspan="13"><?php $this->load->view('interface/'.$uri.'/layout/Report_header')?></td>
                            </tr>
                            <tr style="text-align:center;border:1px solid white;border-bottom: 1px solid gray;">
                                <td colspan="13"><br/><h5 id="headerReportStckPR"></h5></td>
                            </tr>
                            <tr align="center">
                                <th>#</th>
                                <th>PO Code</th>
                                <th>STCK Code</th>
                                <th>Date</th>
                                <th>Sacks</th>
                                <th>Kilos</th>
                                <th>PR Code</th>
                                <th>Date</th>
                                <th>Full Name</th>
                                <th>Category</th>
                                <th>Subcategory</th>
                                <th>Kilos</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    </center>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="submit" onclick="printForm('StckPR','l','Legal','Stocks & Purchase Request');" class="btn btn-info submitBtnPrimary"><i class="fa fa-print"></i> Print</button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="modalMPS_GPA">
    <div class="modal-dialog modal-xxl">
        <div class="modal-content">
            <div class="modal-header no-print">
                <h4 class="modal-title header"></h4>
                <div class='radioBtn btn-group pull-right'>
                    <button type="submit" onclick="printForm('MPS_GPA','l','Legal','MPS_GPA');" class="btn btn-info submitBtnPrimary btn-xs"><i class="fa fa-print"></i> Print</button>
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal"><i class="fa fa-times"></i></button>
                </div>
            </div>
            <div class="modal-body p-2" id="printMPS_GPA">
                <div class="card card-navy p-0 mb-1 table-responsive viewMPS_GPA" style="overflow: auto;">
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="submit" onclick="printForm('MPS_GPA','l','Legal','MPS_GPA');" class="btn btn-info submitBtnPrimary"><i class="fa fa-print"></i> Print</button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>