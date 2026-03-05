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
        date_default_timezone_set("Asia/Manila");
    }

    public function index()
    {
        $this->redirect_home();
        $data = $this->system();
        $data += [
            "page_title"    => "Fresh Organic | Farm-to-Table",
            "current_location"  => "Index",
            "status" => $this->status()
        ];
        $this->load->view('interface/system/Index', $data);
    }

    public function status()
    {
        $interval = $this->input->get('interval');
        $person_id = $this->session->agrishop_person_id;
        if ($person_id != null) {
            $ratings = $this->db->query("SELECT count(1) AS count FROM transaction t1 
                                    JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
		                                    LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce mcfp
		                                    GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                                    LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                                    LEFT JOIN transaction_ratings tr ON tr.transaction_id = t1.id
                                    WHERE (t2.status = 'COMPLETED' AND tr.rating IS NULL) AND t1.person_id = $person_id")->row()->count;
        } else {
            $ratings = 0;
        }

        $data = [];
        $data['transaction_status_pending'] = $this->getTransactionStatus($person_id, 'PENDING', 'client', 'transaction_status');
        $data['transaction_status_reserved'] = $this->getTransactionStatus($person_id, 'RESERVED', 'client', 'transaction_status');
        $data['transaction_delivery_status_preparing'] = $this->getTransactionStatus($person_id, 'PREPARING', 'client', 'transaction_status');
        $data['transaction_delivery_status_pickup'] = $this->getTransactionStatus($person_id, 'TO_PICKUP', 'client', 'transaction_delivery_status');
        $data['transaction_delivery_status_delivery'] = $this->getTransactionStatus($person_id, 'TO_DELIVER', 'client', 'transaction_delivery_status');
        $data['transaction_ratings'] = $ratings > 0 ? $ratings : '';
        if ($interval == 'realtime') {
            echo json_encode($data);
            exit;
        }
        return $data;
    }

}

/* End of file Login_admin.php */
/* Location: ./application/controllers/system/Login_admin.php */