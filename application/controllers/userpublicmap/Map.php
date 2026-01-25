<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Map extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        //$this->redirect();

        $this->load->model('mainModel');
        $this->load->helper('date');
        date_default_timezone_set("Asia/Manila");
    }

    function searchProduce()
    {
        $data =  [];
        $value = $this->input->post("value");
        foreach ($this->db->query("SELECT
                                        fp.farm_id,
                                        ff.img_path,
                                        ff.farm_name,
                                        p.img_path AS farmer_img_path,
                                        CONCAT(p.first_name,' ',p.last_name) AS farmer_name,
                                        ff.barangay_id,
                                        ff.lat,
                                        ff.lon,
                                        p.email_address,
                                        p.contact_num,
                                        json_agg(
                                            json_build_object(
                                                'id', fp.produce_id,
                                                'img_path', fp.produce_img_path,
                                                'name', fp.produce_name,
                                                'harvest_at', fp.harvest_schedule,
                                                'price', fp.price,
                                                'uom', fp.uom,
                                                'qty_left', fp.qty_left
                                            )
                                            ORDER BY fp.harvest_schedule
                                        ) AS produce
                                    FROM (
                                        SELECT
                                            t11.*,
                                            t22.img_path AS produce_img_path,
                                            t22.name AS produce_name,
                                            ROW_NUMBER() OVER (
                                                PARTITION BY t11.farm_id
                                                ORDER BY t11.harvest_schedule DESC
                                            ) AS rn
                                        FROM price_qty_left t11
                                        JOIN produce t22 ON t11.produce_id = t22.id
                                        WHERE
                                            t11.qty_left > 0
                                            AND CONCAT(t22.name, t22.tags) ILIKE '%$value%'
                                    ) fp
                                    LEFT JOIN farmer_farm ff ON fp.farm_id = ff.id
                                    LEFT JOIN farmer f ON ff.farmer_id = f.id
                                    LEFT JOIN person p ON f.person_id = p.id
                                    WHERE fp.rn <= 2   -- 🔥 LIMIT PER FARM
                                    GROUP BY
                                        fp.farm_id,
                                        ff.img_path,
                                        ff.farm_name,
                                        p.img_path,
                                        p.contact_num,
                                        p.email_address,
                                        CONCAT(p.first_name,' ',p.last_name),
                                        ff.barangay_id,
                                        ff.lat,
                                        ff.lon")->result() as $key => $value) {
            $farm_address = $this->getAddress2($value->barangay_id);
            $farm_image = $value->img_path ? base_url($value->img_path) : base_url('dist/img/media/icons/1x1.png');
            $farmer_image = $value->farmer_img_path ? base_url($value->farmer_img_path) : base_url('dist/img/media/icons/1x1.png');
            $farm_image_path = "<img src='$farm_image' width='50' height='50' class='rounded pr-2' data-toggle='tooltip' data-placement='top' title=''>";
            $farmer_image_path = "<img src='$farmer_image' width='50' height='50' class='rounded pr-2' data-toggle='tooltip' data-placement='top' title=''>";
            $data[] = [
                "id"    => $value->farm_id,
                "farm_img_path"    => $farm_image_path,
                "farm_name"    => $value->farm_name,
                "farm_location"    => $farm_address,
                "farmer_img_path" => $farmer_image_path,
                "farmer_name" => $value->farmer_name,
                "lat"  => $value->lat,
                "lon"  => $value->lon,
                "produce"    => $value->produce,
                "farmerContact" => "Contact: " . $value->contact_num . " | Email: " . $value->email_address
            ];
        }
        echo json_encode($data);
    }


    function getFarmProduceList()
    {
        $requestData = $_REQUEST;
        $person_id  = $this->session->agrishop_login_id;
        $farm_id = $requestData['search']['farm_id'];
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Query to get total record count
        $thisQuery = $this->db->query("SELECT count(1) AS total FROM public.farm_produce fp
                                    LEFT JOIN public.produce p ON fp.produce_id = p.id
                                    LEFT JOIN public.produce_classification pc ON p.produce_classification_id = pc.id
                                    LEFT JOIN price_qty_left pql ON fp.farm_id = pql.farm_id AND fp.produce_id = pql.produce_id
                                    WHERE fp.farm_id = $farm_id AND CONCAT(p.name,pc.class_name,p.description,(CASE WHEN p.is_seasonal = true THEN 'SEASONAL' ELSE 'NON-SEASONAL' END),(CASE WHEN p.is_active = true THEN 'ACTIVE' ELSE 'INACTIVE' END)) 
                                    ILIKE '%$searchValue%'");

        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT ROW_NUMBER() OVER (ORDER BY fp.id DESC) AS row_num, fp.id as fp_id,p.id,p.name as produce,pql.harvest_schedule,pql.uom,pql.price,pql.qty_left,pql.latest_price_id,pc.class_name,p.description,
                                    p.is_seasonal,p.is_active,p.created_at, p.img_path, fp.farm_id 
                                    FROM public.farm_produce fp
                                    LEFT JOIN public.produce p ON fp.produce_id = p.id
                                    LEFT JOIN public.produce_classification pc ON p.produce_classification_id = pc.id
                                    LEFT JOIN price_qty_left pql ON fp.farm_id = pql.farm_id AND fp.produce_id = pql.produce_id
                                    WHERE fp.farm_id = $farm_id AND CONCAT(p.name,pc.class_name,p.description,(CASE WHEN p.is_seasonal = true THEN 'SEASONAL' ELSE 'NON-SEASONAL' END),(CASE WHEN p.is_active = true THEN 'ACTIVE' ELSE 'INACTIVE' END)) 
                                    ILIKE '%$searchValue%'
                                    ORDER BY p.created_at DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        $cc = 0;
        foreach ($query->result() as $key => $value) {
            $img = $value->img_path ? base_url($value->img_path) : base_url('dist/img/media/icons/1x1.png');
            $image_path = "<img src='$img' width='50' height='50' class='rounded' data-toggle='t0
            .0.ooltip' data-placement='top' title=''>";
            $q_id = $value->row_num;
            $add_to_cart = "<span class='badge bg-warning text-black' type='button' onclick='add_to_cart({
                                id: \"$value->fp_id\",
                                id_: \"$value->row_num\",
                                img_path: \"$img\",
                                produce: \"$value->produce\",
                                harvest_schedule: \"$value->harvest_schedule\",
                                qty_left: \"$value->qty_left\",
                                price: \"$value->price\",
                                uom: \"$value->uom\",
                                class_name: \"$value->class_name\",
                                is_seasonal: \"$value->is_seasonal\",
                                is_active: \"$value->is_active\",
                                farm_id: \"$value->farm_id\",
                                latest_price_id: \"$value->latest_price_id\"
                            })'><i class='fa fa-cart'></i> Add to cart</span>";
            $data[] = array(
                $add_to_cart,
                '<input type="number" style="width: 100px;" id="qty' . $q_id . '" min="1" max="' . $value->qty_left . '" value="1" class="form-control form-control-sm">',
                $image_path,
                $value->produce,
                $value->harvest_schedule,
                $value->qty_left,
                $value->price . '/' . $value->uom,
                // $value->class_name,
                // $is_seasonal,
                // $is_active,
            );
        } // Prepare the response data in the required format
        $response = array(
            'draw' => intval($requestData['draw']),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords), // For simplicity, assuming no filtering is applied
            'data' => $data,
        );
        echo json_encode($response);
    }

    public function add_to_cart()
    {
        $this->db->trans_begin();
        $data = [];
        $item = $this->input->post("item");
        $qty = $this->input->post("qty");
        //  id,img_path,produce,harvest_schedule,qty_left,price,uom,class_name,is_seasonal,is_active,farm_id
        $farm_id = $item['farm_id'];
        parse_str($this->input->post("c"), $filter);
        $person_id = $this->session->agrishop_person_id;
        $dateNow = $this->now();
        $true = ["success"   => true];
        $false = ["success"   => false];

        $check = $this->db->query("SELECT * FROM transaction WHERE person_id = $person_id AND is_done is FALSE AND farm_id = $farm_id LIMIT 1")->row();
        if ($check) {

            $transaction_id = $check->id;
        } else {

            $data = [
                'person_id' => $person_id,
                'total_sale_amount' => 1,
                'transaction_date' => $dateNow,
                'admin_percentage' => 1,
                'admin_sale_amount' => 0.1,
                'farm_id' => $farm_id,
            ];
            // $b = json_encode($data);
            if ($this->db->insert("transaction", $data)) {
                $transaction_id = $this->db->insert_id();
            } else {
                $false += ["message"   => "Transaction not created!"];
                $ret = $false;
            }
        }

        $ret = $false;
        $data_transaction_status = [
            'transaction_id' => $transaction_id,
            'status' => 'PENDING',
            'created_by_person_id' => $person_id,
        ];

        $this->db->insert("transaction_status", $data_transaction_status);

        $farm_produce_id = $item['id'];
        $latest_price_id = $item['latest_price_id'];
        // $check_exist_cart = $this->db->query("SELECT * FROM my_cart_farm_produce WHERE transaction_id = $transaction_id 
        //                                         AND farm_produce_id = $farm_produce_id 
        //                                         AND price_id_during_transact = $latest_price_id
        //                                         LIMIT 1")->row();

        // $data_my_cart = [
        //     'transaction_id' => $transaction_id,
        //     'farm_produce_id' => $farm_produce_id,
        //     'price_id_during_transact' => $latest_price_id,
        // ];
        // if ($check_exist_cart) {
        //     $data_my_cart += [
        //         'qty' => $qty + $check_exist_cart->qty,
        //         'sub_total' => ($qty + $check_exist_cart->qty) * $item['price'],
        //     ];
        // }else {
        //     $data_my_cart += [
        //         'qty' => $qty,
        //         'sub_total' => $item['price'] * $qty,
        //     ];
        // }

        // if ($check_exist_cart) {
        //     if ($this->db->update("my_cart_farm_produce", $data_my_cart, ["id" => $check_exist_cart->id])) {
        //         $true += ["message"   => "Added to cart!"];
        //         $ret = $true;
        //     } else {
        //         $false += ["message"   => "Failed to add to cart!"];
        //         $ret = $false;
        //     }
        // } else {
        //     if ($this->db->insert("my_cart_farm_produce", $data_my_cart)) {
        //         $true += ["message"   => "Added to cart!"];
        //         $ret = $true;
        //     } else {
        //         $false += ["message"   => "Failed to add to cart!"];
        //         $ret = $false;
        //     }
        // }

        $data_my_cart = [
            'transaction_id' => $transaction_id,
            'farm_produce_id' => $farm_produce_id,
            'price_id_during_transact' => $latest_price_id,
            'qty' => $qty,
            'sub_total' => $item['price'] * $qty,
            'created_at' => $dateNow
        ];

        if ($this->db->insert("my_cart_farm_produce", $data_my_cart)) {
            $true += ["message"   => "Added to cart!"];
            $ret = $true;
        } else {
            $false += ["message"   => "Failed to add to cart!"];
            $ret = $false;
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
        echo json_encode($ret);
    }

    public function remove_produce_from_cart()
    {
        $this->db->trans_begin();
        $data = [];
        $cart_id = $this->input->post("cart_id");
        $transaction_id = $this->input->post("transaction_id");
        $true = ["success"   => true];
        $false = ["success"   => false];

        $cart = $this->db->query("SELECT sum(1) as cc FROM my_cart_farm_produce WHERE transaction_id = $transaction_id")->row();

        if ($this->db->delete("my_cart_farm_produce", ["id" => $cart_id])) {
            if ($cart->cc == 1) {
                $this->db->query("DELETE FROM transaction_status WHERE transaction_id = $transaction_id");
                $this->db->query("DELETE FROM transaction WHERE id = $transaction_id");
            }
            $true += ["message"   => "Removed from cart!"];
            $ret = $true;
        } else {
            $false += ["message"   => "Failed to remove from cart!"];
            $ret = $false;
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
        echo json_encode($ret);
    }

    public function getCartListing()
    {
        $person_id  = $this->session->agrishop_person_id;
        $requestData = $_REQUEST;
        $farm_id = $requestData['search']['farm_id'];
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Query to get total record count
        $thisQuery = $this->db->query("SELECT count(1) AS total FROM transaction t1 
                                    JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
		                                    LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce mcfp
		                                    GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                                    LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                                    WHERE t1.person_id = $person_id AND CONCAT(t4.farm_name,t2.status,t3.payable) ILIKE '%$searchValue%'");

        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT t1.id AS transaction_id, t4.img_path, to_char(t1.transaction_date,'mm/dd/yy') date_, t4.farm_name,t2.status,t3.payable FROM transaction t1 
                                    JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
		                                    LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce mcfp
		                                    GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                                    LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                                    WHERE t1.person_id = $person_id AND CONCAT(t4.farm_name,t2.status,t3.payable) ILIKE '%$searchValue%'
                                    ORDER BY (t2.status = 'PENDING') DESC, t1.id DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        foreach ($query->result() as $value) {
            $total = $value->payable + ($value->payable * 0.01);
            $img = $value->img_path ? base_url($value->img_path) : base_url('dist/img/media/icons/1x1.png');
            $image_path = "<img src='$img' width='50' height='50' class='rounded' data-toggle='tooltip' data-placement='top' title=''>";
            $status_badge = '<span class="badge bg-warning">' . $value->status . '</span>';
            $data[] = array(
                '<div class="d-flex align-items-start p-2" style="gap:10px; width:100%; line-height:1.15">

                    <!-- IMAGE -->
                    <div>
                        ' . $image_path . '
                    </div>

                    <!-- INFO -->
                    <div class="flex-grow-1">

                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div style="font-size:12px; color:#777;">
                                    ' . $value->date_ . '
                                </div>

                                <div style="font-size:14px; font-weight:600; color:#000;">
                                    ' . $value->farm_name . '
                                </div>
                            </div>

                            <span class="badge bg-success" style="font-size:14px;">
                                ₱ ' . $this->format_price($total) . '
                            </span>
                        </div>

                        <div class="d-flex align-items-center mt-1" style="gap:6px;">
                            <span class="badge bg-secondary"
                                style="cursor:pointer; font-size:11px;"
                                onclick="viewTransactionDetails(' . $value->transaction_id . ')">
                                <i class="fa fa-eye"></i> view to checkout
                            </span>

                            ' . $status_badge . '
                        </div>

                    </div>
                </div>
                <hr style="margin:4px 0;">
                '
            );
        } // Prepare the response data in the required format

        $response = array(
            'draw' => intval($requestData['draw']),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords), // For simplicity, assuming no filtering is applied
            'data' => $data,
        );
        echo json_encode($response);
    }

    public function getCartDetails()
    {
        $person_id  = $this->session->agrishop_person_id;
        $requestData = $_REQUEST;
        $transaction_id = $requestData['search']['transaction_id'];
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Query to get total record count
        $thisQuery = $this->db->query("SELECT count(1) AS total FROM my_cart_farm_produce t1
                                    LEFT JOIN farm_produce t2 ON t1.farm_produce_id = t2.id
                                    LEFT JOIN produce t3 ON t2.produce_id = t3.id
                                    LEFT JOIN (SELECT * FROM price_monitoring_farm_produce WHERE is_latest IS TRUE) t4 ON t1.price_id_during_transact = t4.id
                                    WHERE t1.transaction_id =$transaction_id");

        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT t1.id as cart_id,t1.transaction_id,t1.qty,t4.price,t2.uom,t3.name as produce_name,t3.img_path,t1.created_at
                                    FROM my_cart_farm_produce t1
                                    LEFT JOIN farm_produce t2 ON t1.farm_produce_id = t2.id
                                    LEFT JOIN produce t3 ON t2.produce_id = t3.id
                                    LEFT JOIN (SELECT * FROM price_monitoring_farm_produce WHERE is_latest IS TRUE) t4 ON t1.price_id_during_transact = t4.id
                                    WHERE t1.transaction_id =$transaction_id
                                    ORDER BY t1.id DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $gcash_details = $this->db->query("SELECT mcfp.transaction_id ,fpm.*,CONCAT(p.first_name,' ',p.last_name) person_name FROM my_cart_farm_produce mcfp 
									JOIN farm_produce fp ON mcfp.farm_produce_id = fp.id
									JOIN farmer_farm ff ON fp.farm_id = ff.id
                                    JOIN farmer f ON ff.farmer_id = f.id
                                    JOIN person p ON f.person_id = p.id
									JOIN farmer_payment_method fpm ON f.id = fpm.farmer_id
									WHERE mcfp.transaction_id = $transaction_id LIMIT 1");

        $data = array();
        $subtotal = 0;
        $g_name = '';
        $g_number = '';
        $g_qr = '';
        $g_pay = '';

        if ($gcash_details->num_rows() > 0) {
            $g = $gcash_details->row();

            $gcash_icon = base_url('dist/img/credit/gcash_50x50.png');
            $g_name = $g->person_name;
            $g_number = $g->number;
            $g_qr = $g->qr;

            $g_pay = ' <div class="form-check form-check-inline" style="margin-right:10px;">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                        id="pay_gcash" value="gcash">
                                    <label class="form-check-label pay_gcash" data-name="' . $g_name . '" data-number="' . $g_number . '" data-qr="' . $g_qr . '" for="pay_gcash" style="cursor:pointer;">
                                        <img src="' . $gcash_icon . '" alt="GCash" style="width: 20px; height: 20px; margin-left: 5px;">
                                        GCash
                                    </label>
                                </div>';
        }
        foreach ($query->result() as $value) {

            $price = $value->price * $value->qty;
            $subtotal += $price;

            $img = $value->img_path
                ? base_url($value->img_path)
                : base_url('dist/img/media/icons/1x1.png');

            $data[] = array(
                '
                    <div class="d-flex align-items-start p-2" style="gap:10px; width:100%">

                        <!-- PRODUCT IMAGE -->
                        <img src="' . $img . '" 
                            width="56" height="56" 
                            class="rounded shadow-sm"
                            style="object-fit:cover">

                        <!-- PRODUCT INFO -->
                        <div class="flex-grow-1" style="line-height:1.15">

                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div style="font-size:14px; font-weight:600; color:#222; margin-bottom:2px;">
                                        ' . $value->produce_name . '
                                    </div>

                                    <div class="text-muted" style="font-size:12px;">
                                        ' . $value->qty . ' ' . $value->uom . ' × ₱ ' . $this->format_price($value->price) . '
                                    </div>
                                </div>

                                <!-- REMOVE -->
                                <span class="text-black"
                                    style="cursor:pointer; font-size:13px; margin-top:2px;"
                                    title="Remove"
                                    onclick="removeCart(' . $value->cart_id . ',' . $value->transaction_id . ')">
                                    <i class="fa fa-trash"></i>
                                </span>
                            </div>

                            <!-- PRICE -->
                            <div style="margin-top:4px;">
                                <span class="badge bg-success px-2 py-1" style="font-size:12px;">
                                    ₱ ' . $this->format_price($price) . '
                                </span>
                            </div>

                        </div>
                    </div>
                    <hr style="margin:4px 0;">
                    '
            );
        }
        // checkout
        $convenience_fee = $subtotal * 0.01; // 1%
        $total_payment   = $subtotal + $convenience_fee;
        $data[] = array(
            '
                        <div class="p-2" style="width:100%; font-size:13px; line-height:1.2">

                            <!-- DELIVERY OPTION -->
                            <div>
                                <div style="font-weight:600; margin-bottom:2px;">Delivery</div>

                                <div class="form-check form-check-inline" style="margin-right:10px;">
                                    <input class="form-check-input" type="radio" name="delivery_option"
                                        id="pickup" value="pickup" checked>
                                    <label class="form-check-label" for="pickup"  style="cursor:pointer;">
                                        Pick-up
                                    </label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="delivery_option"
                                        id="cod" value="cod">
                                    <label class="form-check-label" for="cod"  style="cursor:pointer;">
                                        COD (Cash on Delivery)
                                    </label>
                                </div>
                            </div>

                            <!-- MODE OF PAYMENT -->
                            <div>
                                <div style="font-weight:600; margin-bottom:2px;">Payment</div>

                                <div class="form-check form-check-inline" style="margin-right:10px;">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                        id="pay_cash" value="cash" checked>
                                    <label class="form-check-label" for="pay_cash" style="cursor:pointer;">
                                    <i class="fa fa-money-bill"></i>
                                        Cash
                                    </label>
                                </div>

                                '.$g_pay.'

                                <!--div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                        id="pay_maya" value="paymaya">
                                    <label class="form-check-label" for="pay_maya" style="cursor:pointer;">
                                        PayMaya
                                    </label>
                                </div-->
                            </div>

                            <hr style="margin:6px 0;">

                            <!-- PAYMENT BREAKDOWN -->
                            <div class="d-flex justify-content-between">
                                <span>Subtotal</span>
                                <span>₱ ' . $this->format_price($subtotal) . '</span>
                            </div>

                            <div class="d-flex justify-content-between text-muted">
                                <span>Fee (1%) <small>(Convenience Fee)</small></span>
                                <span>₱ ' . $this->format_price($convenience_fee) . '</span>
                            </div>

                            <hr style="margin:6px 0;">

                            <!-- TOTAL PAYMENT (HIGHLIGHT) -->
                            <div class="d-flex justify-content-between align-items-center 
                                        p-2 rounded"
                                style="background:#e9f7ef; font-size:15px;">
                                <span style="font-weight:700;">Total</span>
                                <span class="text-black" style="font-weight:bold;">
                                    ₱ ' . $this->format_price($total_payment) . '
                                </span>
                            </div>

                        </div>
                        '
        );
        $data[] = array(
            '<button class="btn btn-primary btn-block w-100 checkout-btn">
                Checkout
            </button>',
        );
        $response = array(
            'draw' => intval($requestData['draw']),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords), // For simplicity, assuming no filtering is applied
            'data' => $data,
        );
        echo json_encode($response);
    }


    public function search_barangay()
    {
        $keyword = $this->input->post('keyword');
        $limit = $this->input->post('limit') ?: 10;
        if (strlen($keyword) < 3) {
            echo json_encode([]);
            return;
        }

        // Split the keyword into individual words
        $words = explode(' ', $keyword);

        // Build dynamic "ILIKE" filters
        $conditions = "";
        foreach ($words as $w) {
            $w = trim($w);
            if ($w !== "") {
                $conditions .= " AND CONCAT(
                t1.description,' ',
                t2.description,', ',
                t3.description,', ',
                t4.region
            ) ILIKE '%" . $this->db->escape_like_str($w) . "%' ";
            }
        }

        $query = $this->db->query("SELECT 
                                        t1.id,
                                        UPPER(CONCAT(
                                            t1.description, ' ',
                                            t2.description, ', ',
                                            t3.description, ', ',
                                            t4.region
                                        )) AS address
                                    FROM tbl_barangay t1
                                    LEFT JOIN tbl_citymun t2 ON t1.citymun_id = t2.id
                                    LEFT JOIN tbl_province t3 ON t2.province_id = t3.id
                                    LEFT JOIN tbl_region t4 ON t3.region_id = t4.id
                                    WHERE 1=1 
                                    $conditions
                                    ORDER BY t1.description 
                                    LIMIT $limit");

        $results = [];
        foreach ($query->result() as $row) {
            $results[] = [
                'id'   => $row->id,
                'text' => $row->address
            ];
        }

        echo json_encode($results);
    }

    public function search_barangay_caraga()
    {
        $keyword = $this->input->post('keyword');
        $limit = $this->input->post('limit') ?: 10;
        if (strlen($keyword) < 3) {
            echo json_encode([]);
            return;
        }

        // Split the keyword into individual words
        $words = explode(' ', $keyword);

        // Build dynamic "ILIKE" filters
        $conditions = "";
        foreach ($words as $w) {
            $w = trim($w);
            if ($w !== "") {
                $conditions .= " AND CONCAT(
                t1.description,' ',
                t2.description,', ',
                t3.description,', ',
                t4.region
            ) ILIKE '%" . $this->db->escape_like_str($w) . "%' ";
            }
        }

        $query = $this->db->query("SELECT 
                                        t1.id,
                                        t1.gid,
                                        UPPER(CONCAT(
                                            t1.description, ' ',
                                            t2.description, ', ',
                                            t3.description, ', ',
                                            t4.region
                                        )) AS address
                                    FROM tbl_barangay_2 t1
                                    JOIN tbl_citymun t2 ON t1.adm3_psgc = t2.ref_id
                                    JOIN tbl_province t3 ON t2.province_id = t3.id
                                    JOIN tbl_region t4 ON t3.region_id = t4.id AND t4.is_active = true
                                    WHERE 1=1 
                                    $conditions
                                    ORDER BY t1.description 
                                    LIMIT $limit");

        $results = [];
        foreach ($query->result() as $row) {
            $results[] = [
                'id'   => $row->id,
                'gid'   => $row->gid,
                'text' => $row->address
            ];
        }

        echo json_encode($results);
    }
}

// farmer_id int4 NOT NULL,
// farm_name varchar(255) NOT NULL,
// total_area_sqm numeric(10, 2) NULL,
// barangay_id int8 NULL,
// geo_polygon text NULL,
// soil_type varchar(100) NULL,
// is_active bool DEFAULT true NOT NULL,
// created_at timestamp(6) DEFAULT now() NULL,
// coordinates text NULL,
// img_path text NULL,
// created_by_person_id int4 DEFAULT 1 NOT NULL,