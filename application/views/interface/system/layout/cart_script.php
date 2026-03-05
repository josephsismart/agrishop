<script>
    function add_to_cart(item) {
        console.log(item)
        let login_uname = "<?= $this->session->agrishop_login_uname ?>";
        if (login_uname == "") {
            window.location.href = "<?= base_url('login') ?>";
            return;
        }
        $.post("<?= base_url('userpublicmap/Map/add_to_cart') ?>", {
            item: item,
            qty: $("#qty" + item.id_).val()
        }, function(res) {
            let j = JSON.parse(res);
            if (j.success == true) {
                successAlert(j.message);
                $(".pending-order").text(j.cart_pending);
            } else {
                failAlert(j.message);
            }
        });
    }

    function viewTransactionDetails(transaction_id) {
        transaction_id_ = transaction_id;
        $("#modalCartDetails").modal("show");
        getTable("CartDetails", 1, 1000);
    }

    function removeCart(cart_id, transaction_id) {

        Swal.fire({
            title: 'Remove item?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Remove',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc3545', // red
            cancelButtonColor: '#28a745', // green
            width: '300px',
            padding: '1em',
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {
                popup: 'swal-mini',
                // confirmButton: 'btn btn-sm btn-danger mx-1',
                // cancelButton: 'btn btn-sm btn-secondary mx-1'
            }
        }).then((result) => {

            if (result.isConfirmed) {
                $.post("<?= base_url('userpublicmap/Map/remove_produce_from_cart') ?>", {
                    cart_id: cart_id,
                    transaction_id: transaction_id,
                }, function(res) {

                    let j = JSON.parse(res);

                    if (j.success) {
                        successAlert(j.message);
                        getTable("CartDetails", 1, 1000);
                        getTable('CartListing', 0, 5);
                        $(".pending-order").text(j.cart_pending);

                    } else {
                        failAlert(j.message);
                    }

                });
            }

        });
    }


    function orderNow(id) {
        farm_id = id;
        const data = farmCache[id]; // retrieve full JSON
        searchProduce = $("#searchProduce").val();
        $('#modalOrderProduce').modal('show');
        getTable("FarmProduceList", 0, 5);

        $('#tblFarmProduceList_filter input').val(searchProduce);

        setTimeout(function() {

            var $searchBox = $('#tblFarmProduceList_filter input');
            $searchBox.focus();
            $searchBox.trigger('input');
            $searchBox.trigger('keyup');

        }, 500);

        $('#modalOrderProduce').on('shown.bs.modal', function() {
            $('#tblFarmProduceList').DataTable().columns.adjust();
        });
        $("#modalOrderProduce [name=farmImage]").html(data.farm_img_path);
        $("#modalOrderProduce [name=farmName]").text(data.farm_name);
        $("#modalOrderProduce [name=farmLocation]").text(data.farm_location + " | " + data.lat + ", " + data.lon); // example field

        $("#modalOrderProduce [name=farmerName]").text(data.farmer_name);
        $("#modalOrderProduce [name=farmerContact]").text(data.farmerContact);
        $("#modalOrderProduce [name=farmerImage]").html(data.farmer_img_path);
    }

    $(".checkout-btn").click(function() {
        alert('a')
    });



    $(document).on('change', 'input[name="payment_method"]', function() {

        let method = $(this).val();

        if (method === 'gcash') {
            let label = $('label.pay_gcash');

            $('#gcashName').text(label.data('name'));
            $('#gcashNumber').text(label.data('number'));
            $('#gcashQR').attr('src', label.data('qr'));

            $('#gcashDetailsBox').slideDown();
        } else {
            $('#gcashDetailsBox').slideUp();
        }
    });

    $('#agreeFee').on('click', function() {
        $('#btnProceedOrder').prop('disabled', !this.checked);
    });

    function format_money(num) {
        return num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function checkout() {

        let trans_id = $("#pay_cash").data('trans_id');
        let convenience_fee = $("#pay_cash").data('convenience_fee');
        let to_admin_payment_status = $("#pay_cash").data('to_admin_payment_stat');

        if (to_admin_payment_status == 'TO_BE_PAID') {
            $("#processingFeeAmount").text("₱ " + format_money(convenience_fee));
            $("#trans_id").val(trans_id);
            $("#convenience_fee").val(convenience_fee);
            $('#modalProcessingFeeModal').modal('show');
        } else {
            let pay = $('input[name="payment_method"]:checked').val();
            let total = $("#pay_cash").data('total');
            let subtotal = $("#pay_cash").data('subtotal');
            let percentage = $("#pay_cash").data('percentage');
            let name = $(".pay_gcash").data('name');
            let number = $(".pay_gcash").data('number');
            let proof = $('input[name="proof_of_payment"]').val();
            let delivery = $('input[name="delivery_option"]:checked').val();

            if (pay === 'gcash' && proof === '') {
                Swal.fire({
                    title: 'Required',
                    text: 'Upload proof of payment',
                    icon: 'warning',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    customClass: {
                        popup: 'swal-mini',
                        icon: 'no-border'
                    }
                });
                return;
            }

            Swal.fire({
                title: '<label style="font-size: 18px;">' + (pay === 'gcash' ? 'Confirm GCash Payment' : 'Confirm Cash Payment') + '</label>',
                html: '<b style="font-size: 20px;margin-top: -20px;">Amount: ₱ ' + format_money(subtotal) + '</b>',
                iconHtml: pay === 'gcash' ?
                    '<img src="<?= base_url('dist/img/credit/gcash_50x50.png') ?>" width="70">' : '<i class="fa fa-money-bill-wave text-primary"></i>',
                showCancelButton: true,
                confirmButtonText: 'Confirm',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#28a745', // green
                cancelButtonColor: '#dc3545', // red
                allowOutsideClick: false,
                allowEscapeKey: false,
                customClass: {
                    popup: 'swal-mini',
                    icon: 'no-border'
                }

            }).then(r => {
                if (!r.isConfirmed) return;

                let fd = new FormData();

                fd.append('pay', pay === 'gcash' ? 'gcash' : 'cash');
                fd.append('total', total);
                fd.append('trans_id', trans_id);
                fd.append('subtotal', subtotal);
                fd.append('percentage', percentage);
                fd.append('number', number);
                fd.append('name', name);
                fd.append('proof', proof);
                fd.append('delivery', delivery);


                // only append proof if gcash
                if (pay === 'gcash') {
                    fd.append('proof_of_payment', $('input[name="proof_of_payment"]')[0].files[0]);
                }

                $.ajax({
                    url: "<?= base_url('userpublicmap/Map/submit_order') ?>",
                    type: "POST",
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: res => {
                        let j = JSON.parse(res);
                        $(".pending-order").text(j.cart_pending);
                        if (j.success == true) {
                            successAlert(j.message); //this line
                            $('#modalCartDetails').modal('hide');
                            getTable('CartListing', 0, 5);;
                        } else {
                            failAlert(j.message);
                        }

                        // setTimeout(function() {
                        //     location.reload();
                        // }, 1500)
                        // Swal.fire(j.success ? 'Success' : 'Error', j.message, j.success ? 'success' : 'error');
                    }
                });
            });
        }
    }


    function cancelOrder() {
        $('#modalCartDetails').modal('hide');
        let pay = $('input[name="payment_method"]:checked').val();
        let trans_id = $("#pay_cash").data('trans_id');

        Swal.fire({
            title: '<label style="font-size:24px;">Confirm Cancel Order</label>',
            html: `
            <div style="font-size:13px; margin-bottom:6px;">
                <b>Important Note:</b><br>
                <i>Orders that are cancelled are <b style="color:red;">non-refundable</b>.</i>
            </div>

            <textarea id="cancel_reason"
                class="swal2-textarea"
                placeholder="Please tell us your reason for cancelling..."
                style="font-size:13px;"></textarea>
        `,
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {
                popup: 'swal-mini',
                icon: 'no-border'
            },
            preConfirm: () => {
                let reason = document.getElementById('cancel_reason').value.trim();
                if (!reason) {
                    Swal.showValidationMessage('Cancellation reason is required');
                    return false;
                }
                return reason;
            }
        }).then(r => {

            if (!r.isConfirmed) return;

            let reason = r.value; // 👈 from textarea

            let fd = new FormData();
            fd.append('trans_id', trans_id);
            fd.append('cancel_reason', reason); // 👈 PASS TO CONTROLLER

            $.ajax({
                url: "<?= base_url('userpublicmap/Map/cancel_order') ?>",
                type: "POST",
                data: fd,
                processData: false,
                contentType: false,
                success: res => {
                    let j = JSON.parse(res);
                    if (j.success == true) {
                        successAlert(j.message);
                        getTable('CartListing', 0, 5);
                        $(".pending-order").text(j.cart_pending);
                    } else {
                        failAlert(j.message);
                    }
                }
            });
        });
    }
</script>