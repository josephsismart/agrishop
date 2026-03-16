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
        $data = $this->system();
        $data += [
            "page_title"    => "Login",
            "current_location"  => "login"
        ];
        $this->load->view('interface/system/Login', $data);
    }

    public function request_login()
    {
        // $sy = $this->getOnLoad()["sy_id"];
        $username = $this->input->post('username');
        $password = md5($this->input->post('password')); //md5($this->input->post('password'));
        $row1 = "";
        $row2 = "";
        $result = "";
        $data = [];
        // Use prepared statements to prevent SQL injection
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
                                    t4.id as farmer_id,
                                    t4.free_sub_confirm,
                                    t4.is_active as farmer_active,
                                    t4.farmer_selling_type,

                                    t5.id as gcash_id,
                                    t5.type as gcash_type,
                                    t5.account_name as gcash_account_name,
                                    t5.number as gcash_account_num,
                                    t5.qr as gcash_qr,

                                    UPPER(CONCAT(
                                        b.description, ' ',
                                        c.description, ', ',
                                        p.description, ', ',
                                        r.region
                                    )) AS address_text,

                                    CASE 
                                        WHEN t4.id IS NOT NULL AND t4.approved_at IS NULL THEN 1 
                                        ELSE 0 
                                    END AS is_registered_farmer,

                                    'f' AS change_pwd,
                                    t1.is_active

                                FROM user t1
                                LEFT JOIN role t2 ON t1.role_id = t2.id
                                LEFT JOIN person t3 ON t1.person_id = t3.id
                                LEFT JOIN farmer t4 ON t3.id = t4.person_id
                                LEFT JOIN (SELECT * FROM farmer_payment_method WHERE is_active = true and type='gcash') t5 ON t3.id = t5.person_id

                                LEFT JOIN tbl_barangay b ON t3.barangay_id = b.id
                                LEFT JOIN tbl_citymun c ON b.citymun_id = c.id
                                LEFT JOIN tbl_province p ON c.province_id = p.id
                                LEFT JOIN tbl_region r ON p.region_id = r.id

                                WHERE t1.password = ? 
                                AND t1.username = ? 
                                AND t1.is_active = true
                                LIMIT 1",
            array($password, $username)
        );

        if ($chck->num_rows() > 0) {
            $row1 = $chck->row();
            $person_id = $row1->person_id;
            $img = $row1->img_path ? base_url($row1->img_path) : base_url('dist/img/media/icons/1x1.png');
            $qr = $row1->gcash_qr ? base_url($row1->gcash_qr) : base_url('dist/img/credit/gcash.png');
            if ($row1->is_active == true) {

                $data += [
                    "agrishop_login_first_name" => $row1->first_name,
                    "agrishop_login_middle_name" => $row1->middle_name,
                    "agrishop_login_last_name" => $row1->last_name,
                    "agrishop_login_birthdate" => $row1->birthdate,
                    "agrishop_login_sex" => $row1->sex,
                    "agrishop_login_email_address" => $row1->email_address,
                    "agrishop_login_contact_num" => $row1->contact_num,
                    "agrishop_login_barangay_id" => $row1->barangay_id,
                    "agrishop_login_address_text" => $row1->barangay_id ? $row1->address_text : "",
                    "agrishop_login_img_path" => $img,
                    "agrishop_request_registration" => $row1->is_registered_farmer,
                    "agrishop_person_id"        => $person_id, // $query->row('id'),
                    "agrishop_login_id"         => $row1->id, // $query->row('id'),
                    "agrishop_login_uname"      => $row1->username, // $query->row('username'),
                    "agrishop_login_level"      => $row1->level, // $value->level,
                    "agrishop_login_uri"        => ($row1->change_pwd == 't' ? "ud440aed189" : ($row1->level == 3 ? "useradmin" : ($row1->level == 1 ? "userconsumer" : ($row1->level == 2 ? "userfarmer" : "")))),

                    "agrishop_login_landing"    => $row1->change_pwd == 't' ? "changepassword" : ($row1->level == 2 || $row1->level == 3 ? "dashboard" : "dataentry"),
                    "agrishop_pass"             => $row1->password,
                    "agrishop_change_password"  => $row1->change_pwd,
                    "agrishop_login_name"       => 'AAAA', #$row2->full_name, // $this->personName($query->row('person_id'),'n'),
                    "agrishop_login_img"        => '', #$this->getImg($row2->img_path), // $this->personName($query->row('person_id'),'n'),
                    "agrishop_pending_trans_count" => $this->getTransactionStatus($person_id, 'PENDING', 'client'),

                    "agrishop_login_farmer_id" => $row1->farmer_id,
                    "agrishop_login_farmer_selling_type" => $row1->farmer_selling_type,
                    "agrishop_login_sub_free_confirmed" => $row1->free_sub_confirm,
                    "agrishop_login_farmer_active" => $row1->farmer_active,
                    "agrishop_login_gcash_id" => $row1->gcash_id,
                    "agrishop_login_gcash_type" => $row1->gcash_type,
                    "agrishop_login_gcash_account_name" => $row1->gcash_account_name,
                    "agrishop_login_gcash_account_num" => $row1->gcash_account_num,
                    "agrishop_login_gcash_qr" => $qr,
                    "agrishop_reserved_trans_count" => $row1->farmer_id ? $this->getTransactionStatus($row1->farmer_id, 'RESERVED', 'farmer') : 0,
                ];
                
                // Generate auto invoice/billing
                // $this->db->query("SELECT fn_generate_auto_invoice2()");

                $this->session->set_userdata($data);

                $level = $this->session->agrishop_login_level;
                $defaultPassword = $this->session->agrishop_change_password;
                $uri = $this->session->agrishop_login_uri;
                $landing = $this->session->agrishop_login_landing;

                if ($level != "") {
                    if ($defaultPassword == 't') {
                        redirect(base_url('ud440aed189/changepassword'));
                    } else {
                        $row1->level == 2 || $row1->level == 3 ? redirect(base_url($uri . '/' . $landing)) : redirect(base_url('index'));

                        // ($query->row('level')==1?redirect(base_url($uri.'/dataentry')):redirect(base_url($uri.'/dashboard')));
                    }
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
        // $this->userlog("USER HAS LOGGED OUT.");
        // if ($this->session->agrishop_login_lrn) {
        //     $this->learnerlog("LEARNER HAS LOGGED OUT.");
        // }

        $array_logout = [
            "agrishop_login_first_name" => '',
            "agrishop_login_middle_name" => '',
            "agrishop_login_last_name" => '',
            "agrishop_login_birthdate" => '',
            "agrishop_login_sex" => '',
            "agrishop_login_email_address" => '',
            "agrishop_login_contact_num" => '',
            "agrishop_login_barangay_id" => '',
            "agrishop_login_address_text" => '',
            "agrishop_login_img_path" => '',
            "agrishop_request_registration" => '',
            "agrishop_person_id" => '',
            "agrishop_login_id" => '',
            "agrishop_login_uname" => '',
            "agrishop_login_level" => '',
            "agrishop_login_uri" => '',
            "agrishop_login_landing" => '',
            "agrishop_pass" => '',
            "agrishop_change_password" => '',
            "agrishop_login_name" => '',
            "agrishop_login_img" => '',
            "agrishop_pending_trans_count" => '',
            "agrishop_login_farmer_id" => '',
            "agrishop_login_gcash_id" => '',
            "agrishop_login_gcash_type" => '',
            "agrishop_login_gcash_account_name" => '',
            "agrishop_login_gcash_account_num" => '',
            "agrishop_login_gcash_qr" => '',

        ];
        $this->session->unset_userdata($array_logout);
        $this->session->sess_destroy();
        redirect(base_url());
    }

    public function updateprofile()
    {
        $this->db->trans_begin();
        $true = ["success"   => true];
        $false = ["success"   => false];
        $upload = "";

        $person_id = $this->session->agrishop_person_id;
        $pic = $this->input->post("picProfile");
        // $personId = $this->input->post("personId");
        // $img_path = $this->input->post("img_path");
        $firstName = strtoupper($this->input->post("firstName"));
        $middleName = strtoupper($this->input->post("middleName"));
        $lastName = strtoupper($this->input->post("lastName"));
        $birthdate = $this->input->post("birthdate");
        $sex = $this->input->post("sex");
        $email = $this->input->post("email");
        $contactNumber = $this->input->post("contactNumber");
        $barangay = $this->input->post("barangayAll");
        $barangay_text = strtoupper($this->input->post("barangay_text"));


        $data = [
            "first_name" => $firstName,
            "middle_name" => $middleName,
            "last_name" => $lastName,
            "birthdate" => $birthdate,
            "sex" => $sex,
            "barangay_id" => $barangay,
            "email_address" => $email,
            "contact_num" => $contactNumber,
        ];

        $data_session = [
            "agrishop_login_first_name" => $firstName,
            "agrishop_login_middle_name" => $middleName,
            "agrishop_login_last_name" => $lastName,
            "agrishop_login_birthdate" => $birthdate,
            "agrishop_login_sex" => $sex,
            "agrishop_login_email_address" => $email,
            "agrishop_login_contact_num" => $contactNumber,
            "agrishop_login_barangay_id" => $barangay,
            "agrishop_login_address_text" => $barangay_text,
        ];

        if (isset($_FILES['picProfile']) && $_FILES['picProfile']['error'] === UPLOAD_ERR_OK) {
            // Normal upload
            $upload = $this->uploadImg($_FILES['picProfile'], $firstName . $middleName . $lastName, 'person', 'picProfile');
            $data += [
                "img_path" => $upload
            ];
            $data_session += [
                "agrishop_login_img_path" => base_url($upload),
            ];
        }

        if ($this->db->update("person", $data, "id = $person_id")) {
            $this->session->set_userdata($data_session);
            $true += ["message"   => "Successfully updated!"];
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

    public function updategcash()
    {
        $this->db->trans_begin();
        $true = ["success"   => true];
        $false = ["success"   => false];
        $upload = "";

        $person_id = $this->session->agrishop_person_id;
        $gcash_id = $this->session->agrishop_login_gcash_id;
        $pic = $this->input->post("picGcash");
        $accountNumber = strtoupper($this->input->post("accountNumber"));
        $accountName = strtoupper($this->input->post("accountName"));


        $data = [
            "number" => $accountNumber,
            "account_name" => $accountName,
        ];

        $data_session = [
            "agrishop_login_gcash_account_name" => $accountName,
            "agrishop_login_gcash_account_num" => $accountNumber,
        ];

        if (isset($_FILES['picGcash']) && $_FILES['picGcash']['error'] === UPLOAD_ERR_OK) {
            // Normal upload
            $upload = $this->uploadImg($_FILES['picGcash'], $accountNumber . $accountName, 'gcash', 'picGcash');
            $data += [
                "qr" => $upload
            ];
            $data_session += [
                "agrishop_login_gcash_qr" => base_url($upload),
            ];
        }

        if (!$gcash_id) {
            $data += [
                "person_id" => $person_id,
                "type" => "gcash",
            ];
        }

        $result = $gcash_id
            ? $this->db->update("farmer_payment_method", $data, ["id" => $gcash_id])
            : $this->db->insert("farmer_payment_method", $data);
        if ($result) {
            $this->session->set_userdata($data_session);
            $true += ["message"   => "Successfully updated!"];
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

/* End of file Login_admin.php */
/* Location: ./application/controllers/system/Login_admin.php */