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

    function getGradesList()//s
    {
        $data = ["data" => []];
        $rmsid = $this->session->schoolmis_login_rm_sec_id;
        $lrn = $this->session->schoolmis_login_lrn;
        $sy = $this->getOnLoad()["sy_id"];
        $v_grades = $this->getOnLoad()["v_grades"];
        $tga = 0;
        $ga = 0;
        $cga = 0;
        $query = $this->db->query("SELECT t4.q1,t4.q2,t4.q3,t4.q4,t1.rm_sctn_sbjct_assgnmnt_id,t2.enrollment_id,t1.subject,t1.subject_abbr,t1.schedule,t1.advisory,t1.full_name,t1.personal_title,t1.parent_party_id FROM building_sectioning.view_subject_grdlvl_personnel_assgnmnt t1
                                    LEFT JOIN sy$sy.bs_view_enrollment t2 ON t1.room_section_id=t2.room_section_id AND t1.schl_yr_id=t2.schl_yr_id
                                    LEFT JOIN(SELECT t1.learner_enrollment_id,t1.rm_sctn_sbjct_assgnmnt_id,
			                                    CASE WHEN (t1.q1stat='18') THEN t1.q1 ELSE 0 END q1,
			                                    CASE WHEN (t1.q2stat='18') THEN t1.q2 ELSE 0 END q2,
			                                    CASE WHEN (t1.q3stat='18') THEN t1.q3 ELSE 0 END q3,
			                                    CASE WHEN (t1.q4stat='18') THEN t1.q4 ELSE 0 END q4
			                                    FROM sy$sy.bs_m_view_grades t1) t4 ON t2.enrollment_id=t4.learner_enrollment_id AND t1.rm_sctn_sbjct_assgnmnt_id=t4.rm_sctn_sbjct_assgnmnt_id
                                    LEFT JOIN building_sectioning.view_room_section t5 ON t1.rm_sctn_sbjct_assgnmnt_id=t5.rm_sctn_sbjct_assgnmnt_id
                                    WHERE t1.room_section_id=$rmsid AND t2.lrn ='$lrn' AND t1.schl_yr_id=1
                                    ORDER BY t1.order_by_sbjct");
        $arr = $query->result();
        foreach ($arr as $key => $value) {
            $p = $value->parent_party_id;
            $t = ($p ? '&emsp;' : null);
            $q1 = $value->q1; //rand(99, 88);//
            $q2 = $value->q2; //rand(99, 88);//
            $q3 = $value->q3; //rand(99, 88);//
            $q4 = $value->q4; //rand(99, 88);//
            $fg = $this->avg4($q1, $q2, $q3, $q4);
            if ($fg && !$p) {
                $ga += intval($fg);
                $cga += 1;
            } else if (!$fg && !$p) {
                $tga += 1;
            }
            if ($value->full_name) {
                $f_g = (!$p ? ($fg > 0 ? $fg : '--') : '');
                $data["data"][] = [
                    "<h6>$t $value->subject</h6>
                        <viewdetails class='view_details' style='display:none;'>
                            " . $t . "<small class='text-xs " . ($value->advisory == 't' ? 'text-success' : '') . "'><i class='fa fa-caret-right'></i> $value->full_name</small>
                        <viewdetails/>",
                    "<p class='text-center mb-n2'>" . ($v_grades == 't' ? ($q1 > 0 ? $q1 : '--') : '--') . "</p>",
                    "<p class='text-center mb-n2'>" . ($v_grades == 't' ? ($q2 > 0 ? $q2 : '--') : '--') . "</p>",
                    "<p class='text-center mb-n2'>" . ($v_grades == 't' ? ($q3 > 0 ? $q3 : '--') : '--') . "</p>",
                    "<p class='text-center mb-n2'>" . ($v_grades == 't' ? ($q4 > 0 ? $q4 : '--') : '--') . "</p>",
                    "<p class='text-center mb-n2'><b>" . ($v_grades == 't' ?$f_g: '--') . "</b></p>",
                    "<p class='text-center mb-n2'>" . ($p ? '' : ($v_grades == 't' ? ($f_g != "--" ? ($f_g >= 75 ? "PASSED" : "FAILED") : "--") : '--')) . "</p>",
                ];
            }
            if ($key === array_key_last($arr)) {
                $data["data"][] = [
                    "<p class='text-right mb-1 text-md'><b>General Average</b></p>",
                    "",
                    "",
                    "",
                    "",
                    // "<p class='text-center mb-1 text-md'><b>" . ($v_grades == 't' ? ($tga == 0 ? round($ga / $cga, 0) : '--') : '--') . "</b></p>",
                    round($ga / $cga, 0),//"<p class='text-center mb-1 text-md'><b>" . ($v_grades == 't' ? ($tga == 0 ? round($ga / $cga, 0) : '--') : '--') . "</b></p>",
                    "",
                ];
            }
        }
        echo json_encode($data);
    }
}
