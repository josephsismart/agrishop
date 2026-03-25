<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Signup extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SQL_BIG_SELECTS=1');
    }

    public function index()
    {
        if ($this->session->agrishop_login_id != "") {
            redirect(base_url() . 'index');
        }
        $this->redirect_home();
        $data = $this->system();
        $data += [
            "page_title"    => "Signup",
            "current_location"  => "signup",
        ];
        $this->load->view('interface/system/Signup', $data);
    }

    public function request_signup()
    {
        $signup_type = $this->input->post('signup_type'); // customer | farmer | supplier

        // ── Shared personal fields ─────────────────────────────
        $first_name   = strtoupper(trim($this->input->post('firstname')));
        $middle_name  = strtoupper(trim($this->input->post('middlename')));
        $last_name    = strtoupper(trim($this->input->post('lastname')));
        $sex          = $this->input->post('sex') == 'MALE' ? TRUE : FALSE;
        $birthDate    = trim($this->input->post('birthDate'));
        $contact      = trim($this->input->post('contact'));
        $email        = trim($this->input->post('email'));
        $barangay     = trim($this->input->post('barangay'));
        $address_info = strtoupper(trim($this->input->post('address_info'))); // ← NEW: address line
        $username     = trim($this->input->post('username'));
        $password     = md5($this->input->post('password'));
        $password2    = md5($this->input->post('password2'));

        // ── Validation ────────────────────────────────────────
        if (!$first_name || !$last_name || !$username || !$this->input->post('password')) {
            echo json_encode(["fill" => true]); return;
        }
        if ($password !== $password2) {
            echo json_encode(["password" => true]); return;
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["email_invalid" => true]); return;
        }

        // ── Check username duplicate ───────────────────────────
        $chck = $this->db->query("SELECT id FROM user WHERE username = ? LIMIT 1", [$username]);
        if ($chck->num_rows() > 0) {
            echo json_encode(["exist" => true]); return;
        }

        // ── Insert person ──────────────────────────────────────
        $data_person = [
            "first_name"    => $first_name,
            "middle_name"   => $middle_name,
            "last_name"     => $last_name,
            "sex"           => $sex,
            "birthdate"     => $birthDate ?: null,
            "contact_num"   => $contact,
            "email_address" => strtoupper($email),
            "barangay_id"   => $barangay ?: null,
            "address_info"  => $address_info, // ← house no / street / sitio
        ];

        if (!$this->db->insert("person", $data_person)) {
            echo json_encode(["success" => false]); return;
        }
        $person_id = $this->db->insert_id();

        // ── Role mapping ───────────────────────────────────────
        // role: 2=consumer, 3=farmer, 4=supplier
        $role_id   = 2;
        $uri       = "";
        $landing   = "index";
        $level     = 1;

        if ($signup_type === 'farmer') {
            $role_id = 3; $uri = "userfarmer"; $landing = "dashboard"; $level = 2;
        } elseif ($signup_type === 'supplier') {
            $role_id = 4; $uri = "usersupplier"; $landing = "dashboard"; $level = 4;
        }

        // ── Insert user ───────────────────────────────────────
        $data_user = [
            "person_id" => $person_id,
            "username"  => $username,
            "password"  => $password,
            "role_id"   => $role_id,
            "is_active" => true,
        ];
        if (!$this->db->insert("user", $data_user)) {
            echo json_encode(["success" => false]); return;
        }
        $user_id = $this->db->insert_id();

        // ── Farmer-specific insert ─────────────────────────────
        if ($signup_type === 'farmer') {
            $valid_id            = $this->input->post('valid_id');
            $organization        = $this->input->post('organization');
            $farmer_selling_type = $this->input->post('farmer_selling_type') ?: 1;

            $data_farmer = [
                "person_id"           => $person_id,
                "farmer_selling_type" => $farmer_selling_type,
                "application_date"    => date('Y-m-d H:i:s'),
                "is_active"           => 1,
                "presented_valid_id"  => $valid_id,
                "organization"        => $organization,
                "free_sub_confirm"    => 0,
            ];

            if (isset($_FILES['picFarmerID']) && $_FILES['picFarmerID']['error'] === UPLOAD_ERR_OK) {
                $upload = $this->uploadImg($_FILES['picFarmerID'], $first_name, 'farmer', 'picFarmerID');
                $data_farmer["id_img_path"] = $upload;
            }

            $this->db->insert("farmer", $data_farmer);
            $farmer_id = $this->db->insert_id();

            // Free 2-month subscription
            $this->db->insert("subscription_history", [
                "farmer_id"             => $farmer_id,
                "subscription_type"     => "FREE",
                "is_active"             => true,
                "subscription_from"     => date('Y-m-d'),
                "subscription_to"       => date('Y-m-d', strtotime('+2 months')),
                "billing_due_date"      => date('Y-m-d', strtotime('+2 months')),
                "grace_period_days"     => 7,
                "created_at"            => date('Y-m-d H:i:s'),
                "created_by_person_id"  => 1,
            ]);
        }

        // ── Supplier-specific insert ───────────────────────────
        if ($signup_type === 'supplier') {
            $valid_id     = $this->input->post('valid_id');
            $business_name = $this->input->post('business_name');

            $data_supplier = [
                "person_id"          => $person_id,
                "application_date"   => date('Y-m-d H:i:s'),
                "is_active"          => 1,
                "presented_valid_id" => $valid_id,
                "organization"       => $business_name,
                "free_sub_confirm"   => 0,
            ];

            if (isset($_FILES['picSupplierID']) && $_FILES['picSupplierID']['error'] === UPLOAD_ERR_OK) {
                $upload = $this->uploadImg($_FILES['picSupplierID'], $first_name, 'supplier', 'picSupplierID');
                $data_supplier["id_img_path"] = $upload;
            }

            $this->db->insert("supplier", $data_supplier);
            $supplier_id = $this->db->insert_id();

            // Free 2-month subscription
            $this->db->insert("supplier_subscription_history", [
                "supplier_id"           => $supplier_id,
                "subscription_type"     => "FREE",
                "is_active"             => true,
                "subscription_from"     => date('Y-m-d'),
                "subscription_to"       => date('Y-m-d', strtotime('+2 months')),
                "billing_due_date"      => date('Y-m-d', strtotime('+2 months')),
                "grace_period_days"     => 7,
                "created_at"            => date('Y-m-d H:i:s'),
                "created_by_person_id"  => 1,
            ]);
        }

        // ── Set session & respond ─────────────────────────────
        
        $session_data = [
            "agrishop_login_id"         => $user_id,
            "agrishop_login_uname"      => $username,
            "agrishop_login_level"      => $level,
            "agrishop_login_uri"        => $uri,
            "agrishop_login_landing"    => $landing,
            "agrishop_change_password"  => 'f',
            "agrishop_login_name"       => $first_name . ' ' . $last_name,
            "agrishop_person_id"        => $person_id,
            "agrishop_login_first_name" => $first_name,
            "agrishop_login_last_name"  => $last_name,
            "agrishop_login_farmer_id"  => $signup_type === 'farmer'   ? ($farmer_id   ?? null) : null,
            "agrishop_login_supplier_id"=> $signup_type === 'supplier' ? ($supplier_id ?? null) : null,
        ];
        $this->session->set_userdata($session_data);

        // Farmer and Supplier need admin approval first
        if ($signup_type === 'farmer' || $signup_type === 'supplier') {

            // Notify all admins of new signup
            $admins = $this->db->query("
                SELECT p.id FROM user u JOIN person p ON u.person_id = p.id
                WHERE u.role_id = 1 AND u.is_active = 1
            ")->result();
            foreach ($admins as $admin) {
                $this->notify(
                    $admin->id,
                    'New ' . ucfirst($signup_type) . ' Application 📋',
                    $first_name . ' ' . $last_name . ' has submitted a ' . $signup_type . ' registration. Please review their ID.',
                    'INFO', $signup_type, null
                );
            }

            echo json_encode([
                "success"     => true,
                "redirect_to" => base_url('pending'),   // <-- pending approval page
            ]);

        } else {
            // Customer — go straight to homepage
            echo json_encode([
                "success"     => true,
                "redirect_to" => base_url('index'),
            ]);
        }

    }
}


/* End of file Login_admin.php */
/* Location: ./application/controllers/system/Login_admin.php */