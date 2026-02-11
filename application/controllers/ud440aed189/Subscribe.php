<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Subscribe extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->redirect();
        $uri = $this->session->agrishop_login_uri;
        $data = $this->system();
        $data += [
            "page_title"    => "Subscribe",
            "current_location"  => "subscribe",
        ];
        $this->load->view('interface/' . $uri . '/Subscribe', $data);
    }

    public function subscribe_application()
    {
        $this->db->trans_begin();
        $person_id = $this->session->agrishop_person_id;
        $farmer_id = $this->session->agrishop_login_farmer_id;
        $dateNow = $this->now();
        $true = ["success"   => true];
        $false = ["success"   => false];

        $transaction_id = $this->input->post('trans_id');
        $status = $this->input->post('status');
        $type = $this->input->post('type');
        $text = $status == "PREPARING" ? "Order accepted" : ($status == "ORDER_IS_READY" ? "Order is ready" : ($status == "COMPLETED" ? "Successfully completed!" : "accept"));

        $data = [];
        if (isset($_FILES['proof_payment']) && $_FILES['proof_payment']['error'] === UPLOAD_ERR_OK) {
            // Normal upload
            $upload = $this->uploadImg($_FILES['proof_payment'], $farmer_id, 'farmer_subscription_application', 'proof_payment');
            $data += [
                "proof_of_payment" => $upload
            ];
        }

        $data += [
            'farmer_id' => $farmer_id,
            'applied_at' => $dateNow,
        ];
        if ($this->db->insert("farmer_subscription_application", $data)) {
            $true += ["message"   => "Subscription application received!"];
            $ret = $true;
        } else {
            $false += ["message"   => "Failed to receive subscription application!"];
            $ret = $false;
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }

        echo json_encode($ret);
    }
}

/* End of file Subscribe.php */
/* Location: ./application/controllers/Subscribe.php */