<?php
defined('BASEPATH') or exit('No direct script access allowed');

class FarmProduce extends MY_Controller
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
            "page_title"        => "Farm & Produce",
            "current_location"  => "farmproduce",
            "content"           =>  [$this->load->view('interface/' . $uri . '/FarmProduce', [
                // "getOnLoad" => $this->getOnLoad(),
            ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }

    function getFarmInfo()
    {
        $requestData = $_REQUEST;
        $person_id  = $this->session->agrishop_login_id;
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Query to get total record count
        $thisQuery = $this->db->query("SELECT COUNT(1) AS total FROM public.farmer_farm 
                                    WHERE created_by_person_id = $person_id AND CONCAT(farm_name,(CASE WHEN is_active = true THEN 'ACTIVE' ELSE 'INACTIVE' END)) ILIKE '%$searchValue%'");
        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT * FROM public.farmer_farm 
                                    WHERE created_by_person_id = $person_id AND CONCAT(farm_name,(CASE WHEN is_active = true THEN 'ACTIVE' ELSE 'INACTIVE' END)) ILIKE '%$searchValue%'
                                    ORDER BY created_at DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        $cc = $offset + 1;
        foreach ($query->result() as $key => $value) {
            $poi = null;
            $is_a_v = $value->is_active;
            $img = $value->img_path ? base_url($value->img_path) : base_url('dist/img/media/icons/1x1.png');
            $image_path = "<img src='$img' width='50' height='50' class='rounded' data-toggle='tooltip' data-placement='top' title=''>";
            $is_active = $is_a_v < 1 ? "<span class='badge bg-danger'>INACTIVE</span>" : "<span class='badge bg-success'>ACTIVE</span>";
            $data[] = array(
                $image_path,
                $value->farm_name,
                $value->barangay_id,
                $value->total_area_sqm . " sqm",
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

    function getProduceInfo()
    {
        $requestData = $_REQUEST;
        $person_id  = $this->session->agrishop_login_id;
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Query to get total record count
        $thisQuery = $this->db->query("SELECT count(1) AS total FROM public.produce p
                                    LEFT JOIN public.produce_classification pc ON p.produce_classification_id = pc.id
                                    WHERE p.created_by_person_id = $person_id AND CONCAT(p.name,pc.class_name,p.description,p.is_active,(CASE WHEN p.is_seasonal = true THEN 'SEASONAL' ELSE 'NON-SEASONAL' END),(CASE WHEN p.is_active = true THEN 'ACTIVE' ELSE 'INACTIVE' END)) 
                                    ILIKE '%$searchValue%'");

        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT p.id,p.name as produce,pc.class_name,p.description,
                                    p.is_seasonal,p.is_active,p.created_at, p.img_path 
                                    FROM public.produce p
                                    LEFT JOIN public.produce_classification pc ON p.produce_classification_id = pc.id
                                    WHERE p.created_by_person_id = $person_id AND CONCAT(p.name,pc.class_name,p.description,(CASE WHEN p.is_seasonal = true THEN 'SEASONAL' ELSE 'NON-SEASONAL' END),(CASE WHEN p.is_active = true THEN 'ACTIVE' ELSE 'INACTIVE' END)) 
                                    ILIKE '%$searchValue%'
                                    ORDER BY p.created_at DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        $cc = $offset + 1;
        foreach ($query->result() as $key => $value) {
            $poi = null;
            $is_a_v = $value->is_active;
            $img = $value->img_path ? base_url($value->img_path) : base_url('dist/img/media/icons/1x1.png');
            $is_active = $is_a_v == 't' ? "<span class='badge bg-success'>ACTIVE</span>" : "<span class='badge bg-danger'>INACTIVE</span>";
            $is_seasonal = $value->is_seasonal == 't' ? "<span class='badge bg-blue'>SEASONAL</span>" : "<span class='badge bg-gray'>NON-SEASONAL</span>";
            $image_path = "<img src='$img' width='50' height='50' class='rounded' data-toggle='tooltip' data-placement='top' title=''>";
            $data[] = array(
                $image_path,
                $value->produce,
                $value->class_name,
                $is_seasonal,
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

    function getFarmProduceInfo()
    {
        $requestData = $_REQUEST;
        $person_id  = $this->session->agrishop_login_id;
        $farm_id = $requestData['search']['farm_id'];
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        // RETURN EMPTY IF FARM ID IS MISSING
        if (!$farm_id || $farm_id == "" || $farm_id == "0") {
            echo json_encode([
                "draw" => intval($requestData['draw']),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            ]);
            return;
        }
        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Query to get total record count
        $thisQuery = $this->db->query("SELECT count(1) AS total FROM public.farm_produce fp
                                    LEFT JOIN public.produce p ON fp.produce_id = p.id
                                    LEFT JOIN public.produce_classification pc ON p.produce_classification_id = pc.id
                                    LEFT JOIN price_qty_left pql ON fp.farm_id = pql.farm_id AND fp.produce_id = pql.produce_id
                                    WHERE fp.farm_id = $farm_id AND CONCAT(p.name,pc.class_name,p.description,(CASE WHEN p.is_seasonal = true THEN 'SEASONAL' ELSE 'NON-SEASONAL' END),(CASE WHEN p.is_active = true THEN 'ACTIVE' ELSE 'INACTIVE' END)) 
                                    ILIKE '%$searchValue%'");

        $totalRecords = $thisQuery->row()->total;

        // RETURN EMPTY IF NO DATA
        if ($totalRecords == 0) {
            echo json_encode([
                "draw" => intval($requestData['draw']),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            ]);
            return;
        }

        $query = $this->db->query("SELECT fp.id as fp_id,p.id,p.name as produce,pql.harvest_schedule,pql.uom,pql.price,pql.qty_left ,pc.class_name,p.description,
                                    p.is_seasonal,p.is_active,p.created_at, p.img_path 
                                    FROM public.farm_produce fp
                                    LEFT JOIN public.produce p ON fp.produce_id = p.id
                                    LEFT JOIN public.produce_classification pc ON p.produce_classification_id = pc.id
                                    LEFT JOIN price_qty_left pql ON fp.farm_id = pql.farm_id AND fp.produce_id = pql.produce_id
                                    WHERE fp.farm_id = $farm_id AND CONCAT(p.name,pc.class_name,p.description,(CASE WHEN p.is_seasonal = true THEN 'SEASONAL' ELSE 'NON-SEASONAL' END),(CASE WHEN p.is_active = true THEN 'ACTIVE' ELSE 'INACTIVE' END)) 
                                    ILIKE '%$searchValue%'
                                    ORDER BY p.created_at DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        $cc = $offset + 1;
        foreach ($query->result() as $key => $value) {
            $poi = null;
            $is_a_v = $value->is_active;
            $img = $value->img_path ? base_url($value->img_path) : base_url('dist/img/media/icons/1x1.png');
            $is_active = $is_a_v == 't' ? "<span class='badge bg-success'>ACTIVE</span>" : "<span class='badge bg-danger'>INACTIVE</span>";
            $is_seasonal = $value->is_seasonal == 't' ? "<span class='badge bg-blue'>SEASONAL</span>" : "<span class='badge bg-gray'>NON-SEASONAL</span>";
            $image_path = "<img src='$img' width='50' height='50' class='rounded' data-toggle='t0
            .0.ooltip' data-placement='top' title=''>";

            $add_produce = "<span class='badge bg-success' type='button' onclick='add_qty({
                                id: \"$value->fp_id\",
                                img_path: \"$img\",
                                produce: \"$value->produce\",
                                harvest_schedule: \"$value->harvest_schedule\",
                                qty_left: \"$value->qty_left\",
                                price: \"$value->price\",
                                uom: \"$value->uom\",
                                class_name: \"$value->class_name\",
                                is_seasonal: \"$value->is_seasonal\",
                                is_active: \"$value->is_active\"
                            })'>+ QTY</span>";
            $data[] = array(
                $add_produce,
                $image_path,
                $value->produce,
                $value->harvest_schedule,
                $value->qty_left,
                $value->price,
                $value->uom,
                $value->class_name,
                $is_seasonal,
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
        $farmName = $this->input->post("farmName");
        $totalAreaSqm = $this->input->post("totalAreaSqm");
        $lat = $this->input->post("lat");
        $lon = $this->input->post("lon");
        $login_id = $this->session->agrishop_login_id;

        $person_id = $this->session->agrishop_login_id;
        $exist = $this->db->query("SELECT * FROM public.farmer_farm WHERE farm_name = '$farmName' and created_by_person_id = $person_id")->num_rows();
        if ($exist > 0) {
            $false += ["message"   => "Farm already exists!", "exist"   => true];
            $ret = $false;
            echo json_encode($ret);
            return;
        }


        $data = [
            "farmer_id" => 3,
            "farm_name" => $farmName,
            "total_area_sqm" => $totalAreaSqm,
            "soil_type" => 1,
            "created_by_person_id" => $login_id,
            "lat" => $lat,
            "lon" => $lon,
        ];
        if (isset($_FILES['picFarm']) && $_FILES['picFarm']['error'] === UPLOAD_ERR_OK) {
            // Normal upload
            $upload = $this->uploadImg($_FILES['picFarm'], $farmName, 'farm', 'picFarm');
            $data += [
                "img_path" => $upload
            ];
        }

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

    function saveProduceInfo()
    {
        $this->db->trans_begin();
        $true = ["success"   => true];
        $false = ["success"   => false];
        $data = [];
        $person_id = $this->session->agrishop_login_id;
        $produceName = $this->input->post("produceName");

        $exist = $this->db->query("SELECT * FROM public.produce WHERE name = '$produceName' and created_by_person_id = $person_id")->num_rows();
        if ($exist > 0) {
            $false += ["message"   => "Produce already exists!", "exist"   => true];
            $ret = $false;
            echo json_encode($ret);
            return;
        }

        $classification = $this->input->post("classification");
        $description = $this->input->post("description");
        $seasonal = $this->input->post("seasonal") == null ? false : true;
        $data = [
            "name" => $produceName,
            "produce_classification_id" => $classification,
            "description" => $description,
            "is_seasonal" => $seasonal,
            "created_by_person_id" => $this->session->agrishop_login_id,
        ];

        if (isset($_FILES['picProduce']) && $_FILES['picProduce']['error'] === UPLOAD_ERR_OK) {
            // Normal upload
            $upload = $this->uploadImg($_FILES['picProduce'], $produceName, 'produce', 'picProduce');
            $data += [
                "img_path" => $upload
            ];
        }

        if ($this->db->insert("produce", $data)) {
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

    function saveFarmProduceSupply()
    {
        $this->db->trans_begin();
        $true = ["success"   => true];
        $false = ["success"   => false];
        $data = [];
        $person_id = $this->session->agrishop_login_id;
        $farm_produce_id = $this->input->post("fp_id");

        $data = [
            "farm_produce_id" => $farm_produce_id,
            "qty" => $this->input->post("qty_add"),
            "created_by_person_id" => $this->session->agrishop_login_id,
        ];

        if ($this->db->insert("farm_produce_supply", $data)) {
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