<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!$this->session->agrishop_login_level) {
    redirect(base_url('login'));
}
$uri = $this->session->agrishop_login_uri;
// $sy_ = $getOnLoad["sy"]; //$getOnLoad["sy_qrtr_e_g"];
?>
<!-- Bootstrap 4 -->

<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
<script src="<?php echo base_url(); ?>dist/layout_shop/js/plugins.js"></script>
<script src="<?php echo base_url(); ?>dist/layout_shop/js/script.js"></script>
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
<script src="<?= base_url() ?>plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/dataTables.buttons.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/responsive/js/dataTables.responsive.min.js"></script>
<!-- SweetAlert2 -->
<script src="<?= base_url() ?>plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url() ?>dist/js/adminlte.min.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script type="text/javascript">
    $('#modalFarmInfo').on('shown.bs.modal', function() {
        map.invalidateSize(); // <-- this tells Leaflet to recalc the map size
        map.setView([8.7, 125.6], 9); // optional: recenter map if needed
    });

    $(".close-sidebar-btn").click(function() {
        // $(".sidebar").slideToggle();
        // $(".main-sidebar").slideToggle();
    })

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
    $(function() {
        $('.select2').select2()
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        });
        $('[data-toggle="tooltip"]').tooltip()
        $('.select2').each(function() {
            let $parent = $(this).closest('.modal');
            $(this).select2({
                dropdownParent: $parent.length ? $parent : $('body') // fallback if not inside a modal
            });
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

    function delay(form, a, b) {
        setTimeout(function() {
            $("#form_save_data" + form + " [name='" + b + "']").val(a);
            $("#form_save_data" + form + " [name='" + b + "']").trigger("change");
        }, 1000)
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
            // searching: tableId == 'GradesList' ? false : true,
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
            // pageLength: pl,// Options for records per page
            // lengthMenu: [
            //     [10, 25, 50, 100],
            //     [10, 25, 50, 100]
            // ],
            ajax: {
                url: "<?= base_url($uri . '/' . $current_location . '/get') ?>" + tableId,
                type: "POST",
                data: function(d) {
                    drawCounter++;
                    d.length = pl;
                    d.draw = drawCounter;
                    d.search.value = $('#tbl' + tableId + '_filter input').val();
                    d.search.farm_id = $('#farmList').val();
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

    function add_qty(data) {
        $('#modalFarmProduceSupply').modal('show');

        // Hidden fields
        $('[name=fp_id]').val(data.id);

        // Visible display-only text
        $('[name=show_produceName]').text(data.produce);
        $('[name=show_classification]').text(data.class_name);
        $('[name=show_uom]').text(data.uom);
        $('[name=show_seasonal]').text(data.is_seasonal === 't' ? 'Seasonal' : 'Non-Seasonal');
        $('[name=show_qty_left]').text(data.qty_left);
        $('[name=price]').text(data.price);

        // Image
        $('[name=previewPicProduce]').attr("src", data.img_path);

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

    function getLocation(a, b, c, e) {
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
        console.log('tesssssssss')
        console.log(formId, getList, getQ, s2, where, sel, e);
    }

    $('#form_save_dataGradeSubject .selectSubjectList').on("select2:select", function(e) {
        // console.log('a')
        var unselected_value = $(this).val();
        // console.log(unselected_value);
    }).trigger('change');


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


    //mapping
    var esri_url = "https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}";
    var esri_attribution = "Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community";

    var mapbox_url = "https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=YOUR_MAPBOX_ACCESS_TOKEN";
    var mapbox_attribution = "Map data &copy; <a href='https://www.openstreetmap.org/'>OpenStreetMap</a> contributors, Imagery &copy; <a href='https://www.mapbox.com/'>Mapbox</a>";

    var caragaBounds = [
        [7.5, 124.5], // SW corner
        [9.5, 126.5] // NE corner
    ];

    // Initialize map, centered in CARAGA
    var map = L.map('map', {
        maxBounds: caragaBounds, // restrict panning
        maxBoundsViscosity: 1.0, // prevent dragging out of bounds
        minZoom: 5,
        maxZoom: 18,
        zoomControl: true
    }).setView([8.915726, 125.562744], 11);
    // Coordinates: Lat = 8.992371, Lng = 125.538025
    var satellite = L.tileLayer(esri_url, {
        maxZoom: 20,
        attribution: esri_attribution
    });


    // Street layer
    var street = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    });

    // Add satellite by default
    street.addTo(map);

    // Layer control
    var baseMaps = {
        "Street": street,
        "Satellite": satellite
    };
    L.control.layers(baseMaps).addTo(map);

    var marker = null;

    // Click event to drop marker & show coordinates
    map.on('click', function(e) {
        if (marker) map.removeLayer(marker);
        marker = L.marker(e.latlng).addTo(map);
        // document.getElementById('coords').innerText = 
        //     "Coordinates: Lat = " + e.latlng.lat.toFixed(6) + ", Lng = " + e.latlng.lng.toFixed(6);
        console.log("Coordinates: Lat = " + e.latlng.lat.toFixed(6) + ", Lng = " + e.latlng.lng.toFixed(6))
        $('#farmCoordinates').val(e.latlng.lat.toFixed(6) + "," + e.latlng.lng.toFixed(6));
        $('#farmLat').val(e.latlng.lat.toFixed(6));
        $('#farmLon').val(e.latlng.lng.toFixed(6));
    });
</script>