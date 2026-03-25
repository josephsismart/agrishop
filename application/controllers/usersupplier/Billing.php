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
            "content"           => [$this->load->view('interface/' . $uri . '/Billing', [
                "subscription"  => $this->getBillingPage(),
                "billing"       => $this->supplier_billing_count(),
            ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }

    // ── Supplier billing count (unpaid invoices) ──────────────
    public function supplier_billing_count()
    {
        $supplier_id = $this->session->agrishop_login_supplier_id;
        $count = $this->db->query("
            SELECT COUNT(1) AS count
            FROM supplier_invoice_billing
            WHERE supplier_id = ?
              AND is_paid = false
        ", [$supplier_id])->row();
        return ["count" => $count->count];
    }

    // ── Current billing summary for billing page ──────────────
    public function getBillingPage()
    {
        $supplier_id = $this->session->agrishop_login_supplier_id;

        // Latest subscription
        $subscription = $this->db->query("
            SELECT * FROM supplier_subscription_history
            WHERE supplier_id = ?
            ORDER BY id DESC LIMIT 1
        ", [$supplier_id])->row();

        // Unpaid invoices
        $unpaid = $this->db->query("
            SELECT * FROM supplier_invoice_billing
            WHERE supplier_id = ? AND is_paid = false
            ORDER BY billing_due_date ASC
        ", [$supplier_id])->result();

        return [
            "subscription"  => $subscription,
            "unpaid"        => $unpaid,
        ];
    }

    // ── Datatable: payment history ────────────────────────────
    public function getPaymentHistory()
    {
        $requestData = $_REQUEST;
        $supplier_id = $this->session->agrishop_login_supplier_id;
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        list($limit, $offset) = $this->calculatePagination($requestData);

        $totalRecords = $this->db->query("
            SELECT COUNT(1) AS total
            FROM supplier_invoice_billing
            WHERE supplier_id = ?
              AND is_paid = TRUE
              AND CONCAT(DATE_FORMAT(paid_at,'%d-%m-%Y'), payment_for, total_payment, invoice_no)
                  COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
        ", [$supplier_id])->row()->total;

        $query = $this->db->query("
            SELECT
                DATE_FORMAT(paid_at,'%d-%m-%Y') AS date_paid,
                payment_for,
                total_payment,
                invoice_no AS ref,
                'PAID' AS status
            FROM supplier_invoice_billing
            WHERE supplier_id = ?
              AND is_paid = TRUE
              AND CONCAT(DATE_FORMAT(paid_at,'%d-%m-%Y'), payment_for, total_payment, invoice_no)
                  COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
            ORDER BY paid_at DESC
            LIMIT $limit OFFSET $offset
        ", [$supplier_id]);

        $data = [];
        foreach ($query->result() as $value) {
            $status_badge = "<span class='badge bg-success'>PAID</span>";
            $data[] = [
                $value->date_paid,
                $value->ref,
                $value->payment_for,
                "<div class='text-center'>$status_badge</div>",
                "<div class='text-right'>₱ " . number_format($value->total_payment, 2) . "</div>",
            ];
        }

        echo json_encode([
            'draw'            => intval($requestData['draw']),
            'recordsTotal'    => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords),
            'data'            => $data,
        ]);
    }

    // ── Submit payment proof (GCash QR) ──────────────────────
    public function savePayBilling()
    {
        $this->db->trans_begin();
        $true  = ["success" => true];
        $false = ["success" => false];

        $id          = $this->input->post('id');
        $person_id   = $this->session->agrishop_person_id;
        $supplier_id = $this->session->agrishop_login_supplier_id;

        // Verify this billing belongs to this supplier
        $check = $this->db->query("
            SELECT id FROM supplier_invoice_billing
            WHERE id = ? AND supplier_id = ? AND is_paid = false
            LIMIT 1
        ", [$id, $supplier_id])->row();

        if (!$check) {
            echo json_encode(["success" => false, "message" => "Invalid billing record."]);
            return;
        }

        $data = [
            "invoice_billing_id"    => $id,
            "created_by_person_id"  => $person_id,
        ];

        if (isset($_FILES['gcash_qr']) && $_FILES['gcash_qr']['error'] === UPLOAD_ERR_OK) {
            $upload = $this->uploadImg($_FILES['gcash_qr'], $id, 'gcash', 'gcash_qr');
            $data["img_path"] = $upload;
        }

        if ($this->db->insert("invoice_billing_proof_of_payment", $data)) {
            $this->insert_billing_status($id, 'FOR_APPROVAL');
            $true  += ["message" => "Payment submitted! Waiting for approval."];
            $ret    = $true;
        } else {
            $false += ["message" => "Something went wrong!"];
            $ret    = $false;
        }

        $this->db->trans_status() === false
            ? $this->db->trans_rollback()
            : $this->db->trans_commit();

        echo json_encode($ret);
    }
}

/* End of file Billing.php */
/* Location: ./application/controllers/usersupplier/Billing.php */
