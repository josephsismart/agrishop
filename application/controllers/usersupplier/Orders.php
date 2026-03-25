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
            "current_location"  => "Orders",
            "content"           => [$this->load->view('interface/' . $uri . '/Orders', [
                "billing" => $this->supplier_billing_count(),
            ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }

    // ── Supplier billing count ────────────────────────────────
    public function supplier_billing_count()
    {
        $supplier_id = (int) $this->session->agrishop_login_supplier_id;
        if (!$supplier_id) return ["count" => 0];

        $result = $this->db->query("
            SELECT COUNT(1) AS count
            FROM supplier_invoice_billing
            WHERE supplier_id = ? AND is_paid = false
        ", [$supplier_id]);

        if (!$result) return ["count" => 0];
        $row = $result->row();
        return ["count" => (int) ($row->count ?? 0)];
    }

    // ── Datatable: orders list ────────────────────────────────
    // ── Count of RESERVED (new) orders for this supplier ─────────
    public function getNewOrderCount()
    {
        $supplier_id = (int) $this->session->agrishop_login_supplier_id;
        if (!$supplier_id) { echo json_encode(['count' => 0]); return; }

        $stx = "SELECT DISTINCT mcs.transaction_id
                 FROM my_cart_supply mcs
                 JOIN supplier_supply ss ON mcs.supplier_supply_id = ss.id
                 WHERE ss.supplier_id = $supplier_id";

        $result = $this->db->query("
            SELECT COUNT(1) AS count
            FROM transaction t
            JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) ts ON t.id = ts.transaction_id
            JOIN ($stx) stx ON t.id = stx.transaction_id
            WHERE ts.status = 'RESERVED'
        ");

        $count = ($result && $result->row()) ? (int) $result->row()->count : 0;
        echo json_encode(['count' => $count]);
    }

    public function getOrderList()
    {
        $requestData = $_REQUEST;
        $supplier_id = (int) $this->session->agrishop_login_supplier_id;
        $status      = $requestData['search']['status'] ?? $this->input->post('status') ?? 'ACTIVE';
        $searchValue = $this->db->escape_like_str(
            isset($requestData['search']['value']) ? $requestData['search']['value'] : ''
        );

        // Guard: must have supplier_id
        if (!$supplier_id) {
            echo json_encode(['draw' => intval($requestData['draw'] ?? 1), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
            return;
        }

        $FILTER_STATUS = $status == 'COMPLETED'
            ? "(ts.status = 'COMPLETED')"
            : ($status == 'CANCELLED'
                ? "(ts.status = 'CANCELLED')"
                : "(ts.status != 'COMPLETED' AND ts.status != 'PENDING' AND ts.status != 'CANCELLED')");

        list($limit, $offset) = $this->calculatePagination($requestData);

        // Subquery: get transaction_ids belonging to this supplier
        $stx = "SELECT DISTINCT mcs.transaction_id
                FROM my_cart_supply mcs
                JOIN supplier_supply ss ON mcs.supplier_supply_id = ss.id
                WHERE ss.supplier_id = $supplier_id";

        $cartSub = "SELECT transaction_id, SUM(sub_total) AS payable
                    FROM my_cart_supply GROUP BY transaction_id";

        $countResult = $this->db->query("
            SELECT COUNT(1) AS total
            FROM transaction t
            JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) ts ON t.id = ts.transaction_id
            JOIN ($stx) stx ON t.id = stx.transaction_id
            LEFT JOIN ($cartSub) cart ON t.id = cart.transaction_id
            WHERE $FILTER_STATUS
              AND CONCAT(ts.status, COALESCE(cart.payable,''))
                  COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
        ");

        $totalRecords = ($countResult && $countResult->num_rows() > 0)
            ? (int) $countResult->row()->total : 0;

        $query = $this->db->query("
            SELECT t.id AS transaction_id, t.transaction_date, t.person_id,
                   ts.status, ts.created_at AS status_updated_at,
                   cart.payable, p.img_path, p.contact_num, p.barangay_id,
                   pop.img AS gcash_img
            FROM transaction t
            JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) ts ON t.id = ts.transaction_id
            JOIN ($stx) stx ON t.id = stx.transaction_id
            LEFT JOIN ($cartSub) cart ON t.id = cart.transaction_id
            LEFT JOIN person p ON t.person_id = p.id
            LEFT JOIN transaction_proof_of_payment pop ON t.id = pop.transaction_id
            WHERE $FILTER_STATUS
              AND CONCAT(ts.status, COALESCE(cart.payable,''))
                  COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
            ORDER BY ts.status = 'RESERVED' DESC, ts.created_at DESC
            LIMIT $limit OFFSET $offset
        ");

        if (!$query) {
            echo json_encode(['draw' => intval($requestData['draw'] ?? 1), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
            return;
        }

        $data = [];
        foreach ($query->result() as $value) {
            $total        = ($value->payable ?? 0) * 1.01;
            $img          = $value->img_path
                ? "<img src='" . base_url($value->img_path) . "' width='70' height='70' class='rounded'>"
                : "<i class='fas fa-user-circle fa-3x text-muted'></i>";
            $status_badge    = $this->statusBadge($value->status);
            $delivery_status = $this->checkTransactionDeliveryStatus($value->transaction_id);
            $payment_status  = $this->checkTransactionPaymentStatus($value->transaction_id);

            $gcash_btn = $value->gcash_img
                ? "<span class='badge bg-white ml-1' style='cursor:pointer;'
                    onclick=\"viewGcashAttachment('" . base_url($value->gcash_img) . "')\">
                    <img src='" . base_url('dist/img/credit/gcash_50x50.png') . "' style='width:18px;height:18px;'>
                   </span>"
                : '';

            $action_btns = ($value->status != 'COMPLETED' && $value->status != 'CANCELLED')
                ? "<span class='badge bg-white' style='cursor:pointer;'
                    onclick=\"updateModalStatus({$value->transaction_id},'{$delivery_status}','DELIVERY')\">
                    <i class='fa fa-truck'></i> " . $this->statusBadge($delivery_status) . "
                   </span>
                   <span class='badge bg-white ml-1' style='cursor:pointer;'
                    onclick=\"updateModalStatus({$value->transaction_id},'{$payment_status}','PAYMENT')\">
                    <i class='fa fa-money-bill'></i> " . $this->statusBadge($payment_status) . "
                   </span>$gcash_btn"
                : '';

            $person_name = $this->getPersonName($value->person_id);
            $address     = $this->getAddress($value->barangay_id);
            $total_fmt   = $this->format_price($total);
            $tid         = $value->transaction_id;

            $data[] = [
                "<div class='d-flex align-items-start p-2' style='gap:10px;width:100%;line-height:1.15'>
                    <div>$img</div>
                    <div class='flex-grow-1'>
                        <div class='d-flex justify-content-between align-items-start'>
                            <div>
                                <div style='font-size:13px;color:#777;'>{$value->transaction_date}</div>
                                <div style='font-size:18px;font-weight:600;color:#000;'>$person_name</div>
                                <div style='font-size:14px;color:#000;'>$address</div>
                                <div style='font-size:16px;font-weight:600;color:#333;'>{$value->contact_num}</div>
                            </div>
                            <span class='badge bg-success' style='font-size:16px;'>₱ $total_fmt</span>
                        </div>
                        <div class='d-flex justify-content-between align-items-center mt-2'>
                            <div>
                                <span class='badge bg-white' style='cursor:pointer;'
                                    onclick='viewOrderDetails($tid)'>
                                    <i class='fa fa-eye'></i> $status_badge
                                </span>
                            </div>
                            <div class='d-flex align-items-center' style='gap:6px;'>$action_btns</div>
                        </div>
                    </div>
                </div>
                <hr style='margin:4px 0;'>"
            ];
        }

        echo json_encode([
            'draw'            => intval($requestData['draw']),
            'recordsTotal'    => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords),
            'data'            => $data,
        ]);
    }

    // ── Order detail items ────────────────────────────────────
    public function getOrderDetails()
    {
        $requestData    = $_REQUEST;
        $transaction_id = (int) ($requestData['search']['transaction_id'] ?? $this->input->post('transaction_id'));

        if (!$transaction_id) {
            echo json_encode(['draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
            return;
        }

        list($limit, $offset) = $this->calculatePagination($requestData);

        $query = $this->db->query("
            SELECT mcs.id AS cart_id, mcs.qty, mcs.sub_total, mcs.price_during_transact,
                   ss.name AS supply_name, ss.uom, ss.img_path,
                   ts.status  AS t_status,
                   tps.status AS t_p_status,
                   tds.status AS t_d_status
            FROM my_cart_supply mcs
            JOIN supplier_supply ss ON mcs.supplier_supply_id = ss.id
            LEFT JOIN (SELECT * FROM transaction_status         WHERE transaction_id = ? AND is_latest IS TRUE) ts  ON mcs.transaction_id = ts.transaction_id
            LEFT JOIN (SELECT * FROM transaction_payment_status WHERE transaction_id = ? AND is_latest IS TRUE) tps ON mcs.transaction_id = tps.transaction_id
            LEFT JOIN (SELECT * FROM transaction_delivery_status WHERE transaction_id = ? AND is_latest IS TRUE) tds ON mcs.transaction_id = tds.transaction_id
            WHERE mcs.transaction_id = ?
            ORDER BY mcs.id DESC
            LIMIT $limit OFFSET $offset
        ", [$transaction_id, $transaction_id, $transaction_id, $transaction_id]);

        if (!$query || $query->num_rows() == 0) {
            echo json_encode(['draw' => intval($requestData['draw'] ?? 1), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
            return;
        }

        // Read statuses from first row only (avoid double ->row() cursor issue)
        $rows       = $query->result();
        $first      = $rows[0];
        $q_status   = $first->t_status   ?? '';
        $q_p_status = $first->t_p_status ?? '';
        $q_d_status = $first->t_d_status ?? '';

        $data     = [];
        $subtotal = 0;

        foreach ($rows as $value) {
            $price     = $value->price_during_transact * $value->qty;
            $subtotal += $price;
            $img       = $value->img_path
                ? base_url($value->img_path)
                : base_url('dist/img/media/icons/1x1.png');

            $data[] = [
                "<div class='d-flex align-items-start p-2' style='gap:10px;width:100%'>
                    <img src='$img' width='56' height='56' class='rounded shadow-sm' style='object-fit:cover'>
                    <div class='flex-grow-1' style='line-height:1.15'>
                        <div style='font-size:14px;font-weight:600;color:#222;margin-bottom:2px;'>{$value->supply_name}</div>
                        <div class='text-muted' style='font-size:12px;'>
                            {$value->qty} {$value->uom} × ₱ " . $this->format_price($value->price_during_transact) . "
                        </div>
                        <div style='margin-top:4px;'>
                            <span class='badge bg-success px-2 py-1' style='font-size:12px;'>
                                ₱ " . $this->format_price($price) . "
                            </span>
                        </div>
                    </div>
                </div>
                <hr style='margin:4px 0;'>"
            ];
        }

        $convenience_fee = $subtotal * 0.01;
        $total_payment   = $subtotal + $convenience_fee;

        $data[] = [
            "<div class='p-2' style='width:100%;font-size:13px;line-height:1.2'>
                <div><div style='font-weight:600;margin-bottom:2px;'>Delivery</div>" . $this->statusBadge($q_d_status) . "</div>
                <div><div style='font-weight:600;margin-bottom:2px;'>Payment</div>" . $this->statusBadge($q_p_status) . "</div>
                <hr style='margin:6px 0;'>
                <div class='d-flex justify-content-between'><span>Subtotal</span><span>₱ " . $this->format_price($subtotal) . "</span></div>
                <div class='d-flex justify-content-between text-muted'><span>Fee (1%)</span><span>₱ " . $this->format_price($convenience_fee) . "</span></div>
                <hr style='margin:6px 0;'>
                <div class='d-flex justify-content-between align-items-center p-2 rounded' style='background:#e9f7ef;font-size:15px;'>
                    <span style='font-weight:700;'>Total</span>
                    <span style='font-weight:bold;'>₱ " . $this->format_price($total_payment) . "</span>
                </div>
            </div>"
        ];

        if ($q_status == 'RESERVED') {
            $data[] = ["<button class='btn bg-primary text-white btn-block w-100' style='font-size:18px;'
                onclick=\"acceptAndPrepare($transaction_id,'PREPARING')\">
                <i class='fa fa-check'></i> Accept and Prepare</button>"];
        }
        if ($q_status == 'PREPARING') {
            $data[] = ["<button class='btn bg-info text-white btn-block w-100' style='font-size:18px;'
                onclick=\"acceptAndPrepare($transaction_id,'ORDER_IS_READY')\">
                <i class='fa fa-check'></i> Order is Ready</button>"];
        }
        if ($q_status == 'ORDER_IS_READY') {
            $data[] = ["<button class='btn bg-success text-white btn-block w-100' style='font-size:18px;'
                onclick=\"acceptAndPrepare($transaction_id,'COMPLETED')\">
                <i class='fa fa-check'></i> Complete Order</button>"];
        }

        echo json_encode([
            'draw'            => intval($requestData['draw'] ?? 1),
            'recordsTotal'    => 10000,
            'recordsFiltered' => 10000,
            'data'            => $data,
        ]);
    }

    // ── Accept / progress order status ───────────────────────
    public function acceptAndPrepare()
    {
        $this->db->trans_begin();
        $true  = ["success" => true];
        $false = ["success" => false];

        $person_id      = $this->session->agrishop_person_id;
        $transaction_id = $this->input->post('trans_id');
        $status         = $this->input->post('status');
        $text           = $status == 'PREPARING'       ? 'Order accepted'
                        : ($status == 'ORDER_IS_READY'  ? 'Order is ready'
                        : ($status == 'COMPLETED'       ? 'Successfully completed!' : 'Updated'));

        $data_status = [
            'transaction_id'        => $transaction_id,
            'status'                => $status,
            'created_by_person_id'  => $person_id,
        ];

        if ($status == 'COMPLETED') {
            $this->transaction_payment_status(['transaction_id' => $transaction_id, 'status' => 'PAID', 'created_by_person_id' => $person_id]);
            $this->transaction_delivery_status(['transaction_id' => $transaction_id, 'status' => 'DELIVERED', 'created_by_person_id' => $person_id]);
            $this->db->update("transaction", ['is_done' => true, 'done_at' => $this->now()], ['id' => $transaction_id]);
        }

        if ($this->transaction_status($data_status)) {
            $true  += ["message" => $text];
            $ret    = $true;
        } else {
            $false += ["message" => "Failed to update status!"];
            $ret    = $false;
        }

        $this->db->trans_status() === false
            ? $this->db->trans_rollback()
            : $this->db->trans_commit();

        echo json_encode($ret);
    }

    // ── Update delivery / payment status ─────────────────────
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

/* End of file Orders.php */
/* Location: ./application/controllers/usersupplier/Orders.php */