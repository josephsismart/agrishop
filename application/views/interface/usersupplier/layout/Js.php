<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!$this->session->agrishop_login_level) { redirect(base_url('login')); }
$uri = $this->session->agrishop_login_uri;
?>
<script src="<?= base_url() ?>dist/layout_shop/js/swiper-bundle.min.js"></script>
<script src="<?= base_url() ?>dist/layout_shop/js/plugins.js"></script>
<script src="<?= base_url() ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url() ?>plugins/select2/js/select2.full.min.js"></script>
<script src="<?= base_url() ?>plugins/toastr/toastr.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/jquery.dataTables.js"></script>
<script src="<?= base_url() ?>plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/dataTables.buttons.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/buttons.print.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/jszip.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/buttons/js/buttons.html5.min.js"></script>
<script src="<?= base_url() ?>plugins/datatables/extensions/responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url() ?>plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>
<script src="<?= base_url() ?>dist/map/leaflet.js"></script>

<script>
    let transaction_id_ = null;

    /* ── Sidebar ── */
    $(function () {
        $('#bars').click(function () {
            $('#offcanvasNavbar').addClass('show');
            $('#sidebar-backdrop').addClass('show');
            $('body').addClass('overflow-hidden');
        });
        $('#sidebar-close, #sidebar-backdrop').click(function () {
            $('#offcanvasNavbar').removeClass('show');
            $('#sidebar-backdrop').removeClass('show');
            $('body').removeClass('overflow-hidden');
        });
        $('.select2').select2();
        $('.select2bs4').select2({ theme: 'bootstrap4' });
        $('[data-toggle="tooltip"]').tooltip();
        $('.select2').each(function () {
            let $parent = $(this).closest('.modal');
            $(this).select2({ dropdownParent: $parent.length ? $parent : $('body') });
        });
    });

    /* ── Toasts ── */
    const Toast = Swal.mixin({ toast: true, position: 'center', showConfirmButton: false, timer: 3000 });
    function successAlert(a) { Toast.fire({ icon: 'success', title: '  ' + a }); }
    function failAlert(a)    { Toast.fire({ icon: 'error',   title: '  ' + a }); }
    function fillIn()        { Toast.fire({ icon: 'error',   title: '  Please fill in all required fields.' }); }
    function existAlert(a)   { Toast.fire({ icon: 'warning', title: '  ' + a }); }

    /* ── Billing badge poll ── */
    setInterval(function () {
        $.post("<?= base_url($uri . '/Dashboard/supplier_billing_count') ?>", function (res) {
            let d = JSON.parse(res);
            let c = parseInt(d.count) || 0;
            $('.countBilling').text(c > 0 ? c : '');
        });
    }, 10000);

    /* ── New order poll — notify supplier + refresh active tab ── */
    window._supplierLastOrderCount = -1;

    // Unlock audio on first click
    window._supplierAudio = new Audio("<?= base_url('dist/notification/notify.wav') ?>");
    window._supplierAudio.volume = 1.0;
    window._supplierAudioUnlocked = false;
    document.addEventListener('click', function unlockSupplierAudio() {
        window._supplierAudio.play()
            .then(function() { window._supplierAudio.pause(); window._supplierAudio.currentTime = 0; window._supplierAudioUnlocked = true; })
            .catch(function() {});
        document.removeEventListener('click', unlockSupplierAudio);
    }, { once: true });

    function checkNewSupplierOrders() {
        $.post("<?= base_url($uri . '/Orders/getNewOrderCount') ?>", function(res) {
            try {
                var d = JSON.parse(res);
                var count = parseInt(d.count) || 0;

                // Update badge on nav
                $('.countOrders').text(count > 0 ? count : '');

                // New order arrived
                if (window._supplierLastOrderCount >= 0 && count > window._supplierLastOrderCount) {
                    // Play sound
                    if (window._supplierAudioUnlocked) {
                        window._supplierAudio.currentTime = 0;
                        window._supplierAudio.play().catch(function(){});
                    }
                    // Toast notification
                    if (typeof toastr !== 'undefined') {
                        toastr.success('You have a new supply order!', 'New Order 🛒', { timeOut: 5000 });
                    }
                    // Refresh active orders table if on Orders page
                    if (typeof getTable === 'function' && document.getElementById('tblOrderList')) {
                        // Only refresh if currently on Active tab
                        if (window.status_filter_OrderList === 'ACTIVE' || !window.status_filter_OrderList) {
                            if ($.fn.DataTable.isDataTable('#tblOrderList')) {
                                $('#tblOrderList').DataTable().ajax.reload(null, false);
                            }
                        }
                    }
                }

                window._supplierLastOrderCount = count;
            } catch(e) {}
        }).fail(function(){});
    }

    // Start polling every 8 seconds
    checkNewSupplierOrders();
    setInterval(checkNewSupplierOrders, 8000);

    /* ── Datatable helper ── */
    function getTable(tableId, dtd, pl) {
        $("#tbl" + tableId).DataTable().destroy();
        $("#tbl" + tableId).DataTable({
            order: [[0, "asc"]],
            dom: 'Bfrtip',
            buttons: [],
            info: pl != -1,
            paging: pl != -1,
            ordering: pl != -1,
            oLanguage: { sSearch: "" },
            processing: true,
            serverSide: true,
            language: { searchPlaceholder: "Search..." },
            ajax: {
                url: "<?= base_url($uri . '/' . $current_location . '/get') ?>" + tableId,
                type: "POST",
                data: function (d) {
                    d.length = pl;
                    d.search.value = $('#tbl' + tableId + '_filter input').val();
                    d.search.transaction_id = transaction_id_;
                    d.search.status = window['status_filter_' + tableId] || null;
                }
            },
            lengthMenu: [5, 10, 25, 50],
            pageLength: pl,
        });
        $("#tbl" + tableId + "_filter").addClass("row");
        $("#tbl" + tableId + "_filter label").css("width", "97%");
        $("#tbl" + tableId + "_filter .form-control-sm").css("width", "97%");
    }

    /* ── ajaxForm saveForm helper ── */
    function saveForm(formId, tblId, tbl, dtd, pl) {
        let a = "";
        let saveData = {
            clearForm: false,
            resetForm: false,
            beforeSubmit: function () {
                a = $("#form_save_data" + formId + " .submitBtnPrimary").text();
                $("#form_save_data" + formId + " .submitBtnPrimary").html('<span class="fa fa-spinner fa-pulse"></span>');
            },
            success: function (data) {
                let d = JSON.parse(data);
                if (d.success) {
                    successAlert(d.message);
                    $("#modal" + formId).modal('hide');
                    $("#form_save_data" + formId)[0].reset();
                    if (tblId) tblId.forEach(id => id ? getTable(id, dtd, pl) : null);
                    if (formId === "UpdateProfile" || formId === "PayBilling" || formId === "SupplyInfo" || formId === "StoreInfo") {
                        setTimeout(() => location.reload(), 1000);
                    }
                } else {
                    failAlert(d.message || "Something went wrong!");
                }
                $("#form_save_data" + formId + " .submitBtnPrimary").attr("disabled", false).html(a);
            },
            error: function () {
                failAlert("Something went wrong!");
                $("#form_save_data" + formId + " .submitBtnPrimary").attr("disabled", false).html(a);
            }
        };
        $("#form_save_data" + formId).ajaxForm(saveData);
    }

    /* ── Image preview helper ── */
    function imageView(a, b) {
        let file = $("[name=" + a + "]")[0].files[0];
        if (file.size > 25 * 1024 * 1024) { alert("Image must be less than 25MB"); return; }
        let reader = new FileReader();
        reader.onload = e => $("[name=" + b + "]").attr('src', e.target.result);
        reader.readAsDataURL(file);
    }

    /* ── Order detail viewer ── */
    function viewOrderDetails(trans_id) {
        transaction_id_ = trans_id;
        $('#modalOrderDetail').modal('show');
        getTable('OrderDetails', 0, 100);
    }

    /* ── Accept/prepare order ── */
    function acceptAndPrepare(trans_id, status) {
        $.post("<?= base_url($uri . '/Orders/acceptAndPrepare') ?>",
            { trans_id: trans_id, status: status },
            function (res) {
                let d = JSON.parse(res);
                d.success ? successAlert(d.message) : failAlert(d.message);
                getTable('OrderList', 0, 10);
                $('#modalOrderDetail').modal('hide');
            }
        );
    }

    /* ── Delivery / payment status update ── */
    function updateModalStatus(trans_id, current_status, type) {
        let options = type === 'DELIVERY'
            ? ['TO_PICKUP','TO_DELIVER','ON_THE_WAY','DELIVERED']
            : ['UNPAID','VERIFYING','PAID','FAILED'];

        let btns = options.map(s =>
            `<button class="btn btn-sm btn-outline-secondary m-1"
                onclick="updateStatus(${trans_id},'${s}','${type.toLowerCase()}')">${s}</button>`
        ).join('');

        Swal.fire({
            title: 'Update ' + type,
            html: `<p>Current: <strong>${current_status}</strong></p>${btns}`,
            showConfirmButton: false,
            showCloseButton: true,
        });
    }

    function updateStatus(trans_id, status, type) {
        $.post("<?= base_url($uri . '/Orders/updateStatus') ?>",
            { trans_id: trans_id, status: status, type: type },
            function (res) {
                let d = JSON.parse(res);
                d.success ? successAlert(d.message) : failAlert(d.message);
                Swal.close();
                getTable('OrderList', 0, 10);
            }
        );
    }

    /* ── GCash attachment viewer ── */
    function viewGcashAttachment(url) {
        Swal.fire({ imageUrl: url, imageAlt: 'GCash Payment', showCloseButton: true, showConfirmButton: false });
    }

    /* ── Validate form ── */
    function validate(form_id) {
        let invalid = 0;
        $("#" + form_id).find("input[type='text'], input[type='number']").each(function () {
            let nr = $(this).attr("nr");
            if (nr != 1 && !$(this).val()) {
                $(this).addClass("is-invalid"); invalid++;
            } else {
                $(this).removeClass("is-invalid");
            }
        });
        return invalid;
    }

    /* ── Supply toggle ── */
    function toggleSupply(id, is_active) {
        Swal.fire({
            title: is_active ? 'Activate supply?' : 'Deactivate supply?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes'
        }).then(result => {
            if (result.isConfirmed) {
                $.post("<?= base_url($uri . '/Supplies/toggleSupply') ?>",
                    { id: id, is_active: is_active },
                    function (res) {
                        let d = JSON.parse(res);
                        d.success ? successAlert('Updated!') : failAlert('Failed!');
                        getTable('SupplyList', 0, 10);
                    }
                );
            }
        });
    }

    /* ── Edit supply ── */
    function editSupply(id) {
        $.post("<?= base_url($uri . '/Supplies/getSupply') ?>", { id: id }, function (res) {
            let d = JSON.parse(res);
            if (!d) return;
            $('[name=id]').val(d.id);
            $('[name=name]').val(d.name);
            $('[name=brand]').val(d.brand);
            $('[name=uom]').val(d.uom);
            $('[name=price]').val(d.price);
            $('[name=qty_available]').val(d.qty_available);
            $('[name=description]').val(d.description);
            $('[name=tags]').val(d.tags);
            $('[name=supply_category_id]').val(d.supply_category_id).trigger('change');
            $('[name=store_id]').val(d.store_id).trigger('change');
            $('#modalSupplyInfo').modal('show');
        });
    }

    /* ── Map (for store pin) ── */
    var map2 = null;
    var storeMarker = null;

    function initStoreMap() {
        if (map2) return;
        map2 = L.map('storeMap').setView([8.915726, 125.562744], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map2);

        map2.on('click', function (e) {
            let latlng = e.latlng;
            if (storeMarker) map2.removeLayer(storeMarker);
            storeMarker = L.marker(latlng).addTo(map2);
            map2.panTo(latlng);
            $('[name=lat]').val(latlng.lat.toFixed(6));
            $('[name=lon]').val(latlng.lng.toFixed(6));
        });
    }

    $('#modalStoreInfo').on('shown.bs.modal', function () {
        initStoreMap();
        setTimeout(() => map2.invalidateSize(), 200);
    });

    saveForm("UpdateProfile", [null], null);
</script>
