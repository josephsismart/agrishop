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
        $this->redirect_home();
        $data = $this->system();
        $data += [
            "page_title"    => "Signup",
            "current_location"  => "signup",
        ];
        $this->load->view('interface/system/Signup', $data);
    }

    public function request_login()
    {
        $sy = $this->getOnLoad()["sy_id"];
        $username = $this->input->post('username');
        $password = md5($this->input->post('password')); //md5($this->input->post('password'));
        $row1 = "";
        $row2 = "";
        $result = "";
        $data = [];
        // Use prepared statements to prevent SQL injection
        $chck = $this->db->query("SELECT t1.* FROM account.view_useraccount t1
                                    WHERE t1.password = ? AND t1.username = ? AND t1.is_active = true LIMIT 1",
                                    array($password, $username));
        
        if ($chck->num_rows() > 0) {
            $row1 = $chck->row();
            $person_id = $row1->person_id;
            if ($row1->role_id != 8) {
                $result = $this->db->query("SELECT t1.* FROM profile.view_schoolpersonnel t1
                                            WHERE t1.person_id = ? AND t1.is_active_schl_personnel=1 LIMIT 1",
                                            array($person_id));
            }
            if ($row1->role_id == 8) {
                $result = $this->db->query("SELECT t1.* FROM sy$sy.bs_view_enrollment t1
                                            WHERE t1.person_id=? AND t1.schl_yr_id=? LIMIT 1",
                                            array($person_id,$sy));
                $rm_id = $result->row()->room_id;
                $result2 = $this->db->query("SELECT t3.* from building_sectioning.tbl_room t1
                                            LEFT JOIN building_sectioning.tbl_building t2 ON t1.building_id=t2.id
                                            LEFT JOIN profile.view_school t3 ON t2.school_id=t3.school_id 
                                            WHERE t1.id=?
                                            LIMIT 1",
                                            array($rm_id));
                $row3 = $result2->row();
                $data += [
                    "schoolmis_login_district"   => $row3->district_name, // $query->row('district_id'),
                    "schoolmis_login_schl_id"    => $row3->school_id_num, // $query->row('district_id'),
                    "schoolmis_login_school_id"  => $row3->school_id, // $query->row('district_id'),
                    "schoolmis_login_schl_name"  => $row3->school_name, // $query->row('district_id'),
                    "schoolmis_login_schl_type"  => $row3->school_type, // $query->row('district_id'),
                    "schoolmis_login_abbrv"      => $row3->abbr, // $query->row('district_id'),
                ];
            }


            if ($result->num_rows() > 0) {
                $row2 = $result->row();
                // foreach ($result as $key => $value) {
                if ($row1->is_active == true) {

                    $data += [
                        "schoolmis_login_id"         => $row1->id, // $query->row('id'),
                        "schoolmis_login_uname"      => $row1->username, // $query->row('username'),
                        "schoolmis_login_level"      => $row1->level, // $value->level,
                        "schoolmis_login_uri"        => ($row1->change_pwd == 't' ? "ud440aed189" : 
                        ($row1->level == 1 ? "usersuperadmin" : 
                        ($row1->level == 2 ? "useradmin" : 
                        ($row1->level == 3 ? "userdepthead" : 
                        ($row1->level == 4 ? "userschoolhead" : 
                        ($row1->level == 5 ? "userschoolplanning" : 
                        ($row1->level == 6 ? "userschooladmin" : 
                        ($row1->level == 7 ? "userteacher" : 
                        ($row1->level == 8 ? "userstudent" : 
                        ($row1->level == 9 ? "uae1b19526823864438cd18ff6b4fbb8e" : "")))))))))),
                        "schoolmis_login_landing"    => $row1->change_pwd == 't' ? "changepassword" : ($row1->level == 6 ? "dashboard" : "dataentry"), //($value->level==2?"dataentry":"dashboard"),
                        "schoolmis_pass"             => $row1->password, // $query->row('password'),
                        "schoolmis_change_password"  => $row1->change_pwd, // $query->row('change_password'),
                        "schoolmis_login_name"       => $row2->full_name, // $this->personName($query->row('person_id'),'n'),
                        "schoolmis_login_img"        => $this->getImg($row2->img_path), // $this->personName($query->row('person_id'),'n'),
                        // "schoolmis_person_exist"     => $value->userid,// 0,
                        // "schoolmis_login_prsnnl_Id"  => $row2->schoolpersonnel_id, // $this->personName($query->row('person_id'),'n'),
                        // "schoolmis_login_title"      => $row2->personal_title, // $this->personName($query->row('person_id'),'p'),
                        // "schoolmis_login_district"   => $row2->district_name, // $query->row('district_id'),
                        // "schoolmis_login_schl_id"    => $row2->school_id_num, // $query->row('district_id'),
                        // "schoolmis_login_schl_name"  => $row2->school_name, // $query->row('district_id'),
                        // "schoolmis_login_schl_type"  => $row2->school_type, // $query->row('district_id'),
                        // "schoolmis_login_abbrv"      => $row2->abbr,// $query->row('district_id'),
                    ];

                    if ($row1->role_id != 8) {
                        $data += [
                            "schoolmis_login_prsnnl_Id"  => $row2->schoolpersonnel_id, // $this->personName($query->row('person_id'),'n'),
                            "schoolmis_login_title"      => $row2->personal_title, // $this->personName($query->row('person_id'),'p'),
                            "schoolmis_login_district"   => $row2->district_name, // $query->row('district_id'),
                            "schoolmis_login_schl_id"    => $row2->school_id_num, // $query->row('district_id'),
                            "schoolmis_login_school_id"  => $row2->school_id, // $query->row('district_id'),
                            "schoolmis_login_schl_name"  => $row2->school_name, // $query->row('district_id'),
                            "schoolmis_login_schl_type"  => $row2->school_type, // $query->row('district_id'),
                            "schoolmis_login_abbrv"      => $row2->abbr, // $query->row('district_id'),
                            "schoolmis_login_dept_id"    => $row2->school_department_id, // $query->row('district_id'),
                            "schoolmis_login_dept_name"  => $row2->dept_name, // $query->row('district_id'),
                        ];
                    }

                    if ($row1->role_id == 8) {
                        $data += [
                            "schoolmis_login_learner_id" => $row2->learner_id, // $this->personName($query->row('person_id'),'n'),
                            "schoolmis_login_lrn"        => $row2->lrn, // $this->personName($query->row('person_id'),'p'),
                            "schoolmis_login_prsnnl_Id"  => $row2->person_id, // $query->row('district_id'),
                            "schoolmis_login_prsn_uuid"  => $row2->person_uuid, // $query->row('district_id'),
                            "schoolmis_login_rm_sec_id"  => $row2->rm_sec_id, // $query->row('district_id'),
                        ];
                    }

                    // $this->session->set_userdata($data);    
                    // $this->userlog("USER HAS LOGGED IN");
                    // redirect(base_url($this->session->schoolmis_login_uri.'/dashboard'));

                    $this->session->set_userdata($data);

                    if ($row1->role_id == 8) {
                        $this->learnerlog("LEARNER HAS LOGGED IN.");
                    }
                    $this->userlog("USER HAS LOGGED IN.");
                    $level = $this->session->schoolmis_login_level;
                    $defaultPassword = $this->session->schoolmis_change_password;
                    $uri = $this->session->schoolmis_login_uri;
                    $landing = $this->session->schoolmis_login_landing;
                    if ($level != "") {
                        if ($defaultPassword == 't') {
                            redirect(base_url('ud440aed189/changepassword'));
                        } else {
                            redirect(base_url($uri . '/' . $landing));
                            // ($query->row('level')==1?redirect(base_url($uri.'/dataentry')):redirect(base_url($uri.'/dashboard')));
                        }
                    }
                } else if ($row1->is_active == false) {
                    redirect(base_url() . 'login?login_attempt=' . md5(1));
                }
                // }
            } else {
                redirect(base_url() . 'login?login_attempt=' . md5(0));
            }
        } else {
            redirect(base_url() . 'login?login_attempt=' . md5(0));
        }
    }

    public function request_logout()
    {
        $this->userlog("USER HAS LOGGED OUT.");
        if ($this->session->schoolmis_login_lrn) {
            $this->learnerlog("LEARNER HAS LOGGED OUT.");
        }

        $array_logout = [
            "schoolmis_login_id"             => '',
            "schoolmis_login_uname"          => '',
            "schoolmis_login_level"          => '',
            "schoolmis_login_uri"            => '',
            "schoolmis_login_landing"        => '',
            "schoolmis_login_prsnnl_Id"      => '',


            //role_id != 8;
            "schoolmis_login_name"           => '',
            "schoolmis_login_title"          => '',
            "schoolmis_login_district"       => '',
            "schoolmis_login_schl_id"        => '',
            "schoolmis_login_schl_name"      => '',
            "schoolmis_login_schl_type"      => '',
            "schoolmis_login_abbrv"          => '',
            //role_id = 8;
            "schoolmis_login_learner_id"     => '',
            "schoolmis_login_lrn"            => '',
            "schoolmis_login_prsn_uuid"      => '',
            "schoolmis_login_rm_sec_id"      => '',

            "schoolmis_pass"                 => '',
            "schoolmis_change_password"      => '',
        ];
        $this->session->unset_userdata($array_logout);
        $this->session->sess_destroy();
        redirect(base_url());
    }
}

/* End of file Login_admin.php */
/* Location: ./application/controllers/system/Login_admin.php */