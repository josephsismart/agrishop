<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Map extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SQL_BIG_SELECTS=1');
        //$this->redirect();

        $this->load->model('mainModel');
        $this->load->helper('date');
        date_default_timezone_set("Asia/Manila");
    }

    function searchProduce()
    {
        $data =  [];
        $value = $this->input->post("value");
        $price_qty_left = $this->price_qty_left();
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
                                    CONCAT('[', GROUP_CONCAT(
                                        JSON_OBJECT(
                                            'id', fp.produce_id,
                                            'img_path', fp.produce_img_path,
                                            'name', fp.produce_name,
                                            'harvest_at', fp.harvest_schedule,
                                            'price', fp.price,
                                            'uom', fp.uom,
                                            'qty_left', fp.qty_left,
                                            'price_wholesale', fp.price_wholesale,
                                            'wholesale_at_qty', fp.wholesale_at_qty
                                        )
                                        ORDER BY fp.harvest_schedule
                                    ), ']') AS produce
                                FROM (
                                    SELECT
                                        t11.*,
                                        t22.img_path AS produce_img_path,
                                        t22.name AS produce_name,
                                        ROW_NUMBER() OVER (
                                            PARTITION BY t11.farm_id
                                            ORDER BY t11.harvest_schedule DESC
                                        ) AS rn
                                    FROM ($price_qty_left) t11
                                    JOIN produce t22 ON t11.produce_id = t22.id
                                    WHERE
                                        CONCAT(t22.name, t22.tags) COLLATE utf8mb4_general_ci LIKE '%$value%'
                                ) fp
                                LEFT JOIN farmer_farm ff ON fp.farm_id = ff.id
                                LEFT JOIN farmer f ON ff.farmer_id = f.id
                                LEFT JOIN person p ON f.person_id = p.id
                                WHERE fp.rn <= 2
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
                                    ff.lon
                                ORDER BY fp.harvest_schedule")->result() as $key => $value) {
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
        $price_qty_left = $this->price_qty_left();
        $thisQuery = $this->db->query("SELECT count(1) AS total FROM farm_produce fp
                                    LEFT JOIN produce p ON fp.produce_id = p.id
                                    LEFT JOIN produce_classification pc ON p.produce_classification_id = pc.id
                                    LEFT JOIN ($price_qty_left) pql ON fp.farm_id = pql.farm_id AND fp.produce_id = pql.produce_id
                                    WHERE fp.farm_id = $farm_id AND CONCAT(p.name,p.tags,pc.class_name,p.description,(CASE WHEN p.is_seasonal = true THEN 'SEASONAL' ELSE 'NON-SEASONAL' END),(CASE WHEN p.is_active = true THEN 'ACTIVE' ELSE 'INACTIVE' END)) 
                                    LIKE '%$searchValue%'");

        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT ROW_NUMBER() OVER (ORDER BY fp.id DESC) AS row_num, fp.id as fp_id,p.id,p.name as produce,p.tags,pql.harvest_schedule,pql.uom,pql.price,pql.qty_left,pql.latest_price_id,pc.class_name,p.description,
                                    p.is_seasonal,p.is_active,p.created_at, p.img_path,pc.img_path as default_img_path, fp.farm_id, pql.wholesale_at_qty,pql.price_wholesale
                                    FROM farm_produce fp
                                    LEFT JOIN produce p ON fp.produce_id = p.id
                                    LEFT JOIN produce_classification pc ON p.produce_classification_id = pc.id
                                    LEFT JOIN ($price_qty_left) pql ON fp.farm_id = pql.farm_id AND fp.produce_id = pql.produce_id
                                    WHERE fp.farm_id = $farm_id AND CONCAT(p.name,p.tags,pc.class_name,p.description,(CASE WHEN p.is_seasonal = true THEN 'SEASONAL' ELSE 'NON-SEASONAL' END),(CASE WHEN p.is_active = true THEN 'ACTIVE' ELSE 'INACTIVE' END)) 
                                    LIKE '%$searchValue%'
                                    ORDER BY p.created_at DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        $cc = 0;
        foreach ($query->result() as $key => $value) {

            // ---------- SAFE VALUES ----------
            $produce = htmlspecialchars($value->produce, ENT_QUOTES, 'UTF-8');
            $tags = htmlspecialchars($value->tags ?? '', ENT_QUOTES, 'UTF-8');
            $uom = htmlspecialchars($value->uom, ENT_QUOTES, 'UTF-8');

            // ---------- IMAGE ----------
            $img = (!empty($value->img_path) && file_exists(FCPATH . $value->img_path))
                ? base_url($value->img_path)
                : base_url($value->default_img_path);

            $image_path = "
        <img src='{$img}'
             width='60'
             height='60'
             class='rounded shadow-sm border'
             style='object-fit:cover;'>";

            // ---------- STOCK COLOR ----------
            $stockColor = ($value->qty_left <= 10) ? "text-danger font-weight-bold" : "text-success font-weight-bold";

            $stock_display = "
                <span class='{$stockColor}' style='font-size:1.1rem;'>
                    {$value->qty_left}
                </span>
            ";

            // ---------- PRICE DISPLAY ----------
            $price_display = "
                <div class='font-weight-bold text-primary' style='font-size:1.1rem;'>
                    ₱" . number_format($value->price, 2) . "
                </div>
                <small class='text-muted' style='display: block;'>per {$uom}</small>
            " . ($value->wholesale_at_qty ? "<small class='badge bg-gray'>Wholesale:<br/> ₱" . number_format($value->price_wholesale, 2) . " per {$uom}<br/> @min {$value->wholesale_at_qty} qty</small>" : "") . "
            ";

            // ---------- QTY INPUT ----------
            $q_id = $value->row_num;

            $qty_input = "
                <input type='number'
                    style='width:90px;text-align:center;'
                    id='qty{$q_id}'
                    min='1'
                    max='{$value->qty_left}'
                    value='1'
                    class='form-control form-control-sm border-success font-weight-bold'>
            ";

            // ---------- ADD TO CART BUTTON ----------
            $add_to_cart = "
                <button class='btn btn-sm btn-warning shadow-sm'
                    onclick='add_to_cart({
                        id: \"{$value->fp_id}\",
                        id_: \"{$value->row_num}\",
                        img_path: \"{$img}\",
                        produce: \"{$produce}\",
                        harvest_schedule: \"{$value->harvest_schedule}\",
                        qty_left: \"{$value->qty_left}\",
                        price: \"{$value->price}\",
                        uom: \"{$uom}\",
                        class_name: \"{$value->class_name}\",
                        is_seasonal: \"{$value->is_seasonal}\",
                        is_active: \"{$value->is_active}\",
                        farm_id: \"{$value->farm_id}\",
                        latest_price_id: \"{$value->latest_price_id}\"
                    })'><b><i class='fa fa-shopping-cart'></i> Add</b>
                </button>
            ";

            // ---------- PRODUCE NAME ----------
            $produce_display = "
            <div class='font-weight-bold'>{$produce}</div>
            " . ($tags ? "<small class='text-muted' style='display: block;'>({$tags})</small>" : "");

            // ---------- FINAL DATA ----------
            $data[] = array(
                $add_to_cart,
                $qty_input,
                $image_path,
                $produce_display,
                '<span class="badge bg-white">' . $value->harvest_schedule . '</span>',
                $stock_display,
                $price_display
            );
        }
        // Prepare the response data in the required format
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
        parse_str($this->input->post("c") ?? '', $filter);
        $person_id = $this->session->agrishop_person_id;
        $farm_produce_id = $item['id'];
        $latest_price_id = $item['latest_price_id'];
        $dateNow = $this->now();
        $transaction_id = '';
        $transaction_status = '';
        $true = ["success"   => true];
        $false = ["success"   => false];
        $price_qty_left = $this->price_qty_left();
        $check_qty_left = $this->db->query("SELECT pql.qty_left, name FROM ($price_qty_left) pql
                                            LEFT JOIN produce p on pql.produce_id = p.id
                                            WHERE pql.id = $farm_produce_id LIMIT 1")->row();
        if ($check_qty_left->qty_left < $qty) {
            $false = ["success"   => false, "message" => "Quantity for " . $check_qty_left->name . " left is $check_qty_left->qty_left, not enough!"];
            echo json_encode($false);
            return;
        }


        $check = $this->db->query("SELECT * FROM transaction WHERE person_id = $person_id AND is_done = false AND farm_id = $farm_id LIMIT 1")->row();

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

        $this->transaction_status($data_transaction_status);

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
            'created_at' => $dateNow
        ];

        $check_qty_left = $this->db->query("SELECT * FROM ($price_qty_left) pql WHERE id = $farm_produce_id AND pql.wholesale_at_qty <= $qty")->row();
        if ($check_qty_left) {
            $data_my_cart += [
                'is_wholesale' => true,
                'sub_total' => $check_qty_left->price_wholesale * $qty,
            ];
        } else {
            $data_my_cart += [
                'sub_total' => $item['price'] * $qty,
            ];
        }

        if ($this->db->insert("my_cart_farm_produce", $data_my_cart)) {
            $cp = $this->getTransactionStatus($person_id, 'PENDING', 'client');
            $true += ["message"   => "Added to cart!", "cart_pending"   => $cp, "cart" => $data_my_cart, "transaction" => $check];
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
        $person_id = $this->session->agrishop_person_id;
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

            $cp = $this->getTransactionStatus($person_id, 'PENDING', 'client');
            $true += ["message"   => "Removed from cart!", "cart_pending"   => $cp];
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
        $this->session->agrishop_pending_trans_count = $this->getTransactionStatus($person_id, 'PENDING', 'client');
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
                                    WHERE t2.status = 'PENDING' AND t1.person_id = $person_id AND CONCAT(t4.farm_name,t2.status,t3.payable) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'");

        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT t1.id AS transaction_id, t4.img_path, DATE_FORMAT(t1.transaction_date,'%m/%d/%y') date_, t4.farm_name,t2.status,t3.payable FROM transaction t1 
                                    JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
		                                    LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce mcfp
		                                    GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                                    LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                                    WHERE t2.status = 'PENDING' AND t1.person_id = $person_id AND CONCAT(t4.farm_name,t2.status,t3.payable) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
                                    ORDER BY t1.id DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        foreach ($query->result() as $value) {
            $total = $value->payable + ($value->payable * 0.01);
            $img = $value->img_path ? base_url($value->img_path) : base_url('dist/img/media/icons/1x1.png');
            $image_path = "<img src='$img' width='50' height='50' class='rounded' data-toggle='tooltip' data-placement='top' title=''>";
            $status_badge = $this->statusBadge($value->status);
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
                            <span class="badge bg-black"
                                style="cursor:pointer; font-size:11px;"
                                onclick="viewTransactionDetails(' . $value->transaction_id . ')">
                                <i class="fa fa-eye"></i> view details
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

    public function getOrderListing()
    {
        $person_id  = $this->session->agrishop_person_id;
        $requestData = $_REQUEST;
        $farm_id = $requestData['search']['farm_id'];
        $status = $requestData['search']['status'];
        if ($status) {
            $status_condition = "t2.status = '$status' OR t5.status = '$status' AND (t2.status != 'COMPLETED' AND t2.status != 'CANCELLED')";
        } else {
            $status_condition = "(t2.status != 'PENDING' AND t2.status != 'COMPLETED' AND t2.status != 'CANCELLED')";
        }
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Query to get total record count
        $thisQuery = $this->db->query("SELECT count(1) AS total FROM transaction t1 
                                    JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
		                                    LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce mcfp
		                                    GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                                    LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                                    LEFT JOIN (SELECT * FROM transaction_delivery_status WHERE is_latest IS TRUE) t5 ON t1.id = t5.transaction_id
                                    WHERE t1.person_id = $person_id AND $status_condition AND CONCAT(t4.farm_name,t2.status,t3.payable) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'");

        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT t1.id AS transaction_id, t4.img_path, DATE_FORMAT(t1.transaction_date,'%m/%d/%y') date_, t4.farm_name,t2.status,t3.payable FROM transaction t1 
                                    JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
		                                    LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce mcfp
		                                    GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                                    LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                                    LEFT JOIN (SELECT * FROM transaction_delivery_status WHERE is_latest IS TRUE) t5 ON t1.id = t5.transaction_id
                                    WHERE $status_condition AND t1.person_id = $person_id AND CONCAT(t4.farm_name,t2.status,t3.payable) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
                                    ORDER BY t1.id DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        foreach ($query->result() as $value) {
            $total = $value->payable + ($value->payable * 0.01);
            $img = $value->img_path ? base_url($value->img_path) : base_url('dist/img/media/icons/1x1.png');
            $image_path = "<img src='$img' width='50' height='50' class='rounded' data-toggle='tooltip' data-placement='top' title=''>";
            $status_badge = $this->statusBadge($value->status);
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
                            <span class="badge bg-black"
                                style="cursor:pointer; font-size:11px;"
                                onclick="viewTransactionDetails(' . $value->transaction_id . ')">
                                <i class="fa fa-eye"></i> view details
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

    public function getCompletedOrderListing()
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
                                    WHERE (t2.status = 'COMPLETED') AND t1.person_id = $person_id AND CONCAT(t4.farm_name,t2.status,t3.payable) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'");

        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT t1.id AS transaction_id, tr.rating, t4.img_path, DATE_FORMAT(t1.transaction_date,'%m/%d/%y') date_, t4.farm_name,t2.status,t3.payable FROM transaction t1 
                                    JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
		                                    LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce mcfp
		                                    GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                                    LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                                    LEFT JOIN transaction_ratings tr ON t1.id = tr.transaction_id
                                    WHERE (t2.status = 'COMPLETED') AND t1.person_id = $person_id AND CONCAT(t4.farm_name,t2.status,t3.payable) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
                                    ORDER BY t1.id DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        foreach ($query->result() as $value) {
            $total = $value->payable + ($value->payable * 0.01);
            $img = $value->img_path ? base_url($value->img_path) : base_url('dist/img/media/icons/1x1.png');
            $image_path = "<img src='$img' width='50' height='50' class='rounded' data-toggle='tooltip' data-placement='top' title=''>";
            $status_badge = $this->statusBadge($value->status);
            $stars = $this->renderStars($value->rating);
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
                                <div style="font-size:12px;">
                                    ' . $stars . '
                                </div>
                            </div>

                            <span class="badge bg-success" style="font-size:14px;">
                                ₱ ' . $this->format_price($total) . '
                            </span>
                        </div>

                        <div class="d-flex align-items-center mt-1" style="gap:6px;">
                            <span class="badge bg-black"
                                style="cursor:pointer; font-size:11px;"
                                onclick="viewTransactionDetails(' . $value->transaction_id . ')">
                                <i class="fa fa-eye"></i> view details
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

    public function getCancelledOrderListing()
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
                                    WHERE (t2.status = 'CANCELLED') AND t1.person_id = $person_id AND CONCAT(t4.farm_name,t2.status,t3.payable) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'");

        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT t1.id AS transaction_id, t4.img_path, DATE_FORMAT(t1.transaction_date,'%m/%d/%y') date_, t4.farm_name,t2.status,t3.payable FROM transaction t1 
                                    JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
		                                    LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce mcfp
		                                    GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                                    LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                                    WHERE (t2.status = 'CANCELLED') AND t1.person_id = $person_id AND CONCAT(t4.farm_name,t2.status,t3.payable) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
                                    ORDER BY t1.id DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        foreach ($query->result() as $value) {
            $total = $value->payable + ($value->payable * 0.01);
            $img = $value->img_path ? base_url($value->img_path) : base_url('dist/img/media/icons/1x1.png');
            $image_path = "<img src='$img' width='50' height='50' class='rounded' data-toggle='tooltip' data-placement='top' title=''>";
            $status_badge = $this->statusBadge($value->status);
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
                            <span class="badge bg-black"
                                style="cursor:pointer; font-size:11px;"
                                onclick="viewTransactionDetails(' . $value->transaction_id . ')">
                                <i class="fa fa-eye"></i> view details
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

    public function getRateOrderListing()
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
                                    LEFT JOIN transaction_ratings tr ON tr.transaction_id = t1.id
                                    WHERE (t2.status = 'COMPLETED' AND tr.rating IS NULL) AND t1.person_id = $person_id AND CONCAT(t4.farm_name,t2.status,t3.payable) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'");

        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT t1.id AS transaction_id, tr.rating, t4.img_path, DATE_FORMAT(t1.transaction_date,'%m/%d/%y') date_, t4.farm_name,t2.status,t3.payable FROM transaction t1 
                                    JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
		                                    LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce mcfp
		                                    GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                                    LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                                    LEFT JOIN transaction_ratings tr ON tr.transaction_id = t1.id
                                    WHERE (t2.status = 'COMPLETED' AND tr.rating IS NULL) AND t1.person_id = $person_id AND CONCAT(t4.farm_name,t2.status,t3.payable) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
                                    ORDER BY t1.id DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        foreach ($query->result() as $value) {
            $total = $value->payable + ($value->payable * 0.01);
            $img = $value->img_path ? base_url($value->img_path) : base_url('dist/img/media/icons/1x1.png');
            $image_path = "<img src='$img' width='50' height='50' class='rounded' data-toggle='tooltip' data-placement='top' title=''>";
            $status_badge = $this->statusBadge($value->status);
            if ($value->rating > 0) {
                $stars = $this->renderStars($value->rating);
            } else {
                $stars = '<span style="cursor:pointer;color:#007bff;font-size:12px"
                onclick="rateTransaction(' . $value->transaction_id . ')">
                ⭐ Rate Now
              </span>';
            }
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
                            <span class="badge bg-black"
                                style="cursor:pointer; font-size:11px;"
                                onclick="viewTransactionDetails(' . $value->transaction_id . ')">
                                <i class="fa fa-eye"></i> view details
                            </span>
                            <div style="font-size:12px;">
                                ' . $stars . '
                            </div>
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

    public function save_rating()
    {
        $person_id = $this->session->agrishop_person_id;
        $data = [
            'transaction_id' => $this->input->post('transaction_id'),
            'rating' => $this->input->post('rating_value'),
            'comment' => $this->input->post('review_comment'),
            'created_at' => date('Y-m-d H:i:s'),
            'created_by_person_id' => $person_id,
        ];

        $this->db->insert('transaction_ratings', $data);

        echo json_encode(['status' => true]);
    }


    private function renderStars($rating)
    {
        $html = '';

        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $html .= '<i class="fa fa-star text-warning"></i>';
            } else {
                $html .= '<i class="fa fa-star text-secondary"></i>';
            }
        }

        return $html;
    }

    public function getCartDetails()
    {
        $person_id  = $this->session->agrishop_person_id;
        $requestData = $_REQUEST;
        $transaction_id = $requestData['search']['transaction_id'];
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        $query = $this->db->query("SELECT t1.id as cart_id,t1.transaction_id,t1.qty,t4.price,t4.price_wholesale,t2.uom,t3.name as produce_name,t3.img_path,t1.created_at, t1.is_wholesale,
                                        t5.status as t_status,t6.status as t_p_status,t7.status as t_d_status
                                    FROM my_cart_farm_produce t1
                                    LEFT JOIN farm_produce t2 ON t1.farm_produce_id = t2.id
                                    LEFT JOIN produce t3 ON t2.produce_id = t3.id
                                    LEFT JOIN (SELECT * FROM transaction_status WHERE transaction_id = $transaction_id AND is_latest IS TRUE) t5 ON t1.transaction_id = t5.transaction_id
                                    LEFT JOIN (SELECT * FROM transaction_payment_status WHERE transaction_id = $transaction_id AND is_latest IS TRUE) t6 ON t1.transaction_id = t6.transaction_id
                                    LEFT JOIN (SELECT * FROM transaction_delivery_status WHERE transaction_id = $transaction_id AND is_latest IS TRUE) t7 ON t1.transaction_id = t7.transaction_id
                                    LEFT JOIN (SELECT * FROM price_monitoring_farm_produce WHERE is_latest IS TRUE) t4 ON t1.price_id_during_transact = t4.id
                                    WHERE t1.transaction_id =$transaction_id
                                    ORDER BY t1.id DESC
                                    LIMIT $limit OFFSET $offset
                                    ");
        $q_status = $query->row()->t_status;
        $q_p_status = $query->row()->t_p_status;
        $q_d_status = $query->row()->t_d_status;
        $pay_to_admin = $this->db->query("SELECT * FROM transaction_payment_to_admin t8 WHERE t8.transaction_id = $transaction_id LIMIT 1");
        if ($q_status == 'PENDING') {
            $gcash_details = $this->db->query("SELECT mcfp.transaction_id ,fpm.*,t8.approved_by as t_p_approved_by, t8.id as t_p_id FROM my_cart_farm_produce mcfp 
									JOIN farm_produce fp ON mcfp.farm_produce_id = fp.id
									JOIN farmer_farm ff ON fp.farm_id = ff.id
                                    JOIN farmer f ON ff.farmer_id = f.id
									JOIN farmer_payment_method fpm ON f.person_id = fpm.person_id
                                    LEFT JOIN transaction_payment_to_admin t8 ON mcfp.transaction_id = t8.transaction_id
									WHERE mcfp.transaction_id = $transaction_id LIMIT 1");
        }

        $data = array();
        $subtotal = 0;
        $g_name = '';
        $g_number = '';
        $g_qr = '';
        $g_pay = '';
        $trash = '';
        foreach ($query->result() as $value) {
            $pricing = $value->is_wholesale == 't' ? $value->price_wholesale : $value->price;
            $price = $pricing * $value->qty;
            $subtotal += $price;

            $img = $value->img_path
                ? base_url($value->img_path)
                : base_url('dist/img/media/icons/1x1.png');
            if ($q_status == 'PENDING') {
                $trash = '<span class="text-black"
                            style="cursor:pointer; font-size:13px; margin-top:2px;"
                            title="Remove"
                            onclick="removeCart(' . $value->cart_id . ',' . $value->transaction_id . ')">
                            <i class="fa fa-trash"></i>
                        </span>';
            }
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
                                        ' . $value->qty . ' ' . $value->uom . ' × ₱ ' . ($value->is_wholesale == 't' ?
                    $this->format_price($value->price_wholesale) . ' <span class="badge bg-gray p-1" style="font-size:10px;">wholesale</span>'
                    :  $this->format_price($value->price)) . '
                                    </div>
                                </div>

                                <!-- REMOVE -->
                                ' . $trash . '
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

        $percent = 0.01;
        $convenience_fee = $subtotal * $percent;
        $total_payment   = $subtotal + $convenience_fee;
        $convenience_fee_ =  ' ₱ ' . $this->format_price($convenience_fee);
        $t_p_approved_by = '';
        $t_p_id = '';
        if ($pay_to_admin->num_rows() > 0) {
            $t_p_approved_by = $pay_to_admin->row()->approved_by;
            $t_p_id = $pay_to_admin->row()->id;
        }
        $to_admin_payment = $t_p_id == '' ? '<font color="black"><i class="fa fa-times-circle"></i>To be paid ' . $convenience_fee_ . '</font>' : ($t_p_id && $t_p_approved_by == '' ? '<font color="orange"><i class="fa fa-exclamation-circle"></i>To be verified ' . $convenience_fee_ . '</font>' : '<font color="green"><i class="fa fa-check-circle"></i>Verified ' . $convenience_fee_ . '</font>');



        if ($q_status == 'PENDING') {

            if ($gcash_details->num_rows() > 0) {
                $g = $gcash_details->row();

                $gcash_icon = base_url('dist/img/credit/gcash_50x50.png');
                $g_name = $g->account_name;
                $g_number = $g->number;
                $g_qr = $g->qr;

                $g_pay = ' <div class="form-check form-check-inline" style="margin-right:10px;">
                                    <input class="form-check-input" type="radio" name="payment_method"
                                        id="pay_gcash" value="gcash">
                                    <label class="form-check-label pay_gcash" 
                                        data-name="' . $g_name . '" 
                                        data-number="' . $g_number . '" 
                                        data-qr="' . $g_qr . '" 
                                    for="pay_gcash" style="cursor:pointer;">
                                        <img src="' . $gcash_icon . '" alt="GCash" style="width: 20px; height: 20px; margin-left: 5px;">
                                        GCash
                                    </label>
                                </div>';
            }
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
                                        id="pay_cash" 
                                        data-total="' . $total_payment . '" 
                                        data-trans_id="' . $transaction_id . '"
                                        data-convenience_fee="' . $convenience_fee . '"
                                        data-subtotal="' . $subtotal . '"
                                        data-percentage="' . $percent . '"
                                        value="cash" checked>
                                    <label class="form-check-label" for="pay_cash" style="cursor:pointer;">
                                    <i class="fa fa-money-bill"></i>
                                        Cash
                                    </label>
                                </div>

                                ' . $g_pay . '

                            </div>

                            <hr style="margin:6px 0;">

                            <!-- PAYMENT BREAKDOWN -->
                            <div class="d-flex justify-content-between">
                                <span>Subtotal</span>
                                <span>₱ ' . $this->format_price($subtotal) . '</span>
                            </div>
                            <div class="d-flex justify-content-between text-muted">
                                <span>Fee (1%) <small>(Convenience Fee)</small></span>
                                <span> ' . $to_admin_payment . '</span>
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

            $data[] = array('
            <div id="gcashDetailsBox" class="p-3 mt-2 border rounded" 
                style="display:none; font-size:13px; background:#f9fbff;">

                <!-- INSTRUCTIONS -->
                <div class="mb-2">
                    <div style="font-weight:600; margin-bottom:4px;">
                        How to Pay via GCash
                    </div>

                    <ol style="padding-left:18px; margin-bottom:6px;">
                        <li>Open your <b>GCash</b> app</li>
                        <li>Tap <b>Scan QR</b></li>
                        <li>Scan the QR code below</li>
                        <li>Enter the <b>exact amount</b> to pay</li>
                        <li>Confirm the payment</li>
                        <li>Upload the <b>proof of payment</b> below</li>
                    </ol>

                    <small class="text-muted">
                        ⚠️ Payment will be verified after submission
                    </small>
                </div>

                <hr style="margin:6px 0;">

                <!-- QR CODE -->
                <div class="text-center mb-2">
                    <img id="gcashQR" src="" alt="GCash QR" 
                        style="max-width:200px;" class="shadow-sm">
                </div>

                <!-- ACCOUNT INFO -->
                <div class="text-center mb-2">
                    <div style="font-weight:600;" id="gcashName"></div>
                    <div class="text-muted" id="gcashNumber"></div>
                </div>

                <hr style="margin:6px 0;">

                <!-- PROOF UPLOAD -->
                <div>
                    <label style="font-weight:600;">
                        Upload Proof of Payment 
                        <i style="color:red;">(Required)</i>
                    </label>

                    <input type="file" 
                        class="form-control form-control-sm mt-1" 
                        name="proof_of_payment" 
                        accept="image/*">
                </div>

            </div>');

            $data[] = array(
                '<button class="btn btn-primary btn-block w-100 checkout-btn" onclick="checkout()">
                Checkout
            </button>',
            );
        } else {
            $data[] = array(
                '
                        <div class="p-2" style="width:100%; font-size:13px; line-height:1.2">

                            <!-- DELIVERY OPTION -->
                            <div>
                                <div style="font-weight:600; margin-bottom:2px;">Delivery Status</div>

                                <div class="form-check form-check-inline" style="margin-right:10px;">
                                    <label class="form-check-label" for="pickup"  style="cursor:pointer;">
                                        ' . $this->statusBadge($q_d_status) . '
                                    </label>
                                </div>
                            </div>

                            <!-- MODE OF PAYMENT -->
                            <div>
                                <div style="font-weight:600; margin-bottom:2px;">Payment</div>
                                <div class="form-check form-check-inline" style="margin-right:10px;">
                                    <label class="form-check-label" for="pay_cash" id="pay_cash" 
                                        data-trans_id="' . $transaction_id . '" style="cursor:pointer;">
                                        ' . $this->statusBadge($q_p_status) . '
                                    </label>
                                </div>

                            </div>

                            <hr style="margin:6px 0;">

                            <!-- PAYMENT BREAKDOWN -->
                            <div class="d-flex justify-content-between">
                                <span>Subtotal</span>
                                <span>₱ ' . $this->format_price($subtotal) . '</span>
                            </div>

                            <div class="d-flex justify-content-between text-muted">
                                <span>Fee (1%) <small>(Convenience Fee)</small></span>
                                <span> ' . $to_admin_payment . '</span>
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
        }

        if ($q_status == 'RESERVED') {
            $data[] = array(
                '<button class="btn bg-danger text-white btn-block w-100 cancel-btn" onclick="cancelOrder()">
                Cancel Order
            </button>',
            );
        }
        $response = array(
            'draw' => intval($requestData['draw']),
            'recordsTotal' => intval(10000),
            'recordsFiltered' => intval(10000), // For simplicity, assuming no filtering is applied
            'data' => $data,
        );
        echo json_encode($response);
    }

    public function payprocessingfee()
    {

        $this->db->trans_begin();
        $person_id = $this->session->agrishop_person_id;
        $dateNow = $this->now();
        $true = ["success"   => true];
        $false = ["success"   => false];
        $trans_id = $this->input->post('trans_id');
        $convenience_fee = $this->input->post('convenience_fee');
        $data_transaction_proof_of_payment = [];
        if (!empty($_FILES['paymentProof'])) {
            if (isset($_FILES['paymentProof']) && $_FILES['paymentProof']['error'] === UPLOAD_ERR_OK) {
                // Normal upload
                $upload = $this->uploadImg($_FILES['paymentProof'], $person_id . $trans_id, 'payment_to_admin', 'paymentProof');
                $data_transaction_proof_of_payment = [
                    "transaction_id" => $trans_id,
                    "amount" => $convenience_fee,
                    "img_path" => $upload
                ];
            }
        }

        if ($this->db->insert("transaction_payment_to_admin", $data_transaction_proof_of_payment)) {
            $this->getTransactionStatus($person_id, 'TO_BE_VERIFIED', 'client');
            $true += ["message"   => "Successfully paid processing fee!"];
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


    public function submit_order()
    {
        $this->db->trans_begin();
        $person_id = $this->session->agrishop_person_id;
        $dateNow = $this->now();
        $true = ["success"   => true];
        $false = ["success"   => false];


        $pay      = $this->input->post('pay');
        $total    = $this->input->post('total');
        $delivery = $this->input->post('delivery');     // pickup / cod
        $transaction_id = $this->input->post('trans_id');
        $number   = $this->input->post('number');
        $name     = $this->input->post('name');
        $subtotal = $this->input->post('subtotal');
        $percentage = $this->input->post('percentage');
        $proof    = $this->input->post('proof_of_payment');


        // $check_qty_left = $this->db->query("SELECT pql.qty_left, name FROM price_qty_left pql
        //                                     LEFT JOIN produce p on pql.produce_id = p.id
        //                                     WHERE pql.id = $farm_produce_id LIMIT 1")->row();
        $price_qty_left = $this->price_qty_left();
        $check_qty_left = $this->db->query("SELECT pql.id,pql.qty_left,qq.qty_to_be_checkout,p.name FROM ($price_qty_left) pql 
            JOIN  (SELECT mcfp.farm_produce_id, sum(mcfp.qty) AS qty_to_be_checkout FROM my_cart_farm_produce mcfp  
                    WHERE mcfp.transaction_id = $transaction_id
                    GROUP BY farm_produce_id) qq ON pql.id=qq.farm_produce_id AND qq.qty_to_be_checkout>pql.qty_left
            JOIN produce p ON pql.produce_id = p.id")->result();
        if (!empty($check_qty_left)) {
            $item = $check_qty_left[0];

            echo json_encode([
                "success" => false,
                "message" => "Quantity for {$item->name} left is {$item->qty_left}, not enough!"
            ]);
            return;
        }


        // FILE (GCash proof)
        if (!empty($_FILES['proof_of_payment'])) {
            if (isset($_FILES['proof_of_payment']) && $_FILES['proof_of_payment']['error'] === UPLOAD_ERR_OK) {
                // Normal upload
                $upload = $this->uploadImg($_FILES['proof_of_payment'], $person_id . $pay, 'proof_payment', 'proof_of_payment');
                $data_transaction_proof_of_payment = [
                    "transaction_id" => $transaction_id,
                    "img" => $upload,
                    "total_amount" => $total,
                    "contact_number" => $number,
                    "name" => $name,
                ];
                $this->db->insert("transaction_proof_of_payment", $data_transaction_proof_of_payment);
            }
        }


        // 💳 PAYMENT STATUS
        if ($pay == 'gcash') {
            $payment_status = 'VERIFYING';
        } else {
            $payment_status = 'UNPAID';
        }

        // 🚚 DELIVERY STATUS
        if ($delivery == 'pickup') {
            $delivery_status = 'TO_BE_PICKUP';
        } else {
            $delivery_status = 'TO_BE_DELIVER';
        }

        // 🚚 DELIVERY STATUS
        $data_delivery_status = [
            'transaction_id' => $transaction_id,
            'status' => $delivery_status,
            'created_by_person_id' => $person_id,
        ];
        $this->transaction_delivery_status($data_delivery_status);

        // 🧾 PAYMENT STATUS
        $data_payment_status = [
            'transaction_id' => $transaction_id,
            'status' => $payment_status,
            'created_by_person_id' => $person_id,
        ];
        $this->transaction_payment_status($data_payment_status);

        // 🧾 TRANSACTION STATUS
        $data_transaction_status = [
            'transaction_id' => $transaction_id,
            'status' => 'RESERVED',
            'created_by_person_id' => $person_id,
        ];
        $this->transaction_status($data_transaction_status);

        // 🧾 TRANSACTION DONE
        $data_transaction = [
            'is_done' => true,
            'done_at' => $this->now(),
        ];
        $this->db->update("transaction", $data_transaction, ["id" => $transaction_id]);



        $data_transaction_details = [
            'transaction_id' => $transaction_id,
            'checkout_at' => $this->now(),
            'payment_method' => $pay,
            'delivery_method' => $delivery,
            'total_payment' => $total,
            'to_admin' => $subtotal * $percentage,
            'to_farmer' => $subtotal,
            'to_admin_percent' => $percentage,
        ];

        if ($this->db->insert("transaction_details", $data_transaction_details)) {
            $cp = $this->getTransactionStatus($person_id, 'PENDING', 'client');
            $true += ["message"   => "Checkout success!", "cart_pending"   => $cp];
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

    public function cancel_order()
    {

        $this->db->trans_begin();
        $person_id = $this->session->agrishop_person_id;
        $dateNow = $this->now();
        $true = ["success"   => true];
        $false = ["success"   => false];

        $reason = $this->input->post('cancel_reason');
        $transaction_id = $this->input->post('trans_id');

        $check = $this->checkTransactionStatus($transaction_id);

        if ($check == 'PREPARING' || $check == 'DELIVERED' || $check == 'CANCELLED') {
            $false += ["message"   => "You cannot cancel anymore this order!"];
            $ret = $false;
            echo json_encode($ret);
            return;
        }

        // 🧾 TRANSACTION STATUS
        $data_transaction_status = [
            'transaction_id' => $transaction_id,
            'status' => 'CANCELLED',
            'created_by_person_id' => $person_id,
        ];
        $this->transaction_status($data_transaction_status);


        $data_transaction_cancel_details = [
            'transaction_id' => $transaction_id,
            'reason' => $reason,
            'created_at' =>  $this->now(),
            'created_by' => $person_id,
        ];


        // 🧾 TRANSACTION DONE
        $data_transaction = [
            'is_done' => true,
            'done_at' => $this->now(),
        ];
        $this->db->update("transaction", $data_transaction, ["id" => $transaction_id]);

        if ($this->db->insert("transaction_cancel", $data_transaction_cancel_details)) {
            $cp = $this->getTransactionStatus($person_id, 'PENDING', 'client');
            $true += ["message"   => "Order cancelled!", "cart_pending"   => $cp];
            $ret = $true;
        } else {
            $false += ["message"   => "Failed to cancel order!"];
            $ret = $false;
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }

        echo json_encode($ret);
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

        // Build dynamic "LIKE" filters
        $conditions = "";
        foreach ($words as $w) {
            $w = trim($w);
            if ($w !== "") {
                $conditions .= " AND CONCAT(
                t1.description,' ',
                t2.description,', ',
                t3.description,', ',
                t4.region
            ) COLLATE utf8mb4_general_ci LIKE '%" . $this->db->escape_like_str($w) . "%' ";
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

        // Build dynamic "LIKE" filters
        $conditions = "";
        foreach ($words as $w) {
            $w = trim($w);
            if ($w !== "") {
                $conditions .= " AND CONCAT(
                t1.description,' ',
                t2.description,', ',
                t3.description,', ',
                t4.region
            ) COLLATE utf8mb4_general_ci LIKE '%" . $this->db->escape_like_str($w) . "%' ";
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
// created_by_person_id int4 DEFAULT 1 NOT NULL,