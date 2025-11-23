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
            "page_title"        => "Reports",
            "current_location"  => "reports",
            "content"           =>  [$this->load->view('interface/' . $uri . '/Reports', [
                "getOnLoad" => $this->getOnLoad(),
                "getSHdboard" => $this->getSHdboard(),
            ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }

    function getMFGradelvl()
    {
        parse_str($this->input->post("a"), $filter);
        $sy = $filter['sy'];

        $data = ["data" => []];
        // $sy = $this->getOnLoad()["sy_id"];
        $tmpG = "";

        $tb11_m = 0;
        $t11_m = 0;
        $t12_m = 0;
        $t13_m = 0;
        $t14_m = 0;
        $t15_m = 0;
        $t16_m = 0;
        $t17_m = 0;
        $t18_m = 0;
        $t19_m = 0;
        $t20_m = 0;
        $t4p_m = 0;
        $tb11_f = 0;
        $t11_f = 0;
        $t12_f = 0;
        $t13_f = 0;
        $t14_f = 0;
        $t15_f = 0;
        $t16_f = 0;
        $t17_f = 0;
        $t18_f = 0;
        $t19_f = 0;
        $t20_f = 0;
        $t4p_f = 0;

        $stb11_m = 0;
        $st11_m = 0;
        $st12_m = 0;
        $st13_m = 0;
        $st14_m = 0;
        $st15_m = 0;
        $st16_m = 0;
        $st17_m = 0;
        $st18_m = 0;
        $st19_m = 0;
        $st20_m = 0;
        $st4p_m = 0;
        $stb11_f = 0;
        $st11_f = 0;
        $st12_f = 0;
        $st13_f = 0;
        $st14_f = 0;
        $st15_f = 0;
        $st16_f = 0;
        $st17_f = 0;
        $st18_f = 0;
        $st19_f = 0;
        $st20_f = 0;
        $st4p_f = 0;

        $t_m = 0;
        $t_f = 0;

        $total_m = 0;
        $total_f = 0;
        $stotal_m = 0;
        $stotal_f = 0;

        $empty = [
            " ", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "",
        ];

        $query = $this->db->query("SELECT t1.*,t2.four_ps_m,t2.four_ps_f FROM (
                                    SELECT t1.rm_sctn_sbjct_assgnmnt_id AS rssaid,t1.sctn_nm,t1.grade,
                                    COALESCE(SUM(t2.below_11_m),0) AS below_11_m,
                                    COALESCE(SUM(t2.below_11_f),0) AS below_11_f,
                                    COALESCE(SUM(t2.age_11_m),0) AS age_11_m,
                                    COALESCE(SUM(t2.age_11_f),0) AS age_11_f,
                                    COALESCE(SUM(t2.age_12_m),0) AS age_12_m,
                                    COALESCE(SUM(t2.age_12_f),0) AS age_12_f,
                                    COALESCE(SUM(t2.age_13_m),0) AS age_13_m,
                                    COALESCE(SUM(t2.age_13_f),0) AS age_13_f,
                                    COALESCE(SUM(t2.age_14_m),0) AS age_14_m,
                                    COALESCE(SUM(t2.age_14_f),0) AS age_14_f,
                                    COALESCE(SUM(t2.age_15_m),0) AS age_15_m,
                                    COALESCE(SUM(t2.age_15_f),0) AS age_15_f,
                                    COALESCE(SUM(t2.age_16_m),0) AS age_16_m,
                                    COALESCE(SUM(t2.age_16_f),0) AS age_16_f,
                                    COALESCE(SUM(t2.age_17_m),0) AS age_17_m,
                                    COALESCE(SUM(t2.age_17_f),0) AS age_17_f,
                                    COALESCE(SUM(t2.age_18_m),0) AS age_18_m,
                                    COALESCE(SUM(t2.age_18_f),0) AS age_18_f,
                                    COALESCE(SUM(t2.age_19_m),0) AS age_19_m,
                                    COALESCE(SUM(t2.age_19_f),0) AS age_19_f,
                                    COALESCE(SUM(t2.above_20_m),0) AS above_20_m,
                                    COALESCE(SUM(t2.above_20_f),0) AS above_20_f
                                    FROM building_sectioning.view_room_section t1
                                    LEFT JOIN (SELECT t1.*
                                    ,SUM(CASE WHEN t1.age_gap<11 AND t1.sex_bool=TRUE THEN 1 END) AS below_11_m 
													,SUM(CASE WHEN t1.age_gap<11 AND t1.sex_bool=FALSE THEN 1 END) AS below_11_f 
													,SUM(CASE WHEN t1.age_gap=11 AND t1.sex_bool=TRUE THEN 1 END) AS age_11_m 
													,SUM(CASE WHEN t1.age_gap=11 AND t1.sex_bool=FALSE THEN 1 END) AS age_11_f 
													,SUM(CASE WHEN t1.age_gap=12 AND t1.sex_bool=TRUE THEN 1 END) AS age_12_m 
													,SUM(CASE WHEN t1.age_gap=12 AND t1.sex_bool=FALSE THEN 1 END) AS age_12_f 
													,SUM(CASE WHEN t1.age_gap=13 AND t1.sex_bool=TRUE THEN 1 END) AS age_13_m 
													,SUM(CASE WHEN t1.age_gap=13 AND t1.sex_bool=FALSE THEN 1 END) AS age_13_f 
													,SUM(CASE WHEN t1.age_gap=14 AND t1.sex_bool=TRUE THEN 1 END) AS age_14_m 
													,SUM(CASE WHEN t1.age_gap=14 AND t1.sex_bool=FALSE THEN 1 END) AS age_14_f 
													,SUM(CASE WHEN t1.age_gap=15 AND t1.sex_bool=TRUE THEN 1 END) AS age_15_m 
													,SUM(CASE WHEN t1.age_gap=15 AND t1.sex_bool=FALSE THEN 1 END) AS age_15_f 
													,SUM(CASE WHEN t1.age_gap=16 AND t1.sex_bool=TRUE THEN 1 END) AS age_16_m 
													,SUM(CASE WHEN t1.age_gap=16 AND t1.sex_bool=FALSE THEN 1 END) AS age_16_f 
													,SUM(CASE WHEN t1.age_gap=17 AND t1.sex_bool=TRUE THEN 1 END) AS age_17_m 
													,SUM(CASE WHEN t1.age_gap=17 AND t1.sex_bool=FALSE THEN 1 END) AS age_17_f 
													,SUM(CASE WHEN t1.age_gap=18 AND t1.sex_bool=TRUE THEN 1 END) AS age_18_m 
													,SUM(CASE WHEN t1.age_gap=18 AND t1.sex_bool=FALSE THEN 1 END) AS age_18_f 
													,SUM(CASE WHEN t1.age_gap=19 AND t1.sex_bool=TRUE THEN 1 END) AS age_19_m 
													,SUM(CASE WHEN t1.age_gap=19 AND t1.sex_bool=FALSE THEN 1 END) AS age_19_f 
													,SUM(CASE WHEN t1.age_gap>19 AND t1.sex_bool=TRUE THEN 1 END) AS above_20_m
													,SUM(CASE WHEN t1.age_gap>19 AND t1.sex_bool=FALSE THEN 1 END) AS above_20_f
                                                FROM (SELECT t1.rm_sctn_sbjct_assgnmnt_id AS rssaid,
                                                            date_part('YEAR',  AGE(t1.birthdate)) AS age_gap,
                                                            t1.sex_bool
                                                        FROM sy$sy.bs_view_enrollment t1
                                                        WHERE t1.schl_yr_id=1 AND t1.status_id=5) t1
                                                        GROUP BY t1.rssaid,t1.age_gap,t1.sex_bool) t2 ON t1.rm_sctn_sbjct_assgnmnt_id = t2.rssaid
                                    WHERE t1.schl_yr_id = 1
                                    GROUP BY t1.rm_sctn_sbjct_assgnmnt_id,t1.sctn_nm,t1.grade,t1.order_by
                                    ORDER BY t1.order_by,t1.sctn_nm) t1
                                    LEFT JOIN (SELECT t1.rssaid, SUM(t1.four_ps_m) AS four_ps_m,SUM(t1.four_ps_f) AS four_ps_f FROM(
                                                    SELECT t1.rm_sctn_sbjct_assgnmnt_id AS rssaid,
                                                        SUM(CASE WHEN t1.four_ps = TRUE AND t1.sex_bool=TRUE THEN 1 ELSE 0 END) four_ps_m,
                                                        SUM(CASE WHEN t1.four_ps = TRUE AND t1.sex_bool=FALSE THEN 1 ELSE 0 END) four_ps_f
                                                    FROM sy$sy.bs_view_enrollment t1
                                                    WHERE t1.schl_yr_id=1 AND t1.status_id=5
                                                    GROUP BY t1.rm_sctn_sbjct_assgnmnt_id,t1.sex)t1    
                                                GROUP BY t1.rssaid) t2 ON t1.rssaid = t2.rssaid");

        foreach ($query->result() as $key => $value) {
            $total = [
                "<center><b>TOTAL</b></center>",
                "<b>" . number_format($tb11_m) . "</b>",
                "<b>" . number_format($tb11_f) . "</b>",
                "<b>" . number_format($t11_m) . "</b>",
                "<b>" . number_format($t11_f) . "</b>",
                "<b>" . number_format($t12_m) . "</b>",
                "<b>" . number_format($t12_f) . "</b>",
                "<b>" . number_format($t13_m) . "</b>",
                "<b>" . number_format($t13_f) . "</b>",
                "<b>" . number_format($t14_m) . "</b>",
                "<b>" . number_format($t14_f) . "</b>",
                "<b>" . number_format($t15_m) . "</b>",
                "<b>" . number_format($t15_f) . "</b>",
                "<b>" . number_format($t16_m) . "</b>",
                "<b>" . number_format($t16_f) . "</b>",
                "<b>" . number_format($t17_m) . "</b>",
                "<b>" . number_format($t17_f) . "</b>",
                "<b>" . number_format($t18_m) . "</b>",
                "<b>" . number_format($t18_f) . "</b>",
                "<b>" . number_format($t19_m) . "</b>",
                "<b>" . number_format($t19_f) . "</b>",
                "<b>" . number_format($t20_m) . "</b>",
                "<b>" . number_format($t20_f) . "</b>",
                "<b>" . number_format($total_m) . "</b>",
                "<b>" . number_format($total_f) . "</b>",
                "<b>" . number_format($t4p_m) . "</b>",
                "<b>" . number_format($t4p_f) . "</b>",
            ];

            $t_m = $value->below_11_m + $value->age_11_m + $value->age_12_m + $value->age_13_m + $value->age_14_m + $value->age_15_m + $value->age_16_m + $value->age_17_m + $value->age_18_m + $value->age_19_m + $value->above_20_m;
            $t_f = $value->below_11_f + $value->age_11_f + $value->age_12_f + $value->age_13_f + $value->age_14_f + $value->age_15_f + $value->age_16_f + $value->age_17_f + $value->age_18_f + $value->age_19_f + $value->above_20_f;
            if (($tmpG != "") && ($tmpG != $value->grade)) {

                $data["data"][] = $total;

                $tb11_m = 0;
                $tb11_f = 0;
                $t11_m = 0;
                $t11_f = 0;
                $t12_m = 0;
                $t12_f = 0;
                $t13_m = 0;
                $t13_f = 0;
                $t14_m = 0;
                $t14_f = 0;
                $t15_m = 0;
                $t15_f = 0;
                $t16_m = 0;
                $t16_f = 0;
                $t17_m = 0;
                $t17_f = 0;
                $t18_m = 0;
                $t18_f = 0;
                $t19_m = 0;
                $t19_f = 0;
                $t20_m = 0;
                $t20_f = 0;
                $total_m = 0;
                $total_f = 0;
                $t4p_m = 0;
                $t4p_f = 0;
                $data["data"][] = $empty;
            }

            if ($tmpG != $value->grade) {
                $data["data"][] = [
                    "<center><b>GRADE - " . $value->grade . "</b></center>", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                ];
            }
            $data["data"][] = [
                '<p class="mb-n2 p-0 mt-n2" align="left">' . $value->sctn_nm . '</p>',
                $value->below_11_m,
                $value->below_11_f,
                $value->age_11_m,
                $value->age_11_f,
                $value->age_12_m,
                $value->age_12_f,
                $value->age_13_m,
                $value->age_13_f,
                $value->age_14_m,
                $value->age_14_f,
                $value->age_15_m,
                $value->age_15_f,
                $value->age_16_m,
                $value->age_16_f,
                $value->age_17_m,
                $value->age_17_f,
                $value->age_18_m,
                $value->age_18_f,
                $value->age_19_m,
                $value->age_19_f,
                $value->above_20_m,
                $value->above_20_f,
                $t_m,
                $t_f,
                $value->four_ps_m,
                $value->four_ps_f,
            ];
            $tb11_m += (int) $value->below_11_m;
            $tb11_f += (int) $value->below_11_f;
            $t11_m += (int) $value->age_11_m;
            $t11_f += (int) $value->age_11_f;
            $t12_m += (int) $value->age_12_m;
            $t12_f += (int) $value->age_12_f;
            $t13_m += (int) $value->age_13_m;
            $t13_f += (int) $value->age_13_f;
            $t14_m += (int) $value->age_14_m;
            $t14_f += (int) $value->age_14_f;
            $t15_m += (int) $value->age_15_m;
            $t15_f += (int) $value->age_15_f;
            $t16_m += (int) $value->age_16_m;
            $t16_f += (int) $value->age_16_f;
            $t17_m += (int) $value->age_17_m;
            $t17_f += (int) $value->age_17_f;
            $t18_m += (int) $value->age_18_m;
            $t18_f += (int) $value->age_18_f;
            $t19_m += (int) $value->age_19_m;
            $t19_f += (int) $value->age_19_f;
            $t20_m += (int) $value->above_20_m;
            $t20_f += (int) $value->above_20_f;
            $total_m += (int) $t_m;
            $total_f += (int) $t_f;
            $t4p_m += (int) $value->four_ps_m;
            $t4p_f += (int) $value->four_ps_f;


            $stb11_m += (int) $value->below_11_m;
            $stb11_f += (int) $value->below_11_f;
            $st11_m += (int) $value->age_11_m;
            $st11_f += (int) $value->age_11_f;
            $st12_m += (int) $value->age_12_m;
            $st12_f += (int) $value->age_12_f;
            $st13_m += (int) $value->age_13_m;
            $st13_f += (int) $value->age_13_f;
            $st14_m += (int) $value->age_14_m;
            $st14_f += (int) $value->age_14_f;
            $st15_m += (int) $value->age_15_m;
            $st15_f += (int) $value->age_15_f;
            $st16_m += (int) $value->age_16_m;
            $st16_f += (int) $value->age_16_f;
            $st17_m += (int) $value->age_17_m;
            $st17_f += (int) $value->age_17_f;
            $st18_m += (int) $value->age_18_m;
            $st18_f += (int) $value->age_18_f;
            $st19_m += (int) $value->age_19_m;
            $st19_f += (int) $value->age_19_f;
            $st20_m += (int) $value->above_20_m;
            $st20_f += (int) $value->above_20_f;
            $stotal_m += (int) $t_m;
            $stotal_f += (int) $t_f;
            $st4p_m += (int) $value->four_ps_m;
            $st4p_f += (int) $value->four_ps_f;

            $tmpG = $value->grade;
        }

        $data["data"][] = $total;
        $data["data"][] = $empty;
        $data["data"][] = [
            "<center><b>GRAND TOTAL</b></center>",
            "<b>" . number_format($stb11_m) . "</b>",
            "<b>" . number_format($stb11_f) . "</b>",
            "<b>" . number_format($st11_m) . "</b>",
            "<b>" . number_format($st11_f) . "</b>",
            "<b>" . number_format($st12_m) . "</b>",
            "<b>" . number_format($st12_f) . "</b>",
            "<b>" . number_format($st13_m) . "</b>",
            "<b>" . number_format($st13_f) . "</b>",
            "<b>" . number_format($st14_m) . "</b>",
            "<b>" . number_format($st14_f) . "</b>",
            "<b>" . number_format($st15_m) . "</b>",
            "<b>" . number_format($st15_f) . "</b>",
            "<b>" . number_format($st16_m) . "</b>",
            "<b>" . number_format($st16_f) . "</b>",
            "<b>" . number_format($st17_m) . "</b>",
            "<b>" . number_format($st17_f) . "</b>",
            "<b>" . number_format($st18_m) . "</b>",
            "<b>" . number_format($st18_f) . "</b>",
            "<b>" . number_format($st19_m) . "</b>",
            "<b>" . number_format($st19_f) . "</b>",
            "<b>" . number_format($st20_m) . "</b>",
            "<b>" . number_format($st20_f) . "</b>",
            "<b>" . number_format($stotal_m) . "</b>",
            "<b>" . number_format($stotal_f) . "</b>",
            "<b>" . number_format($st4p_m) . "</b>",
            "<b>" . number_format($st4p_f) . "</b>",
        ];

        echo json_encode($data);
    }
}
