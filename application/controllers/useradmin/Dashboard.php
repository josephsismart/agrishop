<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->redirect();
    }

    public function index()
    {
        $page_data = $this->system();
        $uri = $this->session->agrishop_login_uri;
        $page_data += [
            "page_title"       => "Dashboard",
            "current_location" => "dashboard",
            "content"          => [$this->load->view('interface/' . $uri . '/Dashboard', [
                "dashboard" => $this->getDashboard(),
            ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }

    function getDashboard()
    {
        // ── Core counts ─────────────────────────────────────────────────────────
        $revenue = $this->db->query("
            SELECT
                (SELECT COUNT(*) FROM user)                                          AS user,
                (SELECT COUNT(*) FROM farmer)                                        AS farmer,
                (SELECT COUNT(*) FROM subscription_history WHERE is_active IS TRUE)  AS subscription,
                (SELECT COALESCE(SUM(total_payment),0) FROM invoice_billing WHERE is_paid IS TRUE) AS revenue
        ")->row();

        $data = [
            "user"         => $revenue->user         == null ? 0 : number_format($revenue->user, 0),
            "farmer"       => $revenue->farmer        == null ? 0 : number_format($revenue->farmer, 0),
            "subscription" => $revenue->subscription  == null ? 0 : number_format($revenue->subscription, 0),
            "revenue"      => $revenue->revenue       == null ? 0 : number_format($revenue->revenue, 0),
        ];

        // ── Top farmers ──────────────────────────────────────────────────────────
        $farmer_revue = $this->db->query("
            SELECT COALESCE(CONCAT(p.first_name,' ',p.last_name), 'Unknown') AS farmer,
                   SUM(td.to_farmer) AS revenue
            FROM transaction t
            LEFT JOIN farmer_farm ff ON t.farm_id = ff.id
            LEFT JOIN transaction_details td ON t.id = td.transaction_id
            LEFT JOIN transaction_cancel tc  ON t.id = tc.transaction_id
            LEFT JOIN farmer f  ON ff.farmer_id = f.id
            LEFT JOIN person p  ON f.person_id = p.id
            WHERE tc.id IS NULL AND p.id IS NOT NULL
            GROUP BY CONCAT(p.first_name,' ',p.last_name)
            ORDER BY SUM(td.to_farmer) DESC
            LIMIT 5
        ")->result();
        $data["top_farmer"] = json_encode($farmer_revue);

        // ── Farmer remittance / billing ──────────────────────────────────────────
        $farmer_remittance = $this->db->query("
            SELECT COALESCE(CONCAT(p.first_name,' ',p.last_name), 'Unknown') AS farmer,
                   SUM(ib.total_payment) AS amount,
                   ib.is_paid AS status
            FROM invoice_billing ib
            LEFT JOIN farmer f ON ib.farmer_id = f.id
            LEFT JOIN person p ON f.person_id = p.id
            WHERE p.id IS NOT NULL
            GROUP BY CONCAT(p.first_name,' ',p.last_name), ib.is_paid
            LIMIT 5
        ")->result();
        $data["farmer_remittance"] = json_encode($farmer_remittance);

        // ── Revenue trend (current year) ─────────────────────────────────────────
        $current_year = date('Y');
        $billing      = $this->billing();
        $revnue_trend = $this->db->query("
            SELECT DATE_FORMAT(paid_at,'%b') AS month,
                   SUM(total_payment) AS revenue
            FROM ($billing) b
            WHERE paid_at IS NOT NULL
            AND DATE_FORMAT(paid_at,'%Y') = '$current_year'
            GROUP BY DATE_FORMAT(paid_at,'%b'), DATE_FORMAT(paid_at,'%m')
            ORDER BY DATE_FORMAT(paid_at,'%m')
        ")->result();
        $data["revnue_trendGraph"] = json_encode($revnue_trend);

        // ── Orders analytics (current year) ──────────────────────────────────────
        $orderAnalytics = $this->db->query("
            SELECT DATE_FORMAT(mcfp.created_at,'%b') AS month,
                   SUM(qty) AS orders
            FROM my_cart_farm_produce mcfp
            WHERE DATE_FORMAT(mcfp.created_at,'%Y') = '$current_year'
            GROUP BY DATE_FORMAT(mcfp.created_at,'%b'), DATE_FORMAT(mcfp.created_at,'%m')
            ORDER BY DATE_FORMAT(mcfp.created_at,'%m')
        ")->result();
        $data["orderAnalyticsGraph"] = json_encode($orderAnalytics);

        // ── Extended stats ───────────────────────────────────────────────────────
        $extras = $this->db->query("
            SELECT
                (SELECT COUNT(*) FROM supplier)                                       AS total_suppliers,
                (SELECT COUNT(*) FROM farmer WHERE approved_by_person_id IS NULL)     AS pending_farmers,
                (SELECT COUNT(*) FROM supplier WHERE approved_by_person_id IS NULL)   AS pending_suppliers,
                (SELECT COUNT(*) FROM transaction WHERE is_done = 1)                  AS completed_orders,
                (SELECT COUNT(*) FROM farmer_farm WHERE is_active = 1)                AS total_farms,
                (SELECT COUNT(DISTINCT produce_id) FROM farm_produce)                 AS total_produce
        ")->row();

        $data += [
            "total_suppliers"   => number_format($extras->total_suppliers   ?? 0),
            "pending_farmers"   => number_format($extras->pending_farmers   ?? 0),
            "pending_suppliers" => number_format($extras->pending_suppliers ?? 0),
            "completed_orders"  => number_format($extras->completed_orders  ?? 0),
            "total_farms"       => number_format($extras->total_farms       ?? 0),
            "total_produce"     => number_format($extras->total_produce     ?? 0),
        ];

        // ── Today's KPIs ─────────────────────────────────────────────────────────
        $today = date('Y-m-d');
        $today_stats = $this->db->query("
            SELECT
                COUNT(DISTINCT t.id)                AS orders_today,
                COALESCE(SUM(td.total_payment), 0)  AS revenue_today
            FROM transaction t
            LEFT JOIN transaction_details td  ON t.id = td.transaction_id
            LEFT JOIN transaction_status ts   ON t.id = ts.transaction_id AND ts.is_latest = 1
            LEFT JOIN transaction_cancel tc   ON t.id = tc.transaction_id
            WHERE tc.id IS NULL
            AND ts.status = 'COMPLETED'
            AND DATE(t.transaction_date) = '$today'
        ")->row();

        $month_stats = $this->db->query("
            SELECT
                COUNT(DISTINCT t.id)                AS orders_month,
                COALESCE(SUM(td.total_payment), 0)  AS revenue_month
            FROM transaction t
            LEFT JOIN transaction_details td  ON t.id = td.transaction_id
            LEFT JOIN transaction_status ts   ON t.id = ts.transaction_id AND ts.is_latest = 1
            LEFT JOIN transaction_cancel tc   ON t.id = tc.transaction_id
            WHERE tc.id IS NULL
            AND ts.status = 'COMPLETED'
            AND MONTH(t.transaction_date) = MONTH(NOW())
            AND YEAR(t.transaction_date)  = YEAR(NOW())
        ")->row();

        $data += [
            "orders_today"  => number_format($today_stats->orders_today  ?? 0),
            "revenue_today" => number_format($today_stats->revenue_today ?? 0, 2),
            "orders_month"  => number_format($month_stats->orders_month  ?? 0),
            "revenue_month" => number_format($month_stats->revenue_month ?? 0, 2),
        ];

        // ── Order status breakdown ────────────────────────────────────────────────
        $status_counts = $this->db->query("
            SELECT ts.status, COUNT(1) AS cnt
            FROM transaction t
            JOIN transaction_status ts ON t.id = ts.transaction_id AND ts.is_latest = 1
            LEFT JOIN transaction_cancel tc ON t.id = tc.transaction_id
            WHERE tc.id IS NULL
            GROUP BY ts.status
        ")->result();

        $order_status_map = [];
        foreach ($status_counts as $row) {
            $order_status_map[strtolower($row->status)] = $row->cnt;
        }

        $cancelled_count = $this->db->query("SELECT COUNT(1) AS cnt FROM transaction_cancel")->row()->cnt ?? 0;

        $data["order_status_map"] = $order_status_map;
        $data["cancelled_orders"] = number_format($cancelled_count);

        // ── Fast/slow produce ─────────────────────────────────────────────────────
        $query_produce = "
            SELECT pr.id,
                CASE WHEN pr.img_path IS NOT NULL THEN pr.img_path ELSE pc.img_path END AS img_path,
                pr.name, SUM(mcfp.qty) AS sum_qty
            FROM my_cart_farm_produce mcfp
            LEFT JOIN transaction t       ON mcfp.transaction_id = t.id
            LEFT JOIN transaction_cancel tc ON t.id = tc.transaction_id
            LEFT JOIN farm_produce fp     ON mcfp.farm_produce_id = fp.id
            LEFT JOIN produce pr          ON fp.produce_id = pr.id
            LEFT JOIN produce_classification pc ON pr.produce_classification_id = pc.id
            WHERE tc.id IS NULL
            GROUP BY pr.id, pr.name, pc.img_path
        ";
        $data["fast_produce"] = $this->db->query($query_produce . " ORDER BY SUM(mcfp.qty) DESC LIMIT 6")->result_array();
        $data["slow_produce"] = $this->db->query($query_produce . " ORDER BY SUM(mcfp.qty) ASC LIMIT 6")->result_array();

        return $data;
    }
}

/* End of file Dashboard.php */
/* Location: ./application/controllers/useradmin/Dashboard.php */
