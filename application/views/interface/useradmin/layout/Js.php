<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!$this->session->agrishop_login_level) {
    redirect(base_url('login'));
}
$uri = $this->session->agrishop_login_uri;
// $sy_ = $getOnLoad["sy"]; //$getOnLoad["sy_qrtr_e_g"];
?>
<!-- Bootstrap 4 -->

<!-- <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script> -->
<script src="<?php echo base_url(); ?>dist/layout_shop/js/swiper-bundle.min.js"></script>
<script src="<?php echo base_url(); ?>dist/layout_shop/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo base_url(); ?>dist/layout_shop/js/plugins.js"></script>
<!-- <script src="<?php echo base_url(); ?>dist/layout_shop/js/script.js"></script> -->
<script src="<?= base_url() ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Select2 -->
<script src="<?= base_url() ?>plugins/select2/js/select2.full.min.js"></script>
<!-- Toastr -->
<script src="<?= base_url() ?>plugins/toastr/toastr.min.js"></script>
<!-- DataTables -->
<script src="<?= base_url() ?>plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/dataTables.buttons.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/buttons.print.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/jszip.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/buttons.flash.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/buttons.html5.min.js"></script>
<script src="<?= base_url() ?>plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/dataTables.buttons.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/responsive/js/dataTables.responsive.min.js"></script>
<!-- SweetAlert2 -->
<script src="<?= base_url() ?>plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url(); ?>dist/map/topojson-client.min.js"></script>
<script src="<?php echo base_url(); ?>dist/map/leaflet.js"></script>
<script src="<?php echo base_url(); ?>dist/js/confetti.browser.min.js"></script>

<!-- <script src="https://unpkg.com/topojson-client@3"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script> -->

