<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dataentry extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->redirect();
        $this->load->model('mainModel');
        $this->load->library('excel');
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

    function savePersonnelInfo()
    {
        $this->db->trans_begin();
        $id = $this->input->post("personId");
        $sy_desc = $this->getOnLoad()["sy"];
        $firstName = strtoupper($this->input->post("firstName"));
        $middleName = strtoupper($this->input->post("middleName"));
        $lastName = strtoupper($this->input->post("lastName"));
        $extName = strtoupper($this->input->post("extName"));
        $sex = $this->input->post("sex");
        $birthdate = $this->input->post("birthdate");
        $brgy = $this->input->post("brgy");
        // $prk = $this->input->post("prk");
        $homeAddress = strtoupper($this->input->post("homeAddress"));
        $is_active = strtoupper($this->input->post("is_active"));
        // $emailAddress = $this->input->post("emailAddress");
        // $genAcc = $this->input->post("createAccount");
        // $createAccount = $genAcc!=""||$genAcc!=null?1:0;

        $emptype = $this->input->post("emptype");
        $personaltitle = $this->input->post("personaltitle");
        $empstatus = $this->input->post("empstatus");
        $employeeID = strtoupper($this->input->post("employeeID"));

        #other details
        $ioeName = strtoupper($this->input->post("ioeName"));
        $ioeAddress = strtoupper($this->input->post("ioeAddress"));
        $ioeNumber = strtoupper($this->input->post("ioeNumber"));

        $login_id = $this->session->schoolmis_login_id;
        $inid = null;
        $dateNow = $this->now();
        $true = ["success"   => true];
        $false = ["success"   => false];
        $cc = 0;

        $exist = $this->db->query("SELECT * FROM profile.tbl_schoolpersonnel t1 WHERE t1.employee_id='$employeeID'");
        if ($id && $exist->num_rows() > 0) {
            if ($exist->num_rows() > 1) {
                $cc = 1;
            } else {
                if ($exist->row()->employee_id == $employeeID) {
                    $cc = 0;
                } else {
                    $cc = 1;
                }
            }
        } else if ($exist->num_rows() > 0) {
            $cc = 1;
        }
        // $cc = $userId ? 0 : $exist->num_rows();
        if ($cc == 0) {

            $oi = '{"ioeName":"' . $ioeName . '","ioeAddress":"' . $ioeAddress . '","ioeNumber":"' . $ioeNumber . '"}';

            $data = [
                "first_name" => $firstName,
                "middle_name" => $middleName,
                "last_name" => $lastName,
                "suffix" => $extName,
                "sex" => $sex,
                "birthdate" => $birthdate ? $birthdate : null,
                "address_info" => $homeAddress,
                "barangay_id" => $brgy,
                "other_information" => $oi,
                // "purok_id" => $prk != "" ? $prk : null,
                // "address_info" => $homeAddress,
                // "email_address" => $emailAddress,
                $id ? "updated_by" : "added_by" => $login_id,
                $id ? "date_updated" : "date_added" => $dateNow,
            ];

            if (isset($_FILES['pic'])) {
                $data += [
                    "img_path" => $this->uploadImg($_FILES['pic'], $employeeID, 'personnel')
                ];
            }

            // else{
            //     $data += [
            //         "img_path" => 'dist/img/media/personnel/' . $sy_desc . '/' . $employeeID . '.jpg',
            //     ];
            // }

            // $data_e = json_encode($data);

            if ($id && $firstName && $lastName && $sex && $brgy && $login_id) {
                if (!$this->mainModel->update("profile.tbl_basicinfo", $data, "id", $id)) {
                    // $this->userlog("UPDATED PERSONNEL DETAILS " . $data_e);
                    $ret = $true;
                    $data2 = [
                        "school_id" => $this->session->schoolmis_login_school_id,
                        "employee_type_id" => $emptype,
                        "personal_title_id" => $personaltitle,
                        "is_active" => $is_active,
                        "status_id" => $empstatus,
                        "employee_id" => $employeeID,

                    ];
                    // $data2_e = json_encode($data2);
                    if (!$this->mainModel->update("profile.tbl_schoolpersonnel", $data2, "basic_info_id", $id)) {
                        // $this->userlog("UPDATED PERSONNEL DETAILS " . $data2_e);
                        $ret = $true;
                    } else {
                        $ret = $false;
                    }
                } else {
                    $ret = $false;
                }
            } else if ($firstName && $lastName && $sex && $brgy && $login_id) {
                if ($this->db->insert("profile.tbl_basicinfo", $data)) {
                    $inid = $this->db->insert_id();
                    $data2 = [
                        "school_id" => $this->session->schoolmis_login_school_id,
                        "employee_type_id" => $emptype,
                        "basic_info_id" => $inid,
                        "personal_title_id" => $personaltitle,
                        "is_active" => $is_active,
                        "status_id" => $empstatus,
                        "employee_id" => $employeeID,
                    ];
                    // $data2_e = json_encode($data2);
                    // $this->userlog("INSERTED PERSONNEL " . $inid . " " . json_encode($data));
                    if ($inid) {
                        if ($this->db->insert("profile.tbl_schoolpersonnel", $data2)) {
                            // $this->userlog("INSERTED PERSONNEL DETAILS " . $data2_e);
                            $ret = $true;
                        } else {
                            $ret = $false;
                        }
                    } else {
                        $ret = $false;
                    }
                }
            } else {
                $ret = $false;
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }
            $this->db->query("REFRESH MATERIALIZED VIEW profile.view_basicinfo;");
        } else {
            $ret = $false;
            $ret += [
                "exist"   => true,
                "message"   => "Employee ID already exist!"
            ];
        }

        echo json_encode($ret);
    }

    function savePersonnelAccount()
    {
        $this->db->trans_begin();
        $userId = $this->input->post("userId");
        $pid = $this->input->post("basicInfoId");
        $spid = $this->input->post("personnelId");
        $deptId = $this->input->post("department");
        $role = $this->input->post("role");
        $email = $this->input->post("email");
        $pwd = $this->input->post("pwd");
        $pw = md5(12345678);
        $confirmpwd = $this->input->post("confirmpwd");
        $login_id = $this->session->schoolmis_login_id;
        $dateNow = $this->now();
        $true = ["success"   => true];
        $false = ["success"   => false];
        $cc = 0;

        if ($role == 4 || $role == 6) {
            $deptId = null;
        }

        $deptId = ($deptId ? $deptId : null);

        // $confirmpwd $pwd
        $exist = $this->db->query("SELECT * FROM account.tbl_useraccount t1 WHERE t1.username='$email'");
        if ($userId && $exist->num_rows() > 0) {
            if ($exist->row()->username == $email) {
                $cc = 0;
            } else {
                $cc = 1;
            }
        } else if ($exist->num_rows() > 0) {
            $cc = 1;
        }
        // $cc = $userId ? 0 : $exist->num_rows();
        if ($cc == 0) {
            if ($confirmpwd && $pwd) {
                if ((strlen($pwd) > 7 || strlen($confirmpwd) > 7) && ($pwd == $confirmpwd)) {
                    $pw = md5($pwd);
                }
            }
            $data = [
                "basic_info_id" => $pid,
                "role_id" => $role,
                "username" => $email,
                "password" => $pw,
                "change_pwd" => $pw == md5(12345678) ? true : false,
                "is_active" => true,
                $userId ? "updated_by" : "added_by" => $login_id,
                $userId ? "date_updated" : "date_added" => $dateNow,
                // "added_by" => $login_id,
                // "date_added" => $dateNow,
            ];

            if ($pid && $role && $email) {
                if ($userId) {
                    $this->db->where('id', $userId);
                    if ($this->db->update("account.tbl_useraccount", $data)) {
                        // $this->userlog("UPDATED ACCOUNT " . json_encode($data));
                        $ret = $true;
                        // if ($deptId) {
                        $data1 = [
                            "school_department_id" => $deptId,
                        ];
                        $this->db->where('basic_info_id', $pid);
                        if ($this->db->update("profile.tbl_schoolpersonnel", $data1)) {
                            $ret = $true;
                        } else {
                            $ret = $false;
                        }
                        // }

                        if ($role == 3) {
                            $d = [
                                "department_head_person_id" => null,
                            ];
                            $this->db->where('department_head_person_id', $spid);
                            if ($this->db->update("profile.tbl_school_department", $d)) {
                                $data2 = [
                                    "department_head_person_id" => $spid,
                                    "updated_by" => $login_id,
                                    "date_updated" => $dateNow,
                                ];
                                if ($userId) {
                                    $this->db->where('id', $deptId);
                                    if ($this->db->update("profile.tbl_school_department", $data2)) {
                                        $ret = $true;
                                    } else {
                                        $ret = $false;
                                    }
                                } else {
                                    $ret = $false;
                                }
                            } else {
                                $ret = $false;
                            }
                        }
                    } else {
                        $ret = $false;
                    }
                } else {
                    if ($this->db->insert("account.tbl_useraccount", $data)) {
                        $inid = $this->db->insert_id();
                        // $this->userlog("CREATED NEW ACCOUNT " . $inid . " " . json_encode($data));
                        $ret = $true;

                        // if ($deptId) {
                        $data1 = [
                            "school_department_id" => $deptId,
                        ];
                        $this->db->where('basic_info_id', $pid);
                        if ($this->db->update("profile.tbl_schoolpersonnel", $data1)) {
                            $ret = $true;
                        } else {
                            $ret = $false;
                        }
                        // }

                        if ($role == 3) {
                            $data2 = [
                                "department_head_person_id" => $spid,
                                "updated_by" => $login_id,
                                "date_updated" => $dateNow,
                            ];
                            if ($inid) {
                                $this->db->where('id', $deptId);
                                if ($this->db->update("profile.tbl_school_department", $data2)) {
                                    $ret = $true;
                                } else {
                                    $ret = $false;
                                }
                            } else {
                                $ret = $false;
                            }
                        }
                    } else {
                        $ret = $false;
                    }
                }
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
            $ret += [
                "exist"   => true,
                "message"   => "Username already exist!"
            ];
        }
        echo json_encode($ret);
    }


    function saveGradeSecInfo()
    {
        $this->db->trans_begin();
        $id = $this->input->post("id");;
        // $gradelevel = $this->input->post("gradelevel");
        $sy = $this->getOnLoad()["sy_id"];
        $grade = $this->input->post("grade");
        $sectionName = strtoupper($this->input->post("sectionName"));
        // $programName = strtoupper($this->input->post("programName"));
        $program_strand = strtoupper($this->input->post("program_strand"));
        $sched = $this->input->post("sched");
        $login_id = $this->session->schoolmis_login_id;
        $true = ["success"   => true];
        $false = ["success"   => false];

        $data = [
            "room_id" => 1,
            "grd_lvl_id" => $grade,
            "schl_yr_id" => $sy,
            "sctn_nm" => $sectionName,
            // "program" => $programName,
            "program_strand_id" => $program_strand,
            "schedule_id" => $sched,
        ];
        if ($id && $grade && $sy && $sectionName && $sched && $login_id) {
            if (!$this->mainModel->update("building_sectioning.tbl_room_section", $data, "id", $id)) {
                $this->db->query("REFRESH MATERIALIZED VIEW sy$sy.bs_view_enrollment;");
                // $this->userlog("UPDATED MEMBER/USER PERSON ".$partyType." ".$firstName.$middleName.$lastName);
                $ret = $true;
            }
        } else if ($grade && $sy && $sectionName && $sched && $login_id) {
            if ($this->db->insert("building_sectioning.tbl_room_section", $data)) {
                // $this->userlog("INSERTED SECTION " . json_encode($data));
                $ret = $true;
            } else {
                $ret = $false;
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
        // $this->db->query("REFRESH MATERIALIZED VIEW building_sectioning.view_room_section;");
    }

    function saveGradeSubject()
    {
        $this->db->trans_begin();
        $data = [];
        $sy = $this->getOnLoad()["sy_id"];
        $gradelevel = $this->input->post("gradelevel");
        $sbjct = $this->input->post("subjectlist");
        $program_strand = $this->input->post("program_strand");
        $login_id = $this->session->schoolmis_login_id;
        $true = ["success"   => true];
        $false = ["success"   => false];

        for ($i = 0; $i < count($sbjct); $i++) {
            $sbject = $sbjct[$i];
            $search = $this->db->query("SELECT t1.*,t2.sbjct_cc FROM building_sectioning.view_subjectlist_grdlvl t1
                                        LEFT JOIN (SELECT t1.gradelvl_id,t1.subject_id, COUNT(1) sbjct_cc FROM building_sectioning.view_subject_grdlvl_personnel_assgnmnt t1
                                                    WHERE t1.schl_yr_id=$sy AND t1.schoolpersonnel_id IS NOT NULL
                                                    GROUP BY t1.subject_id,t1.gradelvl_id) t2 ON t1.subject_id=t2.subject_id AND t1.gradelvl_id=t2.gradelvl_id
                                                    WHERE t1.gradelvl_id=$gradelevel AND t1.subject_id=$sbject AND t1.schl_yr_id=$sy");
            // $search_exist_activate = $this->db->query("SELECT t1.* FROM building_sectioning.tbl_gradelvl_subject t1
            //                                 WHERE t1.gradelvl_id=$gradelevel AND t1.subject_id=$sbject AND t1.schl_yr_id=$sy AND t1.program_strand_id=$program_strand AND t1.is_active=TRUE");
            // $search_exist_inactivate = $this->db->query("SELECT t1.* FROM building_sectioning.tbl_gradelvl_subject t1
            //                                 WHERE t1.gradelvl_id=$gradelevel AND t1.subject_id=$sbject AND t1.schl_yr_id=$sy AND t1.program_strand_id=$program_strand AND t1.is_active=FALSE");
            // echo $search_exist_activate->num_rows();
            // echo $search_exist_inactivate->num_rows();
            if ($search->num_rows() == 0) {
                $data[] = [
                    "gradelvl_id"  =>  $gradelevel,
                    "subject_id"  =>  $sbject,
                    "schl_yr_id"  =>  $sy,
                    "program_strand_id"  =>  $program_strand,
                ];
                // if ($search_exist_activate->num_rows() > 0) {
                //     $this->db->query("UPDATE building_sectioning.tbl_gradelvl_subject SET is_active=FALSE WHERE gradelvl_id=$gradelevel AND subject_id=$sbject AND schl_yr_id=$sy AND program_strand_id=$program_strand AND is_active=TRUE");
                // }
                // if ($search_exist_inactivate->num_rows() > 0) {
                //     $this->db->query("UPDATE building_sectioning.tbl_gradelvl_subject SET is_active=TRUE WHERE gradelvl_id=$gradelevel AND subject_id=$sbject AND schl_yr_id=$sy AND program_strand_id=$program_strand AND is_active=FALSE");
                // }
            }
        }

        if (count($data) == 0) {
            $ret = $true;
        } else {
            // $b = json_encode($data);
            if (count($data) > 0 && $login_id) {
                if ($this->db->insert_batch("building_sectioning.tbl_gradelvl_subject", $data)) {
                    // $this->userlog("INSERTED GRADE LVL SUBJECTS: " . $b);
                    $ret = $true;
                } else {
                    $ret = $false;
                }
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }

        echo json_encode($ret);
        // $this->db->query("REFRESH MATERIALIZED VIEW building_sectioning.view_subject_grdlvl_personnel_assgnmnt;");
    }

    function saveSbjctAssPrsnnl__()
    {
        $this->db->trans_begin();
        $data = [];
        $valid = 0;
        $rmsecid = $this->input->post("rmsecid");
        $sbjct = $this->input->post("sbjct");
        $assignP = $this->input->post("schlpersonnel");
        $assignPerson = $assignP; #==""?null:$assignP;
        $advisory = $this->input->post("advisory");
        $login_id = $this->session->schoolmis_login_id;
        $dateNow = $this->now();
        $true = ["success"   => true];
        $false = ["success"   => false];

        for ($i = 0; $i < count($sbjct); $i++) {
            $sbject = $sbjct[$i];
            $assprsn = $assignPerson[$i];
            $search = $this->db->query("SELECT t1.* FROM building_sectioning.tbl_room_section_subject_assignment t1
                                        WHERE t1.room_section_id=$rmsecid AND t1.subject_id=$sbject");
            $srow = $search->row();

            if ($search->num_rows() > 0) {
                $tmpId = $srow->id;
                $tmpassprsn = $srow->schl_personnel_id;
                if ($assprsn == $tmpassprsn) {
                } else {
                    $update_data = array(
                        'schl_personnel_id' => $assprsn == "" ? null : $assprsn,
                        'advisory' => $sbject == $advisory ? true : false,
                    );
                    $this->db->where('id', $tmpId);
                    if ($this->db->update("building_sectioning.tbl_room_section_subject_assignment", $update_data)) {
                        $ret = $true;
                    } else {
                        $ret = $false;
                    }
                }
                $update_data = array(
                    'advisory' => $sbject == $advisory ? true : false,
                );
                $this->db->where('id', $tmpId);
                if ($this->db->update("building_sectioning.tbl_room_section_subject_assignment", $update_data)) {
                    $ret = $true;
                } else {
                    $ret = $false;
                }
            } else if ($assprsn) {
                $data[] = [
                    'room_section_id' => $rmsecid,
                    'subject_id' => $sbject,
                    'schl_personnel_id' => $assprsn,
                    'advisory' => $sbject == $advisory ? true : false,
                ];
            }
        }
        // $b = json_encode($data);
        if (count($data) > 0 && $login_id) {
            if ($this->db->insert_batch("building_sectioning.tbl_room_section_subject_assignment", $data)) {
                // $this->userlog("INSERTED SUBJECT ASSIGNMENT: " . $b);
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
        // $this->db->query("REFRESH MATERIALIZED VIEW building_sectioning.view_room_section;");
    }

    private function getTimetableIdByTime($time)
    {
        // Remove leading zero from hour (e.g., 05:40 AM → 5:40 AM)
        $time = preg_replace('/^0/', '', $time);

        $query = $this->db->query("SELECT id FROM building_sectioning.tbl_timetable WHERE time_char = ?", [$time]);
        $result = $query->row();

        return $result ? (int) $result->id : null;
    }

    function saveSbjctAssPrsnnl()
    {
        $this->db->trans_begin();
        $data = [];
        $blocks = [];
        $ret = ["success" => false];

        $rmsecid = $this->input->post("rmsecid");
        $sbjct = $this->input->post("subject_id");           // subject_id[]
        $assignPerson = $this->input->post("teacher_id");    // schl_personnel_id[]
        $advisory = $this->input->post("advisory");          // advisory radio (row index or subject_id)

        $is_teaching = $this->input->post("is_teaching");    // 'teaching' or 'non-teaching'
        $start_time_id = $this->input->post("start_time_id");
        $end_time_id = $this->input->post("end_time_id");
        $duration_id = $this->input->post("duration_id");

        $days_selected = $this->input->post("day_of_week");  // shared for all rows

        $login_id = $this->session->schoolmis_login_id;
        $dateNow = $this->now();
        for ($i = 0; $i < count($sbjct); $i++) {
            $sbject = $sbjct[$i];
            $assprsn = $assignPerson[$i];

            // Check if assignment already exists
            $search = $this->db->query("SELECT t1.* FROM building_sectioning.tbl_room_section_subject_assignment t1
                                        WHERE t1.room_section_id = ? AND t1.subject_id = ?", [$rmsecid, $sbject]);
            $srow = $search->row();
            if ($search->num_rows() > 0) {
                $tmpId = $srow->id;
                $tmpassprsn = $srow->schl_personnel_id;

                if ($assprsn != $tmpassprsn) {
                    $update_data = [
                        'schl_personnel_id' => $assprsn ?: null,
                        'advisory' => ($advisory == $i || $advisory == $sbject),
                    ];
                    $this->db->where('id', $tmpId);
                    $this->db->update("building_sectioning.tbl_room_section_subject_assignment", $update_data);
                }

                // Always ensure advisory flag is correct
                $this->db->where('id', $tmpId);
                $this->db->update("building_sectioning.tbl_room_section_subject_assignment", ['advisory' => ($advisory == $i || $advisory == $sbject)]);

                // ✅ Add schedule blocks for selected days
                $to_time_id = $this->getTimetableIdByTime($end_time_id[$i]); 
                foreach ($days_selected as $day) {
                    $blocks[] = [
                        'tbl_rssa_id' => $tmpId,
                        'day_of_week' => $day,
                        'teaching' => $is_teaching[$i] === 'teaching',
                        'from_timetable_id' => $start_time_id[$i],
                        'to_timetable_id' => $to_time_id,
                        'duration_id' => $duration_id[$i],
                        'non_teaching_id' => ($is_teaching[$i] === 'non-teaching') ? $sbject : null
                    ];
                }

            } elseif ($assprsn) {
                $data[] = [
                    'room_section_id' => $rmsecid,
                    'subject_id' => $sbject,
                    'schl_personnel_id' => $assprsn,
                    'advisory' => ($advisory == $i || $advisory == $sbject),
                ];
            }
        }

        // Insert new assignments
        $inserted_ids = [];
        if (count($data) > 0 && $login_id) {
            if ($this->db->insert_batch("building_sectioning.tbl_room_section_subject_assignment", $data)) {
                // Fetch last inserted IDs
                $inserted_ids = $this->db->query("
                    SELECT id FROM building_sectioning.tbl_room_section_subject_assignment
                    WHERE room_section_id = ?
                    ORDER BY id DESC
                    LIMIT ?", [$rmsecid, count($data)])->result_array();

                foreach ($inserted_ids as $index => $row) {
                    $to_time_id = $this->getTimetableIdByTime($end_time_id[$index]); 
                    foreach ($days_selected as $day) {
                        $blocks[] = [
                            'tbl_rssa_id' => $row['id'],
                            'day_of_week' => $day,
                            'teaching' => $is_teaching[$index] === 'teaching',
                            'from_timetable_id' => $start_time_id[$index],
                            'to_timetable_id' => $to_time_id,
                            'duration_id' => $duration_id[$index],
                            'non_teaching_id' => ($is_teaching[$index] === 'non-teaching') ? $sbjct[$index] : null
                        ];
                    }
                }
            }
        }

        // Insert all schedule blocks
        if (!empty($blocks)) {
            $this->db->insert_batch("building_sectioning.tbl_rssa_schedule_blocks", $blocks);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
            $ret = ["success" => true];
        }

        echo json_encode($ret);
    }

    function saveSubject()
    {
        $this->db->trans_begin();
        $index = $this->input->post("index");
        $sbjctnm = $this->input->post("sbjctnm");
        $abbr = $this->input->post("abbr");
        $ordr = $this->input->post("ordr");
        $login_id = $this->session->schoolmis_login_id;
        $true = ["success"   => true];
        $false = ["success"   => false];

        $exist = $this->db->query("SELECT * FROM global.tbl_party t1 WHERE t1.description='$sbjctnm'");
        if ($exist->num_rows() == 0) {
            $data = [
                "party_type_id" => 17,
                "description" => $sbjctnm,
                "is_active" => true,
                "order_by" => $ordr,
                "abbr" => $abbr,
                "group_by" => 11,
            ];
            if ($index && $sbjctnm && $abbr && $ordr && $login_id) {
                if (!$this->mainModel->update("global.tbl_party", $data, "party_index", $index)) {
                    // $this->userlog("UPDATED SUBJECT " . json_encode($data));
                    $ret = $true;
                }
            } else if ($sbjctnm && $abbr && $ordr && $login_id) {
                if ($this->db->insert("global.tbl_party", $data)) {
                    // $this->userlog("INSERTED SUBJECT " . json_encode($data));
                    $ret = $true;
                } else {
                    $ret = $false;
                }
            } else {
                $ret = $false;
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }
        } else {
            $ret = $false;
            $ret += [
                "exist"   => true,
                "message"   => "Subject Name already exist!"
            ];
        }
        echo json_encode($ret);
    }

    function saveProgram()
    {
        $this->db->trans_begin();
        $index = $this->input->post("index");
        $sbjctnm = $this->input->post("sbjctnm");
        $abbr = $this->input->post("abbr");
        $ordr = $this->input->post("ordr");
        $login_id = $this->session->schoolmis_login_id;
        $true = ["success"   => true];
        $false = ["success"   => false];

        $exist = $this->db->query("SELECT * FROM global.tbl_party t1 WHERE t1.description='$sbjctnm'");
        if ($exist->num_rows() == 0) {
            $data = [
                "party_type_id" => 22,
                "description" => $sbjctnm,
                "is_active" => true,
                "order_by" => $ordr,
                "abbr" => $abbr,
                "group_by" => null,
            ];
            if ($index && $sbjctnm && $abbr && $ordr && $login_id) {
                if (!$this->mainModel->update("global.tbl_party", $data, "party_index", $index)) {
                    // $this->userlog("UPDATED SUBJECT " . json_encode($data));
                    $ret = $true;
                }
            } else if ($sbjctnm && $abbr && $ordr && $login_id) {
                if ($this->db->insert("global.tbl_party", $data)) {
                    // $this->userlog("INSERTED SUBJECT " . json_encode($data));
                    $ret = $true;
                } else {
                    $ret = $false;
                }
            } else {
                $ret = $false;
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }
        } else {
            $ret = $false;
            $ret += [
                "exist"   => true,
                "message"   => "Program Name already exist!"
            ];
        }
        echo json_encode($ret);
    }

    function saveSbjctAssPrsnnl1()
    {
        $this->db->trans_begin();
        $data = [];
        $valid = 0;
        $rm_sec_id = $this->input->post("rm_sec_id");
        $sbjct = $this->input->post("sbjct");
        echo 1;
        echo $rm_sec_id;
        echo $sbjct[1];
        $assignPerson = $this->input->post("schlpersonnel");
        $advisory = $this->input->post("advisory");
        $login_id = $this->session->schoolmis_login_id;
        $dateNow = $this->now();
        $true = ["success"   => true];
        $false = ["success"   => false];

        for ($i = 0; $i < count($sbjct); $i++) {
            if ($sbjct[$i] == 0 || $assignPerson[$i] == 0) {
                $valid++;
                break;
            } else {
                $data[] = [
                    'room_section_id' => $rm_sec_id,
                    'subject_id' => $sbjct[$i],
                    'schl_personnel_id' => $assignPerson[$i],
                    'advisory' => $sbjct[$i] == $advisory ? true : false,
                ];
            }
        }
        // $b = json_encode($data);
        if ($valid == 0 && $login_id) {
            if ($this->db->insert_batch("building_sectioning.tbl_room_section_subject_assignment", $data)) {
                // $this->userlog("INSERTED SUBJECT ASSIGNMENT: " . $b);
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
        // $this->db->query("REFRESH MATERIALIZED VIEW building_sectioning.view_subject_grdlvl_personnel_assgnmnt;");
    }

    function saveQuarterInfo()
    {
        $this->db->trans_begin();
        $data = [];
        $true = ["success"   => true];
        $false = ["success"   => false];

        $qrtrid = $this->input->post("qrtrid");
        $quarter = $this->input->post("quarter");
        $enrollment = $this->input->post("enrollment") == true ? true : false;
        $enrolldl = $this->input->post("enrolldl");
        $grading = $this->input->post("grading") == true ? true : false;
        $gradingdl = $this->input->post("gradingdl");
        $viewing = $this->input->post("viewing") == true ? true : false;
        $v_date = $this->input->post("viewing_date");
        $edit = $this->input->post("edit") == true ? true : false;
        $unenroll = $this->input->post("unenroll") == true ? true : false;
        $customQ1 = $this->input->post("customQ1");
        $customQ2 = $this->input->post("customQ2");
        $customQ3 = $this->input->post("customQ3");
        $customQ4 = $this->input->post("customQ4");
        $input_grades_qrtr = ($customQ1 ? 1 : null) . ($customQ2 ? 2 : null) . ($customQ3 ? 3 : null) . ($customQ4 ? 4 : null);

        if ($qrtrid && $quarter) {
            $data = [
                "qrtr" => $quarter,
                "enrollment_stat" => $enrollment,
                "enrollment_deadline" => $enrolldl ? $enrolldl : null,
                "grading_stat" => $grading,
                "grading_deadline" => $gradingdl ? $gradingdl : null,
                "view_grades" => $viewing,
                "view_grades_until" => $v_date ? $v_date : null,
                "edit_student" => $edit,
                "unenroll" => $unenroll,
                "input_grades_qrtr" => $input_grades_qrtr ? $input_grades_qrtr : null,
            ];
            // $b = json_encode($data);
            $this->db->where('id', $qrtrid);
            if ($this->db->update("global.tbl_sy", $data)) {
                // $this->userlog("UPDATED QUARTER DETAILS" . $b);
                $ret = $true;
            } else {
                $ret = $false;
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

    function saveDeptInfo()
    {
        $this->db->trans_begin();
        $duuid = $this->input->post("duuid");
        $name = strtoupper($this->input->post("name"));
        $abbr = strtoupper($this->input->post("abbr"));
        $dateNow = $this->now();
        $login_id = $this->session->schoolmis_login_id;
        $true = ["success"   => true];
        $false = ["success"   => false];

        $exist = $this->db->query("SELECT * FROM profile.tbl_school_department t1 WHERE t1.department_name='$name'");
        if ($exist->num_rows() == 0) {
            $data = [
                "department_name" => $name,
                "abbr" => $abbr,
                "school_id" => $this->session->schoolmis_login_school_id,
                $duuid ? "updated_by" : "added_by" => $login_id,
                $duuid ? "date_updated" : "date_added" => $dateNow,
            ];
            if ($duuid && $name && $abbr && $login_id) {
                if (!$this->mainModel->update("profile.tbl_school_department", $data, "uuid", $duuid)) {
                    // $this->userlog("UPDATED DEPARTMENT " . json_encode($data));
                    $ret = $true;
                }
            } else if ($name && $abbr && $login_id) {
                if ($this->db->insert("profile.tbl_school_department", $data)) {
                    // $this->userlog("INSERTED DEPARTMENT " . json_encode($data));
                    $ret = $true;
                } else {
                    $ret = $false;
                }
            } else {
                $ret = $false;
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }
        } else {
            $ret = $false;
            $ret += [
                "exist"   => true,
                "message"   => "Deparment Name already exist!"
            ];
        }
        echo json_encode($ret);
    }

    function saveGateInfo()
    {
        $this->db->trans_begin();
        $uuid = $this->input->post("uuid");
        $name = strtoupper($this->input->post("name"));
        $abbr = strtoupper($this->input->post("abbr"));
        $dateNow = $this->now();
        $login_id = $this->session->schoolmis_login_id;
        $true = ["success"   => true];
        $false = ["success"   => false];

        // $exist = $this->db->query("SELECT * FROM global.tbl_party t1 WHERE t1.party_type_id=20 AND t1.description='$name'");
        // if ($exist->num_rows() == 0) {
        $data = [
            "party_type_id" => 20,
            "description" => $name,
            "is_active" => true,
            // "order_by" => $ordr,
            "abbr" => $abbr,
            // "group_by" => 11,
        ];
        if ($uuid && $name && $abbr && $login_id) {
            if (!$this->mainModel->update("global.tbl_party", $data, "party_index", $uuid)) {
                // $this->userlog("UPDATED DEPARTMENT " . json_encode($data));
                $ret = $true;
            }
        } else if ($name && $abbr && $login_id) {
            if ($this->db->insert("global.tbl_party", $data)) {
                // $this->userlog("INSERTED DEPARTMENT " . json_encode($data));
                $ret = $true;
            } else {
                $ret = $false;
            }
        } else {
            $ret = $false;
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
        // } else {
        //     $ret = $false;
        //     $ret += [
        //         "exist"   => true,
        //         "message"   => "Gateway Name already exist!"
        //     ];
        // }
        echo json_encode($ret);
    }

    function saveVisitorInfo()
    {
        $this->db->trans_begin();
        $count = strtoupper($this->input->post("count"));
        $login_id = $this->session->schoolmis_login_id;
        $true = ["success" => true];
        $false = ["success" => false];

        $batchData = []; // Array to hold the batch insert data

        $cc = $count > 10 ? 10 : ($count < 0 ? 1 : $count);

        for ($i = 0; $i < $cc; $i++) {
            $data = [
                "description" => "VISITOR",
                "created_by" => $login_id,
            ];
            $batchData[] = $data;
        }

        if (!empty($batchData)) {
            // Perform the batch insert
            $this->db->insert_batch("id.visitor", $batchData);
            $ret = $true;
        }else{
            $ret = $false;
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
        echo json_encode($ret);
    }

    function saveSYInfo()
    {
        $this->db->select_max('to', 'max');
        $query = $this->db->get('global.tbl_sy');
        $max = $query->row()->max;
        $from = $max;
        $to = $max + 1;
        $sy = $from . '-' . $to;
        $true = ["success"   => true];
        $false = ["success"   => false];

        $data1 = [
            "is_active" => false,
        ];
        $data2 = [
            "from" => $from,
            "to" => $to,
            "description" => $sy,
            "inclusive_month" => null,
        ];
        if (!$this->mainModel->update("global.tbl_sy", $data1, "is_active", true)) {
            if ($from && $to && $sy) {
                if ($this->db->insert("global.tbl_sy", $data2)) {
                    $inid = $this->db->insert_id();
                    if ($this->db->query("CREATE TABLE sy$sy.bs_tbl_learner_enrollment (
                                            id bigint NOT NULL,
                                            learner_id bigint NOT NULL,
                                            room_section_id bigint NOT NULL,
                                            status_id bigint NOT NULL,
                                            enrollment_date timestamp(6) without time zone,
                                            added_by bigint
                                        );
                                        ALTER TABLE sy$sy.bs_tbl_learner_enrollment OWNER TO postgres; 
                                        CREATE SEQUENCE sy$sy.bs_tbl_learner_enrollment_seq
                                            START WITH 1
                                            INCREMENT BY 1
                                            NO MINVALUE
                                            NO MAXVALUE
                                            CACHE 1;
                                        ALTER TABLE sy$sy.bs_tbl_learner_enrollment_seq OWNER TO postgres;
                                        ALTER SEQUENCE sy$sy.bs_tbl_learner_enrollment_seq OWNED BY sy$sy.bs_tbl_learner_enrollment.id;
                                        ALTER TABLE ONLY sy$sy.bs_tbl_learner_enrollment ALTER COLUMN id SET DEFAULT nextval('sy$sy.bs_tbl_learner_enrollment_seq'::regclass);
                                        ALTER TABLE ONLY sy$sy.bs_tbl_learner_enrollment
                                            ADD CONSTRAINT tbl_learner_enrollment_learner_id_key UNIQUE (learner_id);
                                        ALTER TABLE ONLY sy$sy.bs_tbl_learner_enrollment
                                            ADD CONSTRAINT tbl_learner_enrollment_pkey$inid PRIMARY KEY (id);
                                        ALTER TABLE ONLY sy$sy.bs_tbl_learner_enrollment
                                            ADD CONSTRAINT tbl_learner_enrollment_added_by_fkey FOREIGN KEY (added_by) REFERENCES account.tbl_useraccount(id);
                                        ALTER TABLE ONLY sy$sy.bs_tbl_learner_enrollment
                                            ADD CONSTRAINT tbl_learner_enrollment_learner_id_fkey FOREIGN KEY (learner_id) REFERENCES profile.tbl_learners(id);
                                        ALTER TABLE ONLY sy$sy.bs_tbl_learner_enrollment
                                            ADD CONSTRAINT tbl_learner_enrollment_room_id_fkey FOREIGN KEY (room_section_id) REFERENCES building_sectioning.tbl_room_section(id);
                                        ALTER TABLE ONLY sy$sy.bs_tbl_learner_enrollment
                                            ADD CONSTRAINT tbl_learner_enrollment_status_id_fkey FOREIGN KEY (status_id) REFERENCES global.tbl_status(id);





                                        -- CREATE TABLE sy$sy.bs_tbl_learner_grades (
                                        --     id bigint NOT NULL,
                                        --     learner_enrollment_id bigint NOT NULL,
                                        --     room_sec_sub_ass_id bigint NOT NULL,
                                        --     qrtr_id integer NOT NULL,
                                        --     grade numeric(255,0) DEFAULT 0 NOT NULL,
                                        --     date_added timestamp without time zone DEFAULT now() NOT NULL,
                                        --     added_by bigint NOT NULL,
                                        --     date_updated timestamp without time zone,
                                        --     updated_by bigint,
                                        --     status_id integer NOT NULL
                                        -- );


                                        -- ALTER TABLE sy$sy.bs_tbl_learner_grades OWNER TO postgres;

                                        
                                        -- ALTER TABLE sy$sy.bs_tbl_learner_grades_seq OWNER TO postgres;
                                        -- ALTER TABLE ONLY sy$sy.bs_tbl_learner_grades ALTER COLUMN id SET DEFAULT nextval('sy$sy.bs_tbl_learner_grades_seq'::regclass);
                                        -- ALTER TABLE ONLY sy$sy.bs_tbl_learner_grades
                                        --     ADD CONSTRAINT tbl_learner_grades_pkey$inid PRIMARY KEY (id);
                                        -- ALTER TABLE ONLY sy$sy.bs_tbl_learner_grades
                                        --     ADD CONSTRAINT tbl_learner_grades_added_by_fkey FOREIGN KEY (added_by) REFERENCES account.tbl_useraccount(id);
                                        -- ALTER TABLE ONLY sy$sy.bs_tbl_learner_grades
                                        --     ADD CONSTRAINT tbl_learner_grades_learner_id_fkey FOREIGN KEY (learner_enrollment_id) REFERENCES sy$sy.bs_tbl_learner_enrollment(id);
                                        -- ALTER TABLE ONLY sy$sy.bs_tbl_learner_grades
                                        --     ADD CONSTRAINT tbl_learner_grades_qrtr_id_fkey FOREIGN KEY (qrtr_id) REFERENCES global.tbl_party(id);
                                        -- ALTER TABLE ONLY sy$sy.bs_tbl_learner_grades
                                        --     ADD CONSTRAINT tbl_learner_grades_status_id_fkey FOREIGN KEY (status_id) REFERENCES global.tbl_status(id);
                                        -- ALTER TABLE ONLY sy$sy.bs_tbl_learner_grades
                                        --     ADD CONSTRAINT tbl_learner_grades_subject_id_fkey FOREIGN KEY (room_sec_sub_ass_id) REFERENCES building_sectioning.tbl_room_section_subject_assignment(id);
                                        -- ALTER TABLE ONLY sy$sy.bs_tbl_learner_grades
                                        --     ADD CONSTRAINT tbl_learner_grades_updated_by_fkey FOREIGN KEY (updated_by) REFERENCES account.tbl_useraccount(id);

                                        CREATE TABLE sy$sy.bs_tbl_learner_grades (
                                            id bigint NOT NULL,
                                            learner_enrollment_id bigint NOT NULL,
                                            room_section_id bigint NOT NULL,
                                            qrtr_id integer NOT NULL,
                                            grade numeric(255) NULL DEFAULT 0,
                                            date_added timestamp without time zone DEFAULT now() NOT NULL,
                                            added_by bigint NOT NULL,
                                            date_updated timestamp NULL,
                                            updated_by bigint NULL,
                                            status_id integer NOT NULL,
                                            sy_id integer NOT NULL,
                                            CONSTRAINT tbl_learner_grades_pkey$inid PRIMARY KEY (id),
                                            CONSTRAINT tbl_learner_grades_sy_id_fkey$inid FOREIGN KEY (sy_id) REFERENCES global.tbl_sy(id),
                                            CONSTRAINT tbl_learner_grades_added_by_fkey$inid FOREIGN KEY (added_by) REFERENCES account.tbl_useraccount(id),
                                            CONSTRAINT tbl_learner_grades_learner_id_fkey$inid FOREIGN KEY (learner_enrollment_id) REFERENCES sy$sy.bs_tbl_learner_enrollment(id),
                                            CONSTRAINT tbl_learner_grades_status_id_fkey$inid FOREIGN KEY (status_id) REFERENCES global.tbl_status(id),
                                            CONSTRAINT tbl_learner_grades_subject_id_fkey$inid FOREIGN KEY (room_section_id) REFERENCES building_sectioning.tbl_room_section(id),
                                            CONSTRAINT tbl_learner_grades_updated_by_fkey$inid FOREIGN KEY (updated_by) REFERENCES account.tbl_useraccount(id)
                                        );

                                        ALTER TABLE sy$sy.bs_tbl_learner_grades OWNER TO postgres;

                                        CREATE SEQUENCE sy$sy.bs_tbl_learner_grades_seq
                                            START WITH 1
                                            INCREMENT BY 1
                                            NO MINVALUE
                                            NO MAXVALUE
                                            CACHE 1;

                                        ALTER SEQUENCE sy$sy.bs_tbl_learner_grades_seq OWNED BY sy$sy.bs_tbl_learner_grades.id;

                                        CREATE OR REPLACE VIEW sy$sy.bs_view_enrollment AS 
                                        SELECT t1.id AS enrollment_id,
                                        t1.room_section_id,
                                        t3.rm_sctn_sbjct_assgnmnt_id,
                                        t1.enrollment_date,
                                        t1.status_id,
                                        t4.description AS enrollment_status,
                                        t3.id AS rm_sec_id,
                                        t3.room_id,
                                        t3.grd_lvl_id,
                                        t3.schl_yr_id,
                                        t3.sctn_nm,
                                        t3.schedule_id,
                                        t3.is_active,
                                        t3.grade,
                                        t3.order_by,
                                        t3.sched,
                                        t3.sy,
                                        t2.learner_id,
                                        t2.lrn,
                                        t2.basic_info_id,
                                        t2.mother_tongue,
                                        t2.ethnic_group,
                                        t2.ffirst_name,
                                        t2.fmiddle_name,
                                        t2.flast_name,
                                        t2.mfirst_name,
                                        t2.mmiddle_name,
                                        t2.mlast_name,
                                        t2.guardian,
                                        t2.relation,
                                        t2.contact_num,
                                        t2.four_ps,
                                        t2.person_id,
                                        t2.person_uuid,
                                        t2.first_name,
                                        t2.middle_name,
                                        t2.last_name,
                                        t2.suffix,
                                        t2.birthdate,
                                        t2.sex_bool,
                                        t2.sex,
                                        t2.full_name,
                                        t2.last_fullname,
                                        t2.address_details,
                                        t2.address_info,
                                        t2.citymun_id,
                                        t2.citymun_name,
                                        t2.barangay_id,
                                        t2.barangay_name,
                                        t2.purok_id,
                                        t2.purok_name
                                    FROM (((sy$sy.bs_tbl_learner_enrollment t1
                                        JOIN profile.view_learner t2 ON ((t1.learner_id = t2.learner_id)))
                                        JOIN building_sectioning.view_room_section t3 ON ((t1.room_section_id = t3.id)))
                                        JOIN global.tbl_status t4 ON ((t1.status_id = t4.id)));

                                        ")) {
                        // $this->userlog("INSERTED AND ACTIVATED NEW SCHOOL YEAR " . json_encode($data2));
                        $ret = $true;
                    } else {
                        $ret = $false;
                    }
                } else {
                    $ret = $false;
                }
            } else {
                $ret = $false;
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
}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */