<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->redirect();
    }

    public function index()
    {
        $page_data = $this->system();
        $uri = $this->session->schoolmis_login_uri;
        $page_data += [
            "page_title"        => "Dashboard",
            "current_location"  => "dashboard",
            "content"           =>  [$this->load->view('interface/' . $uri . '/Dashboard', [
                "getOnLoad" => $this->getOnLoad(),
                "dashboard" => $this->getDashboard(),
                // "getYearMonth" => $this->getDateYearMonth(),
                //"useraccount"      => $this->get_useraccount(),
                //"pending"      => $this->get_pending(),
                //"documents"      => $this->get_documents(),
            ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }


            // 1444  160201000 160201000 Butuan
            // 1455  160212000 160212000 Remedios
            // 1453  160210000 160210000 Santiago
            // 1451  160208000 160208000 Magallanes
            // 1454  160211000 160211000 Tubay
            // 1445  160202000 160202000 Buenavista
            // 1446  160203000 160203000 City
            // 1447  160204000 160204000 Carmen
            // 1448  160205000 160205000 Jabonga
            // 1449  160206000 160206000 Kitcharao
            // 1450  160207000 160207000 Las
            // 1452  160209000 160209000 Nasipit


    function getDashboard()
    {
        $sy = $this->getOnLoad()["sy_id"];
        $q_learner = $this->db->query("SELECT count(1) AS cc FROM sy$sy.bs_tbl_learner_enrollment t1 WHERE t1.status_id=5");
        $q_teaching = $this->db->query("SELECT count(1) AS cc FROM profile.tbl_schoolpersonnel t1 WHERE t1.employee_type_id=4 AND t1.is_active=1");
        $q_nteaching = $this->db->query("SELECT count(1) AS cc FROM profile.tbl_schoolpersonnel t1 WHERE t1.employee_type_id=5 AND t1.is_active=1");
        $q_scanned = $this->db->query("SELECT count(1) AS cc, to_char(date,'mm-dd-yyyy') scanned_date FROM logs.tbl_scan_logs$sy t1
                                        WHERE to_char(date,'mm-dd-yyyy') = to_char(now(),'mm-dd-yyyy')
                                        GROUP BY to_char(date,'mm-dd-yyyy')");

        $data = [
            "learner" => number_format($q_learner->row()->cc, 0),
            "teaching" => number_format($q_teaching->row()->cc, 0),
            "nteaching" => number_format($q_nteaching->row()->cc, 0),
            "scanned" => number_format(($q_scanned->num_rows() > 0 ? $q_scanned->row()->cc : 0), 0),
        ];
        return $data;
    }

    function getMapPlot()
    {
        $sy = $this->getOnLoad()["sy_id"];
        $cm = $this->input->get("cc");
        $data = ["data_map" => []];
        $query = $this->db->query("SELECT
                                            t1.barangay_id AS barangay,
                                            SUM(CASE WHEN t1.sex_bool = TRUE  THEN 1 ELSE 0 END) AS male,
                                            SUM(CASE WHEN t1.sex_bool = FALSE THEN 1 ELSE 0 END) AS female,
                                            SUM(1) AS total
                                        FROM
                                            sy$sy.bs_view_enrollment t1
                                            -- JOIN address.tbl_barangay t2 ON t1.barangay_id = t2.id
                                        WHERE
                                            t1.status_id = 5 AND CONCAT(t1.citymun_id,'000')='$cm' 
                                        GROUP BY
                                            t1.barangay_id;");

        foreach ($query->result() as $row) {
            $data["data_map"][] = [$row->barangay, [intval($row->total)], ['m', [intval($row->male)]], ['f', [intval($row->female)]]];
            $data["data_sex_m"][$row->barangay] = [intval($row->male)];
            $data["data_sex_f"][$row->barangay] = [intval($row->female)];
        }

        echo json_encode($data);
    }

    function getMapPlotCityMun()
    {
        $sy = $this->getOnLoad()["sy_id"];
        $data = ["data_map" => []];
        $query = $this->db->query("SELECT t2.code  AS city_mun,
                                        SUM(CASE WHEN t1.sex_bool = TRUE  THEN 1 ELSE 0 END) AS male,
                                        SUM(CASE WHEN t1.sex_bool = FALSE THEN 1 ELSE 0 END) AS female,
                                        SUM(1) AS total
                                    FROM
                                        sy$sy.bs_view_enrollment t1
                                        JOIN address.tbl_citymun t2 ON t1.citymun_id = t2.id
                                    WHERE
                                        t1.status_id = 5
                                    GROUP BY
                                        t1.citymun_id, t2.code;");

        foreach ($query->result() as $row) {
            $data["data_map"][] = [$row->city_mun, [intval($row->total)]];
            $data["data_sex_m"][$row->city_mun] = [intval($row->male)];
            $data["data_sex_f"][$row->city_mun] = [intval($row->female)];
        }

        echo json_encode($data);
    }

    function getPopulationLearner()
    {
        $sy = $this->getOnLoad()["sy_id"];
        $query = $this->db->query("SELECT
                                        age_bracket,
                                        SUM(CASE WHEN sex = True THEN 1 ELSE 0 END) * -1 AS male_count,
                                        SUM(CASE WHEN sex = False THEN 1 ELSE 0 END) AS female_count
                                    FROM
                                        (
                                            SELECT
                                                t1.sex_bool AS sex,
                                                CASE
                                                    WHEN EXTRACT(YEAR FROM age(t1.birthdate)) <= 13 THEN '13↓'
                                                    WHEN EXTRACT(YEAR FROM age(t1.birthdate)) = 14 THEN '14'
                                                    WHEN EXTRACT(YEAR FROM age(t1.birthdate)) = 15 THEN '15'
                                                    WHEN EXTRACT(YEAR FROM age(t1.birthdate)) = 16 THEN '16'
                                                    WHEN EXTRACT(YEAR FROM age(t1.birthdate)) = 17 THEN '17'
                                                    WHEN EXTRACT(YEAR FROM age(t1.birthdate)) = 18 THEN '18'
                                                    WHEN EXTRACT(YEAR FROM age(t1.birthdate)) = 19 THEN '19'
                                                    WHEN EXTRACT(YEAR FROM age(t1.birthdate)) = 20 THEN '20'
                                                    WHEN EXTRACT(YEAR FROM age(t1.birthdate)) = 21 THEN '21'
                                                    WHEN EXTRACT(YEAR FROM age(t1.birthdate)) >= 22 THEN '22↑'
                                                END AS age_bracket
                                            FROM
                                                sy$sy.bs_view_enrollment t1
                                        ) AS subquery
                                    WHERE age_bracket >= '13↓' AND age_bracket <= '26↑'
                                    GROUP BY
                                        age_bracket
                                    ORDER BY
                                        CASE
                                            WHEN age_bracket = '13↓' THEN 1
                                            WHEN age_bracket = '14' THEN 2
                                            WHEN age_bracket = '15' THEN 3
                                            WHEN age_bracket = '16' THEN 4
                                            WHEN age_bracket = '17' THEN 5
                                            WHEN age_bracket = '18' THEN 6
                                            WHEN age_bracket = '19' THEN 7
                                            WHEN age_bracket = '20' THEN 8
                                            WHEN age_bracket = '21' THEN 9
                                            ELSE 10
                                        END;");

        foreach ($query->result() as $row) {
            $data["data_age"][] = [$row->age_bracket];
            $data["data_sex_m"][] = intval($row->male_count);
            $data["data_sex_f"][] = intval($row->female_count);
        }

        echo json_encode($data);
    }
}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */