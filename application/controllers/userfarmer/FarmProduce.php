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
        $person_id  = $this->session->agrishop_person_id;
        $farmer_id = $this->session->agrishop_login_farmer_id;

        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Query to get total record count
        $thisQuery = $this->db->query("SELECT COUNT(1) AS total FROM public.farmer_farm 
                                    WHERE farmer_id = $farmer_id AND CONCAT(farm_name,(CASE WHEN is_active = true THEN 'ACTIVE' ELSE 'INACTIVE' END)) ILIKE '%$searchValue%'");
        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT * FROM public.farmer_farm 
                                    WHERE farmer_id = $farmer_id AND CONCAT(farm_name,(CASE WHEN is_active = true THEN 'ACTIVE' ELSE 'INACTIVE' END)) ILIKE '%$searchValue%'
                                    ORDER BY created_at DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        $cc = $offset + 1;
        foreach ($query->result() as $key => $value) {
            $poi = null;
            $is_a_v = $value->is_active;
            $default = '<i class="fas fa-user fa-3x"></i>';
            $img_path = $value->img_path ? base_url($value->img_path) : $default;
            $img = $value->img_path ? "<img src='$img_path' width='50' height='50' class='rounded' data-toggle='tooltip' data-placement='top' title=''>" : $default; //base_url('dist/img/media/icons/1x1.png');
            $image_path = $img;
            $is_active = $is_a_v < 1 ? "<span class='badge bg-danger'>INACTIVE</span>" : "<span class='badge bg-success'>ACTIVE</span>";
            $data[] = array(
                $image_path,
                $value->farm_name,
                $this->getAddress2($value->barangay_id),
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
        $person_id  = $this->session->agrishop_person_id;
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Query to get total record count
        $thisQuery = $this->db->query("SELECT COUNT(1) AS total
                                            FROM public.produce p
                                            LEFT JOIN public.produce_classification pc ON p.produce_classification_id = pc.id
                                        WHERE (p.created_by_person_id = $person_id OR p.created_by_person_id IS NULL) AND 
                                        CONCAT(p.name, pc.class_name, p.description, p.tags) ILIKE '%$searchValue%'");

        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT * FROM (
                                        SELECT c.id, c.name AS produce, pc.class_name, c.description,
                                            c.is_seasonal, c.is_active, c.created_at, c.created_by_person_id, c.img_path,pc.img_path as default_img_path, c.tags, c.is_customized
                                        FROM public.produce c
                                        LEFT JOIN public.produce_classification pc ON c.produce_classification_id = pc.id
                                        WHERE c.created_by_person_id = $person_id OR c.created_by_person_id IS NULL) AS x
                                    WHERE CONCAT(x.produce, x.class_name, x.description, x.tags) ILIKE '%$searchValue%'
                                    ORDER BY x.img_path
                                    LIMIT $limit OFFSET $offset");

        $data = array();
        $cc = $offset + 1;
        foreach ($query->result() as $key => $value) {
            $poi = null;
            $is_a_v = $value->is_active;
            $img = (!empty($value->img_path) && file_exists(FCPATH . $value->img_path))
                ? base_url($value->img_path)
                : base_url($value->default_img_path);
            $is_active = $is_a_v == 't' ? "<span class='badge bg-success'>ACTIVE</span>" : "<span class='badge bg-danger'>INACTIVE</span>";
            $is_seasonal = $value->is_seasonal == 't' ? "<span class='badge bg-blue'>SEASONAL</span>" : "<span class='badge bg-gray'>NON-SEASONAL</span>";
            $image_path = "<img src='$img' width='50' height='50' class='rounded' data-toggle='tooltip' data-placement='top' title=''>";
            $produce = $value->created_by_person_id == $person_id ? "<span class='badge bg-orange text-white'>" . $value->produce . "</span>" : $value->produce;
            $data[] = array(
                $image_path,
                $produce,
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


    public function search_produce_list()
    {
        $keyword = $this->input->post('keyword');
        $person_id  = $this->session->agrishop_person_id;

        if (strlen($keyword) < 3) {
            echo json_encode([]);
            return;
        }


        $query = $this->db->query("SELECT * FROM (
                                        SELECT p.id, p.name AS produce, pc.class_name, p.description,
                                            p.is_seasonal, p.is_active, p.created_at, p.created_by_person_id, p.img_path, p.tags, pc.img_path as default_img_path
                                        FROM public.produce p
                                        LEFT JOIN public.produce_classification pc ON p.produce_classification_id = pc.id

                                        UNION ALL

                                        SELECT c.id, c.name AS produce, pc.class_name, c.description,
                                            c.is_seasonal, c.is_active, c.created_at, c.created_by_person_id, c.img_path, c.tags, pc.img_path as default_img_path
                                        FROM public.produce_customize c
                                        LEFT JOIN public.produce_classification pc ON c.produce_classification_id = pc.id
                                        WHERE c.created_by_person_id = $person_id
                                    ) AS x
                                    WHERE x.produce ILIKE '%$keyword%'
                                    ORDER BY x.produce
                                    LIMIT 20");

        $results = [];
        foreach ($query->result() as $row) {
            // $img = $row->img_path ? base_url($row->img_path) : base_url('dist/img/media/icons/1x1.png');//update this
            $img = (!empty($row->img_path) && file_exists(FCPATH . $row->img_path))
                ? base_url($row->img_path)
                : base_url($row->default_img_path);
            $results[] = [
                'id'   => $row->id,
                'name' => $row->produce,
                'created_by' => $row->created_by_person_id,
                'image_url' => $img,
            ];
        }

        echo json_encode($results);
    }

    function getReservedCount()
    {
        $farmer_id  = (int) $this->session->agrishop_login_farmer_id;
        echo $this->getTransactionPeding($farmer_id,'RESERVED','farmer');
    }   

    function getPrice()
    {
        $produce_id = (int) $this->input->post("produce_id");
        $person_id  = (int) $this->session->agrishop_person_id;

        $query = $this->db->query("
        SELECT
            harvest_schedule,
            price,
            COALESCE(qty_sold, 0) AS qty_sold,
            qty_left,
            produce_id
        FROM price_qty_left
        WHERE farmer_person_id = $person_id
          AND produce_id = $produce_id
        LIMIT 1
    ");

        if ($query->num_rows() == 0) {
            echo json_encode([]);
            return;
        }

        $row = $query->row();

        $current_price = (float) $row->price;
        $qty_sold      = (int) $row->qty_sold;
        $qty_left      = (int) $row->qty_left;

        // -----------------------------
        // SMART PRICE RULES
        // -----------------------------
        $suggested_price = $current_price;
        $reason = "Stable demand and supply.";

        if ($qty_sold > $qty_left && $qty_left <= 10) {
            $suggested_price = round($current_price * 1.10, 2);
            $reason = "High demand and low remaining stock.";
        } elseif ($qty_sold < ($qty_left / 2)) {
            $suggested_price = round($current_price * 0.90, 2);
            $reason = "Low demand and high remaining stock.";
        }

        echo json_encode([
            "produce_id"       => $row->produce_id,
            "current_price"    => $current_price,
            "suggested_price"  => $suggested_price,
            "qty_sold"         => $qty_sold,
            "qty_left"         => $qty_left,
            "reason"           => $reason
        ]);
    }


    function getFarmProduceInfo()
    {
        $requestData = $_REQUEST;
        $person_id  = $this->session->agrishop_person_id;
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
                                    p.is_seasonal,p.is_active,p.created_at, p.img_path, pc.img_path as default_img_path 
                                    FROM public.farm_produce fp
                                    LEFT JOIN public.produce p ON fp.produce_id = p.id
                                    LEFT JOIN public.produce_classification pc ON p.produce_classification_id = pc.id
                                    LEFT JOIN price_qty_left pql ON fp.farm_id = pql.farm_id AND fp.produce_id = pql.produce_id
                                    WHERE fp.farm_id = $farm_id AND CONCAT(p.name,pc.class_name,p.description,(CASE WHEN p.is_seasonal = true THEN 'SEASONAL' ELSE 'NON-SEASONAL' END),(CASE WHEN p.is_active = true THEN 'ACTIVE' ELSE 'INACTIVE' END)) 
                                    ILIKE '%$searchValue%'
                                    ORDER BY fp.id desc
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        $cc = $offset + 1;
        foreach ($query->result() as $key => $value) {
            $is_a_v = $value->is_active;

            // Image handling
            $img = (!empty($value->img_path) && file_exists(FCPATH . $value->img_path))
                ? base_url($value->img_path)
                : base_url($value->default_img_path);

            // Beautified Status Badges
            $is_active = $is_a_v == 't'
                ? "<div class='text-center'>
            <span class='badge badge-pill bg-success p-2' style='font-size: 0.95rem; min-width: 100px;'>
                <i class='fas fa-check-circle mr-1'></i> ACTIVE
            </span>
           </div>"
                : "<div class='text-center'>
            <span class='badge badge-pill bg-danger p-2' style='font-size: 0.95rem; min-width: 100px;'>
                <i class='fas fa-times-circle mr-1'></i> INACTIVE
            </span>
           </div>";

            $is_seasonal = $value->is_seasonal == 't'
                ? "<div class='text-center'>
            <span class='badge badge-pill bg-info p-2' style='font-size: 0.95rem; min-width: 120px;'>
                <i class='fas fa-sun mr-1'></i> SEASONAL
            </span>
           </div>"
                : "<div class='text-center'>
            <span class='badge badge-pill bg-secondary p-2' style='font-size: 0.95rem; min-width: 120px;'>
                <i class='fas fa-calendar mr-1'></i> NON-SEASONAL
            </span>
           </div>";

            // Beautified Image with card effect
            $image_path = "<div class='text-center'>
                      <div class='card shadow-sm border-0' style='width: 70px; margin: 0 auto;'>
                          <img src='$img' class='card-img-top rounded-circle' 
                               style='width: 65px; height: 65px; object-fit: cover; border: 2px solid #dee2e6;'
                               data-toggle='tooltip' data-placement='top' 
                               title='" . htmlspecialchars($value->produce, ENT_QUOTES) . "'>
                      </div>
                   </div>";

            // Beautified Action Button
            $add_produce = "<div class='text-center'>
                        <button class='btn btn-success btn-sm px-3 py-2 shadow-sm' 
                                data-toggle='modal' 
                                data-target='#modalAddFarmProduceSupply' 
                                type='button' 
                                onclick='add_qty({
                                    id: \"" . htmlspecialchars($value->fp_id, ENT_QUOTES) . "\",
                                    img_path: \"$img\",
                                    produce: \"" . htmlspecialchars($value->produce, ENT_QUOTES) . "\",
                                    harvest_schedule: \"$value->harvest_schedule\",
                                    qty_left: \"$value->qty_left\",
                                    price: \"$value->price\",
                                    uom: \"" . htmlspecialchars($value->uom, ENT_QUOTES) . "\",
                                    class_name: \"" . htmlspecialchars($value->class_name, ENT_QUOTES) . "\",
                                    is_seasonal: \"$value->is_seasonal\",
                                    is_active: \"$value->is_active\"
                                })'>
                            <i class='fas fa-plus mr-1'></i> ADD MORE
                        </button>
                    </div>";

            // Format numbers with better readability
            $formatted_qty = number_format($value->qty_left, 2);
            $formatted_price = "₱ " . number_format($value->price, 2);

            // Format date for better display
            $formatted_date = date('M d, Y', strtotime($value->harvest_schedule));

            // Beautified Produce Name with category
            $produce_display = "<div>
                            <div class='font-weight-bold text-dark' style='font-size: 1.1rem;'>
                                " . htmlspecialchars($value->produce, ENT_QUOTES) . "
                            </div>
                            <div class='text-muted small mt-1'>
                                <i class='fas fa-tag mr-1'></i>
                                " . htmlspecialchars($value->class_name, ENT_QUOTES) . "
                            </div>
                        </div>";

            // Beautified Quantity Display
            $quantity_display = "<div class='text-center'>
                            <div class='font-weight-bold text-success' style='font-size: 1.2rem;'>
                                $value->qty_left
                            </div>
                            <div class='text-muted small'>
                                <i class='fas fa-weight-hanging mr-1'></i>
                                " . htmlspecialchars($value->uom, ENT_QUOTES) . "
                            </div>
                        </div>";

            // Beautified Price Display
            $price_display = "<div class='text-center'>
                         <div class='font-weight-bold text-primary' style='font-size: 1.2rem;'>
                             $formatted_price
                         </div>
                         <div class='text-muted small'>
                             per " . htmlspecialchars($value->uom, ENT_QUOTES) . "
                         </div>
                      </div>";

            // Beautified Harvest Date
            $date_display = "<div class='text-center'>
                        <div class='font-weight-bold' style='font-size: 1rem; color: #6c757d;'>
                            <i class='fas fa-calendar-alt mr-1 text-warning'></i>
                            $formatted_date
                        </div>
                        <div class='text-muted small'>
                            Harvest Date
                        </div>
                     </div>";

            $data[] = array(
                $add_produce,           // Action button
                $image_path,           // Product image
                $produce_display,      // Product name + category
                $date_display,         // Harvest date
                $quantity_display,     // Quantity left
                $price_display,        // Price
                $is_seasonal,          // Seasonal status
                $is_active,            // Active status
            );
        }
        // foreach ($query->result() as $key => $value) {
        //     $poi = null;
        //     $is_a_v = $value->is_active;
        //     $img = (!empty($value->img_path) && file_exists(FCPATH . $value->img_path))
        //         ? base_url($value->img_path)
        //         : base_url($value->default_img_path);
        //     $is_active = $is_a_v == 't' ? "<span class='badge bg-success'>ACTIVE</span>" : "<span class='badge bg-danger'>INACTIVE</span>";
        //     $is_seasonal = $value->is_seasonal == 't' ? "<span class='badge bg-blue'>SEASONAL</span>" : "<span class='badge bg-gray'>NON-SEASONAL</span>";
        //     $image_path = "<img src='$img' width='50' height='50' class='rounded' data-toggle='t0
        //     .0.ooltip' data-placement='top' title=''>";

        //     $add_produce = "<span class='badge bg-success' data-toggle='modal' data-target='#modalAddFarmProduceSupply' type='button' onclick='add_qty({
        //                         id: \"$value->fp_id\",
        //                         img_path: \"$img\",
        //                         produce: \"$value->produce\",
        //                         harvest_schedule: \"$value->harvest_schedule\",
        //                         qty_left: \"$value->qty_left\",
        //                         price: \"$value->price\",
        //                         uom: \"$value->uom\",
        //                         class_name: \"$value->class_name\",
        //                         is_seasonal: \"$value->is_seasonal\",
        //                         is_active: \"$value->is_active\"
        //                     })'>+ QTY</span>";
        //     $data[] = array(
        //         $add_produce,
        //         $image_path,
        //         $value->produce,
        //         $value->harvest_schedule,
        //         $value->qty_left,
        //         $value->price,
        //         $value->uom,
        //         $value->class_name,
        //         $is_seasonal,
        //         $is_active,
        //     );
        // } // Prepare the response data in the required format
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
        $farmName = strtoupper($this->input->post("farmName"));
        $barangay = $this->input->post("barangay");
        $totalAreaSqm = $this->input->post("totalAreaSqm");
        $lat = $this->input->post("lat");
        $lon = $this->input->post("lon");
        $login_id = $this->session->agrishop_person_id;

        $person_id = $this->session->agrishop_person_id;
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
            "barangay_id" => $barangay,
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
        $person_id = $this->session->agrishop_person_id;
        $produceName =  strtoupper($this->input->post("produceName"));
        $tags = strtoupper($this->input->post("tags"));


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
            "created_by_person_id" => $this->session->agrishop_person_id,
            "tags" => $tags,
            "is_customized" => TRUE
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

    function saveFarmProduce()
    {

        $this->db->trans_begin();
        $true = ["success"   => true];
        $false = ["success"   => false];
        $data = [];
        $farm_produce_id = null;
        $person_id = $this->session->agrishop_person_id;
        $farmId = $this->input->post("farmId");
        $produceSelectedId = $this->input->post("produceSelectedId");
        $produceCreatedById = $this->input->post("produceCreatedById");
        $qty_add = $this->input->post("qty_add");
        $price = $this->input->post("price");
        $uom = $this->input->post("uom");
        $harvest_date = $this->input->post("harvest_date");

        $exist = $this->db->query("SELECT * FROM public.farm_produce WHERE farm_id = $farmId 
            AND  produce_id = $produceSelectedId  AND harvest_schedule = '$harvest_date' AND created_by_person_id = $person_id")->num_rows();
        if ($exist > 0) {
            $false += ["message"   => "Produce with $harvest_date has already exists!", "exist"   => true];
            $ret = $false;
            echo json_encode($ret);
            return;
        }


        $data += [
            "farm_id" => $farmId,
            "produce_id" => $produceSelectedId,
            "harvest_schedule" => $harvest_date,
            "uom" => $uom,
            "created_at" => Date("Y-m-d"),
            "created_by_person_id" => $person_id,
        ];

        if ($this->db->insert("farm_produce", $data)) {
            $farm_produce_id = $this->db->insert_id();
            $data_price_monitor = [
                "farm_produce_id" => $farm_produce_id,
                "price" => $price,
                "created_by_person_id" => $person_id,
            ];
            if ($this->db->insert("price_monitoring_farm_produce", $data_price_monitor)) {
                $data_fp_supply = [
                    "farm_produce_id" => $farm_produce_id,
                    "qty" => $qty_add,
                    "created_by_person_id" => $person_id,
                ];
                if ($this->db->insert("farm_produce_supply", $data_fp_supply)) {
                    $true += ["message"   => "Successfully created!"];
                    $ret = $true;
                }
            } else {
                $false += ["message"   => "Something went wrong!"];
                $ret = $false;
            }
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

    function saveAddFarmProduceSupply()
    {
        $this->db->trans_begin();
        $true = ["success"   => true];
        $false = ["success"   => false];
        $data = [];
        $person_id = $this->session->agrishop_person_id;
        $farm_produce_id = $this->input->post("fp_id");

        $data = [
            "farm_produce_id" => $farm_produce_id,
            "qty" => $this->input->post("qty_add"),
            "created_by_person_id" => $this->session->agrishop_person_id,
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