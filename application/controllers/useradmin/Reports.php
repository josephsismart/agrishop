<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reports extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->redirect();
    }

    // ─── Main page ─────────────────────────────────────────────────────────────
    public function index()
    {
        $page_data = $this->system();
        $uri = $this->session->agrishop_login_uri;
        $page_data += [
            "page_title"       => "Generate Reports",
            "current_location" => "reports",
            "content"          => [$this->load->view('interface/' . $uri . '/GenerateReports', [
                "farmers"       => $this->_getFarmersList(),
                "produce_types" => $this->_getProduceTypes(),
            ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }

    // ─── AJAX: build report data ────────────────────────────────────────────────
    public function getReportData()
    {
        $report_type  = $this->input->post('report_type');
        $date_from    = $this->input->post('date_from');
        $date_to      = $this->input->post('date_to');
        $farmer_id    = $this->input->post('farmer_id');
        $produce_type = $this->input->post('produce_type');
        $order_status = $this->input->post('order_status');

        if (!$date_from || !$date_to) {
            echo json_encode(['success' => false, 'message' => 'Please select a valid date range.']);
            return;
        }

        switch ($report_type) {
            case 'sales':
                $data = $this->_getSalesReport($date_from, $date_to, $farmer_id, $produce_type, $order_status);
                break;
            case 'farmers':
                $data = $this->_getFarmersReport($date_from, $date_to, $farmer_id);
                break;
            case 'products':
                $data = $this->_getProductsReport($date_from, $date_to, $produce_type);
                break;
            case 'orders':
                $data = $this->_getOrdersReport($date_from, $date_to, $farmer_id, $order_status);
                break;
            case 'users':
                $data = $this->_getUsersReport($date_from, $date_to);
                break;
            default: // comprehensive
                $data = $this->_getComprehensiveReport($date_from, $date_to, $farmer_id, $produce_type, $order_status);
                break;
        }

        $data['success']      = true;
        $data['report_type']  = $report_type;
        $data['date_from']    = $date_from;
        $data['date_to']      = $date_to;
        $data['generated_at'] = date('F j, Y \a\t g:i A');
        $data['report_id']    = 'AGRI-' . date('Ymd-His');

        echo json_encode($data);
    }

    // ─── Export as printable PDF page ──────────────────────────────────────────
    public function exportPDF()
    {
        $report_type  = $this->input->post('report_type');
        $date_from    = $this->input->post('date_from');
        $date_to      = $this->input->post('date_to');
        $farmer_id    = $this->input->post('farmer_id');
        $produce_type = $this->input->post('produce_type');
        $order_status = $this->input->post('order_status');

        switch ($report_type) {
            case 'sales':
                $report_data = $this->_getSalesReport($date_from, $date_to, $farmer_id, $produce_type, $order_status);
                break;
            case 'farmers':
                $report_data = $this->_getFarmersReport($date_from, $date_to, $farmer_id);
                break;
            case 'products':
                $report_data = $this->_getProductsReport($date_from, $date_to, $produce_type);
                break;
            case 'orders':
                $report_data = $this->_getOrdersReport($date_from, $date_to, $farmer_id, $order_status);
                break;
            case 'users':
                $report_data = $this->_getUsersReport($date_from, $date_to);
                break;
            default:
                $report_data = $this->_getComprehensiveReport($date_from, $date_to, $farmer_id, $produce_type, $order_status);
                break;
        }

        $sys  = $this->system();
        $data = array_merge($report_data, $sys, [
            'report_type'  => $report_type,
            'date_from'    => $date_from,
            'date_to'      => $date_to,
            'generated_at' => date('F j, Y \a\t g:i A'),
            'generated_by' => $this->session->agrishop_login_first_name . ' ' . $this->session->agrishop_login_last_name,
            'page_title'   => 'Report',
        ]);

        $this->load->view('interface/useradmin/ReportPrint', $data);
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // PRIVATE HELPER: Farmers dropdown
    // ═══════════════════════════════════════════════════════════════════════════
    private function _getFarmersList()
    {
        return $this->db->query("
            SELECT f.id, CONCAT(p.first_name,' ',p.last_name) AS full_name
            FROM farmer f
            JOIN person p ON f.person_id = p.id
            WHERE f.is_active = 1
            ORDER BY p.first_name
        ")->result_array();
    }

    private function _getProduceTypes()
    {
        return $this->db->query("
            SELECT id, class_name FROM produce_classification ORDER BY class_name
        ")->result_array();
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // PRIVATE: Comprehensive Report
    // ═══════════════════════════════════════════════════════════════════════════
    private function _getComprehensiveReport($date_from, $date_to, $farmer_id = '', $produce_type = '', $order_status = '')
    {
        $df = $this->db->escape($date_from);
        $dt = $this->db->escape($date_to);

        // ── Summary counts ──────────────────────────────────────────────────────
        $where_farmer    = ($farmer_id    && $farmer_id    !== 'all') ? " AND t.farm_id IN (SELECT id FROM farmer_farm WHERE farmer_id = " . (int)$farmer_id . ")" : "";
        $where_status    = ($order_status && $order_status !== 'all') ? " AND ts.status = '" . strtoupper($order_status) . "'" : "";
        $where_produce   = ($produce_type && $produce_type !== 'all') ? " AND fp.produce_id IN (SELECT id FROM produce WHERE produce_classification_id = " . (int)$produce_type . ")" : "";

        $summary = $this->db->query("
            SELECT
                COUNT(DISTINCT t.id)                   AS total_orders,
                COALESCE(SUM(td.total_payment), 0)     AS total_revenue,
                COALESCE(SUM(td.to_admin), 0)          AS system_income,
                COUNT(DISTINCT t.person_id)            AS active_buyers
            FROM transaction t
            LEFT JOIN transaction_status ts   ON t.id = ts.transaction_id AND ts.is_latest = 1
            LEFT JOIN transaction_details td  ON t.id = td.transaction_id
            LEFT JOIN my_cart_farm_produce mcfp ON t.id = mcfp.transaction_id
            LEFT JOIN farm_produce fp         ON mcfp.farm_produce_id = fp.id
            LEFT JOIN transaction_cancel tc   ON t.id = tc.transaction_id
            WHERE tc.id IS NULL
            AND DATE(t.transaction_date) BETWEEN $df AND $dt
            $where_farmer $where_status $where_produce
        ")->row();

        $total_orders  = (int)($summary->total_orders  ?? 0);
        $total_revenue = (float)($summary->total_revenue ?? 0);
        $system_income = (float)($summary->system_income ?? 0);
        $active_buyers = (int)($summary->active_buyers  ?? 0);

        $completed = $this->db->query("
            SELECT COUNT(DISTINCT t.id) AS cnt
            FROM transaction t
            JOIN transaction_status ts ON t.id = ts.transaction_id AND ts.is_latest = 1
            LEFT JOIN transaction_cancel tc ON t.id = tc.transaction_id
            WHERE tc.id IS NULL AND ts.status = 'COMPLETED'
            AND DATE(t.transaction_date) BETWEEN $df AND $dt
            $where_farmer
        ")->row()->cnt ?? 0;

        $avg_order = $total_orders > 0 ? $total_revenue / $total_orders : 0;

        $total_users = $this->db->query("SELECT COUNT(1) AS cnt FROM user WHERE is_active = 1")->row()->cnt ?? 0;
        $total_farms = $this->db->query("SELECT COUNT(1) AS cnt FROM farmer_farm WHERE is_active = 1")->row()->cnt ?? 0;
        $total_farmers_cnt = $this->db->query("SELECT COUNT(1) AS cnt FROM farmer WHERE is_active = 1")->row()->cnt ?? 0;

        // Subscription income in period
        $sub_income = $this->db->query("
            SELECT COALESCE(SUM(ib.total_payment), 0) AS tot
            FROM invoice_billing ib
            WHERE ib.is_paid = 1
            AND DATE(ib.paid_at) BETWEEN $df AND $dt
        ")->row()->tot ?? 0;
        $system_income += (float)$sub_income;

        // ── Orders trend ────────────────────────────────────────────────────────
        $trend_rows = $this->db->query("
            SELECT DATE_FORMAT(t.transaction_date,'%b') AS month,
                   DATE_FORMAT(t.transaction_date,'%Y-%m') AS ym,
                   COUNT(t.id) AS orders,
                   COALESCE(SUM(td.total_payment),0) AS revenue
            FROM transaction t
            LEFT JOIN transaction_details td ON t.id = td.transaction_id
            LEFT JOIN transaction_cancel tc  ON t.id = tc.transaction_id
            WHERE tc.id IS NULL
            AND DATE(t.transaction_date) BETWEEN $df AND $dt
            $where_farmer
            GROUP BY DATE_FORMAT(t.transaction_date,'%Y-%m'), DATE_FORMAT(t.transaction_date,'%b')
            ORDER BY ym
        ")->result_array();

        // ── Top farmers ─────────────────────────────────────────────────────────
        $farmer_where = ($farmer_id && $farmer_id !== 'all') ? " AND f.id = " . (int)$farmer_id : "";
        $top_farmers = $this->db->query("
            SELECT CONCAT(p.first_name,' ',p.last_name) AS farmer_name,
                   COUNT(DISTINCT t.id) AS total_orders,
                   COALESCE(SUM(td.to_farmer),0) AS total_sales
            FROM transaction t
            JOIN farmer_farm ff  ON t.farm_id = ff.id
            JOIN farmer f        ON ff.farmer_id = f.id
            JOIN person p        ON f.person_id = p.id
            LEFT JOIN transaction_details td  ON t.id = td.transaction_id
            LEFT JOIN transaction_cancel tc   ON t.id = tc.transaction_id
            WHERE tc.id IS NULL
            AND DATE(t.transaction_date) BETWEEN $df AND $dt
            $farmer_where
            GROUP BY f.id, p.first_name, p.last_name
            ORDER BY total_sales DESC
            LIMIT 10
        ")->result_array();

        // ── Fast/slow products ──────────────────────────────────────────────────
        $prod_where = ($produce_type && $produce_type !== 'all') ? " AND p.produce_classification_id = " . (int)$produce_type : "";
        $products_base = "
            SELECT pr.name AS product_name,
                   pc.class_name AS category,
                   COALESCE(SUM(mcfp.qty),0) AS units_sold,
                   COALESCE(SUM(mcfp.sub_total),0) AS revenue
            FROM my_cart_farm_produce mcfp
            JOIN transaction t       ON mcfp.transaction_id = t.id
            LEFT JOIN transaction_cancel tc ON t.id = tc.transaction_id
            JOIN farm_produce fp      ON mcfp.farm_produce_id = fp.id
            JOIN produce pr           ON fp.produce_id = pr.id
            JOIN produce_classification pc ON pr.produce_classification_id = pc.id
            WHERE tc.id IS NULL
            AND DATE(t.transaction_date) BETWEEN $df AND $dt
            $prod_where
            GROUP BY pr.id, pr.name, pc.class_name
        ";

        $fast_products = $this->db->query($products_base . " ORDER BY units_sold DESC LIMIT 10")->result_array();
        $slow_products = $this->db->query($products_base . " ORDER BY units_sold ASC LIMIT 10")->result_array();

        // ── Orders list ─────────────────────────────────────────────────────────
        $orders_list = $this->db->query("
            SELECT t.id,
                   DATE_FORMAT(t.transaction_date,'%m-%d-%Y') AS txn_date,
                   CONCAT(p_buyer.first_name,' ',p_buyer.last_name) AS buyer,
                   CONCAT(p_farmer.first_name,' ',p_farmer.last_name) AS farmer_name,
                   ff.farm_name,
                   td.total_payment,
                   td.to_farmer,
                   td.to_admin,
                   ts.status,
                   tds.status AS delivery_status
            FROM transaction t
            JOIN person p_buyer      ON t.person_id = p_buyer.id
            LEFT JOIN farmer_farm ff ON t.farm_id = ff.id
            LEFT JOIN farmer f       ON ff.farmer_id = f.id
            LEFT JOIN person p_farmer ON f.person_id = p_farmer.id
            LEFT JOIN transaction_details td   ON t.id = td.transaction_id
            LEFT JOIN transaction_status ts    ON t.id = ts.transaction_id AND ts.is_latest = 1
            LEFT JOIN transaction_delivery_status tds ON t.id = tds.transaction_id AND tds.is_latest = 1
            LEFT JOIN transaction_cancel tc    ON t.id = tc.transaction_id
            WHERE tc.id IS NULL
            AND DATE(t.transaction_date) BETWEEN $df AND $dt
            $where_farmer $where_status $where_produce
            ORDER BY t.transaction_date DESC
            LIMIT 200
        ")->result_array();

        return compact(
            'total_orders', 'total_revenue', 'system_income', 'active_buyers',
            'completed', 'avg_order', 'total_users', 'total_farms', 'total_farmers_cnt',
            'trend_rows', 'top_farmers', 'fast_products', 'slow_products', 'orders_list'
        );
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // PRIVATE: Sales Report
    // ═══════════════════════════════════════════════════════════════════════════
    private function _getSalesReport($date_from, $date_to, $farmer_id = '', $produce_type = '', $order_status = '')
    {
        $df = $this->db->escape($date_from);
        $dt = $this->db->escape($date_to);
        $where_farmer  = ($farmer_id    && $farmer_id    !== 'all') ? " AND t.farm_id IN (SELECT id FROM farmer_farm WHERE farmer_id = " . (int)$farmer_id . ")" : "";
        $where_status  = ($order_status && $order_status !== 'all') ? " AND ts.status = '" . strtoupper($order_status) . "'" : "";
        $where_produce = ($produce_type && $produce_type !== 'all') ? " AND fp.produce_id IN (SELECT id FROM produce WHERE produce_classification_id = " . (int)$produce_type . ")" : "";

        $daily_sales = $this->db->query("
            SELECT DATE(t.transaction_date) AS sale_date,
                   COUNT(DISTINCT t.id) AS orders,
                   COALESCE(SUM(td.total_payment),0) AS revenue,
                   COALESCE(SUM(td.to_admin),0) AS admin_income,
                   COALESCE(SUM(td.to_farmer),0) AS farmer_income
            FROM transaction t
            LEFT JOIN transaction_status ts    ON t.id = ts.transaction_id AND ts.is_latest = 1
            LEFT JOIN transaction_details td   ON t.id = td.transaction_id
            LEFT JOIN my_cart_farm_produce mcfp ON t.id = mcfp.transaction_id
            LEFT JOIN farm_produce fp           ON mcfp.farm_produce_id = fp.id
            LEFT JOIN transaction_cancel tc    ON t.id = tc.transaction_id
            WHERE tc.id IS NULL
            AND DATE(t.transaction_date) BETWEEN $df AND $dt
            $where_farmer $where_status $where_produce
            GROUP BY DATE(t.transaction_date)
            ORDER BY sale_date ASC
        ")->result_array();

        $total_revenue = array_sum(array_column($daily_sales, 'revenue'));
        $total_orders  = array_sum(array_column($daily_sales, 'orders'));
        $avg_order     = $total_orders > 0 ? $total_revenue / $total_orders : 0;

        $top_products = $this->db->query("
            SELECT pr.name AS product_name, pc.class_name AS category,
                   COALESCE(SUM(mcfp.qty),0) AS units_sold,
                   COALESCE(SUM(mcfp.sub_total),0) AS revenue
            FROM my_cart_farm_produce mcfp
            JOIN transaction t  ON mcfp.transaction_id = t.id
            LEFT JOIN transaction_cancel tc ON t.id = tc.transaction_id
            JOIN farm_produce fp ON mcfp.farm_produce_id = fp.id
            JOIN produce pr      ON fp.produce_id = pr.id
            JOIN produce_classification pc ON pr.produce_classification_id = pc.id
            WHERE tc.id IS NULL
            AND DATE(t.transaction_date) BETWEEN $df AND $dt
            $where_farmer $where_produce
            GROUP BY pr.id, pr.name, pc.class_name
            ORDER BY revenue DESC LIMIT 10
        ")->result_array();

        $trend_rows = $daily_sales;

        return compact('daily_sales', 'total_revenue', 'total_orders', 'avg_order', 'top_products', 'trend_rows');
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // PRIVATE: Farmers Report
    // ═══════════════════════════════════════════════════════════════════════════
    private function _getFarmersReport($date_from, $date_to, $farmer_id = '')
    {
        $df = $this->db->escape($date_from);
        $dt = $this->db->escape($date_to);
        $where_farmer = ($farmer_id && $farmer_id !== 'all') ? " AND f.id = " . (int)$farmer_id : "";

        $farmers_list = $this->db->query("
            SELECT f.id AS farmer_id,
                   CONCAT(p.first_name,' ',p.last_name) AS farmer_name,
                   p.email_address AS email,
                   p.contact_num,
                   COUNT(DISTINCT ff.id) AS total_farms,
                   COUNT(DISTINCT t.id)  AS total_orders,
                   COALESCE(SUM(td.to_farmer),0) AS total_sales,
                   DATE_FORMAT(f.application_date,'%m-%d-%Y') AS joined_date,
                   CASE WHEN f.approved_by_person_id IS NOT NULL THEN 'Approved' ELSE 'Pending' END AS status
            FROM farmer f
            JOIN person p            ON f.person_id = p.id
            LEFT JOIN farmer_farm ff ON ff.farmer_id = f.id AND ff.is_active = 1
            LEFT JOIN transaction t  ON t.farm_id = ff.id
                AND DATE(t.transaction_date) BETWEEN $df AND $dt
            LEFT JOIN transaction_cancel tc  ON t.id = tc.transaction_id
            LEFT JOIN transaction_details td ON t.id = td.transaction_id
            WHERE f.is_active = 1 AND tc.id IS NULL
            $where_farmer
            GROUP BY f.id, p.first_name, p.last_name, p.email_address, p.contact_num, f.application_date, f.approved_by_person_id
            ORDER BY total_sales DESC
        ")->result_array();

        $total_farmers = count($farmers_list);
        $total_revenue = array_sum(array_column($farmers_list, 'total_sales'));
        $total_orders  = array_sum(array_column($farmers_list, 'total_orders'));
        $top_farmers   = array_slice($farmers_list, 0, 10);

        return compact('farmers_list', 'total_farmers', 'total_revenue', 'total_orders', 'top_farmers');
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // PRIVATE: Products Report
    // ═══════════════════════════════════════════════════════════════════════════
    private function _getProductsReport($date_from, $date_to, $produce_type = '')
    {
        $df = $this->db->escape($date_from);
        $dt = $this->db->escape($date_to);
        $where_produce = ($produce_type && $produce_type !== 'all') ? " AND pr.produce_classification_id = " . (int)$produce_type : "";

        $products_list = $this->db->query("
            SELECT pr.name AS product_name,
                   pc.class_name AS category,
                   COALESCE(SUM(mcfp.qty),0) AS units_sold,
                   COALESCE(SUM(mcfp.sub_total),0) AS revenue
            FROM my_cart_farm_produce mcfp
            JOIN transaction t      ON mcfp.transaction_id = t.id
            LEFT JOIN transaction_cancel tc ON t.id = tc.transaction_id
            JOIN farm_produce fp    ON mcfp.farm_produce_id = fp.id
            JOIN produce pr         ON fp.produce_id = pr.id
            JOIN produce_classification pc ON pr.produce_classification_id = pc.id
            WHERE tc.id IS NULL
            AND DATE(t.transaction_date) BETWEEN $df AND $dt
            $where_produce
            GROUP BY pr.id, pr.name, pc.class_name
            ORDER BY units_sold DESC
        ")->result_array();

        $fast_products = array_slice($products_list, 0, 10);
        $slow_products = array_slice(array_reverse($products_list), 0, 10);
        $total_units   = array_sum(array_column($products_list, 'units_sold'));
        $total_revenue = array_sum(array_column($products_list, 'revenue'));

        return compact('products_list', 'fast_products', 'slow_products', 'total_units', 'total_revenue');
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // PRIVATE: Orders Report
    // ═══════════════════════════════════════════════════════════════════════════
    private function _getOrdersReport($date_from, $date_to, $farmer_id = '', $order_status = '')
    {
        $df = $this->db->escape($date_from);
        $dt = $this->db->escape($date_to);
        $where_farmer = ($farmer_id    && $farmer_id    !== 'all') ? " AND t.farm_id IN (SELECT id FROM farmer_farm WHERE farmer_id = " . (int)$farmer_id . ")" : "";
        $where_status = ($order_status && $order_status !== 'all') ? " AND ts.status = '" . strtoupper($order_status) . "'" : "";

        $orders_list = $this->db->query("
            SELECT t.id,
                   DATE_FORMAT(t.transaction_date,'%m-%d-%Y %h:%i%p') AS txn_date,
                   CONCAT(p_buyer.first_name,' ',p_buyer.last_name) AS buyer,
                   CONCAT(p_farmer.first_name,' ',p_farmer.last_name) AS farmer_name,
                   ff.farm_name,
                   COALESCE(td.total_payment,0) AS total_payment,
                   COALESCE(td.to_farmer,0) AS to_farmer,
                   COALESCE(td.to_admin,0) AS to_admin,
                   ts.status,
                   tds.status AS delivery_status
            FROM transaction t
            JOIN person p_buyer       ON t.person_id = p_buyer.id
            LEFT JOIN farmer_farm ff  ON t.farm_id = ff.id
            LEFT JOIN farmer f        ON ff.farmer_id = f.id
            LEFT JOIN person p_farmer ON f.person_id = p_farmer.id
            LEFT JOIN transaction_details td   ON t.id = td.transaction_id
            LEFT JOIN transaction_status ts    ON t.id = ts.transaction_id AND ts.is_latest = 1
            LEFT JOIN transaction_delivery_status tds ON t.id = tds.transaction_id AND tds.is_latest = 1
            LEFT JOIN transaction_cancel tc    ON t.id = tc.transaction_id
            WHERE tc.id IS NULL
            AND DATE(t.transaction_date) BETWEEN $df AND $dt
            $where_farmer $where_status
            ORDER BY t.transaction_date DESC
        ")->result_array();

        $total_orders  = count($orders_list);
        $total_revenue = array_sum(array_column($orders_list, 'total_payment'));
        $avg_order     = $total_orders > 0 ? $total_revenue / $total_orders : 0;

        $status_counts = [];
        foreach ($orders_list as $o) {
            $s = $o['status'] ?? 'UNKNOWN';
            $status_counts[$s] = ($status_counts[$s] ?? 0) + 1;
        }

        $trend_rows = [];

        return compact('orders_list', 'total_orders', 'total_revenue', 'avg_order', 'status_counts', 'trend_rows');
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // PRIVATE: Users Report
    // ═══════════════════════════════════════════════════════════════════════════
    private function _getUsersReport($date_from, $date_to)
    {
        $df = $this->db->escape($date_from);
        $dt = $this->db->escape($date_to);

        $users_list = $this->db->query("
            SELECT u.id,
                   CONCAT(p.first_name,' ',p.last_name) AS full_name,
                   p.email_address AS email,
                   p.contact_num,
                   r.name AS role,
                   u.is_active AS status,
                   DATE_FORMAT(u.created_at,'%m-%d-%Y') AS joined_date
            FROM user u
            JOIN person p ON u.person_id = p.id
            JOIN role r   ON u.role_id = r.id
            WHERE DATE(u.created_at) BETWEEN $df AND $dt
            ORDER BY u.created_at DESC
        ")->result_array();

        $total_users   = count($users_list);
        $active_users  = count(array_filter($users_list, fn($u) => $u['status'] == 1));
        $pending_users = $total_users - $active_users;

        $role_counts = [];
        foreach ($users_list as $u) {
            $role_counts[$u['role']] = ($role_counts[$u['role']] ?? 0) + 1;
        }

        return compact('users_list', 'total_users', 'active_users', 'pending_users', 'role_counts');
    }
}

/* End of file Reports.php */
/* Location: ./application/controllers/useradmin/Reports.php */
