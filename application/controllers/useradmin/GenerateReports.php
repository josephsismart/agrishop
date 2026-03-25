<?php
defined('BASEPATH') or exit('No direct script access allowed');

class GenerateReports extends MY_Controller
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
            "page_title"        => "Generate Reports",
            "current_location"  => "GenerateReports",
            "content"           =>  [$this->load->view('interface/' . $uri . '/GenerateReports', [
                "reports" => $this->reports(),
            ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }


    // public function __construct()
    // {
    //     parent::__construct();
    //     if (!$this->session->agrishop_login_level || $this->session->agrishop_login_level != 3) {
    //         redirect(base_url('login'));
    //     }
    // }

    // ──────────────────────────────────────────────
    //  Main Page
    // ──────────────────────────────────────────────
    public function reports()
    {
        $farmers = $this->db->query("
            SELECT f.id, CONCAT(p.first_name,' ',p.last_name) AS full_name
            FROM farmer f
            JOIN person p ON f.person_id = p.id
            WHERE f.is_active = 1
            ORDER BY p.first_name
        ")->result_array();

        $data = [
            'title'   => 'Generate Reports',
            'farmers' => $farmers,
        ];

        $this->load->view('interface/useradmin/layout/Header',  $data);
        $this->load->view('interface/useradmin/GenerateReports', $data);
        $this->load->view('interface/useradmin/layout/Footer');
    }

    // ──────────────────────────────────────────────
    //  AJAX – generate report data (returns JSON)
    // ──────────────────────────────────────────────
    public function generate()
    {
        $report_type = $this->input->post('report_type');
        $date_from   = $this->input->post('date_from');
        $date_to     = $this->input->post('date_to');
        $farmer_id   = $this->input->post('farmer_id');
        $order_status = $this->input->post('order_status');  // 'completed' | 'all'

        // ── Normalise dates ──────────────────────────
        if (!$date_from) $date_from = date('Y-m-01');
        if (!$date_to)   $date_to   = date('Y-m-t');
        $date_from_full = $date_from . ' 00:00:00';
        $date_to_full   = $date_to   . ' 23:59:59';

        $result = [];

        switch ($report_type) {

            // ── 1. COMPREHENSIVE ────────────────────
            case 'comprehensive':
            default:
                $result = $this->_comprehensive($date_from_full, $date_to_full, $farmer_id, $order_status);
                break;

            // ── 2. ORDERS REPORT ────────────────────
            case 'orders':
                $result = $this->_orders_report($date_from_full, $date_to_full, $farmer_id, $order_status);
                break;

            // ── 3. REVENUE / FINANCIAL ──────────────
            case 'revenue':
                $result = $this->_revenue_report($date_from_full, $date_to_full, $farmer_id);
                break;

            // ── 4. FARMERS PERFORMANCE ──────────────
            case 'farmers':
                $result = $this->_farmers_report($date_from_full, $date_to_full);
                break;

            // ── 5. PRODUCE / PRODUCT MOVEMENT ───────
            case 'produce':
                $result = $this->_produce_report($date_from_full, $date_to_full);
                break;

            // ── 6. SUBSCRIPTION / BILLING ───────────
            case 'subscription':
                $result = $this->_subscription_report($date_from_full, $date_to_full);
                break;
        }

        $result['report_type'] = $report_type;
        $result['date_from']   = $date_from;
        $result['date_to']     = $date_to;
        $result['generated_at'] = date('F j, Y \a\t g:i A');

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    // ═══════════════════════════════════════════════
    //  PRIVATE HELPERS
    // ═══════════════════════════════════════════════

    private function _comprehensive($from, $to, $farmer_id, $order_status)
    {
        // totals
        $where_farm  = $farmer_id ? "AND ff.farmer_id = $farmer_id" : '';
        $where_done  = ($order_status == 'completed') ? 'AND t.is_done = 1' : '';
        $where_cancel = "LEFT JOIN transaction_cancel tc ON t.id = tc.transaction_id AND tc.id IS NULL";

        $total_orders = $this->db->query("
            SELECT COUNT(DISTINCT t.id) AS cnt
            FROM transaction t
            LEFT JOIN transaction_cancel tc ON t.id = tc.transaction_id
            LEFT JOIN farmer_farm ff ON t.farm_id = ff.id
            WHERE tc.id IS NULL
              AND t.transaction_date BETWEEN '$from' AND '$to'
              $where_done
              $where_farm
        ")->row()->cnt ?? 0;

        $completed_orders = $this->db->query("
            SELECT COUNT(DISTINCT t.id) AS cnt
            FROM transaction t
            LEFT JOIN transaction_cancel tc ON t.id = tc.transaction_id
            LEFT JOIN farmer_farm ff ON t.farm_id = ff.id
            WHERE tc.id IS NULL AND t.is_done = 1
              AND t.done_at BETWEEN '$from' AND '$to'
              $where_farm
        ")->row()->cnt ?? 0;

        $total_revenue = $this->db->query("
            SELECT COALESCE(SUM(t.total_sale_amount),0) AS rev
            FROM transaction t
            LEFT JOIN transaction_cancel tc ON t.id = tc.transaction_id
            LEFT JOIN farmer_farm ff ON t.farm_id = ff.id
            WHERE tc.id IS NULL AND t.is_done = 1
              AND t.done_at BETWEEN '$from' AND '$to'
              $where_farm
        ")->row()->rev ?? 0;

        $system_income = $this->db->query("
            SELECT COALESCE(SUM(t.admin_sale_amount),0) AS inc
            FROM transaction t
            LEFT JOIN transaction_cancel tc ON t.id = tc.transaction_id
            WHERE tc.id IS NULL AND t.is_done = 1
              AND t.done_at BETWEEN '$from' AND '$to'
        ")->row()->inc ?? 0;

        $sub_income = $this->db->query("
            SELECT COALESCE(SUM(ib.total_payment),0) AS sub
            FROM invoice_billing ib
            WHERE ib.is_paid = 1
              AND ib.paid_at BETWEEN '$from' AND '$to'
        ")->row()->sub ?? 0;

        $total_system_income = $system_income + $sub_income;

        $active_users = $this->db->query("
            SELECT COUNT(*) AS cnt FROM person WHERE is_active = 1
        ")->row()->cnt ?? 0;

        $total_farms = $this->db->query("
            SELECT COUNT(*) AS cnt FROM farmer_farm WHERE is_active = 1
        ")->row()->cnt ?? 0;

        // monthly orders trend
        $trend = $this->db->query("
            SELECT DATE_FORMAT(t.transaction_date,'%b') AS month,
                   DATE_FORMAT(t.transaction_date,'%Y-%m') AS ym,
                   COUNT(DISTINCT t.id) AS orders
            FROM transaction t
            LEFT JOIN transaction_cancel tc ON t.id = tc.transaction_id
            WHERE tc.id IS NULL
              AND t.transaction_date BETWEEN '$from' AND '$to'
            GROUP BY ym, month
            ORDER BY ym
        ")->result_array();

        // top farmers
        $top_farmers = $this->db->query("
            SELECT CONCAT(p.first_name,' ',p.last_name) AS farmer_name,
                   COALESCE(SUM(t.total_sale_amount),0) AS total_sales
            FROM transaction t
            LEFT JOIN transaction_cancel tc ON t.id = tc.transaction_id
            LEFT JOIN farmer_farm ff ON t.farm_id = ff.id
            LEFT JOIN farmer fa ON ff.farmer_id = fa.id
            LEFT JOIN person p ON fa.person_id = p.id
            WHERE tc.id IS NULL AND t.is_done = 1
              AND t.done_at BETWEEN '$from' AND '$to'
            GROUP BY fa.id, farmer_name
            ORDER BY total_sales DESC
            LIMIT 10
        ")->result_array();

        // fast moving produce
        $fast_produce = $this->db->query("
            SELECT p.name, SUM(mcfp.qty) AS units_sold
            FROM my_cart_farm_produce mcfp
            LEFT JOIN transaction t ON mcfp.transaction_id = t.id
            LEFT JOIN transaction_cancel tc ON t.id = tc.transaction_id
            LEFT JOIN farm_produce fp ON mcfp.farm_produce_id = fp.id
            LEFT JOIN produce p ON fp.produce_id = p.id
            WHERE tc.id IS NULL AND t.is_done = 1
              AND t.done_at BETWEEN '$from' AND '$to'
            GROUP BY p.id, p.name
            ORDER BY units_sold DESC
            LIMIT 10
        ")->result_array();

        // slow moving produce
        $slow_produce = $this->db->query("
            SELECT p.name, SUM(mcfp.qty) AS units_sold
            FROM my_cart_farm_produce mcfp
            LEFT JOIN transaction t ON mcfp.transaction_id = t.id
            LEFT JOIN transaction_cancel tc ON t.id = tc.transaction_id
            LEFT JOIN farm_produce fp ON mcfp.farm_produce_id = fp.id
            LEFT JOIN produce p ON fp.produce_id = p.id
            WHERE tc.id IS NULL AND t.is_done = 1
              AND t.done_at BETWEEN '$from' AND '$to'
            GROUP BY p.id, p.name
            ORDER BY units_sold ASC
            LIMIT 10
        ")->result_array();

        return compact(
            'total_orders', 'completed_orders', 'total_revenue',
            'system_income', 'sub_income', 'total_system_income',
            'active_users', 'total_farms', 'trend',
            'top_farmers', 'fast_produce', 'slow_produce'
        );
    }

    private function _orders_report($from, $to, $farmer_id, $order_status)
    {
        $where_farm = $farmer_id ? "AND ff.farmer_id = $farmer_id" : '';
        $where_done = ($order_status == 'completed') ? 'AND t.is_done = 1' : '';

        $orders = $this->db->query("
            SELECT t.id,
                   CONCAT(p.first_name,' ',p.last_name) AS customer,
                   CONCAT(pf.first_name,' ',pf.last_name) AS farmer_name,
                   ff.farm_name,
                   t.total_sale_amount,
                   t.transaction_date,
                   t.done_at,
                   CASE WHEN tc.id IS NOT NULL THEN 'Cancelled'
                        WHEN t.is_done = 1 THEN 'Completed'
                        ELSE 'Pending' END AS status
            FROM transaction t
            LEFT JOIN transaction_cancel tc ON t.id = tc.transaction_id
            LEFT JOIN person p ON t.person_id = p.id
            LEFT JOIN farmer_farm ff ON t.farm_id = ff.id
            LEFT JOIN farmer fa ON ff.farmer_id = fa.id
            LEFT JOIN person pf ON fa.person_id = pf.id
            WHERE t.transaction_date BETWEEN '$from' AND '$to'
              $where_done $where_farm
            ORDER BY t.transaction_date DESC
        ")->result_array();

        $total_amount = array_sum(array_column($orders, 'total_sale_amount'));
        $total_count  = count($orders);

        return compact('orders', 'total_amount', 'total_count');
    }

    private function _revenue_report($from, $to, $farmer_id)
    {
        $where_farm = $farmer_id ? "AND ff.farmer_id = $farmer_id" : '';

        $monthly = $this->db->query("
            SELECT DATE_FORMAT(t.done_at,'%b %Y') AS month,
                   DATE_FORMAT(t.done_at,'%Y-%m') AS ym,
                   COALESCE(SUM(t.total_sale_amount),0) AS revenue,
                   COALESCE(SUM(t.admin_sale_amount),0) AS system_income,
                   COUNT(DISTINCT t.id) AS orders
            FROM transaction t
            LEFT JOIN transaction_cancel tc ON t.id = tc.transaction_id
            LEFT JOIN farmer_farm ff ON t.farm_id = ff.id
            WHERE tc.id IS NULL AND t.is_done = 1
              AND t.done_at BETWEEN '$from' AND '$to'
              $where_farm
            GROUP BY ym, month
            ORDER BY ym
        ")->result_array();

        $sub_monthly = $this->db->query("
            SELECT DATE_FORMAT(ib.paid_at,'%b %Y') AS month,
                   DATE_FORMAT(ib.paid_at,'%Y-%m') AS ym,
                   COALESCE(SUM(ib.total_payment),0) AS amount,
                   COUNT(*) AS count
            FROM invoice_billing ib
            WHERE ib.is_paid = 1
              AND ib.paid_at BETWEEN '$from' AND '$to'
            GROUP BY ym, month
            ORDER BY ym
        ")->result_array();

        $total_revenue   = array_sum(array_column($monthly, 'revenue'));
        $total_sys_inc   = array_sum(array_column($monthly, 'system_income'));
        $total_sub_inc   = array_sum(array_column($sub_monthly, 'amount'));
        $grand_total     = $total_sys_inc + $total_sub_inc;

        return compact('monthly', 'sub_monthly', 'total_revenue', 'total_sys_inc', 'total_sub_inc', 'grand_total');
    }

    private function _farmers_report($from, $to)
    {
        $farmers = $this->db->query("
            SELECT CONCAT(p.first_name,' ',p.last_name) AS farmer_name,
                   p.email,
                   COUNT(DISTINCT ff.id) AS farm_count,
                   COUNT(DISTINCT t.id) AS total_orders,
                   COALESCE(SUM(t.total_sale_amount),0) AS total_sales,
                   fa.date_registered
            FROM farmer fa
            JOIN person p ON fa.person_id = p.id
            LEFT JOIN farmer_farm ff ON fa.id = ff.farmer_id AND ff.is_active = 1
            LEFT JOIN transaction t ON t.farm_id = ff.id AND t.is_done = 1
                AND t.done_at BETWEEN '$from' AND '$to'
            LEFT JOIN transaction_cancel tc ON t.id = tc.transaction_id AND tc.id IS NULL
            WHERE fa.is_active = 1
            GROUP BY fa.id, farmer_name, p.email, fa.date_registered
            ORDER BY total_sales DESC
        ")->result_array();

        $total_farmers = count($farmers);
        $total_revenue = array_sum(array_column($farmers, 'total_sales'));

        return compact('farmers', 'total_farmers', 'total_revenue');
    }

    private function _produce_report($from, $to)
    {
        $produce = $this->db->query("
            SELECT p.name AS produce_name,
                   pc.name AS category,
                   SUM(mcfp.qty) AS units_sold,
                   COALESCE(AVG(fp.price),0) AS avg_price,
                   COALESCE(SUM(mcfp.qty * fp.price),0) AS total_revenue
            FROM my_cart_farm_produce mcfp
            LEFT JOIN transaction t ON mcfp.transaction_id = t.id
            LEFT JOIN transaction_cancel tc ON t.id = tc.transaction_id
            LEFT JOIN farm_produce fp ON mcfp.farm_produce_id = fp.id
            LEFT JOIN produce p ON fp.produce_id = p.id
            LEFT JOIN produce_classification pc ON p.produce_classification_id = pc.id
            WHERE tc.id IS NULL AND t.is_done = 1
              AND t.done_at BETWEEN '$from' AND '$to'
            GROUP BY p.id, produce_name, category
            ORDER BY units_sold DESC
        ")->result_array();

        $total_units   = array_sum(array_column($produce, 'units_sold'));
        $total_revenue = array_sum(array_column($produce, 'total_revenue'));

        return compact('produce', 'total_units', 'total_revenue');
    }

    private function _subscription_report($from, $to)
    {
        $billings = $this->db->query("
            SELECT CONCAT(p.first_name,' ',p.last_name) AS farmer_name,
                   ib.invoice_no,
                   ib.total_payment,
                   ib.billing_period_from,
                   ib.billing_period_to,
                   ib.billing_due_date,
                   CASE WHEN ib.is_paid = 1 THEN 'PAID' ELSE ibs.status END AS status,
                   ib.paid_at,
                   ib.created_at
            FROM invoice_billing ib
            JOIN farmer fa ON ib.farmer_id = fa.id
            JOIN person p ON fa.person_id = p.id
            LEFT JOIN invoice_billing_status ibs ON ib.id = ibs.invoice_billing_id AND ibs.is_latest = 1
            WHERE ib.created_at BETWEEN '$from' AND '$to'
            ORDER BY ib.created_at DESC
        ")->result_array();

        $total_billed  = array_sum(array_column($billings, 'total_payment'));
        $total_paid    = array_sum(array_map(
            fn($r) => $r['status'] == 'PAID' ? $r['total_payment'] : 0,
            $billings
        ));
        $total_pending = $total_billed - $total_paid;
        $total_count   = count($billings);

        return compact('billings', 'total_billed', 'total_paid', 'total_pending', 'total_count');
    }
}
