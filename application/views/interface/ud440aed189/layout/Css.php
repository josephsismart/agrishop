<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title> <?= $system_title ?> | <?= $page_title ?></title>
<!-- Tell the browser to be responsive to screen width -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Font Awesome -->
<link rel="icon" type="image/png" href="<?= $system_svg ?>">
<link rel="stylesheet" href="<?= base_url() ?>plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?= base_url() ?>plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>plugins/fontawesome-free/css/all.min.css">
<!-- Ionicons -->
<!-- SweetAlert2 -->
<link rel="stylesheet" href="<?= base_url() ?>plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<!-- Toastr -->
<link rel="stylesheet" href="<?= base_url() ?>plugins/toastr/toastr.min.css">
<!-- DataTables -->
<link rel="stylesheet" href="<?= base_url() ?>plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>plugins/datatables/extensions/buttons/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="<?= base_url() ?>plugins/datatables/extensions/responsive/css/responsive.dataTables.css">
<link rel="stylesheet" href="<?= base_url() ?>dist/css/adminlte.min.css">
<!-- Google Font: Source Sans Pro -->
  <!-- Select2 -->
<style type="text/css">
	.error{
        outline: 1px solid red;
    }

    /*highcharts*/
    .highcharts-figure, .highcharts-data-table table {
        min-width: 360px; 
        max-width: 800px;
        margin: 1em auto;
    }

    .highcharts-data-table table {
      font-family: Arial;
      border-collapse: collapse;
      border: 1px solid #EBEBEB;
      margin: 10px auto;
      text-align: center;
      width: 100%;
      max-width: 500px;
    }
    .highcharts-data-table caption {
        padding: 1em 0;
        font-size: 12px;
        color: #555;
    }
    .highcharts-data-table th {
      font-weight: 600;
        padding: 0.5em;
    }
    .highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
        padding: 0.5em;
    }
    .highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
        background: #f8f8f8;
    }
    .highcharts-data-table tr:hover {
        background: #f1f7ff;
    }

    #chart5,#myContact h4 {
        text-transform: none;
        font-size: 12px;
        font-weight: normal;
        font-family: Arial;
    }
    #chart5,#myContact p {
        font-size: 12px;
        line-height: 16px;
        font-family: Arial;
    }

    @media screen and (max-width: 600px) {
        #chart5,#myContact h4 {
            font-size: 2.3vw;
            line-height: 3vw;
            font-family: Arial;
        }
        #chart5,#myContact p {
            font-size: 2.3vw;
            line-height: 3vw;
            font-family: Arial;
        }
    }
</style>