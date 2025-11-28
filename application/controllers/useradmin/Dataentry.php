<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dataentry extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        // $this->redirect();
        $this->load->model('mainModel');
        $this->load->library('excel');
        $this->load->helper('date');
        date_default_timezone_set("Asia/Manila");
    }

    public function index()
    {
        $page_data = $this->system();
        $uri = 'userteacher'; // $this->session->schoolmis_login_uri;
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

    function saveBatchUpdateAccount()
    {
        $this->db->trans_begin();
        $data_gen = [];
        parse_str($this->input->post("c"), $filter);
        //create reset disable enable
        $stat = $this->input->post("e");
        $sy = $this->getOnLoad()["sy_id"];
        $learner_id = $filter['learnerCheckBox'];
        $login_id = $this->session->schoolmis_login_id;
        $dateNow = $this->now();
        $create = 0;
        $true = ["success"   => true];
        $false = ["success"   => false];
        for ($i = 0; $i < count($learner_id); $i++) {
            $a = $learner_id[$i];
            $b = explode('_&&_', $a);
            //LEARNER ID _&&_ LRN _&&_ BASIC INFO ID _&&_ ACCOUNT
            $l_id = $b[0];
            $lrn = $this->clean($b[1]);
            $b_info = $b[2];
            $has_account = $b[3];
            if ($stat == 'create' && $has_account == 0) {
                $data_gen[] = [
                    "basic_info_id" => $b_info,
                    "role_id" => 8,
                    "username" => $lrn,
                    "password" => md5(12345678),
                    "change_pwd" => true,
                    "is_active" => true,
                    "added_by" => $login_id,
                    "date_added" => $dateNow,
                ];
                $create++;
            }

            if ($stat == 'reset') {
                $data_gen[] = [
                    "username" => $lrn,
                    "password" => md5(12345678),
                    "change_pwd" => true,
                    "is_active" => true,
                    "updated_by" => $login_id,
                    "date_updated" => $dateNow,
                ];
            }

            if ($stat == 'disable' || $stat == 'enable') {
                $data_gen[] = [
                    "username" => $lrn,
                    "is_active" => $stat == 'disable' ? false : true,
                    "updated_by" => $login_id,
                    "date_updated" => $dateNow,
                ];
            }
        }

        if ($stat == 'create' && $create == 0 && $login_id) {
            $false += ["message"   => "Account already exist!"];
            $ret = $false;
        }

        if ($create > 0 && count($data_gen) > 0 && $login_id) {
            if ($this->db->insert_batch("account.tbl_useraccount", $data_gen)) {
                // $inid = $this->db->insert_id();
                // $this->userlog("CREATED NEW STUDENT ACCOUNT " . $inid . " " . json_encode($data_gen));
                $true += ["message"   => "Successfully created!"];
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
        }

        if ($stat == 'disable' || $stat == 'enable' || $stat == 'reset' && count($data_gen) > 0 && $login_id) {
            if ($this->db->update_batch("account.tbl_useraccount", $data_gen, 'username')) {
                if ($stat == 'reset') {
                    // $this->userlog("PASSWORD RESET STUDENT ACCOUNT " . json_encode($data_gen));
                    $true += ["message"   => "Password reset!"];
                    $ret = $true;
                } else if ($stat == 'disable' || $stat == 'enable') {
                    // $d_e = $stat == 'disable' ? 'DISABLED' : 'ENABLED';
                    // $this->userlog($d_e . " STUDENT ACCOUNT " . json_encode($data_gen));
                    $d_e2 = $stat == 'disable' ? 'Disabled' : 'Enabled';
                    $true += ["message"   => $d_e2 . " Account!"];
                    $ret = $true;
                } else {
                    $false += ["message"   => "Something went wrong!"];
                    $ret = $false;
                }
            } else {
                $false += ["message"   => "Something went wrong!"];
                $ret = $false;
            }
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
            }
        }

        if ($stat == 'disable' || $stat == 'enable' && count($data_gen) > 0 && $login_id) {

            if ($this->db->update_batch("account.tbl_useraccount", $data_gen, 'username')) {
                // $d_e = $stat == 'disable' ? 'DISABLED' : 'ENABLED';
                // $this->userlog($d_e . " STUDENT ACCOUNT " . json_encode($data_gen));
                $true += ["message"   => "Password reset!"];
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
        }
        $this->db->query("REFRESH MATERIALIZED VIEW account.view_useraccount;");
        echo json_encode($ret);
    }

    function saveEnrollmentInfo()
    {
        $this->db->trans_begin();
        $data = [];
        $sy = $this->getOnLoad()["sy_id"];
        $sy_desc = $this->getOnLoad()["sy"];
        $edit = $this->getOnLoad()["edit"];
        $id = $this->input->post("details");
        $a = explode('|', $id);
        $enroll_id = null;
        $learner_id = null;
        $binfo_id = null;
        if (count($a) > 1) {
            $enroll_id = $a[0];
            $learner_id = $a[1];
            $binfo_id = $a[2];
        }
        $lrn = $this->clean($this->input->post("lrn"));
        $rsId = $this->input->post("rsId");
        $firstName = $this->cleanQuote(strtoupper($this->input->post("firstName")));
        $middleName = $this->cleanQuote(strtoupper($this->input->post("middleName")));
        $lastName = $this->cleanQuote(strtoupper($this->input->post("lastName")));
        $extName = $this->cleanQuote(strtoupper($this->input->post("extName")));
        $sex = $this->input->post("sex");
        $birthdate = $this->input->post("birthdate");
        $brgy = $this->input->post("brgy");
        $homeAddress = $this->cleanQuote(strtoupper($this->input->post("homeAddress")));
        $status = $this->input->post("status");
        $enrollDate = $this->input->post("enrollDate");
        // $img_path = $this->input->post("previewPic");

        //father
        $ffname = $this->cleanQuote($this->input->post("ffname"));
        $fmname = $this->cleanQuote($this->input->post("fmname"));
        $flname = $this->cleanQuote($this->input->post("flname"));
        //mother
        $mfname = $this->cleanQuote($this->input->post("mfname"));
        $mmname = $this->cleanQuote($this->input->post("mmname"));
        $mlname = $this->cleanQuote($this->input->post("mlname"));
        $guardian = $this->cleanQuote($this->input->post("guardianName"));
        $relation = $this->cleanQuote($this->input->post("relationship"));

        $ioe = $this->input->post("ioe");
        $contact = $this->cleanQuote($this->input->post("contactNumber"));

        $mother_tongue = $this->input->post("mother_tongue");
        $ip_ethnic_group = $this->input->post("ip_ethnic_group");
        $religion = $this->input->post("religion");
        $four_ps = $this->input->post("four_ps");
        $learning_modality = $this->input->post("learning_modality");
        $remarks = $this->input->post("remarks");



        $login_id = $this->session->schoolmis_login_id;
        $dateNow = $this->now();
        $true = ["success"   => true];
        $false = ["success"   => false];
        // $false += ["message"   => ""];


        $data = [
            "first_name" => $firstName,
            "middle_name" => $middleName,
            "last_name" => $lastName,
            "suffix" => $extName,
            "sex" => $sex,
            "birthdate" => $birthdate,
            "address_info" => $homeAddress,
            "barangay_id" => $brgy,
            // "purok_id" => $prk != "" ? $prk : null,
            // "address_info" => $homeAddress,
            // "email_address" => $emailAddress,
            $id ? "updated_by" : "added_by" => $login_id,
            $id ? "date_updated" : "date_added" => $dateNow,
            // "img_path" => $img_path,
        ];

        $data2 = [
            "lrn" => $lrn,
            "ffirst_name" => $ffname,
            "fmiddle_name" => $fmname,
            "flast_name" => $flname,
            "mfirst_name" => $mfname,
            "mmiddle_name" => $mmname,
            "mlast_name" => $mlname,
            "guardian" => $guardian,
            "relation" => $relation,
            "ioe" => $ioe,
            "contact_num" => $contact,
            "mother_tongue_txt" => $mother_tongue,
            "ip_ethnic_group_txt" => $ip_ethnic_group,
            "religion_txt" => $religion,
            "four_ps" => $four_ps,
            "learning_modality_txt" => $learning_modality,
            "remarks" => $remarks,
        ];

        // First, check if we are updating and an old image exists
        if (!empty($existingData['img_path']) && (isset($_FILES['pic']) || !empty($_POST['pic_captured']))) {
            if (file_exists($existingData['img_path'])) {
                unlink($existingData['img_path']); // delete old image
            }
        }

        if (isset($_FILES['pic']) && $_FILES['pic']['error'] === UPLOAD_ERR_OK) {
            // Normal upload
            $data += [
                "img_path" => $this->uploadImg($_FILES['pic'], $lrn, 'learner')
            ];
        } elseif (!empty($_POST['pic_captured'])) {
            // Captured from camera
            $imgData = $_POST['pic_captured'];
            $imgData = str_replace('data:image/jpeg;base64,', '', $imgData);
            $imgData = str_replace(' ', '+', $imgData);
            $decodedData = base64_decode($imgData);

            $path = 'dist/img/media/learner/' . $sy_desc . '/';
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
            $filename = $lrn . '.jpg';
            file_put_contents($path . $filename, $decodedData);

            $data += [
                "img_path" => $path . $filename
            ];
        } else {
            // No new upload → keep old path
            $data += [
                "img_path" => $existingData['img_path'] ?? null
            ];
        }


        if ($lrn && $firstName && $lastName && $sex && $birthdate && $brgy && $login_id) {
            if ($edit == 't' && $enroll_id && $learner_id && $binfo_id) {
                $this->db->where('id', $binfo_id);
                if ($this->db->update("profile.tbl_basicinfo", $data)) {
                    // $this->userlog("UPDATED STUDENT BASIC INFORMATION " . json_encode($data));
                    $data3 = [
                        "status_id" => $status,
                        "enrollment_date" => $enrollDate,
                    ];
                    // $this->db->where('learner_id', $learner_id);
                    $this->db->where('id', $enroll_id);
                    if ($this->db->update("sy$sy.bs_tbl_learner_enrollment", $data3)) {
                        // $this->userlog("UPDATED STUDENT STATUS " . json_encode($data3));
                        $ret = $true;
                    } else {
                        $ret = $false;
                    }



                    // $this->db->where('lrn', $lrn);
                    $this->db->where('id', $learner_id);
                    if ($this->db->update("profile.tbl_learners", $data2)) {
                        // $this->userlog("UPDATED LEARNER OTHER INFO " . json_encode($data3));
                        $ret = $true;
                    } else {
                        $ret = $false;
                    }
                } else {
                    $ret = $false;
                }
            } else if (!$learner_id) {


                $query = $this->db->query("SELECT t1.id, t2.grade, t2.sctn_nm FROM profile.tbl_learners t1
                                            LEFT JOIN sy$sy.bs_view_enrollment t2 ON t1.id=t2.learner_id
                                            WHERE t1.lrn='$lrn' LIMIT 1");
                $row = $query->row();

                if ($query->num_rows() != 0 && $row->grade != null) {
                    $rowData = $row->grade . ' - ' . $row->sctn_nm;
                    $false += ["message"   => "Existing Data: " . $rowData];
                    $ret = $false;
                } else if ($query->num_rows() != 0 && $row->grade == null) {
                    $lrn_id = $row->id;
                    $rowData = $row->grade . ' - ' . $row->sctn_nm;
                    $false += ["message"   => "LRN : " . $rowData];
                    $ret = $false;

                    $data4 = [
                        "learner_id" => $lrn_id,
                        "room_section_id" => $rsId,
                        "status_id" => $status,
                        "enrollment_date" => $enrollDate,
                        "added_by" => $login_id,
                    ];
                    if ($lrn_id) {
                        if ($this->db->insert("sy$sy.bs_tbl_learner_enrollment", $data4)) {
                            // $this->userlog("ENROLLED STUDENT " . $lrn_id . " " . json_encode($data4));
                            $ret = $true;
                        } else {
                            $ret = $false;
                        }
                    }
                } else {
                    if ($this->db->insert("profile.tbl_basicinfo", $data)) {
                        $inid = $this->db->insert_id();

                        $data2 += [
                            "basic_info_id" => $inid,
                        ];

                        // $this->userlog("INSERTED STUDENT DETAILS " . $inid . " " . json_encode($data));
                        if ($inid) {
                            if ($this->db->insert("profile.tbl_learners", $data2)) {
                                $lrnId = $this->db->insert_id();
                                // $this->userlog("CREATED LRN FOR STUDENT " . $lrnId . " " . json_encode($data2));
                                $data3 = [
                                    "learner_id" => $lrnId,
                                    "room_section_id" => $rsId,
                                    "status_id" => $status,
                                    "enrollment_date" => $enrollDate,
                                    "added_by" => $login_id,
                                ];
                                if ($lrnId) {
                                    if ($this->db->insert("sy$sy.bs_tbl_learner_enrollment", $data3)) {
                                        // $this->userlog("ENROLLED STUDENT " . $lrnId . " " . json_encode($data3));
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
                    }
                }
            } else {
                $false += ["message"   => "Please contact the Administrator"];
                $ret = $false;
            }
        } else {
            $ret = $false;
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
            $this->db->query("REFRESH MATERIALIZED VIEW profile.view_basicinfo;");
            $this->db->query("REFRESH MATERIALIZED VIEW sy$sy.bs_view_enrollment;");
        }

        echo json_encode($ret);
    }

    function saveImportEnrollmentInfo()
    {
        $this->db->trans_begin();
        $rsid = $this->input->post("rsid");
        $batch_update = $this->input->post("batch_update");
        $basicInfoData = [];
        $learnerData = [];
        $enrollmentData = [];
        $true = ["success"   => true];
        $false = ["success"   => false];
        $sy = $this->getOnLoad()["sy_id"];
        $sy_desc = $this->getOnLoad()["sy"];
        $login_id = $this->session->schoolmis_login_id;
        $basicInfoId = null;
        $learnerId = null;
        //aa
        if (isset($_FILES["file"]["name"])) {
            $path = $_FILES["file"]["tmp_name"];
            $object = PHPExcel_IOFactory::load($path);
            foreach ($object->getWorksheetIterator() as $worksheet) {
                $highestRow = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();
                for ($row = 7; $row <= $highestRow; $row++) {
                    //BASIC INFORMATION
                    $LRN = $this->clean($worksheet->getCellByColumnAndRow(0, $row)->getValue());
                    // echo $LRN;
                    if (is_numeric($LRN) && strlen($LRN) > 5) {
                        //LEARNER
                        $full_name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                        $names = $full_name ? explode(",", $full_name) : null;
                        $name = $this->cleanQuote($names);
                        $lname = $name && !empty($name[0]) ? strtoupper(trim($name[0])) : null;
                        $fname = $name && !empty($name[1]) ? strtoupper(trim($name[1])) : null;
                        $mname = $name && !empty($name[2]) ? strtoupper(trim($name[2])) : null;

                        $sex = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                        $bdate = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                        $cell = $worksheet->getCellByColumnAndRow(7, $row);
                        $homeAddress = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
                        $barangay = $worksheet->getCellByColumnAndRow(17, $row)->getValue(); //barangay
                        $mun_city = $worksheet->getCellByColumnAndRow(20, $row)->getValue(); //mun_city
                        $province = $worksheet->getCellByColumnAndRow(21, $row)->getValue(); //province

                        //OTHER DETAILS
                        $mt = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
                        $ip = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
                        $rlgn = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
                        //Father
                        $father_name = $worksheet->getCellByColumnAndRow(27, $row)->getValue();
                        $f_names = $father_name ? explode(",", $father_name) : null;
                        $f_name = $this->cleanQuote($f_names);
                        $flname = $f_name && !empty($f_name[0]) ? strtoupper(trim($f_name[0])) : null;
                        $ffname = $f_name && !empty($f_name[1]) ? strtoupper(trim($f_name[1])) : null;
                        $fmname = $f_name && !empty($f_name[2]) ? strtoupper(trim($f_name[2])) : null;
                        //MOTHER
                        $mother_name = $worksheet->getCellByColumnAndRow(31, $row)->getValue();
                        $m_names = $mother_name ? explode(",", $mother_name) : null;
                        $m_name = $this->cleanQuote($m_names);
                        $mlname = $m_name && !empty($m_name[0]) ? strtoupper(trim($m_name[0])) : null;
                        $mfname = $m_name && !empty($m_name[1]) ? strtoupper(trim($m_name[1])) : null;
                        $mmname = $m_name && !empty($m_name[2]) ? strtoupper(trim($m_name[2])) : null;

                        $g_name = $this->cleanQuote($worksheet->getCellByColumnAndRow(36, $row)->getValue());
                        $rltn = $worksheet->getCellByColumnAndRow(40, $row)->getValue();
                        $cntct = $this->cleanQuote($worksheet->getCellByColumnAndRow(41, $row)->getValue());
                        $module = $worksheet->getCellByColumnAndRow(43, $row)->getValue();
                        $rmrks = $worksheet->getCellByColumnAndRow(44, $row)->getValue();

                        // $dateFormats = ['m-d-Y', 'm/d/Y', 'm-d/Y', 'm/d-Y'];

                        $birthdate = $this->filterAndFormatDate($bdate);



                        if ($this->hasSpecificCharacter($sex, 'M')) {
                            $boolSex = true;
                        } else {
                            $boolSex = false;
                        }
                        // $boolSex = $sex === 'M' ? true : ($sex === 'F' ? false : null);
                        // $boolSex = $this->hasSpecificCharacter($sex, 'M')


                        if ($LRN && $fname && $lname && $bdate && $sex) {
                            $learnerData = array(
                                // "lrn" => $LRN,
                                // "basic_info_id" => $basicInfoId,
                                "ffirst_name" => $ffname,
                                "fmiddle_name" => $fmname,
                                "flast_name" => $flname,
                                "mfirst_name" => $mfname,
                                "mmiddle_name" => $mmname,
                                "mlast_name" => $mlname,
                                "guardian" => $g_name,
                                "relation" => $rltn,
                                "contact_num" => $cntct,
                                "mother_tongue_txt" => $mt,
                                "ip_ethnic_group_txt" => $ip,
                                "religion_txt" => $rlgn,
                                "learning_modality_txt" => $module,
                                "remarks" => $rmrks,
                                "ioe" => $mfname ? 'M' : ($mfname ? 'F' : 'G'),
                            );

                            $basicInfoData = array(
                                "first_name" => $fname,
                                "middle_name" => $mname,
                                "last_name" => $lname,
                                "birthdate" => $birthdate,
                                "sex" => $boolSex,
                                "barangay_id" => $this->getBarangay_City($barangay, $mun_city), //160202054,//$barangay,
                                "address_info" => $homeAddress,
                            );
                            $checkLearner = $this->learnerChecker($LRN, null);
                            // echo $checkLearner . ' aa \n ';
                            if ($checkLearner) { //IF LEARNER EXIST IN TBL_LEARNER
                                $checkEnrollemnt = $this->enrollmentChecker($checkLearner, $batch_update);
                                if ($checkEnrollemnt) { //IF LEARNER EXIST IN TBL_ENROLLMENT THEN DO NOTHING
                                    $ret = $false;
                                } else {
                                    $enrollmentData[] = [
                                        "learner_id" => $checkLearner,
                                        "room_section_id" => $rsid,
                                        "status_id" => 5,
                                        "added_by" => $login_id,
                                    ];
                                }
                                if ($batch_update) {
                                    // $basicInfoData = array(
                                    //     "first_name" => $fname,
                                    //     "middle_name" => $mname,
                                    //     "last_name" => $lname,
                                    //     "birthdate" => $birthdate,
                                    //     "sex" => $boolSex,
                                    //     "barangay_id" => $this->getBarangay_City($barangay, $mun_city), //160202054,//$barangay,
                                    //     "address_info" => $homeAddress,
                                    // );

                                    $this->db->where('id', $checkEnrollemnt);
                                    if ($this->db->update("profile.tbl_basicinfo", $basicInfoData)) {
                                        $ret = $true;
                                        if ($checkEnrollemnt) { //IF THE VALUE OF BASICINO ID HAS DATA THEN PREPARATION FOR A NEW LEARNER
                                            // $learnerData = array(
                                            //     // "lrn" => $LRN,
                                            //     // "basic_info_id" => $basicInfoId,
                                            //     "ffirst_name" => $ffname,
                                            //     "fmiddle_name" => $fmname,
                                            //     "flast_name" => $flname,
                                            //     "mfirst_name" => $mfname,
                                            //     "mmiddle_name" => $mmname,
                                            //     "mlast_name" => $mlname,
                                            //     "guardian" => $g_name,
                                            //     "relation" => $rltn,
                                            //     "contact_num" => $cntct,
                                            //     "mother_tongue_txt" => $mt,
                                            //     "ip_ethnic_group_txt" => $ip,
                                            //     "religion_txt" => $rlgn,
                                            //     "learning_modality_txt" => $module,
                                            //     "remarks" => $rmrks,
                                            //     "ioe" => $mfname ? 'M' : ($mfname ? 'F' : 'G'),
                                            // );
                                            $this->db->where('basic_info_id', $checkEnrollemnt);
                                            if ($this->db->update("profile.tbl_learners", $learnerData)) {
                                                $ret = $true;
                                            } else {
                                                $ret = $false;
                                            }
                                        }
                                    } else {
                                        $ret = $false;
                                    }


                                    // $basicInfoDataLOG = json_encode($basicInfoData);
                                    // if ($this->db->insert("profile.tbl_basicinfo", $basicInfoData)) { //INSERT TO TBL_BASICNFO FOR NEW RECORD
                                    //     $basicInfoId = $this->db->insert_id();
                                    //     $ret = $true;
                                    //     if ($basicInfoId) { //IF THE VALUE OF BASICINO ID HAS DATA THEN PREPARATION FOR A NEW LEARNER
                                    //         $learnerData = [
                                    //             "lrn" => $LRN,
                                    //             "basic_info_id" => $basicInfoId,
                                    //             "ffirst_name" => $ffname,
                                    //             "fmiddle_name" => $fmname,
                                    //             "flast_name" => $flname,
                                    //             "mfirst_name" => $mfname,
                                    //             "mmiddle_name" => $mmname,
                                    //             "mlast_name" => $mlname,
                                    //             "guardian" => $g_name,
                                    //             "relation" => $rltn,
                                    //             "contact_num" => $cntct,
                                    //             "mother_tongue_txt" => $mt,
                                    //             "ip_ethnic_group_txt" => $ip,
                                    //             "religion_txt" => $rlgn,
                                    //             "learning_modality_txt" => $module,
                                    //             "remarks" => $rmrks,
                                    //         ];
                                    //         if ($this->db->insert("profile.tbl_learners", $learnerData)) {
                                    //             $learnerId = $this->db->insert_id();
                                    //             // $this->userlog("INSERTED NEW LEARNER FROM EXCEL FILE" . $learnerDataLOG);
                                    //             $ret = $true;
                                    //             // if ($learnerId) {
                                    //             //     $enrollmentData[] = [
                                    //             //         "learner_id" => $learnerId,
                                    //             //         "room_section_id" => $rsid,
                                    //             //         "status_id" => 5,
                                    //             //         "added_by" => $login_id,
                                    //             //     ];
                                    //             // }
                                    //         }
                                    //     }
                                    // } else {
                                    //     $ret = $false;
                                    // }
                                }
                            } else {
                                $checkBasicInfo = $this->basicInfoChecker($fname, $mname, $lname, $birthdate, $boolSex);
                                // echo $checkLearner . ' bb \n ';
                                if (!$checkBasicInfo) { //IF BASIC INFORMATION NOT EXIST
                                    $basicInfoData += [
                                        // "first_name" => $fname,
                                        // "middle_name" => $mname,
                                        // "last_name" => $lname,
                                        // "birthdate" => $birthdate,
                                        // "sex" => $boolSex,
                                        // "barangay_id" => $this->getBarangay_City($barangay, $mun_city), //160202054,//$barangay,
                                        // "address_info" => $homeAddress,
                                        //img
                                        "img_path" => "dist/img/media/learner/" . $sy_desc . "/" . $LRN . ".jpg",
                                    ];
                                    // $basicInfoDataLOG = json_encode($basicInfoData);
                                    if ($this->db->insert("profile.tbl_basicinfo", $basicInfoData)) { //INSERT TO TBL_BASICNFO FOR NEW RECORD
                                        $basicInfoId = $this->db->insert_id();
                                        // $this->userlog("INERTED NEW BASIC INFO FROM EXCEL FILE" . $basicInfoDataLOG);
                                        $ret = $true;
                                        // echo "d";
                                        // echo $basicInfoId;
                                        if ($basicInfoId) { //IF THE VALUE OF BASICINO ID HAS DATA THEN PREPARATION FOR A NEW LEARNER
                                            $learnerData += [
                                                "lrn" => $LRN,
                                                "basic_info_id" => $basicInfoId,
                                            ];
                                            // $learnerDataLOG = json_encode($learnerData);
                                            if ($this->db->insert("profile.tbl_learners", $learnerData)) {
                                                $learnerId = $this->db->insert_id();
                                                // $this->userlog("INSERTED NEW LEARNER FROM EXCEL FILE" . $learnerDataLOG);
                                                $ret = $true;
                                                if ($learnerId) {
                                                    $enrollmentData[] = [
                                                        "learner_id" => $learnerId,
                                                        "room_section_id" => $rsid,
                                                        "status_id" => 5,
                                                        "added_by" => $login_id,
                                                    ];
                                                }
                                            }
                                        }
                                    } else {
                                        $ret = $false;
                                    }
                                } else if ($checkBasicInfo && !$checkLearner) {
                                    // echo ('aaaaaaaaaaa');
                                    $learnerData += [
                                        "lrn" => $LRN,
                                        "basic_info_id" => $checkBasicInfo,
                                        // "ffirst_name" => $ffname,
                                        // "fmiddle_name" => $fmname,
                                        // "flast_name" => $flname,
                                        // "mfirst_name" => $mfname,
                                        // "mmiddle_name" => $mmname,
                                        // "mlast_name" => $mlname,
                                        // "guardian" => $g_name,
                                        // "relation" => $rltn,
                                        // "contact_num" => $cntct,
                                        // "mother_tongue_txt" => $mt,
                                        // "ip_ethnic_group_txt" => $ip,
                                        // "religion_txt" => $rlgn,
                                        // "learning_modality_txt" => $module,
                                        // "remarks" => $rmrks,
                                    ];
                                    // $learnerDataLOG = json_encode($learnerData);
                                    if ($this->db->insert("profile.tbl_learners", $learnerData)) {
                                        $learnerId = $this->db->insert_id();
                                        // $this->userlog("INSERTED NEW LEARNER FROM EXCEL FILE" . $learnerDataLOG);
                                        $ret = $true;
                                        if ($learnerId) {
                                            $enrollmentData[] = [
                                                "learner_id" => $learnerId,
                                                "room_section_id" => $rsid,
                                                "status_id" => 5,
                                                "added_by" => $login_id,
                                            ];
                                        }
                                    }
                                } else {
                                    $checkLearnerId = $this->learnerChecker(null, $checkBasicInfo);
                                    // echo $checkLearnerId . ' cc \n ';
                                    if ($checkLearnerId) {
                                        $checkEnrollemnt = $this->enrollmentChecker($checkLearnerId, $batch_update);
                                        if ($checkEnrollemnt) { //IF LEARNER EXIST IN TBL_ENROLLMENT THEN DO NOTHING
                                            $ret = $false;
                                        } else {
                                            $enrollmentData[] = [
                                                "learner_id" => $checkLearner,
                                                "room_section_id" => $rsid,
                                                "status_id" => 5,
                                                "added_by" => $login_id,
                                            ];
                                        }
                                    } else {
                                        $ret = $false;
                                    }
                                }
                            }
                        } else {
                            $ret = $false;
                        }
                    }
                }
            }
            // echo json_encode($enrollmentData);
            // echo count($enrollmentData);
            try {
                if (count($enrollmentData) > 0 && $login_id) {
                    // $enrollmentDataLOG = json_encode($enrollmentData);
                    if ($this->db->insert_batch("sy$sy.bs_tbl_learner_enrollment", $enrollmentData)) {
                        // $this->userlog("INSERTED NEW ENROLLEE FROM EXCEL FILE" . $enrollmentDataLOG);
                        $ret = $true;
                    } else {
                        $ret = $false;
                    }
                }

                if ($this->db->trans_status() === false) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();
                    $this->db->query("REFRESH MATERIALIZED VIEW profile.view_basicinfo;");
                    $this->db->query("REFRESH MATERIALIZED VIEW sy$sy.bs_view_enrollment;");
                }
            } catch (Exception $e) {
                // Handle the PostgreSQL duplicate key violation error here
                // For example, you can log the error or return an error message
                $ret = $false;
                // You can also access the error message using $e->getMessage()
                $errorMessage = $e->getMessage();
                $ret = +["message" => $errorMessage];
            }
        } else {
            $ret = $false;
        }
        echo json_encode($ret);
    }

    function learnerUnenroll()
    {
        $this->db->trans_begin();
        $sy = $this->getOnLoad()["sy_id"];
        $unenroll = $this->getOnLoad()["unenroll"];
        $details = $this->input->post("details");
        $pass = $this->session->schoolmis_pass;

        $true = ["success"   => true];
        $false = ["success"   => false];

        $password = md5($this->input->post("password"));
        if ($password != $pass) {
            $false += ["message"   => "Password mismatch!"];
            $ret = $false;
            echo json_encode($ret);
        } else if ($unenroll != 't') {
            $false += ["message"   => "Please contact the Administrator"];
            $ret = $false;
            echo json_encode($ret);
        } else {
            if (isset($details)) {
                $a = explode('|', $details);
                $enroll_id = $a[0];
                $learner_id = $a[1];
                $binfo_id = $a[2];

                if ($this->db->query("DELETE FROM sy$sy.bs_tbl_learner_grades WHERE MD5(learner_enrollment_id::text)='$enroll_id'")) {
                    if ($this->db->query("DELETE FROM sy$sy.bs_tbl_learner_enrollment WHERE MD5(learner_id::text)='$learner_id'")) {
                        $this->db->query("UPDATE account.tbl_useraccount SET is_active=false WHERE MD5(basic_info_id::text)='$binfo_id'");
                        if ($this->db->trans_status() === false) {
                            $this->db->trans_rollback();
                        } else {
                            $this->db->trans_commit();
                            $this->db->query("REFRESH MATERIALIZED VIEW profile.view_basicinfo;");
                            $this->db->query("REFRESH MATERIALIZED VIEW sy$sy.bs_view_enrollment;");
                        }

                        $true += ["message"   => "Successfully unenrolled!"];
                        $ret = $true;
                    } else {
                        $false += ["message"   => "Something went wrong!"];
                        $ret = $false;
                    }
                } else {
                    $false += ["message"   => "Something went wrong!"];
                    $ret = $false;
                }
                echo json_encode($ret);
            } else {
                $b = $this->input->post("b");
                $w_e = "";
                $w_l = "";
                $w_b = "";
                $count = count($b);

                for ($i = 0; $i < count($b); $i++) {
                    $parts = explode("_&&_", $b[$i]);
                    $enroll_id = $parts[4];
                    $learner_id = $parts[5];
                    $binfo_id = $parts[6];
                    $w_e .= " learner_enrollment_id = $enroll_id ";
                    $w_l .= " learner_id = $learner_id ";
                    $w_b .= " basic_info_id = $binfo_id ";
                    if ($i === ($count - 1)) {
                        // This is the last iteration, break out of the loop
                        break;
                    } else {
                        $w_e .= " OR ";
                        $w_l .= " OR ";
                        $w_b .= " OR ";
                    }
                }


                if ($this->db->query("DELETE FROM sy$sy.bs_tbl_learner_grades WHERE $w_e")) {
                    if ($this->db->query("DELETE FROM sy$sy.bs_tbl_learner_enrollment WHERE $w_l")) {
                        $this->db->query("UPDATE account.tbl_useraccount SET is_active=false WHERE $w_b");
                        if ($this->db->trans_status() === false) {
                            $this->db->trans_rollback();
                        } else {
                            $this->db->trans_commit();
                            $this->db->query("REFRESH MATERIALIZED VIEW profile.view_basicinfo;");
                            $this->db->query("REFRESH MATERIALIZED VIEW sy$sy.bs_view_enrollment;");
                        }

                        $true += ["message"   => "Successfully unenrolled!"];
                        $ret = $true;
                    } else {
                        $false += ["message"   => "Something went wrong!"];
                        $ret = $false;
                    }
                } else {
                    $false += ["message"   => "Something went wrong!"];
                    $ret = $false;
                }
                echo json_encode($ret);
            }
        }
    }


    function learnerTransfer()
    {
        $this->db->trans_begin();
        $sy = $this->getOnLoad()["sy_id"];
        $rsid = $this->input->post("g");
        $pass = $this->session->schoolmis_pass;

        $true = ["success"   => true];
        $false = ["success"   => false];

        $password = md5($this->input->post("password"));
        if ($password != $pass) {
            $false += ["message"   => "Password mismatch!"];
            $ret = $false;
            echo json_encode($ret);
        } else {

            $b = $this->input->post("b");
            $w_e = "";
            $w_l = "";
            $w_b = "";
            $count = count($b);

            for ($i = 0; $i < count($b); $i++) {
                $parts = explode("_&&_", $b[$i]);
                // $enroll_id = $parts[4];
                $learner_id = $parts[5];
                // $binfo_id = $parts[6];
                // $w_e .= " MD5(learner_enrollment_id::text)='$enroll_id' ";
                $w_l .= " learner_id=$learner_id ";
                // $w_b .= " MD5(basic_info_id::text)='$binfo_id' ";
                if ($i === ($count - 1)) {
                    // This is the last iteration, break out of the loop
                    break;
                } else {
                    $w_e .= " OR ";
                    $w_l .= " OR ";
                    $w_b .= " OR ";
                }
            }


            if ($this->db->query("UPDATE sy$sy.bs_tbl_learner_enrollment SET room_section_id=$rsid WHERE $w_l")) {
                if ($this->db->trans_status() === false) {
                    $this->db->trans_rollback();
                } else {
                    $this->db->trans_commit();
                    $this->db->query("REFRESH MATERIALIZED VIEW profile.view_basicinfo;");
                    $this->db->query("REFRESH MATERIALIZED VIEW sy$sy.bs_view_enrollment;");
                }

                $true += ["message"   => "Successfully transferred!"];
                $ret = $true;
            } else {
                $false += ["message"   => "Something went wrong!"];
                $ret = $false;
            }

            echo json_encode($ret);
        }
    }

    function updateLearnerInfo()
    {
        $true = ["success"   => true];
        $false = ["success"   => false];
        $ret = $true;
        echo json_encode($ret);
    }

    function saveGradesList_backup()
    {
        $this->db->trans_begin();
        $true = ["success"   => true];
        $false = ["success"   => false];
        if ($this->getOnLoad()["grade_stat"] == 't') {
            $data = [];
            $sy = $this->getOnLoad()["sy_id"];
            $sy_insert = (int)$sy;

            $en_id = $this->input->post("en_id");
            $rm_sec_id = $this->input->post("rm_sec_id");
            $rssaid = $this->input->post("rssaid");
            // $qrtr = $this->getOnLoad()["qrtr"];
            $igq = (string)$this->getOnLoad()["input_grades_qrtr"];

            // for ($x = 0; $x < strlen($igq); $x++) {
            // }

            // $igq1 = "";
            // $igq2 = "";
            // $igq3 = "";
            // $igq4 = "";
            // for ($x = 0; $x < strlen($igq); $x++) {
            //     $qi = $igq[$x];
            //     if ($qi == 1) {
            //         $igq1 = 1;
            //     }
            //     if ($qi == 2) {
            //         $igq2 = 2;
            //     }
            //     if ($qi == 3) {
            //         $igq3 = 3;
            //     }
            //     if ($qi == 4) {
            //         $igq4 = 4;
            //     }
            // }


            // $qrtr1 = 1;
            // $qrtr2 = 2;
            // $gradeLearner1 = $this->input->post("gradeLearner1");
            // $gradeLearner2 = $this->input->post("gradeLearner2");
            $tmpGrade = 0;

            $login_id = $this->session->schoolmis_login_id;
            $dateNow = $this->now();
            $ret = $true;
            // if ($gradeLearner1 != null) {


            for ($x = 0; $x < strlen($igq); $x++) {
                $qi = $igq[$x];
                // if ($qi == 1) {
                //     $igq1 = 1;
                // }
                // if ($qi == 2) {
                //     $igq2 = 2;
                // }
                // if ($qi == 3) {
                //     $igq3 = 3;
                // }
                // if ($qi == 4) {
                //     $igq4 = 4;
                // }
                $data = [];
                if ($qi >= 1 && $qi <= 4) {
                    $gradeLearner = $this->input->post("gradeLearner" . $qi);
                    for ($i = 0; $i < count($en_id); $i++) {

                        if (isset($gradeLearner[$i])) {
                            $en = $en_id[$i];
                            $rm = $rm_sec_id[$i];
                            $rssaid1 = $rssaid[$i];
                            $grades = $this->returnNull($gradeLearner[$i]);
                            $search = $this->db->query("SELECT t1.* FROM sy$sy.bs_tbl_learner_grades t1
                                            WHERE t1.learner_enrollment_id=$en AND t1.rm_sctn_sbjct_assgnmnt_id=$rssaid1 
                                            AND t1.qrtr_id=$qi AND t1.sy_id=$sy");
                            $srow = $search->row();

                            if ($search->num_rows() > 0) {
                                $tmpId = $srow->id;
                                $tmpGrade = $srow->grade;
                                if ($grades == $tmpGrade) {
                                } else {
                                    $update_data = array(
                                        'grade' => $grades,
                                        'date_updated' => $dateNow,
                                        'updated_by' => $login_id
                                    );

                                    // $this->db->where('learner_enrollment_id', $en);
                                    // $this->db->where('room_section_id', $rm);
                                    // $this->db->where('qrtr_id', $qrtr);
                                    // $this->db->where('sy_id', $sy);
                                    $this->db->where('id', $tmpId);
                                    if ($this->db->update("sy$sy.bs_tbl_learner_grades", $update_data)) {
                                        $ret = $true;
                                    } else {
                                        $ret = $false;
                                    }
                                }
                            } else {
                                $data[] = [
                                    'learner_enrollment_id' => $en,
                                    'rm_sctn_sbjct_assgnmnt_id' => $rssaid1,
                                    'qrtr_id' => $qi,
                                    'grade' => $grades,
                                    'date_added' => $dateNow,
                                    'added_by' => $login_id,
                                    'status_id' => 1,
                                    'sy_id' => $sy_insert,
                                ];
                            }
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
                } else {
                    $ret = $false;
                }
            }
        } else {
            $ret = $false;
        }

        echo json_encode($ret);

        $this->db->query("REFRESH MATERIALIZED VIEW sy$sy.bs_m_view_all_grades_stat;
        REFRESH MATERIALIZED VIEW sy$sy.bs_m_view_grades;
        REFRESH MATERIALIZED VIEW sy$sy.bs_m_view_qrtr_grades_stat;");
    }

    function saveGradesList()
    {
        $this->db->trans_begin();
        $true = ["success"   => true];
        $false = ["success"   => false];
        if ($this->getOnLoad()["grade_stat"] == 't') {
            $data = [];
            $data_update = [];
            $q1 = null;
            $q2 = null;
            $q3 = null;
            $q4 = null;

            $sy = $this->getOnLoad()["sy_id"];

            $en_id = $this->input->post("en_id");
            $rssaid = $this->input->post("rssaid");

            $login_id = $this->session->schoolmis_login_id;
            $dateNow = $this->now();
            $ret = $true;

            $ql1 = $this->returnEmptyArr($this->input->post("gradeLearner1"));
            $ql2 = $this->returnEmptyArr($this->input->post("gradeLearner2"));
            $ql3 = $this->returnEmptyArr($this->input->post("gradeLearner3"));
            $ql4 = $this->returnEmptyArr($this->input->post("gradeLearner4"));

            $q1 = $this->input->post("gradeLearner1");
            $q2 = $this->input->post("gradeLearner2");
            $q3 = $this->input->post("gradeLearner3");
            $q4 = $this->input->post("gradeLearner4");

            $data = [];
            $data_update = [];
            for ($i = 0; $i < count($en_id); $i++) {
                $en = $en_id[$i];
                $rssaid1 = $rssaid[$i];
                $search = $this->db->query("SELECT t1.* FROM sy$sy.bs_tbl_learner_grades t1
                WHERE t1.learner_enrollment_id=$en AND t1.rm_sctn_sbjct_assgnmnt_id=$rssaid1");
                $srow = $search->row();

                $qq1 = $ql1 > 0 ? $this->returnNull($q1[$i]) : null;
                $qq2 = $ql2 > 0 ? $this->returnNull($q2[$i]) : null;
                $qq3 = $ql3 > 0 ? $this->returnNull($q3[$i]) : null;
                $qq4 = $ql4 > 0 ? $this->returnNull($q4[$i]) : null;

                if ($search->num_rows() > 0) {
                    $data_update[$i] = [
                        'id' => $srow->id,
                        'date_updated' => $dateNow,
                        'updated_by' => $login_id,
                    ];
                    $ql1 > 0 ? $data_update[$i] += ['q1' => $qq1] : '';
                    $ql2 > 0 ? $data_update[$i] += ['q2' => $qq2] : '';
                    $ql3 > 0 ? $data_update[$i] += ['q3' => $qq3] : '';
                    $ql4 > 0 ? $data_update[$i] += ['q4' => $qq4] : '';
                }

                if ($search->num_rows() == 0) {
                    $data[$i] = [
                        'learner_enrollment_id' => $en,
                        'rm_sctn_sbjct_assgnmnt_id' => $rssaid1,
                        'date_added' => $dateNow,
                        'added_by' => $login_id,
                    ];
                    $ql1 > 0 ? $data[$i] += ['q1' => $qq1] : '';
                    $ql2 > 0 ? $data[$i] += ['q2' => $qq2] : '';
                    $ql3 > 0 ? $data[$i] += ['q3' => $qq3] : '';
                    $ql4 > 0 ? $data[$i] += ['q4' => $qq4] : '';
                }
            }
            if (count($data) > 0 && $login_id) {
                if ($this->db->insert_batch("sy$sy.bs_tbl_learner_grades", $data)) {
                    $ret = $true;
                } else {
                    $ret = $false;
                }
            }

            if (count($data_update) > 0 && $login_id) {
                if ($this->db->update_batch("sy$sy.bs_tbl_learner_grades", $data_update, 'id')) {
                    $ret = $true;
                } else {
                    $ret = $false;
                }
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
                $this->db->query("REFRESH MATERIALIZED VIEW sy$sy.bs_m_view_grades;
                                  REFRESH MATERIALIZED VIEW sy$sy.bs_m_view_all_grades_stat;");
            }
        } else {
            $ret = $false;
        }

        echo json_encode($ret);
    }

    function saveGradesPSList()
    {
        $this->db->trans_begin();
        $true = ["success"   => true];
        $false = ["success"   => false];
        if ($this->getOnLoad()["grade_stat"] == 't') {
            $data = [];
            $data_update = [];
            $q1 = null;
            $q2 = null;
            $q3 = null;
            $q4 = null;

            $sy = $this->getOnLoad()["sy_id"];

            $en_id = $this->input->post("en_id");
            $rssaid = $this->input->post("rssaid");

            $login_id = $this->session->schoolmis_login_id;
            $dateNow = $this->now();
            $ret = $true;

            $q1 = $this->returnNull($this->input->post("gradeLearner1"));
            $q2 = $this->returnNull($this->input->post("gradeLearner2"));
            $q3 = $this->returnNull($this->input->post("gradeLearner3"));
            $q4 = $this->returnNull($this->input->post("gradeLearner4"));

            $data = [];
            $data_update = [];
            for ($i = 0; $i < count($en_id); $i++) {

                $en = $en_id[$i];
                $rssaid1 = $rssaid[$i];


                $search = $this->db->query("SELECT t1.* FROM sy$sy.bs_tbl_learner_grades_ps t1
                WHERE t1.learner_enrollment_id=$en AND t1.rm_sctn_sbjct_assgnmnt_id=$rssaid1");
                $srow = $search->row();

                if ($search->num_rows() > 0) {
                    $data_update[] = [
                        'id' => $srow->id,
                        'q1' => $q1 ? $this->returnNull($q1[$i]) : null,
                        'q2' => $q2 ? $this->returnNull($q2[$i]) : null,
                        'q3' => $q3 ? $this->returnNull($q3[$i]) : null,
                        'q4' => $q4 ? $this->returnNull($q4[$i]) : null,
                        'date_updated' => $dateNow,
                        'updated_by' => $login_id,
                    ];
                } else {
                    $data[] = [
                        'learner_enrollment_id' => $en,
                        'rm_sctn_sbjct_assgnmnt_id' => $rssaid1,
                        'q1' => $q1 ? $this->returnNull($q1[$i]) : null,
                        'q2' => $q2 ? $this->returnNull($q2[$i]) : null,
                        'q3' => $q3 ? $this->returnNull($q3[$i]) : null,
                        'q4' => $q4 ? $this->returnNull($q4[$i]) : null,
                        'date_added' => $dateNow,
                        'added_by' => $login_id,
                    ];
                }
            }
            if (count($data) > 0 && $login_id) {
                if ($this->db->insert_batch("sy$sy.bs_tbl_learner_grades_ps", $data)) {
                    $ret = $true;
                } else {
                    $ret = $false;
                }
            }

            if (count($data_update) > 0 && $login_id) {
                if ($this->db->update_batch("sy$sy.bs_tbl_learner_grades_ps", $data_update, 'id')) {
                    $ret = $true;
                } else {
                    $ret = $false;
                }
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
                $this->db->query("REFRESH MATERIALIZED VIEW sy$sy.bs_m_view_grades_ps;");
            }
        } else {
            $ret = $false;
        }

        echo json_encode($ret);
    }

    function learnerAccount()
    {
        $this->db->trans_begin();
        $uuid = $this->input->post("a");
        $b = $this->input->post("b");
        $sy = $this->getOnLoad()["sy_id"];
        $login_id = $this->session->schoolmis_login_id;
        $dateNow = $this->now();
        $true = ["success"   => true];
        $false = ["success"   => false];
        $query = $this->db->query("SELECT t1.basic_info_id, t1.lrn FROM sy$sy.bs_view_enrollment t1
                                    WHERE t1.person_uuid='$uuid'");
        $row = $query->row();
        if ($query->num_rows() > 0) {
            $data = [
                "basic_info_id" => $row->basic_info_id,
                "role_id" => 8,
                "username" => $row->lrn,
                "password" => md5(12345678),
                "change_pwd" => true,
                "is_active" => true,
                "added_by" => $login_id,
                "date_added" => $dateNow,
            ];

            if (count($data) > 0 && $b == 0 && $login_id) {
                if ($this->db->insert("account.tbl_useraccount", $data)) {
                    // $inid = $this->db->insert_id();
                    // $this->userlog("CREATED NEW STUDENT ACCOUNT " . $inid . " " . json_encode($data));
                    $ret = $true;
                } else {
                    $ret = $false;
                }
            } else if (count($data) > 0 && $b == 1 && $login_id) {
                $update_data = array(
                    'password' => md5(12345678),
                    "change_pwd" => true,
                    'date_updated' => $dateNow,
                    'updated_by' => $login_id
                );
                $this->db->where('username', $row->lrn);
                if ($this->db->update("account.tbl_useraccount", $update_data)) {
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
        }

        echo json_encode($ret);
    }

    function submitGrades()
    {

        $this->db->trans_begin();
        $true = ["success"   => true];
        $false = ["success"   => false];
        $sy = $this->getOnLoad()["sy_id"];
        $qrtr = $this->getOnLoad()["qrtr"];
        $login_id = $this->session->schoolmis_login_id;

        parse_str($this->input->post("c"), $filter);
        // $password = md5($filter['password']);
        // $pass = $this->session->schoolmis_pass;

        // if ($pass == $password) {
        if ($sy && $qrtr && $login_id) {
            //1 3221232
            //2 2123221
            //3 3211123
            //4 4522323
            $remarks = strtoupper($filter["remarks"]);
            $qrssa = $filter["qrssa"];
            $rssa_id = $this->input->post("e");
            $q = $qrssa == 3221232 ? 1 : ($qrssa == 2123221 ? 2 : ($qrssa == 3211123 ? 3 : ($qrssa == 4522323 ? 4 : null)));


            $data = [
                "is_active" => false
            ];

            // $this->db->where('rssa_id', $rssa_id);
            if ($this->db->query("UPDATE sy$sy.bs_tbl_learner_grades_stat 
                                  SET is_active=false WHERE rssa_id=$rssa_id AND qrtr=$q AND sy_id=$sy")) {
                $data1 = [
                    "rssa_id" => $rssa_id,
                    "status_id" => 17,
                    "sy_id" => $sy,
                    "qrtr" => $q,
                    "remarks" => $remarks,
                    "added_by" => $login_id,
                ];
                if ($this->db->insert("sy$sy.bs_tbl_learner_grades_stat", $data1)) {
                    $true += ["message"   => "Successfully submitted!"];
                    $ret = $true;
                }
            }
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
            } else {
                $this->db->trans_commit();
                $this->db->query("REFRESH MATERIALIZED VIEW sy$sy.bs_m_view_grades;
                                  REFRESH MATERIALIZED VIEW sy$sy.bs_m_view_all_grades_stat;");
            }
        } else {
            $false += ["message"   => "Password mismatch!"];
            $ret = $false;
        }

        echo json_encode($ret);
    }
}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */