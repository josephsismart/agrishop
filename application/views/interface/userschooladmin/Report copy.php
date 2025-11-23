<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <!-- <h1 class="m-0 text-dark">Dashboard</h1> -->
      </div><!-- /.col -->
      <!-- <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Dashboard v1</li>
        </ol>
      </div> --><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
  <div class="row m-0">

    <div class="col-md-6">
      <div class="card">
          <div class="card-header bg-info">
            <h5 class="card-title text-uppercase"><i class="fa fa-vial"></i> <b>TEST RESULTS</b></h5>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
              <!-- <div class="btn-group show">
                <button type="button" class="btn btn-tool dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                  <i class="fas fa-wrench"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right" role="menu" x-placement="bottom-end">
                  <a href="#" class="dropdown-item">Action</a>
                  <a href="#" class="dropdown-item">Another action</a>
                  <a href="#" class="dropdown-item">Something else here</a>
                  <a class="dropdown-divider"></a>
                  <a href="#" class="dropdown-item">Separated link</a>
                </div>
              </div> -->
              <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body p-2">
              <form id="filterData_test_report">
                  <div class="row">
                        <div class="col-md-6">
                              <label>FROM DATE</label>
                              <div class="input-group mb-2">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                  </div>
                                  <input type="date" value="<?= Date('Y-m-d') ?>" class="form-control" name="fromDate" maxlength="255" aria-required="true">
                              </div>
                        </div>
                        <div class="col-md-6">
                              <label>TO DATE</label>
                              <div class="input-group mb-2">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                  </div>
                                  <input type="date" value="<?= Date('Y-m-d') ?>" class="form-control" name="toDate" maxlength="255" aria-required="true">
                              </div>
                        </div>
                        <div class="col-md-4">
                              <label>COVID 19 TEST</label>
                              <div class="input-group mb-2">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-virus"></i></span>
                                  </div>
                                  <select class="form-control" name="testCOVIDFilter">
                                    <option value=''>ALL</option>
                                    <?php foreach ($getStatus['data4'] as $key => $value): ?>
                                        <option value='<?= $value['id'] ?>'><?= $value['text'] ?></option>
                                    <?php endforeach ?>
                                  </select>
                              </div>
                        </div>
                        <div class="col-md-4">
                              <label>RESULT</label>
                              <div class="input-group mb-2">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-vial"></i></span>
                                  </div>
                                  <select class="form-control" name="resultCOVIDFilter">
                                      <option value=''>ALL</option>
                                      <?php foreach ($getStatus['data6'] as $key => $value): ?>
                                          <option value='<?= $value['id'] ?>'><?= $value['text'] ?></option>
                                      <?php endforeach ?>
                                  </select>
                              </div>
                        </div>
                        
                        <div class="col-md-4">
                              <label>BARANGAY</label>
                              <div class="input-group mb-2" style="width:100%;">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-home"></i></span>
                                  </div>
                                  <select class="form-control" name="barangayNameFilter">
                                      <option value=''>ALL BARANGAY</option>
                                    <?php foreach ($getBarangay['data1'] as $key => $value): ?>
                                        <option value='<?= $value['id'] ?>'><?= $value['text'] ?></option>
                                    <?php endforeach ?>
                                  </select>
                                  <div class="input-group-append">
                                    <!-- <span class="input-group-text"><i class="fas fa-bed"></i></span> -->
                                    <button type="button" class="btn btn-warning btn-sm text-white searchBtn_test_report" onclick="$(this).html('<span class=\'fa fa-spinner fa-spin\'></span>');$(this).attr('disabled',true);test_report();"><i class="fas fa-search"></i></span></button>
                                  </div>
                              </div>
                        </div>

                  </div>
              </form>
          </div>
          <!-- ./card-body -->
      </div>
      <!-- /.card -->
    </div>

    <div class="col-md-6">
      <div class="card">
          <div class="card-header bg-info">
            <h5 class="card-title text-uppercase"><i class="fa fa-virus"></i> <b>COVID 19 STATUS</b></h5>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
              <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <div class="card-body p-2">
              <form id="filterData_status_report">
                  <div class="row">
                        <div class="col-md-6">
                              <label>FROM DATE</label>
                              <div class="input-group mb-2">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                  </div>
                                  <input type="date" value="<?= Date('Y-m-d') ?>" class="form-control" name="fromDate" maxlength="255" aria-required="true">
                              </div>
                        </div>
                        <div class="col-md-6">
                              <label>TO DATE</label>
                              <div class="input-group mb-2">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                  </div>
                                  <input type="date" value="<?= Date('Y-m-d') ?>" class="form-control" name="toDate" maxlength="255" aria-required="true">
                              </div>
                        </div>
                        <div class="col-md-4">
                              <label>COVID 19 STATUS</label>
                              <div class="input-group mb-2">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-virus"></i></span>
                                  </div>
                                  <select class="form-control" name="statusCOVIDFilter">
                                    <option value=''>ALL</option>
                                    <?php foreach ($getStatus['data1'] as $key => $value): ?>
                                        <option value='<?= $value['id'] ?>'><?= $value['text'] ?></option>
                                    <?php endforeach ?>
                                  </select>
                              </div>
                        </div>
                        <div class="col-md-4">
                              <label>QUARANTINE STATUS</label>
                              <div class="input-group mb-2">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-bed"></i></span>
                                  </div>
                                  <select class="form-control" name="qstatusCOVIDFilter">
                                    <option value=''>ALL</option>
                                    <?php foreach ($getStatus['data9'] as $key => $value): ?>
                                        <option value='<?= $value['id'] ?>'><?= $value['text'] ?></option>
                                    <?php endforeach ?>
                                  </select>
                              </div>
                        </div>
                        <div class="col-md-4">
                              <label>BARANGAY</label>
                              <div class="input-group mb-2" style="width:100%;">
                                  <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-home"></i></span>
                                  </div>
                                  <select class="form-control" name="barangayNameFilter">
                                      <option value=''>ALL BARANGAY</option>
                                    <?php foreach ($getBarangay['data1'] as $key => $value): ?>
                                        <option value='<?= $value['id'] ?>'><?= $value['text'] ?></option>
                                    <?php endforeach ?>
                                  </select>
                                  <div class="input-group-append">
                                    <button type="button" class="btn btn-warning btn-sm text-white searchBtn_status_report" onclick="$(this).html('<span class=\'fa fa-spinner fa-spin\'></span>');$(this).attr('disabled',true);status_report();"><i class="fas fa-search"></i></span></button>
                                  </div>
                              </div>
                        </div>

                  </div>
              </form>
          </div>
      </div>
    </div>

  </div>
