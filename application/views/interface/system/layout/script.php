<script type="text/javascript">
    let farm_id = null;
    let transaction_id_ = null;
    let status_ = null;
    const farmCache = {};
    document.addEventListener("DOMContentLoaded", function() {
        const nav = document.getElementById("topNav");
        const navHeight = nav.offsetHeight;

        // Set CSS variable dynamically
        document.documentElement.style.setProperty('--nav-height', navHeight + 'px');

        // If the map already exists, refresh size
        setTimeout(() => {
            if (window.map) map.invalidateSize();
        }, 200);
    });
    window.onload = () => {
        $('.modal').on('shown.bs.modal', () => window.dispatchEvent(new Event('resize')));
        // $('#homeModal').modal('show');
        // $("#searchProduce").val('tomato');
        // triggerSearch();
    }


    $(document).on('click', '.barangay-item', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');

        // Set selected value to input
        $('.barangayInput').val(name);

        // Store barangay id in hidden input (recommended)
        $('#barangay_id').val(id);

        // Hide dropdown
        $('.barangayResults').hide();
        $('[name=barangayAll]').val(id)
    });

    $('.barangayInput').on('keyup', function() {
        let keyword = $(this).val();

        if (keyword.length < 3) {
            $('.barangayResults').hide();
            return;
        }

        $.ajax({
            url: "<?= base_url('search-barangay') ?>",
            type: "POST",
            data: {
                keyword: keyword,
                limit: 3
            },
            success: function(res) {
                let data = JSON.parse(res);

                if (data.length === 0) {
                    $('.barangayResults').hide();
                    return;
                }

                let html = "";
                data.forEach(row => {
                    html += `<li class="list-group-item barangay-item" data-id="${row.id}" data-name="${row.text}">
                    ${row.text}
                </li>`;
                });

                $('.barangayResults').html(html).show();
            }
        });
    });


    // when clicked
    $(document).on('click', '.barangay-item', function() {
        let name = $(this).data('name');
        let id = $(this).data('id');

        $('.barangayInput').val(name); // show selected barangay
        $('.barangayResults').hide(); // hide list

        // optional: save to hidden field if needed
        // $("#barangay_id").val(id);
    });

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
                    successAlert("Successfully Updated!");
                    // toastr.success("Successfully Applied!")
                    $(".sbmtbttn").hide();
                    $(".redirect").show();
                    if (formId == "UpdateProfile") {
                        setTimeout(function() {
                            location.reload();
                        }, 1000)
                    }
                    if (formId == "PayProcessingFee") {
                        $('#modalProcessingFeeModal').modal('hide');
                    }
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
    saveForm("UpdateProfile", [null], null);
    saveForm("PayProcessingFee", ['CartDetails'], null);

    function imageView(a, b, c) {
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
        $("[name=picProduce]").val("");
        // img = (d == 'FEMALE' ? 'defaultf.png' : 'defaultm.png');
        $("[name=previewPicProduce]").attr("src", "<?= $system_svg_1x1 ?>");
        reader.onload = function(e) {
            document.getElementById(b).src = e.target.result;
        };
    }


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

    function infoAlert(a) {
        Toast.fire({
            icon: 'info',
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
                url: "<?= base_url('userpublicmap/map/get') ?>" + tableId,
                type: "POST",
                data: function(d) {
                    drawCounter++;
                    d.length = pl;
                    d.draw = drawCounter;
                    d.search.value = $('#tbl' + tableId + '_filter input').val();
                    d.search.farm_id = farm_id;
                    d.search.transaction_id = transaction_id_;
                    d.search.status = status_;
                }
            },

            "lengthMenu": [5, 10, 25, 50, 100],
            "pageLength": pl,
        });

        $("#tbl" + tableId).on('draw.dt', function() {
            $(".searchBtn").attr("disabled", false);
            $(".searchBtn").html("<span class=\"fa fa-search\"></span>");
            $(".collapse" + tableId).trigger('click');
        });
        $("#tbl" + tableId + "_filter").addClass("row");
        $("#tbl" + tableId + "_filter label").css("width", "97%");
        $("#tbl" + tableId + "_filter .form-control-sm").css("width", "97%");
        dtd == 1 ? $("#tbl" + tableId).DataTable().destroy() : "";
    }
</script>