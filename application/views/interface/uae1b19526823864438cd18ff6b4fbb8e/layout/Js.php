<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!$this->session->schoolmis_login_level) {
    redirect(base_url('login'));
}
$uri = $this->session->schoolmis_login_uri;
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
<script src="<?= base_url() ?>plugins/datatables/extensions/responsive/js/dataTables.responsive.min.js"></script>
<!-- SweetAlert2 -->
<script src="<?= base_url() ?>plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Toastr -->
<script src="<?= base_url() ?>plugins/toastr/toastr.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url() ?>dist/js/adminlte.min.js"></script>
<script src="<?= base_url() ?>plugins/qrcode/js/qrcode-reader.min.js?v=20190604"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $(".scanner_announcement .scanning").slideUp();
    });


    let lastActivityTime = Date.now();
    let intervalId = null;
    // Function to check activity and toggle visibility accordingly
    function checkActivity() {
        // const currentTime = Date.now();
        // const idleTime = currentTime - lastActivityTime;
        // console.log(idleTime)
        clearInterval(intervalId);
        let timeLeft = 10;
        intervalId = setInterval(function() {
            timeLeft--;
            console.log(timeLeft)

            if (timeLeft === 0) {
                clearInterval(intervalId);
                // Add any additional code you want to run when timeLeft is -1
                console.log("Time's up!");
                $(".scanner_announcement .scanning").slideUp();
                $(".scanner_announcement .announcements").slideDown();
            }
        }, 1000);

        // if (idleTime >= 5000) { // 10 seconds idle time
        //     $(".scanner_announcement .scanning").hide();
        //     $(".scanner_announcement .announcements").show();
        // }
    }

    // Start continuously checking activity
    // continuouslyCheckActivity();
    function vdetailszzz() {
        getQRPerson({
            v: "7132117110057",
            g_id: $("#gate_select").val(),
            g_nm: $("#gate_select option:selected").text()
        }, "GateSearch");
    }
    // continuouslyCheckActivity();
    function vdetailszzz1() {
        getQRPerson({
            v: "6132117110057",
            g_id: $("#gate_select").val(),
            g_nm: $("#gate_select option:selected").text()
        }, "GateSearch");
    }

    function vdetails() {
        if (!$("#gate_select").val()) {
            failAlert('Please Select Gate')
        } else {
            $(".view_details .header").text($("#gate_select option:selected").text())
            $(".view_details").toggle("slow", function() {});
            $(".get_selection").toggle("slow", function() {});
            if ($('#openreader-multi3').is(':hidden') == true) {
                $('#openreader-multi3').trigger('click');
            }

            // setInterval(function() {
            // getQRPerson({
            //     v: "7132117110057",
            //     g_id: $("#gate_select").val(),
            //     g_nm: $("#gate_select option:selected").text()
            // }, "GateSearch");

            // }, 8000)

            // setInterval(function() {
            //     getQRPerson({
            //         v: "7132117110057",
            //         g_id: $("#gate_select").val(),
            //         g_nm: $("#gate_select option:selected").text()
            //     }, "GateSearch");
            // }, 12000)

        }
    }

    function getTable(tableId, dtd, pl) {
        $("#tbl" + tableId).DataTable().destroy();
        var table, table_data = $("#tbl" + tableId).DataTable({
            // "order": [
            //     [0, "asc"]
            // ],
            dom: 'Bfrtip',
            buttons: tableId == 'SearchEnrollLearnersList' ? [
                // 'pageLength',
                {
                    text: "<i class='fa fa-check text-success'></i> Enroll",
                    action: function(e, dt, node, config) {
                        validateTable(tableId);
                    }
                },


            ] : [],
            searching: tableId == 'GradesList' ? false : true,
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
                type: "POST",
                data: function(d) {
                    d.rsid = rsid;
                    d.by = $("#searchby").val();
                    d.key = $("#keyword").val();
                }
            }
        });

        $("#tbl" + tableId).on('draw.dt', function() {
            $(".searchBtn").attr("disabled", false);
            $(".searchBtn").html("<span class=\"fa fa-search\"></span>");
            dtd == 1 ? $("#tbl" + tableId).DataTable().destroy() : "";
            $(".collapse" + tableId).trigger('click');
        });
        $("#tbl" + tableId + "_filter").addClass("row");
        $("#tbl" + tableId + "_filter label").css("width", "99.3%");
        $("#tbl" + tableId + "_filter .form-control-sm").css("width", "99.3%");
    }

    function getFetchList(formId, getList, getQ, s2, where, sel, e) {
        var q = getQ ? getQ : getList;
        $("#form_save_data" + formId + " .select" + getList).empty();
        $.post("<?= base_url('global/getdata/get') ?>" + q, where,
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

    function getQRPerson(where, form) {
        var f = "#form_save_data" + form;
        if (!$("#gate_select").val() || $('.view_details').is(':hidden') == true) {
            failAlert('Please Select Gate')
        } else {
            $.post("<?= base_url($uri . '/getdata/getQRPerson') ?>", where,
                function(data) {
                    var result = JSON.parse(data);
                    var inn = new Audio("<?= base_url() ?>plugins/qrcode/audio/in.mp3");
                    var outt = new Audio("<?= base_url() ?>plugins/qrcode/audio/out.wav");
                    var toink = new Audio("<?= base_url() ?>plugins/qrcode/audio/toink.mp3");
                    if (result["data"].length > 0) {
                        ioo = result["data"][0]['io'];
                        $(f + " #lname").text(result["data"][0]['lname']);
                        $(f + " #fname_mname").html(result["data"][0]['fname_mname']);
                        $(f + " #assignment").html(result["data"][0]['assignment']);
                        $(f + " #io_time").html("<b class='rounded px-2 bg-" + (ioo == 'IN' ? 'success' : 'danger') + "'>" + ioo + "</b>  " + result["data"][0]['time']);
                        // $(f + " #time").html();

                        var reader = new FileReader();
                        // console.log(result["data"][0]['pic'])
                        let io = where['v'][0];
                        // $("[name=previewPic]").val("");
                        // img = (d == 'FEMALE' ? 'defaultf.png' : 'defaultm.png');
                        $("[name=previewPic]").attr("src", result["data"][0]['pic']);
                        reader.onload = function(e) {
                            document.getElementById(b).src = e.target.result;
                        };
                        io == '7' ? inn.play() : (io == '6' ? outt.play() : toink.play());
                    } else {
                        failAlert("No Data found!");
                        toink.play();
                        $("[name=previewPic]").attr("src", "<?= base_url() ?>dist/img/media/icons/1x1.png");
                        $(f + " #name").text("No Data found!");
                        $(f + " #type").text("");
                    }
                    $("#qr").val("");
                    $("#openreader-multi3").trigger("click");
                }
            ).then(function() {
                // $(".btnViewContactT").attr("disabled", false);
            });
            $(".scanner_announcement .scanning").slideDown();
            $(".scanner_announcement .announcements").slideUp();
            checkActivity();
        }
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