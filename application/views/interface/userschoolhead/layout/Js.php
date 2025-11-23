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
<!-- SweetAlert2 -->
<script src="<?= base_url() ?>plugins/sweetalert2/sweetalert2.min.js"></script>
<!-- Toastr -->
<script src="<?= base_url() ?>plugins/toastr/toastr.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url() ?>dist/js/adminlte.min.js"></script>
<script type="text/javascript">
    $(".submitBtnMFGradelvl").click(function() {
        $(".submitBtnMFGradelvl").attr("disabled", true);
        $(".submitBtnMFGradelvl").html("<span class=\"fa fa-spinner fa-pulse\"></span>");
        var pl = 1;
        $("#tblMFGradelvl").DataTable().destroy();
        var table, table_data = $("#tblMFGradelvl").DataTable({
            ajax: {
                url: "<?= base_url($uri . '/reports/getMFGradelvl') ?>",
                type: "POST",
                data: function(d) {
                    d.a = $("#form_report_dataG4Ps").serialize();
                    return table_data;
                }
            },
            fnInitComplete: function(oSettings, json) {

                $("#tblMFGradelvl").DataTable().destroy();
                $("#modalMFGradelvl").modal("show");

                $(".submitBtnMFGradelvl").attr("disabled", false);
                $(".submitBtnMFGradelvl").html("<span class=\"fa fa-search\"></span>");
            }
        });

    });

    $(".submitBtnMPS").click(function() {
        $(".submitBtnMPS").attr("disabled", true);
        $(".submitBtnMPS").html("<span class=\"fa fa-spinner fa-pulse\"></span>");
        $.get("<?= base_url($uri_reports . "/reports/getMPS") ?>", {
                sy: $("#form_report_dataMPS [name='sy']").val(),
                qrtr: $("#form_report_dataMPS [name='qrtr']").val(),
            },
            function(data) {
                var d = JSON.parse(data);
                // console.log(d)
                // JHS MPS DATA FOR THE 4th QUARTER S.Y 2021 - 2022
                $("#modalMPS .header").html("MPS DATA FOR THE " + $("#form_report_dataMPS #qrtr option:selected").text() + "QUARTER S.Y. " + $("#form_report_dataMPS #sy option:selected").text())
                $("#modalMPS").modal("show");
                $("#modalMPS .viewMPS").html(d);
                $(".submitBtnMPS").attr("disabled", false);
                $(".submitBtnMPS").html("<span class='fa fa-search'></span>");
            }).done(function() {
            $('[data-toggle="tooltip"]').tooltip()
        });

    });

    $(".submitBtnGPA").click(function() {
        $(".submitBtnGPA").attr("disabled", true);
        $(".submitBtnGPA").html("<span class=\"fa fa-spinner fa-pulse\"></span>");
        $.get("<?= base_url($uri_reports . "/reports/getGPA") ?>", {
                sy: $("#form_report_dataGPA [name='sy']").val(),
                qrtr: $("#form_report_dataGPA [name='qrtr']").val(),
            },
            function(data) {
                var d = JSON.parse(data);
                // console.log(d)
                // JHS GPA DATA FOR THE 4th QUARTER S.Y 2021 - 2022
                $("#modalGPA .header").html("GPA DATA FOR THE " + $("#form_report_dataGPA #qrtr option:selected").text() + "QUARTER S.Y. " + $("#form_report_dataGPA #sy option:selected").text())
                $("#modalGPA").modal("show");
                $("#modalGPA .viewGPA").html(d);
                $(".submitBtnGPA").attr("disabled", false);
                $(".submitBtnGPA").html("<span class='fa fa-search'></span>");
            }).done(function() {
            $('[data-toggle="tooltip"]').tooltip()
        });

    });

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