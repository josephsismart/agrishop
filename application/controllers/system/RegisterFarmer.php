<?php
defined('BASEPATH') or exit('No direct script access allowed');

class RegisterFarmer extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SQL_BIG_SELECTS=1');
    }

    public function index()
    {
        if($this->session->username!=""){
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

    public function register_farmer()
    {
        // echo json_encode(["success"=>true]);
        $session = $this->session;
        $person_id = $session->person_id;
        // echo $person_id;
        $chck = $this->db->query("SELECT t1.id as person_id FROM person t1
                                    JOIN farmer t2 ON t1.id = t2.person_id
                                    WHERE t1.id = ? LIMIT 1",
                                    array($person_id));
    
        if ($chck->num_rows() > 0) {
            $ret = ["exist"=>true];
        }
        if ($chck->num_rows() == 0) {
            $data_farmer=[
                "person_id"=>$person_id,
                "application_date"=>date("Y-m-d H:i:s")
            ];
            $this->db->insert("farmer", $data_farmer);
            
            $this->session->set_userdata('request_registration', 1);
            $ret = ["success"=>true];
        }

        echo json_encode($ret);
        // $sy = $this->getOnLoad()["sy_id"];
        // $ret=false;
        // $uri="";
        // $first_name = strtoupper(trim($this->input->post('firstname')));
        // $last_name = strtoupper(trim($this->input->post('lastname')));
        // $username = $this->input->post('username');
        // $password = md5($this->input->post('password')); //md5($this->input->post('password'));
        // $row1 = "";
        // $row2 = "";
        // $result = "";
        // $data = [];

        // if (!$first_name || !$last_name || !$username || !$password) {
        //     $ret = ["fill"=>true];
        // }

        // $chck = $this->db->query("SELECT t1.* FROM user t1
        //                             WHERE t1.username = ? LIMIT 1",
        //                             array($username));

        // if ($chck->num_rows() > 0) {
        //     $ret = ["exist"=>true];
        // }

        // if ($chck->num_rows() == 0) {
        //     $data_person = [
        //         "first_name" => $first_name,
        //         "last_name" => $last_name,
        //     ];
        //     if ($this->db->insert("person", $data_person)) {
        //         $inid = $this->db->insert_id();
        //         $data_user = [
        //             "person_id" => $inid,
        //             "username" => $username,
        //             "password" => $password,
        //             "role_id" => 1,
        //         ];

        //         if ($this->db->insert("user", $data_user)) {
        //             $data_session = [
        //                 "login_id" => $this->db->insert_id(),
        //                 "username" => $username,
        //                 "role_lvl" => 1,
        //                 "uri" => "user_consumer",
        //                 "landing" => "index",
        //                 "pass" => $password,
        //                 "change_password" => 'f',
        //                 "login_name" => $first_name . ' ' . $last_name,
        //                 "login_img" => 'dist/img/media/personnel/default.jpg',
        //             ];
        //             $this->session->set_userdata($data_session);
        //             $ret = ["success"=>true];
        //         }
        //     }
        // }
        // echo json_encode($ret);
    }
}

/* End of file Login_admin.php */
/* Location: ./application/controllers/system/Login_admin.php */