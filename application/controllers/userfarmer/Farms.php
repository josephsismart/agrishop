<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Farms extends MY_Controller
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
        $uri = $this->session->agrishop_login_uri;
        $page_data += [
            "page_title"        => "Farms",
            "current_location"  => "farms",
            "content"           =>  [$this->load->view('interface/' . $uri . '/Farms', [
                // "getOnLoad" => $this->getOnLoad(),
            ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }

    function getFarmInfo()
    {
        // $school_id = $this->session->agrishop_login_school_id;
        $thisQuery = $this->db->query("SELECT * FROM public.farmer_farm t1");


        $requestData = $_REQUEST;
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Query to get total record count
        $thisQuery = $this->db->query("SELECT COUNT(*) AS total FROM public.farmer_farm WHERE CONCAT(farm_name,(CASE WHEN is_active = true THEN 'ACTIVE' ELSE 'INACTIVE' END)) ILIKE '%$searchValue%'");
        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT * FROM public.farmer_farm WHERE CONCAT(farm_name,(CASE WHEN is_active = true THEN 'ACTIVE' ELSE 'INACTIVE' END)) ILIKE '%$searchValue%'
                                    ORDER BY created_at DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        $cc = $offset + 1;
        foreach ($query->result() as $key => $value) {
            $poi = null;
            $is_a_v = $value->is_active;
            $is_active = $is_a_v < 1 ? "<span class='badge bg-danger'>INACTIVE</span>" : "<span class='badge bg-success'>ACTIVE</span>";
            $data[] = array(
                $value->farm_name,
                $value->total_area_sqm . " sqm",
                $value->farm_name,
                $is_active,
            );
        } // Prepare the response data in the required format
        $response = array(
            'draw' => intval($requestData['draw']),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords), // For simplicity, assuming no filtering is applied
            'data' => $data,
        );
        echo json_encode($response);
    }

    function saveFarmInfo()
    {
        $this->db->trans_begin();
        $true = ["success"   => true];
        $false = ["success"   => false];
        $data = [
            "farmer_id" => 3,
            "farm_name" => "FARM1",
            "total_area_sqm" => 300,
            // "barangay_id" => 1,
            // "geo_polygon" => "a",
            "soil_type" => 1,
            // "is_active" => "a",
            // "created_at" => "a"
        ];

        if ($this->db->insert("farmer_farm", $data)) {
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

        echo json_encode($ret);
    }
}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */