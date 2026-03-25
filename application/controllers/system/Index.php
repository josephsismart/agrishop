<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Index extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->load->model('mainModel');
        $this->load->helper('date');
        date_default_timezone_set('Asia/Manila');
    }

    public function index()
    {
        $this->redirect_home();
        $data  = $this->system();
        $data += [
            'page_title'       => 'Fresh Organic | Farm-to-Table',
            'current_location' => 'Index',
            'status'           => $this->status(),
            'top_farmers'      => $this->_getTopFarmers(),
            'active_promos'    => $this->_getActivePromos(),
            'live_stats'       => $this->_getLiveStats(),
        ];
        $this->load->view('interface/system/Index', $data);
    }

    // ── Real top farmers by completed orders ─────────────────
    private function _getTopFarmers()
    {
        $rows = $this->db->query("
            SELECT
                CONCAT(p.first_name, ' ', p.last_name) AS farmer_name,
                p.img_path,
                COUNT(DISTINCT t.id)                   AS orders_count,
                COALESCE(SUM(td.to_farmer), 0)         AS total_revenue,
                b.description                          AS barangay
            FROM transaction t
            JOIN farmer_farm ff ON t.farm_id = ff.id
            JOIN farmer f       ON ff.farmer_id = f.id
            JOIN person p       ON f.person_id = p.id
            LEFT JOIN transaction_details td ON t.id = td.transaction_id
            LEFT JOIN transaction_cancel tc  ON t.id = tc.transaction_id
            LEFT JOIN tbl_barangay b         ON p.barangay_id = b.id
            WHERE tc.id IS NULL
            GROUP BY f.id, p.first_name, p.last_name, p.img_path, b.description
            ORDER BY total_revenue DESC
            LIMIT 10
        ")->result_array();
        return $rows;
    }

    // ── Active promos for homepage ────────────────────────────
    private function _getActivePromos()
    {
        // Check if table exists first (may not be created yet)
        $table_exists = $this->db->query(
            "SELECT COUNT(1) AS c FROM information_schema.tables
             WHERE table_schema = DATABASE() AND table_name = 'promo_discount'"
        );
        if (!$table_exists || $table_exists->row()->c == 0) return [];

        $today = date('Y-m-d');
        $result = $this->db->query("
            SELECT pd.id, pd.title, pd.description,
                   pd.original_price, pd.discounted_price,
                   pd.discount_percent,
                   pd.img_path, pd.valid_until,
                   pr.name AS produce_name,
                   CONCAT(p.first_name, ' ', p.last_name) AS farmer_name,
                   p.img_path AS farmer_img
            FROM promo_discount pd
            LEFT JOIN farm_produce fp ON pd.farm_produce_id = fp.id
            LEFT JOIN produce pr      ON fp.produce_id = pr.id
            LEFT JOIN farmer f        ON pd.farmer_id = f.id
            LEFT JOIN person p        ON f.person_id = p.id
            WHERE pd.is_active = 1
              AND pd.valid_from <= '$today'
              AND pd.valid_until >= '$today'
            ORDER BY pd.discount_percent DESC
            LIMIT 8
        ");
        if (!$result) return [];
        $rows = $result->result_array();
        if (empty($rows)) return [];

        // Fallback: compute discount_percent if the column is 0/null
        foreach ($rows as &$r) {
            if (empty($r['discount_percent'])) {
                $orig = (float) $r['original_price'];
                $disc = (float) $r['discounted_price'];
                $r['discount_percent'] = ($orig > 0)
                    ? round((($orig - $disc) / $orig) * 100, 1)
                    : 0;
            }
        }
        return $rows;
    }

    // ── Live platform stats ───────────────────────────────────
    private function _getLiveStats()
    {
        $r = $this->db->query("
            SELECT
                (SELECT COUNT(*) FROM farmer WHERE approved_by_person_id IS NOT NULL AND is_active=1) AS farmers,
                (SELECT COUNT(*) FROM user WHERE is_active=1)                                          AS users,
                (SELECT COUNT(DISTINCT produce_id) FROM farm_produce)                                  AS products,
                (SELECT COUNT(*) FROM transaction WHERE is_done=1)                                     AS orders
        ")->row();
        return [
            'farmers'  => number_format($r->farmers ?? 0),
            'users'    => number_format($r->users ?? 0),
            'products' => number_format($r->products ?? 0),
            'orders'   => number_format($r->orders ?? 0),
        ];
    }

    public function status()
    {
        $interval  = $this->input->get('interval');
        $person_id = $this->session->agrishop_person_id;
        if ($person_id != null) {
            $ratings = $this->db->query("
                SELECT count(1) AS count FROM transaction t1
                JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
                LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce mcfp GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                LEFT JOIN transaction_ratings tr ON tr.transaction_id = t1.id
                WHERE (t2.status = 'COMPLETED' AND tr.rating IS NULL) AND t1.person_id = $person_id
            ")->row()->count;
        } else {
            $ratings = 0;
        }

        $data = [];
        $data['transaction_status_pending']              = $this->getTransactionStatus($person_id, 'PENDING', 'client', 'transaction_status');
        $data['transaction_status_reserved']             = $this->getTransactionStatus($person_id, 'RESERVED', 'client', 'transaction_status');
        $data['transaction_delivery_status_preparing']   = $this->getTransactionStatus($person_id, 'PREPARING', 'client', 'transaction_status');
        $data['transaction_delivery_status_pickup']      = $this->getTransactionStatus($person_id, 'TO_PICKUP', 'client', 'transaction_delivery_status');
        $data['transaction_delivery_status_delivery']    = $this->getTransactionStatus($person_id, 'TO_DELIVER', 'client', 'transaction_delivery_status');
        $data['transaction_ratings']                     = $ratings > 0 ? $ratings : '';

        if ($interval == 'realtime') { echo json_encode($data); exit; }
        return $data;
    }
}

/* End of file Index.php */
