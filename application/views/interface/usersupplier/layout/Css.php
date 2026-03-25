<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?= $system_title ?> | <?= $page_title ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" type="image/png" href="<?= $system_svg ?>">
<link rel="stylesheet" href="<?= base_url() ?>dist/css/fonts.css">
<link rel="stylesheet" href="<?= base_url() ?>plugins/fontawesome-free-6.4.2-web/css/all.min.css">
<link rel="stylesheet" href="<?= base_url() ?>plugins/fontawesome-free/css/all.min.css">
<link rel="stylesheet" href="<?= base_url() ?>plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>plugins/toastr/toastr.min.css">
<link rel="stylesheet" href="<?= base_url() ?>plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>plugins/datatables/extensions/buttons/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="<?= base_url() ?>plugins/datatables/extensions/responsive/css/responsive.dataTables.css">
<link rel="stylesheet" href="<?= base_url(); ?>dist/layout_shop/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="<?= base_url(); ?>dist/layout_shop/css/vendor.css">
<link rel="stylesheet" type="text/css" href="<?= base_url(); ?>dist/layout_shop/css/style.css">
<link rel="stylesheet" href="<?= base_url() ?>dist/css/adminlte.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
    #map { height:400px; width:100%; margin-bottom:10px; }

    .custom-sidebar { position:fixed; top:0; right:-300px; width:300px; height:100%; background:#fff; z-index:1050; transition:right 0.3s ease; }
    .custom-sidebar.show { right:0; }
    #sidebar-backdrop { position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); display:none; z-index:1040; }
    #sidebar-backdrop.show { display:block; }

    /* Supplier theme — orange accent instead of farmer green */
    .menu-list .nav-item { transition:background-color 0.2s ease, transform 0.15s ease; border-radius:8px; }
    .menu-list .nav-item:hover { background-color:#f4a93688; transform:translateY(-1px); }
    .menu-list .nav-item:hover .nav-link { color:#fff; }
    .menu-list .nav-item.active { background-color:#fff3cd; }

    .has-error { border:1px solid rgb(220,53,69) !important; }
    .hidden2 { display:none; }
    .card { border-radius:12px !important; border-width:2px; }
    .card-header { border-radius:10px 10px 0 0 !important; }
    .modal-content { border-radius:15px !important; }
    .modal-title { font-size:1.4rem; font-weight:bold; }
    .status-select { font-weight:600; border:none; transition:filter .2s ease,transform .1s ease; }
    .status-select:hover { filter:brightness(1.15); transform:scale(1.03); cursor:pointer; }

    /* Supplier accent — orange */
    .btn-supplier { background-color:#e67e22; border-color:#e67e22; color:#fff; }
    .btn-supplier:hover { background-color:#d35400; border-color:#d35400; color:#fff; }
    .badge-supplier { background-color:#e67e22; color:#fff; }

    .btn-pulse-subscribe {
        background-color:#e67e22; color:#fff; border-radius:20px;
        animation:pulseGlowOrange 1.5s infinite;
    }
    @keyframes pulseGlowOrange {
        0%   { box-shadow:0 0 0 0 rgba(230,126,34,0.7); }
        70%  { box-shadow:0 0 0 10px rgba(230,126,34,0); }
        100% { box-shadow:0 0 0 0 rgba(230,126,34,0); }
    }
</style>
