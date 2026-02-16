<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title> <?= $system_title ?> | <?= $page_title ?></title>
<!-- Tell the browser to be responsive to screen width -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Font Awesome -->
<link rel="icon" type="image/png" href="<?= $system_svg ?>">
<!-- Google Font: Source Sans Pro -->
<link rel="stylesheet" href="<?= base_url() ?>dist/css/fonts.css">
<link rel="stylesheet" href="<?= base_url() ?>plugins/fontawesome-free-6.4.2-web/css/all.min.css">
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
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous"> -->
<link rel="stylesheet" href="<?= base_url(); ?>dist/layout_shop/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="<?= base_url(); ?>dist/layout_shop/css/vendor.css">
<link rel="stylesheet" type="text/css" href="<?= base_url(); ?>dist/layout_shop/css/style.css">
<link rel="stylesheet" href="<?= base_url() ?>dist/css/adminlte.min.css">
<!-- <link rel="stylesheet" href="<?= base_url() ?>dist/map/leaflet.css"> -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<!-- Google Font: Source Sans Pro -->
<!-- Select2 -->
<style type="text/css">
    /* @import url("https://code.highcharts.com/css/highcharts.css"); */

    #map {
        height: 600px;
        width: 100%;
        margin-bottom: 10px;
    }

    .buttons {
        margin-bottom: 10px;
    }

    .buttons button {
        margin-right: 5px;
        margin-bottom: 5px;
    }

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
        background-color: rgb(117, 184, 255) !important;
        /* Light Blue */
        border-color: rgb(0, 123, 255) !important;
        color: #fff !important;
    }

    .nonteaching-style {
        background-color: rgb(246, 236, 130) !important;
        /* Light Green */
        border-color: #ffc107 !important;
        /* color: #fff !important; */
    }

    .produceList li {
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .produceList li:hover {
        background: #f1f1f1;
    }

    .menu-list .nav-item {
        transition: background-color 0.2s ease, transform 0.15s ease;
        border-radius: 8px;
    }

    .menu-list .nav-item:hover {
        background-color: #a0d49dff;
        /* light hover bg */
        transform: translateY(-1px);
        color: #fff;
    }

    .menu-list .nav-item:hover .nav-link {
        color: #fff;
        /* bootstrap primary */
    }

    .menu-list .nav-item.active {
        background-color: #dff0ff;
    }

    .status-select {
        font-weight: 600;
        border: none;
        transition: filter .2s ease, transform .1s ease;
    }

    .status-select:hover {
        filter: brightness(1.15);
        transform: scale(1.03);
        cursor: pointer;
    }










    .modal-md {
        max-width: 600px;
    }

    .modal-content {
        border-radius: 15px !important;
    }

    .modal-title {
        font-size: 1.4rem;
        font-weight: bold;
    }

    .form-control-lg,
    .btn-lg {
        height: calc(2.5em + 1rem + 2px);
        padding: 0.5rem 1rem;
        font-size: 1.2rem;
    }

    /* .input-group-lg > .form-control,
    .input-group-lg > .input-group-prepend > .input-group-text {
        height: calc(2.5em + 1rem + 2px);
        padding: 0.5rem 1rem;
        font-size: 1.2rem;
    }
     */
    .produceImg img {
        max-height: 150px;
        border-radius: 10px;
        border: 3px solid #e9ecef;
    }

    .border-success {
        border-color: #28a745 !important;
        border-width: 2px !important;
    }

    .border-success:focus {
        box-shadow: 0 0 0 0.3rem rgba(40, 167, 69, 0.35);
        border-color: #28a745 !important;
    }

    .bg-light-success {
        background-color: rgba(40, 167, 69, 0.1) !important;
    }

    .badge-pill {
        font-size: 1.1rem !important;
        padding: 0.5rem 1rem !important;
        min-width: 40px;
    }

    .card {
        border-radius: 12px !important;
        border-width: 2px;
    }

    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }

    .form-label {
        font-size: 1.2rem;
        color: #333;
    }

    .produceList {
        border: 2px solid #dee2e6;
        border-radius: 10px;
        font-size: 1.2rem;
    }

    .produceList .list-group-item {
        padding: 0.75rem 1rem;
        cursor: pointer;
        border: none;
        border-bottom: 2px solid #f8f9fa;
        font-size: 1.1rem;
    }

    .produceList .list-group-item:hover {
        background-color: #e8f5e8;
        transform: translateX(5px);
        transition: all 0.2s ease;
    }

    .produceList .list-group-item:last-child {
        border-bottom: none;
    }

    .btn-lg {
        font-weight: bold;
        border-radius: 10px;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
        transform: translateY(-2px);
        transition: all 0.2s ease;
    }

    .btn-outline-secondary:hover {
        transform: translateY(-2px);
        transition: all 0.2s ease;
    }

    .alert {
        border-radius: 10px;
        font-size: 1.1rem;
    }

    /* Larger font sizes for better readability */
    .form-text {
        font-size: 1.05rem !important;
    }

    /* High contrast for better visibility */
    .text-dark {
        color: #212529 !important;
    }

    /* Clear visual hierarchy */
    .card-header h6 {
        font-size: 1.2rem;
    }

    /* Larger step badges */
    .badge {
        font-weight: bold;
    }















    .btn-animated-subscribe {
        color: #fff;
        border: none;
        padding: 6px 14px;
        border-radius: 20px;
        background: linear-gradient(270deg,
                #28a745,
                #20c997,
                #ffc107,
                #28a745);
        background-size: 600% 600%;
        animation: gradientMove 4s ease infinite;
        box-shadow: 0 0 8px rgba(40, 167, 69, 0.6);
    }

    @keyframes gradientMove {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }

    .btn-animated-subscribe:hover {
        transform: scale(1.08);
        transition: 0.2s ease;
    }

    .btn-pulse-subscribe {
        background-color: #28a745;
        color: #fff;
        border-radius: 20px;
        animation: pulseGlow 1.5s infinite;
    }

    @keyframes pulseGlow {
        0% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
        }
    }


    .confetti {
        position: fixed;
        width: 10px;
        height: 10px;
        background: #28a745;
        animation: fall 3s linear infinite;
    }

    @keyframes fall {
        from {
            top: -10px;
        }

        to {
            top: 100vh;
        }
    }

    canvas.confetti-canvas {
        position: fixed !important;
        z-index: 99999 !important;
        pointer-events: none;
    }
</style>