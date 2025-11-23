<?php //if ( ! defined('BASEPATH')) exit('No direct script access allowed');
defined('BASEPATH') or exit('No direct script access allowed');

class Getdata extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->redirect();
        $this->load->model('mainModel');
        $this->load->helper('date');
        date_default_timezone_set("Asia/Manila");
    }


    function getGradeSecInfo()
    {
        $sy = $this->getOnLoad()["sy_id"];
        $dept_id = $this->session->schoolmis_login_dept_id;
        $data = ["data" => []];
        $data2 = [];
        $c = 1;
        $thisQuery = $this->db->query('SELECT t2.sec_handle, t1.schoolpersonnel_id,t1.person_id,t1.last_fullname, t1.personal_title, t1.school_department_id  FROM profile.view_schoolpersonnel t1
                                        LEFT JOIN (SELECT count(1) AS sec_handle,t1.schoolpersonnel_id
                                                    FROM building_sectioning.view_subject_grdlvl_personnel_assgnmnt t1
                                                    WHERE t1.schl_yr_id=' . $sy . '
                                                    GROUP BY t1.schoolpersonnel_id) t2 ON t1.schoolpersonnel_id=t2.schoolpersonnel_id 
                                        WHERE t1.school_department_id=' . $dept_id . ' AND t1.school_id=1 AND t1."employeeTypeId"=4
                                        ORDER BY t1.last_fullname');
        foreach ($thisQuery->result() as $key => $value) {
            $sec_handle = $value->sec_handle;
            $spid = $value->schoolpersonnel_id;
            $pid = $value->person_id;
            $name = $value->last_fullname;
            $title = $value->personal_title;
            $data2 = [
                "spid" => $spid,
            ];
            $arr = json_encode($data2);

            $data["data"][] = [
                $c++,
                "<div class='row'><div class='col-11'>
                        <span class='badge text-sm pb-0'>$name </span>
                        <small>$title<i></i></small><br/>
                        <small class='ml-2'>LOAD: <b>" . ($sec_handle ?? "<i class='text-red'>NONE</i>") . "</b></small>
                    </div>
                    <div class='col-1'>
                        <button type='button' onclick='customTabViewAllGrades(\"$spid\");' class='btn btn-xs text-xs float-right btn-outline-secondary rounded-circle border-0 btneye btneye$spid'>
                            <span class='fa fa-eye'></span>
                        </button>
                        <!-- <button type='button' class='btn btn-xs text-sm float-right btn-outline-secondary rounded-circle border-0' data-toggle='dropdown' aria-expanded='true'>
                            <span class='fa fa-ellipsis-h'></span>
                        </button>
                        <div class='dropdown-menu'>
                            <a class='dropdown-item' href='#' onclick='getDetails(\"MemberUser\",1)'>Edit Information</a>
                        </div> --!>
                    </div>
                </div>
                <script>
                    $('.btneye$spid').click(function(){ $('.btneye').removeClass('bg-yellow'); $(this).addClass('bg-yellow'); });
                </script>

                ",
            ];
        }
        echo json_encode($data);
    }


    function getViewAllGrades()
    {
        $sid = $this->input->get("a");
        $tab = null;
        $content = null;
        $sy = $this->getOnLoad()["sy_id"];
        $qrtr = $this->getOnLoad()["qrtr"];
        $query = $this->db->query("SELECT t3.*,t1.*,t2.full_name AS fn_advsry,t2.personal_title AS fn_advsry_ttl,t2.img_path AS profile_img FROM building_sectioning.view_subject_grdlvl_personnel_assgnmnt t1
                                   LEFT JOIN (SELECT * FROM building_sectioning.view_subject_grdlvl_personnel_assgnmnt
												WHERE advisory=true
                                   ) t2 ON t1.room_section_id=t2.room_section_id
                                   LEFT JOIN sy$sy.bs_m_view_all_grades_stat t3 ON t1.rm_sctn_sbjct_assgnmnt_id=t3.rssaid
                                   WHERE t1.schoolpersonnel_id=$sid AND t1.schl_yr_id=$sy
                                   ORDER BY t1.sctn_nm ,t1.order_by_sbjct");
        foreach ($query->result() as $key => $value) {
            $q1c = $value->q1c;
            $q1stat = $value->q1stat;
            $q1rmrk = $value->q1rmrk;
            $q2c = $value->q2c;
            $q2stat = $value->q2stat;
            $q2rmrk = $value->q2rmrk;
            $q3c = $value->q3c;
            $q3stat = $value->q3stat;
            $q3rmrk = $value->q3rmrk;
            $q4c = $value->q4c;
            $q4stat = $value->q4stat;
            $q4rmrk = $value->q4rmrk;

            $spid = $value->schoolpersonnel_id;
            $rsid = $value->room_section_id;
            $rssa_id = $value->rm_sctn_sbjct_assgnmnt_id;
            $img = $value->profile_img;
            $fn_advsry = $value->fn_advsry;
            $fn_advsry_ttl = $value->fn_advsry_ttl;
            $sbjctabbr = $value->subject_abbr;
            $sbjct = $value->subject;
            $sbjctid = $value->subject_id;
            // $teacher = $value->full_name;
            // $title = $value->personal_title;
            $advisory = $value->advisory == 't' ? 'Class Advisory ' : '';
            $schedule = $value->schedule;
            $grd_sctn = $value->grade . ' - ' . $value->sctn_nm;
            $active = "";

            $a = null;
            $b = null;
            $c = null;
            if ($key === array_key_first($query->result())) {
                $active = "active";
            }
            $tab .= '<li class="nav-item">
                        <a onclick="rssaid=' . $rssa_id . ';" class="nav-link ' . $active . '" title="' . $sbjct . '" id="custom-tabs-' . $rsid . $sbjctid . '-tab" data-toggle="pill" href="#custom-tabs-' . $rsid . $sbjctid . '" role="tab" aria-controls="custom-tabs-' . $rsid . $sbjctid . '" aria-selected="false">' . $grd_sctn . '-' . $sbjctabbr . '</a>
                    </li>
                    <script>
                        if(rssaid_tmp == 0){
                            rssaid = ' . $rssa_id . ';
                            console.log(rssaid);
                            rssaid_tmp = 1;
                        }
                    </script>';

            $query2 = $this->db->query("SELECT t4.q1,t4.q2,t4.q3,t4.q4,t5.advisory,t5.full_name AS teacher_full_name,t5.personal_title,t1.id,t3.* FROM building_sectioning.tbl_room_section_subject_assignment t1
                                        LEFT JOIN building_sectioning.tbl_room_section t2 ON t1.room_section_id=t2.id
                                        LEFT JOIN sy$sy.bs_view_enrollment t3 ON t1.room_section_id=t3.room_section_id
				                        LEFT JOIN building_sectioning.view_subject_grdlvl_personnel_assgnmnt t5 ON t1.room_section_id=t5.room_section_id AND t1.subject_id=t5.subject_id
                                        LEFT JOIN sy$sy.bs_m_view_grades t4 ON t3.enrollment_id=t4.learner_enrollment_id AND t1.id=t4.rm_sctn_sbjct_assgnmnt_id
                                        WHERE t5.schoolpersonnel_id=$spid AND t3.room_section_id=$rsid AND t1.subject_id=$sbjctid
                                        ORDER BY t3.sex DESC, t3.last_fullname");


            $a = '<div class="tab-pane fade show ' . $active . '" id="custom-tabs-' . $rsid . $sbjctid . '" role="tabpanel" aria-labelledby="custom-tabs-' . $rsid . $sbjctid . '-tab">
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 p-1"><span class="q1c 3221232' . $rssa_id . '">' . $this->apprvGradesBtn($q1stat, $q1c, $q1rmrk, 3221232, 1, $rssa_id) . '</span></div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 p-1"><span class="q2c 2123221' . $rssa_id . '">' . $this->apprvGradesBtn($q2stat, $q2c, $q2rmrk, 2123221, 2, $rssa_id) . '</span></div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 p-1"><span class="q3c 3211123' . $rssa_id . '">' . $this->apprvGradesBtn($q3stat, $q3c, $q3rmrk, 3211123, 3, $rssa_id) . '</span></div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 p-1"><span class="q4c 4522323' . $rssa_id . '">' . $this->apprvGradesBtn($q4stat, $q4c, $q4rmrk, 4522323, 4, $rssa_id) . '</span></div>
                    </div>
                    <div class="post">
                        <div class="user-block">
                            <img class="img-circle img-bordered-sm" src="' . base_url() . $img . '" alt="user image">
                            <span class="username">
                                <a href="#" class="personnel">' . $fn_advsry . ' - ' . $fn_advsry_ttl . '</a>
                            </span>
                            <span class="description">' . $advisory . '<b>' . $grd_sctn . '</b><small> <i>' . $sbjct . '</i> | <i>' . $schedule . '</i></small></span>
                            <!-- <span class="description">Class Advisory <b>Grade XII - ABCD</b> <small><i>ARTS</i> | <i>WD</i></small></span> -->
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-sm table-hover table-striped" id="tblAllGradesList' . $rsid . '" width="100%">
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

            $c_male = 1;
            $c_fmale = 1;
            foreach ($query2->result() as $key2 => $value2) {
                $birthDate = date_create($value2->birthdate);
                $birthDate = strtoupper(date_format($birthDate, "m-d-Y"));
                $sex = substr($value2->sex, 0, 1);
                $q1 = $value2->q1; // ($q1stat ? $value2->q1 : 0);
                $q2 = $value2->q2; // ($q2stat ? $value2->q2 : 0);
                $q3 = $value2->q3; // ($q3stat ? $value2->q3 : 0);
                $q4 = $value2->q4; // ($q4stat ? $value2->q4 : 0);
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
            $content .= $a . $c . $b;
        }

        $data = '<div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">' . $tab . '</ul>
                    </div>
                    <div class="card-body">
                            <div class="tab-content" id="custom-tabs-one-tabContent">
                    ' . $content . '
                    </div>
                </div>
                <script>
                    rssaid_tmp = 0;
                </script>';
        echo json_encode($data);
    }

    function getViewAllGrades1()
    {
        $sid = $this->input->get("a");
        $tab = null;
        $tab2 = null;
        $content = null;
        $content2 = null;
        $tmprsid = null;
        $sy = $this->getOnLoad()["sy_id"];
        $qrtr = $this->getOnLoad()["qrtr"];
        $query = $this->db->query("SELECT t3.*,t1.*,t4.parent,t2.full_name AS fn_advsry,t2.personal_title AS fn_advsry_ttl FROM building_sectioning.view_subject_grdlvl_personnel_assgnmnt t1
                                    LEFT JOIN (SELECT t1.parent_party_id AS parent FROM global.tbl_party t1
                                                WHERE t1.parent_party_id IS NOT NULL
                                                GROUP BY t1.parent_party_id) t4 ON t1.subject_id = t4.parent
                                   LEFT JOIN (SELECT * FROM building_sectioning.view_subject_grdlvl_personnel_assgnmnt
												WHERE advisory=true
                                   ) t2 ON t1.room_section_id=t2.room_section_id
                                   LEFT JOIN (
                                        SELECT DISTINCT(t1.rm_sctn_sbjct_assgnmnt_id) AS rssaid,t2.q1c,t2.q1stat,t2.q1rmrk,t3.q2c,t3.q2stat,t3.q2rmrk,t4.q3c,t4.q3stat,t4.q3rmrk,t5.q4c,t5.q4stat,t5.q4rmrk
                                        FROM sy$sy.bs_tbl_learner_grades t1 					
                                        LEFT JOIN(SELECT t1.rm_sctn_sbjct_assgnmnt_id AS rssa_id,
                                                                            (SUM(CASE WHEN t1.grade IS NOT NULL THEN 1 ELSE 0 END)*100 / count(t1.id)) AS q1c,
                                                                            t2.status q1stat, t2.remarks q1rmrk
                                                            FROM sy$sy.bs_tbl_learner_grades t1 
                                                            LEFT JOIN (SELECT t1.*,t2.description AS status FROM sy$sy.bs_tbl_learner_grades_stat t1
                                                                                    LEFT JOIN global.tbl_status t2 ON t1.status_id=t2.id
                                                                                    WHERE t1.is_active=true AND t1.sy_id=$sy AND t1.qrtr=1) t2 ON t1.rm_sctn_sbjct_assgnmnt_id=t2.rssa_id
                                                            WHERE	t1.sy_id=$sy AND t1.qrtr_id=1
                                                            GROUP BY t1.rm_sctn_sbjct_assgnmnt_id,t2.status,t2.remarks) t2 ON t1.rm_sctn_sbjct_assgnmnt_id=t2.rssa_id
                                                            
                                        LEFT JOIN(SELECT t1.rm_sctn_sbjct_assgnmnt_id AS rssa_id,
                                                                            (SUM(CASE WHEN t1.grade IS NOT NULL THEN 1 ELSE 0 END)*100 / count(t1.id)) AS q2c,
                                                                            t2.status q2stat, t2.remarks q2rmrk
                                                            FROM sy$sy.bs_tbl_learner_grades t1 
                                                            LEFT JOIN (SELECT t1.*,t2.description AS status FROM sy$sy.bs_tbl_learner_grades_stat t1
                                                                                    LEFT JOIN global.tbl_status t2 ON t1.status_id=t2.id
                                                                                    WHERE t1.is_active=true AND t1.sy_id=$sy AND t1.qrtr=2) t2 ON t1.rm_sctn_sbjct_assgnmnt_id=t2.rssa_id
                                                            WHERE	t1.sy_id=$sy AND t1.qrtr_id=2
                                                            GROUP BY t1.rm_sctn_sbjct_assgnmnt_id,t2.status,t2.remarks) t3 ON t1.rm_sctn_sbjct_assgnmnt_id=t3.rssa_id
                                                            
                                        LEFT JOIN(SELECT t1.rm_sctn_sbjct_assgnmnt_id AS rssa_id,
                                                                            (SUM(CASE WHEN t1.grade IS NOT NULL THEN 1 ELSE 0 END)*100 / count(t1.id)) AS q3c,
                                                                            t2.status q3stat, t2.remarks q3rmrk
                                                            FROM sy$sy.bs_tbl_learner_grades t1 
                                                            LEFT JOIN (SELECT t1.*,t2.description AS status FROM sy$sy.bs_tbl_learner_grades_stat t1
                                                                                    LEFT JOIN global.tbl_status t2 ON t1.status_id=t2.id
                                                                                    WHERE t1.is_active=true AND t1.sy_id=$sy AND t1.qrtr=3) t2 ON t1.rm_sctn_sbjct_assgnmnt_id=t2.rssa_id
                                                            WHERE	t1.sy_id=$sy AND t1.qrtr_id=3
                                                            GROUP BY t1.rm_sctn_sbjct_assgnmnt_id,t2.status,t2.remarks) t4 ON t1.rm_sctn_sbjct_assgnmnt_id=t4.rssa_id
                                                            
                                        LEFT JOIN(SELECT t1.rm_sctn_sbjct_assgnmnt_id AS rssa_id,
                                                                            (SUM(CASE WHEN t1.grade IS NOT NULL THEN 1 ELSE 0 END)*100 / count(t1.id)) AS q4c,
                                                                            t2.status q4stat, t2.remarks q4rmrk
                                                            FROM sy$sy.bs_tbl_learner_grades t1 
                                                            LEFT JOIN (SELECT t1.*,t2.description AS status FROM sy$sy.bs_tbl_learner_grades_stat t1
                                                                                    LEFT JOIN global.tbl_status t2 ON t1.status_id=t2.id
                                                                                    WHERE t1.is_active=true AND t1.sy_id=$sy AND t1.qrtr=4) t2 ON t1.rm_sctn_sbjct_assgnmnt_id=t2.rssa_id
                                                            WHERE	t1.sy_id=$sy AND t1.qrtr_id=4
                                                            GROUP BY t1.rm_sctn_sbjct_assgnmnt_id,t2.status,t2.remarks) t5 ON t1.rm_sctn_sbjct_assgnmnt_id=t5.rssa_id


                                   ) t3 ON t1.rm_sctn_sbjct_assgnmnt_id=t3.rssaid
                                   WHERE t1.schoolpersonnel_id=$sid AND t1.schl_yr_id=$sy
                                   ORDER BY  t1.sctn_nm,t1.order_by_sbjct");
        foreach ($query->result() as $key => $value) {
            $q1c = $value->q1c;
            $q1stat = $value->q1stat;
            $q1rmrk = $value->q1rmrk;
            $q2c = $value->q2c;
            $q2stat = $value->q2stat;
            $q2rmrk = $value->q2rmrk;
            $q3c = $value->q3c;
            $q3stat = $value->q3stat;
            $q3rmrk = $value->q3rmrk;
            $q4c = $value->q4c;
            $q4stat = $value->q4stat;
            $q4rmrk = $value->q4rmrk;

            $spid = $value->schoolpersonnel_id;
            $rsid = $value->room_section_id;
            if ($tmprsid == null) {
                $tmprsid = $rsid;
            }
            $fn_advsry = $value->fn_advsry;
            $fn_advsry_ttl = $value->fn_advsry_ttl;
            $fn_img = $value->img_path;
            $sbjctabbr = $value->subject_abbr;
            $sbjct = $value->subject;
            $sbjctid = $value->subject_id;
            $sbjctid_rsid = $sbjctid . $rsid;
            // $teacher = $value->full_name;
            // $title = $value->personal_title;
            $advisory = $value->advisory == 't' ? 'Class Advisory ' : '';
            $schedule = $value->schedule;
            $grd_sctn = $value->grade . ' - ' . $value->sctn_nm;
            $active = "";
            $parnt_prty_id = $value->parent_party_id;
            $parnt = $value->parent;
            $parnt_rsid = $parnt . $rsid;

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
                        <a onclick="' . ((($parnt_rsid == $sbjctid_rsid) && ($rsid == $tmprsid)) || ($parnt_prty_id)
                ? "$('.viewAllGrades2, .sbjct_group').show();$('.sbjct_sngl').hide();$('.viewAllGrades2 .card-header .nav .nav-item .nav-link').removeClass('active');"
                : "$('.viewAllGrades2, .sbjct_group').hide();$('.sbjct_sngl').show()") . '" 
                            class="nav-link ' . $active . '" 
                            title="' . $sbjct . '" 
                            id="custom-tabs-' . $sbjctid_rsid . '-tab" data-toggle="pill" 
                            href="#custom-tabs-' . $sbjctid_rsid . '" 
                            role="tab" aria-controls="custom-tabs-' . $sbjctid_rsid . '" 
                            aria-selected="false">' . $grd_sctn . '-' . $sbjctabbr . '-' . $sbjctid_rsid . '-' . $sid . '</a>
                            
                            <script>' . ((($parnt_rsid == $sbjctid_rsid) && ($rsid == $tmprsid)) || ($parnt_prty_id) ? "$('.viewAllGrades2, .sbjct_group').hide()" : "") . '
                                document.querySelector("#custom-tabs-77-tab")                            
                                document.querySelector("#custom-tabs-four-tab > li:nth-child(1)")
                            </script>
                    </li>';
            if (($parnt_rsid == $sbjctid_rsid) && ($rsid == $tmprsid)) {
                $tab2 .= $t;
            }

            if (!$parnt_prty_id) {
                $tab .= $t;
            } else {
                $tab2 .= $t;
            }

            $query2 = $this->db->query("SELECT t4.q1,t4.q2,t4.q3,t4.q4,t5.advisory,t5.full_name AS teacher_full_name,t5.personal_title,t1.id,t3.* FROM building_sectioning.tbl_room_section_subject_assignment t1
                                        LEFT JOIN building_sectioning.tbl_room_section t2 ON t1.room_section_id=t2.id
                                        LEFT JOIN sy$sy.bs_view_enrollment t3 ON t1.room_section_id=t3.room_section_id
				                        LEFT JOIN building_sectioning.view_subject_grdlvl_personnel_assgnmnt t5 ON t1.room_section_id=t5.room_section_id AND t1.subject_id=t5.subject_id
                                        LEFT JOIN (SELECT t1.*,q1.grade q1,q2.grade q2,q3.grade q3,q4.grade q4 FROM (SELECT t1.learner_enrollment_id, t1.rm_sctn_sbjct_assgnmnt_id
                                                FROM sy$sy.bs_tbl_learner_grades t1
                                                GROUP BY t1.learner_enrollment_id, t1.rm_sctn_sbjct_assgnmnt_id) t1
                                                LEFT JOIN (SELECT t1.learner_enrollment_id, t1.rm_sctn_sbjct_assgnmnt_id, t1.grade
                                                    FROM sy$sy.bs_tbl_learner_grades t1 where t1.sy_id=$sy AND t1.qrtr_id=1)q1 ON t1.learner_enrollment_id =q1.learner_enrollment_id AND t1.rm_sctn_sbjct_assgnmnt_id =q1.rm_sctn_sbjct_assgnmnt_id
                                                LEFT JOIN (SELECT t1.learner_enrollment_id, t1.rm_sctn_sbjct_assgnmnt_id, t1.grade
                                                    FROM sy$sy.bs_tbl_learner_grades t1 where t1.sy_id=$sy AND t1.qrtr_id=2)q2 ON t1.learner_enrollment_id =q2.learner_enrollment_id AND t1.rm_sctn_sbjct_assgnmnt_id =q2.rm_sctn_sbjct_assgnmnt_id
                                                LEFT JOIN (SELECT t1.learner_enrollment_id, t1.rm_sctn_sbjct_assgnmnt_id, t1.grade
                                                    FROM sy$sy.bs_tbl_learner_grades t1 where t1.sy_id=$sy AND t1.qrtr_id=3)q3 ON t1.learner_enrollment_id =q3.learner_enrollment_id AND t1.rm_sctn_sbjct_assgnmnt_id =q3.rm_sctn_sbjct_assgnmnt_id
                                                LEFT JOIN (SELECT t1.learner_enrollment_id, t1.rm_sctn_sbjct_assgnmnt_id, t1.grade
                                                    FROM sy$sy.bs_tbl_learner_grades t1 where t1.sy_id=$sy AND t1.qrtr_id=4)q4 ON t1.learner_enrollment_id =q4.learner_enrollment_id AND t1.rm_sctn_sbjct_assgnmnt_id =q4.rm_sctn_sbjct_assgnmnt_id)
                                            t4 ON t3.enrollment_id=t4.learner_enrollment_id AND t1.id=t4.rm_sctn_sbjct_assgnmnt_id
                                        WHERE t5.schoolpersonnel_id=$spid AND t3.room_section_id=$rsid AND t1.subject_id=$sbjctid
                                        ORDER BY t3.sex DESC, t3.last_fullname");


            $aa = '<div class="tab-pane fade show ' . $active . '" id="custom-tabs-' . $sbjctid_rsid . '" role="tabpanel" aria-labelledby="custom-tabs-' . $sbjctid_rsid . '-tab">
            <div class="row ' . ((($parnt_rsid == $sbjctid_rsid) && ($rsid == $tmprsid)) || ($parnt_prty_id) ? 'sbjct_group' : 'sbjct_sngl') . '">
                <div class="col-12">
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 p-1"><span class="q1c">' . $this->submitGradesBtn($q1stat, $q1c, $q1rmrk, 3221232, 1) . '</span></div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 p-1"><span class="q2c">' . $this->submitGradesBtn($q2stat, $q2c, $q2rmrk, 2123221, 2) . '</span></div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 p-1"><span class="q3c">' . $this->submitGradesBtn($q3stat, $q3c, $q3rmrk, 3211123, 3) . '</span></div>
                        <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 p-1"><span class="q4c">' . $this->submitGradesBtn($q4stat, $q4c, $q4rmrk, 4522323, 4) . '</span></div>
                    </div>
                    <div class="post">
                        <div class="user-block">
                            <img class="img-circle img-bordered-sm" src="http://' . $_SERVER['HTTP_HOST'] . $fn_img . '" alt="user image">
                            <span class="username">
                                <a href="#" class="personnel">' . $fn_advsry . ' - ' . $fn_advsry_ttl . '</a>
                            </span>
                            <span class="description">' . $advisory . '<b>' . $grd_sctn . '</b><small> <i>' . $sbjct . '</i> | <i>' . $schedule . '</i></small></span>
                            <!-- <span class="description">Class Advisory <b>Grade XII - ABCD</b> <small><i>ARTS</i> | <i>WD</i></small></span> -->
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-sm table-hover table-striped ' . ((($parnt_rsid == $sbjctid_rsid) && ($rsid == $tmprsid)) || ($parnt_prty_id) ? 'sbjct_group' : 'sbjct_sngl') . '" id="tblAllGradesList' . $rsid . '" width="100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>LRN</th>
                                <th>Personal Details</th>
                                <th>Sex</th>
                                <th>Status</th>
                                <th>Q1</th>
                                <th>Q2</th>
                                <th>Q3s</th>
                                <th>Q4</th>
                            </tr>
                        </thead>
                        <tbody>';
            if (($parnt_rsid == $sbjctid_rsid) && ($rsid == $tmprsid)) {
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
                $birthDate = date_create($value2->birthdate);
                $birthDate = strtoupper(date_format($birthDate, "m-d-Y"));
                $sex = substr($value2->sex, 0, 1);
                $q1 = $value2->q1;
                $q2 = $value2->q2;
                $q3 = $value2->q3;
                $q4 = $value2->q4;
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
                            </tr>';
            }


            $b =      '</tbody>
                    </table>
                  </div>';
            // $content .= $a . $c . $b;

            if (($parnt_rsid == $sbjctid_rsid) && ($rsid == $tmprsid)) {
                $content2 .= $a2 . $c . $b;
            }

            if (!$parnt_prty_id) {
                $content .= $a . $c . $b;
            } else {
                $content2 .= $a2 . $c . $b;
            }
        }

        // $data = '<div class="card-header p-0 border-bottom-0">
        //             <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">' . $tab . '</ul>
        //         </div>
        //         <div class="card-body">
        //                 <div class="tab-content" id="custom-tabs-one-tabContent">
        //           ' . $content . '
        //         </div>
        //         </div>';
        $data = '<div class="card-header p-0 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">' . $tab . '</ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-one-tabContent">
                        
                        <div class="card card-info p-0 mt-n2 table-responsive viewAllGrades2" style="display:none;">
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
}
