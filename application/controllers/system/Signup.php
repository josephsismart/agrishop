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
        if($this->session->agrishop_login_id!=""){
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
        $ret=false;
        $uri="";
        $first_name = strtoupper(trim($this->input->post('firstname')));
        $last_name = strtoupper(trim($this->input->post('lastname')));
        $username = $this->input->post('username');
        $password = md5($this->input->post('password')); //md5($this->input->post('password'));
        $row1 = "";
        $row2 = "";
        $result = "";
        $data = [];

        if (!$first_name || !$last_name || !$username || !$password) {
            $ret = ["fill"=>true];
        }

        $chck = $this->db->query("SELECT t1.* FROM public.user t1
                                    WHERE t1.username = ? LIMIT 1",
                                    array($username));

        if ($chck->num_rows() > 0) {
            $ret = ["exist"=>true];
        }

        if ($chck->num_rows() == 0) {
            $data_person = [
                "first_name" => $first_name,
                "last_name" => $last_name,
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
                    $ret = ["success"=>true];

                }
            }
        }
        echo json_encode($ret);
    }
}

/* End of file Login_admin.php */
/* Location: ./application/controllers/system/Login_admin.php */