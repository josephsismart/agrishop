<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->redirect();
        $this->load->helper('date');
        date_default_timezone_set("Asia/Manila");
    }

    public function index()
    {
        $page_data = $this->system();
        $uri = $this->session->agrishop_login_uri;
        $page_data += [
            "page_title"        => "Dashboard",
            "current_location"  => "dashboard",
            "content"           => [$this->load->view('interface/' . $uri . '/Dashboard', [
                "dashboard" => $this->getDashboard(),
                "billing"   => $this->supplier_billing_count(),
            ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }

    function getDashboard()
    {
        $supplier_id  = (int) $this->session->agrishop_login_supplier_id;
        $current_year = date('Y');

        // ── Helper: safe query row ─────────────────────────────
        // Returns null instead of crashing if query returns false
        $safeRow = function($sql, $params = []) {
            $q = $this->db->query($sql, $params);
            return ($q !== false) ? $q->row() : null;
        };
        $safeResult = function($sql, $params = []) {
            $q = $this->db->query($sql, $params);
            return ($q !== false) ? $q->result() : [];
        };

        // ── Total supplies ─────────────────────────────────────
        $r = $safeRow("SELECT COUNT(1) AS total FROM supplier_supply WHERE supplier_id = ? AND is_active = 1", [$supplier_id]);
        $total_supplies = $r ? (int) $r->total : 0;

        // ── Total stores ───────────────────────────────────────
        $r = $safeRow("SELECT COUNT(1) AS total FROM supplier_store WHERE supplier_id = ? AND is_active = 1", [$supplier_id]);
        $total_stores = $r ? (int) $r->total : 0;

        // ── Revenue & orders ───────────────────────────────────
        // Uses LEFT JOIN so it still returns a row even when my_cart_supply is empty
        $r = $safeRow("
            SELECT
                COUNT(DISTINCT mcs.transaction_id) AS total_orders,
                COALESCE(SUM(mcs.sub_total), 0)    AS revenue
            FROM supplier_supply ss
            LEFT JOIN my_cart_supply mcs ON mcs.supplier_supply_id = ss.id
            WHERE ss.supplier_id = ?
        ", [$supplier_id]);
        $revenue      = ($r && $r->revenue)      ? number_format((float) $r->revenue, 0) : 0;
        $total_orders = ($r && $r->total_orders) ? number_format((int) $r->total_orders, 0) : 0;

        $data = [
            "revenue"        => $revenue,
            "total_orders"   => $total_orders,
            "total_supplies" => number_format($total_supplies, 0),
            "total_stores"   => number_format($total_stores, 0),
        ];

        // ── Top selling supplies ──────────────────────────────
        $top = $safeResult("
            SELECT
                ss.name,
                sc.name          AS category,
                ss.uom,
                ss.price,
                COALESCE(SUM(mcs.qty), 0)       AS qty_sold,
                COALESCE(SUM(mcs.sub_total), 0) AS total_sales,
                ss.img_path
            FROM supplier_supply ss
            LEFT JOIN my_cart_supply  mcs ON mcs.supplier_supply_id = ss.id
            LEFT JOIN supply_category sc  ON ss.supply_category_id  = sc.id
            WHERE ss.supplier_id = ?
            GROUP BY ss.id, ss.name, sc.name, ss.uom, ss.price, ss.img_path
            ORDER BY qty_sold DESC
            LIMIT 10
        ", [$supplier_id]);
        $data["top_supplies"] = json_encode($top);

        // ── Monthly orders graph ──────────────────────────────
        $monthly = $safeResult("
            SELECT
                DATE_FORMAT(t.transaction_date, '%b') AS mon,
                COALESCE(SUM(mcs.qty), 0)       AS qty,
                COALESCE(SUM(mcs.sub_total), 0) AS revenue
            FROM my_cart_supply mcs
            JOIN supplier_supply ss ON mcs.supplier_supply_id = ss.id
            JOIN transaction t      ON mcs.transaction_id     = t.id
            WHERE ss.supplier_id = ?
              AND DATE_FORMAT(t.transaction_date, '%Y') = ?
            GROUP BY
                DATE_FORMAT(t.transaction_date, '%b'),
                DATE_FORMAT(t.transaction_date, '%m')
            ORDER BY DATE_FORMAT(t.transaction_date, '%m')
        ", [$supplier_id, $current_year]);
        $data["ordersGraph"] = json_encode($monthly);

        // ── Category breakdown ────────────────────────────────
        $cats = $safeResult("
            SELECT sc.name AS category, COUNT(ss.id) AS count
            FROM supplier_supply ss
            JOIN supply_category sc ON ss.supply_category_id = sc.id
            WHERE ss.supplier_id = ? AND ss.is_active = 1
            GROUP BY sc.name
        ", [$supplier_id]);
        $data["categoryGraph"] = json_encode($cats);

        return $data;
    }

    // ── Supplier billing count (unpaid invoices) ──────────────
    public function supplier_billing_count()
    {
        $supplier_id = (int) $this->session->agrishop_login_supplier_id;
        if (!$supplier_id) return ["count" => 0];

        // Guard: table might not exist during first deploy
        $table_exists = $this->db->query(
            "SELECT COUNT(1) AS c FROM information_schema.tables
             WHERE table_schema = DATABASE() AND table_name = 'supplier_invoice_billing'"
        );
        if (!$table_exists || (int) $table_exists->row()->c === 0) {
            return ["count" => 0];
        }

        $q = $this->db->query("
            SELECT COUNT(1) AS count
            FROM supplier_invoice_billing
            WHERE supplier_id = ? AND is_paid = false
        ", [$supplier_id]);

        if (!$q) return ["count" => 0];
        $row = $q->row();
        return ["count" => $row ? (int) $row->count : 0];
    }
}

/* End of file Dashboard.php */
/* Location: ./application/controllers/usersupplier/Dashboard.php */