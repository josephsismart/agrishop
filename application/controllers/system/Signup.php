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
            ];
            if ($this->db->insert("public.person", $data_person)) {
                $inid = $this->db->insert_id();
                $data_user = [
                    "person_id" => $inid,
                    "username" => $username,
                    "password" => $password,
                    "role_id" => 1,
                ];

                if ($this->db->insert("public.user", $data_user)) {
                    $data_session = [
                        "agrishop_request_registration" => 0,
                        "agrishop_person_id" => $inid,
                        "agrishop_login_id" => $this->db->insert_id(),
                        "agrishop_login_uname" => $username,
                        "agrishop_login_level" => 1,
                        "agrishop_login_uri" => "user_consumer",
                        "agrishop_login_landing" => "index",
                        "agrishop_pass" => $password,
                        "agrishop_change_password" => 'f',
                        "agrishop_login_name" => $first_name . ' ' . $last_name,
                        "agrishop_login_img" => 'dist/img/media/personnel/default.jpg',
                    ];
                    $this->session->set_userdata($data_session);
                    $ret = ["success" => true];
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