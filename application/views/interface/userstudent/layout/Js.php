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

<script type="text/javascript">
    setInterval(function() {
        $.post("<?= base_url('Main/allow') ?>");
    }, 3000)
    var rsid = 0;

    function vdetails(){
        $(".view_details").toggle("slow", function(){
        });
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