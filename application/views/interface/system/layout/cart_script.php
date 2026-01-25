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
            width: '300px',
            padding: '1em',
            buttonsStyling: false,
            customClass: {
                popup: 'swal-mini',
                confirmButton: 'btn btn-sm btn-danger mx-1',
                cancelButton: 'btn btn-sm btn-secondary mx-1'
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

    $(".checkout-btn").click(function(){
        alert('a')
    });
</script>