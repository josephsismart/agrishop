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
            "page_title"        => "Dashboard",
            "current_location"  => "dashboard",
            "content"           =>  [$this->load->view('interface/' . $uri . '/Dashboard', [
                "dashboard" => $this->getDashboard(),
            ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }


    function getDashboard()
    {
        $revenue = $this->db->query("SELECT
                                        (SELECT count(*) FROM user) AS user,
                                        (SELECT count(*) FROM farmer) AS farmer,
                                        (SELECT count(*) FROM subscription_history WHERE is_active IS true) AS subscription,
                                        (SELECT COALESCE(sum(total_payment),0) FROM invoice_billing WHERE is_paid) AS revenue")->row();

        $data = [
            "user" => $revenue->user == null ? 0 : number_format($revenue->user, 0),
            "farmer" => $revenue->farmer == null ? 0 : number_format($revenue->farmer, 0),
            "subscription" => $revenue->subscription == null ? 0 : number_format($revenue->subscription, 0),
            "revenue" => $revenue->revenue == null ? 0 : number_format($revenue->revenue, 0),
        ];

        $farmer_revue = $this->db->query("SELECT concat(p.first_name,' ',p.last_name) AS farmer, sum(td.to_farmer) AS revenue FROM transaction t 
                                                LEFT JOIN farmer_farm ff ON t.farm_id = ff.id
                                                LEFT JOIN transaction_details td ON t.id = td.transaction_id
                                                LEFT JOIN transaction_cancel tc ON t.id= tc.transaction_id
                                                LEFT JOIN farmer f ON ff.farmer_id = f.id
                                                LEFT JOIN person p ON f.person_id = p.id
                                                WHERE tc.id IS NULL
                                                GROUP BY concat(p.first_name,' ',p.last_name)
                                                ORDER BY sum(td.to_farmer) DESC
                                                LIMIT 5")->result();
        $farmer_revenue = json_encode($farmer_revue);

        $data += [
            "top_farmer" => $farmer_revenue
        ];

        $farmer_remittance = $this->db->query("SELECT concat(p.first_name,' ',p.last_name) AS farmer, SUM(ib.total_payment) AS amount, ib.is_paid as status  FROM invoice_billing ib 
                                    LEFT JOIN farmer f ON ib.farmer_id = f.id
                                    LEFT JOIN person p ON f.person_id = p.id
                                    GROUP BY  concat(p.first_name,' ',p.last_name), ib.is_paid
                                    LIMIT 5")->result();
        $farmer_remittance = json_encode($farmer_remittance);

        $data += [
            "farmer_remittance" => $farmer_remittance
        ];

        $current_year = date('Y');
        $billing = $this->billing();
        $revnue_trend = $this->db->query("SELECT DATE_FORMAT(paid_at,'%b') AS month, sum(total_payment) revenue FROM ($billing) b 
                                                WHERE paid_at IS NOT NULL AND DATE_FORMAT(paid_at,'%Y')='$current_year'
                                                GROUP BY DATE_FORMAT(paid_at,'%b'),DATE_FORMAT(paid_at,'%m')
                                                ORDER BY DATE_FORMAT(paid_at,'%m')")->result();
        $revnue_trendGraph = json_encode($revnue_trend);

        $data += [
            "revnue_trendGraph" => $revnue_trendGraph
        ];

        $orderAnalytics = $this->db->query("SELECT DATE_FORMAT(mcfp.created_at,'%b') AS month, sum(qty) orders FROM my_cart_farm_produce mcfp 
                                                WHERE DATE_FORMAT(mcfp.created_at,'%Y') = '$current_year'
                                                GROUP BY DATE_FORMAT(mcfp.created_at,'%b'), DATE_FORMAT(mcfp.created_at,'%m')
                                                ORDER BY DATE_FORMAT(mcfp.created_at,'%m')")->result();
        $orderAnalyticsGraph = json_encode($orderAnalytics);

        $data += [
            "orderAnalyticsGraph" => $orderAnalyticsGraph
        ];

        return $data;
    }
}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */