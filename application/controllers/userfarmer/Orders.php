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
            "content"           => [$this->load->view('interface/' . $uri . '/Orders', [
                "billing" => $this->subscription_count(),
            ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }

    public function acceptAndPrepare()
    {
        $this->db->trans_begin();
        $person_id = $this->session->agrishop_person_id;
        $true  = ["success" => true];
        $false = ["success" => false];

        $transaction_id = $this->input->post('trans_id');
        $status         = $this->input->post('status');
        $text = $status == "PREPARING"      ? "Order accepted"
              : ($status == "ORDER_IS_READY" ? "Order is ready"
              : ($status == "COMPLETED"      ? "Successfully completed!" : "Updated"));

        $data_transaction_status = [
            'transaction_id'       => $transaction_id,
            'status'               => $status,
            'created_by_person_id' => $person_id,
        ];

        if ($status == "COMPLETED") {
            $this->transaction_payment_status([
                'transaction_id'       => $transaction_id,
                'status'               => 'PAID',
                'created_by_person_id' => $person_id,
            ]);
            $this->transaction_delivery_status([
                'transaction_id'       => $transaction_id,
                'status'               => 'DELIVERED',
                'created_by_person_id' => $person_id,
            ]);
            $this->db->update("transaction", ['is_done' => true, 'done_at' => $this->now()], ["id" => $transaction_id]);
        }

        if ($this->transaction_status($data_transaction_status)) {
            $true  += ["message" => $text];
            $ret    = $true;
        } else {
            $false += ["message" => "Failed to update order!"];
            $ret    = $false;
        }

        $this->db->trans_status() === false
            ? $this->db->trans_rollback()
            : $this->db->trans_commit();

        echo json_encode($ret);
    }

    // ── Main listing (handles ACTIVE / COMPLETED / CANCELLED) ─
    public function getCartListing($status = null, $searchValue = null)
    {
        $requestData  = $_REQUEST;
        $farmer_id    = $this->session->agrishop_login_farmer_id;

        // Status comes from DataTables custom data field
        $status = $status ?? (isset($requestData['search']['status']) ? $requestData['search']['status'] : 'ACTIVE');

        $FILTER_STATUS = $status == 'COMPLETED'
            ? "(t2.status = 'COMPLETED')"
            : ($status == 'CANCELLED'
                ? "(t2.status = 'CANCELLED')"
                : "(t2.status != 'COMPLETED' AND t2.status != 'PENDING' AND t2.status != 'CANCELLED')");

        $searchValue = $searchValue ?: (isset($requestData['search']['value']) ? $requestData['search']['value'] : '');

        list($limit, $offset) = $this->calculatePagination($requestData);

        $countQuery = $this->db->query("SELECT count(1) AS total FROM transaction t1
                            JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
                            LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                            LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                            WHERE t4.farmer_id = $farmer_id
                            AND $FILTER_STATUS
                            AND CONCAT(t4.farm_name, t2.status, COALESCE(t3.payable,'')) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'");

        $totalRecords = $countQuery->row()->total;

        $query = $this->db->query("SELECT t1.id AS transaction_id, t1.transaction_date, t1.person_id,
                                        t5.barangay_id, t5.contact_num, t5.img_path,
                                        DATE_FORMAT(t1.transaction_date,'%m/%d/%y') AS date_,
                                        t4.farm_name, t2.status, t2.created_at, t3.payable,
                                        t6.img AS gcash
                                   FROM transaction t1
                                   JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
                                   LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                                   LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                                   LEFT JOIN person t5 ON t1.person_id = t5.id
                                   LEFT JOIN transaction_proof_of_payment t6 ON t1.id = t6.transaction_id
                                   WHERE t4.farmer_id = $farmer_id
                                   AND $FILTER_STATUS
                                   AND CONCAT(t4.farm_name, t2.status, COALESCE(t3.payable,'')) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
                                   ORDER BY t2.status='RESERVED' DESC, t2.created_at DESC
                                   LIMIT $limit OFFSET $offset");

        $data = [];
        foreach ($query->result() as $value) {
            $total       = $value->payable + ($value->payable * 0.01);
            $img         = $value->img_path ? base_url($value->img_path) : base_url('dist/img/media/icons/1x1.png');
            $image_path  = "<img src='$img' width='70' height='70' class='rounded' style='object-fit:cover;'>";
            $status_badge    = $this->statusBadge($value->status);
            $delivery_status = $this->checkTransactionDeliveryStatus($value->transaction_id);
            $payment_status  = $this->checkTransactionPaymentStatus($value->transaction_id);

            $gcash_btn = $value->gcash != null
                ? "<span class='badge badge-light ml-1' style='cursor:pointer;'
                    onclick=\"viewGcashAttachment('" . base_url($value->gcash) . "')\">
                    <img src='" . base_url('dist/img/credit/gcash_50x50.png') . "' alt='GCash' style='width:18px;height:18px;'>
                   </span>"
                : '';

            $action_btns = ($value->status != 'COMPLETED' && $value->status != 'CANCELLED')
                ? "<div class='d-flex align-items-center' style='gap:8px;'>
                        <span class='badge badge-light' style='cursor:pointer;'
                            onclick=\"updateModalStatus({$value->transaction_id},'{$delivery_status}','DELIVERY')\">
                            <i class='fa fa-truck'></i> " . $this->statusBadge($delivery_status) . "
                        </span>
                        <span class='badge badge-light' style='cursor:pointer;'
                            onclick=\"updateModalStatus({$value->transaction_id},'{$payment_status}','PAYMENT')\">
                            <i class='fa fa-money-bill'></i> " . $this->statusBadge($payment_status) . "
                        </span>
                        $gcash_btn
                   </div>"
                : '';

            $data[] = [
                '<div class="d-flex align-items-start p-2" style="gap:10px;width:100%;line-height:1.15">
                    <div>' . $image_path . '</div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div style="font-size:13px;color:#777;">' . $value->transaction_date . '</div>
                                <div style="font-size:18px;font-weight:600;color:#000;">' . $this->getPersonName($value->person_id) . '</div>
                                <div style="font-size:14px;color:#555;">' . $this->getAddress($value->barangay_id) . '</div>
                                <div style="font-size:15px;font-weight:600;color:#333;">' . $value->contact_num . '</div>
                            </div>
                            <span class="badge badge-success" style="font-size:15px;">
                                &#8369; ' . $this->format_price($total) . '
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div>
                                <span class="badge badge-light" title="View Order Items"
                                    style="cursor:pointer;font-size:13px;"
                                    onclick="viewTransactionDetails(' . $value->transaction_id . ')">
                                    <i class="fa fa-eye"></i> ' . $status_badge . '
                                </span>
                            </div>
                            ' . $action_btns . '
                        </div>
                    </div>
                </div>
                <hr style="margin:4px 0;">'
            ];
        }

        echo json_encode([
            'draw'            => intval($requestData['draw']),
            'recordsTotal'    => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords),
            'data'            => $data,
        ]);
    }

    // ── Order item detail ──────────────────────────────────────
    public function getCartDetails()
    {
        $requestData    = $_REQUEST;
        // FIX: properly read transaction_id from search params passed by getTable()
        $transaction_id = isset($requestData['search']['transaction_id'])
            ? (int) $requestData['search']['transaction_id']
            : 0;

        if (!$transaction_id) {
            echo json_encode([
                'draw'            => intval($requestData['draw'] ?? 1),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [['<div class="text-center text-muted p-3"><i class="fa fa-info-circle mr-1"></i> No order selected.</div>']],
            ]);
            return;
        }

        list($limit, $offset) = $this->calculatePagination($requestData);

        $query = $this->db->query("SELECT t1.id AS cart_id, t1.transaction_id, t1.qty,
                                        t4.price, t4.price_wholesale, t2.uom, t3.name AS produce_name,
                                        t3.img_path, t1.created_at, t1.is_wholesale,
                                        t5.status AS t_status, t6.status AS t_p_status, t7.status AS t_d_status
                                   FROM my_cart_farm_produce t1
                                   LEFT JOIN farm_produce t2 ON t1.farm_produce_id = t2.id
                                   LEFT JOIN produce t3 ON t2.produce_id = t3.id
                                   LEFT JOIN (SELECT * FROM transaction_status WHERE transaction_id = $transaction_id AND is_latest IS TRUE) t5 ON t1.transaction_id = t5.transaction_id
                                   LEFT JOIN (SELECT * FROM transaction_payment_status WHERE transaction_id = $transaction_id AND is_latest IS TRUE) t6 ON t1.transaction_id = t6.transaction_id
                                   LEFT JOIN (SELECT * FROM transaction_delivery_status WHERE transaction_id = $transaction_id AND is_latest IS TRUE) t7 ON t1.transaction_id = t7.transaction_id
                                   LEFT JOIN (SELECT * FROM price_monitoring_farm_produce WHERE is_latest IS TRUE) t4 ON t1.price_id_during_transact = t4.id
                                   WHERE t1.transaction_id = $transaction_id
                                   ORDER BY t1.id DESC
                                   LIMIT $limit OFFSET $offset");

        if ($query->num_rows() == 0) {
            echo json_encode([
                'draw'            => intval($requestData['draw'] ?? 1),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [['<div class="text-center text-muted p-3"><i class="fa fa-box-open mr-1"></i> No items found for this order.</div>']],
            ]);
            return;
        }

        $rows       = $query->result();
        $q_status   = $rows[0]->t_status   ?? '';
        $q_p_status = $rows[0]->t_p_status ?? '';
        $q_d_status = $rows[0]->t_d_status ?? '';

        $data     = [];
        $subtotal = 0;

        foreach ($rows as $value) {
            $pricing  = $value->is_wholesale == 't' ? $value->price_wholesale : $value->price;
            $price    = $pricing * $value->qty;
            $subtotal += $price;

            $img = $value->img_path
                ? base_url($value->img_path)
                : base_url('dist/img/media/icons/1x1.png');

            $wholesale_badge = $value->is_wholesale == 't'
                ? ' <span class="badge badge-secondary" style="font-size:10px;">wholesale</span>'
                : '';

            $data[] = [
                '<div class="d-flex align-items-start p-2" style="gap:10px;width:100%">
                    <img src="' . $img . '" width="56" height="56" class="rounded shadow-sm" style="object-fit:cover">
                    <div class="flex-grow-1" style="line-height:1.15">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div style="font-size:14px;font-weight:600;color:#222;margin-bottom:2px;">
                                    ' . $value->produce_name . '
                                </div>
                                <div class="text-muted" style="font-size:12px;">
                                    ' . $value->qty . ' ' . $value->uom . ' &times; &#8369; ' . $this->format_price($pricing) . $wholesale_badge . '
                                </div>
                            </div>
                        </div>
                        <div style="margin-top:4px;">
                            <span class="badge badge-success px-2 py-1" style="font-size:12px;">
                                &#8369; ' . $this->format_price($price) . '
                            </span>
                        </div>
                    </div>
                </div>
                <hr style="margin:4px 0;">'
            ];
        }

        // Summary row
        $convenience_fee = $subtotal * 0.01;
        $total_payment   = $subtotal + $convenience_fee;

        $data[] = [
            '<div class="p-2" style="width:100%;font-size:13px;line-height:1.2">
                <div class="mb-2">
                    <div style="font-weight:600;margin-bottom:2px;">Delivery Status</div>
                    ' . $this->statusBadge($q_d_status) . '
                </div>
                <div class="mb-2">
                    <div style="font-weight:600;margin-bottom:2px;">Payment Status</div>
                    ' . $this->statusBadge($q_p_status) . '
                </div>
                <hr style="margin:6px 0;">
                <div class="d-flex justify-content-between">
                    <span>Subtotal</span>
                    <span>&#8369; ' . $this->format_price($subtotal) . '</span>
                </div>
                <div class="d-flex justify-content-between text-muted">
                    <span>Convenience Fee (1%)</span>
                    <span>&#8369; ' . $this->format_price($convenience_fee) . '</span>
                </div>
                <hr style="margin:6px 0;">
                <div class="d-flex justify-content-between align-items-center p-2 rounded"
                    style="background:#e9f7ef;font-size:15px;">
                    <span style="font-weight:700;">Total</span>
                    <span style="font-weight:bold;">&#8369; ' . $this->format_price($total_payment) . '</span>
                </div>
            </div>'
        ];

        // Action buttons based on status
        if ($q_status == 'RESERVED') {
            $data[] = [
                '<button class="btn btn-primary btn-block" style="font-size:16px;"
                    onclick="acceptAndPrepare(' . $transaction_id . ', \'PREPARING\')">
                    <i class="fa fa-check mr-1"></i> Accept and Prepare
                </button>'
            ];
        }
        if ($q_status == 'PREPARING') {
            $data[] = [
                '<button class="btn btn-info btn-block" style="font-size:16px;"
                    onclick="acceptAndPrepare(' . $transaction_id . ', \'ORDER_IS_READY\')">
                    <i class="fa fa-check mr-1"></i> Order is Ready
                </button>'
            ];
        }
        if ($q_status == 'ORDER_IS_READY') {
            $data[] = [
                '<button class="btn btn-success btn-block" style="font-size:16px;"
                    onclick="acceptAndPrepare(' . $transaction_id . ', \'COMPLETED\')">
                    <i class="fa fa-check mr-1"></i> Complete Order
                </button>'
            ];
        }

        echo json_encode([
            'draw'            => intval($requestData['draw'] ?? 1),
            'recordsTotal'    => 10000,
            'recordsFiltered' => 10000,
            'data'            => $data,
        ]);
    }

    public function updateStatus()
    {
        $this->db->trans_begin();
        $true  = ["success" => true];
        $false = ["success" => false];

        $person_id      = $this->session->agrishop_person_id;
        $transaction_id = $this->input->post('trans_id');
        $status         = $this->input->post('status');
        $type           = $this->input->post('type');

        $table = $type == 'payment'   ? 'transaction_payment_status'
               : ($type == 'delivery' ? 'transaction_delivery_status' : '');

        if (!$table) {
            echo json_encode(["success" => false, "message" => "Invalid type."]);
            return;
        }

        $data_status = [
            'transaction_id'       => $transaction_id,
            'status'               => $status,
            'created_by_person_id' => $person_id,
        ];

        if ($this->update_transaction_status($data_status, $table)) {
            $true  += ["message" => "Successfully updated!"];
            $ret    = $true;
        } else {
            $false += ["message" => "Failed to update!"];
            $ret    = $false;
        }

        $this->db->trans_status() === false
            ? $this->db->trans_rollback()
            : $this->db->trans_commit();

        echo json_encode($ret);
    }
}
