<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!$this->session->agrishop_login_level) {
    redirect(base_url('login'));
}
$uri = $this->session->agrishop_login_uri;
?>

<script src="<?= base_url() ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url() ?>plugins/select2/js/select2.full.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script src="<?= base_url() ?>plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/dataTables.buttons.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url() ?>plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="<?= base_url() ?>plugins/toastr/toastr.min.js"></script>
<script src="<?= base_url() ?>dist/js/adminlte.min.js"></script>

<script type="text/javascript">
    setInterval(function() {
        $.post("<?= base_url('Main/allow') ?>");
    }, 3000);

    var confirmP = "";
    var rmvP     = "";
    var refrmvP  = "";
    var stq      = "";
    var pwd      = "";
    var entryId  = 0;
    var addItemId = 0;
    var validatorC = 0;
    var valid    = 0;
    var grdlvl   = 0;
    var rmid     = 0;

    $(function() {
        $('.select2').select2();
        $('.select2bs4').select2({ theme: 'bootstrap4' });
    });

    $("input[data-bootstrap-switch]").each(function() {
        $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });

    function clear_form(form_id) {
        $("#" + form_id)[0].reset();
        $("#" + form_id).find("input[type='hidden']").each(function() { $(this).val(""); });
        $("#" + form_id).find("input[type='checkbox']").each(function() { $(this).attr("checked", false); });
        $("#" + form_id).find("select").each(function() { $(this).trigger("change"); });
        $("#" + form_id + " .submitBtnPrimary").attr("disabled", false).html("Save Data");
    }

    function delay(a, b) {
        setTimeout(function() {
            $("#form_save_dataPersonnelInfo [name='" + b + "']").val(a).trigger("change");
        }, 1000);
    }

    function getDetails(a, b) {
        $.each(b, function(k, v) {
            $("#form_save_data" + a).each(function() {
                $("[name='" + k + "']").val(v);
                $("[class='" + k + "']").html(v);
                $("[name='" + k + "']").trigger("change");
            });
        });
    }

    function validate(form_id) {
        let invalid = 0;
        $($("#" + form_id).find("select").get().reverse()).each(function() {
            var name = $(this).attr("name");
            var j    = clean($(this).attr("name"));
            var nr   = $(this).attr("nr");
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
            var name = clean($(this).attr("name"));
            var nr   = $(this).attr("nr");
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
        });
        valid = invalid;
    }

    function saveForm(formId, tblId, tbl, dtd, pl) {
        let a = "";
        var saveData = {
            clearForm: false,
            resetForm: false,
            beforeSubmit: function(e) {
                validate("form_save_data" + formId);
                if (valid != 0) { fillIn(); return false; }
                a = $("#form_save_data" + formId + " .submitBtnPrimary").text();
                $("#form_save_data" + formId + " .submitBtnPrimary").html('<span class="fa fa-spinner fa-pulse"></span>');
            },
            success: function(data) {
                var d = JSON.parse(data);
                if (d.success == true) {
                    successAlert("Successfully Saved!");
                    clear_form("form_save_data" + formId);
                    $("#modal" + formId).modal('hide');
                    for (var i = 0; i < tblId.length; i++) { getTable(tblId[i], dtd, pl); }
                    tbl ? removeAllItemList("tbl" + tbl) : null;
                    tbl ? $("#btn" + tbl).trigger("click") : null;
                } else if (d.success == false && d.exist == true) {
                    existAlert(d.message);
                } else if (d.existCode == true) {
                    existAlert("Code already taken!<br/>by: " + d.existPerson);
                } else {
                    failAlert("Something went wrong!");
                }
                $("#form_save_data" + formId + " .submitBtnPrimary").attr("disabled", false).html(a);
            }
        };
        $("#form_save_data" + formId).ajaxForm(saveData);
    }

    function getTable(tableId, dtd, pl) {
        $("#tbl" + tableId).DataTable().destroy();
        var table_data = $("#tbl" + tableId).DataTable({
            "order": [[0, "asc"]],
            dom: 'Bfrtip',
            buttons: [],
            "info":     pl == -1 ? false : true,
            "paging":   pl == -1 ? false : true,
            "ordering": pl == -1 ? false : true,
            "oLanguage": { "sSearch": "" },
            language: { searchPlaceholder: "Search..." },
            pageLength: pl,
            lengthMenu: [10, 25, 50, 100],
            ajax: {
                url:  "<?= base_url($uri . '/getdata/get') ?>" + tableId,
                type: "POST",
                data: function(d) {}
            }
        });
        $("#tbl" + tableId).on('draw.dt', function() {
            $(".searchBtn").attr("disabled", false).html('<span class="fa fa-search"></span>');
            dtd == 1 ? $("#tbl" + tableId).DataTable().destroy() : "";
            $(".collapse" + tableId).trigger('click');
        });
        $("#tbl" + tableId + "_filter").addClass("row");
        $("#tbl" + tableId + "_filter label").css("width", "99.3%");
        $("#tbl" + tableId + "_filter .form-control-sm").css("width", "99.3%");
    }

    function tblReload(tableId) {
        $("#tbl" + tableId).DataTable().ajax.reload();
    }

    function getFetchList(formId, getList, getQ, s2, where, sel, e) {
        var q = getQ ? getQ : getList;
        $("#form_save_data" + formId + " .select" + getList).empty();
        $.post("<?= base_url($uri . '/getdata/get') ?>" + q, where, function(data) {
            var result = JSON.parse(data);
            (sel == 0 || e == 0) ? $("#form_save_data" + formId + " .select" + getList).append("<option value=''>SELECT</option>") : "";
            for (var i = 0; i < result["data"].length; i++) {
                $("#form_save_data" + formId + " .select" + getList).append("<option value='" + result["data"][i]['id'] + "'>" + result["data"][i]['item'] + "</option>");
            }
        }).then(function() {
            s2 == 1 ? $("#form_save_data" + formId + " .select" + getList).select2() : "";
        });
    }

    function getLocation(a, b, c, e) {
        for (var i = 0; i < a.length; i++) {
            clearLoc(b[i], c);
            let form = 'form_save_data' + c;
            let d    = $('#' + form + ' .select' + a[i]).val();
            let ab   = d == '' || d == null ? 0 : d;
            getFetchList(c, b[i], null, 1, { v: ab }, 1, 1);
        }
    }

    function clearLoc(a, b) {
        let form = 'form_save_data' + b;
        $("#" + form + " .select" + a).empty();
    }

    function clean(a) {
        var str = a;
        return str === undefined ? null : str.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
    }

    const Toast = Swal.mixin({ toast: true, position: 'center', showConfirmButton: false, timer: 3000 });
    function successAlert(a) { Toast.fire({ icon: 'success', title: '  ' + a }); }
    function failAlert(a)    { Toast.fire({ icon: 'error',   title: '  ' + a }); }
    function fillIn()        { Toast.fire({ icon: 'error',   title: '  Please fill in all the required fields.' }); }
    function existAlert(a)   { Toast.fire({ icon: 'warning', title: '  ' + a }); }
    function noData(a)       { Toast.fire({ icon: 'warning', title: '  ' + a }); }

    function printForm(a, b, c, d) {
        var orientation = (b == 'p' ? 'portrait' : 'landscape');
        var margin      = 'margin:5mm 5mm 5mm 5mm;';
        var accompWindow = window.open('height=1500,width=2000');
        accompWindow.document.write('<html><head>');
        accompWindow.document.title = d;
        accompWindow.document.write(
            '<link rel="stylesheet" href="<?= base_url() ?>plugins/fontawesome-free/css/all.min.css">' +
            '<link rel="stylesheet" href="<?= base_url() ?>dist/css/adminlte.min.css">'
        );
        accompWindow.document.write('</head>');
        accompWindow.document.write('<style>@page{size:' + c + ' ' + orientation + ';' + margin + '}</style>');
        accompWindow.document.write('<body>' + $("#print" + a).html() + '</body></html>');
        setTimeout(function() { accompWindow.print(); accompWindow.close(); }, 1000);
    }
</script>
