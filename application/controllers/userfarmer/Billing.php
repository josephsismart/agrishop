<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Billing extends MY_Controller
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
            "page_title"        => "Billing",
            "current_location"  => "billing",
            "content"           =>  [$this->load->view('interface/' . $uri . '/Billing', [
                "subscription" => $this->billing_page(),
                "billing" => $this->billing_page(true),
            ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }

    function getPaymentHistory()
    {
        $requestData = $_REQUEST;
        $farmer_id  = $this->session->agrishop_login_farmer_id;
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Query to get total record count
        $billing = $this->billing();
        $thisQuery = $this->db->query("SELECT COUNT(1) AS total
                                            FROM ($billing) b
                                        WHERE b.farmer_id=$farmer_id
                                        AND b.is_paid IS TRUE 
                                        AND CONCAT(DATE_FORMAT(b.paid_at,'%d-%m-%Y'),b.payment_for, b.total_payment, b.status, b.invoice_no) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'");

        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT DATE_FORMAT(b.paid_at,'%d-%m-%Y') date_paid, b.payment_for,b.total_payment, b.status, b.invoice_no AS ref FROM ($billing) b
                                    WHERE b.farmer_id=$farmer_id
                                    AND b.is_paid IS TRUE
                                    AND CONCAT(DATE_FORMAT(b.paid_at,'%d-%m-%Y'),b.payment_for, b.total_payment, b.status, b.invoice_no) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
                                    ORDER BY DATE_FORMAT(b.paid_at,'%d-%m-%Y') DESC
                                    LIMIT $limit OFFSET $offset");

        $data = array();
        $cc = $offset + 1;
        foreach ($query->result() as $key => $value) {
            $is_a_v = $value->status;
            $status_ = $is_a_v == 'PAID' ? "<span class='badge bg-success'>PAID</span>" : "<span class='badge bg-gray'>" . $is_a_v . "</span>";
            $data[] = array(
                $value->date_paid,
                $value->ref,
                $value->payment_for,
                "<div class='text-center'>" .$status_. "</div>",
                "<div class='text-right'>" .$value->total_payment. "</div>",
            );
        } // Prepare the response data in the required format
        $response = array(
            'draw' => intval($requestData['draw']),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords), // For simplicity, assuming no filtering is applied
            'data' => $data,
        );
        echo json_encode($response);
    }

    function savePayBilling()
    {
        $this->db->trans_begin();
        $true = ["success"   => true];
        $false = ["success"   => false];

        $id = $this->input->post('id');


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
}

/* End of file Billing.php */
/* Location: ./application/controllers/userfarmer/Billing.php */