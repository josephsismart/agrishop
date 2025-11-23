<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dataentry extends MY_Controller
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
        $page_data = $this->system();
        $uri = $this->session->schoolmis_login_uri;
        $page_data += [
            "page_title"        => "Dataentry",
            "current_location"  => "dataentry",
            "content"           =>  [$this->load->view('interface/' . $uri . '/Dataentry', [
                "getOnLoad" => $this->getOnLoad(),
            ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }

    function saveSearchBatchEnroll()
    {
        $this->db->trans_begin();
        $data = [];
        parse_str($this->input->post("c"), $filter);
        $sy = $this->getOnLoad()["sy_id"];
        $rsid = $this->input->post("d");
        $learner_id = $filter['searchEnrollCheckBox'];
        $login_id = $this->session->schoolmis_login_id;
        $dateNow = $this->now();
        $true = ["success"   => true];
        $false = ["success"   => false];

        for ($i = 0; $i < count($learner_id); $i++) {
            $data[] = [
                'learner_id' => (int)$learner_id[$i],
                'room_section_id' => $rsid,
                'status_id' => 5,
                'enrollment_date' => $dateNow,
                'added_by' => $login_id,
            ];
        }
        // $b = json_encode($data);
        if (count($data) > 0 && $login_id) {
            if ($this->db->insert_batch("sy$sy.bs_tbl_learner_enrollment", $data)) {
                // $this->userlog("INSERTED NEW ENROLLMENT: " . $b);
                $ret = $true;
            }
        } else {
            $ret = $false;
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
        echo json_encode($ret);
    }

    // function saveEnrollmentInfo()
    // {
    //     $this->db->trans_begin();
    //     $id = $this->input->post("personId");
    //     $firstName = strtoupper($this->input->post("firstName"));
    //     $middleName = strtoupper($this->input->post("middleName"));
    //     $lastName = strtoupper($this->input->post("lastName"));
    //     $extName =      strtoupper($this->input->post("extName"));
    //     $sex = $this->input->post("sex");
    //     $birthdate = $this->input->post("birthdate");
    //     $brgy = $this->input->post("brgy");
    //     $prk = $this->input->post("prk");
    //     $homeAddress = strtoupper($this->input->post("homeAddress"));
    //     $emailAddress = $this->input->post("emailAddress");
    //     // $genAcc = $this->input->post("createAccount");
    //     // $createAccount = $genAcc!=""||$genAcc!=null?1:0;

    //     $emptype = $this->input->post("emptype");
    //     $personaltitle = $this->input->post("personaltitle");
    //     $empstatus = $this->input->post("empstatus");
    //     $login_id = $this->session->schoolmis_login_id;
    //     $inid = null;
    //     $dateNow = $this->now();
    //     $true = ["success"   => true];
    //     $false = ["success"   => false];

    //     $data = [
    //         "first_name" => $firstName,
    //         "middle_name" => $middleName,
    //         "last_name" => $lastName,
    //         "suffix" => $extName,
    //         "sex" => $sex,
    //         "birthdate" => $birthdate,
    //         "address_info" => $homeAddress,
    //         "barangay_id" => $brgy,
    //         "purok_id" => $prk != "" ? $prk : null,
    //         "address_info" => $homeAddress,
    //         "email_address" => $emailAddress,
    //         $id ? "updated_by" : "added_by" => $login_id,
    //         $id ? "date_updated" : "date_added" => $dateNow,
    //     ];
    //     if ($id && $firstName && $lastName && $homeAddress && $sex && $birthdate && $brgy && $login_id) {
    //         if (!$this->mainModel->update("profile.tbl_basicinfo", $data, "id", $id)) {
    //             // $this->userlog("UPDATED MEMBER/USER PERSON ".$partyType." ".$firstName.$middleName.$lastName);
    //             $ret = $true;
    //         }
    //     } else if ($firstName && $lastName && $homeAddress && $sex && $birthdate && $brgy && $login_id) {
    //         if ($this->db->insert("profile.tbl_basicinfo", $data)) {
    //             $inid = $this->db->insert_id();
    //             $data2 = [
    //                 "school_id" => 1,
    //                 "employee_type_id" => $emptype,
    //                 "basic_info_id" => $inid,
    //                 "personal_title_id" => $personaltitle,
    //                 "status_id" => $empstatus,
    //             ];
    //             $this->userlog("INSERTED MEMBER/USER PERSON " . $inid . " " . json_encode($data));
    //             if ($inid) {
    //                 if ($this->db->insert("profile.tbl_schoolpersonnel", $data2)) {
    //                     $this->userlog("INSERTED SCHOOL PERSON " . $inid . " " . json_encode($data2));
    //                     $ret = $true;
    //                 } else {
    //                     $ret = $false;
    //                 }
    //             } else {
    //                 $ret = $false;
    //             }
    //         }
    //     } else {
    //         $ret = $false;
    //     }

    //     if ($this->db->trans_status() === false) {
    //         $this->db->trans_rollback();
    //     } else {
    //         $this->db->trans_commit();
    //     }

    //     echo json_encode($ret);
    // }

    function saveEnrollmentInfo()
    {
        // $this->db->trans_begin();
        // $data = [];
        // parse_str($this->input->post("c"), $filter);
        // $sy = $this->getOnLoad()["sy_id"];
        // $rsid = $this->input->post("d");
        // $learner_id = $filter['searchEnrollCheckBox'];
        // $login_id = $this->session->schoolmis_login_id;
        // $dateNow = $this->now();
        $true = ["success"   => true];
        // $false = ["success"   => false];

        // for ($i = 0; $i < count($learner_id); $i++) {
        //     $data[] = [
        //         'learner_id' => (int)$learner_id[$i],
        //         'room_section_id' => $rsid,
        //         'status_id' => 5,
        //         'enrollment_date' => $dateNow,
        //         'added_by' => $login_id,
        //     ];
        // }
        // $b = json_encode($data);
        // if (count($data) > 0 && $login_id) {
        //     if ($this->db->insert_batch("sy$sy.bs_tbl_learner_enrollment", $data)) {
        //         $this->userlog("INSERTED NEW ENROLLMENT: " . $b);
                $ret = $true;
        //     }
        // } else {
        //     $ret = $false;
        // }

        // if ($this->db->trans_status() === false) {
        //     $this->db->trans_rollback();
        // } else {
        //     $this->db->trans_commit();
        // }
        echo json_encode($ret);
    }

    function saveGradesList()
    {
        $this->db->trans_begin();
        $data = [];
        $sy = $this->getOnLoad()["sy_id"];
        $sy_insert = (int)$sy;

        $en_id = $this->input->post("en_id");
        $rm_sec_id = $this->input->post("rm_sec_id");
        $qrtr = $this->getOnLoad()["qrtr"];
        $gradeLearner = $this->input->post("gradeLearner");
        $tmpGrade = 0;

        $login_id = $this->session->schoolmis_login_id;
        $dateNow = $this->now();
        $true = ["success"   => true];
        $false = ["success"   => false];
        $ret = $true;
        for ($i = 0; $i < count($en_id); $i++) {
            $en = $en_id[$i];
            $rm = $rm_sec_id[$i];
            $grades = $this->returnNull($gradeLearner[$i]);
            $search = $this->db->query("SELECT t1.* FROM sy$sy.bs_tbl_learner_grades t1
                                        WHERE t1.learner_enrollment_id=$en AND t1.room_section_id=$rm 
                                        AND t1.qrtr_id=$qrtr AND t1.sy_id=$sy");
            $srow = $search->row();

            if ($search->num_rows() > 0) {
                $tmpGrade = $srow->grade;
                if ($grades == $tmpGrade) {
                } else {
                    $update_data = array(
                        'grade' => $grades,
                        'date_updated' => $dateNow,
                        'updated_by' => $login_id
                    );

                    $this->db->where('learner_enrollment_id', $en);
                    $this->db->where('room_section_id', $rm);
                    $this->db->where('qrtr_id', $qrtr);
                    $this->db->where('sy_id', $sy);
                    if ($this->db->update("sy$sy.bs_tbl_learner_grades", $update_data)) {
                        $ret = $true;
                    } else {
                        $ret = $false;
                    }
                }
            } else {
                $data[] = [
                    'learner_enrollment_id' => $en,
                    'room_section_id' => $rm,
                    'qrtr_id' => $qrtr,
                    'grade' => $grades,
                    'date_added' => $dateNow,
                    'added_by' => $login_id,
                    'status_id' => 1,
                    'sy_id' => $sy_insert,
                ];
            }
        }
        // $b = json_encode($data);
        if (count($data) > 0 && $login_id) {
            if ($this->db->insert_batch("sy$sy.bs_tbl_learner_grades", $data)) {
                // $this->userlog("INSERTED GRADES: " . $b);
                $ret = $true;
            } else {
                $ret = $false;
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }

        echo json_encode($ret);
    }
}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */