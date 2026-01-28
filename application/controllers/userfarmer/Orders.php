<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Orders extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->redirect();
        $this->load->model('mainModel');
        $this->load->helper('date');
        date_default_timezone_set("Asia/Manila");
    }

    public function index()
    {
        $this->user_create_page(['view' => 'interface/userfarmer/Orders']);
    }

    public function accept_order()
    {
        $this->db->trans_begin();
        $person_id = $this->session->agrishop_person_id;
        $dateNow = $this->now();
        $true = ["success"   => true];
        $false = ["success"   => false];

        $transaction_id = $this->input->post('trans_id');

        $check = $this->checkTransactionStatus($transaction_id);

        if ($check == 'PREPARING' || $check == 'DELIVERED' || $check == 'CANCELLED' || $check == 'COMPLETED') {
            $false += ["message"   => "You cannot accept this order!"];
            $ret = $false;
            echo json_encode($ret);
            return;
        }

        // 🧾 TRANSACTION STATUS
        $data_transaction_status = [
            'transaction_id' => $transaction_id,
            'status' => 'PREPARING',
            'created_by_person_id' => $person_id,
        ];
        $this->db->insert("transaction_status", $data_transaction_status);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $false += ["message"   => "Failed to accept order!"];
            $ret = $false;
        } else {
            $this->db->trans_commit();
            $true += ["message"   => "Order accepted!"];
            $ret = $true;
        }

        echo json_encode($ret);
    }

    public function getIncomingOrders()
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
                                    WHERE t4.created_by_person_id = $person_id AND t2.status = 'RESERVED' 
                                    AND CONCAT(t4.farm_name,t2.status,t3.payable) ILIKE '%$searchValue%'");

        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT t1.id AS transaction_id, t4.img_path, to_char(t1.transaction_date,'mm/dd/yy') date_, t4.farm_name,t2.status,t3.payable FROM transaction t1 
                                    JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
                                    LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce mcfp
                                    GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                                    LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                                    WHERE t4.created_by_person_id = $person_id AND t2.status = 'RESERVED'
                                    AND CONCAT(t4.farm_name,t2.status,t3.payable) ILIKE '%$searchValue%\'
                                    ORDER BY t1.id DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        foreach ($query->result() as $value) {
            $total = $value->payable + ($value->payable * 0.01);
            $img = $value->img_path ? base_url($value->img_path) : base_url('dist/img/media/icons/1x1.png');
            $image_path = "<img src='$img' width='50' height='50' class='rounded' data-toggle='tooltip' data-placement='top' title=''>";
            $status_badge = $this->statusBadge($value->status);
            $accept_button = '<button class="btn btn-success btn-sm" onclick="acceptOrder(' . $value->transaction_id . ')"><i class="fa fa-check"></i> Accept</button>';
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
                \',
                $this->format_price($total),
                $status_badge,
                $accept_button
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
}
