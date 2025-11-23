<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!$this->session->schoolmis_login_level) {
    redirect(base_url('login'));
}
$uri = $this->session->schoolmis_login_uri;
$uri_reports = "reports";
?>
<!-- Bootstrap 4 -->

<script src="<?= base_url() ?>plugins/ol3/ol.js"></script>
<script src="<?= base_url() ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Select2 -->
<script src="<?= base_url() ?>plugins/select2/js/select2.full.min.js"></script>
<!-- DataTables -->
<script src="<?= base_url() ?>plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script src="<?= base_url() ?>plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/dataTables.buttons.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/responsive/js/dataTables.responsive.min.js"></script>
<!-- Popper -->
<script src="<?= base_url() ?>plugins/popper/popper.min.js"></script>
<!-- SweetAlert2 -->
<script src="<?= base_url() ?>plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Toastr -->
<script src="<?= base_url() ?>plugins/toastr/toastr.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url() ?>dist/js/adminlte.min.js"></script>

<script type="text/javascript">
    setInterval(function() {
        $.post("<?= base_url('Main/allow') ?>");
    }, 3000)
    var rssaid = 0;
    var rssaid_tmp = 0;
    var qrssaid = 0;
    var aTmp = "";
    var bTmp = "";
    var cTmp = "";
    var dTmp = "";
    var fTmp = "";
    var gTmp = "";

    function clear_form(form_id) {
        let f1 = "PersonnelInfo";
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

    function validate(form_id) {
        let invalid = 0;
        $($("#" + form_id).find("select").get().reverse()).each(function() {
            var name = $(this).attr("name");
            var j = clean($(this).attr("name"));
            var nr = $(this).attr("nr");
            var multiple = $(this).attr("multiple");
            // console.log(j)


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

    function saveForm(formId, tblId, tbl, dtd, pl) {
        let a = "";
        var saveData = {
            clearForm: false,
            resetForm: false,
            beforeSubmit: function(e) {
                if (valid != 0) {
                    fillIn();
                    return false;
                }
                a = $("#form_save_data" + formId + " .submitBtnPrimary").text();
                $("#form_save_data" + formId + " .submitBtnPrimary").attr("disabled", true);
                $("#form_save_data" + formId + " .submitBtnPrimary").html("<span class=\"fa fa-spinner fa-pulse\"></span>");
            },
            success: function(data) {
                var d = JSON.parse(data);
                if (d.success == true) {
                    successAlert("Successfully Saved!");
                    clear_form("form_save_data" + formId);
                    $("#modal" + formId).modal('hide');
                    for (var i = 0; i < tblId.length; i++) {
                        getTable(tblId[i], dtd, pl);
                    }
                    tbl ? removeAllItemList("tbl" + tbl) : null;
                    tbl ? $("#btn" + tbl).trigger("click") : null;
                } else if (d.success == false && d.exist == true) {
                    existAlert(d.message);
                } else if (d.existCode == true) {
                    existAlert("Code already taken!<br/>by: " + d.existPerson);
                } else {
                    failAlert("Something went wrong!");
                }
                $("#form_save_data" + formId + " .submitBtnPrimary").attr("disabled", false);
                $("#form_save_data" + formId + " .submitBtnPrimary").html(a);
            }
        };
        $("#form_save_data" + formId).ajaxForm(saveData);
    }

    function getTable(tableId, dtd, pl) {
        $(".form_save_data" + tableId + " .overlay").show();
        // a = $("#tblGradeSecInfo .btneye").text();

        // $("#tblGradeSecInfo .btneye").attr("disabled", true);
        // $("#tblGradeSecInfo .btneye").html("<span class=\"fa fa-spinner fa-pulse\"></span>");

        // $("#tblGradeSecInfo .btneye").attr("disabled", false);
        // $("#tblGradeSecInfo .btneye").html(a);
        $("#tbl" + tableId).DataTable().destroy();
        var table, table_data = $("#tbl" + tableId).DataTable({
            "order": [
                [0, "asc"]
            ],
            dom: 'Bfrtip',
            buttons: [],
            // searching: tableId == 'GradesList' ? false : true,
            "info": pl == -1 ? false : true,
            "paging": pl == -1 ? false : true,
            "ordering": pl == -1 ? false : true,
            "oLanguage": {
                "sSearch": ""
            },
            language: {
                searchPlaceholder: "Search...",
            },
            pageLength: pl,
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            ajax: {
                url: "<?= base_url($uri . '/getdata/get') ?>" + tableId,
                icon: "POST",
                data: function(d) {}
            }
        });

        $("#tbl" + tableId).on('draw.dt', function() {
            $(".searchBtn").attr("disabled", false);
            $(".searchBtn").html("<span class=\"fa fa-search\"></span>");
            dtd == 1 ? $("#tbl" + tableId).DataTable().destroy() : "";
            $(".collapse" + tableId).trigger('click');
            $(".form_save_data" + tableId + " .overlay").hide();

        });
        $("#tbl" + tableId + "_filter").addClass("row");
        $("#tbl" + tableId + "_filter label").css("width", "99.3%");
        $("#tbl" + tableId + "_filter .form-control-sm").css("width", "99.3%");

        // if (tableId == "SubjectList") {
        //     getFetchList('GradeSubject', "SubjectList", "PartyList", 0, {
        //         v: 17
        //     }, 0);

        //     setTimeout(() => {
        //         arr = [];
        //         $("#form_save_dataGradeSubject .selectSubjectList option").each(function() {
        //             let a = $(this).attr("value");
        //             a != '' ? arr.push($(this).attr("value")) : null;
        //         });
        //         console.log(arr)
        //     }, 3000);

        // }
    }

    function customTabViewAllGrades(b) {
        a = $("#tblGradeSecInfo .btneye").text();

        $("#tblGradeSecInfo .btneye").attr("disabled", true);
        $("#tblGradeSecInfo .btneye").html("<span class=\"fa fa-sync-alt fa-pulse\"></span>");

        $(".form_view_dataGradesInfo .overlay").show();
        $(".form_view_dataGradesInfo .viewAllGrades").empty();
        $.get("<?= base_url($uri . "/Getdata/getViewAllGrades") ?>", {
                a: b
            },
            function(data) {
                var d = JSON.parse(data);
                $(".form_view_dataGradesInfo .viewAllGrades").html(d);
                $(".form_view_dataGradesInfo .overlay").fadeToggle();

                $("#tblGradeSecInfo .btneye").attr("disabled", false);
                $("#tblGradeSecInfo .btneye").html("<span class='fa fa-eye'></span>");
            }).done(function() {
            $('[data-toggle="tooltip"]').tooltip()
        });
    }

    function preSbmitGrades(a, b, c, d, f, g) {
        aTmp = a;
        bTmp = b;
        cTmp = c;
        dTmp = d;
        fTmp = f;
        gTmp = g;
        qrssaid = d.toString() + g.toString();
        $("#modalLearnersSubmitGrades #qrssa").val(d);
        $("#modalLearnersSubmitGrades").modal("show");
    }

    function submitGrades(a) {
        // alert(a)
        validate("form_save_dataSubmitGradesConfirm");
        if (valid != 0) {
            fillIn();
            return false;
        }
        // a = $("#form_save_dataUnenrollConfirm .submitBtnPrimary").text();
        // $("#form_save_dataUnenrollConfirm .submitBtnPrimary").attr("disabled", true);
        // $("#form_save_dataUnenrollConfirm .submitBtnPrimary").html("<span class=\"fa fa-spinner fa-pulse\"></span>");
        var s = $("#form_save_dataSubmitGradesConfirm").serialize();
        $.post("<?= base_url($uri . '/Dataentry/submitGrades') ?>", {
                c: s,
                e: rssaid
            },
            function(data) {
                var result = JSON.parse(data);
                if (result.success == true) {
                    successAlert(result.message);
                    $("#modalLearnersSubmitGrades, #modalGrades").modal('hide');
                    var st = $("#modalLearnersSubmitGrades .status").val();
                    var rm = $("#modalLearnersSubmitGrades .remarks").val();
                    clear_form("form_save_dataSubmitGradesConfirm");
                    var ye = '<i class="fa fa-envelope float-right text-yellow"></i>';
                    var ttip = "data-toggle='tooltip' data-placement='bottom' data-html='true' title='<em>Message:</em> <b>" + rm + "</b>'";
                    if (st == "RECHECK") {
                        $e = $("." + qrssaid).html("<button " + (rm ? ttip : '') +
                            " type='button' class='btn btn-block btn-xs bg-navy' style='cursor:default'>" +
                            "<b> Q" + fTmp + " - " + bTmp + "%</b> RECHECK" +
                            (rm ? ye : '') +
                            "</button>");
                    } else if (st == "APPROVE") {
                        $e = $("." + qrssaid).html("<button " + (rm ? ttip : '') +
                            ' onclick="preSbmitGrades(\'' + aTmp + '\',' + bTmp + ',\'' + cTmp + '\',' + dTmp + ',' + fTmp + ',' + gTmp + ')"' +
                            " type='button' class='btn btn-block btn-xs bg-success'>" +
                            "<i class='fa fa-thumbs-up'></i> <b> Q" + fTmp + " - " + bTmp + "%</b> APPROVED" +
                            (rm ? ye : '') +
                            "</button>");
                    } else {
                        $e = null;
                    }
                    $('[data-toggle="tooltip"]').tooltip()
                } else if (result.success == false) {
                    failAlert(result.message);
                }
            }
        ).then(function() {});
    }

    function tblReload(tableId) {
        $("#tbl" + tableId).DataTable().ajax.reload();
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

    function clean(a) {
        var str = a;
        return str === undefined ? null : str.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
    }

    $(".submitBtnMPS_GPA").click(function() {
        $(".submitBtnMPS_GPA").attr("disabled", true);
        $(".submitBtnMPS_GPA").html("<span class=\"fa fa-spinner fa-pulse\"></span>");
        $.get("<?= base_url($uri_reports . "/reports/getMPS_GPA_dept") ?>", {
                sy: $("#form_report_dataMPS_GPA [name='sy']").val(),
                qrtr: $("#form_report_dataMPS_GPA [name='qrtr']").val(),
                report: $("#form_report_dataMPS_GPA [name='report']").val(),
            },
            function(data) {
                var d = JSON.parse(data);
                // console.log(d)
                // JHS MPS_GPA DATA FOR THE 4th QUARTER S.Y 2021 - 2022
                $("#modalMPS_GPA .header").html("MPS_GPA DATA FOR THE "+$("#form_report_dataMPS_GPA #qrtr option:selected").text() + "QUARTER S.Y. " + $("#form_report_dataMPS_GPA #sy option:selected").text())
                $("#modalMPS_GPA").modal("show");
                $("#modalMPS_GPA .viewMPS_GPA").html(d);
                $(".submitBtnMPS_GPA").attr("disabled", false);
                $(".submitBtnMPS_GPA").html("<span class='fa fa-search'></span>");
            }).done(function() {
            $('[data-toggle="tooltip"]').tooltip()
        });

    });

    const Toast = Swal.mixin({
        toast: true,
        position: 'center',
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

    function noData(a) {
        Toast.fire({
            icon: 'warning',
            title: '  ' + a,
        })
    }

    function printForm(a, b, c, d) {
        var orientation = (b == 'p' ? 'portrait' : 'landscape');
        var margin = (c == 'Legal' ? 'margin:5mm 5mm 5mm 5mm;' : 'margin:5mm 5mm 5mm 5mm;');
        var windowUrl = 'Print Form';
        var uniqueName = new Date();
        var windowName = 'emailSection' + uniqueName.getTime();
        var accompWindow = window.open('height=1500,width=2000');
        accompWindow.document.write('<html>');
        accompWindow.document.write('<head>');
        accompWindow.document.title = d;
        accompWindow.document.write('<link rel="stylesheet" href="<?= base_url() ?>plugins/fontawesome-free/css/all.min.css">' +
            '<link rel="stylesheet" href="<?= base_url() ?>dist/css/adminlte.min.css">');
        accompWindow.document.write('</head>');
        accompWindow.document.write('<style> @page { size: ' + c + ' ' + orientation + ';' + margin + '} .square {height: 100px;width: 100px;border:1px solid black; } </style>');
        accompWindow.document.write('<body>' + $("#print" + a).html() + '</body>');
        accompWindow.document.write('</html>');
        setTimeout(function() {
            accompWindow.print();
            accompWindow.close();
        }, 1000);
    }
</script>