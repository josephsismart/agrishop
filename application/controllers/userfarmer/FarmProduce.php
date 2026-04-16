<?php
defined('BASEPATH') or exit('No direct script access allowed');

class FarmProduce extends MY_Controller
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
            "page_title"        => "Farm & Produce",
            "current_location"  => "FarmProduce",
            "content"           =>  [$this->load->view('interface/' . $uri . '/FarmProduce', [
                "billing" => $this->subscription_count(),
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
        $thisQuery = $this->db->query("SELECT COUNT(1) AS total FROM farmer_farm 
                                    WHERE farmer_id = $farmer_id AND CONCAT(farm_name,(CASE WHEN is_active = true THEN 'ACTIVE' ELSE 'INACTIVE' END)) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'");
        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT * FROM farmer_farm 
                                    WHERE farmer_id = $farmer_id AND CONCAT(farm_name,(CASE WHEN is_active = true THEN 'ACTIVE' ELSE 'INACTIVE' END)) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
                                    ORDER BY created_at DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        $cc = $offset + 1;
        foreach ($query->result() as $key => $value) {
            $poi = null;
            $is_a_v = $value->is_active;
            $img_src = (!empty($value->img_path) && file_exists(FCPATH . $value->img_path))
                ? base_url($value->img_path)
                : base_url('dist/img/media/icons/1x1.png');
            $image_path = "<img src='$img_src' width='55' height='55' class='rounded shadow-sm' style='object-fit:cover;'>";
            $is_active = $is_a_v < 1 ? "<span class='badge bg-danger'>INACTIVE</span>" : "<span class='badge bg-success'>ACTIVE</span>";
            $produce_count = $this->db->query("SELECT COUNT(1) AS c FROM farm_produce WHERE farm_id=?", [$value->id])->row()->c ?? 0;
            $actions = "<div class='d-flex' style='gap:4px;'>"
                . "<button class='btn btn-xs btn-warning' onclick='editFarm({$value->id})' title='Edit Farm'><i class='fa fa-edit'></i> Edit</button>"
                . "</div>";
            $data[] = array(
                $image_path,
                "<div style='line-height:1.3;'>"
                    . "<div style='font-weight:700;font-size:13px;'>" . htmlspecialchars($value->farm_name) . "</div>"
                    . "<span class='badge badge-info' style='font-size:10px;'>$produce_count produce</span>"
                    . "</div>",
                "<small>" . $this->getAddress2($value->barangay_id) . "</small>",
                "<span style='font-weight:600;'>" . number_format($value->total_area_sqm) . "</span> <small class='text-muted'>sqm</small>",
                $is_active,
                $actions,
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
        $selling_type = $this->session->agrishop_login_farmer_selling_type;
        if ($selling_type == 1) {
            $where = "WHERE id < 9";
        } else if ($selling_type == 2) {
            $where = "WHERE id = 9";
        } else {
            $where = "";
        }

        $query_classification = "SELECT * FROM produce_classification $where";

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Query to get total record count
        $thisQuery = $this->db->query("SELECT COUNT(1) AS total
                                            FROM produce p
                                            JOIN ($query_classification) pc ON p.produce_classification_id = pc.id
                                        WHERE (p.created_by_person_id = $person_id OR p.created_by_person_id IS NULL) AND 
                                        CONCAT(p.name, pc.class_name, p.description, p.tags) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'");

        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT * FROM (
                                        SELECT c.id, c.name AS produce, pc.class_name, c.description,
                                            c.is_seasonal, c.is_active, c.created_at, c.created_by_person_id, c.img_path,pc.img_path as default_img_path, c.tags, c.is_customized
                                        FROM produce c
                                        JOIN ($query_classification) pc ON c.produce_classification_id = pc.id
                                        WHERE c.created_by_person_id = $person_id OR c.created_by_person_id IS NULL) AS x
                                    WHERE CONCAT(x.produce, x.class_name, x.description, x.tags) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
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
            $is_active = $is_a_v == true ? "<span class='badge bg-success'>ACTIVE</span>" : "<span class='badge bg-danger'>INACTIVE</span>";
            $is_seasonal = $value->is_seasonal == true ? "<span class='badge bg-blue'>SEASONAL</span>" : "<span class='badge bg-gray'>NON-SEASONAL</span>";
            $image_path = "<img src='$img' width='50' height='50' class='rounded' data-toggle='tooltip' data-placement='top' title=''>";
            $produce = $value->created_by_person_id == $person_id ? "<span class='badge bg-orange text-white'>" . $value->produce . "</span>" : $value->produce;
            // Only show edit/delete for custom (farmer-created) produce
            $person_id_session = $this->session->agrishop_person_id;
            $is_custom = ($value->is_customized == 1 && $value->created_by_person_id == $person_id_session);
            $produce_actions = $is_custom
                ? "<div class='d-flex' style='gap:4px;'>"
                . "<button class='btn btn-xs btn-warning' onclick='editProduceItem({$value->id})' title='Edit'><i class='fa fa-edit'></i></button>"
                . "</div>"
                : "<span class='badge badge-secondary' style='font-size:10px;'>System</span>";

            $data[] = array(
                $image_path,
                $produce,
                $value->class_name,
                $is_seasonal,
                $is_active,
                $produce_actions,
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
        $selling_type = $this->session->agrishop_login_farmer_selling_type;
        if ($selling_type == 1) {
            $where = "WHERE id < 9";
        } else if ($selling_type == 2) {
            $where = "WHERE id = 9";
        } else {
            $where = "";
        }
        $query_classification = "SELECT * FROM produce_classification $where";


        $query = $this->db->query("SELECT x.*,ppi.days_to_harvest,ppi.category,ppi.life_span,ppi.harvest_frequency,ppi.yield_per_sqm_as_kg FROM (
                                        SELECT p.id, p.name AS produce, pc.class_name, p.description,
                                            p.is_seasonal, p.is_active, p.created_at, p.created_by_person_id, p.img_path, p.tags, pc.img_path as default_img_path
                                        FROM produce p
                                        JOIN ($query_classification) pc ON p.produce_classification_id = pc.id

                                        UNION ALL

                                        SELECT c.id, c.name AS produce, pc.class_name, c.description,
                                            c.is_seasonal, c.is_active, c.created_at, c.created_by_person_id, c.img_path, c.tags, pc.img_path as default_img_path
                                        FROM produce_customize c
                                        JOIN ($query_classification) pc ON c.produce_classification_id = pc.id
                                        WHERE c.created_by_person_id = $person_id
                                    ) AS x
                                    LEFT JOIN produce_plantation_info ppi on x.id = ppi.produce_id
                                    WHERE CONCAT(x.produce, x.tags) LIKE '%$keyword%'
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
                'days_to_harvest' => $row->days_to_harvest,
                'category' => $row->category,
                'life_span' => $row->life_span,
                'harvest_frequency' => $row->harvest_frequency,
                'yield_per_sqm_as_kg' => $row->yield_per_sqm_as_kg
            ];
        }

        echo json_encode($results);
    }

    function confirmFreeTrial()
    {
        $farmer_id  = (int) $this->session->agrishop_login_farmer_id;
        $confirm = $this->input->post("confirm");
        $expired = $this->input->post("expired");
        if ($confirm == true) {
            $this->db->query("UPDATE farmer SET free_sub_confirm = true WHERE id = $farmer_id");

            $data_session = [
                "agrishop_login_sub_free_confirmed" => "t"
            ];
        }
        $this->session->set_userdata($data_session);
    }

    function getReservedCount()
    {
        $farmer_id  = (int) $this->session->agrishop_login_farmer_id;
        echo $this->getTransactionStatus($farmer_id, 'RESERVED', 'farmer');
    }

    function getPrice()
    {
        $produce_id = (int) $this->input->post("produce_id");
        $person_id  = (int) $this->session->agrishop_person_id;
        $price_qty_left = $this->price_qty_left();

        $query = $this->db->query("SELECT
                                        t1.harvest_schedule,
                                        t1.price,
                                        COALESCE(t1.qty_sold, 0) AS qty_sold,
                                        t1.qty_left,
                                        t1.produce_id
                                    FROM ($price_qty_left) t1
                                    WHERE t1.farmer_person_id = $person_id
                                    AND t1.produce_id = $produce_id
                                    LIMIT 1");

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


    public function getPriceHistory()
    {
        $fp_id     = $this->input->post('fp_id');
        $farmer_id = $this->session->agrishop_login_farmer_id;

        if (!$fp_id) {
            echo json_encode([]);
            return;
        }

        // Security: verify fp belongs to this farmer
        $check = $this->db->query("
            SELECT fp.id FROM farm_produce fp
            JOIN farmer_farm ff ON fp.farm_id = ff.id
            WHERE fp.id = ? AND ff.farmer_id = ?
            LIMIT 1
        ", [$fp_id, $farmer_id])->row();

        if (!$check) {
            echo json_encode([]);
            return;
        }

        $history = $this->db->query("
            SELECT
                DATE_FORMAT(created_at, '%b %d, %Y %h:%i %p') AS created_at,
                price,
                price_wholesale,
                wholesale_at_qty,
                is_latest
            FROM price_monitoring_farm_produce
            WHERE farm_produce_id = ?
            ORDER BY id DESC
        ", [$fp_id])->result();

        echo json_encode($history ?: []);
    }

    // ── Update price of a farm produce ────────────────────────
    public function updatePrice()
    {
        $this->db->trans_begin();
        $true  = ["success" => true];
        $false = ["success" => false];

        $fp_id         = $this->input->post('fp_id');
        $price         = $this->input->post('price');
        $price_whl     = $this->input->post('price_wholesale') ?: null;
        $whl_qty       = $this->input->post('wholesale_at_qty') ?: null;
        $person_id     = $this->session->agrishop_person_id;
        $farmer_id     = $this->session->agrishop_login_farmer_id;

        if (!$fp_id || !$price) {
            echo json_encode(["fill" => true, "message" => "Please enter a price."]);
            return;
        }

        // Security check
        $check = $this->db->query("
            SELECT fp.id FROM farm_produce fp
            JOIN farmer_farm ff ON fp.farm_id = ff.id
            WHERE fp.id = ? AND ff.farmer_id = ?
            LIMIT 1
        ", [$fp_id, $farmer_id])->row();

        if (!$check) {
            echo json_encode(["success" => false, "message" => "Unauthorized."]);
            return;
        }

        // Mark previous price as not latest
        $this->db->update(
            "price_monitoring_farm_produce",
            ["is_latest" => false],
            ["farm_produce_id" => $fp_id]
        );

        // Insert new price
        $data = [
            "farm_produce_id"      => $fp_id,
            "price"                => $price,
            "price_wholesale"      => $price_whl,
            "wholesale_at_qty"     => $whl_qty,
            "is_latest"            => true,
            "created_by_person_id" => $person_id,
            "created_at"           => date('Y-m-d H:i:s'),
        ];

        if ($this->db->insert("price_monitoring_farm_produce", $data)) {
            $true += ["message" => "Price updated successfully!"];
            $ret   = $true;
        } else {
            $false += ["message" => "Something went wrong!"];
            $ret    = $false;
        }

        $this->db->trans_status() === false
            ? $this->db->trans_rollback()
            : $this->db->trans_commit();

        echo json_encode($ret);
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
        $price_qty_left = $this->price_qty_left();
        // Query to get total record count
        $thisQuery = $this->db->query("SELECT count(1) AS total FROM farm_produce fp
                                    LEFT JOIN produce p ON fp.produce_id = p.id
                                    LEFT JOIN produce_classification pc ON p.produce_classification_id = pc.id
                                    LEFT JOIN ($price_qty_left) pql ON fp.farm_id = pql.farm_id AND fp.produce_id = pql.produce_id
                                    WHERE fp.farm_id = $farm_id AND CONCAT(p.name,pc.class_name,p.description,(CASE WHEN p.is_seasonal = true THEN 'SEASONAL' ELSE 'NON-SEASONAL' END),(CASE WHEN p.is_active = true THEN 'ACTIVE' ELSE 'INACTIVE' END)) 
                                    LIKE '%$searchValue%'");

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
        $query = $this->db->query("SELECT fp.id as fp_id,fp.specification_variety,p.id,p.name as produce,pql.harvest_schedule,pql.uom,pql.price,pql.qty_left ,pc.class_name,p.description,pql.price_wholesale,pql.wholesale_at_qty,
                                    p.is_seasonal,p.is_active,p.created_at, p.img_path, pc.img_path as default_img_path 
                                    FROM farm_produce fp
                                    LEFT JOIN produce p ON fp.produce_id = p.id
                                    LEFT JOIN produce_classification pc ON p.produce_classification_id = pc.id
                                    LEFT JOIN ($price_qty_left) pql ON fp.farm_id = pql.farm_id AND fp.produce_id = pql.produce_id
                                    WHERE fp.farm_id = $farm_id AND CONCAT(p.name,pc.class_name,p.description,(CASE WHEN p.is_seasonal = true THEN 'SEASONAL' ELSE 'NON-SEASONAL' END),(CASE WHEN p.is_active = true THEN 'ACTIVE' ELSE 'INACTIVE' END)) 
                                    LIKE '%$searchValue%'
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
            $is_active = $is_a_v == true
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

            $is_seasonal = $value->is_seasonal == true
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
                        <button class='btn bg-navy btn-sm px-3 py-2 shadow-sm' 
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
                                    is_active: \"$value->is_active\",
                                    wholesale_price: \"$value->price_wholesale\",
                                    wholesale_qty: \"$value->wholesale_at_qty\"
                                })'>
                            <i class='fas fa-plus mr-1'></i> ADD MORE
                        </button>
                    </div>";

            // Format numbers with better readability
            $formatted_qty = number_format($value->qty_left, 2);
            $formatted_price = "₱ " . number_format($value->price, 2);
            $formatted_wholesale_price = $value->price_wholesale ? "₱ " . number_format($value->price_wholesale, 2) : "";

            // Format date for better display
            $formatted_date = date('M d, Y', strtotime($value->harvest_schedule));

            // Beautified Produce Name with category
            $produce_display = "<div>
                            <div class='font-weight-bold text-dark' style='font-size: 1.1rem; mb-n1'>
                                " . htmlspecialchars($value->produce, ENT_QUOTES) . "
                            </div>
                            <span class='text-muted small mt-n2'>
                                " . htmlspecialchars($value->specification_variety, ENT_QUOTES) . "
                            </span>
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
                         
                        <button class='btn btn-xs btn-warning mr-1'
                            onclick='openPriceManager({$value->fp_id},\"{$value->produce}\",{$value->price})'
                            title='Manage Price'>
                            <i class='fa fa-tags'></i> ₱" . number_format($value->price, 2) . "
                        </button>
                         <div class='text-muted small'>
                             per " . htmlspecialchars($value->uom, ENT_QUOTES) . "
                         </div>
                      </div>"
                . ($value->price_wholesale ? "<div class='badge bg-gray'  title='Wholesale Price' whole-sale>ws@ $formatted_wholesale_price (min " . $value->wholesale_at_qty . ")</div>" : "");

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

            // "Remove Posting" = set is_active=false so it's hidden from search (not hard delete)
            $remove_posting_btn = "<button class='btn btn-xs btn-secondary ml-1'"
                . " onclick='removePosting({$value->fp_id})'"
                . " title='Remove posting (hide from buyers)'>"
                . "<i class='fa fa-eye-slash'></i></button>";

            $data[] = array(
                $add_produce,// . $remove_posting_btn,  // Action buttons
                $image_path,                 // Product image
                $produce_display,            // Product name + category
                $date_display,               // Harvest date
                $quantity_display,           // Quantity left
                $price_display,              // Price
                $is_seasonal,
                $is_active,                  // Active status
            );
        }
        // foreach ($query->result() as $key => $value) {
        //     $poi = null;
        //     $is_a_v = $value->is_active;
        //     $img = (!empty($value->img_path) && file_exists(FCPATH . $value->img_path))
        //         ? base_url($value->img_path)
        //         : base_url($value->default_img_path);
        //     $is_active = $is_a_v == true ? "<span class='badge bg-success'>ACTIVE</span>" : "<span class='badge bg-danger'>INACTIVE</span>";
        //     $is_seasonal = $value->is_seasonal == true ? "<span class='badge bg-blue'>SEASONAL</span>" : "<span class='badge bg-gray'>NON-SEASONAL</span>";
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
        $farmer_id = $this->session->agrishop_login_farmer_id;

        $person_id = $this->session->agrishop_person_id;
        $exist = $this->db->query("SELECT * FROM farmer_farm WHERE farm_name = '$farmName' and created_by_person_id = $person_id")->num_rows();
        if ($exist > 0) {
            $false += ["message"   => "Farm already exists!", "exist"   => true];
            $ret = $false;
            echo json_encode($ret);
            return;
        }


        $data = [
            "farmer_id" => $farmer_id,
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


        $exist = $this->db->query("SELECT * FROM produce WHERE name = '$produceName' and created_by_person_id = $person_id")->num_rows();
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
        $specification_variety = $this->input->post("specification_variety");
        $produceCreatedById = $this->input->post("produceCreatedById");
        $wholesale_min_qty = $this->input->post("wholesale_min_qty");
        $wholesale_price = $this->input->post("wholesale_price");
        $qty_add = $this->input->post("qty_add");
        $price = $this->input->post("price");
        $uom = $this->input->post("uom");
        $harvest_date = $this->input->post("harvest_date");

        $exist = $this->db->query("SELECT * FROM farm_produce WHERE farm_id = $farmId 
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
            "specification_variety" => strtoupper($specification_variety),
            "harvest_schedule" => $harvest_date,
            "uom" => $uom,
            "created_at" => Date("Y-m-d"),
            "created_by_person_id" => $person_id,
        ];

        if ($this->db->insert("farm_produce", $data)) {
            $farm_produce_id = $this->db->insert_id();

            // Handle produce image upload
            if (isset($_FILES['picProduce']) && $_FILES['picProduce']['error'] === UPLOAD_ERR_OK) {
                $upload = $this->uploadImg($_FILES['picProduce'], 'produce_' . $produceSelectedId, 'produce', 'picProduce');
                $this->db->update('produce', ['img_path' => $upload], ['id' => $produceSelectedId]);
            }

            $data_price_monitor = [
                "farm_produce_id" => $farm_produce_id,
                "price" => $price,
                "created_by_person_id" => $person_id,
            ];
            if ($wholesale_min_qty != null && $wholesale_price != null) {
                $data_price_monitor += [
                    "wholesale_at_qty" => $wholesale_min_qty,
                    "price_wholesale" => $wholesale_price,
                ];
            }
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

    // ── Update farm ────────────────────────────────────────────
    public function updateFarmInfo()
    {
        $farm_id   = (int) $this->input->post('farm_id');
        $farmer_id = (int) $this->session->agrishop_login_farmer_id;
        $person_id = (int) $this->session->agrishop_person_id;

        $data = [
            'farm_name'      => strtoupper($this->input->post('farmName')),
            'barangay_id'    => $this->input->post('barangay') ?: null,
            'total_area_sqm' => $this->input->post('totalAreaSqm'),
            'lat'            => $this->input->post('lat'),
            'lon'            => $this->input->post('lon'),
        ];

        if (!empty($_FILES['picFarm']['name'])) {
            $upload = $this->uploadImg($_FILES['picFarm'], $data['farm_name'], 'farm', 'picFarm');
            if ($upload) $data['img_path'] = $upload;
        }

        $this->db->update('farmer_farm', $data, ['id' => $farm_id, 'farmer_id' => $farmer_id]);
        echo json_encode(['success' => $this->db->affected_rows() >= 0, 'message' => 'Farm updated!']);
    }

    // ── Get single farm for edit ────────────────────────────────
    public function getFarmById()
    {
        $farm_id   = (int) $this->input->get('id');
        $farmer_id = (int) $this->session->agrishop_login_farmer_id;
        $row = $this->db->query(
            "SELECT * FROM farmer_farm WHERE id=? AND farmer_id=? LIMIT 1",
            [$farm_id, $farmer_id]
        )->row();
        if ($row) {
            $row->barangay_text = $this->getAddress2($row->barangay_id);
        }
        echo json_encode($row ?: null);
    }

    // ── Delete farm ─────────────────────────────────────────────
    public function deleteFarm()
    {
        $farm_id   = (int) $this->input->post('farm_id');
        $farmer_id = (int) $this->session->agrishop_login_farmer_id;
        // Check if farm has produce
        $has_produce = $this->db->query(
            "SELECT COUNT(1) AS c FROM farm_produce WHERE farm_id=?",
            [$farm_id]
        )->row()->c;
        if ($has_produce > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete: farm has produce assigned. Remove produce first.']);
            return;
        }
        $this->db->delete('farmer_farm', ['id' => $farm_id, 'farmer_id' => $farmer_id]);
        echo json_encode(['success' => true, 'message' => 'Farm deleted.']);
    }

    // ── Update produce (custom only) ────────────────────────────
    public function updateProduceInfo()
    {
        $produce_id = (int) $this->input->post('produce_id');
        $person_id  = (int) $this->session->agrishop_person_id;

        $data = [
            'name'                     => strtoupper($this->input->post('produceName')),
            'produce_classification_id' => $this->input->post('classification'),
            'description'              => $this->input->post('description'),
            'is_seasonal'              => $this->input->post('seasonal') ? 1 : 0,
            'tags'                     => strtoupper($this->input->post('tags')),
        ];
        if (!empty($_FILES['picProduce']['name'])) {
            $upload = $this->uploadImg($_FILES['picProduce'], $data['name'], 'produce', 'picProduce');
            if ($upload) $data['img_path'] = $upload;
        }
        // Only allow edit of farmer's own custom produce
        $this->db->update('produce', $data, ['id' => $produce_id, 'created_by_person_id' => $person_id, 'is_customized' => 1]);
        echo json_encode(['success' => $this->db->affected_rows() >= 0, 'message' => 'Produce updated!']);
    }

    // ── Get produce for edit ────────────────────────────────────
    public function getProduceById()
    {
        $id        = (int) $this->input->get('id');
        $person_id = (int) $this->session->agrishop_person_id;
        $row = $this->db->query(
            "SELECT * FROM produce WHERE id=? AND created_by_person_id=? LIMIT 1",
            [$id, $person_id]
        )->row();
        echo json_encode($row ?: null);
    }

    // ── Delete farm produce entry ───────────────────────────────
    public function deleteFarmProduce()
    {
        $fp_id     = (int) $this->input->post('fp_id');
        $farmer_id = (int) $this->session->agrishop_login_farmer_id;
        // Verify ownership
        $ok = $this->db->query("
            SELECT fp.id FROM farm_produce fp
            JOIN farmer_farm ff ON fp.farm_id = ff.id
            WHERE fp.id=? AND ff.farmer_id=? LIMIT 1
        ", [$fp_id, $farmer_id])->num_rows() > 0;

        if (!$ok) {
            echo json_encode(['success' => false, 'message' => 'Not authorized.']);
            return;
        }

        // Check if it has transactions
        $has_orders = $this->db->query(
            "SELECT COUNT(1) AS c FROM my_cart_farm_produce WHERE farm_produce_id=?",
            [$fp_id]
        )->row()->c;
        if ($has_orders > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete: this produce has existing orders.']);
            return;
        }
        $this->db->delete('farm_produce', ['id' => $fp_id]);
        echo json_encode(['success' => true, 'message' => 'Produce removed from farm.']);
    }



    // ── Remove Posting — hides produce from buyer search (sets is_active=0) ──
    public function removePosting()
    {
        $fp_id     = (int) $this->input->post('fp_id');
        $farmer_id = (int) $this->session->agrishop_login_farmer_id;

        // Verify ownership
        $ok = $this->db->query("
            SELECT fp.id FROM farm_produce fp
            JOIN farmer_farm ff ON fp.farm_id = ff.id
            WHERE fp.id=? AND ff.farmer_id=? LIMIT 1
        ", [$fp_id, $farmer_id])->num_rows() > 0;

        if (!$ok) {
            echo json_encode(['success' => false, 'message' => 'Not authorized.']);
            return;
        }

        // Just deactivate the produce listing - does NOT delete data
        $this->db->query("
            UPDATE farm_produce fp
            JOIN farmer_farm ff ON fp.farm_id = ff.id
            SET fp.harvest_schedule = NULL
            WHERE fp.id = ? AND ff.farmer_id = ?
        ", [$fp_id, $farmer_id]);

        // Alternative: mark produce as inactive via price_monitoring
        // For now we just hide it from current listings by nulling harvest
        echo json_encode(['success' => true, 'message' => 'Posting removed. Produce hidden from buyers.']);
    }

    // ── Restore posting ────────────────────────────────────────
    public function restorePosting()
    {
        $fp_id     = (int) $this->input->post('fp_id');
        $farmer_id = (int) $this->session->agrishop_login_farmer_id;
        $harvest   = $this->input->post('harvest_date');

        $ok = $this->db->query("
            SELECT fp.id FROM farm_produce fp
            JOIN farmer_farm ff ON fp.farm_id = ff.id
            WHERE fp.id=? AND ff.farmer_id=? LIMIT 1
        ", [$fp_id, $farmer_id])->num_rows() > 0;

        if (!$ok) {
            echo json_encode(['success' => false, 'message' => 'Not authorized.']);
            return;
        }

        $this->db->query("
            UPDATE farm_produce fp
            JOIN farmer_farm ff ON fp.farm_id = ff.id
            SET fp.harvest_schedule = ?
            WHERE fp.id = ? AND ff.farmer_id = ?
        ", [$harvest ?: date('Y-m-d'), $fp_id, $farmer_id]);

        echo json_encode(['success' => true, 'message' => 'Posting restored!']);
    }
}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */