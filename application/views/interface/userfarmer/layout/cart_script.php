<script>
    /* ── Move order-status modal to <body> so Bootstrap can position it
           properly even when .wrapper has overflow:hidden ── */
    $(function () {
        $('#modalUpdateOrderStatus').appendTo('body');
    });

    function viewTransactionDetails(transaction_id) {
        transaction_id_ = transaction_id;
        $("#modalCartDetails").modal("show");
        getTable("CartDetails", 0, 10000);
    }

    function viewGcashAttachment(img) {
        $(".proof_payment").html(`<img name="previewPic" src="${img}" width="100%" height="100%" class="rounded-3 border border-3 border-primary shadow" style="cursor:pointer;object-fit:cover;" alt="GCash Receipt" nr="1">`);
        $("#viewGcashModal").modal("show");
    }

    /* ── Unified status modal ─────────────────────────────── */
    function openUpdateStatus(trans_id, order_s, del_s, pay_s) {
        $('#updateStatusTransId').val(trans_id);
        $('#curOrderStatus').text(order_s || '—');
        $('#curDeliveryStatus').text(del_s || '—');
        $('#curPaymentStatus').text(pay_s || '—');

        // Build order status options (forward-only), auto-select next step
        var opts = '<option value="">— Keep current —</option>';
        if (order_s === 'RESERVED')       opts += '<option value="PREPARING" selected>PREPARING</option>';
        if (order_s === 'PREPARING')      opts += '<option value="ORDER_IS_READY" selected>ORDER IS READY</option>';
        if (order_s === 'ORDER_IS_READY') opts += '<option value="COMPLETED" selected>✅ COMPLETED</option>';
        $('#selOrderStatus').html(opts);

        // Pre-select current delivery & payment status
        $('#selDeliveryStatus').val(del_s || '');
        $('#selPaymentStatus').val(pay_s || '');
        $('#modalUpdateOrderStatus').modal('show');
    }

    function saveAllStatuses() {
        var trans_id = $('#updateStatusTransId').val();
        var order_s  = $('#selOrderStatus').val();
        var del_s    = $('#selDeliveryStatus').val();
        var pay_s    = $('#selPaymentStatus').val();

        if (!order_s && !del_s && !pay_s) {
            failAlert('Please select at least one status to update.');
            return;
        }

        $('#btnSaveAllStatuses').prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Saving...');

        $.post("<?= base_url('userfarmer/Orders/updateOrderStatuses') ?>", {
            trans_id:        trans_id,
            order_status:    order_s,
            delivery_status: del_s,
            payment_status:  pay_s
        }, function(res) {
            var d = JSON.parse(res);
            $('#btnSaveAllStatuses').prop('disabled', false).html('<i class="fa fa-save mr-1"></i> Save');
            if (d.success) {
                successAlert(d.message);
                $('#modalUpdateOrderStatus').modal('hide');
                getTable('CartListing', 0, 10);
                if ($('#modalCartDetails').hasClass('show')) {
                    getTable('CartDetails', 0, 10000);
                }
            } else {
                failAlert(d.message);
            }
        });
    }

    // Kept for backward compatibility
    function acceptAndPrepare(transaction_id, status) {
        $.post("<?= base_url('userfarmer/Orders/acceptAndPrepare') ?>", {
            trans_id: transaction_id,
            status: status
        }, function(res) {
            var j = JSON.parse(res);
            if (j.success) {
                successAlert(j.message);
                getTable('CartListing', 0, 5);
                if (status == 'COMPLETED') getTable('CartListingCompleted', 0, 5);
                $("#modalCartDetails").modal("hide");
            } else {
                errorAlert(j.message);
            }
        });
    }
</script>
