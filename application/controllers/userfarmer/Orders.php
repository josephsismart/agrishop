<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Orders extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->redirect();
        $this->load->model('mainModel');
        $this->load->helper('date');
        date_default_timezone_set("Asia/Manila");
    }

    public function index()
    {
        $page_data = $this->system();
        $uri = $this->session->agrishop_login_uri;
        $page_data += [
            "page_title"        => "Orders",
            "current_location"  => "orders",
            "content"           =>  [$this->load->view('interface/' . $uri . '/Orders', [
                "billing" => $this->billing_page(true),
            ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }


    public function acceptAndPrepare()
    {
        $this->db->trans_begin();
        $person_id = $this->session->agrishop_person_id;
        $dateNow = $this->now();
        $true = ["success"   => true];
        $false = ["success"   => false];

        $transaction_id = $this->input->post('trans_id');
        $status = $this->input->post('status');
        $text = $status == "PREPARING" ? "Order accepted" : ($status == "ORDER_IS_READY" ? "Order is ready" : ($status == "COMPLETED" ? "Successfully completed!" : "accept"));


        // 🧾 TRANSACTION STATUS
        $data_transaction_status = [
            'transaction_id' => $transaction_id,
            'status' => $status,
            'created_by_person_id' => $person_id,
        ];

        if ($status == "COMPLETED") {

            // 🧾 PAID DONE
            $data_transaction_payment = [
                'transaction_id' => $transaction_id,
                'status' => "PAID",
                'created_by_person_id' => $person_id,
            ];
            $this->transaction_payment_status($data_transaction_payment);

            // 🧾 DELIVERED DONE
            $data_transaction_payment = [
                'transaction_id' => $transaction_id,
                'status' => "DELIVERED",
                'created_by_person_id' => $person_id,
            ];
            $this->transaction_delivery_status($data_transaction_payment);


            // 🧾 TRANSACTION DONE
            $data_transaction = [
                'is_done' => true,
                'done_at' => $this->now(),
            ];
            $this->db->update("transaction", $data_transaction, ["id" => $transaction_id]);
        }



        if ($this->transaction_status($data_transaction_status)) {
            // $cp = $this->getTransactionPeding($person_id);
            $true += ["message"   => $text];
            $ret = $true;
        } else {
            $false += ["message"   => "Failed to order!"];
            $ret = $false;
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }

        echo json_encode($ret);
    }



    public function getCartListingCompleted()
    {

        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';
        $this->getCartListing('COMPLETED', $searchValue);
    }

    public function getCartListingCancelled()
    {

        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';
        $this->getCartListing('CANCELLED', $searchValue);
    }


    public function getCartListing($status = null, $searchValue = null)
    {
        $person_id  = $this->session->agrishop_person_id;
        $requestData = $_REQUEST;
        $farmer_id = $this->session->agrishop_login_farmer_id;
        $status = $status ?? $this->input->post('status');
        $FILTER_STATUS = $status == 'COMPLETED' ? "(t2.status = 'COMPLETED')" : ($status == 'CANCELLED' ? "(t2.status = 'CANCELLED')" :
            "(t2.status != 'COMPLETED' AND t2.status != 'PENDING' AND t2.status != 'CANCELLED')");
        $searchValue = $searchValue ?: (isset($requestData['search']['value']) ? $requestData['search']['value'] : '');

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Query to get total record count
        $thisQuery = $this->db->query("SELECT count(1) AS total FROM transaction t1 
                                    JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
		                                    LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce mcfp
		                                    GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                                    LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                                    WHERE 
                                    t4.farmer_id = $farmer_id AND 
                                    $FILTER_STATUS AND 
                                    CONCAT(t4.farm_name,t2.status,t3.payable) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'");

        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT t1.id AS transaction_id,t1.transaction_date ,t1.person_id,t5.barangay_id,t5.contact_num,  t5.img_path, DATE_FORMAT(t1.transaction_date,'%m/%d/%y') date_, 
                                            t4.farm_name,t2.status,t2.created_at,t3.payable,t6.img AS gcash FROM transaction t1 
                                    JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
		                            LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce mcfp
		                                        GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                                    LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                                    LEFT JOIN person t5 ON t1.person_id = t5.id
                                    LEFT JOIN transaction_proof_of_payment t6 ON t1.id = t6.transaction_id
                                    WHERE 
                                    t4.farmer_id = $farmer_id AND 
                                    $FILTER_STATUS AND 
                                    CONCAT(t4.farm_name,t2.status,t3.payable) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
                                    ORDER BY t2.status='RESERVED' DESC, t2.created_at DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        foreach ($query->result() as $value) {
            $total = $value->payable + ($value->payable * 0.01);
            $img = $value->img_path ? base_url($value->img_path) : base_url('dist/img/media/icons/1x1.png');
            $image_path = "<img src='$img' width='70' height='70' class='rounded' data-toggle='tooltip' data-placement='top' title=''>";
            $status_badge = $this->statusBadge($value->status);
            $delivery_status = $this->checkTransactionDeliveryStatus($value->transaction_id);
            $payment_status = $this->checkTransactionPaymentStatus($value->transaction_id);
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
                                <div style="font-size:13px; color:#777;">
                                    ' . $value->transaction_date . '
                                </div>

                                <div style="font-size:18px; font-weight:600; color:#000;">
                                    ' . $this->getPersonName($value->person_id) . '
                                </div>

                                <div style="font-size:14px; color:#000;">
                                    ' . $this->getAddress($value->barangay_id) . '
                                </div>

                                <div style="font-size:16px;font-weight:600; color:#333;">
                                    ' . $value->contact_num . '
                                </div>
                            </div>
                            <span class="badge bg-success" style="font-size:16px;">
                                ₱ ' . $this->format_price($total) . '
                            </span>
                            
                                
                        </div>

                        <!-- BOTTOM ROW -->
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <!-- LEFT ACTIONS -->
                            <div class="d-flex align-items-center" style="gap:6px;">
                                <span class="badge bg-white" title="View Order"
                                    style="cursor:pointer; font-size:13px;"
                                    onclick="viewTransactionDetails(' . $value->transaction_id . ')">
                                    <i class="fa fa-eye"></i> ' . $status_badge . '
                                </span>
                            </div>
                            ' . (($value->status != 'COMPLETED' && $value->status != 'CANCELLED') ? '
                            <div class="d-flex align-items-center" style="gap:8px;">
                                <span class="badge bg-white" style="cursor:pointer;"
                                    onclick="updateModalStatus(' . $value->transaction_id . ',\'' . $delivery_status . '\',\'DELIVERY\')">
                                    <i class="fa fa-truck"></i> ' . $this->statusBadge($delivery_status) . '
                                </span>

                                <span class="badge bg-white" style="cursor:pointer;"
                                    onclick="updateModalStatus(' . $value->transaction_id . ',\'' . $payment_status . '\',\'PAYMENT\')">
                                    <i class="fa fa-money-bill"></i> ' . $this->statusBadge($payment_status) . '
                                </span>' .
                    ($value->gcash != null ? '
                                <span class="badge bg-white ml-n1" style="cursor:pointer;"
                                    onclick="viewGcashAttachment(`' . base_url($value->gcash) . '`)">
                                    <img src="' . base_url('dist/img/credit/gcash_50x50.png') . '" alt="GCash" style="width:18px; height:18px;">
                                </span>' : '') .
                    '
                            </div>
                            ' : '') . '
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
        $transaction_id = $transaction_id ?? $requestData['search']['transaction_id'];

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


        $data = array();
        $subtotal = 0;
        $trash = '';
        foreach ($query->result() as $value) {
            $pricing = $value->is_wholesale == 't' ? $value->price_wholesale : $value->price;
            $price = $pricing * $value->qty;
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

        if ($q_status == 'RESERVED') {
            $data[] = array(
                '<button class="btn bg-primary text-white btn-block w-100 cancel-btn" style="font-size:18px !important;" onclick="acceptAndPrepare(' . $transaction_id . ', \'PREPARING\')">
                    <fa class="fa fa-check"></fa> Accept and Prepare
                </button>',
            );
        }

        if ($q_status == 'PREPARING') {
            $data[] = array(
                '<button class="btn bg-info text-white btn-block w-100 cancel-btn" style="font-size:18px !important;" onclick="acceptAndPrepare(' . $transaction_id . ', \'ORDER_IS_READY\')">
                    <fa class="fa fa-check"></fa> Order is Ready
                </button>',
            );
        }

        if ($q_status == 'ORDER_IS_READY') {
            $data[] = array(
                '<button class="btn bg-success text-white btn-block w-100 cancel-btn" style="font-size:18px !important;" onclick="acceptAndPrepare(' . $transaction_id . ', \'COMPLETED\')">
                    <fa class="fa fa-check"></fa> Complete Order
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

    public function updateStatus()
    {
        $this->db->trans_begin();
        $person_id = $this->session->agrishop_person_id;
        $dateNow = $this->now();
        $true = ["success"   => true];
        $false = ["success"   => false];

        $transaction_id = $this->input->post('trans_id');
        $status = $this->input->post('status');
        $type = $this->input->post('type');
        $text = $status == "PREPARING" ? "Order accepted" : ($status == "ORDER_IS_READY" ? "Order is ready" : ($status == "COMPLETED" ? "Successfully completed!" : "accept"));
        $table = "";

        if ($type == "payment") {
            $table = "transaction_payment_status";
        }
        if ($type == "delivery") {
            $table = "transaction_delivery_status";
        }

        $data_status = [
            'transaction_id' => $transaction_id,
            'status' => $status,
            'created_by_person_id' => $person_id,
        ];
        // echo $transaction_id . " " . $status . " " . $person_id;
        if ($this->transaction_delivery_status($data_status)) {
            $true += ["message"   => "Successfully updaed!"];
            $ret = $true;
        } else {
            $false += ["message"   => "Failed to order!"];
            $ret = $false;
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }

        echo json_encode($ret);
    }
}
