<?php //if ( ! defined('BASEPATH')) exit('No direct script access allowed');
defined('BASEPATH') or exit('No direct script access allowed');

class Getdata extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // $this->redirect();
        $this->load->model('mainModel');
        $this->load->helper('date');
        date_default_timezone_set("Asia/Manila");
    }

    function getCityMunList()
    {
        $v = $this->input->post("v");
        $filter = $v == '' || $v == null ? 0 : $v;
        $data = $this->CityMunList($filter, 1);
        echo json_encode($data);
    }

    function getBarangayList()
    {
        $v = $this->input->post("v");
        $filter = $v == '' || $v == null ? 0 : $v;
        $data = $this->BarangayList($filter);
        echo json_encode($data);
    }

    function getPurokList()
    {
        $v = $this->input->post("v");
        $filter = $v == '' || $v == null ? 0 : $v;
        $data = $this->PurokList($filter);
        echo json_encode($data);
    }

    function getPartyList()
    {
        $v = $this->input->post("v");
        $filter = $v == '' || $v == null ? 0 : $v;
        $data = $this->PartyList($filter);
        echo json_encode($data);
    }

    function getPartyTypeList()
    {
        $v = $this->input->post("v");
        $filter = $v == '' || $v == null ? 0 : $v;
        $data = $this->PartyTypeList($filter);
        echo json_encode($data);
    }

    function getStatusList()
    {
        $v = $this->input->post("v");
        $filter = $v == '' || $v == null ? 0 : $v;
        $data = $this->StatusList($filter);
        echo json_encode($data);
    }

    function getAssignedSectionList()
    {
        $data = ["data" => []];
        $data2 = [];
        $c = 1;
        $personnel_id = $this->session->schoolmis_login_prsnnl_Id;
        $uri = $this->session->schoolmis_login_uri;
        $sy = $this->getOnLoad()["sy_id"];
        $enroll_stat = $this->getOnLoad()["enroll_stat"];
        $grade_stat = $this->getOnLoad()["grade_stat"];

        $requestData = $_REQUEST;
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';
        $k12 = isset($requestData['k12']) ? $requestData['k12'] : '';
        $prgrm_strnd = isset($requestData['prgrm_strnd']) ? $requestData['prgrm_strnd'] : '';
        $grade = isset($requestData['grade']) ? $requestData['grade'] : '';
        $fltr = "";
        if ($k12) {
            $fltr = " AND t3.id=$k12";
        }
        if ($prgrm_strnd) {
            $fltr .= " AND t1.program_strand_id=$prgrm_strnd";
        }
        if ($grade) {
            $fltr .= " AND t1.gradelvl_id=$grade";
        }

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // $queryz = $this->db->query("SELECT * FROM global.tbl_party
        //                             WHERE party_type_id=17 AND CONCAT(description,abbr,(CASE WHEN is_active = 't' THEN 'ACTIVE' ELSE 'INACTIVE' END)) ILIKE '%$searchValue%'
        //                             ORDER BY order_by ASC
        //                             LIMIT $limit OFFSET $offset");


        if ($uri == 'userteacher') {
            // $query = $this->db->query("SELECT t1.room_section_id,
            //                             t1.rm_sctn_sbjct_assgnmnt_id,
            //                             t1.grade,t1.schedule,t1.subject_abbr,
            //                             t1.sctn_nm,t1.full_name,t1.advisory 
            //                             FROM building_sectioning.view_subject_grdlvl_personnel_assgnmnt t1 
            //                             -- LEFT JOIN (SELECT * FROM building_sectioning.view_subject_grdlvl_personnel_assgnmnt
            //                             --                                         WHERE advisory=true
            //                             --                         ) t2 ON t1.room_section_id=t2.room_section_id
            //                             WHERE t1.schoolpersonnel_id=1 AND t1.schl_yr_id=$sy 
            //                             ORDER BY t1.advisory DESC,t1.sctn_nm,t1.order_by_sbjct");

            // Query to get total record count
            $thisQuery = $this->db->query("SELECT count(1) AS total 
                                            FROM building_sectioning.view_subject_grdlvl_personnel_assgnmnt t1 
                                            WHERE t1.schoolpersonnel_id=$personnel_id AND t1.schl_yr_id=$sy AND CONCAT(t1.room_section_id, t1.rm_sctn_sbjct_assgnmnt_id, t1.grade,t1.schedule, t1.subject_abbr, t1.sctn_nm,t1.full_name,t1.advisory) ILIKE '%$searchValue%'");
            $totalRecords = $thisQuery->row()->total;

            $query = $this->db->query("SELECT t1.room_section_id, t1.rm_sctn_sbjct_assgnmnt_id, t1.grade,t1.schedule, t1.subject_abbr, t1.sctn_nm,t1.full_name,t1.advisory 
                                            FROM building_sectioning.view_subject_grdlvl_personnel_assgnmnt t1 
                                            WHERE t1.schoolpersonnel_id=$personnel_id AND t1.schl_yr_id=$sy AND CONCAT(t1.room_section_id, t1.rm_sctn_sbjct_assgnmnt_id, t1.grade,t1.schedule, t1.subject_abbr, t1.sctn_nm,t1.full_name,t1.advisory) ILIKE '%$searchValue%' 
                                            ORDER BY t1.advisory DESC,t1.sctn_nm,t1.order_by_sbjct
                                            LIMIT $limit OFFSET $offset");
        } elseif ($uri == 'userschooladmin') {
            // $query = $this->db->query("SELECT t1.room_section_id,
            //                             t1.rm_sctn_sbjct_assgnmnt_id,
            //                             t1.grade,t1.schedule,t1.subject_abbr,
            //                             t1.sctn_nm,t1.full_name,t1.advisory 
            //                             FROM building_sectioning.view_subject_grdlvl_personnel_assgnmnt t1
            //                             WHERE t1.schl_yr_id=1  AND t1.advisory = true
            //                             ORDER BY t1.advisory DESC,t1.sctn_nm,t1.order_by_sbjct");
            $thisQuery = $this->db->query("SELECT count(1) AS total
                                            FROM building_sectioning.view_subject_grdlvl_personnel_assgnmnt t1 
                                            LEFT JOIN global.tbl_party t2 ON t1.gradelvl_id = t2.id 
                                            LEFT JOIN global.tbl_partytype t3 ON t2.party_type_id = t3.id
                                            WHERE t1.schl_yr_id=$sy $fltr AND t1.advisory = true  AND CONCAT(t1.room_section_id, t1.rm_sctn_sbjct_assgnmnt_id, t1.grade,t1.schedule,t1.subject_abbr, t1.sctn_nm,t1.full_name,t1.advisory) ILIKE '%$searchValue%'");
            $totalRecords = $thisQuery->row()->total;

            $query = $this->db->query("SELECT t1.room_section_id, t1.rm_sctn_sbjct_assgnmnt_id, t1.grade,t1.schedule,t1.subject_abbr, t1.sctn_nm,t1.full_name,t1.advisory 
                                            FROM building_sectioning.view_subject_grdlvl_personnel_assgnmnt t1
                                            LEFT JOIN global.tbl_party t2 ON t1.gradelvl_id = t2.id 
                                            LEFT JOIN global.tbl_partytype t3 ON t2.party_type_id = t3.id
                                            WHERE t1.schl_yr_id=$sy $fltr AND t1.advisory = true  AND CONCAT(t1.room_section_id, t1.rm_sctn_sbjct_assgnmnt_id, t1.grade,t1.schedule,t1.subject_abbr, t1.sctn_nm,t1.full_name,t1.advisory) ILIKE '%$searchValue%'
                                            ORDER BY t1.advisory DESC,t1.sctn_nm,t1.order_by_sbjct
                                            LIMIT $limit OFFSET $offset");
        }

        $data = array();
        // $cc = $offset + 1;
        foreach ($query->result() as $key => $value) {
            $rmid = $value->room_section_id;
            $query1 = $this->db->query("SELECT t1.*,t2.male,t2.female,t2.total_enrollee FROM building_sectioning.view_room_section t1
                                        LEFT JOIN (SELECT t1.room_section_id,t1.schl_yr_id, SUM(CASE WHEN t1.sex_bool='t' THEN 1 ELSE 0 END) AS male,
                                        SUM(CASE WHEN t1.sex_bool='f' THEN 1 ELSE 0 END) AS female, SUM(1) AS total_enrollee
                                        FROM sy$sy.bs_view_enrollment t1
										GROUP by t1.room_section_id,t1.schl_yr_id) t2 ON t1.id=t2.room_section_id
                                        AND t1.schl_yr_id=t2.schl_yr_id
                                        WHERE t1.id = $rmid");
            $qrow = $query1->row();
            $rssaid = $value->rm_sctn_sbjct_assgnmnt_id;
            $g = $value->grade;
            $sched = $value->schedule;
            $subject = $value->subject_abbr;
            $s = $value->sctn_nm;
            $advsry = $value->advisory;
            $img = $this->getImg($qrow->img_path);
            $male = number_format($qrow->male) ?? "-";
            $female = number_format($qrow->female) ?? "-";
            $t_enrollee = number_format($qrow->total_enrollee) ?? "-";
            $conso = ($advsry == 't') ? '<a class="dropdown-item text-xs" role="button" onclick="reportsConsoGrades()">CONSOLIDATED GRADES</a>' : '';
            $smea = ($advsry == 't') ? '<a class="dropdown-item text-xs" role="button" onclick="reportsConsoGrades()">CONSOLIDATED GRADES</a>' : '';
            $enroll = ($advsry == 't' && $enroll_stat == 't') ? '<a class="dropdown-item bg-success text-xs" role="button" data-toggle="modal" onclick="clear_form1();closeCardBodyIfOpen();" data-target="#modalEnrollment">ENROLLMENT</a>' : '';
            $grade = ''; #($grade_stat == 't') ? '<a class="dropdown-item bg-primary text-xs" role="button" data-toggle="modal" onclick="getGradesListFN();getGradesPSListFN();" data-target="#modalGradesList">GRADES & EXAM</a>' : '';
            // $q_exam = '<a class="dropdown-item bg-pink text-xs" role="button" data-toggle="modal" onclick="getGradesPSListFN()" data-target="#modalGradesPSList">QUARTER EXAM/PS</a>';
            $grade_all = ''; #($advsry == 't') ? '<a class="dropdown-item bg-info text-xs" role="button" data-toggle="modal" onclick="customTabViewAllGrades()" data-target="#modalAllGrades">GRADES STATUS</a>' : '';

            // $gradesDL = '<a class="dropdown-item bg-primary text-xs" role="button"' .
            //     " onclick=\"getGradesSMEAListFN(1)\">GRADES FORM</a>";
            // $qeDL = '<a class="dropdown-item bg-pink text-xs" role="button"' .
            //     " onclick=\"getGradesSMEAListFN(2)\">QUARTER EXAM FORM</a>";

            $data2 = [
                "img_path_personnel" => '<img class="img-circle img-bordered-sm" src="' . $img . '" alt="user image">',
                "personnel" => $qrow->full_name . " - " . $qrow->personal_title,
                "description" => "Class Advisory <b>" . $qrow->grade . " - " . $qrow->sctn_nm . "</b><small> <i>" . $qrow->subject . "</i> | <i>" . $qrow->sched . "</i></small>",
                "male" => $male,
                "female" => $female,
                "total_enrollee" => $t_enrollee,
                // "reports" => '<div class="float-right ml-1">
                //             <button class="btn btn-xs bg-navy" data-toggle="dropdown"><b style="font-size:10px;"><i class="fa fa-file"></i> REPORTS</b> | <i class="fa fa-caret-down"></i></button>
                //             <div class="dropdown-menu p-0" role="menu">
                //             ' . $conso  . '</div></div>',

                "forms" => '<div class="float-right ml-1">
                            <button class="btn btn-xs bg-navy" data-toggle="dropdown"><b style="font-size:10px;"><i class="fa fa-list-ul"></i> FORMS</b> | <i class="fa fa-caret-down"></i></button>
                            <div class="dropdown-menu p-0" role="menu">
                            ' . $enroll  . $grade  . $grade_all . '</div></div>',

                // "downloads" => '<div class="float-right ml-1">
                //             <button class="btn btn-xs bg-navy" data-toggle="dropdown"><b style="font-size:10px;"><i class="fa fa-download"></i> DOWNLOADS</b> | <i class="fa fa-caret-down"></i></button>
                //             <div class="dropdown-menu p-0" role="menu">' .
                //     $gradesDL  . $qeDL  . "</div></div>",

                // "others" => ($advsry == 't' || $uri == 'userschooladmin') ? '<button type="button" class="btn btn-xs text-sm float-right btn-outline-secondary rounded-circle border-0 ml-1" data-toggle="dropdown" aria-expanded="true">
                //                                     <span class="fa fa-ellipsis-h"></span>
                //                                 </button>
                //                                 <div class="dropdown-menu p-0" role="menu">
                //                                     <a class="dropdown-item text-xs" role="button" onclick="logsHideShow()">Logs & Account Settings</a>
                //                                     <a class="dropdown-item text-xs" role="button" onclick="allStudentLogs()">View All Student Logs</a>
                //                                 </div>' : '',
            ];

            $arr2 = json_encode($data2);
            $slct = ($advsry === 't' && $key === array_key_first($query->result()) ? 'slctdRadioAdvisory' : '');
            $data[] = [
                "<div class='row' style='white-space: nowrap;margin-left:-1.2rem;'>
                    <div class='col-12 " . ($advsry === 't' ? 'text-success' : '') . "'>
                        <input type='radio' id='slctRmRadio" . $rmid . $rssaid . "' class='" . $slct . "' name='slctRm' value='" . $rmid . "' 
                                onclick='getLearnersListFN(\"LearnersList\"," . $rmid . "," . $rssaid . ",\"" . $advsry . "\",\"" . $s . "\");
                                        getDetails(\"PersonnelInfo\",$arr2,1,\".\");
                        '/>
                        <label class='w-100'  style='cursor:pointer' for='slctRmRadio" . $rmid . $rssaid . "'>" .
                    ($uri == 'userteacher' ?
                        "<span class='badge text-sm pb-0'>$g - $s</span><small>$qrow->code - <i>$subject</i> | <i>$sched</i></small>
                            <small class='float-right'><b class='text-primary'>" . $male . "</b> + <b class='text-pink'>" . $female . "</b> = <b>" . $t_enrollee . "</b></small><br/>" :

                        "<span class='badge text-sm pb-0' style='color:#000;'>$g - $s</span><i style='color:$qrow->program_strand_color;'>$qrow->program_strand_abbr</i>" .
                        ($male == 0 && $female == 0 ? "" : "<small class='float-right'><b class='text-primary'>" . $male . "</b> + <b class='text-pink'>" . $female . "</b> = <b>" . $t_enrollee . "</b></small>")) .
                    "</label>
                    </div>
                </div>",
            ];
        }
        $response = array(
            'draw' => intval($requestData['draw']),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords), // For simplicity, assuming no filtering is applied
            'data' => $data,
        );
        echo json_encode($response);
    }

    function getViewAllGrades()
    {
        $rsid = $this->input->get("a");
        $tab = null;
        $tab2 = null;
        $content = null;
        $content2 = null;
        $sy = $this->getOnLoad()["sy_id"];
        $qrtr = $this->getOnLoad()["qrtr"];
        $query = $this->db->query("SELECT t1.subject_abbr,t1.subject,t1.subject_id,t1.full_name,t1.img_path
        t1.personal_title,t1.advisory,t1.schedule,t1.grade,t1.sctn_nm,t1.parent_party_id,
        t2.parent FROM building_sectioning.view_subject_grdlvl_personnel_assgnmnt t1
                                    LEFT JOIN (SELECT t1.parent_party_id AS parent FROM global.tbl_party t1
                                                            WHERE t1.parent_party_id IS NOT NULL
                                                            GROUP BY t1.parent_party_id) t2 ON t1.subject_id = t2.parent
                                    WHERE t1.room_section_id=$rsid AND t1.schl_yr_id=$sy AND t1.full_name IS NOT NULL ORDER BY t1.order_by_sbjct");
        foreach ($query->result() as $key => $value) {
            $sbjctabbr = $value->subject_abbr;
            $sbjct = $value->subject;
            $sbjctid = $value->subject_id;
            $teacher = $value->full_name;
            $img = $value->img_path;
            $title = $value->personal_title;
            $advisory = $value->advisory == 't' ? 'Class Advisory ' : '';
            $schedule = $value->schedule;
            $grd_sctn = $value->grade . ' - ' . $value->sctn_nm;
            $active = "";
            $parnt_prty_id = $value->parent_party_id;
            $parnt = $value->parent;

            $a = null;
            $b = null;
            $c = null;

            $a2 = null;
            $b2 = null;
            $c2 = null;
            if ($key === array_key_first($query->result())) {
                $active = "active";
            }
            $t = '<li class="nav-item">
                    <a onclick="' . (($parnt == $sbjctid) || ($parnt_prty_id)
                ? "$('.viewAllGrades2, .sbjct_group').show();$('.sbjct_sngl').hide();$('.viewAllGrades2 .card-header .nav .nav-item .nav-link').removeClass('active');"
                : "$('.viewAllGrades2, .sbjct_group').hide();$('.sbjct_sngl').show()") . '" 
                        class="nav-link ' . $active . '" title="' . $sbjct . '" 
                        id="custom-tabs-' . $sbjctid . '-tab" 
                        data-toggle="pill" 
                        href="#custom-tabs-' . $sbjctid . '" 
                        role="tab" aria-controls="custom-tabs-' . $sbjctid . '" 
                        aria-selected="false" 
                        >' . $sbjctabbr . '</a>
                        <script>' . (($parnt == $sbjctid) || ($parnt_prty_id) ? "$('.viewAllGrades2, .sbjct_group').hide()" : "") . '
                            document.querySelector("#custom-tabs-77-tab")                            
                            document.querySelector("#custom-tabs-four-tab > li:nth-child(1)")
                        </script>
                </li>';
            if ($parnt == $sbjctid) {
                $tab2 .= $t;
            }

            if (!$parnt_prty_id) {
                $tab .= $t;
            } else {
                $tab2 .= $t;
            }


            $query2 = $this->db->query("SELECT t4.q1,t4.q2,t4.q3,t4.q4,t5.advisory,t5.full_name AS teacher_full_name,t5.personal_title,t1.id,
                                        t3.last_fullname,t3.enrollment_status,t3.sex,t3.lrn FROM building_sectioning.tbl_room_section_subject_assignment t1
                                        LEFT JOIN building_sectioning.tbl_room_section t2 ON t1.room_section_id=t2.id
                                        LEFT JOIN sy$sy.bs_view_enrollment t3 ON t1.room_section_id=t3.room_section_id
				                        LEFT JOIN building_sectioning.view_subject_grdlvl_personnel_assgnmnt t5 ON t1.room_section_id=t5.room_section_id AND t1.subject_id=t5.subject_id
                                        LEFT JOIN (

                                                    SELECT t1.learner_enrollment_id,t1.rm_sctn_sbjct_assgnmnt_id,
                                                            CASE WHEN(COALESCE(t1.q1stat,'')!='18')THEN 0 ELSE t1.q1 END AS q1,
                                                            CASE WHEN(COALESCE(t1.q2stat,'')!='18')THEN 0 ELSE t1.q2 END AS q2,
                                                            CASE WHEN(COALESCE(t1.q3stat,'')!='18')THEN 0 ELSE t1.q3 END AS q3,
                                                            CASE WHEN(COALESCE(t1.q4stat,'')!='18')THEN 0 ELSE t1.q4 END AS q4 FROM sy$sy.bs_m_view_grades t1
                                            
                                                    )
                                                    t4 ON t3.enrollment_id=t4.learner_enrollment_id AND t1.id=t4.rm_sctn_sbjct_assgnmnt_id
                                        WHERE t5.subject_id=$sbjctid AND t3.room_section_id=$rsid
                                        ORDER BY t3.sex DESC, t3.last_fullname");


            $aa = '<div class="tab-pane fade show ' . $active . ' " id="custom-tabs-' . $sbjctid . '" role="tabpanel" aria-labelledby="custom-tabs-' . $sbjctid . '-tab">
            <div class="row ' . (($parnt == $sbjctid) || ($parnt_prty_id) ? 'sbjct_group' : 'sbjct_sngl') . '">
                <div class="col-12">
                    <div class="post">
                        <div class="user-block">
                            <img class="img-circle img-bordered-sm" src="' . base_url() . $img . '" alt="user image">
                            <span class="username">
                                <a role="button" class="personnel">' . $teacher . ' - ' . $title . '</a>
                            </span>
                            <span class="description">' . $advisory . '<b>' . $grd_sctn . '</b><small> <i>' . $sbjct . '</i> | <i>' . $schedule . '</i></small></span>
                            <!-- <span class="description">Class Advisory <b>Grade XII - ABCD</b> <small><i>ARTS</i> | <i>WD</i></small></span> -->
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-sm table-hover table-striped ' . (($parnt == $sbjctid) || ($parnt_prty_id) ? 'sbjct_group' : 'sbjct_sngl') . '" id="tblAllGradesList' . $sbjctid . '" width="100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>LRN</th>
                                <th>Personal Details</th>
                                <th>Sex</th>
                                <th>Status</th>
                                <th>Q1</th>
                                <th>Q2</th>
                                <th>Q3</th>
                                <th>Q4</th>
                                <th>AVG</th>
                            </tr>
                        </thead>
                        <tbody>';

            if ($parnt == $sbjctid) {
                $a2 .= $aa;
            }

            if (!$parnt_prty_id) {
                $a .= $aa;
            } else {
                $a2 .= $aa;
            }

            $c_male = 1;
            $c_fmale = 1;
            foreach ($query2->result() as $key2 => $value2) {
                // $birthDate = date_create($value2->birthdate);
                // $birthDate = strtoupper(date_format($birthDate, "m-d-Y"));
                $sex = substr($value2->sex, 0, 1);
                $q1 = $value2->q1;
                $q2 = $value2->q2;
                $q3 = $value2->q3;
                $q4 = $value2->q4;
                $avg = round(($q1 + $q2 + $q3 + $q4) / 4, 0);
                $v = $qrtr == 1 ? $q1 : ($qrtr == 2 ? $q2 : ($qrtr == 3 ? $q3 : $q4));
                $c_fmale == 1 && $sex == 'F' ?
                    $c .= '<tr>
                                <td> </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>' : "";
                $c .= '<tr>
                                <td>' . ($sex == 'M' ? $c_male++ : $c_fmale++) . '</td>
                                <td>' . $value2->lrn . '</td>
                                <td>' . $value2->last_fullname . '</td>
                                <td>' . $sex . '</td>
                                <td>' . $value2->enrollment_status . '</td>
                                <td>' . $this->gradeColor($q1) . '</td>
                                <td>' . $this->gradeColor($q2) . '</td>
                                <td>' . $this->gradeColor($q3) . '</td>
                                <td>' . $this->gradeColor($q4) . '</td>
                                <td>' . $this->gradeColor($avg) . '</td>
                            </tr>';
            }


            $b =      '</tbody>
                    </table>
                  </div>';



            if ($parnt == $sbjctid) {
                $content2 .= $a2 . $c . $b;
            }

            if (!$parnt_prty_id) {
                $content .= $a . $c . $b;
            } else {
                $content2 .= $a2 . $c . $b;
            }
        }

        $data = '<div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">' . $tab . '</ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-one-tabContent">
                        
                        <div class="card card-info p-0 mt-n2 table-responsive viewAllGrades2">
                            <div class="card-header p-0 border-bottom-0">
                                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">' . $tab2 . '</ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="custom-tabs-one-tabContent">
                                    ' . $content2 . '
                                </div>
                            </div>
                        </div>
                        ' . $content . '
                    </div>
                </div>';
        echo json_encode($data);
    }

    function getSectionList()
    {
        $data = ["data" => []];
        $c = 1;
        $glvl_id = $this->input->get("g");
        $sy = $this->getOnLoad()["sy_id"];
        $query = $this->db->query("SELECT id, sctn_nm FROM building_sectioning.tbl_room_section trs 
                                    WHERE schl_yr_id = $sy AND is_active = TRUE AND grd_lvl_id = $glvl_id 
                                    ORDER BY sctn_nm");
        foreach ($query->result() as $key => $value) {
            $data["data"][] = [
                "a" => $value->id,
                "b" => $value->sctn_nm,
            ];
        }
        echo json_encode($data);
    }

    function getLearnersList()
    {
        $data = ["data" => []];
        $data2 = [];
        $c_male = 1;
        $c_fmale = 1;
        $personnel_id = $this->session->schoolmis_login_prsnnl_Id;
        $sy = $this->getOnLoad()["sy_id"];
        $edit = $this->getOnLoad()["edit"];
        $unenroll = $this->getOnLoad()["unenroll"];
        $rsid = $this->input->post("rsid");
        $uri = $this->session->schoolmis_login_uri;

        $q = $this->db->query("SELECT 1 AS cc FROM building_sectioning.view_subject_grdlvl_personnel_assgnmnt t1
                                WHERE t1.room_section_id=$rsid AND t1.advisory='t' AND t1.schl_yr_id=$sy and t1.schoolpersonnel_id=$personnel_id LIMIT 1");

        $q1 = $q->row();
        $advsry = $q1->cc ?? 0;
        $query = $this->db->query("SELECT TO_CHAR(t1.enrollment_date :: DATE, 'yyyy-mm-dd') status_date,t2.learner_account,t2.acc_stat,t2.logs,t1.* FROM sy$sy.bs_view_enrollment t1
                                    LEFT JOIN (SELECT t1.basic_info_id AS learner_account,t2.logs,t1.is_active AS acc_stat FROM account.tbl_useraccount t1
																	 LEFT JOIN (SELECT t2.basic_info_id,count(1) AS logs FROM sy$sy.g_tbl_userlogs_learner t1
																	 LEFT JOIN account.tbl_useraccount t2 ON t1.user_id=t2.id
																	 GROUP BY t2.basic_info_id) t2 ON t1.basic_info_id=t2.basic_info_id) t2 ON t1.basic_info_id=t2.learner_account
                                   WHERE t1.room_section_id=$rsid AND t1.schl_yr_id=$sy 
                                   ORDER BY t1.sex DESC, t1.last_fullname");

        foreach ($query->result() as $key => $value) {
            $logs =  number_format($value->logs);
            $a_c = $value->acc_stat;
            $searchLog = $value->lrn . ' - ' . $value->last_fullname;
            $birthDate = date_create($value->birthdate);
            $personUuid = $value->person_uuid;
            $birthDate = '<p style="font-size:.8rem;white-space: nowrap;">' . strtoupper(date_format($birthDate, "m-d-Y")) . '</p>';
            $bold = "font-weight-bold";
            $s = substr($value->sex, 0, 1);
            $sex = '<p style="font-size:.7rem;">' . $s . '</p>';
            $txtColor = $value->learner_account && $a_c == 't' ? 'text-success ' . $bold : ($a_c == 'f' ? 'text-danger ' . $bold : 'text-black font-weight-light');
            //LEARNER ID _&&_ LRN _&&_ BASIC INFO ID _&&_ ACCOUNT
            // $val =  $value->learner_id . '_&&_' . $value->lrn  . '_&&_' . $value->basic_info_id . '_&&_' . ($value->learner_account ? 1 : 0) . '_&&_' . md5($value->enrollment_id) . '_&&_' . md5($value->learner_id) . '_&&_' . md5($value->basic_info_id);
            $val =  $value->learner_id . '_&&_' . $value->lrn  . '_&&_' . $value->basic_info_id . '_&&_' . ($value->learner_account ? 1 : 0) . '_&&_' . $value->enrollment_id . '_&&_' . $value->learner_id . '_&&_' . $value->basic_info_id;
            $brgy_city = '<p style="font-size:.8rem;white-space: nowrap;">' . $value->barangay_name  . '</p>';
            $img_path = $this->getImg($value->img_path);

            $od = json_decode($value->other_details, true);
            $data1 = [
                "details" => $value->enrollment_id . '|' . $value->learner_id . '|' . $value->basic_info_id,
                "lrn" => $value->lrn,
                "firstName" => $value->first_name,
                "middleName" => $value->middle_name,
                "lastName" => $value->last_name,
                "extName" => $value->suffix,
                "sex" => $value->sex_bool,
                "birthdate" => $value->birthdate,
                "cty" => $value->citymun_id,
                "brgy" => $value->barangay_id,
                "homeAddress" => $value->address_info,
                "status" => $value->status_id,
                "enrollDate" => $value->status_date,
                "img_path" => $img_path,
                // {
                //     "father": "RAFAEL ABANERO",
                //     "mother": "MIRIAM MAY HIDALGO",
                //     "four_ps": null,
                //     "remarks": "",
                //     "guardian": "",
                //     "relation": "",
                //     "ethnic_group": null,
                //     "learner_uuid": "f3f2f388-1efc-11ee-a03d-08979886cf75",
                //     "religion_txt": "Christianity",
                //     "mother_tongue": null,
                //     "contact_number": "",
                //     "incase_emergency": "MIRIAM MAY HIDALGO",
                //     "mother_tongue_txt": "Cebuano / Sinugbuanong Binisay",
                //     "ip_ethnic_group_txt": "",
                //     "learning_modality_txt": "Modular (print)"
                //   }
                //other_info
                "ffname" => $od["ffn"],
                "fmname" => $od["fmn"],
                "flname" => $od["fln"],
                "mfname" => $od["mfn"],
                "mmname" => $od["mmn"],
                "mlname" => $od["mln"],
                "guardianName" => $od["guardian"],
                "relationship" => $od["g_relation"],
                "contactNumber" => $od["contact_number"],
                "ioe" => $od["incase_emergency"],
                "mfg" => $od["ioe_name"],

                "mother_tongue" => $od["mother_tongue_txt"],
                "ip_ethnic_group" => $od["ip_ethnic_group_txt"],
                "religion" => $od["religion_txt"],
                "four_ps" => $od["four_ps"],
                "learning_modality" => $od["learning_modality_txt"],
                "remarks" => $od["remarks"],
            ];
            $data2 = [
                "lrn" => $value->lrn,
                "last_fullname" => $value->last_fullname,
                "details" => md5($value->enrollment_id) . '|' . md5($value->learner_id) . '|' . md5($value->basic_info_id),
            ];
            $arr1 = json_encode($data1);
            $arr2 = json_encode($data2);
            $c_fmale == 1 && $s == 'F' ?
                $data["data"][] = [
                    " ",
                    "",
                    "",
                    "",
                    "",
                    "",
                    "",
                ] : "";
            $data["data"][] = [
                ($s == 'M' ? $c_male++ : $c_fmale++),
                '<div class="row logs_account" style="white-space: nowrap;display:none;">
                    <div class="col-12">
                        <div class="custom-control custom-checkbox">
                            <input style="cursor:pointer" class="custom-control-input learnerCheckBox" type="checkbox" id="customCheckbox' . $value->lrn . '" name="learnerCheckBox[]" value="' . $val . '">
                            <label style="cursor:pointer" for="customCheckbox' . $value->lrn . '" class="custom-control-label ' . $txtColor . '">' . $value->lrn . '</label>
                        </div>
                    </div>
                    <div class="col-4" hidden>
                        <button type="button" class="btn btn-xs text-gray ml-1" data-toggle="dropdown" aria-expanded="true">' .
                    ($value->logs ? '<span title="' . $value->logs . ' Log(s) Count" class="badge badge-danger button-badge">' . $logs . '</span>' : '<span class="fa fa-ellipsis-h"></span>') . '
                        </button>
                        <div class="dropdown-menu" style="cursor:pointer;">
                        ' . ($value->logs ? "<a class='dropdown-item' role='button' onclick='allStudentLogs(\" $searchLog \")'><i class='fa fa-list'></i> View Logs</a>" : null) . '
                        ' . ($value->learner_account ? "<a class='dropdown-item' onclick='learnerAccnt(\"$personUuid\",\"1\")' role='button'><i class='fa fa-key'></i> Reset Password</a>" : "<a class='dropdown-item' onclick='learnerAccnt(\"$personUuid\",\"0\")'><i class='fa fa-plus'></i> Create Account</a>") . '
                        </div>
                    </div>
                </div>
                

                <div class="normal_view" style="white-space: nowrap;">
                    ' . (($edit == 't'  && $advsry == 1) || ($uri == 'userschooladmin') ? "<span class='fa fa-pencil-alt text-primary text-sm' style='cursor:pointer;' onclick='getDetails(\"UpdateLearnerInfo\",$arr1,1,\"#\");$(\"#modalEnrollment\").modal(\"show\");closeCardBodyIfOpen();delay(\"EnrollmentInfo\",$value->barangay_id,\"brgy\")'></span> " : '') . $value->lrn . '
                </div>',
                '<p style="white-space: nowrap;">' . $this->readQuote($value->last_fullname) . '</p>',
                $sex,
                $birthDate,
                $brgy_city,
                '<div class="normal_view" style="white-space: nowrap;">
                    ' . $value->enrollment_status . (($unenroll == 't' && $advsry == 1) || ($uri == 'userschooladmin') ?''
                    // " <span class='fa fa-trash-alt text-danger text-sm' style='cursor:pointer' onclick='getDetails(\"UnenrollConfirm\",$arr2,1,\"#\");setTimeout(function(){ $(\".passwordUnenroll\").val(\"\").focus(); } ,200);$(\"#modalLearnersUnenroll\").modal(\"show\");'>
                    // </span>"
                    // '<button type="button" class="btn btn-xs text-sm float-right btn-outline-secondary rounded-circle border-0 ml-1" data-toggle="dropdown" aria-expanded="true">
                    //     <span class="fa fa-ellipsis-h"></span>
                    // </button>
                    // <div class="dropdown-menu p-0" role="menu">' .
                    // "<a class='dropdown-item text-xs bg-info' role='button' onclick='getDetails(\"UnenrollConfirm\",$arr2,1,\"#\");'><span class='fa fa-address-card'></span> PREVIEW ID</a>" .
                    // "<a class='dropdown-item text-xs bg-danger' role='button' onclick='getDetails(\"UnenrollConfirm\",$arr2,1,\"#\");setTimeout(function(){ $(\".passwordUnenroll\").val(\"\").focus(); } ,200);$(\"#modalLearnersUnenroll\").modal(\"show\");'><span class='fa fa-xmark'></span> UNENROLL</a>" .
                    // "<a class='dropdown-item text-xs bg-warning' role='button' onclick='getDetails(\"UnenrollConfirm\",$arr2,1,\"#\");'><span class='fa fa-right-left'></span> TRANSFER</a>" .
                    // "<a class='dropdown-item text-xs' role='button' onclick='getDetails(\"UnenrollConfirm\",$arr2,1,\"#\");setTimeout(function(){ $(\".passwordUnenroll\").val(\"\").focus(); } ,200);$(\"#modalLearnersSF1\").modal(\"show\");'><span class='fa fa-folder-open'></span> FORM 1</a>" .
                    // "<a class='dropdown-item text-xs bg-info' role='button' onclick='alert()'><span class='fa fa-list'></span> GENERATE</a>" .
                    // "<a class='dropdown-item text-xs bg-info' role='button' onclick='getDetails(\"TransferConfirm\",$arr2,1,\"#\");setTimeout(function(){ $(\".passwordTransfer\").val(\"\").focus(); } ,200);$(\"#modalLearnersTransfer\").modal(\"show\");'><span class='fa fa-arrow-right'></span> TRANSFER</a>
                    // "</div>"
                    : '') . '
                </div>',
            ];
        }
        echo json_encode($data);
    }


    function getLearnersListSearch()
    {
        $data = ["data" => []];
        $c_male = 1;
        $c_fmale = 1;
        $sy = $this->getOnLoad()["sy_id"];

        // $q = $this->db->query("SELECT 1 AS cc FROM building_sectioning.view_subject_grdlvl_personnel_assgnmnt t1
        //                         WHERE t1.schl_yr_id=$sy and t1.schoolpersonnel_id=$personnel_id LIMIT 1");

        // $q1 = $q->row();
        // $advsry = $q1->cc ?? 0;

        $requestData = $_REQUEST;
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Query to get total record count
        $thisQuery = $this->db->query("SELECT COUNT(1) AS total FROM sy$sy.bs_view_enrollment t1 WHERE  CONCAT(t1.*) ILIKE '%$searchValue%'");
        $totalRecords = $thisQuery->row()->total;

        $data = array();
        $cc = $offset + 1;
        
        $query = $this->db->query("SELECT TO_CHAR(t1.enrollment_date :: DATE, 'yyyy-mm-dd') status_date,t2.learner_account,t2.acc_stat,t2.logs,t1.* FROM sy$sy.bs_view_enrollment t1
                                    LEFT JOIN (SELECT t1.basic_info_id AS learner_account,t2.logs,t1.is_active AS acc_stat FROM account.tbl_useraccount t1
																	 LEFT JOIN (SELECT t2.basic_info_id,count(1) AS logs FROM sy$sy.g_tbl_userlogs_learner t1
																	 LEFT JOIN account.tbl_useraccount t2 ON t1.user_id=t2.id
																	 GROUP BY t2.basic_info_id) t2 ON t1.basic_info_id=t2.basic_info_id) t2 ON t1.basic_info_id=t2.learner_account
                                   WHERE t1.schl_yr_id=$sy AND CONCAT(t1.*) ILIKE '%$searchValue%'
                                   ORDER BY t1.last_fullname
                                   LIMIT $limit OFFSET $offset");

        foreach ($query->result() as $key => $value) {
            $a_c = $value->acc_stat;
            $birthDate = date_create($value->birthdate);
            $birthDate = '<p style="font-size:.8rem;white-space: nowrap;">' . strtoupper(date_format($birthDate, "m-d-Y")) . '</p>';
            $bold = "font-weight-bold";
            $s = substr($value->sex, 0, 1);
            $brgy_city = '<p style="font-size:.8rem;white-space: nowrap;">' . $value->barangay_name  . '</p>';
            $img_path = $this->getImg($value->img_path);

            $od = json_decode($value->other_details, true);
           
            $data[] = [
                $cc++,
                $value->lrn,
                '<p style="white-space: nowrap;">' . $this->readQuote($value->last_fullname) . '</p>',
                '<p style="white-space: nowrap;">'. $value->grade.' - '.$value->sctn_nm . '</p>',
                $s,
                $birthDate,
                $brgy_city,
                $value->enrollment_status,
            ];
        }

        $response = array(
            'draw' => intval($requestData['draw']),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords), // For simplicity, assuming no filtering is applied
            'data' => $data,
        );

        echo json_encode($response);
    }

    function getGradesSMEAList()
    {
        $data = ["data" => []];
        $c_male = 1;
        $c_fmale = 1;

        $personnel_id = $this->session->schoolmis_login_prsnnl_Id;
        $rssaid = $this->input->post("rssaid");


        $sy = $this->getOnLoad()["sy_id"];
        $syt = $this->getOnLoad()["sy"];
        $qrtr = $this->getOnLoad()["qrtr"];
        $rsid = $this->input->post("rsid");
        $query1 = $this->db->query("SELECT t1.* FROM sy$sy.bs_view_enrollment t1
                                   WHERE t1.room_section_id=$rsid AND t1.schl_yr_id=$sy 
                                   ORDER BY t1.sex DESC, t1.last_fullname");



        $query = $this->db->query("SELECT t4.q1,t4.q2,t4.q3,t4.q4, t5.q1 AS psq1,t5.q2 AS psq2,t5.q3 AS psq3,t5.q4 AS psq4, t1.id,t3.last_fullname,t3.lrn,t3.sex,t3.birthdate,t3.lrn,t3.enrollment_id,t3.room_section_id FROM building_sectioning.tbl_room_section_subject_assignment t1
                                    LEFT JOIN building_sectioning.tbl_room_section t2 ON t1.room_section_id=t2.id
                                    LEFT JOIN sy$sy.bs_view_enrollment t3 ON t1.room_section_id=t3.room_section_id
                                    LEFT JOIN (SELECT t1.* FROM sy$sy.bs_m_view_grades t1)
                                        t4 ON t3.enrollment_id=t4.learner_enrollment_id AND t1.id=t4.rm_sctn_sbjct_assgnmnt_id
                                    LEFT JOIN (SELECT t1.* FROM sy$sy.bs_m_view_grades_ps t1)
                                        t5 ON t3.enrollment_id=t5.learner_enrollment_id AND t1.id=t4.rm_sctn_sbjct_assgnmnt_id
                                    WHERE t1.schl_personnel_id=$personnel_id AND t1.id=$rssaid
                                    -- WHERE t1.room_section_id=$rsid AND t1.schl_yr_id=$sy
                                    ORDER BY t3.sex DESC, t3.last_fullname");


        foreach ($query->result() as $key => $value) {
            // $birthDate = date_create($value->birthdate);
            // $birthDate = strtoupper(date_format($birthDate, "m-d-Y"));
            $sex = substr($value->sex, 0, 1);

            $c_fmale == 1 && $sex == 'F' ?
                $data["data"][] = [
                    " ",
                    "",
                    "",
                    "",

                    "",
                    "",
                    "",
                    "",

                    "",

                    "",
                    "",
                    "",
                    "",

                ] : "";
            $data["data"][] = [
                ($sex == 'M' ? $c_male++ : $c_fmale++),
                "`" . $value->lrn . "`",
                $value->last_fullname,
                $value->q1,
                $value->q2,
                $value->q3,
                $value->q4,

                "|",

                $value->psq1,
                $value->psq2,
                $value->psq3,
                $value->psq4
            ];
        }
        echo json_encode($data);
    }

    function getSearchEnrollLearnersList()
    {
        $data = ["data" => []];
        $by = $this->input->post("by");
        $key = $this->input->post("key");
        $w = $by == 1 ? "t1.lrn='$key'" : "t1.last_name='$key'";
        $c = 1;
        $personnel_id = $this->session->schoolmis_login_prsnnl_Id;
        $sy = $this->getOnLoad()["sy_id"];
        $rsid = $this->input->post("rsid");
        $query = $this->db->query("SELECT t1.lrn,t1.learner_id,t1.last_fullname,t1.sex,t1.birthdate,
                                    t2.grade,t2.sctn_nm,t2.enrollment_date, t2.sy FROM profile.view_learner t1
                                    LEFT JOIN sy$sy.bs_view_enrollment t2 ON t1.learner_id=t2.learner_id
                                    -- WHERE $w
                                    ORDER BY t2.enrollment_date DESC
                                    LIMIT 10");

        foreach ($query->result() as $key => $value) {
            $birthDate = date_create($value->birthdate);
            $birthDate = strtoupper(date_format($birthDate, "m-d-Y"));
            $sex = substr($value->sex, 0, 1);
            $data["data"][] = [
                $c++,
                $value->grade === null ? "<div class='custom-control custom-checkbox'>
                    <input class='custom-control-input' type='checkbox' id='customCheckbox" . $value->lrn . "' name='searchEnrollCheckBox[]' value='" . $value->learner_id . "'>
                    <label for='customCheckbox" . $value->lrn . "' class='custom-control-label'>" . $value->lrn . "</label>
                </div>" : "<div class='custom-control custom-checkbox'><span class='custom-control-input'>ENRL</span> <label>" . $value->lrn . "</label></div>",
                $value->last_fullname,
                $sex,
                $birthDate,
                $value->grade . '-' . $value->sctn_nm . '<br/>' . $value->enrollment_date,
            ];
        }
        echo json_encode($data);
    }

    function getGradesList()
    {
        $data = ["data" => []];
        $c_male = 1;
        $c_fmale = 1;
        $personnel_id = $this->session->schoolmis_login_prsnnl_Id;
        $sy = $this->getOnLoad()["sy_id"];
        $qrtr = $this->getOnLoad()["qrtr"];
        $igq = (string)$this->getOnLoad()["input_grades_qrtr"];
        $rssaid = $this->input->post("rssaid");

        $q1c = null;
        $q1stat = null;
        $q1rmrk = null;
        $q2c = null;
        $q2stat = null;
        $q2rmrk = null;
        $q3c = null;
        $q3stat = null;
        $q3rmrk = null;
        $q4c = null;
        $q4stat = null;
        $q4rmrk = null;

        $igq1 = "";
        $igq2 = "";
        $igq3 = "";
        $igq4 = "";
        for ($x = 0; $x < strlen($igq); $x++) {
            $qi = $igq[$x];
            if ($qi == 1) {
                $igq1 = 1;
            }
            if ($qi == 2) {
                $igq2 = 2;
            }
            if ($qi == 3) {
                $igq3 = 3;
            }
            if ($qi == 4) {
                $igq4 = 4;
            }
        }

        $query = $this->db->query("SELECT t4.q1,t4.q2,t4.q3,t4.q4, t1.id,t3.last_fullname,t3.sex,t3.birthdate,t3.lrn,t3.enrollment_id,t3.room_section_id FROM building_sectioning.tbl_room_section_subject_assignment t1
                                    LEFT JOIN building_sectioning.tbl_room_section t2 ON t1.room_section_id=t2.id
                                    LEFT JOIN sy$sy.bs_view_enrollment t3 ON t1.room_section_id=t3.room_section_id
                                    LEFT JOIN (SELECT t1.* FROM sy$sy.bs_m_view_grades t1 WHERE t1.rm_sctn_sbjct_assgnmnt_id=$rssaid)
                                        t4 ON t3.enrollment_id=t4.learner_enrollment_id AND t1.id=t4.rm_sctn_sbjct_assgnmnt_id
                                    WHERE t1.schl_personnel_id=$personnel_id AND t1.id=$rssaid
                                    ORDER BY t3.sex DESC, t3.last_fullname");

        $query1 = $this->db->query("SELECT t1.* FROM sy$sy.bs_m_view_all_grades_stat t1 
                                    WHERE t1.rssaid = $rssaid");

        foreach ($query1->result() as $key => $v) {
            $q1c = $v->q1c;
            $q1stat = $v->q1stat;
            $q1rmrk = $v->q1rmrk;
            $q2c = $v->q2c;
            $q2stat = $v->q2stat;
            $q2rmrk = $v->q2rmrk;
            $q3c = $v->q3c;
            $q3stat = $v->q3stat;
            $q3rmrk = $v->q3rmrk;
            $q4c = $v->q4c;
            $q4stat = $v->q4stat;
            $q4rmrk = $v->q4rmrk;
            $data["details"] = [
                "q1c" => $this->submitGradesBtn($q1stat, $q1c, $q1rmrk, 3221232, 1),
                "q2c" => $this->submitGradesBtn($q2stat, $q2c, $q2rmrk, 2123221, 2),
                "q3c" => $this->submitGradesBtn($q3stat, $q3c, $q3rmrk, 3211123, 3),
                "q4c" => $this->submitGradesBtn($q4stat, $q4c, $q4rmrk, 4522323, 4),
            ];
        }

        foreach ($query->result() as $key => $value) {
            $birthDate = date_create($value->birthdate);
            $birthDate = strtoupper(date_format($birthDate, "m-d-Y"));
            $sex = substr($value->sex, 0, 1);
            $q1 = $value->q1;
            $q2 = $value->q2;
            $q3 = $value->q3;
            $q4 = $value->q4;
            $avg = round(($q1 + $q2 + $q3 + $q4) / 4, 0);
            $v = $qrtr == 1 ? $q1 : ($qrtr == 12 ? $q2 : ($qrtr == 3 ? $q3 : $q4));
            $entry1 = $igq1 != "" ? $this->grades_input($value->lrn, $q1, 1) : "";
            $entry2 = $igq2 != "" ? $this->grades_input($value->lrn, $q2, 2) : "";
            $entry3 = $igq3 != "" ? $this->grades_input($value->lrn, $q3, 3) : "";
            $entry4 = $igq4 != "" ? $this->grades_input($value->lrn, $q4, 4) : "";

            $c_fmale == 1 && $sex == 'F' ?
                $data["data"][] = [
                    " ",
                    "",
                    "",
                    "",
                    "",
                    "",
                ] : "";

            $data["data"][] = [
                "<p style='text-align:left;white-space: nowrap;' class='mb-0'>" . ($sex == 'M' ? $c_male++ : $c_fmale++) . ". " . $value->last_fullname . "</p>",
                "<input value='" . $value->enrollment_id . "' name='en_id[]' hidden/>
                <input value='" . $value->room_section_id . "' name='rm_sec_id[]' hidden/>
                <input value='" . $rssaid . "' name='rssaid[]' hidden/>" .
                    ($igq1 != "" && ($q1stat == null || $q1stat == "RECHECK") ? $entry1 : $this->gradeColor($q1)),
                ($igq2 != "" && ($q2stat == null || $q2stat == "RECHECK") ? $entry2 : $this->gradeColor($q2)),
                ($igq3 != "" && ($q3stat == null || $q3stat == "RECHECK") ? $entry3 : $this->gradeColor($q3)),
                ($igq4 != "" && ($q4stat == null || $q4stat == "RECHECK") ? $entry4 : $this->gradeColor($q4)),
                $this->gradeColor($avg),
            ];
        }
        echo json_encode($data);
    }

    function getGradesPSList()
    {
        $data = ["data" => []];
        $c_male = 1;
        $c_fmale = 1;
        $personnel_id = $this->session->schoolmis_login_prsnnl_Id;
        $sy = $this->getOnLoad()["sy_id"];
        $qrtr = $this->getOnLoad()["qrtr"];
        $igq = (string)$this->getOnLoad()["input_grades_qrtr"];
        $rssaid = $this->input->post("rssaid");

        $igq1 = "";
        $igq2 = "";
        $igq3 = "";
        $igq4 = "";
        for ($x = 0; $x < strlen($igq); $x++) {
            $qi = $igq[$x];
            if ($qi == 1) {
                $igq1 = 1;
            }
            if ($qi == 2) {
                $igq2 = 2;
            }
            if ($qi == 3) {
                $igq3 = 3;
            }
            if ($qi == 4) {
                $igq4 = 4;
            }
        }

        $query = $this->db->query("SELECT t4.q1,t4.q2,t4.q3,t4.q4, t1.id,t3.last_fullname,t3.sex,t3.birthdate,t3.lrn,t3.enrollment_id,t3.room_section_id FROM building_sectioning.tbl_room_section_subject_assignment t1
                                    LEFT JOIN building_sectioning.tbl_room_section t2 ON t1.room_section_id=t2.id
                                    LEFT JOIN sy$sy.bs_view_enrollment t3 ON t1.room_section_id=t3.room_section_id
                                    LEFT JOIN (SELECT t1.* FROM sy$sy.bs_m_view_grades_ps t1 WHERE t1.rm_sctn_sbjct_assgnmnt_id=$rssaid)
                                        t4 ON t3.enrollment_id=t4.learner_enrollment_id AND t1.id=t4.rm_sctn_sbjct_assgnmnt_id
                                    WHERE t1.schl_personnel_id=$personnel_id AND t1.id=$rssaid
                                    ORDER BY t3.sex DESC, t3.last_fullname");


        foreach ($query->result() as $key => $value) {
            $sex = substr($value->sex, 0, 1);
            $q1 = $value->q1;
            $q2 = $value->q2;
            $q3 = $value->q3;
            $q4 = $value->q4;
            $avg = round(($q1 + $q2 + $q3 + $q4) / 4, 0);
            $v = $qrtr == 1 ? $q1 : ($qrtr == 12 ? $q2 : ($qrtr == 3 ? $q3 : $q4));
            $entry1 = $igq1 != "" ? $this->grades_input($value->lrn, $q1, 1) : "";
            $entry2 = $igq2 != "" ? $this->grades_input($value->lrn, $q2, 2) : "";
            $entry3 = $igq3 != "" ? $this->grades_input($value->lrn, $q3, 3) : "";
            $entry4 = $igq4 != "" ? $this->grades_input($value->lrn, $q4, 4) : "";
            // $entryAvg = $this->grades_input($value->lrn, $avg, 5);
            $c_fmale == 1 && $sex == 'F' ?
                $data["data"][] = [
                    " ",
                    "",
                    "",
                    "",
                    "",
                    "",
                ] : "";

            $data["data"][] = [
                "<p style='text-align:left;white-space: nowrap;' class='mb-0'>" . ($sex == 'M' ? $c_male++ : $c_fmale++) . ". " . $value->last_fullname . "</p>",
                "<input value='" . $value->enrollment_id . "' name='en_id[]' hidden/>
                <input value='" . $value->room_section_id . "' name='rm_sec_id[]' hidden/>
                <input value='" . $rssaid . "' name='rssaid[]' hidden/>" .
                    $entry1,
                $entry2,
                $entry3,
                $entry4,
                $this->gradeColor($avg)
                // $entryAvg
                // "<label class='avg$value->lrn'>" . $this->gradeColor($avg) . "</label>",
            ];
        }
        echo json_encode($data);
    }

    function getAllStudentLogs()
    {
        $data = ["data" => []];
        $c = 1;
        $rsid = $this->input->post("rsid");
        $sy = $this->getOnLoad()["sy_id"];
        $query = $this->db->query("SELECT t1.*,t2.lrn,t2.last_fullname FROM sy$sy.g_tbl_userlogs_learner t1 
                                    JOIN sy$sy.bs_view_enrollment t2 ON t1.user_name=t2.lrn 
                                    WHERE t2.rm_sec_id=$rsid
                                    ORDER BY t1.date_time DESC");
        foreach ($query->result() as $key => $value) {
            $data["data"][] = [
                '<b>' . $c++ . '.</b>',
                '<div style="white-space: nowrap;">' . $value->lrn . ' - ' . $value->last_fullname . '</div>',
                '<div style="white-space: nowrap;">' . $value->date_time . '</div>',
                '<div style="white-space: nowrap;">' . $value->action . '</div>',
                '<div style="white-space: nowrap;">' . $value->ip . '</div>',
            ];
        }
        echo json_encode($data);
    }

    function getHonors()
    {
        $data = [
            "data" => [],
            "q1a_wst" => [], "q1a_whh" => [], "q1a_wh" => [],
        ];
        //q1
        $data_q1a_wst = [];
        $data_q1a_whh = [];
        $data_q1a_wh = [];
        //q2
        $data_q2a_wst = [];
        $data_q2a_whh = [];
        $data_q2a_wh = [];
        //q3
        $data_q3a_wst = [];
        $data_q3a_whh = [];
        $data_q3a_wh = [];
        //q4
        $data_q4a_wst = [];
        $data_q4a_whh = [];
        $data_q4a_wh = [];
        //q4
        $data_fga_wst = [];
        $data_fga_whh = [];
        $data_fga_wh = [];

        $c = 1;
        $rsid = $this->input->post("rsid");
        $sy = $this->getOnLoad()["sy_id"];
        $q1a_wst = 0;
        $q1a_whh = 0;
        $q1a_wh = 0;

        $q2a_wst = 0;
        $q2a_whh = 0;
        $q2a_wh = 0;

        $q3a_wst = 0;
        $q3a_whh = 0;
        $q3a_wh = 0;

        $q4a_wst = 0;
        $q4a_whh = 0;
        $q4a_wh = 0;

        $fga_wst = 0;
        $fga_whh = 0;
        $fga_wh = 0;
        $query = $this->db->query("SELECT t2.room_section_id ,t1.*,t3.total_sbjct,t4.last_fullname FROM(
                                    SELECT t1.learner_enrollment_id,
                                        SUM(t1.q1) AS q1,
                                        SUM(t1.q2) AS q2,
                                        SUM(t1.q3) AS q3,
                                        SUM(t1.q4) AS q4
                                    FROM (SELECT * FROM sy$sy.bs_tbl_learner_grades t1
                                            JOIN (SELECT t1.id,t1.room_section_id,t2.parent_party_id,t2.description,
                                                                        t3.schl_yr_id
                                                                        FROM ((building_sectioning.tbl_room_section_subject_assignment t1
                                                                            LEFT JOIN global.tbl_party t2 ON ((t1.subject_id = t2.id)))
                                                                            LEFT JOIN building_sectioning.tbl_room_section t3 ON ((t1.room_section_id = t3.id)))
                                                                    WHERE (t2.parent_party_id IS NULL) AND t1.room_section_id = $rsid) t2 ON t1.rm_sctn_sbjct_assgnmnt_id=t2.id) t1
                                    GROUP BY t1.learner_enrollment_id) t1
                                    JOIN sy$sy.bs_tbl_learner_enrollment t2 ON t1.learner_enrollment_id=t2.id
                                    JOIN(SELECT t1.* FROM building_sectioning.view_room_section_sbjct_cnt t1 WHERE t1.schl_yr_id = $sy) t3 ON t2.room_section_id=t3.room_section_id
                                    JOIN sy$sy.bs_view_enrollment t4 ON t1.learner_enrollment_id=t4.enrollment_id
                                    WHERE t2.room_section_id = $rsid");
        foreach ($query->result() as $key => $value) {
            $ts = $value->total_sbjct;
            $q1 = round($value->q1 / $ts);
            $q2 = round($value->q2 / $ts);
            $q3 = round($value->q3 / $ts);
            $q4 = round($value->q4 / $ts);
            $fg = round(($q1 + $q2 + $q3 + $q4) / 4);

            //q1
            if ($q1 >= 98 &&  $q1 <= 100) {
                $q1a_wst += 1;
                $data_q1a_wst[] = [
                    "l" => $value->last_fullname,
                    "g" => $q1,
                ];
            } else if ($q1 >= 95 &&  $q1 <= 97) {
                $q1a_whh += 1;
                $data_q1a_whh[] = [
                    "l" => $value->last_fullname,
                    "g" => $q1,
                ];
            } else if ($q1 >= 90 &&  $q1 <= 94) {
                $q1a_wh += 1;
                $data_q1a_wh[] = [
                    "l" => $value->last_fullname,
                    "g" => $q1,
                ];
            }

            //q2
            if ($q2 >= 98 &&  $q2 <= 100) {
                $q2a_wst += 1;
                $data_q2a_wst[] = [
                    "l" => $value->last_fullname,
                    "g" => $q2,
                ];
            } else if ($q2 >= 95 &&  $q2 <= 97) {
                $q2a_whh += 1;
                $data_q2a_whh[] = [
                    "l" => $value->last_fullname,
                    "g" => $q2,
                ];
            } else if ($q2 >= 90 &&  $q2 <= 94) {
                $q2a_wh += 1;
                $data_q2a_wh[] = [
                    "l" => $value->last_fullname,
                    "g" => $q2,
                ];
            }

            //q3
            if ($q3 >= 98 &&  $q3 <= 100) {
                $q3a_wst += 1;
                $data_q3a_wst[] = [
                    "l" => $value->last_fullname,
                    "g" => $q3,
                ];
            } else if ($q3 >= 95 &&  $q3 <= 97) {
                $q3a_whh += 1;
                $data_q3a_whh[] = [
                    "l" => $value->last_fullname,
                    "g" => $q3,
                ];
            } else if ($q3 >= 90 &&  $q3 <= 94) {
                $q3a_wh += 1;
                $data_q3a_wh[] = [
                    "l" => $value->last_fullname,
                    "g" => $q3,
                ];
            }

            //q4
            if ($q4 >= 98 &&  $q4 <= 100) {
                $q4a_wst += 1;
                $data_q4a_wst[] = [
                    "l" => $value->last_fullname,
                    "g" => $q4,
                ];
            } else if ($q4 >= 95 &&  $q4 <= 97) {
                $q4a_whh += 1;
                $data_q4a_whh[] = [
                    "l" => $value->last_fullname,
                    "g" => $q4,
                ];
            } else if ($q4 >= 90 &&  $q4 <= 94) {
                $q4a_wh += 1;
                $data_q4a_wh[] = [
                    "l" => $value->last_fullname,
                    "g" => $q4,
                ];
            }

            //fg
            if ($fg >= 98 &&  $fg <= 100) {
                $fga_wst += 1;
                $data_fga_wst[] = [
                    "l" => $value->last_fullname,
                    "g" => $fg,
                ];
            } else if ($fg >= 95 &&  $fg <= 97) {
                $fga_whh += 1;
                $data_fga_whh[] = [
                    "l" => $value->last_fullname,
                    "g" => $fg,
                ];
            } else if ($fg >= 90 &&  $fg <= 94) {
                $fga_wh += 1;
                $data_fga_wh[] = [
                    "l" => $value->last_fullname,
                    "g" => $fg,
                ];
            }
        }

        //q1
        $q1_wst_btn = $this->returnBtnHonor($data_q1a_wst, $q1a_wst);
        $q1_whh_btn = $this->returnBtnHonor($data_q1a_whh, $q1a_whh);
        $q1_wh_btn = $this->returnBtnHonor($data_q1a_wh, $q1a_wh);
        //q2
        $q2_wst_btn = $this->returnBtnHonor($data_q2a_wst, $q2a_wst);
        $q2_whh_btn = $this->returnBtnHonor($data_q2a_whh, $q2a_whh);
        $q2_wh_btn = $this->returnBtnHonor($data_q2a_wh, $q2a_wh);
        //q3
        $q3_wst_btn = $this->returnBtnHonor($data_q3a_wst, $q3a_wst);
        $q3_whh_btn = $this->returnBtnHonor($data_q3a_whh, $q3a_whh);
        $q3_wh_btn = $this->returnBtnHonor($data_q3a_wh, $q3a_wh);
        //q4
        $q4_wst_btn = $this->returnBtnHonor($data_q4a_wst, $q4a_wst);
        $q4_whh_btn = $this->returnBtnHonor($data_q4a_whh, $q4a_whh);
        $q4_wh_btn = $this->returnBtnHonor($data_q4a_wh, $q4a_wh);
        //q4
        $fg_wst_btn = $this->returnBtnHonor($data_fga_wst, $fga_wst);
        $fg_whh_btn = $this->returnBtnHonor($data_fga_whh, $fga_whh);
        $fg_wh_btn = $this->returnBtnHonor($data_fga_wh, $fga_wh);

        $query2 = $this->db->query("SELECT * FROM global.tbl_party WHERE party_type_id=19 AND is_active=true ORDER BY order_by");
        foreach ($query2->result() as $key => $value) {
            $pid = $value->id;
            //q1
            $q1a_wst = $q1_wst_btn;
            $q1a_whh = $q1_whh_btn;
            $q1a_wh = $q1_wh_btn;
            //q2
            $q2a_wst = $q2_wst_btn;
            $q2a_whh = $q2_whh_btn;
            $q2a_wh = $q2_wh_btn;
            //q3
            $q3a_wst = $q3_wst_btn;
            $q3a_whh = $q3_whh_btn;
            $q3a_wh = $q3_wh_btn;
            //q4
            $q4a_wst = $q4_wst_btn;
            $q4a_whh = $q4_whh_btn;
            $q4a_wh = $q4_wh_btn;
            //fg
            $fga_wst = $fg_wst_btn;
            $fga_whh = $fg_whh_btn;
            $fga_wh = $fg_wh_btn;

            $data["data"][] = [
                '<p class="mb-n2 text-nowrap">' . $value->description . ' <i>' . $value->abbr . '</i></p>',
                $pid == 109 ? $q1a_wst : ($pid == 110 ? $q1a_whh : $q1a_wh),
                $pid == 109 ? $q2a_wst : ($pid == 110 ? $q2a_whh : $q2a_wh),
                $pid == 109 ? $q3a_wst : ($pid == 110 ? $q3a_whh : $q3a_wh),
                $pid == 109 ? $q4a_wst : ($pid == 110 ? $q4a_whh : $q4a_wh),
                $pid == 109 ? $fga_wst : ($pid == 110 ? $fga_whh : $fga_wh),
                '4',
            ];
        }
        echo json_encode($data);
    }
    // function getSYInfo()
    // {
    //     $data = ["data" => []];
    //     $c = 1;
    //     $thisQuery = $this->db->query("SELECT t1.* FROM global.tbl_sy t1 ORDER BY t1.from DESC");
    //     foreach ($thisQuery->result() as $key => $value) {
    //         $stat = $value->is_active;
    //         $data["data"][] = [
    //             $c++,
    //             "<div class='row'><div class='col-6'>
    //                 <span class='badge text-md " . ($stat == "t" ? "bg-success" : "bg-gray") . "'>$value->description</span>
    //             </div>
    //             <div class='col-6'>
    //                 <button type='button' class='btn btn-xs text-sm float-right text-gray' data-toggle='dropdown' aria-expanded='true'>
    //                     <span class='fa fa-ellipsis-h'></span>
    //                 </button>
    //                 <div class='dropdown-menu'>
    //                     <a class='dropdown-item' role='button' onclick='getDetails(\"MemberUser\",1)'>Edit Information</a>
    //                 </div>
    //             </div></div>",
    //         ];
    //     }
    //     echo json_encode($data);
    // }
}
