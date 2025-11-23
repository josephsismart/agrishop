<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title> <?= $system_title ?> | <?= $page_title ?></title>
<!-- Tell the browser to be responsive to screen width -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Font Awesome -->
<link rel="icon" type="image/png" href="<?= $system_svg ?>">
<!-- Google Font: Source Sans Pro -->
<!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"> -->
<!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=League+Gothic&display=swap"> -->
<!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;500;600;800&display=swap"> -->
<!-- <link rel="stylesheet" href="<?= base_url() ?>plugins/fonts/LeagueGothic.css/"> -->
<!-- <link rel="stylesheet" href="<?= base_url() ?>plugins/fonts/Montserrat.css/"> -->
<!-- <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin> -->
<link rel="stylesheet" href="<?= base_url() ?>dist/css/fonts.css">
<link rel="stylesheet" href="<?= base_url() ?>plugins/fontawesome-free-6.4.2-web/css/all.min.css">
<link rel="stylesheet" href="<?= base_url() ?>plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="<?= base_url() ?>plugins/ol3/ol.css">
<link rel="stylesheet" href="<?= base_url() ?>plugins/ol3/Popup.css">
<link rel="stylesheet" href="<?= base_url() ?>plugins/ol3/LayersControl.css">
<link rel="stylesheet" href="<?= base_url() ?>plugins/ol3/ol3.css">
<!-- Ionicons -->
<!-- SweetAlert2 -->
<link rel="stylesheet" href="<?= base_url() ?>plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<!-- Toastr -->
<link rel="stylesheet" href="<?= base_url() ?>plugins/toastr/toastr.min.css">
<!-- DataTables -->
<link rel="stylesheet" href="<?= base_url() ?>plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>plugins/datatables/extensions/buttons/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="<?= base_url() ?>plugins/datatables/extensions/responsive/css/responsive.dataTables.css">
<link rel="stylesheet" href="<?= base_url() ?>plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?= base_url() ?>plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>dist/css/adminlte.min.css">
<!-- Google Font: Source Sans Pro -->
<!-- Select2 -->
<style type="text/css">
    /* @import url("https://code.highcharts.com/css/highcharts.css"); */

    #cameraStream.mirrored {
        transform: scaleX(-1);
    }
    .highcharts-pie-series .highcharts-point {
        stroke: #ede;
        stroke-width: 2px;
    }

    .highcharts-pie-series .highcharts-data-label-connector {
        stroke: silver;
        stroke-dasharray: 2, 2;
        stroke-width: 2px;
    }

    .highcharts-figure,
    .highcharts-data-table table {
        min-width: 320px;
        max-width: 600px;
        margin: 1em auto;
    }

    .highcharts-data-table table {
        font-family: Verdana, sans-serif;
        border-collapse: collapse;
        border: 1px solid #ebebeb;
        margin: 10px auto;
        text-align: center;
        width: 100%;
        max-width: 500px;
    }

    .highcharts-data-table caption {
        padding: 1em 0;
        font-size: 1.2em;
        color: #555;
    }

    .highcharts-data-table th {
        font-weight: 600;
        padding: 0.5em;
    }

    .highcharts-data-table td,
    .highcharts-data-table th,
    .highcharts-data-table caption {
        padding: 0.5em;
    }

    .highcharts-data-table thead tr,
    .highcharts-data-table tr:nth-child(even) {
        background: #f8f8f8;
    }

    .highcharts-data-table tr:hover {
        background: #f1f7ff;
    }
/* 
    @font-face {
        font-family: 'League Gothic';
        font-style: normal;
        font-weight: 400;
        font-stretch: 100%;
        font-display: swap;
        src: <?= base_url() ?>'/plugins/fonts/League Gothic/qFdR35CBi4tvBz81xy7WG7ep-BQAY7Krj7feObpH_9aug9UKQw.woff2'format('woff2');
        unicode-range: U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+0300-0301, U+0303-0304, U+0308-0309, U+0323, U+0329, U+1EA0-1EF9, U+20AB;
    }

    @font-face {
        font-family: 'League Gothic';
        font-style: normal;
        font-weight: 400;
        font-stretch: 100%;
        font-display: swap;
        src: <?= base_url() ?>'/plugins/fonts/League Gothic/qFdR35CBi4tvBz81xy7WG7ep-BQAY7Krj7feObpH_9avg9UKQw.woff2'format('woff2');
        unicode-range: U+0100-02AF, U+0304, U+0308, U+0329, U+1E00-1E9F, U+1EF2-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
    }

    @font-face {
        font-family: 'League Gothic';
        font-style: normal;
        font-weight: 400;
        font-stretch: 100%;
        font-display: swap;
        src: <?= base_url() ?>'/plugins/fonts/League Gothic/qFdR35CBi4tvBz81xy7WG7ep-BQAY7Krj7feObpH_9ahg9U.woff2'format('woff2');
        unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
    } */

    .error {
        outline: 1px solid red;
    }

    /*highcharts*/
    .highcharts-figure,
    .highcharts-data-table table {
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

    .highcharts-data-table td,
    .highcharts-data-table th,
    .highcharts-data-table caption {
        padding: 0.5em;
    }

    .highcharts-data-table thead tr,
    .highcharts-data-table tr:nth-child(even) {
        background: #f8f8f8;
    }

    .highcharts-data-table tr:hover {
        background: #f1f7ff;
    }

    #chart5,
    #myContact h4 {
        text-transform: none;
        font-size: 12px;
        font-weight: normal;
        font-family: Arial;
    }

    #chart5,
    #myContact p {
        font-size: 12px;
        line-height: 16px;
        font-family: Arial;
    }

    @media screen and (max-width: 600px) {

        #chart5,
        #myContact h4 {
            font-size: 2.3vw;
            line-height: 3vw;
            font-family: Arial;
        }

        #chart5,
        #myContact p {
            font-size: 2.3vw;
            line-height: 3vw;
            font-family: Arial;
        }
    }

    .has-error {
        border: 1px solid rgb(220, 53, 69) !important;
    }

    .hidden2 {
        display: none;
    }

    .custom-radio,
    input[type=radio] {
        transform: scale(1.3);
    }
    .teaching-style {
        background-color:rgb(117, 184, 255) !important;  /* Light Blue */
        border-color:rgb(0, 123, 255) !important;
        color: #fff !important;
    }

    .nonteaching-style {
        background-color:rgb(246, 236, 130) !important; /* Light Green */
        border-color: #ffc107 !important;
        /* color: #fff !important; */
    }
</style>