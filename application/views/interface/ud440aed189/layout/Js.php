<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!$this->session->schoolmis_login_level) {
    redirect(base_url('login'));
}
$uri = $this->session->schoolmis_login_uri;
?>
<!-- Bootstrap 4 -->

<script src="<?= base_url() ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Select2 -->
<script src="<?= base_url() ?>plugins/select2/js/select2.full.min.js"></script>
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

    function validate(form_id) {
        let invalid = 0;
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

    function passwordChecker(a, b, c) {
        var f = "form_save_data" + a;
        var g = $("#" + f + " ." + b).val();
        var h = $("#" + f + " ." + c).val();
        if (g.length > 7 && h.length > 7) {
            $("#" + f + " .atleast").hide();
            if (g != h) {
                $("#" + f + " .good").hide();
                $("#" + f + " .bad").show();
                $("#" + f + " .submitBtnPrimary").attr("disabled", true);
            } else if (!g && !h) {
                $("#" + f + " .good").hide();
                $("#" + f + " .bad").hide();
                $("#" + f + " .submitBtnPrimary").attr("disabled", true);
            } else {
                $("#" + f + " .good").show();
                $("#" + f + " .bad").hide();
                $("#" + f + " .submitBtnPrimary").attr("disabled", false);
            }
        } else {
            $("#" + f + " .atleast").show();
            $("#" + f + " .submitBtnPrimary").attr("disabled", true);
        }
    }

    function saveForm(formId, tblId, tbl, dtd, pl) {
        let a = "";
        var saveData = {
            clearForm: false,
            resetForm: false,
            beforeSubmit: function(e) {
                validate("form_save_data" + formId);
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
                } else if (d.exist == true) {
                    existAlert("Person already exist!<br/>You can search and add TEST RESULT");
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
</script>