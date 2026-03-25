<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SQL_BIG_SELECTS=1');
    }

    public function index()
    {
        $this->redirect_home();
        $data  = $this->system();
        $data += ["page_title" => "Login", "current_location" => "login"];
        $this->load->view('interface/system/Login', $data);
    }

    public function request_login()
    {
        $username = $this->input->post('username');
        $password = md5($this->input->post('password'));
        $data     = [];

        // ── FIX: renamed province alias from 'p' to 'prov' to avoid
        //    conflict, and wrapped address CONCAT in COALESCE to prevent
        //    NULL barangay from breaking the whole query.
        //    Also added supplier LEFT JOIN for agrishop_login_supplier_id.
        $chck = $this->db->query(
            "SELECT
                t1.id,
                t1.password,
                t1.person_id,
                t1.username,
                t2.level,
                t3.first_name,
                t3.middle_name,
                t3.last_name,
                t3.birthdate,
                t3.sex,
                t3.email_address,
                t3.contact_num,
                t3.barangay_id,
                t3.img_path,

                t4.id                   AS farmer_id,
                t4.free_sub_confirm,
                t4.is_active            AS farmer_active,
                t4.farmer_selling_type,

                t5.id                   AS gcash_id,
                t5.type                 AS gcash_type,
                t5.account_name         AS gcash_account_name,
                t5.number               AS gcash_account_num,
                t5.qr                   AS gcash_qr,

                t_sup.id                AS supplier_id,

                COALESCE(UPPER(CONCAT(
                    b.description, ' ',
                    c.description, ', ',
                    prov.description, ', ',
                    r.region
                )), '') AS address_text,

                CASE
                    WHEN t4.id IS NOT NULL AND t4.approved_at IS NULL THEN 1
                    ELSE 0
                END AS is_registered_farmer,

                'f'         AS change_pwd,
                t1.is_active

            FROM user t1
            LEFT JOIN role           t2   ON t1.role_id     = t2.id
            LEFT JOIN person         t3   ON t1.person_id   = t3.id
            LEFT JOIN farmer         t4   ON t3.id          = t4.person_id
            LEFT JOIN (
                SELECT * FROM farmer_payment_method
                WHERE is_active = true AND type = 'gcash'
            )                        t5   ON t3.id          = t5.person_id
            LEFT JOIN supplier       t_sup ON t3.id         = t_sup.person_id
            LEFT JOIN tbl_barangay   b    ON t3.barangay_id = b.id
            LEFT JOIN tbl_citymun    c    ON b.citymun_id   = c.id
            LEFT JOIN tbl_province   prov ON c.province_id  = prov.id
            LEFT JOIN tbl_region     r    ON prov.region_id = r.id

            WHERE t1.password = ?
              AND t1.username = ?
              AND t1.is_active = true
            LIMIT 1",
            [$password, $username]
        );

        // ── Guard: query itself failed (DB error) ─────────────
        if (!$chck) {
            redirect(base_url() . 'login?login_attempt=' . md5(0));
            return;
        }

        if ($chck->num_rows() > 0) {
            $row1      = $chck->row();
            $person_id = $row1->person_id;
            $img       = $row1->img_path
                ? base_url($row1->img_path)
                : base_url('dist/img/media/icons/1x1.png');
            $qr        = $row1->gcash_qr
                ? base_url($row1->gcash_qr)
                : base_url('dist/img/credit/gcash.png');

            if ($row1->is_active == true) {

                // ── URI / landing per role level ───────────────
                if ($row1->change_pwd == 't') {
                    $uri     = "ud440aed189";
                    $landing = "changepassword";
                } elseif ($row1->level == 3) {
                    $uri     = "useradmin";
                    $landing = "dashboard";
                } elseif ($row1->level == 4) {
                    // Supplier: check if approved yet
                    $sup_approved = $this->db->query(
                        "SELECT approved_by_person_id FROM supplier WHERE person_id = ? AND is_active = 1 LIMIT 1",
                        [$person_id]
                    )->row();
                    if ($sup_approved && $sup_approved->approved_by_person_id) {
                        $uri     = "usersupplier";
                        $landing = "dashboard";
                    } else {
                        $uri     = "system";
                        $landing = "pending";
                    }
                } elseif ($row1->level == 2) {
                    // Farmer: check if approved yet
                    $far_approved = $this->db->query(
                        "SELECT approved_by_person_id FROM farmer WHERE person_id = ? AND is_active = 1 LIMIT 1",
                        [$person_id]
                    )->row();
                    if ($far_approved && $far_approved->approved_by_person_id) {
                        $uri     = "userfarmer";
                        $landing = "dashboard";
                    } else {
                        $uri     = "system";
                        $landing = "pending";
                    }
                } else {
                    $uri     = "";
                    $landing = "index";
                }

                $data = [
                    "agrishop_login_first_name"          => $row1->first_name,
                    "agrishop_login_middle_name"         => $row1->middle_name,
                    "agrishop_login_last_name"           => $row1->last_name,
                    "agrishop_login_birthdate"           => $row1->birthdate,
                    "agrishop_login_sex"                 => $row1->sex,
                    "agrishop_login_email_address"       => $row1->email_address,
                    "agrishop_login_contact_num"         => $row1->contact_num,
                    "agrishop_login_barangay_id"         => $row1->barangay_id,
                    "agrishop_login_address_text"        => $row1->address_text,
                    "agrishop_login_img_path"            => $img,
                    "agrishop_request_registration"      => $row1->is_registered_farmer,
                    "agrishop_person_id"                 => $person_id,
                    "agrishop_login_id"                  => $row1->id,
                    "agrishop_login_uname"               => $row1->username,
                    "agrishop_login_level"               => $row1->level,
                    "agrishop_login_uri"                 => $uri,
                    "agrishop_login_landing"             => $landing,
                    "agrishop_pass"                      => $row1->password,
                    "agrishop_change_password"           => $row1->change_pwd,
                    "agrishop_login_name"                => trim($row1->first_name . ' ' . $row1->last_name),
                    "agrishop_login_img"                 => $img,
                    "agrishop_pending_trans_count"       => $this->getTransactionStatus($person_id, 'PENDING', 'client'),

                    // Farmer-specific
                    "agrishop_login_farmer_id"           => $row1->farmer_id,
                    "agrishop_login_farmer_selling_type" => $row1->farmer_selling_type,
                    "agrishop_login_sub_free_confirmed"  => $row1->free_sub_confirm,
                    "agrishop_login_farmer_active"       => $row1->farmer_active,
                    "agrishop_reserved_trans_count"      => $row1->farmer_id
                        ? $this->getTransactionStatus($row1->farmer_id, 'RESERVED', 'farmer') : 0,

                    // GCash
                    "agrishop_login_gcash_id"            => $row1->gcash_id,
                    "agrishop_login_gcash_type"          => $row1->gcash_type,
                    "agrishop_login_gcash_account_name"  => $row1->gcash_account_name,
                    "agrishop_login_gcash_account_num"   => $row1->gcash_account_num,
                    "agrishop_login_gcash_qr"            => $qr,

                    // ── NEW: Supplier session ──────────────────
                    "agrishop_login_supplier_id"         => $row1->supplier_id,
                ];

                $this->session->set_userdata($data);

                // ── Redirect ───────────────────────────────────
                if ($row1->change_pwd == 't') {
                    redirect(base_url('ud440aed189/changepassword'));
                } elseif ($uri === 'system' && $landing === 'pending') {
                    // Unapproved farmer/supplier — go to pending page
                    redirect(base_url('pending'));
                } elseif (in_array($row1->level, [2, 3, 4])) {
                    redirect(base_url($uri . '/' . $landing));
                } else {
                    redirect(base_url('index'));
                }

            } else {
                redirect(base_url() . 'login?login_attempt=' . md5(0));
            }
        } else {
            redirect(base_url() . 'login?login_attempt=' . md5(0));
        }
    }

    public function request_logout()
    {
        $this->session->unset_userdata([
            "agrishop_login_first_name",
            "agrishop_login_middle_name",
            "agrishop_login_last_name",
            "agrishop_login_birthdate",
            "agrishop_login_sex",
            "agrishop_login_email_address",
            "agrishop_login_contact_num",
            "agrishop_login_barangay_id",
            "agrishop_login_address_text",
            "agrishop_login_img_path",
            "agrishop_request_registration",
            "agrishop_person_id",
            "agrishop_login_id",
            "agrishop_login_uname",
            "agrishop_login_level",
            "agrishop_login_uri",
            "agrishop_login_landing",
            "agrishop_pass",
            "agrishop_change_password",
            "agrishop_login_name",
            "agrishop_login_img",
            "agrishop_pending_trans_count",
            "agrishop_login_farmer_id",
            "agrishop_login_farmer_selling_type",
            "agrishop_login_sub_free_confirmed",
            "agrishop_login_farmer_active",
            "agrishop_reserved_trans_count",
            "agrishop_login_gcash_id",
            "agrishop_login_gcash_type",
            "agrishop_login_gcash_account_name",
            "agrishop_login_gcash_account_num",
            "agrishop_login_gcash_qr",
            "agrishop_login_supplier_id",
        ]);
        $this->session->sess_destroy();
        redirect(base_url());
    }

    public function updateprofile()
    {
        $this->db->trans_begin();
        $true  = ["success" => true];
        $false = ["success" => false];

        $person_id    = $this->session->agrishop_person_id;
        $firstName    = strtoupper($this->input->post("firstName"));
        $middleName   = strtoupper($this->input->post("middleName"));
        $lastName     = strtoupper($this->input->post("lastName"));
        $birthdate    = $this->input->post("birthdate");
        $sex          = $this->input->post("sex");
        $email        = $this->input->post("email");
        $contactNumber= $this->input->post("contactNumber");
        $barangay     = $this->input->post("barangayAll");
        $barangay_text= strtoupper($this->input->post("barangay_text"));
        $address_info = strtoupper($this->input->post("address_info"));

        $data = [
            "first_name"    => $firstName,
            "middle_name"   => $middleName,
            "last_name"     => $lastName,
            "birthdate"     => $birthdate,
            "sex"           => $sex,
            "barangay_id"   => $barangay ?: null,
            "email_address" => $email,
            "contact_num"   => $contactNumber,
            "address_info"  => $address_info,
        ];

        $data_session = [
            "agrishop_login_first_name"    => $firstName,
            "agrishop_login_middle_name"   => $middleName,
            "agrishop_login_last_name"     => $lastName,
            "agrishop_login_birthdate"     => $birthdate,
            "agrishop_login_sex"           => $sex,
            "agrishop_login_email_address" => $email,
            "agrishop_login_contact_num"   => $contactNumber,
            "agrishop_login_barangay_id"   => $barangay,
            "agrishop_login_address_text"  => $barangay_text,
        ];

        if (isset($_FILES['picProfile']) && $_FILES['picProfile']['error'] === UPLOAD_ERR_OK) {
            $upload = $this->uploadImg($_FILES['picProfile'], $firstName . $middleName . $lastName, 'person', 'picProfile');
            $data         += ["img_path"                => $upload];
            $data_session += ["agrishop_login_img_path" => base_url($upload)];
        }

        if ($this->db->update("person", $data, "id = $person_id")) {
            $this->session->set_userdata($data_session);
            $true += ["message" => "Successfully updated!"];
            $ret   = $true;
        } else {
            $false += ["message" => "Something went wrong!"];
            $ret    = $false;
        }

        $this->db->trans_status() === false
            ? $this->db->trans_rollback()
            : $this->db->trans_commit();

        echo json_encode($ret);
    }

    public function updategcash()
    {
        $this->db->trans_begin();
        $true  = ["success" => true];
        $false = ["success" => false];

        $person_id     = $this->session->agrishop_person_id;
        $gcash_id      = $this->session->agrishop_login_gcash_id;
        $accountNumber = strtoupper($this->input->post("accountNumber"));
        $accountName   = strtoupper($this->input->post("accountName"));

        $data = [
            "number"       => $accountNumber,
            "account_name" => $accountName,
        ];
        $data_session = [
            "agrishop_login_gcash_account_name" => $accountName,
            "agrishop_login_gcash_account_num"  => $accountNumber,
        ];

        if (isset($_FILES['picGcash']) && $_FILES['picGcash']['error'] === UPLOAD_ERR_OK) {
            $upload = $this->uploadImg($_FILES['picGcash'], $accountNumber . $accountName, 'gcash', 'picGcash');
            $data         += ["qr"                    => $upload];
            $data_session += ["agrishop_login_gcash_qr" => base_url($upload)];
        }

        if (!$gcash_id) {
            $data += ["person_id" => $person_id, "type" => "gcash", "is_active" => true, "created_at" => date('Y-m-d H:i:s')];
        }

        $result = $gcash_id
            ? $this->db->update("farmer_payment_method", $data, ["id" => $gcash_id])
            : $this->db->insert("farmer_payment_method", $data);

        if ($result) {
            if (!$gcash_id) {
                $data_session["agrishop_login_gcash_id"] = $this->db->insert_id();
            }
            $this->session->set_userdata($data_session);
            $true += ["message" => "Successfully updated!"];
            $ret   = $true;
        } else {
            $false += ["message" => "Something went wrong!"];
            $ret    = $false;
        }

        $this->db->trans_status() === false
            ? $this->db->trans_rollback()
            : $this->db->trans_commit();

        echo json_encode($ret);
    }

    // ── Notification proxies (called by routes) ───────────────
    public function getNotificationCount()   { parent::getNotificationCount(); }
    public function getUnreadNotifications() { parent::getUnreadNotifications(); }
    public function markNotificationsRead()  { parent::markNotificationsRead(); }
}

/* End of file Login.php */
/* Location: ./application/controllers/system/Login.php */