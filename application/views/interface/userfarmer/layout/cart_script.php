<script>
    getTable('CartListing', 0, 5);
    console.log('a')
    getTable('CartListingCompleted', 0, 5);
    getTable('CartListingCancelled', 0, 3);

    function viewTransactionDetails(transaction_id) {
        transaction_id_ = transaction_id;
        // status_ = status;
        $("#modalCartDetails").modal("show");
        getTable("CartDetails", 1, 10000);
    }

    function viewGcashAttachment(img){
        $(".proof_payment").html(`<img name="previewPic" src="${img}" width="100%" height="100%" class="rounded-3 border border-3 border-primary shadow" style="cursor:pointer; object-fit:cover;" alt="GCash QR Code" nr="1">`);
        $("#viewGcashModal").modal("show");
    }

    function acceptAndPrepare(transaction_id,status) {
        transaction_id_ = transaction_id;
        // $("#modalCartDetails").modal("show");
        // getTable("CartDetails", 1, 10000);
        $.post("<?= base_url('userfarmer/Orders/acceptAndPrepare') ?>", {
            trans_id: transaction_id,
            status: status
        }, function(res) {
            let j = JSON.parse(res);
            if (j.success == true) {
                successAlert(j.message);
                getTable('CartListing', 0, 5);
                if (status == 'COMPLETED') {
                    getTable('CartListingCompleted', 0, 5);
                }
                $("#modalCartDetails").modal("hide");

            } else {
                errorAlert(j.message);
            }
        });
    }

    function updateModalStatus(a,b,c){
        transaction_id_ = a;
        status_ = b;
        if (c == 'DELIVERY') {
            $("#modalDeliveryStatus").modal("show");
            $("#deliveryStatus").val(b)
        }
        if (c == 'PAYMENT') {
            $("#modalPaymentStatus").modal("show");
            $("#paymentStatus").val(b)
        }
    }

    function updateStatus(type,status){
        $.post("<?= base_url('userfarmer/Orders/updateStatus') ?>", {
            trans_id: transaction_id_,
            status: status,
            type: type
        }, function(res) {
            let j = JSON.parse(res);
            if (j.success == true) {
                successAlert(j.message);
                getTable('CartListing', 0, 5);
                $("#modalPaymentStatus,#modalDeliveryStatus").modal("hide");

            } else {
                errorAlert(j.message);
            }
        });
    }
</script>