<?php
defined('BASEPATH') or exit('No direct script access allowed');

class OnProduction extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->redirect();
        $this->load->model('mainModel');
        // $this->load->library('excel');
        $this->load->helper('date');
        date_default_timezone_set("Asia/Manila");
    }

    public function index()
    {
        $page_data = $this->system();
        $uri = $this->session->agrishop_login_uri;
        $page_data += [
            "page_title"        => "On Production",
            "current_location"  => "OnProduction",
            "content"           =>  [$this->load->view('interface/' . $uri . '/OnProduction', [
                "billing" => $this->subscription_count(),
            ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }

    function searchProduction()
    {
        $value = $this->input->get('value');
        if (empty($value)) {
            echo json_encode([]);
            return;
        }
        $status_production = "AND (status!='COMPLETED' AND status!='CANCELLED' AND status!='DAMAGED')";

        $query = "SELECT t1.*,t2.status,t2.note,t2.created_at,ff.img_path,t1.produce_id, p.img_path as farmer_img_path,
                                        ff.farm_name,
                                        CONCAT(p.first_name, ' ', p.middle_name, ' ', p.last_name) as farmer_name,
                                        p.contact_num,p.email_address,
                                        ff.barangay_id,
                                        ff.lat,
                                        ff.lon, pr.name as produce_name, pr.img_path as produce_img_path FROM farmer_produce_production t1
                                    JOIN (SELECT * FROM farmer_produce_production_status WHERE is_latest = 1 $status_production)t2 on t1.id=t2.farmer_produce_production_id
                                    LEFT JOIN farmer_farm ff ON t1.farmer_farm_id = ff.id
                                    LEFT JOIN farmer f on ff.farmer_id= f.id
                                    LEFT JOIN person p on f.person_id=p.id
                                    LEFT JOIN produce pr on t1.produce_id = pr.id
                                    WHERE CONCAT(pr.name, pr.tags, t1.variety) COLLATE utf8mb4_general_ci LIKE '%$value%'";
        $query = $this->db->query($query);

        $data = [];
        foreach ($query->result() as $value) {
            $farm_address = $this->getAddress2($value->barangay_id);
            $farm_image = $value->img_path ? base_url($value->img_path) : base_url('dist/img/media/icons/1x1.png');
            $farmer_image = $value->farmer_img_path ? base_url($value->farmer_img_path) : base_url('dist/img/media/icons/1x1.png');
            $produce_image = $this->defaultImage($value->produce_img_path, $value->produce_id);
            $farm_image_path = "<img src='$farm_image' width='50' height='50' class='rounded pr-2' data-toggle='tooltip' data-placement='top' title=''>";
            $farmer_image_path = "<img src='$farmer_image' width='50' height='50' class='rounded pr-2' data-toggle='tooltip' data-placement='top' title=''>";
            $produce_image_path = "<img src='$produce_image' width='50' height='50' class='rounded pr-2' data-toggle='tooltip' data-placement='top' title=''>";
            $data[] = [
                "id"    => $value->id,
                "farm_img_path"    => $farm_image_path,
                "farm_name"    => $value->farm_name,
                "farm_location"    => $farm_address,
                "farmer_img_path" => $farmer_image_path,
                "farmer_name" => $value->farmer_name,
                "planted_date" => $value->planted_date,
                "area_sqm" => $value->area_sqm . " sqm",
                "status" => $value->status,
                "lat"  => $value->lat,
                "lon"  => $value->lon,
                "produce"    => $value->produce_name,
                "variety" => $value->variety,
                "produce_img_path" => $produce_image_path,
                "farmerContact" => "Contact: " . $value->contact_num . " | Email: " . $value->email_address
            ];
        }
        echo json_encode($data);
    }


    function getProductionInfo($status_production = '')
    {
        $requestData = $_REQUEST;
        $person_id  = $this->session->agrishop_person_id;
        $farmer_id = $this->session->agrishop_login_farmer_id;
        // $status_production = isset($requestData['search']['status_production']) ? $requestData['search']['status_production'] : '';
        if ($status_production == '') {
            $status_production = "AND (status!='COMPLETED' AND status!='CANCELLED' AND status!='DAMAGED')";
        } else {
            $status_production = "AND (status='COMPLETED' OR status='CANCELLED' OR status='DAMAGED')";
        }

        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Query to get total record count
        $thisQuery = $this->db->query("SELECT COUNT(1) as total FROM farmer_produce_production t1
                                JOIN (SELECT * FROM farmer_produce_production_status WHERE is_latest = 1 $status_production)t2 on t1.id=t2.farmer_produce_production_id
                                LEFT JOIN farmer_farm ff ON t1.farmer_farm_id = ff.id
                                LEFT JOIN farmer f on ff.farmer_id= f.id
                                LEFT JOIN person p on f.person_id=p.id
                                LEFT JOIN produce pr on t1.produce_id = pr.id
                                WHERE f.id= $farmer_id AND CONCAT(pr.name, pr.tags, t1.variety, t2.status) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'");
        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT t1.*,t2.status,t2.note,t2.created_at,ff.img_path,t1.produce_id, p.img_path as farmer_img_path,
                                        ff.farm_name,
                                        ff.barangay_id,
                                        ff.lat,
                                        ff.lon, pr.name as produce_name, pr.img_path as produce_img_path FROM farmer_produce_production t1
                                    JOIN (SELECT * FROM farmer_produce_production_status WHERE is_latest = 1 $status_production)t2 on t1.id=t2.farmer_produce_production_id
                                    LEFT JOIN farmer_farm ff ON t1.farmer_farm_id = ff.id
                                    LEFT JOIN farmer f on ff.farmer_id= f.id
                                    LEFT JOIN person p on f.person_id=p.id
                                    LEFT JOIN produce pr on t1.produce_id = pr.id
                                    WHERE f.id= $farmer_id  AND CONCAT(pr.name, pr.tags, t1.variety, t2.status) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
                                    ORDER BY id DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        $cc = 1;
        foreach ($query->result() as $key => $value) {
            $is_a_v = $value->status;
            $img_path = $this->defaultImage($value->produce_img_path, $value->produce_id);
            $img = "<img src='$img_path' width='50' height='50' class='rounded' data-toggle='tooltip' data-placement='top' title=''>";
            $status_color = $value->status == 'DAMAGED' ? 'bg-danger' : ($value->status == 'CANCELLED' ? 'bg-gray' : 'bg-success');
            $status = "<span class='badge $status_color'>" . $value->status . "</span>";
            $note = '<i class="text-muted text-xs" title="' . $value->note . '">' . $value->note . '</i>';
            $status_date = "<br/><small class='text-black'>" . date('M d, Y', strtotime($value->created_at)) . "</small>";
            $data[] = array(
                $cc++,
                "<div onclick=\"production_id_=" . $value->id . ";$('#note').val('" . $value->note . "'); $('#productionStatus').val('" . $value->status . "');$('#modalProductionStatus').modal('show');\" style=\"cursor:pointer;\">" . $img . '</br>' . $status . $status_date . '<br/>' . $note  . '</div>',
                '<b class="text-black">' . $value->produce_name . '</b></br><i class="text-gray">' . $value->variety . '</i>',
                'HRVST:<b class="text-black">' . $value->expected_harvest_date .
                    '</b><br/>YIELD:<b class="text-success"> ' . $value->expected_yield .
                    '</b><br/>REVENUE:<b class="text-blue"> ' . $value->expected_revenue . '</b>',
                $value->planted_date,
                $value->area_sqm . " sqm",
                $value->market_price_per_kg,
                $value->farm_name . "<br/><span class='text-xs'><i>" . $this->getAddress2($value->barangay_id) . "</i></span>",
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

    function getProductionInfoCompleted()
    {
        $this->getProductionInfo('COMPLETED');
    }

    function saveProductionInfo()
    {
        $this->db->trans_begin();
        $true = ["success"   => true];
        $false = ["success"   => false];
        $data = [];
        $person_id = $this->session->agrishop_person_id;

        $data = [
            'farmer_farm_id' => $this->input->post("farmer_farm_id"),
            'produce_id' => $this->input->post("produce_id"),
            'variety' => strtoupper($this->input->post("variety")),
            'planted_date' => $this->input->post("planted_date"),
            'last_harvest_date' => $this->input->post("last_harvest_date"),
            'area_sqm' => $this->input->post("area_sqm"),
            'market_price_per_kg' => $this->input->post("market_price_per_kg"),
            'expected_harvest_date' => $this->input->post("expected_harvest_date"),
            'expected_yield' => $this->input->post("expected_yield"),
            'expected_revenue' => $this->input->post("expected_revenue"),
        ];

        if ($this->db->insert("farmer_produce_production", $data)) {
            $production_id = $this->db->insert_id();
            $data_status = [
                'farmer_produce_production_id' => $production_id,
                'status' => "PLANTED",
                'created_at' => date("Y-m-d H:i:s"),
                'created_by_person_id' => $person_id
            ];
            $this->insert_production_status($this->input->post("farm_id"), $data_status);

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

    public function updateProductionStatus()
    {
        $this->db->trans_begin();
        $person_id = $this->session->agrishop_person_id;
        $dateNow = date("Y-m-d H:i:s");
        $true = ["success"   => true];
        $false = ["success"   => false];

        $production_id = $this->input->post('production_id');
        $status = $this->input->post('status');
        $data_status = [
            'farmer_produce_production_id' => $production_id,
            'status' => $status,
            'created_at' => $dateNow,
            'created_by_person_id' => $person_id,
            'note' => $this->input->post('note')
        ];
        $this->db->update('farmer_produce_production_status', ['is_latest' => 0], ['farmer_produce_production_id' => $production_id]);
        // echo $transaction_id . " " . $status . " " . $person_id;
        if ($this->db->insert('farmer_produce_production_status', $data_status)) {
            $true += ["message"   => "Successfully updated!"];
            $ret = $true;
        } else {
            $false += ["message"   => "Failed to update!"];
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