<script type="text/javascript">

    $(document).on('click', '.barangay-item', function() {
        let id = $(this).data('id');
        let gid = $(this).data('gid');
        let name = $(this).data('name');

        // Set selected value to input
        $('.barangayInput').val(name);

        // Store barangay id in hidden input (recommended)
        $('#barangay_id').val(id);

        // Hide dropdown
        $('.barangayResults').hide();
        $('[name=barangay]').val(id)
        // alert(gid)
        // pinCentroidById(gid);

        pinCentroidById(gid);
    });

    $(document).on('click', '.barangay-itemAll', function() {
        let id = $(this).data('id');
        let gid = $(this).data('gid');
        let name = $(this).data('name');

        // Set selected value to input
        $('.barangayInputAll').val(name);

        // Store barangay id in hidden input (recommended)

        // Hide dropdown
        $('.barangayResultsAll').hide();
        $('[name=barangayAll]').val(id)
        // alert(gid)
        // pinCentroidById(gid);

    });

    $('.barangayInputAll').on('keyup', function() {
        let keyword = $(this).val();

        if (keyword.length < 3) {
            $('.barangayResultsAll').hide();
            return;
        }

        $.ajax({
            url: "<?= base_url('search-barangay') ?>",
            type: "POST",
            data: {
                keyword: keyword
            },
            success: function(res) {
                let data = JSON.parse(res);

                if (data.length === 0) {
                    $('.barangayResultsAll').hide();
                    return;
                }

                let html = "";
                data.forEach(row => {
                    html += `<li class="list-group-item barangay-itemAll" data-id="${row.id}" data-gid="${row.gid}" data-name="${row.text}">
                    ${row.text}
                </li>`;
                });

                $('.barangayResultsAll').html(html).show();
            }
        });
    });

    $('.barangayInput').on('keyup', function() {
        let keyword = $(this).val();

        if (keyword.length < 3) {
            $('.barangayResults').hide();
            return;
        }

        $.ajax({
            url: "<?= base_url('search-barangay-caraga') ?>",
            type: "POST",
            data: {
                keyword: keyword
            },
            success: function(res) {
                let data = JSON.parse(res);

                if (data.length === 0) {
                    $('.barangayResults').hide();
                    return;
                }

                let html = "";
                data.forEach(row => {
                    html += `<li class="list-group-item barangay-item" data-id="${row.id}" data-gid="${row.gid}" data-name="${row.text}">
                    ${row.text}
                </li>`;
                });

                $('.barangayResults').html(html).show();
            }
        });
    });

    function clear_form(b) {
        let f1 = "PersonnelInfo";
        let a = "form_save_data" + b;
        $("#" + a)[0].reset();
        $("#" + a).find("input[type='hidden']").each(function() {
            $(this).val("");
        });
        $("#" + a).find("input[type='checkbox']").each(function() {
            $(this).attr("checked", false);
        });
        if (b == f1) {} else {
            $("#" + a).find("select").each(function() {
                $(this).trigger("change");
            });
        }


        $("#" + a + " .submitBtnPrimary").attr("disabled", false);
        $("#" + a + " .submitBtnPrimary").html("Save Data");
        $("#" + a + " .clearBtn").html("Clear");
        $("#" + a + " .submitBtnPrimary").removeClass("btn-info").addClass("btn-primary");
        $("#" + a + " .clearBtn").removeClass("btn-danger");

        defaultImg('pic', 'previewPic', 'imgtargetLink', 'MALE');

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

    saveForm("UpdateProfile", [null], null)
    saveForm("UpdateGcash", [null], null)

    function saveForm(formId, tblId, tbl, dtd, pl) {
        let a = "";
        var saveData = {
            clearForm: false,
            resetForm: false,
            beforeSubmit: function(e) {
                if (formId != 'SbjctAssPrsnnl') {
                    validate("form_save_data" + formId);
                }
                if (valid != 0) {
                    fillIn();
                    return false;
                }
                a = $("#form_save_data" + formId + " .submitBtnPrimary").text();
                // $("#form_save_data" + formId + " .submitBtnPrimary").attr("disabled", true);
                $("#form_save_data" + formId + " .submitBtnPrimary").html("<span class=\"fa fa-spinner fa-pulse\"></span>");
            },
            success: function(data) {
                var d = JSON.parse(data);
                if (d.success == true) {
                    successAlert(d.message);
                    clear_form(formId);
                    $("#modal" + formId).modal('hide');
                    for (var i = 0; i < tblId.length; i++) {
                        getTable(tblId[i], dtd, pl);
                    }
                    tbl ? removeAllItemList("tbl" + tbl) : null;
                    tbl ? $("#btn" + tbl).trigger("click") : null;
                    if (formId == "FarmInfo" || formId == "UpdateProfile" || formId == "UpdateGcash") {
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    }
                    // if(formId=="AddFarmProduceSupply" ){
                    //     $("#modalAddFarmProduceSupply").modal("hide");
                    // }
                } else if (d.success == false && d.exist == true) {
                    existAlert(d.message);
                } else {
                    failAlert("Something went wrong!");
                }
                $("#form_save_data" + formId + " .submitBtnPrimary").attr("disabled", false);
                $("#form_save_data" + formId + " .submitBtnPrimary").html(a);
            },
            error: function() {
                failAlert("Something went wrong!");
                $("#form_save_data" + formId + " .submitBtnPrimary").attr("disabled", false);
                $("#form_save_data" + formId + " .submitBtnPrimary").html(a);
            }
        };
        $("#form_save_data" + formId).ajaxForm(saveData);
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
                url: "<?= base_url($uri . '/' . $current_location . '/get') ?>" + tableId,
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
            $(".collapse" + tableId).trigger('click');
        });
        $("#tbl" + tableId + "_filter").addClass("row");
        $("#tbl" + tableId + "_filter label").css("width", "97%");
        $("#tbl" + tableId + "_filter .form-control-sm").css("width", "97%");
        dtd == 1 ? $("#tbl" + tableId).DataTable().destroy() : "";

    }

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

    function tblReload(tableId) {
        // $("#tbl" + tableId).DataTable().ajax.reload();

        let table = $("#tbl" + tableId).DataTable();

        table.ajax.reload(function() {
            // After reload is complete and DOM is updated
        }, false); // false = keep current pagination


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