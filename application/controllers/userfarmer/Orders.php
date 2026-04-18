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
        $eta_input      = $this->input->post('estimated_delivery'); // optional datetime string
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

            // Save ETA if provided
            if ($eta_input && $status === 'PREPARING') {
                $this->db->update('transaction',
                    ['estimated_delivery' => date('Y-m-d H:i:s', strtotime($eta_input))],
                    ['id' => $transaction_id]
                );
            }

            // Notify the customer on every meaningful status transition
            $buyer = $this->db->query(
                "SELECT p.id AS person_id FROM transaction t
                 JOIN person p ON t.person_id = p.id
                 WHERE t.id = ? LIMIT 1",
                [$transaction_id]
            )->row();
            $farm_info = $this->db->query(
                "SELECT ff.farm_name FROM transaction t
                 JOIN farmer_farm ff ON t.farm_id = ff.id
                 WHERE t.id = ? LIMIT 1",
                [$transaction_id]
            )->row();
            $farm_name = $farm_info ? $farm_info->farm_name : 'the farm';

            if ($buyer) {
                $notifs = [
                    'PREPARING'      => ['Order is Being Prepared',   "Your order from {$farm_name} is now being prepared."],
                    'ORDER_IS_READY' => ['Order Ready for Pickup!',    "Your order from {$farm_name} is ready for pickup/delivery."],
                    'COMPLETED'      => ['Order Completed!',           "Your order from {$farm_name} has been completed. Thank you!"],
                    'CANCELLED'      => ['Order Cancelled',            "Your order from {$farm_name} has been cancelled."],
                ];
                if (isset($notifs[$status])) {
                    $this->notify(
                        $buyer->person_id,
                        $notifs[$status][0],
                        $notifs[$status][1],
                        $status === 'CANCELLED' ? 'DANGER' : 'SUCCESS',
                        'transaction',
                        $transaction_id
                    );
                }
            }
        } else {
            $false += ["message" => "Failed to update order!"];
            $ret    = $false;
        }

        $this->db->trans_status() === false
            ? $this->db->trans_rollback()
            : $this->db->trans_commit();

        echo json_encode($ret);
    }

    // ── Dashboard: 6 most recent RESERVED orders (clean flat JSON) ──
    public function getDashboardOrders()
    {
        $farmer_id = (int) $this->session->agrishop_login_farmer_id;
        if (!$farmer_id) { echo json_encode([]); return; }

        $rows = $this->db->query("
            SELECT
                t.id            AS transaction_id,
                t.transaction_date,
                t.person_id,
                p.img_path      AS customer_img,
                p.contact_num,
                ff.farm_name,
                ts.status,
                ts.created_at   AS status_date,
                COALESCE(SUM(mc.sub_total), 0) * 1.01 AS total
            FROM transaction t
            JOIN (SELECT * FROM transaction_status WHERE is_latest = 1) ts
                ON t.id = ts.transaction_id
            JOIN farmer_farm ff ON t.farm_id = ff.id
            LEFT JOIN person p   ON t.person_id = p.id
            LEFT JOIN my_cart_farm_produce mc ON t.id = mc.transaction_id
            WHERE ff.farmer_id = $farmer_id
              AND ts.status NOT IN ('COMPLETED','CANCELLED','PENDING')
            GROUP BY t.id, t.transaction_date, t.person_id,
                     p.img_path, p.contact_num, ff.farm_name,
                     ts.status, ts.created_at
            ORDER BY (ts.status = 'RESERVED') DESC, ts.created_at DESC
            LIMIT 6
        ")->result();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id'          => $r->transaction_id,
                'date'        => date('M d, Y', strtotime($r->transaction_date)),
                'customer'    => $this->getPersonName($r->person_id),
                'avatar'      => $r->customer_img
                                    ? base_url($r->customer_img)
                                    : base_url('dist/img/media/icons/1x1.png'),
                'farm'        => $r->farm_name,
                'amount'      => number_format((float)$r->total, 2),
                'status'      => $r->status,
                'is_new'      => ($r->status === 'RESERVED'),
            ];
        }
        echo json_encode($out);
    }

    // ── Count of RESERVED (new) orders for this farmer ──────────
    public function getNewOrderCount()
    {
        $farmer_id = (int) $this->session->agrishop_login_farmer_id;
        if (!$farmer_id) { echo json_encode(['count' => 0]); return; }

        $result = $this->db->query("
            SELECT COUNT(1) AS count
            FROM transaction t
            JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) ts ON t.id = ts.transaction_id
            JOIN farmer_farm ff ON t.farm_id = ff.id
            WHERE ff.farmer_id = $farmer_id
              AND ts.status = 'RESERVED'
        ");

        $count = ($result && $result->row()) ? (int) $result->row()->count : 0;
        echo json_encode(['count' => $count]);
    }

    // ── Main listing (handles ACTIVE / COMPLETED / CANCELLED) ─
    public function getCartListing($status = null, $searchValue = null)
    {
        $requestData  = $_REQUEST;
        $farmer_id    = $this->session->agrishop_login_farmer_id;

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
                            JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                            LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                            WHERE t4.farmer_id = $farmer_id
                            AND $FILTER_STATUS
                            AND CONCAT(t4.farm_name, t2.status, COALESCE(t3.payable,'')) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'");

        $totalRecords = $countQuery->row()->total;

        $query = $this->db->query("SELECT t1.id AS transaction_id, t1.transaction_date, t1.person_id,
                                        t5.barangay_id, t5.contact_num, t5.img_path,
                                        t4.farm_name, t2.status, t2.created_at, t3.payable,
                                        t6.img AS gcash,
                                        td.total_payment AS td_total,
                                        td.delivery_fee   AS td_delivery_fee,
                                        td.to_admin       AS td_service_fee,
                                        td.delivery_method AS td_delivery_method
                                   FROM transaction t1
                                   JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
                                   JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                                   LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                                   LEFT JOIN person t5 ON t1.person_id = t5.id
                                   LEFT JOIN transaction_proof_of_payment t6 ON t1.id = t6.transaction_id
                                   LEFT JOIN transaction_details td ON t1.id = td.transaction_id
                                   WHERE t4.farmer_id = $farmer_id
                                   AND $FILTER_STATUS
                                   AND CONCAT(t4.farm_name, t2.status, COALESCE(t3.payable,'')) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
                                   ORDER BY t2.status='RESERVED' DESC, t2.created_at DESC
                                   LIMIT $limit OFFSET $offset");

        $data = [];
        foreach ($query->result() as $value) {
            // Use actual total_payment from transaction_details (includes delivery fee)
            // Fall back to cart subtotal * 1.01 for older orders without transaction_details
            if (!empty($value->td_total) && $value->td_total > 0) {
                $total = (float)$value->td_total;
            } else {
                $total = ($value->payable ?? 0) * 1.01;
            }
            $total_fmt       = $this->format_price($total);
            $person_name     = $this->getPersonName($value->person_id);
            $address         = $this->getAddress($value->barangay_id);
            $tid             = $value->transaction_id;
            $st              = $value->status;
            $delivery_status = $this->checkTransactionDeliveryStatus($tid);
            $payment_status  = $this->checkTransactionPaymentStatus($tid);

            $img = $value->img_path
                ? base_url($value->img_path)
                : base_url('dist/img/media/icons/1x1.png');

            $new_badge = ($st === 'RESERVED')
                ? ' <span class="badge-new">NEW</span>'
                : '';

            $amt_class = ($st === 'CANCELLED') ? 'ocard-amount amt-cancelled' : 'ocard-amount';

            $pill_order    = "<span class='mini-pill pill-{$st}'>" . str_replace('_', ' ', $st) . "</span>";
            $pill_delivery = $delivery_status
                ? "<span class='mini-pill pill-{$delivery_status}'>" . str_replace('_', ' ', $delivery_status) . "</span>"
                : '';
            $pill_payment  = $payment_status
                ? "<span class='mini-pill pill-{$payment_status}'>" . str_replace('_', ' ', $payment_status) . "</span>"
                : '';

            if ($value->gcash) {
                $gcash_btn = "<button class='btn-gcash' onclick=\"viewGcashAttachment('" . base_url($value->gcash) . "')\"
                    title='View GCash receipt' style='position:relative;'>
                    <img src='" . base_url('dist/img/credit/gcash_50x50.png') . "' style='width:22px;height:22px;'>
                   </button>";
            } else {
                $gcash_btn = "<button class='btn-gcash' onclick=\"viewGcashAttachment('')\"
                    title='No GCash receipt uploaded' style='opacity:.45;'>
                    <img src='" . base_url('dist/img/credit/gcash_50x50.png') . "' style='width:22px;height:22px;'>
                   </button>";
            }

            $update_btn = (!in_array($st, ['COMPLETED', 'CANCELLED']))
                ? "<button class='btn-update-status'
                    onclick=\"openUpdateStatus({$tid},'{$st}','{$delivery_status}','{$payment_status}')\">
                    <i class='fa fa-edit'></i> Update Status
                   </button>"
                : '';

            // ── Fetch inline produce items ───────────────────────────
            $items_query = $this->db->query("
                SELECT
                    pr.id               AS produce_id,
                    pr.name             AS produce_name,
                    pr.img_path         AS produce_img,
                    mc.qty,
                    fp.uom,
                    mc.is_wholesale,
                    COALESCE(CASE WHEN mc.is_wholesale THEN pm.price_wholesale ELSE pm.price END, 0) AS unit_price
                FROM my_cart_farm_produce mc
                LEFT JOIN farm_produce fp ON mc.farm_produce_id = fp.id
                LEFT JOIN produce pr      ON fp.produce_id = pr.id
                LEFT JOIN (SELECT * FROM price_monitoring_farm_produce WHERE is_latest IS TRUE) pm
                          ON mc.price_id_during_transact = pm.id
                WHERE mc.transaction_id = $tid
                ORDER BY mc.id ASC
            ");

            $items_html = '';
            $item_count = 0;
            if ($items_query && $items_query->num_rows() > 0) {
                $item_count = $items_query->num_rows();
                foreach ($items_query->result() as $item) {
                    $line  = (float)$item->unit_price * (float)$item->qty;
                    // Use same image endpoint as the map — emoji SVG fallback per produce name
                    $pimg  = $item->produce_id
                        ? base_url('image/produce/' . $item->produce_id)
                        : base_url('dist/img/media/icons/1x1.png');
                    $ws_badge = $item->is_wholesale
                        ? '<span class="oitem-ws-badge">wholesale</span>'
                        : '';
                    $items_html .= "
                    <div class='oitem-row'>
                        <img src='{$pimg}' class='oitem-img' alt='produce'>
                        <div class='oitem-info'>
                            <div class='oitem-name'>{$item->produce_name}{$ws_badge}</div>
                            <div class='oitem-qty'>{$item->qty} {$item->uom} &times; &#8369;" . $this->format_price($item->unit_price) . "</div>
                        </div>
                        <div class='oitem-price'>&#8369;" . $this->format_price($line) . "</div>
                    </div>";
                }
            }

            $td_delivery_fee    = (float)($value->td_delivery_fee    ?? 0);
            $td_delivery_method = $value->td_delivery_method ?? '';
            $svc_fee            = ($value->payable ?? 0) * 0.01;

            $delivery_row = ($td_delivery_fee > 0)
                ? "<div class='oitem-summary-row'><span>&#x1F69A; Delivery" . ($td_delivery_method ? " ({$td_delivery_method})" : '') . "</span><span>&#8369;" . $this->format_price($td_delivery_fee) . "</span></div>"
                : '';

            $item_label = $item_count . ' Item' . ($item_count !== 1 ? 's' : '');

            if ($item_count > 0) {
                $items_section = "
                <div class='oitem-toggle' onclick='toggleOrderItems(this)'>
                    <span><i class='fa fa-box-open' style='font-size:11px;'></i> {$item_label}</span>
                    <i class='fa fa-chevron-down oitem-chevron'></i>
                </div>
                <div class='oitem-list'>
                    {$items_html}
                    <div class='oitem-summary'>
                        <div class='oitem-summary-row'><span>Service Fee (1%)</span><span>&#8369;" . $this->format_price($svc_fee) . "</span></div>
                        {$delivery_row}
                        <div class='oitem-summary-row oitem-total'><span>Total</span><span>&#8369;{$total_fmt}</span></div>
                    </div>
                </div>";
            } else {
                // Items were not recorded in cart table — show total from transaction_details if available
                $td_total_disp = (float)($value->td_total ?? 0);
                $total_line = ($td_total_disp > 0)
                    ? "<span style='font-weight:700;color:#065f46;margin-left:6px;'>&#8369;" . $this->format_price($td_total_disp) . "</span>"
                    : '';
                $items_section = "<div class='oitem-empty'><i class='fa fa-exclamation-triangle mr-1' style='color:#f59e0b;'></i> Item details not available{$total_line}</div>";
            }

            $data[] = [
                "<div class='ocard status-{$st}'>
                    <div class='d-flex align-items-start' style='gap:12px;'>
                        <img src='{$img}' class='ocard-avatar' alt='Customer'>
                        <div class='flex-grow-1'>
                            <div class='d-flex justify-content-between align-items-start'>
                                <div>
                                    <div style='font-size:11px;color:#9ca3af;font-weight:600;letter-spacing:.02em;'>
                                        <i class='fa fa-calendar-alt' style='font-size:10px;'></i> {$value->transaction_date}
                                        &nbsp;<span style='opacity:.5;'>|</span>&nbsp;
                                        <span style='color:#374151;'>#{$tid}</span>
                                    </div>
                                    <div style='font-size:15px;font-weight:700;color:#111;line-height:1.2;margin:2px 0;'>
                                        {$person_name}{$new_badge}
                                    </div>
                                    <div style='font-size:12px;color:#6b7280;'>{$address}</div>
                                    <div style='font-size:12px;color:#374151;font-weight:600;margin-top:1px;'>
                                        <i class='fa fa-phone-alt' style='font-size:10px;'></i> {$value->contact_num}
                                    </div>
                                </div>
                                <span class='{$amt_class}'>&#8369;{$total_fmt}</span>
                            </div>
                            <div style='margin-top:6px;display:flex;flex-wrap:wrap;gap:4px;'>
                                {$pill_order}{$pill_delivery}{$pill_payment}
                            </div>
                            {$items_section}
                            <div class='ocard-actions'>
                                {$update_btn}
                                {$gcash_btn}
                            </div>
                        </div>
                    </div>
                </div>"
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
                                        t3.id AS produce_id, t3.img_path, t1.created_at, t1.is_wholesale,
                                        t5.status AS t_status, t6.status AS t_p_status, t7.status AS t_d_status,
                                        td.delivery_fee AS td_delivery_fee, td.delivery_method AS td_delivery_method,
                                        td.total_payment AS td_total
                                   FROM my_cart_farm_produce t1
                                   LEFT JOIN farm_produce t2 ON t1.farm_produce_id = t2.id
                                   LEFT JOIN produce t3 ON t2.produce_id = t3.id
                                   LEFT JOIN (SELECT * FROM transaction_status WHERE transaction_id = $transaction_id AND is_latest IS TRUE) t5 ON t1.transaction_id = t5.transaction_id
                                   LEFT JOIN (SELECT * FROM transaction_payment_status WHERE transaction_id = $transaction_id AND is_latest IS TRUE) t6 ON t1.transaction_id = t6.transaction_id
                                   LEFT JOIN (SELECT * FROM transaction_delivery_status WHERE transaction_id = $transaction_id AND is_latest IS TRUE) t7 ON t1.transaction_id = t7.transaction_id
                                   LEFT JOIN (SELECT * FROM price_monitoring_farm_produce WHERE is_latest IS TRUE) t4 ON t1.price_id_during_transact = t4.id
                                   LEFT JOIN transaction_details td ON t1.transaction_id = td.transaction_id
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
            $pricing  = $value->is_wholesale ? $value->price_wholesale : $value->price;
            $price    = $pricing * $value->qty;
            $subtotal += $price;

            $img = $value->produce_id
                ? base_url('image/produce/' . $value->produce_id)
                : base_url('dist/img/media/icons/1x1.png');

            $wholesale_badge = $value->is_wholesale
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

        // Summary row — use transaction_details if available for accurate totals
        $td_delivery_fee    = (float)($rows[0]->td_delivery_fee   ?? 0);
        $td_total           = (float)($rows[0]->td_total          ?? 0);
        $td_delivery_method = $rows[0]->td_delivery_method ?? '';

        $convenience_fee = $subtotal * 0.01;
        $total_payment   = ($td_total > 0) ? $td_total : ($subtotal + $convenience_fee + $td_delivery_fee);

        $delivery_fee_row = '';
        if ($td_delivery_fee > 0) {
            $delivery_fee_row = '
                <div class="d-flex justify-content-between text-muted">
                    <span>&#x1F69A; Delivery Fee</span>
                    <span>&#8369; ' . $this->format_price($td_delivery_fee) . '</span>
                </div>';
        }

        $delivery_method_badge = $td_delivery_method
            ? '<span class="badge badge-info" style="font-size:11px;text-transform:uppercase;">' . htmlspecialchars($td_delivery_method) . '</span>'
            : '';

        $data[] = [
            '<div class="p-2" style="width:100%;font-size:13px;line-height:1.2">
                <div class="mb-2">
                    <div style="font-weight:600;margin-bottom:2px;">Delivery Status</div>
                    ' . $this->statusBadge($q_d_status) . ' ' . $delivery_method_badge . '
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
                    <span>Service Fee (1%)</span>
                    <span>&#8369; ' . $this->format_price($convenience_fee) . '</span>
                </div>
                ' . $delivery_fee_row . '
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

    // ── Unified status updater ────────────────────────────────────
    public function updateOrderStatuses()
    {
        $this->db->trans_begin();
        $person_id      = $this->session->agrishop_person_id;
        $transaction_id = $this->input->post('trans_id');
        $order_status   = $this->input->post('order_status');
        $delivery       = $this->input->post('delivery_status');
        $payment        = $this->input->post('payment_status');

        $buyer     = $this->db->query("SELECT person_id FROM transaction WHERE id = ?", [$transaction_id])->row();
        $farm_name = '';
        $farm_row  = $this->db->query("SELECT ff.farm_name FROM transaction t JOIN farmer_farm ff ON t.farm_id = ff.id WHERE t.id = ?", [$transaction_id])->row();
        if ($farm_row) $farm_name = $farm_row->farm_name;

        // ── Order status ──────────────────────────────────────────
        if ($order_status) {
            if ($order_status == 'COMPLETED') {
                $this->transaction_payment_status(['transaction_id' => $transaction_id, 'status' => 'PAID',      'created_by_person_id' => $person_id]);
                $this->transaction_delivery_status(['transaction_id' => $transaction_id, 'status' => 'DELIVERED', 'created_by_person_id' => $person_id]);
                $this->db->update('transaction', ['is_done' => true, 'done_at' => $this->now()], ['id' => $transaction_id]);
            }
            $this->transaction_status(['transaction_id' => $transaction_id, 'status' => $order_status, 'created_by_person_id' => $person_id]);
            if ($buyer) {
                $notifs = [
                    'PREPARING'      => ['Order is Being Prepared',  "Your farm produce order is now being prepared by {$farm_name}."],
                    'ORDER_IS_READY' => ['Order Ready!',              "Your farm produce order is ready for pickup/delivery from {$farm_name}."],
                    'COMPLETED'      => ['Order Completed',           "Your farm produce order has been completed. Thank you!"],
                ];
                if (isset($notifs[$order_status])) {
                    $this->notify($buyer->person_id, $notifs[$order_status][0], $notifs[$order_status][1], 'SUCCESS', 'transaction', $transaction_id);
                }
            }
        }

        // ── Delivery status ───────────────────────────────────────
        if ($delivery) {
            $this->transaction_delivery_status(['transaction_id' => $transaction_id, 'status' => $delivery, 'created_by_person_id' => $person_id]);
            if ($buyer) {
                $this->notify($buyer->person_id, 'Delivery Status Updated', "Your order delivery status has been updated to: {$delivery}.", 'INFO', 'transaction', $transaction_id);
            }
        }

        // ── Payment status ────────────────────────────────────────
        if ($payment) {
            $this->transaction_payment_status(['transaction_id' => $transaction_id, 'status' => $payment, 'created_by_person_id' => $person_id]);
            if ($buyer) {
                $this->notify($buyer->person_id, 'Payment Status Updated', "Your order payment status has been updated to: {$payment}.", 'INFO', 'transaction', $transaction_id);
            }
        }

        $this->db->trans_status() === false
            ? $this->db->trans_rollback()
            : $this->db->trans_commit();

        echo json_encode(['success' => true, 'message' => 'Status updated successfully!']);
    }
}
