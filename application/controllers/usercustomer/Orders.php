<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Orders extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SQL_BIG_SELECTS=1');
        // Allow any logged-in user; usercustomer is accessible to level 1 (consumer) and above
        if (!$this->session->agrishop_login_id) {
            redirect(base_url('/'));
        }
    }

    public function index()
    {
        $page_data  = $this->system();
        $person_id  = $this->session->agrishop_person_id;

        // Count badges
        $counts = $this->_getCounts($person_id);

        $page_data += [
            'page_title'       => 'My Orders',
            'current_location' => 'my-orders',
            'counts'           => $counts,
        ];

        // Build full page layout (same pattern used by farmer/supplier controllers)
        $this->load->view('interface/system/layout/modals',   $page_data);
        $this->load->view('interface/usercustomer/Orders',    $page_data);
    }

    // ── AJAX: DataTables list ─────────────────────────────────
    public function getOrders()
    {
        ob_start(); // capture any stray PHP notices/warnings
        $person_id   = (int)$this->session->agrishop_person_id;
        if (!$person_id) {
            ob_end_clean();
            echo json_encode(['draw'=>1,'recordsTotal'=>0,'recordsFiltered'=>0,'data'=>[]]);
            return;
        }
        $requestData = $_REQUEST;
        $filter      = isset($requestData['filter']) ? $requestData['filter'] : 'ALL';
        $search      = isset($requestData['search']['value']) ? trim($requestData['search']['value']) : '';

        $start  = (int)($requestData['start']  ?? 0);
        $length = (int)($requestData['length'] ?? 10);

        // ── Build WHERE (no cart joins here — keeps one row per transaction) ──
        $where = "t.person_id = {$person_id}";

        if ($filter === 'PENDING') {
            $where .= " AND ts.status = 'PENDING'";
        } elseif ($filter === 'TO_RECEIVE') {
            $where .= " AND ts.status IN ('RESERVED','PREPARING','ORDER_IS_READY')";
        } elseif ($filter === 'COMPLETED') {
            $where .= " AND ts.status = 'COMPLETED'";
        } elseif ($filter === 'CANCELLED') {
            $where .= " AND ts.status = 'CANCELLED'";
        } elseif ($filter === 'TO_RATE') {
            $where .= " AND ts.status = 'COMPLETED' AND tr.id IS NULL";
        }

        if ($search !== '') {
            $s = $this->db->escape_like_str($search);
            $where .= " AND (t.id LIKE '%{$s}%' OR ff.farm_name LIKE '%{$s}%' OR ss.store_name LIKE '%{$s}%' OR CONCAT(fp.first_name,' ',fp.last_name) LIKE '%{$s}%')";
        }

        // Hide ghost PENDING transactions that have no items at all
        $where .= " AND NOT (ts.status = 'PENDING' AND (SELECT COUNT(*) FROM my_cart_farm_produce WHERE transaction_id=t.id) = 0 AND (SELECT COUNT(*) FROM my_cart_supply WHERE transaction_id=t.id) = 0)";

        // One row per transaction — with farmer/supplier person name
        $baseQuery = "
            FROM transaction t
            JOIN transaction_status ts ON ts.transaction_id = t.id AND ts.is_latest = 1
            LEFT JOIN checkout ch           ON ch.transaction_id = t.id
            LEFT JOIN transaction_details td ON td.transaction_id = t.id
            LEFT JOIN farmer_farm ff        ON t.farm_id = ff.id
            LEFT JOIN farmer fa             ON ff.farmer_id = fa.id
            LEFT JOIN person fp             ON fa.person_id = fp.id
            LEFT JOIN supplier sup          ON t.supplier_id = sup.id
            LEFT JOIN person spp            ON sup.person_id = spp.id
            LEFT JOIN (SELECT supplier_id, MIN(store_name) AS store_name, MIN(img_path) AS img_path
                       FROM supplier_store WHERE is_active=1 GROUP BY supplier_id) ss ON t.supplier_id = ss.supplier_id
            LEFT JOIN transaction_ratings tr ON tr.transaction_id = t.id
            WHERE {$where}
        ";

        $countResult = $this->db->query("SELECT COUNT(*) AS cnt {$baseQuery}");
        $total = $countResult ? (int)$countResult->row()->cnt : 0;

        $rowsResult = $this->db->query("
            SELECT
                t.id                AS transaction_id,
                t.transaction_date,
                CASE WHEN t.farm_id IS NOT NULL THEN 'farmer' ELSE 'supplier' END AS order_type,
                COALESCE(
                    td.to_farmer,
                    (SELECT COALESCE(SUM(sub_total),0) FROM my_cart_farm_produce WHERE transaction_id=t.id)
                    + (SELECT COALESCE(SUM(sub_total),0) FROM my_cart_supply WHERE transaction_id=t.id)
                ) AS order_subtotal,
                COALESCE(
                    NULLIF(td.total_payment, 0),
                    NULLIF(t.total_sale_amount, 0),
                    (
                        (SELECT COALESCE(SUM(sub_total),0) FROM my_cart_farm_produce WHERE transaction_id=t.id)
                        + (SELECT COALESCE(SUM(sub_total),0) FROM my_cart_supply WHERE transaction_id=t.id)
                    ) * 1.01
                ) AS total_sale_amount,
                ts.status,
                ch.payment_method,
                ch.delivery_method,
                ch.delivery_address,
                COALESCE(ff.farm_name, ss.store_name)        AS store_label,
                COALESCE(ff.img_path,  ss.img_path)          AS store_img,
                CONCAT(COALESCE(fp.first_name, spp.first_name, ''),
                       ' ',
                       COALESCE(fp.last_name,  spp.last_name,  '')) AS owner_name,
                (SELECT COUNT(*) FROM my_cart_farm_produce WHERE transaction_id = t.id)
                + (SELECT COUNT(*) FROM my_cart_supply       WHERE transaction_id = t.id) AS item_count,
                tr.id     AS rating_id,
                tr.rating AS existing_rating
            {$baseQuery}
            ORDER BY t.id DESC
            LIMIT {$start}, {$length}
        ");
        $rows = $rowsResult ? $rowsResult->result() : [];

        $data = [];
        foreach ($rows as $r) {
            $si        = $this->_statusInfo($r->status);
            $label     = $r->store_label ?: 'Unknown Store';
            $ownerName = trim($r->owner_name);
            $isFarmer  = ($r->order_type === 'farmer');
            $typeIcon  = $isFarmer ? 'fa-tractor' : 'fa-store';
            $typeLabel = $isFarmer ? 'Farmer' : 'Supplier';
            $typeColor = $isFarmer ? '#16a34a' : '#0369a1';
            $storeImg  = $r->store_img
                ? '<img src="' . base_url($r->store_img) . '" style="width:36px;height:36px;border-radius:8px;object-fit:cover;flex-shrink:0;">'
                : '<div style="width:36px;height:36px;border-radius:8px;background:' . ($isFarmer ? '#d1fae5' : '#dbeafe') . ';display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fa ' . $typeIcon . '" style="color:' . $typeColor . ';font-size:15px;"></i></div>';

            $rowSub    = (float)$r->order_subtotal;
            $rowFee    = round($rowSub * 0.01, 2);
            $rowTotal  = (float)$r->total_sale_amount;
            $isPending = ($r->status === 'PENDING');

            $data[] = [
                'farm'   => $storeImg
                            . '<div style="margin-left:10px;">'
                            . '<div style="font-weight:700;font-size:13px;color:#111827;">' . htmlspecialchars($label) . '</div>'
                            . ($ownerName ? '<div style="font-size:11px;color:' . $typeColor . ';font-weight:600;"><i class="fa ' . $typeIcon . '" style="font-size:10px;margin-right:3px;"></i>' . $typeLabel . ': ' . htmlspecialchars($ownerName) . '</div>' : '')
                            . '<div style="font-size:11px;color:#6b7280;">Order #' . $r->transaction_id . ' &middot; ' . $r->item_count . ' item(s)</div>'
                            . '</div>',
                'date'   => '<div style="font-size:12px;color:#374151;">' . date('M d, Y', strtotime($r->transaction_date)) . '</div>'
                            . '<div style="font-size:11px;color:#9ca3af;">' . date('h:i A', strtotime($r->transaction_date)) . '</div>',
                'status' => '<span style="background:' . $si['bg'] . ';color:' . $si['color'] . ';border-radius:20px;padding:5px 12px;font-size:11px;font-weight:700;display:inline-block;">'
                            . $si['icon'] . ' ' . $si['label'] . '</span>',
                'amount' => '<div style="font-weight:700;font-size:13px;color:#111827;">&#8369;' . number_format($rowTotal, 2) . '</div>'
                            . '<div style="font-size:10px;color:#9ca3af;">&#8369;' . number_format($rowSub, 2) . ' + &#8369;' . number_format($rowFee, 2) . ' fee</div>'
                            . '<div style="font-size:10px;color:#6b7280;">' . ($isPending ? 'PENDING' : strtoupper($r->payment_method ?? '')) . '</div>',
                'action' => $this->_buildActionCell($r),
            ];
        }

        $counts = $this->_getCounts($person_id);

        ob_end_clean();
        echo json_encode([
            'draw'            => isset($requestData['draw']) ? (int)$requestData['draw'] : 1,
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $data,
            'counts'          => [
                'pending'    => (int)$counts->pending,
                'to_receive' => (int)$counts->to_receive,
                'completed'  => (int)$counts->completed,
                'cancelled'  => (int)$counts->cancelled,
                'to_rate'    => (int)$counts->to_rate,
            ],
        ]);
    }

    // ── AJAX: Get full tracking timeline for one order ────────
    public function getTracking()
    {
        ob_start();
        $transaction_id = (int)$this->input->post('transaction_id');
        $person_id      = (int)$this->session->agrishop_person_id;

        // Security: verify this transaction belongs to the logged-in person
        $tx = $this->db->query(
            "SELECT t.*, ts.status AS current_status,
                    COALESCE(ch.payment_method, td.payment_method) AS payment_method,
                    COALESCE(ch.delivery_method, td.delivery_method) AS delivery_method,
                    ch.delivery_address
             FROM transaction t
             JOIN transaction_status ts ON ts.transaction_id = t.id AND ts.is_latest = 1
             LEFT JOIN checkout ch         ON ch.transaction_id = t.id
             LEFT JOIN transaction_details td ON td.transaction_id = t.id
             WHERE t.id = ? AND t.person_id = ?",
            [$transaction_id, $person_id]
        )->row();

        if (!$tx) { ob_end_clean(); echo json_encode(['success' => false]); return; }

        // Items in this order (farm produce + supply)
        $items = $this->db->query(
            "SELECT mc.qty, mc.sub_total,
                pr.name        AS produce_name,
                fp.specification_variety,
                fp.uom,
                pr.img_path    AS produce_img,
                pm.price       AS unit_price
             FROM my_cart_farm_produce mc
             JOIN farm_produce fp           ON mc.farm_produce_id = fp.id
             JOIN produce pr                ON fp.produce_id = pr.id
             LEFT JOIN price_monitoring_farm_produce pm ON mc.price_id_during_transact = pm.id
             WHERE mc.transaction_id = ?
             UNION ALL
             SELECT mc2.qty, mc2.sub_total,
                ss.name        AS produce_name,
                NULL           AS specification_variety,
                ss.uom,
                ss.img_path    AS produce_img,
                mc2.price_during_transact AS unit_price
             FROM my_cart_supply mc2
             JOIN supplier_supply ss ON mc2.supplier_supply_id = ss.id
             WHERE mc2.transaction_id = ?",
            [$transaction_id, $transaction_id]
        )->result();

        // Full status history
        $history = $this->db->query(
            "SELECT ts.status, ts.created_at, ts.is_latest,
                    CONCAT(p.first_name,' ',p.last_name) AS updated_by
             FROM transaction_status ts
             LEFT JOIN person p ON ts.created_by_person_id = p.id
             WHERE ts.transaction_id = ?
             ORDER BY ts.created_at ASC",
            [$transaction_id]
        )->result();

        // Build pipeline stages
        $pipeline = ['PENDING', 'RESERVED', 'PREPARING', 'ORDER_IS_READY', 'COMPLETED'];
        $current  = $tx->current_status;

        // If cancelled, special pipeline
        if ($current === 'CANCELLED') {
            $pipeline = ['PENDING', 'CANCELLED'];
        }

        $stagesDone = [];
        foreach ($pipeline as $stage) {
            $reached = false;
            $reachedAt = null;
            foreach ($history as $h) {
                if ($h->status === $stage) {
                    $reached   = true;
                    $reachedAt = $h->created_at;
                }
            }
            $stagesDone[] = [
                'stage'      => $stage,
                'label'      => $this->_stageLabel($stage),
                'icon'       => $this->_stageIcon($stage),
                'done'       => $reached,
                'is_current' => $stage === $current,
                'date'       => $reached ? date('M d, Y h:i A', strtotime($reachedAt)) : null,
            ];
        }

        // Farm info (from transaction.farm_id)
        $farm = $this->db->query(
            "SELECT farm_name, img_path FROM farmer_farm WHERE id = ? LIMIT 1",
            [$tx->farm_id ?? 0]
        )->row();
        $farmName = $farm ? $farm->farm_name : 'Unknown Farm';
        $farmImg  = ($farm && $farm->img_path) ? base_url($farm->img_path) : null;

        // Rating info
        $ratingRow = $this->db->query(
            "SELECT id, rating, comment FROM transaction_ratings WHERE transaction_id = ? LIMIT 1",
            [$transaction_id]
        )->row();
        $canRate      = ($tx->current_status === 'COMPLETED' && !$ratingRow);
        $existingRate = $ratingRow ? (int)$ratingRow->rating  : null;
        $rateComment  = $ratingRow ? $ratingRow->comment       : null;

        // Compute subtotal, fee, total from cart or transaction_details
        $td = $this->db->query("SELECT to_farmer, to_admin, total_payment FROM transaction_details WHERE transaction_id=? LIMIT 1", [$transaction_id])->row();
        if ($td && $td->total_payment > 0) {
            $trkSubtotal = (float)$td->to_farmer;
            $trkFee      = (float)$td->to_admin;
            $trkTotal    = (float)$td->total_payment;
        } else {
            $trkSubtotal = array_sum(array_map(fn($i) => (float)$i->sub_total, $items));
            $trkFee      = round($trkSubtotal * 0.01, 2);
            $trkTotal    = $trkSubtotal + $trkFee;
        }

        ob_end_clean();
        echo json_encode([
            'success'         => true,
            'transaction_id'  => $transaction_id,
            'current_status'  => $current,
            'status_info'     => $this->_statusInfo($current),
            'date_ordered'    => date('M d, Y h:i A', strtotime($tx->transaction_date)),
            'subtotal'        => number_format($trkSubtotal, 2),
            'fee'             => number_format($trkFee, 2),
            'total'           => number_format($trkTotal, 2),
            'payment_method'  => $tx->payment_method,
            'delivery_method' => $tx->delivery_method,
            'delivery_address'=> $tx->delivery_address,
            'farm_name'       => $farmName,
            'farm_img'        => $farmImg,
            'pipeline'        => $stagesDone,
            'history'         => array_map(function($h) {
                return [
                    'status'     => $h->status,
                    'label'      => $this->_stageLabel($h->status),
                    'date'       => date('M d, Y h:i A', strtotime($h->created_at)),
                    'updated_by' => $h->updated_by,
                    'is_latest'  => (bool)$h->is_latest,
                ];
            }, $history),
            'can_rate'        => $canRate,
            'existing_rating' => $existingRate,
            'rate_comment'    => $rateComment,
            'items'           => array_map(function($i) {
                $name = $i->produce_name . ($i->specification_variety ? ' (' . $i->specification_variety . ')' : '');
                return [
                    'name'     => $name,
                    'img'      => $i->produce_img ? base_url($i->produce_img) : null,
                    'qty'      => $i->qty,
                    'uom'      => $i->uom,
                    'price'    => number_format($i->unit_price ?? 0, 2),
                    'subtotal' => number_format($i->sub_total, 2),
                ];
            }, $items),
        ]);
    }

    // ── Cancel an order (only if still PENDING) ───────────────
    public function cancelOrder()
    {
        $transaction_id = (int)$this->input->post('transaction_id');
        $reason         = trim($this->input->post('reason'));
        $person_id      = $this->session->agrishop_person_id;

        // Verify ownership + still cancellable
        $ts = $this->db->query(
            "SELECT ts.status FROM transaction t
             JOIN transaction_status ts ON ts.transaction_id = t.id AND ts.is_latest = 1
             WHERE t.id = ? AND t.person_id = ?",
            [$transaction_id, $person_id]
        )->row();

        if (!$ts || !in_array($ts->status, ['PENDING'])) {
            echo json_encode(['success' => false, 'message' => 'Order cannot be cancelled at this stage.']);
            return;
        }

        $this->db->trans_begin();

        $this->db->update('transaction_status', ['is_latest' => 0], ['transaction_id' => $transaction_id]);
        $this->db->insert('transaction_status', [
            'transaction_id'       => $transaction_id,
            'status'               => 'CANCELLED',
            'is_latest'            => 1,
            'created_at'           => date('Y-m-d H:i:s'),
            'created_by_person_id' => $person_id,
        ]);
        $this->db->insert('transaction_cancel', [
            'transaction_id' => $transaction_id,
            'reason'         => $reason ?: 'Cancelled by customer',
            'created_at'     => date('Y-m-d H:i:s'),
            'created_by'     => $person_id,
        ]);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            echo json_encode(['success' => false, 'message' => 'Failed to cancel order.']);
        } else {
            $this->db->trans_commit();
            echo json_encode(['success' => true, 'message' => 'Order cancelled.']);
        }
    }

    // ── Helpers ───────────────────────────────────────────────
    // ── AJAX: Save rating ─────────────────────────────────────
    public function saveRating()
    {
        $person_id      = (int)$this->session->agrishop_person_id;
        $transaction_id = (int)$this->input->post('transaction_id');
        $rating_value   = (int)$this->input->post('rating_value');
        $comment        = trim($this->input->post('review_comment') ?? '');

        if (!$person_id || !$transaction_id || $rating_value < 1 || $rating_value > 5) {
            echo json_encode(['success' => false, 'message' => 'Invalid data.']); return;
        }

        $ts = $this->db->query(
            "SELECT ts.status FROM transaction t
             JOIN transaction_status ts ON ts.transaction_id = t.id AND ts.is_latest = 1
             WHERE t.id = ? AND t.person_id = ? LIMIT 1",
            [$transaction_id, $person_id]
        )->row();

        if (!$ts || $ts->status !== 'COMPLETED') {
            echo json_encode(['success' => false, 'message' => 'Only completed orders can be rated.']); return;
        }

        $existing = $this->db->query(
            "SELECT id FROM transaction_ratings WHERE transaction_id = ? LIMIT 1",
            [$transaction_id]
        )->row();
        if ($existing) {
            echo json_encode(['success' => false, 'message' => 'Already rated.']); return;
        }

        $this->db->insert('transaction_ratings', [
            'transaction_id'       => $transaction_id,
            'rating'               => $rating_value,
            'comment'              => $comment,
            'created_at'           => date('Y-m-d H:i:s'),
            'created_by_person_id' => $person_id,
        ]);

        echo json_encode(['success' => true]);
    }

    // ── Helpers ───────────────────────────────────────────────
    private function _getCounts($person_id)
    {
        $row = $this->db->query(
            "SELECT
                SUM(ts.status = 'PENDING'
                    AND ((SELECT COUNT(*) FROM my_cart_farm_produce WHERE transaction_id=t.id)
                       + (SELECT COUNT(*) FROM my_cart_supply WHERE transaction_id=t.id)) > 0) AS pending,
                SUM(ts.status IN ('RESERVED','PREPARING','ORDER_IS_READY'))              AS to_receive,
                SUM(ts.status = 'COMPLETED')                                             AS completed,
                SUM(ts.status = 'CANCELLED')                                             AS cancelled,
                SUM(ts.status = 'COMPLETED' AND tr.id IS NULL)                           AS to_rate
             FROM transaction t
             JOIN transaction_status ts ON ts.transaction_id = t.id AND ts.is_latest = 1
             LEFT JOIN transaction_ratings tr ON tr.transaction_id = t.id
             WHERE t.person_id = ?",
            [$person_id]
        )->row();
        return $row ?? (object)['pending' => 0, 'to_receive' => 0, 'completed' => 0, 'cancelled' => 0, 'to_rate' => 0];
    }

    private function _buildActionCell($r)
    {
        $canRate     = ($r->status === 'COMPLETED' && !$r->rating_id);
        $alreadyRated = ($r->status === 'COMPLETED' && $r->rating_id);

        $trackBtn = '<button class="btn btn-sm btn-outline-success" onclick="viewOrderTracking(' . $r->transaction_id . ')"'
            . ' style="border-radius:8px;font-size:12px;font-weight:600;white-space:nowrap;">'
            . '<i class="fa fa-route" style="margin-right:4px;"></i>Track</button>';

        if ($canRate) {
            $rateBtn = ' <button class="btn btn-sm btn-warning" onclick="coOpenRating(' . $r->transaction_id . ')"'
                . ' style="border-radius:8px;font-size:12px;font-weight:600;white-space:nowrap;">'
                . '<i class="fa fa-star" style="margin-right:4px;"></i>Rate</button>';
            return '<div style="display:flex;gap:4px;flex-wrap:wrap;">' . $trackBtn . $rateBtn . '</div>';
        }

        if ($alreadyRated) {
            $stars = '';
            for ($i = 1; $i <= 5; $i++) {
                $stars .= '<i class="fa fa-star" style="color:' . ($i <= $r->existing_rating ? '#f59e0b' : '#d1d5db') . ';font-size:12px;"></i>';
            }
            return '<div style="display:flex;gap:4px;flex-wrap:wrap;align-items:center;">'
                . $trackBtn
                . '<div style="white-space:nowrap;">' . $stars . '</div></div>';
        }

        return $trackBtn;
    }

    private function _statusInfo($status)
    {
        $map = [
            'PENDING'        => ['label' => 'Pending',    'bg' => '#fef3c7', 'color' => '#92400e', 'icon' => '⏳'],
            'RESERVED'       => ['label' => 'Confirmed',  'bg' => '#dbeafe', 'color' => '#1e40af', 'icon' => '✅'],
            'PREPARING'      => ['label' => 'Preparing',  'bg' => '#ede9fe', 'color' => '#5b21b6', 'icon' => '🍳'],
            'ORDER_IS_READY' => ['label' => 'Ready',      'bg' => '#d1fae5', 'color' => '#065f46', 'icon' => '📦'],
            'COMPLETED'      => ['label' => 'Delivered',  'bg' => '#bbf7d0', 'color' => '#14532d', 'icon' => '🎉'],
            'CANCELLED'      => ['label' => 'Cancelled',  'bg' => '#fee2e2', 'color' => '#991b1b', 'icon' => '❌'],
        ];
        return $map[$status] ?? ['label' => $status, 'bg' => '#f3f4f6', 'color' => '#374151', 'icon' => '•'];
    }

    private function _stageLabel($stage)
    {
        $labels = [
            'PENDING'        => 'Order Placed',
            'RESERVED'       => 'Confirmed',
            'PREPARING'      => 'Preparing',
            'ORDER_IS_READY' => 'Ready for Pickup',
            'COMPLETED'      => 'Delivered',
            'CANCELLED'      => 'Cancelled',
        ];
        return $labels[$stage] ?? $stage;
    }

    private function _stageIcon($stage)
    {
        $icons = [
            'PENDING'        => 'fa-shopping-cart',
            'RESERVED'       => 'fa-check-circle',
            'PREPARING'      => 'fa-box-open',
            'ORDER_IS_READY' => 'fa-store',
            'COMPLETED'      => 'fa-check-double',
            'CANCELLED'      => 'fa-times-circle',
        ];
        return $icons[$stage] ?? 'fa-circle';
    }
}
