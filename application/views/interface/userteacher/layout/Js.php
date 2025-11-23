<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!$this->session->schoolmis_login_level) {
    redirect(base_url('login'));
}
$uri = 'userteacher'; //$this->session->schoolmis_login_uri;
$uri_reports = "reports";
$sy_ = $getOnLoad["sy"]; //$getOnLoad["sy_qrtr_e_g"];
$q_ = $getOnLoad["qrtrR"]; //$getOnLoad["sy_qrtr_e_g"];
?>
<!-- Bootstrap 4 -->

<script src="<?= base_url() ?>plugins/ol3/ol.js"></script>
<script src="<?= base_url() ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Select2 -->
<script src="<?= base_url() ?>plugins/select2/js/select2.full.min.js"></script>
<!-- DataTables -->
<script src="<?= base_url() ?>plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/dataTables.buttons.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/buttons.print.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/jszip.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/buttons.flash.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/buttons.html5.min.js"></script>
<!-- <script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/pdfmake.min.js"></script> -->
<!-- <script src="<?= base_url() ?>plugins/inputmask/jquery.inputmask.min.js"></script> -->
<script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/vfs_fonts.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/responsive/js/dataTables.responsive.min.js"></script>
<!-- SweetAlert2 -->
<script src="<?= base_url() ?>plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Toastr -->
<script src="<?= base_url() ?>plugins/toastr/toastr.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url() ?>dist/js/adminlte.min.js"></script>
<!-- ExportExcel -->
<script src="<?= base_url() ?>plugins/tablexcel/src/jquery.table2excel.js"></script>
<!-- ImportExcel -->
<script src="<?= base_url() ?>plugins/jquery/xlsx.full.min.js"></script>
<script src="<?= base_url() ?>plugins/jquery/jszip.js"></script>
<script src="<?= base_url() ?>plugins/qrcode_generator/qrcode.min.js"></script>
<script src="<?= base_url() ?>plugins/barcode_generator/JsBarcode.all.min.js"></script>

<!-- <script src="<?= base_url() ?>plugins/tablexport/FileSaver.min.js"></script>
<script src="<?= base_url() ?>plugins/tablexport/Blob.min.js"></script>
<script src="<?= base_url() ?>plugins/tablexport/xls.core.min.js"></script>
<script src="<?= base_url() ?>plugins/tablexport/tableexport.js"></script> -->

