<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Changepassword extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->redirect();
        $this->load->model('mainModel');
        $this->load->helper('date');
        date_default_timezone_set("Asia/Manila");
    }

    public function index()
    {
        $this->redirect_home();
        $data = $this->system();
        $uri = $this->session->schoolmis_login_uri;
        $data += [
            "page_title"    => "Change Password",
            "current_location"  => "changepassword",
        ];
        $this->load->view('interface/'.$uri.'/Changepassword', $data);
    }

    function saveUpdatePassword()
    {
        $this->db->trans_begin();
        $crrntpwd = md5($this->input->post("crrntpwd"));
        $pwd = md5($this->input->post("pwd"));
        $confirmpwd = md5($this->input->post("confirmpwd"));

        $sesspwd = $this->session->schoolmis_pass;
        $login_id = $this->session->schoolmis_login_id;
        $lvl = $this->session->schoolmis_login_level;
        $dateNow = $this->now();
        $true = ["success"   => true];
        $false = ["success"   => false];
        if (strlen($pwd) > 7 && strlen($confirmpwd) > 7 && ($crrntpwd == $sesspwd) && ($pwd == $confirmpwd)) {
            $data = [
                "password" => $confirmpwd,
                "change_pwd" => 'f',
                "updated_by" => $login_id,
                "date_updated" => $dateNow,
            ];
            if ($login_id) {
                $this->db->where('id', $login_id);
                if ($this->db->update("account.tbl_useraccount", $data)) {
                    $this->userlog("UPDATED PASSWORD ACCOUNT " . json_encode($data));
                    $this->session->schoolmis_login_uri = 
                    ($lvl == 1 ? "usersuperadmin" : 
                    ($lvl == 2 ? "useradmin" : 
                    ($lvl == 3 ? "userdepthead" : 
                    ($lvl == 4 ? "userschoolhead" : 
                    ($lvl == 5 ? "userschoolplanning" : 
                    ($lvl == 6 ? "userschooladmin" : 
                    ($lvl == 7 ? "userteacher" : 
                    ($lvl == 8 ? "userstudent" : 
                    ($lvl == 9 ? "uae1b19526823864438cd18ff6b4fbb8e" : "")))))))));
                    $this->session->schoolmis_change_password = 'f';
                    $this->session->schoolmis_pass = $confirmpwd;
                    $this->session->schoolmis_login_landing = 'dataentry';
                    $ret = $true;
                } else {
                    $ret = $false;
                }

                if ($this->db->trans_status() === false) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();
                    $this->db->query("REFRESH MATERIALIZED VIEW account.view_useraccount;");
                }
            } else {
                $ret = $false;
            }
        } else {
            $ret = $false;
        }

        echo json_encode($ret);
    }
}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */