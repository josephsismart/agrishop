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

    function getQRPerson()
    {
        $sy = $this->getOnLoad()["sy_id"];
        $ssy = 'sy' . $sy;
        $data = ["data" => []];
        $dataTemp = [];
        $filter = $this->input->post("v");
        $g_name =  $this->input->post("g_nm");
        $g_id = $this->input->post("g_id");
        $personId = null;
        $where = "";
        // $v = $filter != "" ? $filter[0] : null;
        // if ($v) {
        $v = $filter[0];
        $where = $v == '7' ? " WHERE t1.in2_='$filter'" : ($v == '6' ? " WHERE t1.out2_='$filter'" : " WHERE t1.in_='zzzzz'");
        $z = ($v == '7' ? 'IN' : 'OUT');
        $io = "<b class='text-" . ($v == '7' ? 'success' : 'danger') . "'>" . $z . "</b>";
        // $dateTime = new DateTime();
        // $currentTime = $dateTime->format('Y-m-d H:i:s');
        // $user_id = $this->session->lnhs_login_id;
        // $thisQuery = $this->db->query("SELECT  t1.full_name, t1.img_path, t1.type FROM profile.view_gete_pass t1 $where LIMIT 1"); #sy$sy. bs_view_enrollment t1 $where LIMIT 1");
        $thisQuery = $this->db->query("SELECT t1.*, COALESCE(t1.middle_name,'-') mname, TO_CHAR(CURRENT_TIMESTAMP, 'HH:MI AM') AS time_now FROM public.attendance('$ssy') AS t1(in_ text, in2_ text, 
                                        out_ text, out2_ text, type_ text, full_name text,
                                        last_name text, first_name text, middle_name text, 
                                        assignment_ text, img_path text) $where LIMIT 1"); #sy$sy. bs_view_enrollment t1 $where LIMIT 1");


        if ($thisQuery) {
            foreach ($thisQuery->result() as $key => $value) {
                $mn = $value->mname;
                $data["data"][] = [
                    // "fullName" => $value->full_name,
                    // "description" => $value->type_ . " " . $io,

                    "lname"  => $value->last_name,
                    "fname_mname"  => $value->first_name . ' ' . ($mn ? ($mn == '-' ? '-' : ($mn[0] . '.')) : ''),
                    "assignment"  => $value->assignment_,
                    "type"  => $value->type_,
                    "io" => $z,

                    "pic" => $this->getImg($value->img_path),
                    "time" => $value->time_now,
                ];
                // $personId = $value->id;
                $this->scanlog($sy, $value->type_, $filter, $z, $g_name, $g_id);
            }
        }
        // }
        // echo ($where);
        echo json_encode($data);
    }
}
