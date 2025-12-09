<script>
    let CART = [];

    $(document).on("click", ".btnAddToCart", function() {

        let id = $(this).data("id");

        let item = PRODUCE_LIST.find(p => p.id == id);
        if (!item) return;

        let existing = CART.find(c => c.id == id);

        if (existing) {
            existing.qty++;
        } else {
            CART.push({
                id: item.id,
                name: item.produce,
                price: item.price,
                img: item.img,
                uom: item.uom,
                qty: 1
            });
        }

        updateCartUI();
        openCart();
    });


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
        $("#modalOrderProduce [name=farmLocation]").text(data.location_full); // example field
        
        $("#modalOrderProduce [name=farmerName]").text(data.farmer_name);
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




    function updateCartUI() {
        let html = "";
        let total = 0;

        CART.forEach((item, index) => {

            total += item.qty * item.price;

            html += `
                    <div class="d-flex align-items-center mb-2 border rounded p-2 bg-light">
                        <img src="${item.img}" width="45" height="45" class="rounded border mr-2">

                        <div class="flex-grow-1">
                            <div class="font-weight-bold">${item.name}</div>
                            <small class="text-muted">₱${item.price} / ${item.uom}</small>

                            <div class="d-flex align-items-center mt-1">
                                <button class="btn btn-sm btn-outline-secondary btnMinus" data-index="${index}">
                                    -
                                </button>

                                <span class="mx-2">${item.qty}</span>

                                <button class="btn btn-sm btn-outline-secondary btnPlus" data-index="${index}">
                                    +
                                </button>
                            </div>
                        </div>

                        <button class="btn btn-sm text-danger btnRemove" data-index="${index}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    `;
        });

        $("#cartItems").html(html);
        $("#cartTotal").text("₱" + total.toFixed(2));
    }


    $(document).on("click", ".btnPlus", function() {
        let idx = $(this).data("index");
        CART[idx].qty++;
        updateCartUI();
    });

    $(document).on("click", ".btnMinus", function() {
        let idx = $(this).data("index");
        if (CART[idx].qty > 1) CART[idx].qty--;
        updateCartUI();
    });

    $(document).on("click", ".btnRemove", function() {
        let idx = $(this).data("index");
        CART.splice(idx, 1);
        updateCartUI();
    });

    function openCart() {
        $("#cartSidebar").addClass("open");
    }

    function closeCart() {
        $("#cartSidebar").removeClass("open");
    }


    function openCheckout() {
        let html = "";
        let total = 0;

        CART.forEach(item => {
            total += item.qty * item.price;
            html += `
            <div class="d-flex justify-content-between mb-1">
                <span>${item.name} x ${item.qty}</span>
                <span>₱${(item.qty * item.price).toFixed(2)}</span>
            </div>
        `;
        });

        html += `<hr>
             <div class="d-flex justify-content-between font-weight-bold">
                 <span>Total</span>
                 <span>₱${total.toFixed(2)}</span>
             </div>`;

        $("#checkoutSummary").html(html);

        $("#modalCheckout").modal("show");
    }


    function submitCheckout() {
        $.post("<?= base_url('Order/submit') ?>", {
            items: CART,
            method: $("#deliveryMethod").val(),
            remarks: $("#remarks").val()
        }, function(response) {
            alert("Order confirmed!");
            CART = [];
            updateCartUI();
            closeCart();
            $("#modalCheckout").modal("hide");
        });
    }
</script>