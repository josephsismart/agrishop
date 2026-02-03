<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Signup extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
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
        // $sy = $this->getOnLoad()["sy_id"];
        $ret = false;
        $uri = "";

        $first_name = strtoupper(trim($this->input->post('firstname')));
        $last_name = strtoupper(trim($this->input->post('lastname')));
        $sex = $this->input->post('sex') == 'MALE' ? TRUE : FALSE; //strtoupper(trim($this->input->post('sex')));
        $birthDate = strtoupper(trim($this->input->post('birthDate')));
        $contact = strtoupper(trim($this->input->post('contact')));
        $email = strtoupper(trim($this->input->post('email')));
        $barangay = trim($this->input->post('barangay'));


        $username = $this->input->post('username');
        $password = md5($this->input->post('password')); //md5($this->input->post('password'));

        #for farmers
        $valid_id = $this->input->post('valid_id');
        $picFarmerID = $this->input->post('picFarmerID');
        $organization = $this->input->post('organization');

        $row1 = "";
        $row2 = "";
        $result = "";
        $data = [];

        if (!$first_name || !$last_name || !$username || !$password) {
            $ret = ["fill" => true];
        }

        if ($password != $this->input->post('password2')) {
            $ret = ["password" => true];
        }

        $chck = $this->db->query(
            "SELECT t1.* FROM public.user t1
                                    WHERE t1.username = ? LIMIT 1",
            array($username)
        );

        if ($chck->num_rows() > 0) {
            $ret = ["exist" => true];
        }

        if ($chck->num_rows() == 0) {
            $data_person = [
                "first_name" => $first_name,
                "last_name" => $last_name,
                "sex" => $sex,
                "birthdate" => $birthDate,
                "contact_num" => $contact,
                "email_address" => $email,
                "barangay_id" => $barangay,

            ];
            if ($this->db->insert("public.person", $data_person)) {
                $inid = $this->db->insert_id();

                if ($valid_id != "") {

                    $data_farmer = [
                        "person_id" => $inid,
                        "date_registered" => Date('Y-m-d'),
                        "presented_valid_id" => $valid_id,
                        "organization" => $organization,
                        "approved_by_person_id" => 1,
                        "approved_at" => Date('Y-m-d'),
                    ];

                    if (isset($_FILES['picFarmerID']) && $_FILES['picFarmerID']['error'] === UPLOAD_ERR_OK) {
                        // Normal upload
                        $upload = $this->uploadImg($_FILES['picFarmerID'], $first_name, 'farmer', 'picFarmerID');
                        $data_farmer += [
                            "id_img_path" => $upload
                        ];
                    }
                    if ($this->db->insert("public.farmer", $data_farmer)) {
                        $f_id = $this->db->insert_id();
                        $data_farmer_free_sub = [
                            "farmer_id" => $f_id,
                            "started_at" => Date('Y-m-d'),
                            "ended_at" => date('Y-m-d', strtotime('+2 months')),
                        ];
                        $this->db->insert("public.farmer_subscription_free", $data_farmer_free_sub);
                    }
                }

                $data_user = [
                    "person_id" => $inid,
                    "username" => $username,
                    "password" => $password,
                    "role_id" => $valid_id ? 3 : 2,
                ];

                if ($this->db->insert("public.user", $data_user)) {
                    $user_id = $this->db->insert_id();
                    $data_session = [
                        "agrishop_request_registration" => 0,
                        "agrishop_login_id" => $user_id,
                        "agrishop_login_uname" => $username,
                        "agrishop_login_level" => $valid_id ? 2 : 1,
                        "agrishop_login_uri" =>   $valid_id ? "userfarmer" : "",
                        "agrishop_login_landing" => $valid_id ? "FarmProduce" : "index",
                        "agrishop_pass" => $password,
                        "agrishop_change_password" => 'f',
                        "agrishop_login_name" => $first_name . ' ' . $last_name,
                        "agrishop_login_img" => 'dist/img/media/personnel/default.jpg',
                    ];

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

                                FROM public.user t1
                                LEFT JOIN public.role t2 ON t1.role_id = t2.id
                                LEFT JOIN public.person t3 ON t1.person_id = t3.id
                                LEFT JOIN public.farmer t4 ON t3.id = t4.person_id
                                LEFT JOIN (SELECT * FROM public.farmer_payment_method WHERE is_active = true and type='gcash') t5 ON t3.id = t5.person_id
                                
                                LEFT JOIN tbl_barangay b ON t3.barangay_id = b.id
                                LEFT JOIN tbl_citymun c ON b.citymun_id = c.id
                                LEFT JOIN tbl_province p ON c.province_id = p.id
                                LEFT JOIN tbl_region r ON p.region_id = r.id

                                WHERE t1.id = ? 
                                AND t1.is_active = true
                                LIMIT 1",
                        array($user_id)
                    );

                    $row1 = $chck->row();
                    $person_id = $row1->person_id;
                    $img = $row1->img_path ? base_url($row1->img_path) : base_url('dist/img/media/icons/1x1.png');
                    $qr = $row1->gcash_qr ? base_url($row1->gcash_qr) : base_url('dist/img/credit/gcash.png');


                    $data_session += [
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
                        "agrishop_person_id"        => $person_id, // $query->row('id'),

                        "agrishop_login_farmer_id" => $row1->farmer_id,
                        "agrishop_login_gcash_id" => $row1->gcash_id,
                        "agrishop_login_gcash_type" => $row1->gcash_type,
                        "agrishop_login_gcash_account_name" => $row1->gcash_account_name,
                        "agrishop_login_gcash_account_num" => $row1->gcash_account_num,
                        "agrishop_login_gcash_qr" => $qr,
                    ];


                    #check farmer subscription
                    if ($row1->farmer_id) {
                        $chck2 = $this->db->query("SELECT f.id,TO_CHAR(fsf.ended_at,'yyyy-mm-dd') AS free_end_at, fsf.confirmed AS free_confirmed, fsf.is_expired AS free_expired,
                                                    fs2.start_date, fs2.end_date,fs2.is_active,fs2.is_expired ,fs2.is_latest
                                                    FROM farmer AS f
                                                    JOIN farmer_subscription_free fsf ON f.id = fsf.farmer_id
                                                    LEFT JOIN farmer_subscription fs2 ON f.id= fs2.farmer_id
                                                    LEFT JOIN farmer_subscription_application fsa ON fs2.farmer_application_subscription_id = fsa.id
                                                    WHERE f.id = ?", array($row1->farmer_id));
                        if ($chck2->num_rows() > 0) {
                            $row2 = $chck2->row();

                            if (date('Y-m-d') > $row2->free_end_at && $row2->is_expired == false) {
                                $this->db->query("UPDATE public.farmer_subscription_free SET is_expired = true WHERE farmer_id = $row1->farmer_id");
                                $data_session += [
                                    "agrishop_login_sub_free_expired" => 't',
                                ];
                            } else if ($row2->is_expired == true) {
                                $data_session += [
                                    "agrishop_login_sub_free_expired" => 't',
                                ];
                            } else {
                                $data_session += [
                                    "agrishop_login_sub_free_expired" => 'f',
                                ];
                            }

                            $data_session += [
                                "agrishop_login_sub_free_end_at" => $row2->free_end_at,
                                "agrishop_login_sub_free_confirmed" => $row2->free_confirmed,
                                "agrishop_login_sub_start_date" => $row2->start_date,
                                "agrishop_login_sub_end_date" => $row2->end_date,
                                "agrishop_login_sub_is_active" => $row2->is_active,
                                "agrishop_login_sub_is_expired" => $row2->is_expired,
                                "agrishop_login_sub_is_latest" => $row2->is_latest,
                            ];
                        }
                    }

                    $this->session->set_userdata($data_session);

                    $level = $valid_id ? 2 : 1;
                    $defaultPassword = 'f';
                    $uri = $valid_id ? "userfarmer" : "";
                    $landing = $valid_id ? "FarmProduce" : "index";
                    $ret = [
                        "success" => true,
                        "redirect_to" => base_url($uri . '/' . $landing)
                    ];
                }
            }
        }
        echo json_encode($ret);
    }

    public function sendSMS()
    {
        $url = "https://api.engagespark.com/v1/sms";
        $phone = "639159566465";
        $apiKey = "a2927f88d9a6b51e1fb0a68f90d8c8b3fcdd972d";
        $apiToken = $apiKey;      // from dashboard
        $orgId    = 17879;                     // as per your org profile
        $toPhone  = $phone;                 // recipient, Philippines +63
        $fromId   = 'MYAPP';                          // your sender ID (string)
        $message  = 'Please unblock me';

        // Build payload
        $payload = [
            "orgId" => $orgId,
            "message" => $message,
            "fullPhoneNumber" => "+639159566465",
        ];

        // Init cURL
        $ch = curl_init('https://api.engagespark.com/v1/sms/contact');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Token ' . $apiToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        // Execute and get response
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        // Show result
        if ($err) {
            echo "cURL Error: $err";
        } else {
            echo "Response: $response";
        }
    }


    public function email_verification()
    {
        $action = 'send'; //$this->input->post('action'); // send | verify
        $email  = 'josephsismart@gmail.com'; //$this->input->post('email');
        $code   = $this->input->post('code');

        // Basic validation
        if (!$action || !$email) {
            echo json_encode(["status" => false, "message" => "Invalid request"]);
            return;
        }

        // Storage file path (you can change to DB if needed)
        $storage = APPPATH . "cache/email_verification.json";
        $records = file_exists($storage) ? json_decode(file_get_contents($storage), true) : [];

        // ---------------------------------------------------------
        // 1) SEND CODE
        // ---------------------------------------------------------
        if ($action == "send") {
            // generate 6 digit code
            $newCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // save hashed to prevent plaintext exposure
            $records[$email] = [
                "hash"      => password_hash($newCode, PASSWORD_DEFAULT),
                "expires"   => time() + 600, // 10 minutes
                "used"      => false,
                "attempts"  => 0
            ];

            // save file
            file_put_contents($storage, json_encode($records));

            // SEND EMAIL
            $this->load->library('email');
            $this->email->from('no-reply@agrishop.com', 'Agrishop');
            $this->email->to($email);
            $this->email->subject("Your Verification Code");
            $this->email->message("Your 6-digit verification code is: <b>$newCode</b><br>Valid for 10 minutes.");
            $this->load->library('email');

            $config = [
                'protocol'  => 'smtp',
                'smtp_host' => 'smtp.gmail.com',
                'smtp_port' => 587,
                'smtp_user' => 'sismarjoseph@gmail.com',
                'smtp_pass' => 'YOUR_APP_PASSWORD',
                'smtp_crypto' => 'tls',
                'mailtype'  => 'html',
                'charset'   => 'utf-8',
                'newline'   => "\r\n",
                'crlf'      => "\r\n"
            ];

            $this->email->initialize($config);
            $sent = $this->email->send();

            echo json_encode([
                "status" => $sent,
                "message" => $sent ? "Verification code sent to your email." : "Failed to send email."
            ]);
            return;
        }

        // ---------------------------------------------------------
        // 2) VERIFY CODE
        // ---------------------------------------------------------
        if ($action == "verify") {
            if (!isset($records[$email])) {
                echo json_encode(["status" => false, "message" => "No code requested for this email."]);
                return;
            }

            $row = $records[$email];

            // check expiry
            if ($row["expires"] < time()) {
                echo json_encode(["status" => false, "message" => "Code expired."]);
                return;
            }

            // check attempts
            if ($row["attempts"] >= 5) {
                echo json_encode(["status" => false, "message" => "Too many attempts."]);
                return;
            }

            // verify
            $records[$email]["attempts"]++;

            if (!password_verify($code, $row["hash"])) {
                file_put_contents($storage, json_encode($records));
                echo json_encode(["status" => false, "message" => "Invalid code."]);
                return;
            }

            // success — mark used
            $records[$email]["used"] = true;
            file_put_contents($storage, json_encode($records));

            echo json_encode(["status" => true, "message" => "Email verified successfully!"]);
            return;
        }
    }
}


/* End of file Login_admin.php */
/* Location: ./application/controllers/system/Login_admin.php */