<script type="text/javascript">
    // setInterval(function() {
    //     $.post("<?= base_url('Main/allow') ?>");
    // }, 3000)
    var confirmP = "";
    var rmvP = "";
    var refrmvP = "";
    var stq = "";
    var pwd = "";
    var entryId = 0;
    var addItemId = 0;
    var validatorC = 0;
    var valid = 0;
    var grdlvl = 0;
    var rmid = 0;
    var rsid = 0;
    var sec_name = "";
    var rssaid = 0;
    var logsHS = 0;
    var adviser = 0;
    var bTmp = 0;
    var cTmp = 0;
    var filename = "";

    var vars = {};
    $(function() {
        $('.select2').select2()
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        });
    });

    function check_all(a) {
        if ($("#" + a).prop('checked') == true) {
            $("#tblLearnersList").find("input[type='checkbox']").each(function() {
                $("." + a).prop("checked", true);
            });
        } else {
            $("#tblLearnersList").find("input[type='checkbox']").each(function() {
                $("." + a).prop("checked", false);
            });
        }
    }

    function clear_form1() {
        clear_form("form_save_dataEnrollmentInfo");
        defaultImg('pic', 'previewPic', 'imgtargetLink', 'MALE');
        $("#form_save_dataEnrollmentInfo #ersid").val(rsid)
    }

    function clear_form(form_id) {
        $("#" + form_id)[0].reset();
        $("#" + form_id).find("input[type='hidden']").each(function() {
            $(this).val("");
        });
        $("#" + form_id).find("input[type='checkbox']").each(function() {
            $(this).attr("checked", false);
        });
        $("#" + form_id).find("select").each(function() {
            $(this).trigger("change");
        });

        $("#" + form_id + " .submitBtnPrimary").attr("disabled", false);
        $("#" + form_id + " .submitBtnPrimary").html("Save Data");
    }

    function getQoute(a){
        var str = a;
        return str ? str.replace(/\$/g, '"').replace(/\?/g, "'") : null;
    }

    function getDetails(a, b, c, d) {
        $.each(b, function(k, vv) {
            $(d + "form_save_data" + a).each(function() {
                v = getQoute(vv);
                $("[name='" + k + "']").val(v);
                $("[class='" + k + "']").html(v);
                $("[name='" + k + "']").trigger("change");
                if (k == "img_path") {
                    // console.log(v)
                    if (v == null) {
                        defaultImg('pic', 'previewPic', 'imgtargetLink', 'MALE');
                    } else {
                        var reader = new FileReader();
                        $("[name=pic]").val("");
                        // $("[name=previewPic]").attr("src", "<?= base_url() ?>" + v);
                        $("[name=previewPic]").attr("src", v);
                        reader.onload = function(e) {
                            document.getElementById(b).src = e.target.result;
                        };
                    }
                }
            });
        });
    }

    function unenroll() {
        validate("form_save_dataUnenrollConfirm");
        if (valid != 0) {
            fillIn();
            return false;
        }
        // a = $("#form_save_dataUnenrollConfirm .submitBtnPrimary").text();
        // $("#form_save_dataUnenrollConfirm .submitBtnPrimary").attr("disabled", true);
        // $("#form_save_dataUnenrollConfirm .submitBtnPrimary").html("<span class=\"fa fa-spinner fa-pulse\"></span>");
        s = $("#form_save_dataUnenrollConfirm").serialize();
        $.post("<?= base_url($uri . '/Dataentry/learnerUnenroll') ?>", s,
            function(data) {
                var result = JSON.parse(data);
                if (result.success == true) {
                    successAlert(result.message);
                    // getTable("LearnersList", 0, -1);
                    $("#modalLearnersUnenroll").modal('hide');
                    // getTable("LearnersList", 0, -1);
                    getTable("AssignedSectionList", 0, 10);
                    // setTimeout(function() {
                    //     $(".form_save_dataSectionList #slctRmRadio" + rsid + rssaid).attr("checked", true).trigger("click");
                    // }, 1500);
                } else if (result.success == false) {
                    failAlert(result.message);
                    // getTable("LearnersList", 0, -1);
                }
            }
        ).then(function() {
            // s2 == 1 ? $("#form_save_data" + formId + " .select" + getList).select2() : "";
        });
    }

    function sectionList(){
        let gg = $("#form_save_dataSectionListFilterTransfer .selectGradeLevelList").val();
        let $x = $("#form_save_dataSectionListFilterTransfer .sectionListTransfer");
        $.get("<?= base_url($uri . "/Getdata/getSectionList") ?>", { g: gg },
        function(data) {
            var d = JSON.parse(data);
            $x.empty();
            $x.append("<option value=''>SELECT</option>");
            for (var i = 0; i < d["data"].length; i++) {
                $x.append("<option value='"+d["data"][i]["a"]+"'>"+d["data"][i]["b"]+"</option>");
            }
        }).done(function() {});
    }

    function BatchUnenroll() {
        var selectedValues = $("#tblLearnersList").find("[name='learnerCheckBox[]']:checked").map(function() {
            return $(this).val();
        }).get();

        // 'selectedValues' now contains an array of selected values
        if (selectedValues.length < 1) {
            existAlert('Please select Learner');
            return false;
        }else{
            $("#modalLearnersBatchUnenroll .listBatchUnenroll").empty();
            for(i=0;i<selectedValues.length;i++){
                inputString = selectedValues[i];
                resultArray = inputString.split("_&&_");
                $("#modalLearnersBatchUnenroll .listBatchUnenroll").
                append('<span class="badge badge-xs badge-info">'+ resultArray[1] +'</span> ');
            }
        }

        if (!$("#modalLearnersBatchUnenroll").is(':visible')) {
            $("#modalLearnersBatchUnenroll").modal('show');
            return false;
        }

        if ($("#modalLearnersBatchUnenroll .passwordUnenroll").val()=='') {
            warningAlert("Please enter password!");
            $("#modalLearnersBatchUnenroll .passwordUnenroll").focus();
            return false;
        }
        // a = $("#form_save_dataUnenrollConfirm .submitBtnPrimary").text();
        // $("#form_save_dataUnenrollConfirm .submitBtnPrimary").attr("disabled", true);
        // $("#form_save_dataUnenrollConfirm .submitBtnPrimary").html("<span class=\"fa fa-spinner fa-pulse\"></span>");
        // s = $("#form_save_dataUnenrollConfirm").serialize();
        
        $.post("<?= base_url($uri . '/Dataentry/learnerUnenroll') ?>", {b:selectedValues,password:$("#modalLearnersBatchUnenroll .passwordUnenroll").val()},
            function(data) {
                var result = JSON.parse(data);
                if (result.success == true) {
                    successAlert(result.message);
                    $("#modalLearnersBatchUnenroll .passwordUnenroll").val('');
                    $("#modalLearnersBatchUnenroll").modal('hide');
                    // getTable("AssignedSectionList", 0, 10);
                    var page = $('#tblAssignedSectionList').DataTable().page();
                    var search = $('#tblAssignedSectionList').DataTable().search();
                    getTable("AssignedSectionList", 0, 10, search, page);
                } else if (result.success == false) {
                    failAlert(result.message);
                }
            }
        ).then(function() {
            // s2 == 1 ? $("#form_save_data" + formId + " .select" + getList).select2() : "";
        });
    }

    function BatchTransfer() {
        var selectedValues = $("#tblLearnersList").find("[name='learnerCheckBox[]']:checked").map(function() {
            return $(this).val();
        }).get();


        // 'selectedValues' now contains an array of selected values
        if (selectedValues.length < 1) {
            existAlert('Please select Learner');
            return false;
        }else{
            $("#modalLearnersBatchTransfer .listBatchTransfer").empty();
            for(i=0;i<selectedValues.length;i++){
                inputString = selectedValues[i];
                resultArray = inputString.split("_&&_");
                $("#modalLearnersBatchTransfer .listBatchTransfer").
                append('<span class="badge badge-xs badge-info">'+ resultArray[1] +'</span> ');
            }
        }

        if (!$("#modalLearnersBatchTransfer").is(':visible')) {
            $("#modalLearnersBatchTransfer").modal('show');
            return false;
        }

        if (!$("#form_save_dataSectionListFilterTransfer .sectionListTransfer").val()) {
            existAlert('Please select Section');
            return false;
        }

        if ($("#modalLearnersBatchTransfer .passwordTransfer").val()=='') {
            warningAlert("Please enter password!");
            $("#modalLearnersBatchTransfer .passwordTransfer").focus();
            return false;
        }
        // a = $("#form_save_dataTransferConfirm .submitBtnPrimary").text();
        // $("#form_save_dataTransferConfirm .submitBtnPrimary").attr("disabled", true);
        // $("#form_save_dataTransferConfirm .submitBtnPrimary").html("<span class=\"fa fa-spinner fa-pulse\"></span>");
        // s = $("#form_save_dataTransferConfirm").serialize();
        
        $.post("<?= base_url($uri . '/Dataentry/learnerTransfer') ?>", {b:selectedValues,
                                password:$("#modalLearnersBatchTransfer .passwordTransfer").val(),
                                g:$("#form_save_dataSectionListFilterTransfer .sectionListTransfer").val()},
            function(data) {
                var result = JSON.parse(data);
                if (result.success == true) {
                    successAlert(result.message);
                    $("#modalLearnersBatchTransfer .passwordTransfer").val('');
                    $("#modalLearnersBatchTransfer").modal('hide');
                    // getTable("AssignedSectionList", 0, 10);
                    var page = $('#tblAssignedSectionList').DataTable().page();
                    var search = $('#tblAssignedSectionList').DataTable().search();
                    getTable("AssignedSectionList", 0, 10, search, page);
                } else if (result.success == false) {
                    failAlert(result.message);
                }
            }
        ).then(function() {
            // s2 == 1 ? $("#form_save_data" + formId + " .select" + getList).select2() : "";
        });
    }

    function transfer() {
        validate("form_save_dataTransferConfirm");
        if (valid != 0) {
            fillIn();
            return false;
        }
        // a = $("#form_save_dataTransferConfirm .submitBtnPrimary").text();
        // $("#form_save_dataTransferConfirm .submitBtnPrimary").attr("disabled", true);
        // $("#form_save_dataTransferConfirm .submitBtnPrimary").html("<span class=\"fa fa-spinner fa-pulse\"></span>");
        s = $("#form_save_dataTransferConfirm").serialize();
        $.post("<?= base_url($uri . '/Dataentry/learnerTransfer') ?>", s,
            function(data) {
                var result = JSON.parse(data);
                if (result.success == true) {
                    successAlert(result.message);
                    // getTable("LearnersList", 0, -1);
                    $("#modalLearnersTransfer").modal('hide');
                    // getTable("LearnersList", 0, -1);
                    getTable("AssignedSectionList", 0, 10);
                    // setTimeout(function() {
                    //     $(".form_save_dataSectionList #slctRmRadio" + rsid + rssaid).attr("checked", true).trigger("click");
                    // }, 1500);
                } else if (result.success == false) {
                    failAlert(result.message);
                    // getTable("LearnersList", 0, -1);
                }
            }
        ).then(function() {
            // s2 == 1 ? $("#form_save_data" + formId + " .select" + getList).select2() : "";
        });
    }

    function imageView(a, b, c) {
        // var reader = new FileReader();
        // var fh = filehandler($('#' + a).val());
        // reader.onload = function(e) {
        //     document.getElementById(b).src = e.target.result;
        // };
        // reader.readAsDataURL(document.getElementById(a).files[0]);

        var fileInput = $("[name=" + a + "]")[0]; // Get the file input element
        var file = fileInput.files[0]; // Get the selected file

        if (file.size > 25 * 1024 * 1024) {
            // Picture size is above 2MB
            alert("Picture must be less than 2MB");
            return; // You can handle this case according to your requirements
        }

        var reader = new FileReader();

        reader.onload = function(e) {
            $("[name=" + b + "]").attr('src', e.target.result); // Set the source of the image element
        };

        reader.readAsDataURL(file);
    }

    function defaultImg(a, b, c, d) {
        var reader = new FileReader();
        $("[name=pic]").val("");
        // img = (d == 'FEMALE' ? 'defaultf.png' : 'defaultm.png');
        $("[name=previewPic]").attr("src", "<?= $system_svg_1x1 ?>");
        reader.onload = function(e) {
            document.getElementById(b).src = e.target.result;
        };
    }

    function filehandler(item) {
        var ext = item.split('.').pop().toLowerCase();
        if ($.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
            item = 1;
        } else {
            item = 0;
        }
        return item;
    }

    function validate(form_id) {
        let invalid = 0;
        $($("#" + form_id).find("select").get().reverse()).each(function() {
            var name = $(this).attr("name");
            var j = clean($(this).attr("name"));
            var nr = $(this).attr("nr");
            var multiple = $(this).attr("multiple");

            if (nr != 1) {
                if (!$(this).val() || $(this).val() == 'null') {
                    $(this).focus().addClass("is-invalid");
                    $("#" + form_id + " select[name='" + name + "']").focus().next().find('.select2-selection').addClass('has-error');
                    $("#" + form_id + " ." + j).addClass('border-danger');
                    invalid++;
                } else {
                    $(this).removeClass("is-invalid");
                    $("#" + form_id + " select[name='" + name + "']").focus().next().find('.select2-selection').removeClass('has-error');
                    $("#" + form_id + " ." + j).removeClass('border-danger');
                }
            }
        });

        $($("#" + form_id).find("input").get().reverse()).each(function() {
            if ($("#" + form_id + ' input[type="search"]')) {
                // return 0;
            }
            if ($("#" + form_id + ' input[type="text"]')) {
                var name = clean($(this).attr("name"));
                var nr = $(this).attr("nr");

                if (name == null) {} else if (nr != 1) {
                    if (!$(this).val()) {
                        $(this).focus().addClass("is-invalid");
                        $("#" + form_id + " ." + name).addClass('border-danger');
                        invalid++;
                    } else {
                        $(this).removeClass("is-invalid");
                        $("#" + form_id + " ." + name).removeClass('border-danger');
                    }
                }
            }
        });
        valid = invalid;
    }

    function reportForm(formId) {
        $("#form_report_data" + formId + " .submitBtnPrimary").attr("disabled", true);
        $("#form_report_data" + formId + " .submitBtnPrimary").html("<span class=\"fa fa-spinner fa-pulse\"></span>");
        var table, table_data = $("#tblReport" + formId).DataTable({
            "iDisplayLength": -1,
            ajax: {
                url: "<?= base_url($uri . '/report/report') ?>" + formId,
                icon: "POST",
                data: function(d) {
                    d.a = $("#form_report_data" + formId).serialize();
                    return table_data;
                }
            },
            "initComplete": function(settings, json) {
                json.data.length == 0 ? failAlert("No Data found!") : $("#modalReport" + formId).modal("show");
                $("#tblReport" + formId).DataTable().destroy();
                $("#form_report_data" + formId + " .submitBtnPrimary").attr("disabled", false);
                $("#form_report_data" + formId + " .submitBtnPrimary").html("Go!");
                if (formId == "InvPO") {
                    $("#headerReport" + formId).text("Inventory & Purchase Order as of " + $("[name='filterInvPOfromDate'").val() + " - " + $("[name='filterInvPOtoDate'").val());
                }
                if (formId == "StckPR") {
                    $("#headerReport" + formId).text("Stocks & Purchase Request as of " + $("[name='filterStckPRfromDate'").val() + " - " + $("[name='filterStckPRtoDate'").val());
                }
            }
        });
    }

    function saveForm(formId, tblId, tbl, dtd, pl) {
        var saveData = {
            clearForm: false,
            resetForm: false,
            beforeSubmit: function(e) {
                validate("form_save_data" + formId);
                if (valid != 0) {
                    fillIn();
                    return false;
                }
                // $("#modal" + formId + " .tab-content").hide();
                // $("#modal" + formId + " .overlay").show();
                a = $("#form_save_data" + formId + " .submitBtnPrimary").text();
                // if (formId != "GradesList") {
                $("#form_save_data" + formId + " .submitBtnPrimary").attr("disabled", true);
                $("#form_save_data" + formId + " .submitBtnPrimary").html("<span class=\"fa fa-spinner fa-pulse\"></span>");
                // }
            },
            success: function(data) {
                var d = JSON.parse(data);
                if (d.success == true) {
                    successAlert("Successfully Saved!");
                    // alert(formId)
                    if ((formId == "GradesList") || (formId == "GradesPSList")) {} else {
                        $("#modal" + formId).modal('hide');
                        clear_form("form_save_data" + formId);
                    }

                    if (formId == "GradesList") {
                        getGradesListFN();
                    }

                    if (formId == "GradesPSList") {
                        getGradesPSListFN();
                    }

                    if (tblId.length > 1) {
                        for (var i = 0; i < tblId.length; i++) {
                            getTable(tblId[i], dtd, pl);
                        }
                    }
                    tbl ? removeAllItemList("tbl" + tbl) : null;
                    tbl ? $("#btn" + tbl).trigger("click") : null;

                    if (formId == 'EnrollmentInfo') {
                        $('#modalEnrollment').modal('hide');
                    }
                } else if (d.exist == true) {
                    existAlert("Person already exist!<br/>You can search and add TEST RESULT");
                } else if (d.existCode == true) {
                    existAlert("Code already taken!<br/>by: " + d.existPerson);
                } else if (d.message) {
                    failAlert(d.message);
                } else {
                    failAlert("Something went wrong!");
                }
                // $("#modal" + formId + " .overlay").hide();
                $("#modal" + formId + " .tab-content").show();
                $("#form_save_data" + formId + " .submitBtnPrimary").attr("disabled", false);
                $("#form_save_data" + formId + " .submitBtnPrimary").html(a);
                // getTable("LearnersList", 0, -1);
                var page = $('#tblAssignedSectionList').DataTable().page();
                var search = $('#tblAssignedSectionList').DataTable().search();
                getTable("AssignedSectionList", 0, 10, search, page);
                // setTimeout(function() {
                // $(".form_save_dataSectionList #slctRmRadio" + rsid + rssaid).attr("checked", true).trigger("click");
                // }, 1500);

            }
        };
        $("#form_save_data" + formId).ajaxForm(saveData);
    }

    function getTable(tableId, dtd, pl, search, ppage) {
        var drawCounter = 0;
        $("#modal" + tableId + " .content").hide();
        $("#modal" + tableId + " .overlay").show();
        setTimeout(() => {
            $("#tbl" + tableId).DataTable().destroy();
            var table, table_data = $("#tbl" + tableId).DataTable({
                // "order": [
                //     [0, "asc"]
                // ],
                dom: 'Bfrtip',
                buttons: tableId == 'SearchEnrollLearnersList' ? [{
                        text: "<i class='fa fa-check text-success'></i> Enroll",
                        action: function(e, dt, node, config) {
                            validateTable(tableId);
                        }
                    }] : [] &&
                    tableId == 'LearnersList' ? [
                        // {
                        //     text: "<i class='fa fa-cog'></i> Account",
                        //     action: function(e, dt, node, config) {
                        //         validateTable(tableId);
                        //     }
                        // }, 
                        {
                            extend: 'print',
                            text: '<i class="fa fa-print"></i> PRINT LIST',
                            header: "_excel"

                        },
                        // {
                        //     text: '<i class="fa fa-user"></i> Preview ID',
                        //     action: function(e, dt, node, config) {
                        //         PreviewID();
                        //     }

                        // }
                    ] : [] &&
                    tableId == 'AllStudentLogs' ? [{
                        extend: 'print',
                        text: '<i class="fa fa-print"></i> Print',
                        header: "_excel"

                    }, {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel"></i> Export Excel',
                        header: "_excel"

                    }] : [],

                searching: ((tableId == "GradesList" || tableId == "GradesPSList") ? false : true),
                // scrollX: true,
                // scrollY: "500px",
                // scrollY: tableId == 'GradesList' ? "500px" : null,
                // scrollCollapse: true,
                "search": {
                    "search": search ?? "",
                },
                "info": pl == -1 ? false : true,
                "paging": pl == -1 ? false : true,
                "ordering": pl == -1 ? false : true,
                "oLanguage": {
                    "sSearch": ""
                },
                "processing": true,
                "serverSide": true,
                language: {
                    searchPlaceholder: "Search...",
                    info: '<p class="text-right pr-2 mt-n3 mb-n1">Displaying _START_ to _END_ of _TOTAL_ entries</p>',
                },
                // pagingType: 'simple',
                lengthMenu: [
                    [2, 4, 8, -1],
                    [2, 4, 8, "All"]
                ],
                pageLength: pl,

                ajax: {
                    url: "<?= base_url($uri . '/getdata/get') ?>" + tableId,
                    type: "POST",
                    cache: true,
                    data: function(d) {
                        d.rsid = rsid;
                        d.rssaid = rssaid;
                        d.by = $("#searchby").val();
                        d.key = $("#keyword").val();

                        drawCounter++;
                        d.length = pl;
                        d.draw = drawCounter;
                        d.search.value = $('#tbl' + tableId + '_filter input').val();

                        //filter list of section for teacher
                        d.k12 = $("#form_save_dataSectionListFilter .selectK12List").val();
                        d.prgrm_strnd = $("#form_save_dataSectionListFilter .selectStrandList").val();
                        d.grade = $("#form_save_dataSectionListFilter .selectGradeLevelList").val();
                    }
                },
                // "pageLength": pl,

                fnInitComplete: function(oSettings, json) {
                    if (tableId == "Honors") {
                        $('[data-toggle="tooltip"]').tooltip();
                    }
                    // if (tableId == "AssignedSectionList") {
                    //     if (rssaid != 0) {
                    //         $(".form_save_dataSectionList #slctRmRadio" + rsid + rssaid).attr("checked", true).trigger("click");
                    //     } else {
                    //         $(".form_save_dataSectionList .slctdRadioAdvisory").attr("checked", true).trigger("click");
                    //     }
                    // }

                    if (tableId == "GradesList") {
                        $("#modal" + tableId + " .q1c").empty();
                        $("#modal" + tableId + " .q2c").empty();
                        $("#modal" + tableId + " .q3c").empty();
                        $("#modal" + tableId + " .q4c").empty();
                        if (json && json["details"]) {
                            if (json["details"]["q1c"]) {
                                $("#modal" + tableId + " .q1c").html(json["details"]["q1c"])
                            }
                            if (json["details"]["q2c"]) {
                                $("#modal" + tableId + " .q2c").html(json["details"]["q2c"])
                            }
                            if (json["details"]["q3c"]) {
                                $("#modal" + tableId + " .q3c").html(json["details"]["q3c"])
                            }
                            if (json["details"]["q4c"]) {
                                $("#modal" + tableId + " .q4c").html(json["details"]["q4c"])
                            }
                        }
                        $('[data-toggle="tooltip"]').tooltip()
                    }

                    if (tableId == "GradesPSList") {
                        $('[data-toggle="tooltip"]').tooltip()
                    }

                    if (tableId == "GradesSMEAList") {
                        // $("#modalDLLearnerGradesSP").modal("show");
                        downloadExcel('tbl' + tableId, filename);

                        $(".downloadform").attr("disabled", false);
                        $(".downloadform").html("<span class=\"fa fa-download\"></span> Download form");
                    }
                    $("#modal" + tableId + " .content").show();
                    $("#modal" + tableId + " .overlay").hide();
                }
            });

            $("#tbl" + tableId).on('draw.dt', function() {
                // Enable the search button and adjust styling
                $(".searchBtn").attr("disabled", false);
                $(".searchBtn").html("<span class=\"fa fa-search\"></span>");

                // Check if DataTable needs to be destroyed (dtd == 1)
                dtd == 1 ? $("#tbl" + tableId).DataTable().destroy() : "";

            });
            $("#tbl" + tableId + "_filter").addClass("row");
            $("#tbl" + tableId + "_filter label").css("width", "99%");
            $("#tbl" + tableId + "_filter .form-control-sm").css("width", "99%");
        }, 100);
        setTimeout(() => {
            // Trigger the click event on the collapse element
            // $(".collapse" + tableId).trigger('click');

            // Set the initial page to page number 2
            // ppage = typeof ppage !== 'undefined' ? ppage : 0;
            var table = $("#tbl" + tableId).DataTable();
            table.page(ppage).draw('page');
        }, 1200);
        setTimeout(() => {
            if (tableId == "AssignedSectionList") {
                if (rssaid != 0) {
                    $(".form_save_dataSectionList #slctRmRadio" + rsid + rssaid).attr("checked", true).trigger("click");
                } else {
                    $(".form_save_dataSectionList .slctdRadioAdvisory").attr("checked", true).trigger("click");
                }
            }
        }, 2000);

    }

    function validateTable(table_id) {
        var c = $("#tbl" + table_id + ' input:checkbox:checked').length;
        if (c < 1) {
            existAlert("Please Select Person!");
        } else {
            if (table_id == 'SearchEnrollLearnersList') {
                searchBatchEnroll(table_id);
            } else if (table_id == 'LearnersList') {
                $("#modal" + table_id).modal('show');
            }
        }
    }

    function batchUpdateAccount(a) {
        var b = $("#form" + a).serialize();
        var d = $("[name='accountSettings']").val();
        $.post("<?= base_url($uri . "/Dataentry/saveBatchUpdateAccount") ?>", {
                c: b,
                e: d,
            },
            function(data) {
                logsHideShow()
                var d = JSON.parse(data);
                if (d.success == true) {
                    successAlert(d.message);
                    tblReload(a);
                    $("#modalLearnersList").modal('hide');
                    setTimeout(function() {
                        logsHideShow()
                        $("#learnerCheckBox").prop("checked", false);
                    }, 1500);
                    // tblReload('LearnersList');
                    // // tblReload('AssignedSectionList');
                    // // getTable("LearnersList", 0, -1);
                    // getTable("AssignedSectionList", 0, -1);
                    // setTimeout(function() {
                    //     $(".form_save_dataSectionList #slctRmRadio" + rsid + rssaid).attr("checked", true).trigger("click");
                    // }, 1500);
                } else {
                    failAlert(d.message);
                }
            }).done(function() {});
    }

    function searchBatchEnroll(a) {
        var b = $("#form" + a).serialize();
        $.post("<?= base_url($uri . "/Dataentry/saveSearchBatchEnroll") ?>", {
                c: b,
                d: rsid,
            },
            function(data) {
                var d = JSON.parse(data);
                if (d.success == true) {
                    successAlert("Successfully Saved!");
                    tblReload(a);
                    tblReload('LearnersList');
                    // tblReload('AssignedSectionList');
                    // getTable("LearnersList", 0, -1);
                    getTable("AssignedSectionList", 0, 10);
                    setTimeout(function() {
                        $(".form_save_dataSectionList #slctRmRadio" + rsid + rssaid).attr("checked", true).trigger("click");
                    }, 1500);
                } else {
                    failAlert("Something went wrong!");
                }
            }).done(function() {});
    }



    function tblReload(tableId) {
        $("#tbl" + tableId).DataTable().ajax.reload(function() {
            $("." + tableId).hide();
        });
    }

    function getSbjctAssPrsnnlFN(tableId, a, b) {
        grdlvl = a;
        rmid = b;
        tblReload(tableId);
    }

    function getLearnersListFN(tableId, a, b, c, d) {
        rsid = a;
        rssaid = b;
        adviser = c;
        sec_name = d;
        $("." + tableId).show();
        $("#tbl" + tableId + " tbody").empty();
        $("#form_save_dataEnrollmentInfo #ersid").val(a)
        resetHide(tableId);
        tblReload(tableId, c);

        if (adviser == "t") {
            getTable("Honors", 0, -1);
            $(".header1").show();
            // $(".header2").removeClass("col-lg-12");
            // $(".header2").addClass("col-lg-12");
        } else {
            $(".header1").hide();
            // $(".header2").removeClass("col-lg-12");
            // $(".header2").addClass("col-lg-12");
        }
        tblDT = $('#tbl' + tableId).DataTable();
        if (adviser === "t") {
            getTable("LearnersList", 0, -1);
        } else if (tblDT.button().length == 1) {
            tblDT.button(0).destroy();
        }
    }

    function getGradesListFN() {
        $("#form_save_dataGradesList .submitBtnPrimary").attr("disabled", false);
        $("#form_save_dataGradesList .submitBtnPrimary").html("Save Grades");
        getTable("GradesList", 0, -1);
    }

    function getGradesPSListFN() {
        $("#form_save_dataGradesPSList .submitBtnPrimary").attr("disabled", false);
        $("#form_save_dataGradesPSList .submitBtnPrimary").html("Save Grades");
        getTable("GradesPSList", 0, -1);
    }

    function getSbjctAssPrsnnl(tableId) {
        var table, table_data = $("#tbl" + tableId).DataTable({
            "order": [
                [0, "asc"]
            ],
            dom: 'Bfrtip',
            buttons: [
                'pageLength',

            ],
            "oLanguage": {
                "sSearch": ""
            },
            language: {
                searchPlaceholder: "Search...",
            },
            pageLength: -1,
            lengthMenu: [
                [-1],
                ["Show all rows"]
            ],
            ajax: {
                url: "<?= base_url($uri . '/getdata/get') ?>" + tableId,
                type: "POST",
                data: function(d) {
                    d.grdlvl = grdlvl;
                    d.rmid = rmid;
                }
            }
        });
        $("#tbl" + tableId).on('draw.dt', function() {
            $("#form_save_data" + tableId + " .select" + tableId).select2();
            grdlvl != 0 ? $("#modal" + tableId).modal('show') : "";
        });
    }

    function allStudentLogs(a) {
        search = a ?? "";
        getTable("AllStudentLogs", 0, 10, search);
        $("#modalAllStudentLogs").modal("show");
    }

    function logsHideShow() {
        $("#tblLearnersList .logs_account").toggle("slow", function() {
            if ($("#tblLearnersList .logs_account").is(":visible")) {
                logsHS = 1
                $(".ViewButtonsToggle").removeClass("fa-plus bg-success");
                $(".ViewButtonsToggle").addClass("fa-minus bg-gray");
            } else {
                $("#tblLearnersList .normal_view").is(":visible")
                logsHS = 0
                $(".ViewButtonsToggle").removeClass("fa-minus bg-gray");
                $(".ViewButtonsToggle").addClass("fa-plus bg-success");
            }
        });
        $("#tblLearnersList .normal_view").toggle("slow", function() {
            if ($("#tblLearnersList .normal_view").is(":visible")) {} else {
                $("#tblLearnersList .logs_account").is(":visible")
            }
        });
    }

    function resetHide(t) {
        if (logsHS == 1) {
            logsHideShow();
            $('.viewButtons').slideToggle();
            $("#tblLearnersList").find("input[type='checkbox']").each(function() {
                $(this).prop("checked", false);
            });
        }
    }

    function learnerAccnt(a, b) {
        $.post("<?= base_url($uri . '/Dataentry/learnerAccount') ?>", {
                a: a,
                b: b,
            },
            function(data) {
                var result = JSON.parse(data);
                if (result.success == true) {
                    successAlert("Successfully Saved!");
                    getTable("LearnersList", 0, -1);
                    setTimeout(function() {
                        logsHideShow()
                    }, 1500);
                }
            }
        ).then(function() {
            // s2 == 1 ? $("#form_save_data" + formId + " .select" + getList).select2() : "";
        });
    }

    function getLocation(a, b, c) {
        // clearLoc(b, c);
        // let form = 'form_save_data' + c;
        // let d = $('#' + form + ' .select' + a).val();
        // let ab = d == '' || d == null ? 0 : d;
        // getFetchList(c, b, null, 1, {
        //     v: ab
        // });
        for (var i = 0; i < a.length; i++) {
            clearLoc(b[i], c);
            let form = 'form_save_data' + c;
            let d = $('#' + form + ' .select' + a[i]).val();
            let ab = d == '' || d == null ? 0 : d;
            getFetchList(c, b[i], null, 1, {
                v: ab
            }, 1, 1);
            // getFetchList(c, b[i], null, 1, e);
        }
    }

    function clearLoc(a, b) {
        let form = 'form_save_data' + b;
        $("#" + form + " .select" + a).empty();
    }

    function getFetchList(formId, getList, getQ, s2, where, sel, e) {
        var q = getQ ? getQ : getList;
        $("#form_save_data" + formId + " .select" + getList).empty();
        $.post("<?= base_url($uri . '/getdata/get') ?>" + q, where,
            function(data) {
                var result = JSON.parse(data);
                (sel == 0 || e == 0) ? $("#form_save_data" + formId + " .select" + getList).append("<option value=''>SELECT</option>"): "";
                for (var i = 0; i < result["data"].length; i++) {
                    $("#form_save_data" + formId + " .select" + getList).append("<option value='" + result["data"][i]['id'] + "'>" + result["data"][i]['item'] + "</option>");
                }
            }
        ).then(function() {
            s2 == 1 ? $("#form_save_data" + formId + " .select" + getList).select2() : "";
        });
    }

    $('#import_form').on('submit', function(event) {

        let formData = new FormData(this);
        formData.append('rsid', rsid);

        $("#import_form .overlay").slideDown();

        event.preventDefault();
        // $(".submitBtnUpload").attr("disabled", true);
        $.ajax({
            url: "<?= base_url($uri . '/Dataentry/saveImportEnrollmentInfo'); ?>",
            // method:"POST",
            type: "POST",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            // data: formData,
            // processData: false,
            // contentType: false,
            // xhr: function() {
            //     var xhr = new window.XMLHttpRequest();
            //     xhr.upload.addEventListener('progress', function(evt) {
            //     if (evt.lengthComputable) {
            //         var percentComplete = (evt.loaded / evt.total) * 100;
            //         $('#progress').text(percentComplete.toFixed(2) + '%');
            //         $('#progress').css('width', percentComplete + '%');
            //     }
            //     }, false);
            //     return xhr;
            // },
            success: function(data) {
                // getTable("LearnersList", 0, -1);
                // getTable("AssignedSectionList", 0, 10);
                // setTimeout(function() {
                //     $(".form_save_dataSectionList #slctRmRadio" + rsid + rssaid).attr("checked", true).trigger("click");
                // }, 1500);
                $('#file').val('');
                successAlert("Successfully Saved!");
                $("#import_form .overlay").slideUp();
                var page = $('#tblAssignedSectionList').DataTable().page();
                var search = $('#tblAssignedSectionList').DataTable().search();
                getTable("AssignedSectionList", 0, 10, search, page);
            },
            error: function(data) {
                failAlert("Something went wrong!");
                $("#import_form .overlay").slideUp();
            }
        });
    });

    function customTabViewAllGrades() {
        $("#modalAllGrades .overlay").show();
        $("#modalAllGrades .viewAllGrades").empty();
        if (vars['viewAllGrades' + rsid] != undefined) {
            $("#modalAllGrades .viewAllGrades").html(vars['viewAllGrades' + rsid]);
            $("#modalAllGrades .overlay").hide();
        } else {
            $.get("<?= base_url($uri . "/Getdata/getViewAllGrades") ?>", {
                    a: rsid
                },
                function(data) {
                    var d = JSON.parse(data);
                    vars['viewAllGrades' + rsid] = d;
                    $("#modalAllGrades .viewAllGrades").html(d);
                    $("#modalAllGrades .overlay").hide();
                }).done(function() {});
        }
    }

    function preSbmitGrades(a, b, c) {
        bTmp = b;
        cTmp = c;
        $("#modalLearnersSubmitGrades #qrssa").val(a);
        $("#modalLearnersSubmitGrades").modal("show");
    }

    function submitGrades() {
        validate("form_save_dataSubmitGradesConfirm");
        if (valid != 0) {
            fillIn();
            return false;
        }
        var s = $("#form_save_dataSubmitGradesConfirm").serialize();
        $.post("<?= base_url($uri . '/Dataentry/submitGrades') ?>", {
                c: s,
                e: rssaid
            },
            function(data) {
                var result = JSON.parse(data);
                if (result.success == true) {
                    successAlert(result.message);
                    var rm = $("#modalLearnersSubmitGrades .remarks").val();
                    $("#modalGradesList .q" + bTmp + "c").html('<span ' +
                        " class='badge w-100 text-sm bg-navy' data-toggle='tooltip' data-placement='bottom' data-html='true' title='<em>Message:</em> <b>" + rm + "</b>'>" +
                        "<b>FOR APPROVAL Q" + bTmp + " - " + cTmp + "%</b>" +
                        (rm ? '<i class="fa fa-envelope float-right text-yellow"></i>' : '') +
                        "</span>");
                    $("#modalLearnersSubmitGrades").modal('hide');
                    $("#modalLearnersSubmitGrades .remarks").val("");
                    $('[data-toggle="tooltip"]').tooltip();
                } else if (result.success == false) {
                    failAlert(result.message);
                }
            }
        ).then(function() {});
    }

    function maxInput(a, c) {
        var b = $("#" + a).val();
        if (b > 100 || b == 0) {
            $("#" + a).val("");
        } else {

        }
    }

    // function autoavg(a) {
    //     console.log('a')
    //     $(this).text("zz")
    // }

    $("[type='number']").keydown(function(e) {
        e = e || window.event;
        d = e.keyCode;
        if (d == '38' || d == '40' || d == '189' || d == '187' || d == '69') {
            e.preventDefault();
        }
    });

    function showTableHonors(a) {
        let i;
        let cc = 1;
        $("#tblHonorsList tbody").empty();
        if (a.length > 0) {
            for (i = 0; i < a.length; i++) {
                $("#tblHonorsList tbody").append('<tr>' +
                    '<td>' + cc++ + '.   ' + a[i]["l"] + '</td>' +
                    '<td><center>' + a[i]["g"] + '</center></td>' +
                    '</tr>');
            }
        }
    }

    $(".submitBtnGRADE_SLIP").click(function() {
        // alert(rsid)
        var q = $("#form_report_dataGRADE_SLIP #qrtr").val();
        // console.log(q)
        // console.log("aaaaa")
        var g = "";
        var g = "";
        var c = [];
        var e = "";
        var ee = "";
        var grd_q = "";
        var margins = "";

        for (m = 0; m < q.length; m++) {
            var qqq = "Q" + q[m];
            grd_q += '<td style="padding:0px;" width="1">' + qqq + '</td>';
        }

        // for (mr = 0; mr < 20; m++) {
        //     margins += '<td class="border border-transparent" width="5%">  </td>';
        // }


        $("#modalGRADE_SLIP #tblGradesList").empty();
        // $(".submitBtnGRADE_SLIP").attr("disabled", true);
        // $(".submitBtnGRADE_SLIP").html("<span class=\"fa fa-spinner fa-pulse\"></span>");
        $.get("<?= base_url($uri_reports . "/reports/getGRADE_SLIP") ?>", {
                // sy: $("#form_report_dataGRADE_SLIP [name='sy']").val(),
                // qrtr: q,
                rmsid: rsid,
                // report: $("#form_report_dataGRADE_SLIP [name='report']").val(),
            },
            function(data) {
                var d = JSON.parse(data);
                let z = 0;
                let x = 0;
                for (let i = 0; i < d.length; i++) {
                    x = i;


                    $("#modalGRADE_SLIP #tblGradesList").append('<tr>');
                    for (let j = 1; j <= 7; j++) {
                        y = JSON.parse(d[i]["grades"]);
                        let ga = 0;
                        let cga = 0;
                        for (let k = 0; k < y.length; k++) {
                            if (y[k]["q1"] != null) {
                                for (m = 0; m < q.length; m++) {
                                    var qq = "q" + q[m];
                                    ga += y[k][qq];
                                    cga += 1;
                                    e += '<td style="padding:0px 3px 0px 0px;" align="right">' + y[k][qq] + '</td>'; //y[k][qq];
                                    ee += '<td style="padding:0px 3px 0px 0px;" align="right"><b>' + Math.round((ga / cga), 0) + '</b></td>';
                                }

                                p = y[k]["parent_party_id"]; //$value->parent_party_id;
                                t = (p ? '&emsp;' : "");
                                g += ('<tr><td style="padding:0px 0px 0px 3px;">' + t + y[k]["subject_abbr"] + '</td>' + e + '</tr>');
                                if (k == (y.length - 1)) {
                                    g += ('<tr><td style="padding:0px 0px 0px 3px;"><b> AVERAGE </b></td>' + ee + '</tr>');
                                }
                                e = "";
                                ee = "";
                            }
                        }
                        i++;
                        // console.log(i)
                        g_s = d[i - 1]["grade"] + ' - ' + d[i - 1]["sctn_nm"];
                        if (d.length <= i) {
                            break;
                        } else {
                            $("#modalGRADE_SLIP #tblGradesList").append(
                                '<td>' +
                                '<table width="100%" cellspacing="0" style="font-size:10px;border:1.5px dashed #B2B5B8;">' +
                                '<tr><td style="padding:0px;font-size:8px;" colspan="5">' +
                                '<div class="row">' +
                                '<div class="col-2 pl-2" align="center"><img src="<?= $system_svg_1x1 ?>" width="23" height="23"/></div>' +
                                '<div class="col-10">Republic of the Philippines<br>' +
                                '<strong>Agusan National High School</strong><br>' +
                                'Butuan City' +
                                '</div></div>' +
                                '</td></tr>' +
                                '<tr><td style="padding:1px;font-size:8px;" colspan="5">' + g_s + '  |  <?= $sy_; ?>  - Q-' + q + '<br/><b>' + d[i - 1]["lrn"] + '</b></td></tr>' +
                                '<tr><td style="padding:1px;" colspan="5"><b>' + d[i - 1]["last_fullname"] + '</b></td></tr>' +
                                '<tr><td style="padding:0px;">Learing Areas</td>' + grd_q + '</tr>' + g +
                                '</table>' +
                                '</td>');
                            g = "";
                        }
                    }
                    g = "";

                    i--;
                    $("#modalGRADE_SLIP #tblGradesList").append('</tr>');

                }
                // console.log(d[0])
                // console.log(d)
                // JHS GRADE_SLIP DATA FOR THE 4th QUARTER S.Y 2021 - 2022
                $("#modalGRADE_SLIP .header").html("GRADES SLIP AS OF SY: 2020-2019 Q:" + q)
                $("#modalGRADE_SLIP").modal("show");
                // $("#modalGRADE_SLIP .viewGRADE_SLIP").html(d);
                $(".submitBtnGRADE_SLIP").attr("disabled", false);
                $(".submitBtnGRADE_SLIP").html("<span class='fa fa-search'></span>");
            }).done(function() {
            $('[data-toggle="tooltip"]').tooltip()
        });

    });

    function autoSizeFont(text, minFontSize, maxFontSize, maxWidth) {
        var $tempElement = $('<span>').text(text).hide().appendTo('body');
        var fontSize = maxFontSize;

        // Set the initial font size
        $tempElement.css('font-size', fontSize + 'px');

        // Check if the text width exceeds the maximum width
        while ($tempElement.width() > maxWidth && fontSize > minFontSize) {
            fontSize--;
            $tempElement.css('font-size', fontSize + 'px');
        }

        // Clean up the temporary element
        $tempElement.remove();

        return fontSize;
    }

    function closeCardBodyIfOpen() {
        if ($('.collapse-body').css('display') === 'block') {
            $('.collapse-header').addClass('collapsed-card');
            $('.collapse-body').slideUp();
        }
    }

    function PreviewID() {
        // alert(rsid)
        // var q = $("#form_report_dataGRADE_SLIP #qrtr").val();
        $('#loadingModal').modal('show');
        var g = "";
        var g = "";
        var c = [];
        var e = "";
        var ee = "";
        var grd_q = "";

        var minFontSize = 12;
        var maxFontSize = 24;
        var maxWidth = 360;



        // for (m = 0; m < q.length; m++) {
        //     var qqq = "Q" + q[m];
        //     grd_q += '<td style="padding:0px;" width="1">' + qqq + '</td>';
        // }

        $("#modalPreviewID #tblPreviewID").empty();
        // $(".submitBtnPreviewID").attr("disabled", true);
        // $(".submitBtnPreviewID").html("<span class=\"fa fa-spinner fa-pulse\"></span> Preview ID");
        var selectedValues = $("#tblLearnersList").find("[name='learnerCheckBox[]']:checked").map(function() {
            return $(this).val();
        }).get();

        // 'selectedValues' now contains an array of selected values
        if (selectedValues.length < 1) {
            existAlert('Please select Learner')
            // break;
            return false;
        }

        $.get("<?= base_url($uri_reports . "/reports/getPreviewID") ?>", {
                // sy: $("#form_report_dataGRADE_SLIP [name='sy']").val(),
                // qrtr: q,
                s: selectedValues,
                rmsid: rsid,
                // report: $("#form_report_dataGRADE_SLIP [name='report']").val(),
            },
            function(data) {
                var d = JSON.parse(data);
                let z = 0;
                let x = 0;
                for (let i = 0; i < d.length; i++) {
                    x = i;


                    $("#modalPreviewID #tblPreviewID").append('<tr>');
                    for (let j = 1; j <= 1; j++) {
                        // y = JSON.parse(d[i]["grades"]);
                        // let ga = 0;
                        // let cga = 0;
                        // for (let k = 0; k < y.length; k++) {
                        //     if (y[k]["q1"] != null) {
                        //         for (m = 0; m < q.length; m++) {
                        //             var qq = "q" + q[m];
                        //             ga += y[k][qq];
                        //             cga += 1;
                        //             e += '<td style="padding:0px 3px 0px 0px;" align="right">' + y[k][qq] + '</td>'; //y[k][qq];
                        //             ee += '<td style="padding:0px 3px 0px 0px;" align="right"><b>' + Math.round((ga / cga), 0) + '</b></td>';
                        //         }

                        //         p = y[k]["parent_party_id"]; //$value->parent_party_id;
                        //         t = (p ? '&emsp;' : "");
                        //         g += ('<tr><td style="padding:0px 0px 0px 3px;">' + t + y[k]["subject_abbr"] + '</td>' + e + '</tr>');
                        //         if (k == (y.length - 1)) {
                        //             g += ('<tr><td style="padding:0px 0px 0px 3px;"><b> AVERAGE </b></td>' + ee + '</tr>');
                        //         }
                        //         e = "";
                        //         ee = "";
                        //     }
                        // }
                        // alert(i)
                        // i++;
                        let ji = i;
                        // console.log(i)
                        g_s = d[ji]["grade"] + ' - ' + d[ji]["sctn_nm"];
                        let lrn = d[ji]["lrn"];
                        let img_path = d[ji]["img_path"];
                        let full_name = d[ji]["full_name"];
                        let last_name = d[ji]["last_name"];
                        let first_minitial = d[ji]["first_minitial"];
                        let birthdate = d[ji]["birthdate"];
                        let g_sec = d[ji]["grade"] + ' - ' + d[ji]["sctn_nm"];
                        let program = d[ji]["program"];
                        let program_strand = ifnull(d[ji]["program_strand"]);
                        let program_strand_color = ifnull(d[ji]["program_strand_color"]);
                        let grade_k = d[ji]["grade_k"];
                        let color_k = d[ji]["color_k"];
                        let advisory = d[ji]["advisory"];
                        let add_details = d[ji]["address_details"];
                        let other_details = d[ji]["other_details"];
                        let od = JSON.parse(other_details);
                        let incase_emergency = ifnull(od['ioe_name']);
                        let relation = ifnull(od['relation']);
                        let contact_number = ifnull(od['contact_number']);
                        // console.log(od)
                        // alert(i)
                        // if (d.length <= i) {
                        //     break;
                        // } else {

                        // width: 354px; /* 7.5c5 8onverted to pixels */
                        // height: 391px; /* 11cm converted to pixels */
                        //width: 354px;height: 518px;">'+
                        $("#modalPreviewID #tblPreviewID").append(
                            '<td align="left" width="50%" style="border: 1px dashed #000;padding: 2.5px;">'+
                                '<table cellspacing="0" style="font-size:10px;border:2.5px solid #3786A3;background-image: url(<?= $system_bg_l_front_id ?>);background-repeat: no-repeat;background-size: 100% 100%;width: 360px;height: 530px;">'+
                                    '<tr align="center" style="height:5.5rem; border: 1px solid #000;font-size:12px;color: rgba(0, 0, 0, 0);">'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                    '</tr>'+
                                    // '<tr align="center" style="height:1rem; border: 1px solid #000;font-size:5px;">'+
                                    //     '<td class="border border-transparent"> </td>'+
                                    //     '<td colspan="9" class="border border-transparent" style="font-family:Montserrat, sans-serif;font-weight:800;font-size:' + autoSizeFont(grade_k, 8, 12, 100) + 'px;vertical-align:bottom;">'+
                                    //         '<p style="padding:0;margin-bottom:-2px;color:'+color_k+'">'+grade_k+'</p>'+
                                    //     '</td>'+
                                    //     '<td colspan="10" class="border border-transparent"> </td>'+
                                    // '</tr>'+
                                    '<!-- img -->'+
                                    '<tr align="center" style="height:1rem;border: 1px solid #000;font-size:5px;">'+
                                        '<td class="border border-transparent"></td>'+
                                        '<td rowspan="4" colspan="10" class="border border-transparent" style="padding:0;vertical-align: top;width:1rem;">'+
                                            '<img name="previewPic" src="' + img_path + '" class="" alt="User Image" style="border:2px solid #007bff;border-radius:5px;padding:0;margin:0;vertical-align:top;width:100%;height:11.1rem;">'+
                                        '</td>'+
                                        '<td class="border border-transparent"></td>'+
                                        '<td colspan="6" class="border border-transparent" style="font-family:Montserrat, sans-serif;font-weight:bold;font-size:10px;vertical-align:bottom;">'+
                                            // '<p style="padding:0;margin-bottom:-4px;">S.Y. 2023-2024</p>'+
                                            // '<img name="previewPic" src="<?= $system_svg_1x1 ?>" width="85" height="85" alt="User Image" style="padding:0;margin:0;vertical-align:top;">'+
                                        '</td>'+
                                        '<td class="border border-transparent"></td>'+
                                        '<td class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1rem;border: 1px solid #000;font-size:5px;">'+
                                        '<td class="border border-transparent"></td>'+
                                        '<td class="border border-transparent"></td>'+
                                        '<td colspan="6" class="border border-transparent" style="font-family:Montserrat, sans-serif;font-weight:bold;font-size:10px;vertical-align:bottom;">'+
                                            '<p style="padding:0;margin-bottom:-1px;color:#240AA2">S.Y. <?= $sy_ ?></p>'+
                                            // '<img name="previewPic" src="<?= $system_svg_1x1 ?>" width="85" height="85" alt="User Image" style="padding:0;margin:0;vertical-align:top;">'+
                                        '</td>'+
                                        '<td class="border border-transparent"></td>'+
                                        '<td class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1rem; border: 1px solid #000;font-size:7px;">'+
                                        '<td class="border border-transparent"> </td>'+
                                        '<td class="border border-transparent"> </td>'+
                                        '<td colspan="6" style="background-color:#fff">'+
                                            '<div id="qqqq1'+i+'" style="padding:4px;"></div>'+
                                            // '<img name="previewPic" src="<?= $system_svg_1x1 ?>" width="100%" height="100%" class="border border-primary border-2 rounded elevation-1" alt="User Image">'+
                                        '</td>' +
                                        // '<td colspan="8" class="border border-transparent" style="padding:0;height:3.8rem;vertical-align: top;width:1rem;">'+
                                        //     '<img name="previewPic" src="<?= $system_svg_1x1 ?>" width="100%" height="100%" class="border border-primary border-2 rounded elevation-1" alt="User Image">'+
                                        // '</td>'+
                                        '<td class="border border-transparent"> </td>'+
                                        '<td class="border border-transparent"> </td>'+
                                    '</tr>'+
                                    '<!-- barcode -->'+
                                    // '<tr align="center" style="height:1rem; border: 1px solid #000;font-size:7px;">'+
                                    //     '<td class="border border-transparent"> </td>'+
                                    //     '<td class="border border-transparent"> </td>'+
                                    //     '<td colspan="6" style="padding:1px;vertical-align:top;">'+
                                    //         // <img id="bbbb1' + i + '" alt="Barcode Image" width="100%" height="60">
                                    //     '</td>' +
                                    //     // '<td colspan="8" class="border border-transparent" style="padding:0;height:3.8rem;vertical-align: top;width:1rem;">'+
                                    //     //     '<img name="previewPic" src="<?= $system_svg_1x1 ?>" width="100%" height="100%" class="border border-primary border-2 rounded elevation-1" alt="User Image">'+
                                    //     // '</td>'+
                                    //     '<td class="border border-transparent"> </td>'+
                                    //     '<td class="border border-transparent"> </td>'+
                                    // '</tr>'+
                                    '<!-- lrn -->'+
                                    '<tr align="center" style="height:1rem; border: 1px solid #000;font-size:7px;">'+
                                        '<td class="border border-transparent"></td>'+
                                        // '<td class="border border-transparent"></td>'+
                                        '<td colspan="8" class="border border-transparent" style="font-family:Montserrat, sans-serif;font-weight:600;font-size:12px;vertical-align:bottom;">'+
                                            '<p style="padding:0;letter-spacing:1px; display: inline;">' + lrn + '</p><br/>'+
                                            '<p style="padding:0;font-family:Montserrat, sans-serif;font-weight:800;font-size:' + autoSizeFont(grade_k, 8, 12, 100) + 'px;display: inline;color:'+color_k+'">'+grade_k+'</p>'+
                                        '</td>'+
                                        // '<td class="border border-transparent"> </td>'+
                                        '<td class="border border-transparent"> </td>'+
                                    '</tr>'+
                                    '<!-- signature -->'+
                                    // '<tr align="center" style="height:1.5rem; border: 1px solid #000;font-size:7px;">'+
                                    //     '<td class="border border-transparent"> </td>'+
                                    //     // '<td colspan="10" class="border border-transparent"> </td>'+
                                    //     // '<td class="border border-transparent"> </td>'+
                                    //     '<td colspan="8" class="border border-transparent" style="font-family:Montserrat, sans-serif;font-weight:800;font-size:' + autoSizeFont(grade_k, 8, 12, 100) + 'px;vertical-align:bottom;">'+
                                    //         '<p style="padding:0;margin-top:-30px;display: inline;color:'+color_k+'">'+grade_k+'</p>'+
                                    //     '</td>'+
                                    //     // '<td class="border border-transparent"> </td>'+
                                    //     '<td class="border border-transparent"> </td>'+
                                    // '</tr>'+
                                    // '<tr align="center" style="height:.3rem; border: 1px solid #000;font-size:7px;padding:0">'+
                                    //     '<td class="border border-transparent"> </td>'+
                                    //     // '<td colspan="10" class="border border-transparent"> </td>'+
                                    //     '<td class="border border-transparent"> </td>'+
                                    //     '<td colspan="6" class="border border-transparent"> </td>'+
                                    //     '<td class="border border-transparent"> </td>'+
                                    //     '<td class="border border-transparent"> </td>'+
                                    // '</tr>'+
                                    '<!-- lastname -->'+
                                    '<tr align="center" style="height:1.4rem; border: 1px solid #000;padding:0">'+
                                        '<td class="border border-transparent"> </td>'+
                                        '<td colspan="18" class="border border-transparent text-left" style="font-family:Montserrat, sans-serif;color:#102C3D;font-weight:800;font-size:' + autoSizeFont(last_name, 12, 25, 175) + 'px;padding:0 0 3px 0;">'+
                                            '<p class="mb-n4 mt-n1">'+ last_name +'</p>'+
                                        '</td>'+
                                        '<td class="border border-transparent"> </td>'+
                                    '</tr>'+
                                    '<!-- firstname middleinitial -->'+
                                    '<tr align="center" style="height:1rem; border: 1px solid #000;">'+
                                        '<td class="border border-transparent"> </td>'+
                                        '<!-- <td colspan="18" class="border border-transparent"> </td> -->'+
                                        '<td colspan="18" class="border border-transparent text-left" style="font-family:Montserrat, sans-serif;color:#102C3D;font-weight:600;font-size:' + autoSizeFont(first_minitial, 11, 20, 200) + 'px;padding:0;">'+
                                            '<p class="mb-n3">'+ first_minitial +'</p>'+
                                        '</td>'+
                                        '<td class="border border-transparent"> </td>'+
                                    '</tr>'+
                                    '<!-- birthdate League Gothic -->'+
                                    '<tr align="center" style="height:1.2rem; border: 1px solid #000;">'+
                                        '<td class="border border-transparent"> </td>'+
                                        '<td colspan="5" class="border border-transparent text-left" style="font-family:League Gothic, sans-serif;color:#41A1DF;font-size:1.1rem;padding-top:15px;">'+
                                            '<p class="mb-n2">Birthdate:</p>'+
                                        '</td>'+
                                        '<td colspan="13" class="border border-transparent text-left" style="font-family:League Gothic, sans-serif;font-weight:300;font-size:' + autoSizeFont(birthdate, 14, 18, 175) + 'px;padding-top:15px;">'+
                                            '<p class="mb-n2">'+birthdate+'</p>'+
                                        '</td>'+
                                        '<td class="border border-transparent"> </td>'+
                                    '</tr>'+
                                    '<!-- grade section League Gothic -->'+
                                    '<tr align="center" style="height:1.2rem; border: 1px solid #000;">'+
                                        '<td class="border border-transparent"> </td>'+
                                        '<td colspan="5" class="border border-transparent text-left" style="font-family:League Gothic, sans-serif;color:#41A1DF;font-size:1.1rem;padding-top:0px;">'+
                                            '<p class="mb-n2">Grade & Section:</p>'+
                                        '</td>'+
                                        '<td colspan="13" class="border border-transparent text-left" style="font-family:League Gothic, sans-serif;color:forestgreen;font-weight:300;font-size:' + autoSizeFont(g_sec, 15, 18, 200) + 'px;padding:0;">'+
                                            '<p class="mb-n2">'+g_sec+'</p>'+
                                        '</td>'+
                                        '<td class="border border-transparent"> </td>'+
                                    '</tr>'+
                                    '<!-- program/strand -->'+
                                    '<tr align="center" style="height:1.2rem; border: 1px solid #000;">'+
                                        '<td class="border border-transparent"> </td>'+
                                        '<td colspan="5" class="border border-transparent text-left" style="font-family:League Gothic, sans-serif;color:#41A1DF;font-size:1.1rem;padding-top:0px;">'+
                                            '<p class="mb-n2">Program/Strand:</p>'+
                                        '</td>'+
                                        '<td colspan="13" class="border border-transparent text-left" style="font-family:League Gothic, sans-serif;color:forestgreen;font-weight:300;font-size:' + autoSizeFont(program_strand, 15, 18, 200) + 'px;padding:0;">'+
                                            '<p class="mb-n2" style="color:'+program_strand_color+'">'+program_strand+'</p>'+
                                        '</td>'+
                                        '<td class="border border-transparent"> </td>'+
                                    '</tr>'+
                                    '<!-- adviser -->'+
                                    '<tr align="center" style="height:1.2rem; border: 1px solid #000;">'+
                                        '<td class="border border-transparent"> </td>'+
                                        '<td colspan="5" class="border border-transparent text-left" style="font-family:League Gothic, sans-serif;color:#41A1DF;font-size:1.1rem;padding-top:0px;">'+
                                            '<p class="mb-n2">Adviser:</p>'+
                                        '</td>'+
                                        '<td colspan="13" class="border border-transparent text-left" style="font-family:League Gothic, sans-serif;font-weight:300;font-size:' + autoSizeFont(advisory, 14, 18, 175) + 'px;padding:0;">'+
                                            '<p class="mb-n2">'+advisory+'</p>'+
                                        '</td>'+
                                        '<td class="border border-transparent"> </td>'+
                                    '</tr>'+
                                    // '<tr align="center" style="height:1.6rem; border: 1px solid #000;font-size:7px;color: rgba(0, 0, 0, 0);">'+
                                    //     '<td colspan="20" class="border border-transparent"> </td>'+
                                    // '</tr>'+

                                    '<!-- school year -->'+
                                    // '<tr align="center" style="height:.3rem; border: 1px solid #000;font-size:7px;">'+
                                    //     '<td class="border border-transparent"> </td>'+
                                    //     '<td colspan="4" class="border border-transparent" style="font-family:Montserrat, sans-serif;font-weight:800;vertical-align:bottom;">'+
                                    //         '<p style="font-size:.55rem;margin:-.5rem 0 -.1rem 0;">S.Y. 2023-2024</p>'+
                                    //     '</td>'+
                                    //     '<td colspan="14" class="border border-transparent"> </td>'+
                                    //     '<td class="border border-transparent"> </td>'+
                                    // '</tr>'+
                                    '<!-- qrcode -->'+
                                    // '<tr align="center" style="height:4rem; border: 1px solid #000;font-size:7px;">'+
                                    //     '<td class="border border-transparent"> </td>'+
                                    //     '<td colspan="4" class="border border-transparent" style="padding:0;height:3.8rem;vertical-align:top;">'+
                                    //         // '<img name="previewPic" src="<?= $system_svg_1x1 ?>" width="100%" height="100%" class="border border-primary border-2 rounded elevation-1" alt="User Image">'+
                                    //         '<div id="qqqq1'+i+'"></div>'+
                                    //     '</td>'+
                                    //     '<td colspan="14" class="border border-transparent"> </td>'+
                                    //     '<td class="border border-transparent"> </td>'+
                                    // '</tr>'+
                                    '<tr stye="border: 1px solid #000;font-size:1px;height:1rem;">'+
                                        '<td colspan="20"> </td>'+
                                    '</tr>'+
                                '</table>'+
                            '</td>'+

                            '<td align="right" width="50%" style="border: 1px dashed #000;padding: 2.5px;">'+
                                '<table cellspacing="0" style="font-size:10px;border:2.5px solid #3786A3;background-image: url(<?= $system_bg_l_back_id ?>);background-repeat: no-repeat;background-size: 100% 100%;width: 360px;height: 530px;">'+
                                    '<tr align="center" style="height:5.5rem; border: 1px solid #000;font-size:12px;color: rgba(0, 0, 0, 0);">'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                        '<td class="border border-transparent" width="5%">  </td>'+
                                    '</tr>'+
                                    '<!-- qrcode back -->'+
                                    '<tr align="center" style="height:4rem; border: 1px solid #000;font-size:5px;">'+
                                        '<td colspan="6" class="border border-transparent"></td>'+
                                        '<td colspan="8" class="border border-transparent" style="vertical-align: top;width:1rem;background-color:#fff;">'+
                                            // '<img name="previewPic" src="<?= $system_svg_1x1 ?>" width="100%" height="100%" class="border border-primary border-2 rounded elevation-1" alt="User Image" style="padding:0;margin:0;vertical-align:top;">'+
                                            '<div id="qqqq2'+i+'" style="text-align:center;width:100%;height:100%;padding:5px;"></div>'+
                                        '</td>'+
                                        '<td colspan="6" class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1.4rem; border: 1px solid #000;font-size:7px;">'+
                                        '<td colspan="20" class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1.45rem; border: 1px solid #000;font-size:7px;">'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                        '<td colspan="16" class="border border-transparent" style="font-family:Montserrat, sans-serif;color:#000;font-weight:800;font-size:' + autoSizeFont(incase_emergency, 9, 20, 190) + 'px;padding:0;">'+
                                            '<p class="mb-n2 mt-n1">'+getQoute(incase_emergency.toUpperCase())+'</p>'+
                                        '</td>'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1rem; border: 1px solid #000;">'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                        '<td colspan="16" class="border border-transparent" style="font-family:League Gothic, sans-serif;color:#1E34AB;font-weight:300;font-size:15px;padding:0;">'+
                                            '<p class="mb-n2 mt-n2">PARENT/GUARDIAN</p>'+
                                        '</td>'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1rem; border: 1px solid #000;">'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                        '<td colspan="16" class="border border-transparent" style="font-family:League Gothic, sans-serif;color:#000;font-weight:300;font-size:' + autoSizeFont(contact_number, 9, 15, 190) + 'px;padding:0;">'+
                                            '<p class="mb-n2 mt-n2">'+getQoute(contact_number.toUpperCase())+'</p>'+
                                        '</td>'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1rem; border: 1px solid #000;">'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                        '<td colspan="16" class="border border-transparent" style="font-family:League Gothic, sans-serif;color:#000;font-weight:300;font-size:' + autoSizeFont(add_details, 9, 15, 190) + 'px;padding:0;">'+
                                            '<p class="mb-n2 mt-n2">'+getQoute(add_details.toUpperCase())+'</p>'+
                                        '</td>'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                    '</tr>'+

                                    '<tr stye="border: 1px solid #000">'+
                                        '<td colspan="20"></td>'+
                                    '</tr>'+
                                '</table>'+
                            '</td>'
                        );
                        g = "";
                        // }
                        QR_BAR_Generator(i, od['learner_uuid'], lrn)
                    }
                    g = "";

                    // i--;
                    $("#modalPreviewID #tblPreviewID").append('</tr>');

                }
                
                $('#loadingModal').modal('hide');
                // console.log(d[0])
                // console.log(d)
                // JHS PreviewID DATA FOR THE 4th QUARTER S.Y 2021 - 2022
                // $("#modalPreviewID .header").html("STUDENT ID AS OF SY:  <?= $sy_; ?>  Q:" + q)
                $("#modalPreviewID .header").html("Student ID preview")
                $("#modalPreviewID").modal("show");
                // $("#modalPreviewID .viewPreviewID").html(d);
                // $(".submitBtnPreviewID").attr("disabled", false);
                // $(".submitBtnPreviewID").html("<span class='fa fa-user'></span> Preview ID");
            }).done(function() {
            $('[data-toggle="tooltip"]').tooltip()
        });

    }

    function QR_BAR_Generator(a, b, c) {
        var qrcode = new QRCode(document.getElementById("qqqq1" + a), {
            text: '7' + c,
            height: 100,
            width: 100
        });
        var qrcode = new QRCode(document.getElementById("qqqq2" + a), {
            text: '6' + c,
            height: 133,
            width: 133,
        });
        // JsBarcode("#bbbb1" + a, '7' + c, {
        //     pixelRatio: 80,
        //     displayValue: false,
        //     margin: 0
        // });
        // JsBarcode("#bbbb0" + a, '6' + c, {
        //     pixelRatio: 100,
        //     displayValue: false
        // });
        // JsBarcode("#bbbb"+a, c,{displayValue: false});
        // $("#bbbb"+a).attr('src', z);
    }

    var invalidChars = [
        "-",
        "+",
        "e",
    ];

    function clean(a) {
        var str = a;
        return str.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
    }

    function cleanInt(a) {
        var str = a;
        return str.replace(/\D/g, '');
    }

    const Toast = Swal.mixin({
        toast: true,
        position: 'center',
        showConfirmButton: false,
        timer: 3000
    });

    const ToastTop = Swal.mixin({
        toast: true,
        position: 'top',
        showConfirmButton: false,
        timer: 3000
    });

    function successAlert(a) {
        Toast.fire({
            icon: 'success',
            title: '  ' + a
        })
    }

    function failAlert(a) {
        Toast.fire({
            icon: 'error',
            title: '  ' + a
        })
    }

    function fillIn() {
        Toast.fire({
            icon: 'error',
            title: '  Please fill in all the required fields.'
        })
    }

    function existAlert(a) {
        Toast.fire({
            icon: 'warning',
            title: '  ' + a
        })
    }

    function warningAlert(a) {
        ToastTop.fire({
            icon: 'warning',
            title: '  ' + a
        })
    }

    function noData(a) {
        Toast.fire({
            icon: 'warning',
            title: '  ' + a,
        })
    }

    function printForm(a, b, c, d) {
        // var table = $("#tblPreviewID").DataTable();
        var orientation = (b == 'p' ? 'portrait' : 'landscape');
        var margin = (c == 'Legal' ? 'margin:3mm 3mm 3mm 3mm;' : 'margin:3mm 3mm 3mm 3mm;');
        var windowUrl = 'Print Form';
        var uniqueName = new Date();
        var windowName = 'emailSection' + uniqueName.getTime();
        var accompWindow = window.open('height=1500,width=2000');
        accompWindow.document.write('<html>');
        accompWindow.document.write('<head>');
        accompWindow.document.title = d;
        accompWindow.document.write('<link rel="stylesheet" href="<?= base_url() ?>plugins/fontawesome-free/css/all.min.css">' +
            '<link rel="stylesheet" href="<?= base_url() ?>dist/css/adminlte.min.css">' +
            // '<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">'+
            // '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=League+Gothic&display=swap">'+
            // '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;500;600;800&display=swap">'+
            // '<link rel="preconnect" href="https://fonts.googleapis.com">'+
            // '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'+
            '<link rel="stylesheet" href="<?= base_url() ?>dist/css/fonts.css">'+
            '<link rel="stylesheet" href="<?= base_url() ?>dist/css/adminlte.min.css">');
        accompWindow.document.write('</head>');
        accompWindow.document.write('<style> @page { size: ' + c + ' ' + orientation + ';' + margin + '} </style>');
        accompWindow.document.write('<body>' + $("#print" + a).html() + '</body>');
        accompWindow.document.write('</html>');
        setTimeout(function() {
            accompWindow.print();
            accompWindow.close();
        }, 1000);
        // table.draw();
        // window.print();
    }

    function downloadExcel(a, b) {
        // var preserveColors = (table.hasClass('table2excel_with_colors') ? true : false);
        $('#' + a).table2excel({
            //exclude: ".noExl",
            name: b,
            filename: b + new Date().toISOString().replace(/[\-\:\.]/g, ""),
            fileext: ".xls",
            exclude_img: false,
            exclude_links: false,
            exclude_inputs: false,
            preserveColors: true
        });
    }

    function generatePDF() {
        // var docDefinition = {
        //     content: [{
        //         table: {
        //             // headers are automatically repeated if the table spans over multiple pages
        //             // you can declare how many rows should be treated as headers
        //             headerRows: 1,
        //             widths: ['*', 'auto', 100, '*'],

        //             body: $("#tblLearnersList tbody").html()
        //         }
        //     }]
        // };
        // open the PDF in a new window
        // pdfMake.createPdf(docDefinition).open();

        // // print the PDF
        // pdfMake.createPdf(docDefinition).print();

        // download the PDF
        // pdfMake.createPdf(docDefinition).download('optionalName.pdf');
    }

    //ranking

    function changeArr(input, N) {
        // Copy input array into newArray
        var newArray = JSON.parse(JSON.stringify(input));

        // Sort newArray[] in ascending order
        newArray.sort((a, b) => a - b);

        var i;

        // Map to store the rank of
        // the array element
        var ranks = new Map();

        var rank = 1;

        for (var index = 0; index < N; index++) {

            var element = newArray[index];

            // Update rank of element
            if (!ranks.has(element)) {
                ranks.set(element, rank);
                rank++;
            }
        }

        // Assign ranks to elements
        for (var index = 0; index < N; index++) {
            var element = input[index];
            input[index] = ranks.get(input[index]);
        }
        return input;
    }


    //reports
    function reportsConsoGrades() {
        mdl = 'modalReportConsoGrades';
        $("#" + mdl + " .content").hide();
        $("#" + mdl + " .overlay").show();
        $("#tblReportConsoGrades tbody").empty();
        $.post("<?= base_url($uri . '/reports/getReportConsoGrades') ?>", {
                rsid: rsid
            },
            function(data) {
                var result = JSON.parse(data);
                $("#tblReportConsoGrades tbody").append("<tr>" + result["thead"] + "</tr>");
                $("#tblReportConsoGrades tbody").append("<tr>" + result["thead2"] + "</tr>");
                $("#tblReportConsoGrades tbody").append("<tr>" + result["tbody"] + "</tr>");
                $("#tblReportConsoGrades tbody").append("<tr width='100%'>" + result["signatory1"] + "</tr>");
                $("#tblReportConsoGrades tbody").append("<tr width='100%'>" + result["signatory2"] + "</tr>");
                $("#tblReportConsoGrades tbody").append("<tr width='100%'>" + result["signatory3"] + "</tr>");
            }
        ).then(function() {
            var arr1 = [],
                arr2 = [],
                arr3 = [],
                arr4 = [],
                avg = [];
            $("#tblReportConsoGrades .q1TBRank").each(function() {
                arr1.push((parseInt($(this).html())) * -1);
            });

            $("#tblReportConsoGrades .q2TBRank").each(function() {
                arr2.push((parseInt($(this).html())) * -1);
            });

            $("#tblReportConsoGrades .q3TBRank").each(function() {
                arr3.push((parseInt($(this).html())) * -1);
            });

            $("#tblReportConsoGrades .q4TBRank").each(function() {
                arr4.push((parseInt($(this).html())) * -1);
            });

            $("#tblReportConsoGrades .avgTBRank").each(function() {
                avg.push((parseInt($(this).html())) * -1);
            });

            var N1 = arr1.length,
                N2 = arr2.length,
                N3 = arr3.length,
                N4 = arr4.length,
                N5 = arr4.length;
            arr1 = changeArr(arr1, N1);
            arr2 = changeArr(arr2, N2);
            arr3 = changeArr(arr3, N3);
            arr4 = changeArr(arr4, N4);
            avg = changeArr(avg, N5);
            var i1 = 0,
                i2 = 0,
                i3 = 0,
                i4 = 0,
                i5 = 0;
            $("#tblReportConsoGrades .q1Rank").each(function() {
                $(this).html(arr1[i1++]);
            });
            $("#tblReportConsoGrades .q2Rank").each(function() {
                $(this).html(arr2[i2++]);
            });
            $("#tblReportConsoGrades .q3Rank").each(function() {
                $(this).html(arr3[i3++]);
            });
            $("#tblReportConsoGrades .q4Rank").each(function() {
                $(this).html(arr4[i4++]);
            });
            $("#tblReportConsoGrades .avgRank").each(function() {
                $(this).html(avg[i5++]);
            });

            $("#" + mdl + " .content").show();
            $("#" + mdl + " .overlay").hide();


            // $('#tblReportConsoGrades').DataTable({
            //     dom: 'Bfrtip',
            //     buttons: [{
            //         extend: 'pdfHtml5',
            //         orientation: 'landscape',
            //         pageSize: 'LEGAL'
            //     }]
            // });
        });
        $("#" + mdl).modal("show");
    }


    function uploadFile(a, b, c) {
        //Reference the FileUpload element.
        var fileUpload = $("#" + a + " #" + b)[0];

        //Validate whether File is valid Excel file.
        var regex = /(.xls|.xlsx)$/;
        // console.log(fileUpload)
        // console.log(regex.test(fileUpload.value.toLowerCase()))
        if (regex.test(fileUpload.value.toLowerCase())) {
            if (typeof(FileReader) != "undefined") {
                var reader = new FileReader();

                //For Browsers other than IE.
                if (reader.readAsBinaryString) {
                    reader.onload = function(e) {
                        ProcessExcel(e.target.result, c);
                    };
                    reader.readAsBinaryString(fileUpload.files[0]);
                } else {
                    //For IE Browser.
                    reader.onload = function(e) {
                        var data = "";
                        var bytes = new Uint8Array(e.target.result);
                        for (var i = 1; i < bytes.byteLength; i++) {
                            data += String.fromCharCode(bytes[i]);
                        }
                        ProcessExcel(data, c);
                    };
                    reader.readAsText(fileUpload.files[0]);
                }
            } else {
                alert("This browser does not support HTML5.");
            }
        } else {
            alert("Please upload a valid Excel file.");
        }
        fileUpload = null;
        $("#" + a + " #" + b).val("");
        $("#" + a + " ." + b).text("Choose file");
        customFile
    }

    function delay(form, a, b) {
        setTimeout(function() {
            $("#form_save_data" + form + " [name='" + b + "']").val(a);
            $("#form_save_data" + form + " [name='" + b + "']").trigger("change");
        }, 1000)
    }

    function ProcessExcel(data, c) {
        //Read the Excel File data.
        var workbook = XLSX.read(data, {
            type: 'binary'
        });


        // //Fetch the name of First Sheet.
        var firstSheet = workbook.SheetNames[0];
        // //Read all rows from First Sheet into an JSON array.
        var excelRows = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[firstSheet]);
        for (var i = 1; i < excelRows.length; i++) {

            // console.log(excelRows[i].Lrn)
            var lrn = cleanInt(excelRows[i].Lrn);
            $('#tblGradesList #gradeLearner' + lrn + '1').val(excelRows[i].Grades_Entry_Data)
            $('#tblGradesList #gradeLearner' + lrn + '2').val(excelRows[i].__EMPTY)
            $('#tblGradesList #gradeLearner' + lrn + '3').val(excelRows[i].__EMPTY_1)
            $('#tblGradesList #gradeLearner' + lrn + '4').val(excelRows[i].__EMPTY_2)

            $('#tblGradesPSList #gradeLearner' + lrn + '1').val(excelRows[i].Percentage_Score_Data)
            $('#tblGradesPSList #gradeLearner' + lrn + '2').val(excelRows[i].__EMPTY_3)
            $('#tblGradesPSList #gradeLearner' + lrn + '3').val(excelRows[i].__EMPTY_4)
            $('#tblGradesPSList #gradeLearner' + lrn + '4').val(excelRows[i].__EMPTY_5)
        }

        // var dvExcel = $("#dvExcel");
        // dvExcel.html("");
        // dvExcel.append(table);
    };

    function ifnull(a) {
        let b = a;
        if ((a == "null") || (a == null) || (a == "") || (a == '')) {
            // alert(a)
            b = '-';
        }
        return b;
    }
</script>