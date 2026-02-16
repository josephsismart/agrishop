<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

    public $global_requestid = null;
    public $global_requestid_personnel = null;

    public function system()
    {
        $data = [
            "system_title"  => "AgriShop",
            // "system_title"  => "Agusan National High School",
            "system_logo"   => base_url("dist/layout_shop/img/media/icons/icon.png"),
            "system_svg"    => base_url("dist/layout_shop/images/logo1.svg"),
            "system_op"    => base_url("dist/img/media/icons/icon_op.png"),
            "system_svg_1x1"    => base_url("dist/img/media/icons/1x1.png"),
            "system_bg_l_front_id"    => base_url("dist/img/media/bg/id/2024-2025/learner/v2/v2_front.png"),
            "system_bg_l_back_id"    => base_url("dist/img/media/bg/id/2024-2025/learner/v2/v2_back.png"),
            "system_bg_nt_front_id"    => base_url("dist/img/media/bg/id/2024-2025/non-teaching/v1/v1_front.png"),
            "system_bg_nt_back_id"    => base_url("dist/img/media/bg/id/2024-2025/non-teaching/v1/v1_back.png"),
            "system_bg_t_front_id"    => base_url("dist/img/media/bg/id/2024-2025/teaching/v1/v1_front.png"),
            "system_bg_t_back_id"    => base_url("dist/img/media/bg/id/2024-2025/teaching/v1/v1_back.png"),
            "system_bg_v_front_id"    => base_url("dist/img/media/bg/id/2024-2025/visitor/v1/v1_front.png"),
            "system_bg_v_back_id"    => base_url("dist/img/media/bg/id/2024-2025/visitor/v1/v1_back.png"),
            "system_esig"    => base_url("dist/img/media/esig/roa.png"),

            "system_deped_1x1"    => base_url("dist/img/media/icons/deped_1x1.png"),
            "system_depeddiv_1x1"    => base_url("dist/img/media/icons/depeddiv_1x1.png"),

        ];
        return $data;
    }

    public function public_create_page($data = [])
    {
        $level = $this->session->agrishop_login_level;
        $defaultPassword = $this->session->agrishop_change_password;
        $uri = $this->session->agrishop_login_uri;
        if ($level != "") {
            if ($defaultPassword == 't') {
                return $this->load->view('interface/userpassword/layout/Page', $data, false);
            } else {
                return $this->load->view('interface/' . $uri . '/layout/Page', $data, false);
            }
        }
    }

    public function user_create_page($data = [])
    {
        return $this->load->view('interface/user/layout/Page', $data, false);
    }

    public function redirect()
    {
        $this->check_subscription();
        $login = $this->session->agrishop_login_id;
        $defaultPassword = $this->session->agrishop_change_password;
        $uri = $this->session->agrishop_login_uri;
        $landing = $this->session->agrishop_login_landing;
        if (!$login) {
            redirect(base_url('/'));
        }
        if (isset($login) && $this->uri->segment(1) != $uri) {
            if ($defaultPassword == 1) {
                redirect(base_url('userpassword/changepassword'));
            } else {
                redirect(base_url($uri . '/' . $landing));
            }
        }
    }
    public function redirect2()
    {
        $login = $this->session->agrishop_login_id;
        if (!$login) {
            redirect(base_url('/'));
        }
    }

    public function redirect_home()
    {
        $this->check_subscription();
        $level = $this->session->agrishop_login_level;
        $defaultPassword = $this->session->agrishop_change_password;
        $uri = $this->session->agrishop_login_uri;
        $landing = $this->session->agrishop_login_landing;
        // if (isset($this->session->agrishop_login_id) && $this->uri->segment(1) == "" || $this->uri->segment(1) == "login" || $this->uri->segment(1) == "map") {
        if (isset($this->session->agrishop_login_id) && $this->uri->segment(1) == "" || $this->uri->segment(1) == "login" || $this->uri->segment(1) == "map") {
            if ($level != "") {
                if ($uri == "userconsumer") {
                    redirect(base_url('index'));
                } else {
                    redirect(base_url($uri . '/' . $landing));
                }
            }
        }
    }

    public function check_qty_left($farm_produce_id, $qty){
        $check_qty_left = $this->db->query("SELECT pql.qty_left, name FROM price_qty_left pql
                                            LEFT JOIN produce p on pql.produce_id = p.id
                                            WHERE pql.id = $farm_produce_id LIMIT 1")->row();
        if ($check_qty_left->qty_left < $qty) {
            $false = ["success"   => false, "message" => "Quantity for " . $check_qty_left->name . " left is $check_qty_left->qty_left, not enough!"];
            echo json_encode($false);
            return;
        }
    }

    public function check_subscription()
    {
        $farmer_id = $this->session->agrishop_login_farmer_id;
        if (!$farmer_id) return;

        /* 1️⃣ WAITING FOR ADMIN VALIDATION */
        $approved_active = $this->db->query("
        SELECT 1
        FROM farmer_subscription
        WHERE farmer_id = ?
        AND is_latest = true
        AND is_active = true
        AND is_expired = false
        LIMIT 1
    ", [$farmer_id])->row();

        if ($approved_active) {
            $this->session->set_userdata([
                "agrishop_login_uri" => "userfarmer",
                "agrishop_login_landing" => "dashboard"
            ]);
            return;
        }

        /* 1️⃣ WAITING FOR ADMIN VALIDATION */
        $pending = $this->db->query("
        SELECT 1
        FROM farmer_subscription_application
        WHERE farmer_id = ?
        AND checked_at IS NULL
        LIMIT 1
    ", [$farmer_id])->row();

        if ($pending) {
            $this->session->set_userdata([
                "agrishop_login_uri" => "ud440aed188v",
                "agrishop_login_landing" => "validation"
            ]);
            return;
        }

        /* 2️⃣ ACTIVE PAID SUBSCRIPTION */
        $paid = $this->db->query("
        SELECT *
        FROM farmer_subscription
        WHERE farmer_id = ?
        AND is_latest = true
        AND is_active = true
        AND is_expired = false
        AND end_date >= CURRENT_DATE
        LIMIT 1
    ", [$farmer_id])->row();

        if ($paid) {
            return; // ✅ allow normal routing
        }

        /* 3️⃣ PAID SUB EXPIRED */
        $paid_expired = $this->db->query("
        SELECT *
        FROM farmer_subscription
        WHERE farmer_id = ?
        AND is_latest = true
        AND (is_expired = true OR end_date < CURRENT_DATE)
        LIMIT 1
    ", [$farmer_id])->row();

        if ($paid_expired) {
            $this->session->set_userdata([
                "agrishop_login_uri" => "ud440aed189",
                "agrishop_login_landing" => "subscribe"
            ]);
            return;
        }

        /* 4️⃣ FREE SUBSCRIPTION */
        $free = $this->db->query("
        SELECT *
        FROM farmer_subscription_free
        WHERE farmer_id = ?
        LIMIT 1
    ", [$farmer_id])->row();

        if ($free) {
            if (date('Y-m-d') > $free->ended_at) {
                $this->db->query("
                UPDATE farmer_subscription_free
                SET is_expired = true
                WHERE farmer_id = ?
            ", [$farmer_id]);

                $this->session->set_userdata([
                    "agrishop_login_uri" => "ud440aed189",
                    "agrishop_login_landing" => "subscribe"
                ]);
            }
            return;
        }

        /* 5️⃣ NO SUBSCRIPTION AT ALL */
        $this->session->set_userdata([
            "agrishop_login_uri" => "ud440aed189",
            "agrishop_login_landing" => "subscribe"
        ]);
    }

    // public function check_subscription()
    // {
    //     $farmer_id = $this->session->agrishop_login_farmer_id;
    //     $data = $this->db->query("SELECT * FROM farmer_subscription_application WHERE farmer_id = " . $farmer_id . " AND checked_at IS NULL")->row();

    //     if ($data) {
    //         $data_session = [
    //             "agrishop_login_uri" => "ud440aed188v",
    //             "agrishop_login_landing" => "Validation"
    //         ];
    //         $this->session->set_userdata($data_session);
    //     }

    //     #check farmer subscription
    //     if ($farmer_id) {
    //         $chck2 = $this->db->query("SELECT f.id,TO_CHAR(fsf.ended_at,'yyyy-mm-dd') AS free_end_at, fsf.confirmed AS free_confirmed, fsf.is_expired AS free_expired,
    //                                         fs2.start_date, fs2.end_date,fs2.is_active,fs2.is_expired ,fs2.is_latest, fsa.checked, fsa.checked_at
    //                                         FROM farmer AS f
    //                                         JOIN farmer_subscription_free fsf ON f.id = fsf.farmer_id
    //                                         LEFT JOIN (SELECT * FROM farmer_subscription WHERE is_latest = true) fs2 ON f.id= fs2.farmer_id
    //                                         LEFT JOIN farmer_subscription_application fsa ON fs2.farmer_application_subscription_id = fsa.id
    //                                         WHERE f.id = ?", array($farmer_id));
    //         if ($chck2->num_rows() > 0) {
    //             $row2 = $chck2->row();

    //             if (date('Y-m-d') > $row2->free_end_at && $row2->end_date == null) {
    //                 $this->db->query("UPDATE public.farmer_subscription_free SET is_expired = true WHERE farmer_id = $farmer_id");
    //                 $data += [
    //                     "agrishop_login_sub_free_expired" => 't',
    //                     "agrishop_login_uri" => 'ud440aed189',
    //                     "agrishop_login_landing" => 'subscribe',
    //                 ];
    //             } else if ($row2->is_expired == true) {
    //                 $data += [
    //                     "agrishop_login_sub_free_expired" => 't',
    //                     "agrishop_login_uri" => 'ud440aed189',
    //                     "agrishop_login_landing" => 'subscribe',
    //                 ];
    //             } else {
    //                 $data += [
    //                     "agrishop_login_sub_free_expired" => 'f',
    //                     "agrishop_login_uri"        => ($this->session->agrishop_login_change_pwd == 't' ? "ud440aed189" : ($this->session->agrishop_login_level == 0 ? "useradmin" : ($this->session->agrishop_login_level == 1 ? "userconsumer" : ($this->session->agrishop_login_level == 2 ? "userfarmer" : "")))),
    //                     "agrishop_login_landing"    => $this->session->agrishop_login_change_pwd == 't' ? "changepassword" : ($this->session->agrishop_login_level == 2 ? "dashboard" : "dataentry"),
    //                 ];
    //             }

    //             if ($row2->end_date !== null && date('Y-m-d') >= $row2->end_date) {
    //                 $data += [
    //                     "agrishop_login_uri" => 'ud440aed189',
    //                     "agrishop_login_landing" => 'subscribe',
    //                 ];
    //             }
    //         }
    //     }

    //     // $farmer_id = $this->session->agrishop_login_farmer_id;
    //     // $data = $this->db->query("SELECT * FROM farmer_subscription WHERE farmer_id = " . $farmer_id . " AND is_active = true AND is_latest = true")->row();
    //     // return $data;
    // }



    public function redirect_session()
    {
        $login = $this->session->agrishop_login_id;
        if (!$login) {
            redirect(base_url('/'));
        }
    }

    public function removeCharacter($text)
    {
        return preg_replace("/[^0-9]/", "", $text);
    }

    public function clean($string)
    {
        $string = str_replace(' ', '-', $string);
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    }

    public function cleanQuote($string)
    {
        $string = str_replace('"', '$', $string);  // Replace double quotes with ??
        $string = str_replace("'", '?', $string);   // Replace single quotes with ?
        return $string;
    }

    public function readQuote($string)
    {
        $string = str_replace('$', '"', $string);  // Replace double quotes with ??
        $string = str_replace('?', "'", $string);   // Replace single quotes with ?
        return $string;
    }

    public function returnNull($a)
    {
        $return = !$a ? NULL : $a;
        return $return;
    }

    public function returnEmptyArr($a)
    {
        $return = !$a ? 0 : count($a);
        return $return;
    }

    public function returnZero($a)
    {
        $return = !$a ? 0 : $a;
        return $return;
    }

    public function getAddress($filter)
    {
        $fltr = $filter ? $filter : 0;
        $address = "";
        $query = $this->db->query("SELECT 
                                        t1.id,
                                        UPPER(CONCAT(
                                            t1.description, ' ',
                                            t2.description, ', ',
                                            t3.description, ', ',
                                            t4.region
                                        )) AS address
                                    FROM tbl_barangay t1
                                    LEFT JOIN tbl_citymun t2 ON t1.citymun_id = t2.id
                                    LEFT JOIN tbl_province t3 ON t2.province_id = t3.id
                                    LEFT JOIN tbl_region t4 ON t3.region_id = t4.id
                                    WHERE t1.id=$fltr");
        if ($query->num_rows() > 0) {
            $address = $query->row()->address;
        }
        return $address;
    }

    public function getPersonName($filter)
    {
        $fltr = $filter ? $filter : 0;
        $name = "";
        $query = $this->db->query("SELECT * FROM person t1 WHERE t1.id=$fltr");
        if ($query->num_rows() > 0) {
            $mname = $query->row()->middle_name != "" ? " " . substr($query->row()->middle_name, 0, 1) . ". " : " ";
            $name = $query->row()->first_name . $mname . $query->row()->last_name;
        }
        return $name;
    }

    public function getTransactionPeding($id, $status, $type)
    {
        if ($type == 'farmer') {
            $FILTR = "t4.farmer_id=$id";
        }
        // else ($type == 'client') {
        else {
            $FILTR = "t1.person_id=$id";
        }

        $thisQuery = $this->db->query("SELECT count(1) AS total FROM transaction t1 
                                    JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
                                    LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce mcfp
                                                GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                                    LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                                    WHERE $FILTR AND t2.status = '$status'");
        $c = $thisQuery->row()->total;
        $cc = $c > 0 ? $c : '';
        if ($type == 'farmer') {
            $this->session->agrishop_reserved_trans_count = $cc;
        } else {
            $this->session->agrishop_pending_trans_count = $cc;
        }

        return $cc;
    }

    public function format_price($amount)
    {
        return number_format((float)$amount, 2, '.', ',');
    }

    public function generate_gcash_qr($amount = 0, $gcash_number = '', $merchant_name = '', $reference = '')
    {
        // Set defaults if empty
        if (empty($gcash_number)) {
            $gcash_number = '09171234567'; // Your default GCash number
        }
        if (empty($merchant_name)) {
            $merchant_name = 'My Business';
        }
        if (empty($reference)) {
            $reference = 'INV' . date('YmdHis') . rand(100, 999);
        }

        // Format amount to 2 decimal places
        $formatted_amount = number_format((float)$amount, 2, '.', '');

        /**
         * Option 1: GCash Deep Link (EASIEST - opens GCash directly)
         * Format: gcash://pay?pa=GCASH_NUMBER&pn=MERCHANT&am=AMOUNT
         */
        $qr_data = "gcash://pay?" . http_build_query([
            'pa' => $gcash_number,
            'pn' => $merchant_name,
            'am' => $formatted_amount,
            'tid' => $reference
        ]);

        /**
         * Option 2: Simple amount only (User inputs recipient)
         * $qr_data = "gcash://scan?amount=" . $formatted_amount;
         */

        // Generate QR code URL using api.qrserver.com
        $qr_size = 300; // QR code size in pixels
        $qr_url = "https://api.qrserver.com/v1/create-qr-code/";
        $qr_url .= "?size=" . $qr_size . "x" . $qr_size;
        $qr_url .= "&data=" . urlencode($qr_data);
        $qr_url .= "&format=png";
        $qr_url .= "&margin=10";
        $qr_url .= "&color=000000";
        $qr_url .= "&bgcolor=FFFFFF";
        $qr_url .= "&qzone=1";

        // Return everything you need
        return [
            'qr_url' => $qr_url,          // Direct URL to QR image
            'qr_data' => $qr_data,        // The encoded data
            'amount' => $formatted_amount,
            'merchant_name' => $merchant_name,
            'gcash_number' => $gcash_number,
            'reference' => $reference,
            'instructions' => "1. Open GCash app\n2. Tap 'Scan QR'\n3. Scan this code\n4. Confirm payment"
        ];
    }

    public function getAddress2($filter)
    {
        $fltr = $filter ? $filter : 0;
        $address = "";
        $query = $this->db->query("SELECT 
                                        t1.id,
                                        UPPER(CONCAT(
                                            t1.description, ' ',
                                            t2.description, ', ',
                                            t3.description, ', ',
                                            t4.region
                                        )) AS address
                                    FROM tbl_barangay_2 t1
                                    JOIN tbl_citymun t2 ON t1.adm3_psgc = t2.ref_id
                                    JOIN tbl_province t3 ON t2.province_id = t3.id
                                    JOIN tbl_region t4 ON t3.region_id = t4.id AND t4.is_active = true
                                    WHERE t1.id=$fltr");
        if ($query->num_rows() > 0) {
            $address = $query->row()->address;
        }
        return $address;
    }

    public function allow_schema()
    {
        $this->db->query("GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA account TO xnyiyspvjvppjz;

                            GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA address TO xnyiyspvjvppjz;
                            
                            GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA building_sectioning TO xnyiyspvjvppjz;
                            
                            GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA global TO xnyiyspvjvppjz;
                            
                            GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA profile TO xnyiyspvjvppjz;
                            
                            GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO xnyiyspvjvppjz;");


        // $this->db->query("GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA account TO xnyiyspvjvppjz;

        // GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA address TO xnyiyspvjvppjz;

        // GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA building_sectioning TO xnyiyspvjvppjz;

        // GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA global TO xnyiyspvjvppjz;

        // GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA profile TO xnyiyspvjvppjz;

        // GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO xnyiyspvjvppjz;");
    }

    public function PartyList($filter)
    {
        $data = ["data" => []];
        $thisQuery = $this->db->query("SELECT * FROM global.tbl_party t1 
                                        WHERE t1.party_type_id=$filter 
                                        AND t1.is_active=true
                                        ORDER BY t1.order_by");
        foreach ($thisQuery->result() as $key => $value) {
            $data["data"][] = [
                "id" => $value->id,
                "item" => $value->description,
            ];
        }
        return $data;
    }

    public function PartyTypeList($filter)
    {
        $data = ["data" => []];
        $thisQuery = $this->db->query("SELECT * FROM global.tbl_partytype t1 
                                        WHERE t1.group_id=$filter 
                                        AND t1.is_active=true
                                        ORDER BY t1.order_by");
        foreach ($thisQuery->result() as $key => $value) {
            $data["data"][] = [
                "id" => $value->id,
                "item" => $value->description,
            ];
        }
        return $data;
    }

    public function SchoolPersonnelList($filter)
    {
        $w = $filter ? "WHERE t1.employeeTypeId=$filter" : "";
        $data = ["data" => []];
        $thisQuery = $this->db->query("SELECT * FROM profile.view_schoolpersonnel t1 $w ORDER BY t1.first_name");
        foreach ($thisQuery->result() as $key => $value) {
            $data["data"][] = [
                "id" => $value->schoolpersonnel_id,
                "item" => $value->full_name,
            ];
        }
        return $data;
    }

    public function StatusList($filter)
    {
        $data = ["data" => []];
        $thisQuery = $this->db->query("SELECT * FROM global.tbl_status t1 
                                        WHERE t1.status_type_id=$filter 
                                        AND t1.is_active=true
                                        ORDER BY t1.order_by");
        foreach ($thisQuery->result() as $key => $value) {
            $data["data"][] = [
                "id" => $value->id,
                "item" => $value->description,
            ];
        }
        return $data;
    }

    public function GradesSectionList($filter)
    {
        $data = ["data" => []];
        $thisQuery = $this->db->query("SELECT * FROM global.tbl_status t1 
                                        WHERE t1.status_type_id=$filter 
                                        AND t1.is_active=true
                                        ORDER BY t1.order_by");
        foreach ($thisQuery->result() as $key => $value) {
            $data["data"][] = [
                "id" => $value->id,
                "item" => $value->description,
            ];
        }
        return $data;
    }

    public function getOnLoad()
    {
        $query = $this->db->query("SELECT * FROM global.tbl_sy t1 WHERE t1.is_active=true");
        $row = $query->row();
        $sy_id = $row->id;
        $sy = $row->description;
        $qrtr = $row->qrtr;
        $enroll_stat = $row->enrollment_stat;
        $enroll_dl = $row->enrollment_deadline;
        $grade_stat = $row->grading_stat;
        $grade_dl = $row->grading_deadline;
        $edit = $row->edit_student;
        $unenroll = $row->unenroll;
        $v_grades = $row->view_grades;
        $v_grades_date = $row->view_grades_until;
        $input_grades_qrtr = $row->input_grades_qrtr;
        $qrtrR = $qrtr == 1 ? "1st" : ($qrtr == 2 ? "2nd" : ($qrtr == 3 ? "3rd" : ($qrtr == 4 ? "4th" : "--")));
        $edl = "";
        $edl1 = "";
        $gdl = "";
        $gdl1 = "";
        $vgd = "";
        if ($enroll_dl) {
            $edl = $this->dateFormat($enroll_dl);
            $edl1 = "<br/>" . $edl;
        }
        if ($grade_dl) {
            $gdl = $this->dateFormat($grade_dl);
            $gdl1 = "<br/>" . $gdl;
        }
        if ($v_grades == 't') {
            $vgd = $this->dateFormat($v_grades_date);
        }

        $data = [
            "sy_id" => $sy_id,
            "sy" => $sy,
            "qrtr" => $qrtr,
            "qrtrR" => $qrtrR,
            "enroll_stat" => $enroll_stat,
            "enroll_dl" => $enroll_dl,
            "grade_stat" => $grade_stat,
            "grade_dl" => $grade_dl,
            "edl" => $edl1,
            "gdl" => $gdl1,
            "edit" => $edit,
            "unenroll" => $unenroll,
            "v_grades" => $v_grades,
            "vgd" => $vgd,
            "input_grades_qrtr" => $input_grades_qrtr,
            // "sy_qrtr_e_g" => "<b>SY:</b> " . $sy . " | <b>Q:</b> " . $qrtrR,
            "sy_qrtr_e_g" => "<b>SY:</b> " . $sy . " | <b>Q:</b> |" . $qrtrR .
                "<div class='d-none d-sm-block d-lg-block'>" .
                ($enroll_stat == 't' ? " <small class='text-success text-bold' style='white-space: nowrap;'><b>ENR: </b>" . $edl . "</small>" : "") .
                ($grade_stat == 't' ? " | <small class='text-success text-bold' style='white-space: nowrap;'><b>GRD: </b>" . $gdl . "</small>" : "") .
                "</div>",
        ];
        return $data;
    }

    public function getSHdboard()
    {
        $sy = $this->getOnLoad()["sy_id"];
        $query = $this->db->query("SELECT SUM(CASE WHEN t1.sex_bool=TRUE THEN 1 END) AS male,
                                    SUM(CASE WHEN t1.sex_bool=FALSE THEN 1 END) AS female
                                    FROM sy$sy.bs_view_enrollment t1
                                    WHERE t1.status_id=5");

        $query1 = $this->db->query("SELECT SUM(CASE WHEN t1.sex_bool=TRUE THEN 1 END) AS male,
                                    SUM(CASE WHEN t1.sex_bool=FALSE THEN 1 END) AS female
                                    FROM profile.view_schoolpersonnel t1
                                    WHERE t1.person_id != 1197 
                                    AND t1.person_id != 1 
                                    AND t1.person_id != 1431 
                                    AND t1.person_id != 1102 
                                    -- AND t1.is_active=TRUE
                                    AND t1.is_active_schl_personnel>0");

        $query2 = $this->db->query("SELECT t1.role_id,t1.user_description, COUNT(1) AS cc FROM account.view_useraccount t1
                                    GROUP BY t1.user_description,t1.role_id");

        $row = $query->row();
        $row1 = $query1->row();
        $emale =  number_format($row->male);
        $efmale =  number_format($row->female);
        $tenroll =  number_format($row->male + $row->female);

        $tpmale =  number_format($row1->male);
        $tpfemale =  number_format($row1->female);
        $ttpenroll =  number_format($row1->male + $row1->female);

        foreach ($query2->result() as $key => $value) {
            $r = $value->role_id;
            if ($r == 3) {
                $dephead = (int) $value->cc;
            }
            if ($r == 7) {
                $teacher = (int) $value->cc;
            }
            if ($r == 8) {
                $learner = (int) $value->cc;
            }
        }

        $data = [
            "emale" => $emale,
            "efmale" => $efmale,
            "tenroll" => $tenroll,

            "tpmale" => $tpmale,
            "tpfemale" => $tpfemale,
            "ttpenroll" => $ttpenroll,

            "dephead" => $dephead,
            "teacher" => $teacher,
            "learner" => $learner,
        ];
        return $data;
    }

    public function filterAndFormatDate($date)
    {
        $dateFormats = [
            'm-d-Y',
            'm/d/Y',
            'm-d-y',
            'm/d/y',
            'Y-m-d',
            'Ymd', // For dates like '20030112'
            'Y/m/d',
            'Y.m.d',
        ];

        if (is_numeric($date) && $date >= 1 && $date <= 99999) {
            // Convert $date to date format (assuming it's an Excel date serial)
            $excelDateOrigin = 25569; // Adjust for Excel's date origin (January 1, 1970, in Unix timestamp)
            $unixTimestamp = ($date - $excelDateOrigin) * 86400; // 86400 seconds in a day
            return $birthdate = date('Y-m-d', $unixTimestamp);
        } else {
            foreach ($dateFormats as $format) {
                $z = trim($date);
                $dateTime = DateTime::createFromFormat($format, $z);
                if ($dateTime !== false) {
                    return $birthdate = $dateTime->format('Y-m-d');
                    break; // Exit the loop once a valid format is found
                }
            }
        }

        if ($date === null) {
            // Handle the case where the date format doesn't match either expected format
            // You can add your error handling logic here
            return null;
        }

        // Return null if none of the formats matched
    }

    public function get_ip()
    {
        $ip = "";
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (!empty($_SERVER["HTTP_X_FORWARDED"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED"];
        } elseif (!empty($_SERVER["REMOTE_ADDR"])) {
            $ip = $_SERVER["REMOTE_ADDR"];
        }
        if ($ip == "::1") {
            $ip = "127.0.0.1";
        }
        return $ip;
    }

    public function confirmPassword($a)
    {
        $pwd = md5($a);
        $login_id = $this->session->agrishop_login_id;
        $query = $this->db->query("SELECT 1 AS pwd FROM tbl_user WHERE id=$login_id AND password='$pwd' LIMIT 1");
        return $query->row("pwd");
    }

    public function now()
    {
        date_default_timezone_set("Asia/Manila");
        $now = date("Y-m-d H:i:s");
        return $now;
    }

    public function do_upload($input_name, $upload_path, $file_name)
    {
        $path = "";
        // $num = mt_rand(1, 1000000);

        $config['upload_path']      = $upload_path;
        $config['allowed_types']    = 'pdf|docx|xls|ppt|jpg|png|jpeg|txt';
        $config['max_size']         = '100000';
        $config['overwrite']        = true;
        $config['file_name']        = $file_name;
        // $config['max_width']         = '5000';
        // $config['max_height']        = '5000';

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        $upload = $this->upload->do_upload($input_name);
        if ($upload) {
            $path = $file_name;
        }
        return $path;
    }

    public function userlog($action)
    {
        $login_id = $this->session->agrishop_login_id;
        $login_alias = $this->session->agrishop_login_uname;
        $now = $this->now();
        $action = addslashes($action);
        $ip = $this->get_ip();
        $data = [
            "date" => $now,
            "action" => $action,
            "user_id" => $login_id,
            "user_name" => $login_alias,
            "ip" => $ip,
        ];
        if ($login_id) {
            $this->db->insert("global.tbl_userlogs", $data);
        }
    }

    public function calculatePagination($requestData)
    {
        $limit = isset($requestData['length']) ? intval($requestData['length']) : 10;
        $offset = isset($requestData['start']) ? intval($requestData['start']) : 0;
        return array($limit, $offset);
    }

    public function checkTransactionStatus($transaction_id)
    {
        $query = $this->db->query("SELECT t2.status FROM public.transaction t1
                                    JOIN (SELECT * FROM public.transaction_status WHERE transaction_id=$transaction_id AND is_latest=TRUE) t2 ON t1.id=t2.transaction_id
                                    WHERE t1.id=$transaction_id LIMIT 1");
        return $query->row("status");
    }

    public function checkTransactionDeliveryStatus($transaction_id)
    {
        $query = $this->db->query("SELECT t2.status FROM public.transaction t1
                                    JOIN (SELECT * FROM public.transaction_delivery_status WHERE transaction_id=$transaction_id AND is_latest=TRUE) t2 ON t1.id=t2.transaction_id
                                    WHERE t1.id=$transaction_id LIMIT 1");
        return $query->row("status");
    }

    public function checkTransactionPaymentStatus($transaction_id)
    {
        $query = $this->db->query("SELECT t2.status FROM public.transaction t1
                                    JOIN (SELECT * FROM public.transaction_payment_status WHERE transaction_id=$transaction_id AND is_latest=TRUE) t2 ON t1.id=t2.transaction_id
                                    WHERE t1.id=$transaction_id LIMIT 1");
        return $query->row("status");
    }

    public function statusBadge($status)
    {
        $map = [
            // 🧾 TRANSACTION
            'PENDING'    => 'bg-warning text-dark',
            'RESERVED'   => 'bg-info text-dark',
            'PREPARING'  => 'bg-orange',
            'ORDER_IS_READY'  => 'bg-info',
            'COMPLETED'  => 'bg-success',
            'CANCELLED'  => 'bg-danger',

            // 💳 PAYMENT
            'UNPAID'     => 'bg-secondary',
            'VERIFYING'  => 'bg-info text-dark',
            'PAID'       => 'bg-success',
            'FAILED'     => 'bg-danger',

            // 🚚 DELIVERY
            'TO_PICKUP'  => 'bg-warning text-dark',
            'TO_DELIVER' => 'bg-info',
            'ON_THE_WAY' => 'bg-info text-dark',
            'DELIVERED'  => 'bg-success',
        ];

        $color = $map[$status] ?? 'bg-dark';

        return '<span style="font-size:12px; color:#fff !important;" class="badge ' . $color . '">' . $status . '</span>';
    }

    public function scanlog($x, $type, $scanned_id, $io, $g_name, $g_id)
    {
        $exist = false;
        $query = $this->db->query("SELECT EXISTS (
                                    SELECT 1
                                    FROM pg_tables
                                    WHERE schemaname = 'logs' AND tablename = 'tbl_scan_logs$x'
                                ) AS t;");
        if ($query->num_rows() > 0) {
            $exist =  $query->row()->t;
        } else {
            $q = "";
            $qq = "";
            $q  = $this->db->query("CREATE SEQUENCE logs.tbl_scan_logs_seq$x
                                    INCREMENT BY 1
                                    MINVALUE 1
                                    MAXVALUE 9223372036854775807
                                    START 1
                                    CACHE 1
                                    NO CYCLE;");
            if ($q) {
                $qq = $this->db->query("CREATE TABLE logs.tbl_scan_logs$x (
                                            id int8 NOT NULL DEFAULT nextval('logs.tbl_scan_logs_seq$x'::regclass),
                                            date timestamp NOT NULL DEFAULT now(),
                                            action text NOT NULL,
                                            scan_data text NULL,
                                            scanned_by text NOT NULL,
                                            gate_scanned text NOT NULL,
                                            ip text NULL,
                                            CONSTRAINT tbl_userlogs_pkey PRIMARY KEY (id)
                                        );");
            }
            if ($qq) {
                $exist = true;
            }
        }
        if ($exist == true) {
            $login_id = $this->session->agrishop_login_id;
            $login_alias = $this->session->agrishop_login_uname;
            $now = $this->now();
            // $action = $action; //addslashes($action);
            $ip = $this->get_ip();
            $data = [
                "date" => $now,
                "action" => '{"IO":"' . $io . '"}',
                "scan_data" => '{"tpye":"' . $type . '","id":"' . $scanned_id . '"}',
                "scanned_by" => '{"user":"' . $login_alias . '","id":"' . $login_id . '"}',
                "gate_scanned" => '{"name":"' . $g_name . '","id":"' . $g_id . '"}',
                "ip" => $ip,
            ];
            if ($login_id) {
                $this->db->insert("logs.tbl_scan_logs$x", $data);
            }
        }
    }

    public function learnerlog($action)
    {
        $sy = $this->getOnLoad()["sy_id"];
        $login_id = $this->session->agrishop_login_id;
        $login_alias = $this->session->agrishop_login_uname;
        $now = $this->now();
        $action = addslashes($action);
        $ip = $this->get_ip();
        $data = [
            "date_time" => $now,
            "action" => $action,
            "user_id" => $login_id,
            "user_name" => $login_alias,
            "ip" => $ip,
        ];
        if ($login_id) {
            $this->db->insert("sy$sy.g_tbl_userlogs_learner", $data);
        }
    }

    public function uploadImg($pic, $picname, $path_, $dupload)
    {
        $newImageName = null;
        $isUploaded = false;

        if (isset($pic) && !$isUploaded) {
            $config['upload_path'] = "dist/img/media/$path_/";

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload($dupload)) {
                $myPic = null;
            } else {
                $isUploaded = true;
                $myPic = $this->upload->data();

                // Determine the file extension
                $extension = pathinfo($myPic['file_name'], PATHINFO_EXTENSION);

                // Final new image name
                $cleanName = preg_replace('/[^a-z0-9_-]/', '', strtolower($picname));
                $newImageName = $cleanName . "_" . time() . "." . $extension;
                $newImagePath = $config['upload_path'] . $newImageName;

                // Config for image_lib (to resize/copy)
                $config['image_library'] = 'gd2';
                $config['source_image'] = $myPic['full_path'];   // original uploaded file
                $config['new_image'] = $newImagePath;

                $this->load->library('image_lib', $config);
                $this->image_lib->resize();

                // 🔑 Remove the original uploaded file (with random/original name)
                if (file_exists($myPic['full_path']) && $myPic['full_path'] !== $newImagePath) {
                    unlink($myPic['full_path']);
                }

                return $newImagePath;
            }
        }
    }

    public function getImg($a)
    {
        // Check if the provided path is a URL
        if (filter_var($a, FILTER_VALIDATE_URL)) {
            $pathExists = get_headers($a);
            if ($pathExists && strpos($pathExists[0], '200')) {
                return $a;
            }
        } else {
            // Check if the provided path is a local file
            $localPath = realpath($a);
            if ($localPath && is_file($localPath)) {
                // Check if the file is an image
                $imageInfo = getimagesize($localPath);
                if ($imageInfo !== false) {
                    return base_url() . $a;
                }
            }
        }

        // Return the default image URL
        return base_url('dist/img/media/icons/1x1.png');
    }

    public function dateFormat($a)
    {
        $b = "-";
        if ($a != null) {
            $c = date_create($a);
            $b = date_format($c, "M d, Y");
        }
        return strtoUpper($b);
    }
}

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */