<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reports extends MY_Controller
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
            "page_title"        => "Report",
            "current_location"  => "report",
            "content"           =>  [$this->load->view('interface/' . $uri . '/Report', [], TRUE)]
        ];
        $this->public_create_page($page_data);
    }

    function getReportConsoGrades()
    {
        $data = ["thead" => [], "thead2" => [], "tbody" => [], "signatory1" => [], "signatory2" => [], "signatory3" => []];
        $rsid = $this->input->post("rsid");
        $thead = [];
        $thead2 = [];
        $tbody = [];
        $signatory1 = [];
        $signatory2 = [];
        $signatory3 = [];

        $qar = [];
        $qar1 = 0;
        $cc = 0;
        $cc_fmale = 1;
        $cc_male = 1;
        $ccc = 1;
        $rssaid = 0;
        $ppid = 0;
        $ppid_cc = 0;
        $sbjct_cc = 0;
        $optional = "";
        $na = "";
        $optional_cc = 0;
        $na_cc = 0;
        $sy = $this->getOnLoad()["sy_id"];
        $queryHead = $this->db->query("SELECT t1.rm_sctn_sbjct_assgnmnt_id,t1.subject_abbr,t1.subject,t1.subject_id,t1.full_name,
        t1.personal_title,t1.advisory,t1.schedule,t1.grade,t1.sctn_nm,t1.parent_party_id,
        t2.parent,t1.color,t1.is_optional,t1.is_na FROM building_sectioning.view_subject_grdlvl_personnel_assgnmnt t1
                                    LEFT JOIN (SELECT t1.parent_party_id AS parent FROM global.tbl_party t1
                                                WHERE t1.parent_party_id IS NOT NULL
                                                GROUP BY t1.parent_party_id) t2 ON t1.subject_id = t2.parent
                                    WHERE t1.room_section_id=$rsid AND t1.schl_yr_id=$sy AND t1.full_name IS NOT NULL 
                                    ORDER BY t1.order_by_sbjct");
        $thead[] = ["<td width='1' rowspan='2'>#</td><td width='1' rowspan='2'>Learner</td>"];
        $t_eid = 0;

        foreach ($queryHead->result() as $key => $value) {
            // $qar1 = 0;
            $color = $value->color;
            $optional = $value->is_optional;
            $stc = 'background-color:' . $color;
            $na = $value->is_na;
            $na_cc = $na_cc + ($na == "t" ? 1  : 0);
            $sbjct_cc = $sbjct_cc + (($optional == "t" || $na == "t") ? 0  : 1);
            $optional_cc = $optional_cc + ($optional == "t" ? 1  : 0);


            $thead[] = [
                "<td colspan='5' style='" . $stc . ";'>" . $value->subject_abbr . "</td>"
            ];

            if ($na != "t") {
                $cc++;

                if ($cc == 1 || $cc == 5) {
                    $signatory1[] = [
                        "<tr><td style='border:1px solid white;border-top:1px solid black;border-bottom:1px solid white'> </td></tr><tr><td style='border:1px solid white;'> </td></tr>"
                    ];
                }
                if ($cc == 9) {
                    $signatory2[] = [
                        "<tr><td style='border:1px solid white;'> </td></tr><tr><td style='border:1px solid white;'> </td></tr>"
                    ];
                }
                if ($cc <= 4) {
                    $signatory1[] = [
                        "<td style='border:1px solid white' colspan='" . ($cc == 1 ? 15 : 20) . "' width='1'><p style='text-align:center'><u>  " . strtoupper($value->full_name) . '  </u></br>' .
                            strtoupper($value->personal_title) . '-' .
                            strtoupper($value->subject_abbr) . "</p></td>"
                    ];
                } else if ($cc <= 8) {
                    $signatory2[] = [
                        "<td style='border:1px solid white' colspan='" . ($cc == 5 ? 15 : 20) . "' width='1'><p style='text-align:center'><u>  " . strtoupper($value->full_name) . '  </u></br>' .
                            strtoupper($value->personal_title) . '-' .
                            strtoupper($value->subject_abbr) . "</p></td>"
                    ];
                } else {
                    $signatory3[] = [
                        "<td style='border:1px solid white' colspan='" . ($cc == 9 ? 15 : 20) . "' width='1'><p style='text-align:center'><u>  " . strtoupper($value->full_name) . '  </u></br>' .
                            strtoupper($value->personal_title) . '-' .
                            strtoupper($value->subject_abbr) . "</p></td>"
                    ];
                }
            }

            $thead2[] = [
                "<td>Q1</td><td>Q2</td><td>Q3</td><td>Q4</td><td style='" . $stc . ";'>FG</td>"
            ];
            $rssaid = $value->rm_sctn_sbjct_assgnmnt_id;
            // $ppid = $value->parent_party_id;
            // $ppid_cc =  $ppid_cc + ($ppid == null ? 0  : 1);

            $queryBody = $this->db->query("SELECT t2.sex,t2.last_fullname,t2.enrollment_id
                                            FROM sy$sy.bs_view_enrollment t2
                                            WHERE t2.room_section_id=$rsid
                                            ORDER BY t2.sex_bool DESC,t2.last_fullname 
                                            ");
            if (empty($tbody)) {
                foreach ($queryBody->result() as $key => $value2) {
                    $sex = substr($value2->sex, 0, 1);
                    if ($cc_fmale == 1 && $sex == 'F') {
                        $tbody[] = [
                            "<tr><td align='left'> </td></tr>"
                        ];
                    }
                    $tbody[] = [
                        "<tr id='" . $value2->enrollment_id . "'><td align='left'>" . ($sex == 'M' ? $cc_male++ : $cc_fmale++) . "</td>
                        <td align='left' style='white-space: nowrap;'>" . $value2->last_fullname . "</td></tr>"
                    ];
                    $qar[] = [
                        "eid" => $value2->enrollment_id,
                    ];
                }
            }

            $queryBody2 = $this->db->query("SELECT t1.enrollment_id,t1.full_name,t2.q1,t2.q2,t2.q3,t2.q4 FROM sy$sy.bs_view_enrollment t1
                                            LEFT JOIN(SELECT t1.learner_enrollment_id,t1.rm_sctn_sbjct_assgnmnt_id, 
                                                        SUM(t1.q1) AS q1,SUM(t1.q2) AS q2,SUM(t1.q3) AS q3,SUM(t1.q4) AS q4 from(
                                                        SELECT t1.id,t1.learner_enrollment_id,t1.rm_sctn_sbjct_assgnmnt_id,
                                                        t1.q1,
                                                        t1.q2,
                                                        t1.q3,
                                                        t1.q4
                                                        FROM sy$sy.bs_tbl_learner_grades t1
                                                        )AS t1 
                                                        WHERE t1.rm_sctn_sbjct_assgnmnt_id=$rssaid
                                                        GROUP BY t1.learner_enrollment_id,t1.rm_sctn_sbjct_assgnmnt_id) t2 ON t1.enrollment_id=t2.learner_enrollment_id
                                                        WHERE t1.room_section_id = $rsid
                                                        ");
            foreach ($queryBody2->result() as $key => $value3) {
                $eid = $value3->enrollment_id;
                $q1 = $this->returnZero($value3->q1);
                $q2 = $this->returnZero($value3->q2);
                $q3 = $this->returnZero($value3->q3);
                $q4 = $this->returnZero($value3->q4);
                $avg = round(($q1 + $q2 + $q3 + $q4) / 4, 0);
                // $avg2 = ($q1 + $q2 + $q3 + $q4) / 4;

                $tbody[] = [
                    "<script>
                        $('#tblReportConsoGrades #" . $eid . "').append('<td class=" . ($na == "f" ? 'q1' . $eid : '') . " width=" . 1 . " align=" . 'center' . ">" . $this->returnDDashed($q1) . "</td>'+
                                                                        '<td class=" . ($na == "f" ? 'q2' . $eid : '') . "  width=" . 1 . " align=" . 'center' . ">" . $this->returnDDashed($q2) . "</td>'+
                                                                        '<td class=" . ($na == "f" ? 'q3' . $eid : '') . "  width=" . 1 . " align=" . 'center' . ">" . $this->returnDDashed($q3) . "</td>'+
                                                                        '<td class=" . ($na == "f" ? 'q4' . $eid : '') . "  width=" . 1 . " align=" . 'center' . ">" . $this->returnDDashed($q4) . "</td>'+
                                                                        '<td class=" . ($na == "f" ? 'avg' . $eid : '') . "  width=" . 1 . " align=" . 'center' . " style=" . $stc . ">" . $this->returnDDashed($avg) . "</td>')
                    </script>"
                ];
            }
        }

        $thead[] = [
            "<td colspan='8' width='1'>QUARTER AVE. & RANK</td>" .
                "<td rowspan='2' width='1'>GEN. AV.</td>" .
                "<td rowspan='2' width='1'>FINAL RANK</td>"
        ];

        $thead2[] = [
            "<td>Q1</td><td>R1</td>" .
                "<td>Q2</td><td>R2</td>" .
                "<td>Q3</td><td>R3</td>" .
                "<td>Q4</td><td>R4</td>"
        ];
        $zz = 0;
        $sbjct_cc = $sbjct_cc + ($optional_cc > 0 ? 1 : 0);
        for ($i = 0; $i < count($qar); $i++) {
            $tbody[] = [
                "<script>
                    var sumq1 = 0,sumq2 = 0,sumq3 = 0,sumq4 = 0,sumavg = 0;
                    $('#tblReportConsoGrades #" . $qar[$i]["eid"] . " .q1" . $qar[$i]["eid"] . "').each(function(){
                        a = $(this).html()=='--'?0:$(this).html();
                        sumq1 += +a/" . $sbjct_cc . ";
                    });

                    $('#tblReportConsoGrades #" . $qar[$i]["eid"] . " .q2" . $qar[$i]["eid"] . "').each(function(){
                        a = $(this).html()=='--'?0:$(this).html();
                        sumq2 += +a/" . $sbjct_cc . ";
                    });

                    $('#tblReportConsoGrades #" . $qar[$i]["eid"] . " .q3" . $qar[$i]["eid"] . "').each(function(){
                        a = $(this).html()=='--'?0:$(this).html();
                        sumq3 += +a/" . $sbjct_cc . ";
                    });

                    $('#tblReportConsoGrades #" . $qar[$i]["eid"] . " .q4" . $qar[$i]["eid"] . "').each(function(){
                        a = $(this).html()=='--'?0:$(this).html();
                        sumq4 += +a/" . $sbjct_cc . ";
                    });
                    
                    sumq1 = parseInt(sumq1.toFixed());
                    sumq2 = parseInt(sumq2.toFixed());
                    sumq3 = parseInt(sumq3.toFixed());
                    sumq4 = parseInt(sumq4.toFixed());
                    
                    sumavg = parseInt(((sumq1+sumq2+sumq3+sumq4)/4).toFixed());

                    $('#tblReportConsoGrades #" . $qar[$i]["eid"] . "').append('<td class=" . 'q1TBRank' . " width=" . 1 . " align=" . 'center' . ">'+sumq1+'</td>');
                    $('#tblReportConsoGrades #" . $qar[$i]["eid"] . "').append('<td class=" . 'q1Rank' . " width=" . 1 . " align=" . 'center' . "></td>');
                    $('#tblReportConsoGrades #" . $qar[$i]["eid"] . "').append('<td class=" . 'q2TBRank' . " width=" . 1 . " align=" . 'center' . ">'+sumq2+'</td>');
                    $('#tblReportConsoGrades #" . $qar[$i]["eid"] . "').append('<td class=" . 'q2Rank' . " width=" . 1 . " align=" . 'center' . "></td>');
                    $('#tblReportConsoGrades #" . $qar[$i]["eid"] . "').append('<td class=" . 'q3TBRank' . " width=" . 1 . " align=" . 'center' . ">'+sumq3+'</td>');
                    $('#tblReportConsoGrades #" . $qar[$i]["eid"] . "').append('<td class=" . 'q3Rank' . " width=" . 1 . " align=" . 'center' . "></td>');
                    $('#tblReportConsoGrades #" . $qar[$i]["eid"] . "').append('<td class=" . 'q4TBRank' . " width=" . 1 . " align=" . 'center' . ">'+sumq4+'</td>');
                    $('#tblReportConsoGrades #" . $qar[$i]["eid"] . "').append('<td class=" . 'q4Rank' . " width=" . 1 . " align=" . 'center' . "></td>');
                    $('#tblReportConsoGrades #" . $qar[$i]["eid"] . "').append('<td class=" . 'avgTBRank' . " width=" . 1 . " align=" . 'center' . ">'+sumavg+'</td>');
                    $('#tblReportConsoGrades #" . $qar[$i]["eid"] . "').append('<td class=" . 'avgRank' . " width=" . 1 . " align=" . 'center' . "></td>');
                </script>"
            ];
        }

        $data["thead"] = $thead;
        $data["thead2"] = $thead2;
        $data["tbody"] = $tbody;
        $data["signatory1"] = $signatory1;
        $data["signatory2"] = $signatory2;
        $data["signatory3"] = $signatory3;

        echo json_encode($data);
    }
}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */