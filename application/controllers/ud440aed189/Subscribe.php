<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Subscribe extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SQL_BIG_SELECTS=1');
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

    function subscribe_application()
    {
        $this->db->trans_begin();
        $true = ["success"   => true];
        $false = ["success"   => false];
        $farmer_id = $this->session->agrishop_login_farmer_id;
        $billing = $this->billing();
        $query_pending = $this->db->query("SELECT b.id,  DATE_FORMAT(b.billing_due_date, '%Y-%m-%d') as billing_due_date, b.proof_img_path, b.status, f.is_active FROM ($billing) b
                                    LEFT JOIN farmer f ON b.farmer_id=f.id
                                    WHERE b.farmer_id = $farmer_id AND b.status!='PAID' AND b.payment_for ='SUBSCRIPTION'")->row();
        $id = $query_pending->id;


        $person_id = $this->session->agrishop_person_id;

        $data = [
            "invoice_billing_id" => $id,
            "created_by_person_id" => $person_id,
        ];
        if (isset($_FILES['gcash_qr']) && $_FILES['gcash_qr']['error'] === UPLOAD_ERR_OK) {
            // Normal upload
            $upload = $this->uploadImg($_FILES['gcash_qr'], $id, 'gcash', 'gcash_qr');
            $data += [
                "img_path" => $upload
            ];
        }

        if ($this->db->insert("invoice_billing_proof_of_payment", $data)) {
            $this->insert_billing_status($id, 'FOR_APPROVAL');
            $true += ["message"   => "Successfully created!"];
            $ret = $true;
        } else {
            $false += ["message"   => "Something went wrong!"];
            $ret = $false;
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }

        echo json_encode($ret);
    }

    public function subscribe_application2()
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