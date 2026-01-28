<script>
    function add_to_cart(item) {
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
                errorAlert(j.message);
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
                        errorAlert(j.message);
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

        //         farmImage
        // farmName
        // farmLocation
        // ownerPic
        // ownerName
        // ownerExperience
        // ownerContact


        // Produce
        // $('[name=produce_id]').val(data.id);
        // $('[name=order_produce_img]').attr('src', data.img_path);
        // $('[name=order_produce_name]').text(data.produce);
        // $('[name=order_produce_class]').text(data.classification);
        // $('[name=order_price]').text(data.price);
        // $('[name=order_uom]').text(data.uom);
        // $('[name=order_qty_left]').text(data.qty_left);
        // $('[name=order_harvest]').text(data.harvest_schedule);

        // // Farmer
        // $('[name=farmer_id]').val(data.farmer_id);
        // $('[name=farmer_img]').attr('src', data.farmer_img);
        // $('[name=farmer_name]').text(data.farmer_name);
        // $('[name=farmer_exp]').text(data.farmer_experience);
        // $('[name=farmer_loc]').text(data.farmer_location);
        // $('[name=farmer_rating]').text(data.farmer_rating);

        // // Reset
        // $('[name=order_qty]').val('');
        // $('[name=order_notes]').val('');
        // $('[name=order_method]').val('');
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


    function checkout() {

        let pay = $('input[name="payment_method"]:checked').val();
        let total = $("#pay_cash").data('total');
        let trans_id = $("#pay_cash").data('trans_id');
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
            html: '<b style="font-size: 20px;margin-top: -20px;">Amount: ₱ ' + total + '</b>',
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
                    successAlert(j.message);
                    $('#modalCartDetails').modal('hide');
                    getTable('CartListing', 0, 5);
                    $(".pending-order").text(j.cart_pending);

                    // setTimeout(function() {
                    //     location.reload();
                    // }, 1500)
                    // Swal.fire(j.success ? 'Success' : 'Error', j.message, j.success ? 'success' : 'error');
                }
            });
        });
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
                    successAlert(j.message);
                    getTable('CartListing', 0, 5);
                    $(".pending-order").text(j.cart_pending);
                }
            });
        });
    }
</script>