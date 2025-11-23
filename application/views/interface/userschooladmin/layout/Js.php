<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!$this->session->schoolmis_login_level) {
    redirect(base_url('login'));
}
$uri = $this->session->schoolmis_login_uri;
$uri_reports = "reports";
$sy_ = $getOnLoad["sy"]; //$getOnLoad["sy_qrtr_e_g"];
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
<script src="<?= base_url() ?>plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/dataTables.buttons.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/responsive/js/dataTables.responsive.min.js"></script>
<!-- SweetAlert2 -->
<script src="<?= base_url() ?>plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Toastr -->
<script src="<?= base_url() ?>plugins/toastr/toastr.min.js"></script>
<script src="<?= base_url() ?>plugins/qrcode_generator/qrcode.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url() ?>dist/js/adminlte.min.js"></script>

<script type="text/javascript">
    $(".close-sidebar-btn").click(function() {
        // $(".sidebar").slideToggle();
        // $(".main-sidebar").slideToggle();
    })
    // document.getElementById('closeSidebarBtn').addEventListener('click', function() {
    //     alert('a')
    //     var sidebar = document.getElementById('sidebar');
    //     if (sidebar.style.display === 'none') {
    //         sidebar.style.display = 'block';
    //     } else {
    //         sidebar.style.display = 'none';
    //     }
    // });

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
    $(function() {
        $('.select2').select2()
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        });
        $('[data-toggle="tooltip"]').tooltip()
        $('.select2').each(function () {
            let $parent = $(this).closest('.modal');
            $(this).select2({
                dropdownParent: $parent.length ? $parent : $('body') // fallback if not inside a modal
            });
        });
    });

    $('#subjectOptions').select2({
        templateResult: function (data) {
            if (!data.id) return data.text;

            const html = $(data.element).attr('data-html');
            if (html) {
                return $(html);
            }

            return data.text;
        },
        templateSelection: function (data) {
            return data.text; // Or also render HTML if needed
        },
        escapeMarkup: function (markup) {
            return markup; // Allow HTML
        }
    });

    function initSubjectSelect2(selector = '.select2-subject') {
        $(selector).select2({
            templateResult: function (data) {
                if (!data.id) return data.text;

                const $option = $(data.element);
                const html = $option.attr('data-html');
                return html ? $(html) : data.text;
            },
            templateSelection: function (data) {
                return data.text; // or same as result if you want
            },
            escapeMarkup: function (markup) {
                return markup; // VERY important: allow HTML
            }
        });
    }

    $("input[data-bootstrap-switch]").each(function() {
        $(this).bootstrapSwitch('state', $(this).prop('checked'));
    })

    let recalculateTimeout = null;

    function triggerRecalculateWithDelay() {
        clearTimeout(recalculateTimeout); // cancel previous
        recalculateTimeout = setTimeout(() => {
            document.getElementById('btnRecalculate').click();
        }, 300); // only trigger once after 300ms of inactivity
    }

    function updateAllEndTimes() {
        let previousEndTime = null;

        $('#scheduleTable tbody tr').each(function (index) {
            const row = $(this);
            const startSelect = row.find('.startTime');
            const durationSelect = row.find('.duration');
            const endTimeInput = row.find('.endTime');

            // If not first row, set start time to previous row's end time
            if (previousEndTime) {
                const $optionToSelect = startSelect.find('option').filter(function () {
                    return $(this).attr('class') === previousEndTime;
                });

                if ($optionToSelect.length > 0) {
                    const valueToSet = $optionToSelect.val();
                    startSelect.val(valueToSet).trigger('change.select2');
                }
            }

            // Get start time and duration (same logic as updateEndTime)
            const startTimeStr = startSelect.find('option:selected').attr('class'); // "08:30:00"
            const durationStr = durationSelect.find('option:selected').attr('class'); // "00:40:00"

            if (startTimeStr && durationStr) {
                const [h1, m1, s1] = startTimeStr.split(':').map(Number);
                const [h2, m2, s2] = durationStr.split(':').map(Number);

                const startDate = new Date();
                startDate.setHours(h1);
                startDate.setMinutes(m1);
                startDate.setSeconds(s1);
                startDate.setMinutes(startDate.getMinutes() + h2 * 60 + m2);

                // format to "hh:mm AM/PM"
                let h = startDate.getHours();
                const m = startDate.getMinutes();
                const ampm = h >= 12 ? 'PM' : 'AM';
                h = h % 12;
                h = h ? h : 12;
                const formatted = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')} ${ampm}`;

                endTimeInput.val(formatted);

                // Save this end time in class format for next row (HH:MM:SS)
                const hh = startDate.getHours().toString().padStart(2, '0');
                const mm = startDate.getMinutes().toString().padStart(2, '0');
                previousEndTime = `${hh}:${mm}:00`;
            } else {
                endTimeInput.val('');
                previousEndTime = null;
            }
        });
    }

    function updateEndTime(element) {
        const row = $(element).closest('tr');
        const startTimeStr = row.find('.startTime option:selected').attr('class'); // from class = "08:30:00"
        const durationStr = row.find('.duration option:selected').attr('class');   // from class = "00:30:00"
        const endTimeInput = row.find('.endTime');

        if (startTimeStr && durationStr) {
            const [h1, m1, s1] = startTimeStr.split(':').map(Number);
            const [h2, m2, s2] = durationStr.split(':').map(Number);

            const startDate = new Date();
            startDate.setHours(h1);
            startDate.setMinutes(m1);
            startDate.setSeconds(s1);
            startDate.setMinutes(startDate.getMinutes() + h2 * 60 + m2);

            let h = startDate.getHours();
            const m = startDate.getMinutes();
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12;
            h = h ? h : 12;
            const formatted = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')} ${ampm}`;
            endTimeInput.val(formatted);
        } else {
            endTimeInput.val('');
        }
    }

    let rowCounter = 0;

    $('.btnAddTeaching').on('click', function () {
        let $lastRow = $('#scheduleTable tbody tr:last');
        let lastEndTime = $lastRow.find('.endTime').val()?.trim();
        const currentRowIndex = rowCounter++;
        const type = $(this).data('type'); // 'teaching' or 'non-teaching'
        const optionsHtml = (type === 'teaching')
            ? $('#subjectOptions').html()
            : $('#nonTeachingOptions').html();
        const inputClass = (type === 'teaching') ? 'teaching-style' : 'nonteaching-style';
        const isTeaching = (type === 'teaching') ? 'teaching' : 'non-teaching';

        if (lastEndTime) {
            const [h, m] = lastEndTime.split(':');
            const date = new Date();
            date.setHours(parseInt(h));
            date.setMinutes(parseInt(m));
            lastEndTime = date.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
        }

        let startTimeOptions = '';
        $('#timetableOptions option').each(function () {
            const timeChar = $(this).text().trim();
            const timeValue = $(this).val();
            const timeClass = $(this).attr('class');

            if (lastEndTime && timeChar === lastEndTime) {
                startTimeOptions += `<option selected value="${timeValue}" class="${timeClass || ''}">${timeChar}</option>`;
            } else {
                startTimeOptions += `<option value="${timeValue}" class="${timeClass || ''}">${timeChar}</option>`;
            }
        });

        let durationOptions = $('#durationOptions').html();
        let subjectOptions = $('#subjectOptions').html();
        let teacherOptions = $('#teacherOptions').html();

        const newRow = `
            <tr>
                <td>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-danger btn-xs btnRemoveRow">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                        <div class="input-group-append flex-grow-1">
                            <select class="form-control form-control-sm startTime" name="start_time_id[]" onchange="triggerRecalculateWithDelay();" style="width: 90%;">
                                <option></option>
                                ${startTimeOptions}
                            </select>
                        </div>
                    </div>
                </td>
                <td>
                    <select class="form-control form-control-sm duration" name="duration_id[]" onchange="triggerRecalculateWithDelay();" style="width: 100%;">
                        <option></option>
                        ${durationOptions}
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm endTime ${inputClass}" readonly name="end_time_id[]">
                </td>
                <td style="width: 200px;">
                    <select class="form-control form-control-sm select2-subject" name="subject_id[]" style="width: 150px;">
                        <option></option>
                        ${optionsHtml}
                    </select>
                </td>
                <td style="width: 200px;">
                    <input type="hidden" name="is_teaching[]" value="${isTeaching}">
                    <select class="form-control form-control-sm select2-teacher" name="teacher_id[]" style="width: 150px;">
                        <option></option>
                        ${teacherOptions}
                    </select>
                </td>
                <td align="center">
                    <input type="radio" style="margin-top:10px;margin-left:20px" name="advisory" value="${currentRowIndex}">
                </td>
            </tr>
        `;

        $('#scheduleTable tbody').append(newRow);

        const $newRow = $('#scheduleTable tbody tr:last');

        // Re-init select2 for subject
        $newRow.find('.select2-subject').select2({
            dropdownParent: $('#modalSbjctAssPrsnnl'),
            templateResult: function (data) {
                if (!data.id) return data.text;
                const html = $(data.element).attr('data-html');
                return html ? $(html) : data.text;
            },
            templateSelection: function (data) {
                return data.text;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });

        // Init teacher, duration, startTime
        $newRow.find('.select2-teacher, .duration, .startTime').select2({
            dropdownParent: $('#modalSbjctAssPrsnnl'),
            width: '100%' // Optional for cleaner layout
        });

        // ✅ Auto-calculate end time
        updateEndTime($newRow.find('.duration')[0]);
    });


    // Remove row
    $('#scheduleTable').on('click', '.btnRemoveRow', function () {
        $(this).closest('tr').remove();
        triggerRecalculateWithDelay();
    });

    $('#subjectAssgnContainer').on('click', '.btnRemoveRow', function () {
        $(this).closest('tr').remove();
        triggerRecalculateWithDelay(); // Optional
    });

    $('#subjectAssgnContainer .select2-subject, .select2-teacher, .duration, .startTime').select2({
        dropdownParent: $('#modalSbjctAssPrsnnl'), // or your actual modal/container
        width: '100%'
    });

    // Re-bind any necessary JS
    triggerRecalculateWithDelay();
    


    $('#btnRecalculate').on('click', function () {
        updateAllEndTimes();
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

        // if (b == f1) {
        //     getFetchList(f1, "RegionList", null, 1, {
        //         v: null
        //     }, 1, 1);
        //     getFetchList(f1, "ProvinceList", null, 1, {
        //         v: 16
        //     }, 1, 1);
        //     getFetchList(f1, "CityMunList", null, 1, {
        //         v: 1602
        //     }, 1, 1);
        //     getFetchList(f1, "BarangayList", null, 1, {
        //         v: 160201
        //     }, 1, 1);
        // }
    }

    function delay(form, a, b) {
        setTimeout(function() {
            $("#form_save_data" + form + " [name='" + b + "']").val(a);
            $("#form_save_data" + form + " [name='" + b + "']").trigger("change");
        }, 1000)
    }

    function getDetails(a, b, c) {
        // hideUpdate();
        // clear_form("form_save_data" + a);
        $("#form_save_data" + a + " .submitBtnPrimary").html(c == 1 ? "Update Data" : "Save Data");
        c == 1 ? $("#form_save_data" + a + " .submitBtnPrimary").removeClass("btn-primary").addClass("btn-info") : $("#form_save_data" + a + " .submitBtnPrimary").removeClass("btn-info").addClass("btn-primary");
        $("#form_save_data" + a + " .clearBtn").html(c == 1 ? "cancel" : "clear");
        c == 1 ? $("#form_save_data" + a + " .clearBtn").removeClass("btn-gray").addClass("btn-danger") : $("#form_save_data" + a + " .clearBtn").removeClass("btn-danger").addClass("btn-gray");

        $.each(b, function(k, v) {
            $("#form_save_data" + a).each(function() {
                $("[name='" + k + "']").prop("checked", v);
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


    function dfltpwdchck(a) {
        if (a == true) {
            $('.pwd, .confirmpwd').val('');
            $('.pwd, .confirmpwd').attr('nr', 1);
            $('.pwd, .confirmpwd').removeClass('is-invalid border-danger');
            $('.fillpwd').slideUp();
            $("#form_save_dataPersonnelAccount .submitBtnPrimary").attr("disabled", false);
        } else {
            $('.pwd, .confirmpwd').attr('nr', 0);
            $('.fillpwd').slideDown();
        }
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
                // $(".address_details").removeClass("col-lg-12").addClass("col-lg-5");
            } else if (!g && !h) {
                $("#" + f + " .good").hide();
                $("#" + f + " .bad").hide();
                $("#" + f + " .submitBtnPrimary").attr("disabled", true);
            } else {
                $("#" + f + " .good").show();
                $("#" + f + " .bad").hide();
                $("#" + f + " .submitBtnPrimary").attr("disabled", false);
                // $(".address_details").removeClass("col-lg-5").addClass("col-lg-12");
            }
            // $("#" + f + " .submitBtnPrimary").attr("disabled", false);
        } else {
            $("#" + f + " .atleast").show();
            if (g.length == 0 && h.length == 0) {
                $("#" + f + " .submitBtnPrimary").attr("disabled", false);
            } else {
                $("#" + f + " .submitBtnPrimary").attr("disabled", true);
            }
        }
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
                $("#form_save_data" + formId + " .submitBtnPrimary").attr("disabled", true);
                $("#form_save_data" + formId + " .submitBtnPrimary").html("<span class=\"fa fa-spinner fa-pulse\"></span>");
            },
            success: function(data) {
                var d = JSON.parse(data);
                if (d.success == true) {
                    successAlert("Successfully Saved!");
                    clear_form(formId);
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
            buttons: tableId == 'PersonnelInfo' ? [
                    // {
                    //     text: "<i class='fa fa-cog'></i> Account",
                    //     action: function(e, dt, node, config) {
                    //         validateTable(tableId);
                    //     }
                    // }, 

                    // {
                    //     extend: 'print',
                    //     text: '<i class="fa fa-print"></i> Print',
                    //     header: "_excel"

                    // },
                    {
                        text: '<i class="fa fa-user"></i> NON-TEACHING ID',
                        action: function(e, dt, node, config) {
                            PreviewID('NT');
                        }

                    },
                    {
                        text: '<i class="fa fa-user"></i> TEACHING ID',
                        action: function(e, dt, node, config) {
                            PreviewID('T');
                        }

                    }
                ] : [] &&
                tableId == 'AllStudentLogs' ? [{
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Print',
                    header: "_excel"

                }, {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel"></i> Export Excel',
                    header: "_excel"

                }] : [] &&
                tableId == 'VisitorInfo' ? [
                    {
                        text: '<i class="fa fa-user"></i> PRINT ID',
                        action: function(e, dt, node, config) {
                            PreviewVID();
                        },

                    }
                ] : [],
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
                url: "<?= base_url($uri . '/getdata/get') ?>" + tableId,
                type: "POST",
                data: function(d) {
                    drawCounter++;
                    d.length = pl;
                    d.draw = drawCounter;
                    d.search.value = $('#tbl' + tableId + '_filter input').val();
                    // if (tableId == "PersonnelInfo") {
                    //     d.a = $("#form_save_dataPersonnelSearch").serialize();
                    // }
                    // if (tableId == "PersonnelInfo") {
                    //     d.search.value = $('#searchTable1').val();
                    // } else if (tableId == "GateInfo") {
                    //     d.search.value = $('#searchTable2').val();
                    // }
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

        if (tableId == "SubjectList") {
            getFetchList('GradeSubject', "SubjectList", "PartyList", 0, {
                v: 17
            }, 0);
            getFetchList('GradeSubject', "ProgStranList", "PartyList", 0, {
                v: 22
            }, 0);

            // setTimeout(() => {
            //     arr = [];
            //     $("#form_save_dataGradeSubject .selectSubjectList option").each(function() {
            //         let a = $(this).attr("value");
            //         a != '' ? arr.push($(this).attr("value")) : null;
            //     });
            //     // console.log(arr)
            // }, 3000);

        }

        if (tableId == "ProgramList") {
            getFetchList('GradeSecInfo', "ProgStranList", "PartyList", 0, {
                v: 22
            }, 0);
        }
    }

    function PreviewID(aa){
        var bg_url_f = (aa=='T'?'url(<?= $system_bg_t_front_id ?>)':'url(<?= $system_bg_nt_front_id ?>)')
        var bg_url_b = (aa=='T'?'url(<?= $system_bg_t_back_id ?>)':'url(<?= $system_bg_nt_back_id ?>)')
        var g = "";
        var g = "";
        var c = [];
        var e = "";
        var ee = "";
        var grd_q = "";

        var minFontSize = 12;
        var maxFontSize = 24;
        var maxWidth = 360;


        $("#modalPreviewID #tblPreviewID").empty();
        $.get("<?= base_url($uri . '/getdata/getPreviewPersonnelID') ?>", {
                f: aa,
            },
            function(data) {
                var d = JSON.parse(data);
                let z = 0;
                let x = 0;
                for (let i = 0; i < d.length; i++) {
                    x = i;


                    $("#modalPreviewID #tblPreviewID").append('<tr>');
                    for (let j = 1; j <= 1; j++) {
                        let ji = i;
                        g_s = d[ji]["grade"] + ' - ' + d[ji]["sctn_nm"];
                        let empid = d[ji]["empid"];
                        let img_path = d[ji]["img_path"];
                        let full_name = d[ji]["full_name"];
                        let last_name = d[ji]["last_name"];
                        let first_minitial = d[ji]["first_minitial"];
                        let birthdate = d[ji]["birthdate"];
                        let emp_type = d[ji]["emp_type"];
                        let title = d[ji]["title"];
                        let add_details = d[ji]["address_details"];
                        let other_details = d[ji]["other_details"];
                        let od = JSON.parse(other_details);
                        let ioeName = ifnull(d[ji]['ioeName']);
                        let ioeNumber = ifnull(d[ji]['ioeNumber']);
                        let ioeAddress = ifnull(d[ji]['ioeAddress']);
                        $("#modalPreviewID #tblPreviewID").append(
                            '<td align="left" width="50%" style="border: 1px dashed #000;padding: 2.5px;">'+
                                '<table cellspacing="0" style="font-size:10px;border:2.5px solid #3786A3;background-image: '+bg_url_f+';background-repeat: no-repeat;background-size: 100% 100%;width: 360px;height: 530px;">'+
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
                                    '<!-- img -->'+
                                    '<tr align="center" style="height:1rem;border: 1px solid #000;font-size:5px;">'+
                                        '<td class="border border-transparent"></td>'+
                                        '<td rowspan="4" colspan="10" class="border border-transparent" style="padding:0;vertical-align: top;width:1rem;">'+
                                            '<img name="previewPic" src="' + img_path + '" class="" alt="User Image" style="border:2px solid #007bff;border-radius:5px;padding:0;margin:0;vertical-align:top;width:100%;height:11.1rem;">'+
                                        '</td>'+
                                        '<td class="border border-transparent"></td>'+
                                        '<td colspan="6" class="border border-transparent" style="font-family:Montserrat, sans-serif;font-weight:bold;font-size:10px;vertical-align:bottom;">'+
                                        '</td>'+
                                        '<td class="border border-transparent"></td>'+
                                        '<td class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1rem;border: 1px solid #000;font-size:5px;">'+
                                        '<td class="border border-transparent"></td>'+
                                        '<td class="border border-transparent"></td>'+
                                        '<td colspan="6" class="border border-transparent" style="font-family:Montserrat, sans-serif;font-weight:bold;font-size:10px;vertical-align:bottom;">'+
                                            '<p style="padding:0;margin-bottom:-1px;color:#240AA2">S.Y. <?= $sy_; ?></p>'+
                                        '</td>'+
                                        '<td class="border border-transparent"></td>'+
                                        '<td class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1rem; border: 1px solid #000;font-size:7px;">'+
                                        '<td class="border border-transparent"> </td>'+
                                        '<td class="border border-transparent"> </td>'+
                                        '<td colspan="6" style="background-color:#fff">'+
                                            '<div id="qqqq1'+i+'" style="padding:4px;"></div>'+
                                        '</td>' +
                                        '<td class="border border-transparent"> </td>'+
                                        '<td class="border border-transparent"> </td>'+
                                    '</tr>'+
                                    '<!-- barcode -->'+
                                    '<!-- empid -->'+
                                    '<tr align="center" style="height:1rem; border: 1px solid #000;font-size:7px;">'+
                                        '<td class="border border-transparent"></td>'+
                                        '<td colspan="8" class="border border-transparent" style="font-family:Montserrat, sans-serif;font-weight:800;font-size:12px;vertical-align:bottom;">'+
                                            '<p style="padding:0;letter-spacing:1px; display: inline;">' + empid + '</p><br/>'+
                                            '<p style="padding:0;font-family:Montserrat, sans-serif;font-weight:600;font-size:12px;display: inline;color:#E82B2B">'+ emp_type +'</p>'+
                                        '</td>'+
                                        '<td class="border border-transparent"> </td>'+
                                    '</tr>'+
                                    '<!-- signature -->'+
                                    '<tr align="center" style="height:.1rem; border: 1px solid #000;font-size:7px;">'+
                                        '<td class="border border-transparent"> </td>'+
                                        '<td class="border border-transparent"> </td>'+
                                        '<td colspan="6" class="border border-transparent"> </td>'+
                                        '<td class="border border-transparent"> </td>'+
                                        '<td class="border border-transparent"> </td>'+
                                    '</tr>'+
                                    '<!-- lastname -->'+
                                    '<tr align="center" style="height:1.4rem; border: 1px solid #000;padding:0">'+
                                        '<td class="border border-transparent"> </td>'+
                                        '<td colspan="18" class="border border-transparent text-left" style="font-family:Montserrat, sans-serif;color:#102C3D;font-weight:800;font-size:' + autoSizeFont(last_name, 12, 32, 175) + 'px;padding:0 0 5px 0;">'+
                                            '<p class="mb-n4 mt-n1">'+ last_name +'</p>'+
                                        '</td>'+
                                        '<td class="border border-transparent"> </td>'+
                                    '</tr>'+
                                    '<!-- firstname middleinitial -->'+
                                    '<tr align="center" style="height:1rem; border: 1px solid #000;">'+
                                        '<td class="border border-transparent"> </td>'+
                                        '<!-- <td colspan="18" class="border border-transparent"> </td> -->'+
                                        '<td colspan="18" class="border border-transparent text-left" style="font-family:Montserrat, sans-serif;color:#102C3D;font-weight:500;font-size:' + autoSizeFont(first_minitial, 12, 28, 200) + 'px;padding:5px 0 0 0;">'+
                                            '<p class="mb-n3 mt-1">'+ first_minitial +'</p>'+
                                        '</td>'+
                                        '<td class="border border-transparent"> </td>'+
                                    '</tr>'+
                                    '<!-- grade section League Gothic -->'+
                                    '<tr align="center" style="height:1.2rem; border: 1px solid #000;">'+
                                        '<td class="border border-transparent"> </td>'+
                                        '<td colspan="18" class="border border-transparent text-left" style="font-family:League Gothic, sans-serif;color:#E82B2B;font-size:1.5rem;padding-top:20px;">'+
                                            '<p class="mb-n2">' + title +'</p>'+
                                        '</td>'+
                                        // '<td colspan="13" class="border border-transparent text-left" style="font-family:League Gothic, sans-serif;font-weight:300;font-size:' + autoSizeFont(g_sec, 14, 18, 175) + 'px;padding-top:10px;">'+
                                            // '<p class="mb-n2">'+g_sec+'</p>'+
                                        // '</td>'+
                                        '<td class="border border-transparent"> </td>'+
                                    '</tr>'+
                                    '<!-- program/strand -->'+
                                    '<tr align="center" style="height:1.2rem; border: 1px solid #000;">'+
                                        '<td class="border border-transparent"> </td>'+
                                        '<td colspan="5" class="border border-transparent text-left" style="font-family:League Gothic, sans-serif;color:#41A1DF;font-size:1.1rem;padding-top:0px;">'+
                                            // '<p class="mb-n2">Program/Strand:</p>'+
                                        '</td>'+
                                        '<td colspan="13" class="border border-transparent text-left" style="font-family:League Gothic, sans-serif;color:forestgreen;font-weight:300;font-size:1px;padding:0;">'+
                                            // '<p class="mb-n2" style="color:'+program_strand_color+'">'+program_strand+'</p>'+
                                        '</td>'+
                                        '<td class="border border-transparent"> </td>'+
                                    '</tr>'+
                                    '<!-- adviser -->'+
                                    '<tr align="center" style="height:1.2rem; border: 1px solid #000;">'+
                                        '<td class="border border-transparent"> </td>'+
                                        '<td colspan="5" class="border border-transparent text-left" style="font-family:League Gothic, sans-serif;color:#41A1DF;font-size:1.1rem;padding-top:0px;">'+
                                            // '<p class="mb-n2">Adviser:</p>'+
                                        '</td>'+
                                        '<td colspan="13" class="border border-transparent text-left" style="font-family:League Gothic, sans-serif;font-weight:300;font-size:1px;padding:0;">'+
                                            // '<p class="mb-n2">'+advisory+'</p>'+
                                        '</td>'+
                                        '<td class="border border-transparent"> </td>'+
                                    '</tr>'+
                                    '<!-- school year -->'+
                                    '<!-- qrcode -->'+
                                    '<tr stye="border: 1px solid #000;font-size:1px;height:1rem;">'+
                                        '<td colspan="20"> </td>'+
                                    '</tr>'+
                                '</table>'+
                            '</td>'+

                            '<td align="right" width="50%" style="border: 1px dashed #000;padding: 2.5px;">'+
                                '<table cellspacing="0" style="font-size:10px;border:2.5px solid #3786A3;background-image: '+bg_url_b+';background-repeat: no-repeat;background-size: 100% 100%;width: 360px;height: 530px;">'+
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
                                            '<div id="qqqq2'+i+'" style="text-align:center;width:100%;height:100%;padding:5px;"></div>'+
                                        '</td>'+
                                        '<td colspan="6" class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1.4rem; border: 1px solid #000;font-size:7px;">'+
                                        '<td colspan="20" class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1.45rem; border: 1px solid #000;font-size:7px;">'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                        '<td colspan="16" class="border border-transparent" style="font-family:Montserrat, sans-serif;color:#000;font-weight:800;font-size:' + autoSizeFont(ioeName, 9, 20, 190) + 'px;padding:0;">'+
                                            '<p class="mb-n2 mt-n1">'+ioeName.toUpperCase()+'</p>'+
                                        '</td>'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1rem; border: 1px solid #000;">'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                        '<td colspan="16" class="border border-transparent" style="font-family:League Gothic, sans-serif;color:#1E34AB;font-weight:300;font-size:15px;padding:0;">'+
                                            '<p class="mb-n2 mt-n2">CONTACT PERSON</p>'+
                                        '</td>'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1rem; border: 1px solid #000;">'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                        '<td colspan="16" class="border border-transparent" style="font-family:League Gothic, sans-serif;color:#000;font-weight:300;font-size:' + autoSizeFont(ioeNumber, 9, 15, 190) + 'px;padding:0;">'+
                                            '<p class="mb-n2 mt-n2">'+ioeNumber.toUpperCase()+'</p>'+
                                        '</td>'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1rem; border: 1px solid #000;">'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                        '<td colspan="16" class="border border-transparent" style="font-family:League Gothic, sans-serif;color:#000;font-weight:300;font-size:' + autoSizeFont(ioeAddress, 9, 15, 190) + 'px;padding:0;">'+
                                            '<p class="mb-n2 mt-n2">'+ioeAddress.toUpperCase()+'</p>'+
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
                        QR_BAR_Generator(i, empid, 'default')
                    }
                    g = "";
                    $("#modalPreviewID #tblPreviewID").append('</tr>');

                }
                $("#modalPreviewID .header").html("Student ID preview")
                $("#modalPreviewID").modal("show");
            }).done(function() {
            $('[data-toggle="tooltip"]').tooltip()
        });

    }

    

    function PreviewVID(){
        var bg_url_f = 'url(<?= $system_bg_v_front_id ?>)';
        var bg_url_b = 'url(<?= $system_bg_v_back_id ?>)';
        var g = "";
        var g = "";
        var c = [];
        var e = "";
        var ee = "";
        var grd_q = "";

        var minFontSize = 12;
        var maxFontSize = 24;
        var maxWidth = 360;


        $("#modalPreviewID #tblPreviewID").empty();
        $.get("<?= base_url($uri . '/getdata/getPreviewVisitorID') ?>",
            function(data) {
                var d = JSON.parse(data);
                let z = 0;
                let x = 0;
                for (let i = 0; i < d.length; i++) {
                    x = i;


                    $("#modalPreviewID #tblPreviewID").append('<tr>');
                    for (let j = 1; j <= 1; j++) {
                        let ji = i;
                        let v_id = d[ji]["visitor_id"];
                        let cc = d[ji]["count"];
                        $("#modalPreviewID #tblPreviewID").append(
                            '<td align="left" width="50%" style="border: 1px dashed #000;padding: 2.5px;">'+
                                '<table cellspacing="0" style="font-size:10px;border:2.5px solid #3786A3;background-image: '+bg_url_f+';background-repeat: no-repeat;background-size: 100% 100%;width: 360px;height: 530px;">'+
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
                                    '<tr align="center" style="height:4rem; border: 1px solid #000;font-size:5px;">'+
                                        '<td colspan="6" class="border border-transparent"></td>'+
                                        '<td colspan="8" class="border border-transparent" style="vertical-align: top;width:1rem;background-color:#fff;">'+
                                            '<div id="qqqq1'+i+'" style="text-align:center;width:100%;height:100%;padding:5px;"></div>'+
                                        '</td>'+
                                        '<td colspan="6" class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1.4rem; border: 1px solid #000;font-size:7px;">'+
                                        '<td colspan="20" class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1.45rem; border: 1px solid #000;font-size:7px;">'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                        '<td colspan="16" class="border border-transparent" style="font-family:Montserrat, sans-serif;color:#000;font-weight:800;padding:0;">'+
                                        '</td>'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1rem; border: 1px solid #000;">'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                        '<td colspan="16" class="border border-transparent" style="font-family:League Gothic, sans-serif;color:#1E34AB;font-weight:300;font-size:15px;padding:0;">'+
                                        '</td>'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1rem; border: 1px solid #000;">'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                        '<td colspan="16" class="border border-transparent" style="font-family:League Gothic, sans-serif;color:#000;font-weight:300;padding:0;">'+
                                        '</td>'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1rem; border: 1px solid #000;">'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                        '<td colspan="16" class="border border-transparent" style="font-family:League Gothic, sans-serif;color:#000;font-weight:300;padding:0;">'+
                                        '</td>'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1rem; border: 1px solid #000;">'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                        '<td colspan="16" class="border border-transparent" style="font-family:Montserrat, sans-serif;font-size:4rem;color:#240aa2;font-weight:800;padding:0;">'+
                                            '<p class="mb-n2 mt-n1">'+cc+'</p>'+
                                        '</td>'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr stye="border: 1px solid #000;font-size:1px;height:1rem;">'+
                                        '<td colspan="20"> </td>'+
                                    '</tr>'+
                                '</table>'+
                            '</td>'+

                            '<td align="right" width="50%" style="border: 1px dashed #000;padding: 2.5px;">'+
                                '<table cellspacing="0" style="font-size:10px;border:2.5px solid #3786A3;background-image: '+bg_url_b+';background-repeat: no-repeat;background-size: 100% 100%;width: 360px;height: 530px;">'+
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
                                            '<div id="qqqq2'+i+'" style="text-align:center;width:100%;height:100%;padding:5px;"></div>'+
                                        '</td>'+
                                        '<td colspan="6" class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1.4rem; border: 1px solid #000;font-size:7px;">'+
                                        '<td colspan="20" class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1.45rem; border: 1px solid #000;font-size:7px;">'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                        '<td colspan="16" class="border border-transparent" style="font-family:Montserrat, sans-serif;color:#000;font-weight:800;padding:0;">'+
                                        '</td>'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1rem; border: 1px solid #000;">'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                        '<td colspan="16" class="border border-transparent" style="font-family:League Gothic, sans-serif;color:#1E34AB;font-weight:300;font-size:15px;padding:0;">'+
                                        '</td>'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1rem; border: 1px solid #000;">'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                        '<td colspan="16" class="border border-transparent" style="font-family:League Gothic, sans-serif;color:#000;font-weight:300;padding:0;">'+
                                        '</td>'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1rem; border: 1px solid #000;">'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                        '<td colspan="16" class="border border-transparent" style="font-family:League Gothic, sans-serif;color:#000;font-weight:300;padding:0;">'+
                                        '</td>'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                    '</tr>'+
                                    '<tr align="center" style="height:1rem; border: 1px solid #000;">'+
                                        '<td colspan="2" class="border border-transparent"></td>'+
                                        '<td colspan="16" class="border border-transparent" style="font-family:Montserrat, sans-serif;font-size:4rem;color:#240aa2;font-weight:800;padding:0;">'+
                                            '<p class="mb-n2 mt-n1">'+cc+'</p>'+
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
                        QR_BAR_Generator(i, v_id, 'visitor')
                    }
                    g = "";
                    $("#modalPreviewID #tblPreviewID").append('</tr>');

                }
                $("#modalPreviewID .header").html("Student ID preview")
                $("#modalPreviewID").modal("show");
            }).done(function() {
            $('[data-toggle="tooltip"]').tooltip()
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

    // function getTablez(tableId, dtd, pl) {
    //     var drawCounter = 0;
    //     $("#tbl" + tableId).DataTable().destroy();
    //     var table_data = $("#tbl" + tableId).DataTable({

    //         "processing": true,
    //         "serverSide": true,
    //         ajax: {
    //             url: "<?= base_url($uri . '/getdata/get') ?>" + tableId,
    //             type: "POST",
    //             data: function(d) {
    //                 // Calculate the offset (start) based on the page number
    //                 // d.start = (d.start / d.length) + 1;
    //                 // d.length = pl; // Set the limit (length) to the desired value
    //                 // d.draw = d.draw || 1; // Set the default value to 1 if not provided

    //                 drawCounter++;
    //                 d.draw = drawCounter;
    //                 // Include the search value in the AJAX request
    //                 d.search.value = $('.dataTables_filter input').val();
    //             }
    //         },
    //         "lengthMenu": [10, 25, 50, 100], // Options for records per page
    //         "pageLength": 10,
    //     });

    //     // Rest of your code...
    // }

    function searchPersonnel() {
        alert('a')
    }

    function tblReload(tableId) {
        // $("#tbl" + tableId).DataTable().ajax.reload();

        let table = $("#tbl" + tableId).DataTable();

        table.ajax.reload(function () {
            // After reload is complete and DOM is updated
        }, false); // false = keep current pagination


        
        // $('.select2').select2()
        // $('.select2bs4').select2({
        //     theme: 'bootstrap4'
        // });
        // $('[data-toggle="tooltip"]').tooltip()
        // $('.select2').each(function () {
        //     let $parent = $(this).closest('.modal');
        //     $(this).select2({
        //         dropdownParent: $parent.length ? $parent : $('body') // fallback if not inside a modal
        //     });
        // });
        
    }

    function getSbjctAssPrsnnlFN2(module, grade_id, rmsecid) {
        // Optional: open modal or panel
        $('#modalSubjectAssignment').modal('show'); // if using modal

        // Store rmsecid somewhere for reuse
        $('#subjectAssgnContainer').data('rmsecid', rmsecid);

        // Trigger first tab (Monday) to load
        $('#dayTabs .nav-link[data-day="Monday"]').trigger('click');
    }

    // $(document).ready(function () {
    //     console.log(dayTabs('Monday'));
    // });

    function getSbjctAssPrsnnlFN(tableId, a, b) {
        grdlvl = a;
        rmid = b;
        tblReload(tableId);
    }

    // $(document).on('click', '#dayTabs .nav-link', function () {
    //     const day = $(this).data('day');
    //     dayTabs(day);
    // });
        
    function dayTabs(day, tableId = null) {
        const rmsecid = $('#form_save_dataSbjctAssPrsnnl input[name="rmsecid"]').val();

        $('#dayTabs .nav-link').removeClass('active');
        $(`#dayTabs .nav-link[data-day="${day}"]`).addClass('active');

        $.get("<?= base_url($uri . '/getdata/getScheduleByDay') ?>", {
            rmsecid: rmsecid,
            day_of_week: day
        }, function(res) {
            $('#subjectAssgnContainer').html(res); // Now it works — because response is HTML
        });
    }

    function getSbjctAssPrsnnl(tableId) {
        $("#tbl" + tableId).DataTable().destroy();
        var table, table_data = $("#tbl" + tableId).DataTable({
            "order": [
                [0, "asc"]
            ],
            dom: 'Bfrtip',
            buttons: [],
            searching: false,
            "info": false,
            "paging": false,
            "ordering": false,
            "oLanguage": {
                "sSearch": ""
            },
            // language: {
            //     searchPlaceholder: "Search...",
            // },
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
            // $("#tbl" + tableId).DataTable().destroy();

            // $(".searchBtn").attr("disabled", false);
            // $(".searchBtn").html("<span class=\"fa fa-search\"></span>");
            $("#form_save_data" + tableId + " .select" + tableId).select2();

            // $(".searchBtn").attr("disabled", false);
            // $(".searchBtn").html("<span class=\"fa fa-search\"></span>");
            // $("#tbl" + tableId).DataTable().destroy();
            // $(".collapse" + tableId).trigger('click');

            grdlvl != 0 ? $("#modal" + tableId).modal('show') : "";
        });
        // $("#tbl"+tableId+"_filter").addClass("row");
        // $("#tbl"+tableId+"_filter label").css("width","99%");
        // $("#tbl"+tableId+"_filter .form-control-sm").css("width","99%");
    }

    function QR_BAR_Generator(a, b, c) {
        var qrcode = new QRCode(document.getElementById("qqqq1"+a), {
            text: '7' + b,
            height: (c == 'visitor' ? 133 : 100),
            width: (c == 'visitor' ? 133 : 100)
        });
        var qrcode = new QRCode(document.getElementById("qqqq2"+a), {
            text: '6' + b,
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

    function ifnull(a) {
        let b = a;
        if ((a == "null") || (a == null) || (a == "") || (a == '')) {
            // alert(a)
            b = '-';
        }
        return b;
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
    }

    $('#form_save_dataGradeSubject .selectSubjectList').on("select2:select", function(e) {
        // console.log('a')
        var unselected_value = $(this).val();
        // console.log(unselected_value);
    }).trigger('change');


    function getSelectSubject() {
        var where = $("#form_save_dataGradeSubject .selectGradeList").val();
        var where2 = $("#form_save_dataGradeSubject .selectProgStranList").val();
        $.post("<?= base_url($uri . '/getdata/getGradeSubjectList') ?>", {
                v: where,
                v2: where2
            },
            function(data) {
                var result = JSON.parse(data);
                var data = [];
                if (!result.length) {
                    for (var i = 0; i < result['data'].length; i++) {
                        data.push(result['data'][i]['subject_id']);
                    }
                    $("#form_save_dataGradeSubject .selectSubjectList").val(data);
                    $("#form_save_dataGradeSubject .selectSubjectList").trigger('change');
                }
            }
        ).then(function() {});
    }

    var invalidChars = [
        "-",
        "+",
        "e",
    ];

    function viewRegionProvince() {
        $(".region_province").toggle("slow", function() {
            if ($(".region_province").is(":visible")) {
                $(".address_details").removeClass("col-lg-12").addClass("col-lg-5");
            } else {
                $(".address_details").removeClass("col-lg-5").addClass("col-lg-12");
            }
        });
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
            '<link rel="stylesheet" href="<?= base_url() ?>dist/css/adminlte.min.css">'+
            // '<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">'+
            // '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=League+Gothic&display=swap">'+
            // '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;500;600;800&display=swap">'+
            // '<link rel="preconnect" href="https://fonts.googleapis.com">'+
            // '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'+
            '<link rel="stylesheet" href="<?= base_url() ?>dist/css/fonts.css">'+
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