</section>
<script type="text/javascript">

    function test_report(){
        $("#tbl_test_report tbody").empty();
        var tbl_test_report, tbl_test_report_data;
            tbl_test_report = $("#tbl_test_report").DataTable({
                                "columnDefs": [ {
                                  "orderable": false,
                                }],
                                "paging": false,
                                "pageLength": 10,
                                "lengthChange": false,
                                "searching": false,
                                "ordering": true,
                                "info": false,
                                "autoWidth": true,
                                ajax: {
                                    url: "<?= base_url('usertestvalidator/Report/getTestReport') ?>",
                                    type: "POST",
                                    data:
                                    function(d) {
                                      d.a=$("#filterData_test_report").serialize();
                                      return tbl_test_report_data;
                                    },success: function(data){
                                        $.each(data["data"], function(a, b) {
                                            $("#tbl_test_report").append("<tr><td>"+b[0]+"</td>"+
                                                                                  "<td>"+b[1]+"</td>"+
                                                                                  "<td>"+b[2]+"</td>"+
                                                                                  "<td>"+b[3]+"</td>"+
                                                                                  "<td>"+b[4]+"</td>"+
                                                                                  "<td>"+b[5]+"</td>"+
                                                                                  "<td>"+b[6]+"</td>"+
                                                                                  "<td>"+b[7]+"</td>"+
                                                                                  "<td>"+b[8]+"</td>"+
                                                                                  "<td>"+b[9]+"</td></tr>");
                                        });
                                        $(".searchBtn_test_report").attr("disabled", false);
                                        $(".searchBtn_test_report").html("<span class=\"fa fa-search\"></span>");
                                        $('#modal_test_report').modal('show');

                                        $("#tbl_summary_test_category tbody,#tbl_summary_test_gender tbody,#tbl_summary_test_barangay tbody,#tbl_summary_test_age tbody").empty();
                                        // $("#tbl_certificate_reportExp").append("<tr><td colspan='7'><b>Summary</b></td></tr>");
                                        var len1=data["data1"].length-1;
                                        var len2=data["data2"].length-1;
                                        var len3=data["data3"].length-1;
                                        var len4=data["data4"].length-1;
                                        if(len1>-1){
                                            $.each(data["data1"], function(a, b) {
                                            $("#tbl_summary_test_category").append("<tr><td align='left'>"+b[0]+"</td>"+
                                                                                  "<td align='right'>"+b[1]+" </td>"+
                                                                                  "<td align='right'>"+b[2]+" </td>"+
                                                                                  "<td align='right'>"+b[3]+" </td>"+
                                                                                  "<td align='right'>"+b[4]+" </td>"+
                                                                                  "<td align='right' width='1'><b>   "+b[5]+" </b></td></tr>");
                                            });
                                        }
                                        if(len2>-1){
                                            $.each(data["data2"], function(a, b) {
                                            $("#tbl_summary_test_gender").append("<tr><td align='left' width='1'>"+b[0]+"</td>"+
                                                                                  "<td align='right'>"+b[1]+" </td>"+
                                                                                  "<td align='right'>"+b[2]+" </td>"+
                                                                                  "<td align='right'>"+b[3]+" </td>"+
                                                                                  "<td align='right'>"+b[4]+" </td>"+
                                                                                  "<td align='right' width='1'><b>   "+b[5]+" </b></td></tr>");
                                            });
                                        }
                                        if(len3>-1){
                                            $.each(data["data3"], function(a, b) {
                                            $("#tbl_summary_test_barangay").append("<tr><td align='left'>"+b[0]+"</td>"+
                                                                                  "<td align='right'>"+b[1]+" </td>"+
                                                                                  "<td align='right'>"+b[2]+" </td>"+
                                                                                  "<td align='right'>"+b[3]+" </td>"+
                                                                                  "<td align='right'>"+b[4]+" </td>"+
                                                                                  "<td align='right' width='1'><b>   "+b[5]+" </b></td></tr>");
                                            });
                                        }
                                        if(len4>-1){
                                            $.each(data["data4"], function(a, b) {
                                            $("#tbl_summary_test_age").append("<tr><td align='left'>"+b[0]+"</td>"+
                                                                                  "<td align='right'>"+b[1]+" </td>"+
                                                                                  "<td align='right'>"+b[2]+" </td>"+
                                                                                  "<td align='right'>"+b[3]+" </td>"+
                                                                                  "<td align='right'>"+b[4]+" </td>"+
                                                                                  "<td align='right' width='1'><b>   "+b[5]+" </b></td></tr>");
                                            });
                                        }
                                    }
                                }
                            }).destroy();
                            
                            // $('#tbl_test_report').on('draw.dt', function () {
                            //     $(".searchBtn_test_report").attr("disabled", false);
                            //     $(".searchBtn_test_report").html("<span class=\"fa fa-search\"></span>");
                            //     $('#modal_test_report').modal('show');
                            // });
    }
    function status_report(){
        $("#tbl_status_report tbody").empty();
        var tbl_status_report, tbl_status_report_data;
            tbl_status_report = $("#tbl_status_report").DataTable({
                                "columnDefs": [ {
                                  "orderable": false,
                                }],
                                "paging": false,
                                "pageLength": 10,
                                "lengthChange": false,
                                "searching": false,
                                "ordering": true,
                                "info": false,
                                "autoWidth": true,
                                ajax: {
                                    url: "<?= base_url('usertestvalidator/Report/getStatusReport') ?>",
                                    type: "POST",
                                    data:
                                    function(d) {
                                      d.a=$("#filterData_status_report").serialize();
                                      return tbl_status_report_data;
                                    }
                                }
                            }).destroy();
                            
                            $('#tbl_status_report').on('draw.dt', function () {
                                $(".searchBtn_status_report").attr("disabled", false);
                                $(".searchBtn_status_report").html("<span class=\"fa fa-search\"></span>");
                                $('#modal_status_report').modal('show');
                            });
    }

</script>

<!-- /.content -->