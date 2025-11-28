<script type="text/javascript">
    window.onload = () => {
        $('.modal').on('shown.bs.modal', () => window.dispatchEvent(new Event('resize')));
    }

    var valid = 0;

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
        let f = "form_save_data" + a;
        let g = $("#" + f + " ." + b).val(); // password
        let h = $("#" + f + " ." + c).val(); // confirm password

        // --- LENGTH CHECK (Password only) ---
        if (g.length >= 8) {
            $("#" + f + " .atleast").hide();
            $("#" + f + " .good8").show();
        } else {
            $("#" + f + " .atleast").show();
            $("#" + f + " .good8").hide();
        }

        // --- MATCH CHECK (always active) ---
        if (!g && !h) {
            // nothing typed yet
            $("#" + f + " .good").hide();
            $("#" + f + " .bad").hide();
        } else if (g === h) {
            // matches
            $("#" + f + " .good").show();
            $("#" + f + " .bad").hide();
        } else {
            // mismatch
            $("#" + f + " .good").hide();
            $("#" + f + " .bad").show();
        }

        // --- ENABLE SUBMIT ---
        if (g.length >= 8 && g === h) {
            $("#" + f + " .submitBtnPrimary").prop("disabled", false);
        } else {
            $("#" + f + " .submitBtnPrimary").prop("disabled", true);
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
                    successAlert("Successfully Applied!");
                    // toastr.success("Successfully Applied!")
                    $(".sbmtbttn").hide();
                    $(".redirect").show();
                    setTimeout(function() {
                        location.reload();
                    }, 2000)
                } else if (d.exist == true) {
                    existAlert("Application already exist!");
                    // toastr.warning("Application already exist!")
                } else {
                    failAlert("Something went wrong!");
                }
                $("#form_save_data" + formId + " .submitBtnPrimary").attr("disabled", false);
                $("#form_save_data" + formId + " .submitBtnPrimary").html(a);
            }
        };
        $("#form_save_data" + formId).ajaxForm(saveData);
    }

    saveForm("RegisterFarmer", [null], null);

    function clean(a) {
        var str = a;
        return str === undefined ? null : str.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');
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

    function getTable(tableId, dtd, pl) {
        var drawCounter = 0;
        $("#tbl" + tableId).DataTable().destroy();
        var table, table_data = $("#tbl" + tableId).DataTable({
            "order": [
                [0, "asc"]
            ],
            dom: 'Bfrtip',
            buttons: [],
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
            },
            ajax: {
                url: "<?= base_url('system/getdata/getFarmList') ?>",
                type: "POST",
                data: function(d) {
                    drawCounter++;
                    d.length = pl;
                    d.draw = drawCounter;
                    d.search.value = $('#tbl' + tableId + '_filter input').val();
                }
            },

            "lengthMenu": [5, 10, 25, 50, 100],
            "pageLength": pl,
        });

        $("#tbl" + tableId).on('draw.dt', function() {
            $(".searchBtn").attr("disabled", false);
            $(".searchBtn").html("<span class=\"fa fa-search\"></span>");
            dtd == 1 ? $("#tbl" + tableId).DataTable().destroy() : "";
            $(".collapse" + tableId).trigger('click');
        });
        $("#tbl" + tableId + "_filter").addClass("row");
        $("#tbl" + tableId + "_filter label").css("width", "97%");
        $("#tbl" + tableId + "_filter .form-control-sm").css("width", "97%");
    }
</script>