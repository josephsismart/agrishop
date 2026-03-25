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
                                        ff.lon, pr.name as produce_name, pr.img_path as produce_img_path,
                                        t1.expected_harvest_date, t1.expected_yield, t1.expected_revenue,
                                        t1.market_price_per_kg, t1.area_sqm as area_sqm_raw
                                        FROM farmer_produce_production t1
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
                "farmerContact"        => "Contact: " . $value->contact_num . " | Email: " . $value->email_address,
                "expected_harvest_date"=> $value->expected_harvest_date,
                "expected_yield"       => $value->expected_yield,
                "expected_revenue"     => $value->expected_revenue,
                "market_price_per_kg"  => $value->market_price_per_kg,
                "note"                 => $value->note,
                "farmer_img_raw"       => $farmer_image,
                "produce_img_raw"      => $produce_image,
            ];
        }
        echo json_encode($data);
    }


    function getProductionInfo($status_production = '')
    {
        $requestData  = $_REQUEST;
        // Logged-in farmer — controls who can update
        $my_farmer_id = (int) $this->session->agrishop_login_farmer_id;

        if ($status_production == '') {
            $status_filter = "AND (status!='COMPLETED' AND status!='CANCELLED' AND status!='DAMAGED')";
        } else {
            $status_filter = "AND (status='COMPLETED' OR status='CANCELLED' OR status='DAMAGED')";
        }

        $searchValue = $this->db->escape_like_str(
            isset($requestData['search']['value']) ? $requestData['search']['value'] : ''
        );
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Show ALL farmers production records
        $base = "FROM farmer_produce_production t1
            JOIN (SELECT * FROM farmer_produce_production_status WHERE is_latest = 1 $status_filter) t2
                ON t1.id = t2.farmer_produce_production_id
            LEFT JOIN farmer_farm ff ON t1.farmer_farm_id = ff.id
            LEFT JOIN farmer f ON ff.farmer_id = f.id
            LEFT JOIN person p ON f.person_id = p.id
            LEFT JOIN produce pr ON t1.produce_id = pr.id
            WHERE CONCAT(
                COALESCE(pr.name,''), COALESCE(pr.tags,''), COALESCE(t1.variety,''),
                COALESCE(t2.status,''), COALESCE(p.first_name,''), COALESCE(p.last_name,''),
                COALESCE(ff.farm_name,'')
            ) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'";

        $totalRecords = (int) $this->db->query("SELECT COUNT(1) AS total $base")->row()->total;

        $query = $this->db->query("
            SELECT t1.*, t2.status, t2.note, t2.created_at AS status_date,
                   ff.img_path AS farm_img, ff.farm_name, ff.barangay_id, ff.lat, ff.lon,
                   f.id AS owner_farmer_id,
                   CONCAT(p.first_name, ' ', p.last_name) AS farmer_name,
                   p.img_path AS farmer_img,
                   pr.name AS produce_name, pr.img_path AS produce_img_path
            $base
            ORDER BY t1.id DESC
            LIMIT $limit OFFSET $offset
        ");

        $data = [];
        $cc   = 1;
        foreach ($query->result() as $value) {
            $is_owner = ((int)$value->owner_farmer_id === $my_farmer_id);

            $img_path = $this->defaultImage($value->produce_img_path, $value->produce_id);
            $img = "<img src='$img_path' width='50' height='50' class='rounded'>";

            $sc = $value->status == 'DAMAGED'   ? 'bg-danger'
               : ($value->status == 'CANCELLED' ? 'bg-secondary'
               : ($value->status == 'COMPLETED' ? 'bg-info' : 'bg-success'));
            $badge = "<span class='badge $sc'>" . $value->status . "</span>";
            $sdate = "<br><small class='text-muted'>" . date('M d, Y', strtotime($value->status_date)) . "</small>";
            $note  = "<br><i class='text-muted text-xs'>" . htmlspecialchars($value->note ?? '') . "</i>";

            // Only owner can click to update
            // Use data-* attributes to avoid any quote/syntax issues in onclick
            if ($is_owner) {
                $img_col = '<div class="prod-status-btn" style="cursor:pointer" title="Click to update status"'
                    . ' data-pid="' . $value->id . '"'
                    . ' data-note="' . htmlspecialchars($value->note ?? '', ENT_QUOTES) . '"'
                    . ' data-status="' . htmlspecialchars($value->status, ENT_QUOTES) . '">'
                    . $img . $badge . $sdate . $note . '</div>';
            } else {
                $img_col = '<div style="cursor:default" title="View only — not your record">'
                    . $img . $badge . $sdate . $note . '</div>';
            }

            $f_img = !empty($value->farmer_img)
                ? base_url($value->farmer_img)
                : base_url('dist/img/media/icons/1x1.png');
            $farmer_col = "<div class='d-flex align-items-center' style='gap:6px;'>"
                . "<img src='$f_img' width='32' height='32' class='rounded-circle' style='object-fit:cover;flex-shrink:0;'>"
                . "<div style='line-height:1.2;'><div style='font-size:12px;font-weight:600;'>" . htmlspecialchars($value->farmer_name) . "</div>"
                . ($is_owner ? "<span class='badge bg-success' style='font-size:9px;'>You</span>" : "")
                . "</div></div>";

            $data[] = [
                $cc++,
                $img_col,
                '<b>' . htmlspecialchars($value->produce_name) . '</b><br><i class="text-gray text-xs">' . htmlspecialchars($value->variety ?? '') . '</i>',
                'HRVST: <b>' . $value->expected_harvest_date . '</b><br>'
                . 'YIELD: <b class="text-success">' . $value->expected_yield . '</b><br>'
                . 'REVENUE: <b class="text-primary">' . $value->expected_revenue . '</b>',
                $value->planted_date,
                $value->area_sqm . ' sqm',
                $value->market_price_per_kg,
                $farmer_col,
                $value->farm_name . "<br><small class='text-muted'><i>" . $this->getAddress2($value->barangay_id) . "</i></small>",
            ];
        }

        echo json_encode([
            'draw'            => intval($requestData['draw']),
            'recordsTotal'    => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords),
            'data'            => $data,
        ]);
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

    public function getProductionMapData()
    {
        $query = $this->db->query("
            SELECT t1.id, t1.variety, t1.expected_harvest_date,
                   t2.status,
                   ff.farm_name, ff.lat, ff.lon, ff.barangay_id,
                   f.id AS owner_farmer_id,
                   CONCAT(p.first_name, ' ', p.last_name) AS farmer_name,
                   pr.name AS produce
            FROM farmer_produce_production t1
            JOIN (SELECT * FROM farmer_produce_production_status
                  WHERE is_latest = 1
                  AND status NOT IN ('COMPLETED','CANCELLED','DAMAGED')) t2
                ON t1.id = t2.farmer_produce_production_id
            LEFT JOIN farmer_farm ff ON t1.farmer_farm_id = ff.id
            LEFT JOIN farmer f ON ff.farmer_id = f.id
            LEFT JOIN person p ON f.person_id = p.id
            LEFT JOIN produce pr ON t1.produce_id = pr.id
            WHERE ff.lat IS NOT NULL AND ff.lon IS NOT NULL
        ");

        $data = [];
        foreach ($query->result() as $row) {
            $data[] = [
                'id'             => $row->id,
                'lat'            => (float) $row->lat,
                'lon'            => (float) $row->lon,
                'produce'        => $row->produce,
                'variety'        => $row->variety,
                'status'         => $row->status,
                'farm_name'      => $row->farm_name,
                'farmer_name'    => $row->farmer_name,
                'owner_farmer_id'=> (int) $row->owner_farmer_id,
                'harvest_date'   => $row->expected_harvest_date,
                'location'       => $this->getAddress2($row->barangay_id),
            ];
        }
        echo json_encode($data);
    }
}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */