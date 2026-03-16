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
                "billing" => $this->billing_page(true),
                "initial_data" => $this->getInitialData(),
            ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }

    function getInitialData()
    {
        $revenue = $this->db->query("SELECT
                                        COUNT(CASE WHEN ibs.status = 'PAID' THEN 1 END) AS paid,
                                        COUNT(CASE WHEN ibs.status = 'PENDING' THEN 1 END) AS pending,
                                        COUNT(CASE WHEN ibs.status = 'FOR_APPROVAL' THEN 1 END) AS for_approval,
                                        COUNT(CASE WHEN ibs.status = 'REJECTED' THEN 1 END) AS rejected
                                    FROM invoice_billing_status ibs
                                    WHERE ibs.is_latest IS TRUE")->row();

        $data = [
            "paid" => number_format($revenue->paid, 0),
            "pending" => number_format($revenue->pending, 0),
            "for_approval" => number_format($revenue->for_approval, 0),
            "rejected" => number_format($revenue->rejected, 0),
        ];

        return $data;
    }


    function getBillingFarmers()
    {
        $requestData = $_REQUEST;
        $farmer_id  = $this->session->agrishop_login_farmer_id;
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Query to get total record count
        $billing = $this->billing();
        $thisQuery = $this->db->query("SELECT COUNT(1) AS total
                                            FROM ($billing) t1
                                        WHERE t1.is_paid = false 
                                        AND CONCAT(t1.payment_for, t1.total_payment, t1.status, t1.invoice_no) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'"
                                        );

        $totalRecords = $thisQuery->row()->total;
        $query = $this->db->query("SELECT id, proof_img_path, DATE_FORMAT(created_at,'%m-%d-%Y %h:%i%p') as billing_date, farmer,DATE_FORMAT(paid_at,'%d-%m-%Y') date_paid, payment_for,total_payment, status, status_remarks, invoice_no AS ref FROM ($billing) t1
                                    WHERE t1.is_paid = false
                                    AND CONCAT(t1.payment_for, t1.total_payment, t1.status, t1.invoice_no) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
                                    ORDER BY id DESC
                                    LIMIT $limit OFFSET $offset");

        $data = array();
        $cc = $offset + 1;
        foreach ($query->result() as $key => $value) {
            $is_a_v = $value->status;
            $status_ = $is_a_v == 'PENDING' ? "<span class='badge bg-warning'>PENDING</span>" : 
                    ($is_a_v == 'FOR_APPROVAL' ? "<span class='badge bg-info'>FOR_APPROVAL</span>" :
                     ($is_a_v == 'APPROVED' ? "<span class='badge bg-success'>APPROVED</span>" : "<span class='badge bg-danger'>REJECTED</span><br/><small class='wrap-text'>{$value->status_remarks}</small>"));
            $button = $is_a_v == 'FOR_APPROVAL' || $is_a_v == 'APPROVED' ? "<button title='View Details' class='btn btn-sm bg-black view-details' onclick='viewPaymentDetails(\"" . $value->proof_img_path . "\")'><i class='fa fa-paperclip'></i></button>" .
                " <button title='Approve Payment' class='btn btn-sm bg-success approve-payment' onclick='approvePayment(" . $value->id . "," . $value->total_payment . "," . "\"" . $value->payment_for . "\"," . "\"" . $value->ref . "\")'><i class='fa fa-check'></i></button>" .
                " <button title='Reject Payment' class='btn btn-sm bg-danger reject-payment' onclick='rejectPayment(" . $value->id . ")'><i class='fa fa-times'></i></button>"
                : "--";

            $data[] = array(
                $value->billing_date,
                "<b>" . $value->farmer . "</b>",
                $value->ref,
                $value->payment_for,
                "<div class='text-center'>" . $value->total_payment . "</div>",
                "<div class='text-center'>" . $status_ . "</div>",
                $button,
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

    function reject_payment()
    {
        $billing_id = $this->input->post('billing_id');
        $rejection_reason = $this->input->post('rejection_reason');
        $true = ["success"   => true];

        $this->insert_billing_status($billing_id, 'REJECTED', $rejection_reason);
        $true += ["message"   => "Successfully rejected!"];
        $ret = $true;


        echo json_encode($ret);
    }

    function accept_payment()
    {
        $billing_id = $this->input->post('billing_id');
        $true = ["success"   => true];

        $this->insert_billing_status($billing_id, 'PAID');
        $true += ["message"   => "Successfully accepted!"];
        $ret = $true;


        echo json_encode($ret);
    }

    function getBillingHistory()
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
                                        WHERE b.is_paid IS TRUE 
                                        AND CONCAT(DATE_FORMAT(b.paid_at,'%d-%m-%Y'),b.payment_for, b.total_payment, b.status, b.invoice_no) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'");

        $totalRecords = $thisQuery->row()->total;
        $query = $this->db->query("SELECT id, DATE_FORMAT(paid_at,'%m-%d-%Y %h:%i%p') as paid_at, proof_img_path, DATE_FORMAT(created_at,'%m-%d-%Y %h:%i%p') as billing_date, farmer,DATE_FORMAT(paid_at,'%d-%m-%Y') date_paid, payment_for,total_payment, status, status_remarks, invoice_no AS ref FROM ($billing) b
                                    WHERE b.is_paid IS TRUE
                                    AND CONCAT(DATE_FORMAT(b.paid_at,'%d-%m-%Y'),b.payment_for, b.total_payment, b.status, b.invoice_no) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
                                    ORDER BY b.paid_at DESC
                                    LIMIT $limit OFFSET $offset");

        $data = array();
        $cc = $offset + 1;
        foreach ($query->result() as $key => $value) {
            $is_a_v = $value->status;
            $status_ = $is_a_v == 'PAID' ? "<span class='badge bg-success'>PAID</span>" : "<span class='badge bg-gray'>" . $is_a_v . "</span>";
            $view= "<button title='View Details' class='btn btn-sm bg-black view-details' onclick='viewPaymentDetails(\"" . $value->proof_img_path . "\")'><i class='fa fa-paperclip'></i></button>";
            $data[] = array(
                $value->billing_date,
                "<b>" . $value->farmer . "</b>",
                $value->ref,
                $value->payment_for,
                "<div class='text-center'>" . $value->total_payment . "</div>",
                $value->paid_at,
                "<div class='text-center'>" . $status_ . "</div>",
                $view,
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
}

/* End of file Billing.php */
/* Location: ./application/controllers/userfarmer/Billing.php */