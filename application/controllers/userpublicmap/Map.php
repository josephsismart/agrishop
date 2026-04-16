<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Map extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SQL_BIG_SELECTS=1');
        //$this->redirect();

        $this->load->model('mainModel');
        $this->load->helper('date');
        date_default_timezone_set("Asia/Manila");
    }

    public function getSupplyCartCount()
    {
        $person_id = $this->session->agrishop_person_id;
        if (!$person_id) {
            echo json_encode(['count' => 0]);
            return;
        }

        $result = $this->db->query("
            SELECT COUNT(1) AS count
            FROM my_cart_supply mcs
            JOIN transaction t ON mcs.transaction_id = t.id
            JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE AND status = 'PENDING') ts ON t.id = ts.transaction_id
            WHERE t.person_id = ?
        ", [$person_id]);

        $count = ($result && $result->row()) ? (int) $result->row()->count : 0;
        echo json_encode(['count' => $count]);
    }

    function searchProduce()
    {
        $data =  [];
        $value = $this->input->post("value");
        $price_qty_left = $this->price_qty_left();
        foreach ($this->db->query("SELECT
                                    fp.farm_id,
                                    ff.img_path,
                                    ff.farm_name,
                                    p.img_path AS farmer_img_path,
                                    CONCAT(p.first_name,' ',p.last_name) AS farmer_name,
                                    ff.barangay_id,
                                    ff.lat,
                                    ff.lon,
                                    p.email_address,
                                    p.contact_num,
                                    CONCAT('[', GROUP_CONCAT(
                                        JSON_OBJECT(
                                            'id', fp.produce_id,
                                            'img_path', fp.produce_img_path,
                                            'name', fp.produce_name,
                                            'harvest_at', fp.harvest_schedule,
                                            'price', fp.price,
                                            'uom', fp.uom,
                                            'qty_left', fp.qty_left,
                                            'price_wholesale', fp.price_wholesale,
                                            'wholesale_at_qty', fp.wholesale_at_qty
                                        )
                                        ORDER BY fp.harvest_schedule
                                    ), ']') AS produce
                                FROM (
                                    SELECT
                                        t11.*,
                                        t22.img_path AS produce_img_path,
                                        t22.name AS produce_name,
                                        ROW_NUMBER() OVER (
                                            PARTITION BY t11.farm_id
                                            ORDER BY t11.harvest_schedule DESC
                                        ) AS rn
                                    FROM ($price_qty_left) t11
                                    JOIN produce t22 ON t11.produce_id = t22.id
                                    WHERE
                                        CONCAT(t22.name, t22.tags) COLLATE utf8mb4_general_ci LIKE '%$value%'
                                ) fp
                                LEFT JOIN farmer_farm ff ON fp.farm_id = ff.id
                                LEFT JOIN farmer f ON ff.farmer_id = f.id
                                LEFT JOIN person p ON f.person_id = p.id
                                WHERE fp.rn <= 2
                                GROUP BY
                                    fp.farm_id,
                                    ff.img_path,
                                    ff.farm_name,
                                    p.img_path,
                                    p.contact_num,
                                    p.email_address,
                                    CONCAT(p.first_name,' ',p.last_name),
                                    ff.barangay_id,
                                    ff.lat,
                                    ff.lon
                                ORDER BY fp.harvest_schedule")->result() as $key => $value) {
            $farm_address = $this->getAddress2($value->barangay_id);
            $farm_image = $value->img_path ? base_url($value->img_path) : base_url('dist/img/media/icons/1x1.png');
            $farmer_image = $value->farmer_img_path ? base_url($value->farmer_img_path) : base_url('dist/img/media/icons/1x1.png');
            $farm_image_path = "<img src='$farm_image' width='50' height='50' class='rounded pr-2' data-toggle='tooltip' data-placement='top' title=''>";
            $farmer_image_path = "<img src='$farmer_image' width='50' height='50' class='rounded pr-2' data-toggle='tooltip' data-placement='top' title=''>";
            $data[] = [
                "id"           => $value->farm_id,
                "farm_img_path"  => $farm_image_path,
                "farm_img_url"   => $farm_image,
                "farm_name"    => $value->farm_name,
                "farm_location"  => $farm_address,
                "farmer_img_path" => $farmer_image_path,
                "farmer_img_url"  => $farmer_image,
                "farmer_name"  => $value->farmer_name,
                "lat"  => $value->lat,
                "lon"  => $value->lon,
                "produce"    => $value->produce,
                "farmerContact" => "Contact: " . $value->contact_num . " | Email: " . $value->email_address
            ];
        }
        echo json_encode($data);
    }


    public function getSuppliesShop()
    {
        $requestData = $_REQUEST;
        $keyword     = isset($requestData['search']['value'])   ? $requestData['search']['value']   : '';
        $category_id = isset($requestData['search']['category']) ? $requestData['search']['category'] : '';
        $searchValue = $this->db->escape_like_str($keyword);

        list($limit, $offset) = $this->calculatePagination($requestData);

        $cat_where = $category_id ? "AND ss.supply_category_id = " . (int)$category_id : "";

        $totalRecords = $this->db->query("
            SELECT COUNT(1) AS total
            FROM supplier_supply ss
            JOIN supply_category sc ON ss.supply_category_id = sc.id
            JOIN supplier sup ON ss.supplier_id = sup.id AND sup.is_active = 1
            WHERE ss.is_active = 1 AND ss.qty_available > 0
              $cat_where
              AND CONCAT(ss.name, COALESCE(ss.brand,''), sc.name, COALESCE(ss.tags,''))
                  COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
        ")->row()->total ?? 0;

        $query = $this->db->query("
            SELECT ss.id, ss.name, ss.brand, ss.uom, ss.price, ss.qty_available,
                   ss.img_path, ss.description,
                   sc.name AS category,
                   st.store_name, st.lat, st.lon, st.address_text,
                   CONCAT(p.first_name,' ',p.last_name) AS supplier_name,
                   p.contact_num
            FROM supplier_supply ss
            JOIN supply_category sc ON ss.supply_category_id = sc.id
            JOIN supplier sup ON ss.supplier_id = sup.id AND sup.is_active = 1
            JOIN person p ON sup.person_id = p.id
            LEFT JOIN supplier_store st ON ss.store_id = st.id
            WHERE ss.is_active = 1 AND ss.qty_available > 0
              $cat_where
              AND CONCAT(ss.name, COALESCE(ss.brand,''), sc.name, COALESCE(ss.tags,''))
                  COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
            ORDER BY ss.name ASC
            LIMIT $limit OFFSET $offset
        ");

        if (!$query) {
            echo json_encode(['draw' => intval($requestData['draw'] ?? 1), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
            return;
        }

        // Category colour map
        $catColors = [
            'PESTICIDE'  => ['bg' => '#fef3c7', 'color' => '#92400e'],
            'SEEDS'      => ['bg' => '#d1fae5', 'color' => '#065f46'],
            'TOOLS'      => ['bg' => '#dbeafe', 'color' => '#1e40af'],
            'FEEDS'      => ['bg' => '#ede9fe', 'color' => '#5b21b6'],
            'FERTILIZER' => ['bg' => '#fce7f3', 'color' => '#9d174d'],
            'EQUIPMENT'  => ['bg' => '#e0f2fe', 'color' => '#0369a1'],
        ];

        $data = [];
        foreach ($query->result() as $v) {
            $catKey = strtoupper($v->category);
            $catBg  = isset($catColors[$catKey]) ? $catColors[$catKey]['bg']    : '#f3f4f6';
            $catFg  = isset($catColors[$catKey]) ? $catColors[$catKey]['color']  : '#374151';

            if (!empty($v->img_path) && file_exists(FCPATH . $v->img_path)) {
                $imgHtml = "<img src='" . base_url($v->img_path) . "' style='width:72px;height:72px;object-fit:cover;border-radius:10px;flex-shrink:0;'>";
            } else {
                $imgHtml = "<div style='width:72px;height:72px;background:#f0fdf4;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;'><i class='fa fa-seedling' style='color:#16a34a;font-size:28px;'></i></div>";
            }

            $stockBg    = $v->qty_available <= 10 ? '#fee2e2' : '#d1fae5';
            $stockColor = $v->qty_available <= 10 ? '#991b1b' : '#065f46';
            $stockIcon  = $v->qty_available <= 10 ? 'fa-exclamation-circle' : 'fa-check-circle';
            $location   = $v->store_name ? htmlspecialchars($v->store_name) . ($v->address_text ? ' · ' . htmlspecialchars($v->address_text) : '') : '—';

            $mapBtn = $v->lat
                ? "<button onclick='viewOnMap({$v->lat},{$v->lon},\"" . addslashes($v->store_name) . "\")' "
                .  "style='display:inline-flex;align-items:center;justify-content:center;padding:6px 10px;border:1px solid #d1d5db;border-radius:8px;background:#fff;color:#374151;cursor:pointer;font-size:12px;'>"
                .  "<i class='fa fa-map-marker-alt'></i></button>"
                : '';

            $nameEncoded = json_encode(htmlspecialchars($v->name));

            $card = "
<div style='display:flex;align-items:flex-start;gap:14px;padding:14px 16px;border-bottom:1px solid #f1f5f9;'>
  {$imgHtml}
  <div style='flex:1;min-width:0;'>

    <!-- Row 1: name + category badge -->
    <div style='display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:4px;'>
      <span style='font-weight:700;font-size:14px;color:#111827;'>" . htmlspecialchars($v->name) . "</span>
      <span style='background:{$catBg};color:{$catFg};font-size:10px;font-weight:600;padding:2px 8px;border-radius:20px;white-space:nowrap;'>{$v->category}</span>
    </div>

    <!-- Row 2: brand + location -->
    <div style='font-size:12px;color:#6b7280;margin-bottom:8px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;'>
      " . ($v->brand ? "<span style='font-weight:500;color:#374151;'>" . htmlspecialchars($v->brand) . "</span> &nbsp;·&nbsp; " : '') . "
      <i class='fa fa-map-marker-alt' style='color:#ef4444;font-size:10px;'></i> {$location}
    </div>

    <!-- Row 3: price | stock | qty + buttons -->
    <div style='display:flex;align-items:center;gap:8px;flex-wrap:wrap;'>
      <span style='background:#f0fdf4;color:#15803d;font-weight:700;font-size:14px;padding:4px 10px;border-radius:20px;'>
        ₱" . number_format($v->price, 2) . " <span style='font-size:11px;font-weight:400;'>/ {$v->uom}</span>
      </span>
      <span style='background:{$stockBg};color:{$stockColor};font-size:11px;font-weight:600;padding:3px 8px;border-radius:20px;'>
        <i class='fa {$stockIcon}' style='margin-right:2px;'></i>" . number_format($v->qty_available) . " {$v->uom}
      </span>
      <input type='number' id='qty_{$v->id}' min='1' max='{$v->qty_available}' value='1'
             style='width:58px;padding:4px 6px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;text-align:center;'>
      <button onclick='addSupplyToCartShop({$v->id},{$v->price},\"qty_{$v->id}\",{$nameEncoded})'
              style='display:inline-flex;align-items:center;gap:5px;padding:6px 12px;background:#f59e0b;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;'>
        <i class='fa fa-cart-plus'></i> Add
      </button>
      {$mapBtn}
    </div>

  </div>
</div>";

            $data[] = ['card' => $card];
        }

        echo json_encode([
            'draw'            => intval($requestData['draw'] ?? 1),
            'recordsTotal'    => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords),
            'data'            => $data,
        ]);
    }

    // ── Get supply categories for filter dropdown ─────────────
    public function getSupplyCategories()
    {
        $rows = $this->db->query("SELECT id, name FROM supply_category WHERE is_active = 1 ORDER BY name")->result();
        echo json_encode($rows ?: []);
    }


    function searchSupplier()
    {
        $data  = [];
        $value = $this->input->post("value");

        $rows = $this->db->query("
            SELECT
                st.id           AS store_id,
                st.store_name,
                st.lat,
                st.lon,
                st.img_path     AS store_img,
                st.address_text AS store_address,
                st.barangay_id,
                p.img_path      AS supplier_img,
                CONCAT(p.first_name,' ',p.last_name) AS supplier_name,
                p.contact_num,
                p.email_address,
                CONCAT('[', GROUP_CONCAT(
                    JSON_OBJECT(
                        'id',           ss.id,
                        'name',         ss.name,
                        'brand',        ss.brand,
                        'category',     sc.name,
                        'price',        ss.price,
                        'uom',          ss.uom,
                        'qty_available',ss.qty_available,
                        'img_path',     ss.img_path
                    )
                    ORDER BY ss.name
                ), ']') AS supplies
            FROM supplier_store st
            JOIN supplier sup    ON st.supplier_id = sup.id AND sup.is_active = 1
            JOIN person p        ON sup.person_id = p.id
            JOIN supplier_supply ss ON ss.store_id = st.id AND ss.is_active = 1 AND ss.qty_available > 0
            JOIN supply_category sc ON ss.supply_category_id = sc.id
            WHERE st.is_active = 1
              AND st.lat IS NOT NULL
              AND st.lon IS NOT NULL
              AND CONCAT(ss.name, ss.brand, ss.tags, sc.name) COLLATE utf8mb4_general_ci LIKE ?
            GROUP BY
                st.id, st.store_name, st.lat, st.lon,
                st.img_path, st.address_text, st.barangay_id,
                p.img_path, p.contact_num, p.email_address,
                CONCAT(p.first_name,' ',p.last_name)
            ORDER BY st.store_name
        ", ['%' . $value . '%'])->result();

        foreach ($rows as $row) {
            $store_address  = $row->store_address ?: $this->getAddress2($row->barangay_id);
            $store_img      = $row->store_img
                ? base_url($row->store_img)
                : base_url('dist/img/media/icons/1x1.png');
            $supplier_img   = $row->supplier_img
                ? base_url($row->supplier_img)
                : base_url('dist/img/media/icons/1x1.png');

            $data[] = [
                "id"             => $row->store_id,
                "store_name"     => $row->store_name,
                "store_img"      => "<img src='$store_img' width='50' height='50' class='rounded'>",
                "store_address"  => $store_address,
                "supplier_name"  => $row->supplier_name,
                "supplier_img"   => "<img src='$supplier_img' width='40' height='40' class='rounded-circle'>",
                "lat"            => $row->lat,
                "lon"            => $row->lon,
                "supplies"       => $row->supplies,
                "contact"        => "Contact: " . $row->contact_num . " | Email: " . $row->email_address,
            ];
        }

        echo json_encode($data);
    }

    function getFarmProduceList()
    {
        $requestData = $_REQUEST;
        $person_id  = $this->session->agrishop_login_id;
        $farm_id = $requestData['search']['farm_id'];
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Query to get total record count
        $price_qty_left = $this->price_qty_left();
        $thisQuery = $this->db->query("SELECT count(1) AS total FROM farm_produce fp
                                    LEFT JOIN produce p ON fp.produce_id = p.id
                                    LEFT JOIN produce_classification pc ON p.produce_classification_id = pc.id
                                    LEFT JOIN ($price_qty_left) pql ON fp.farm_id = pql.farm_id AND fp.produce_id = pql.produce_id
                                    WHERE fp.farm_id = $farm_id AND CONCAT(p.name,p.tags,pc.class_name,p.description,(CASE WHEN p.is_seasonal = true THEN 'SEASONAL' ELSE 'NON-SEASONAL' END),(CASE WHEN p.is_active = true THEN 'ACTIVE' ELSE 'INACTIVE' END)) 
                                    LIKE '%$searchValue%'");

        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT ROW_NUMBER() OVER (ORDER BY fp.id DESC) AS row_num, fp.id as fp_id,p.id,p.name as produce,p.tags,pql.harvest_schedule,pql.uom,pql.price,pql.qty_left,pql.latest_price_id,pc.class_name,p.description,
                                    p.is_seasonal,p.is_active,p.created_at, p.img_path,pc.img_path as default_img_path, fp.farm_id, pql.wholesale_at_qty,pql.price_wholesale
                                    FROM farm_produce fp
                                    LEFT JOIN produce p ON fp.produce_id = p.id
                                    LEFT JOIN produce_classification pc ON p.produce_classification_id = pc.id
                                    LEFT JOIN ($price_qty_left) pql ON fp.farm_id = pql.farm_id AND fp.produce_id = pql.produce_id
                                    WHERE fp.farm_id = $farm_id AND CONCAT(p.name,p.tags,pc.class_name,p.description,(CASE WHEN p.is_seasonal = true THEN 'SEASONAL' ELSE 'NON-SEASONAL' END),(CASE WHEN p.is_active = true THEN 'ACTIVE' ELSE 'INACTIVE' END)) 
                                    LIKE '%$searchValue%'
                                    ORDER BY p.created_at DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        $cc = 0;
        foreach ($query->result() as $key => $value) {

            // ---------- SAFE VALUES ----------
            $produce = htmlspecialchars($value->produce, ENT_QUOTES, 'UTF-8');
            $tags = htmlspecialchars($value->tags ?? '', ENT_QUOTES, 'UTF-8');
            $uom = htmlspecialchars($value->uom, ENT_QUOTES, 'UTF-8');

            // ---------- IMAGE ----------
            $img = (!empty($value->img_path) && file_exists(FCPATH . $value->img_path))
                ? base_url($value->img_path)
                : base_url($value->default_img_path);

            $image_path = "
        <img src='{$img}'
             width='60'
             height='60'
             class='rounded shadow-sm border'
             style='object-fit:cover;'>";

            // ---------- STOCK COLOR ----------
            $stockColor = ($value->qty_left <= 10) ? "text-danger font-weight-bold" : "text-success font-weight-bold";

            $stock_display = "
                <span class='{$stockColor}' style='font-size:1.1rem;'>
                    {$value->qty_left}
                </span>
            ";

            // ---------- PRICE DISPLAY ----------
            $price_display = "
                <div class='font-weight-bold text-primary' style='font-size:1.1rem;'>
                    ₱" . number_format($value->price, 2) . "
                </div>
                <small class='text-muted' style='display: block;'>per {$uom}</small>
            " . ($value->wholesale_at_qty ? "<small class='badge bg-gray'>Wholesale:<br/> ₱" . number_format($value->price_wholesale, 2) . " per {$uom}<br/> @min {$value->wholesale_at_qty} qty</small>" : "") . "
            ";

            // ---------- QTY INPUT ----------
            $q_id = $value->row_num;

            $qty_input = "
                <input type='number'
                    style='width:90px;text-align:center;'
                    id='qty{$q_id}'
                    min='1'
                    max='{$value->qty_left}'
                    value='1'
                    class='form-control form-control-sm border-success font-weight-bold'>
            ";

            // ---------- ADD TO CART BUTTON ----------
            $add_to_cart = "
                <button class='btn btn-sm btn-warning shadow-sm'
                    onclick='add_to_cart({
                        id: \"{$value->fp_id}\",
                        id_: \"{$value->row_num}\",
                        img_path: \"{$img}\",
                        produce: \"{$produce}\",
                        harvest_schedule: \"{$value->harvest_schedule}\",
                        qty_left: \"{$value->qty_left}\",
                        price: \"{$value->price}\",
                        uom: \"{$uom}\",
                        class_name: \"{$value->class_name}\",
                        is_seasonal: \"{$value->is_seasonal}\",
                        is_active: \"{$value->is_active}\",
                        farm_id: \"{$value->farm_id}\",
                        latest_price_id: \"{$value->latest_price_id}\"
                    })'><b><i class='fa fa-shopping-cart'></i> Add</b>
                </button>
            ";

            // ---------- PRODUCE NAME ----------
            $produce_display = "
            <div class='font-weight-bold'>{$produce}</div>
            " . ($tags ? "<small class='text-muted' style='display: block;'>({$tags})</small>" : "");

            // ---------- FINAL DATA ----------
            $data[] = array(
                $add_to_cart,
                $qty_input,
                $image_path,
                $produce_display,
                '<span class="badge bg-white">' . $value->harvest_schedule . '</span>',
                $stock_display,
                $price_display
            );
        }
        // Prepare the response data in the required format
        $response = array(
            'draw' => intval($requestData['draw']),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords), // For simplicity, assuming no filtering is applied
            'data' => $data,
        );
        echo json_encode($response);
    }

    public function add_to_cart()
    {
        $this->db->trans_begin();
        $data = [];
        $item = $this->input->post("item");
        $qty = $this->input->post("qty");
        //  id,img_path,produce,harvest_schedule,qty_left,price,uom,class_name,is_seasonal,is_active,farm_id
        $farm_id = $item['farm_id'];
        parse_str($this->input->post("c") ?? '', $filter);
        $person_id = $this->session->agrishop_person_id;
        $farm_produce_id = $item['id'];
        $latest_price_id = $item['latest_price_id'];
        $dateNow = $this->now();
        $transaction_id = '';
        $transaction_status = '';
        $true = ["success"   => true];
        $false = ["success"   => false];
        $price_qty_left = $this->price_qty_left();
        $check_qty_left = $this->db->query("SELECT pql.qty_left, name FROM ($price_qty_left) pql
                                            LEFT JOIN produce p on pql.produce_id = p.id
                                            WHERE pql.id = $farm_produce_id LIMIT 1")->row();
        if ($check_qty_left->qty_left < $qty) {
            $false = ["success"   => false, "message" => "Quantity for " . $check_qty_left->name . " left is $check_qty_left->qty_left, not enough!"];
            echo json_encode($false);
            return;
        }


        $check = $this->db->query("SELECT * FROM transaction WHERE person_id = $person_id AND is_done = false AND farm_id = $farm_id LIMIT 1")->row();

        if ($check) {
            $transaction_id = $check->id;
        } else {

            $data = [
                'person_id' => $person_id,
                'total_sale_amount' => 1,
                'transaction_date' => $dateNow,
                'admin_percentage' => 1,
                'admin_sale_amount' => 0.1,
                'farm_id' => $farm_id,
            ];
            // $b = json_encode($data);
            if ($this->db->insert("transaction", $data)) {
                $transaction_id = $this->db->insert_id();
            } else {
                $false += ["message"   => "Transaction not created!"];
                $ret = $false;
            }
        }

        $ret = $false;
        $data_transaction_status = [
            'transaction_id' => $transaction_id,
            'status' => 'PENDING',
            'created_by_person_id' => $person_id,
        ];

        $this->transaction_status($data_transaction_status);

        // $check_exist_cart = $this->db->query("SELECT * FROM my_cart_farm_produce WHERE transaction_id = $transaction_id 
        //                                         AND farm_produce_id = $farm_produce_id 
        //                                         AND price_id_during_transact = $latest_price_id
        //                                         LIMIT 1")->row();

        // $data_my_cart = [
        //     'transaction_id' => $transaction_id,
        //     'farm_produce_id' => $farm_produce_id,
        //     'price_id_during_transact' => $latest_price_id,
        // ];
        // if ($check_exist_cart) {
        //     $data_my_cart += [
        //         'qty' => $qty + $check_exist_cart->qty,
        //         'sub_total' => ($qty + $check_exist_cart->qty) * $item['price'],
        //     ];
        // }else {
        //     $data_my_cart += [
        //         'qty' => $qty,
        //         'sub_total' => $item['price'] * $qty,
        //     ];
        // }

        // if ($check_exist_cart) {
        //     if ($this->db->update("my_cart_farm_produce", $data_my_cart, ["id" => $check_exist_cart->id])) {
        //         $true += ["message"   => "Added to cart!"];
        //         $ret = $true;
        //     } else {
        //         $false += ["message"   => "Failed to add to cart!"];
        //         $ret = $false;
        //     }
        // } else {
        //     if ($this->db->insert("my_cart_farm_produce", $data_my_cart)) {
        //         $true += ["message"   => "Added to cart!"];
        //         $ret = $true;
        //     } else {
        //         $false += ["message"   => "Failed to add to cart!"];
        //         $ret = $false;
        //     }
        // }

        $data_my_cart = [
            'transaction_id' => $transaction_id,
            'farm_produce_id' => $farm_produce_id,
            'price_id_during_transact' => $latest_price_id,
            'qty' => $qty,
            'created_at' => $dateNow
        ];

        $check_qty_left = $this->db->query("SELECT * FROM ($price_qty_left) pql WHERE id = $farm_produce_id AND pql.wholesale_at_qty <= $qty")->row();
        if ($check_qty_left) {
            $data_my_cart += [
                'is_wholesale' => true,
                'sub_total' => $check_qty_left->price_wholesale * $qty,
            ];
        } else {
            $data_my_cart += [
                'sub_total' => $item['price'] * $qty,
            ];
        }

        if ($this->db->insert("my_cart_farm_produce", $data_my_cart)) {
            $cp = $this->getTransactionStatus($person_id, 'PENDING', 'client');
            $true += ["message"   => "Added to cart!", "cart_pending"   => $cp, "cart" => $data_my_cart, "transaction" => $check];
            $ret = $true;
        } else {
            $false += ["message"   => "Failed to add to cart!"];
            $ret = $false;
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }

        echo json_encode($ret);
    }

    // ── Add promo deal to cart ────────────────────────────────────────────
    public function add_promo_to_cart()
    {
        $this->db->trans_begin();
        $true  = ["success" => true];
        $false = ["success" => false];

        $login_id  = $this->session->agrishop_login_id;
        $person_id = $this->session->agrishop_person_id;

        if (!$login_id) {
            echo json_encode(["success" => false, "login_required" => true]);
            return;
        }

        $promo_id = (int) $this->input->post('promo_id');
        $qty      = (int) $this->input->post('qty');
        $qty      = $qty < 1 ? 1 : $qty;

        // Fetch promo — must be active and still valid
        $today = date('Y-m-d');
        $promo = $this->db->query("
            SELECT pd.*,
                   ff.id   AS farm_id,
                   CONCAT(p.first_name,' ',p.last_name) AS farmer_name,
                   p.id    AS farmer_person_id
            FROM promo_discount pd
            JOIN farmer f      ON pd.farmer_id = f.id
            JOIN person p      ON f.person_id  = p.id
            LEFT JOIN farmer_farm ff ON ff.farmer_id = f.id AND ff.is_active = 1
            WHERE pd.id = ? AND pd.is_active = 1
              AND pd.valid_from <= ? AND pd.valid_until >= ?
            LIMIT 1
        ", [$promo_id, $today, $today])->row();

        if (!$promo) {
            echo json_encode(["success" => false, "message" => "Promo not found or has expired."]);
            return;
        }

        // Enforce max_qty if set
        if ($promo->max_qty && $qty > $promo->max_qty) {
            echo json_encode(["success" => false, "message" => "Max quantity for this promo is {$promo->max_qty}."]);
            return;
        }

        $farm_id   = $promo->farm_id;
        $price     = (float) $promo->discounted_price;
        $sub_total = $price * $qty;
        $dateNow   = $this->now();

        // Reuse existing PENDING transaction for this farm or create new one
        $check = $this->db->query(
            "SELECT t.id FROM transaction t
             JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE AND status='PENDING') ts
               ON t.id = ts.transaction_id
             WHERE t.person_id = ? AND t.farm_id = ? LIMIT 1",
            [$person_id, $farm_id]
        )->row();

        if ($check) {
            $transaction_id = $check->id;
        } else {
            $this->db->insert("transaction", [
                'person_id'          => $person_id,
                'total_sale_amount'  => 1,
                'transaction_date'   => $dateNow,
                'admin_percentage'   => 1,
                'admin_sale_amount'  => 0.1,
                'farm_id'            => $farm_id,
            ]);
            $transaction_id = $this->db->insert_id();
            $this->transaction_status([
                'transaction_id'       => $transaction_id,
                'status'               => 'PENDING',
                'created_by_person_id' => $person_id,
            ]);
        }

        // Insert into my_cart_promo
        $this->db->insert("my_cart_promo", [
            'transaction_id'   => $transaction_id,
            'promo_discount_id' => $promo_id,
            'qty'              => $qty,
            'price_at_add'     => $price,
            'sub_total'        => $sub_total,
            'created_at'       => $dateNow,
        ]);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            echo json_encode(["success" => false, "message" => "Failed to add promo to cart."]);
            return;
        }

        $this->db->trans_commit();

        $cp = $this->getTransactionStatus($person_id, 'PENDING', 'client');

        // Notify farmer
        if ($promo->farmer_person_id) {
            $buyer_name = $this->getPersonName($person_id);
            $this->notify(
                $promo->farmer_person_id,
                '🏷️ Promo Order Incoming!',
                "{$buyer_name} added your promo \"{$promo->title}\" to their cart.",
                'SUCCESS',
                'transaction',
                $transaction_id
            );
        }

        echo json_encode([
            "success"      => true,
            "message"      => "Promo added to cart!",
            "cart_pending" => $cp,
        ]);
    }

    function addSupplyToCart()
    {
        $this->db->trans_begin();
        $true  = ["success" => true];
        $false = ["success" => false];

        $person_id         = $this->session->agrishop_person_id;
        $login_id          = $this->session->agrishop_login_id;
        $supply_id         = $this->input->post('supply_id');
        $qty               = $this->input->post('qty');
        $price             = $this->input->post('price');

        if (!$login_id) {
            echo json_encode(["login_required" => true]);
            return;
        }
        if (!$supply_id || !$qty || $qty < 1) {
            echo json_encode(["fill" => true]);
            return;
        }

        // Get supply details
        $supply = $this->db->query("
            SELECT ss.*, sup.person_id AS supplier_person_id, sup.id AS supplier_id
            FROM supplier_supply ss
            JOIN supplier sup ON ss.supplier_id = sup.id
            WHERE ss.id = ? AND ss.is_active = 1 AND ss.qty_available >= ?
            LIMIT 1
        ", [$supply_id, $qty])->row();

        if (!$supply) {
            echo json_encode(["success" => false, "message" => "Supply not available or insufficient stock."]);
            return;
        }

        // Check if there's an open (PENDING) supply transaction for this buyer
        $existing_trans = $this->db->query("
            SELECT t.id FROM transaction t
            JOIN (SELECT * FROM transaction_status WHERE is_latest = TRUE AND status = 'PENDING') ts ON t.id = ts.transaction_id
            WHERE t.person_id = ? AND t.supplier_id = ?
            LIMIT 1
        ", [$person_id, $supply->supplier_id])->row();

        if ($existing_trans) {
            $transaction_id = $existing_trans->id;
        } else {
            // Create new transaction
            $data_trans = [
                "person_id"          => $person_id,
                "supplier_id"        => $supply->supplier_id,
                "transaction_date"   => date('Y-m-d H:i:s'),
                "total_sale_amount"  => 0,   // updated on checkout
                "is_done"            => false,
            ];
            $this->db->insert("transaction", $data_trans);
            $transaction_id = $this->db->insert_id();

            // Initial PENDING status
            $this->transaction_status([
                'transaction_id'       => $transaction_id,
                'status'               => 'PENDING',
                'created_by_person_id' => $person_id,
            ]);
            // Initial UNPAID payment status
            $this->transaction_payment_status([
                'transaction_id'       => $transaction_id,
                'status'               => 'UNPAID',
                'created_by_person_id' => $person_id,
            ]);
            // Initial TO_PICKUP delivery status
            $this->transaction_delivery_status([
                'transaction_id'       => $transaction_id,
                'status'               => 'TO_PICKUP',
                'created_by_person_id' => $person_id,
            ]);
        }

        // Insert cart item
        $sub_total = $supply->price * $qty;
        $data_cart = [
            "transaction_id"       => $transaction_id,
            "supplier_supply_id"   => $supply_id,
            "qty"                  => $qty,
            "sub_total"            => $sub_total,
            "price_during_transact" => $supply->price,
            "created_at"           => date('Y-m-d H:i:s'),
        ];

        if ($this->db->insert("my_cart_supply", $data_cart)) {
            $true += [
                "message"        => $supply->name . " added to cart!",
                "transaction_id" => $transaction_id,
                "sub_total"      => $sub_total,
            ];
            $ret = $true;
        } else {
            $false += ["message" => "Failed to add to cart!"];
            $ret = $false;
        }

        $this->db->trans_status() === false
            ? $this->db->trans_rollback()
            : $this->db->trans_commit();

        echo json_encode($ret);
    }

    public function remove_produce_from_cart()
    {
        $this->db->trans_begin();
        $person_id = $this->session->agrishop_person_id;
        $data = [];
        $cart_id = $this->input->post("cart_id");
        $transaction_id = $this->input->post("transaction_id");
        $true = ["success"   => true];
        $false = ["success"   => false];

        $cart = $this->db->query("SELECT sum(1) as cc FROM my_cart_farm_produce WHERE transaction_id = $transaction_id")->row();

        if ($this->db->delete("my_cart_farm_produce", ["id" => $cart_id])) {
            if ($cart->cc == 1) {
                $this->db->query("DELETE FROM transaction_status WHERE transaction_id = $transaction_id");
                $this->db->query("DELETE FROM transaction WHERE id = $transaction_id");
            }

            $cp = $this->getTransactionStatus($person_id, 'PENDING', 'client');
            $true += ["message"   => "Removed from cart!", "cart_pending"   => $cp];
            $ret = $true;
        } else {
            $false += ["message"   => "Failed to remove from cart!"];
            $ret = $false;
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }
        $this->session->agrishop_pending_trans_count = $this->getTransactionStatus($person_id, 'PENDING', 'client');
        echo json_encode($ret);
    }


    public function remove_supply_from_cart()
    {
        $this->db->trans_begin();
        $person_id      = $this->session->agrishop_person_id;
        $cart_id        = $this->input->post('cart_id');
        $transaction_id = $this->input->post('transaction_id');
        $true  = ["success" => true];
        $false = ["success" => false];

        $cart = $this->db->query(
            "SELECT COUNT(1) AS cc FROM my_cart_supply WHERE transaction_id = ?",
            [$transaction_id]
        )->row();

        if ($this->db->delete("my_cart_supply", ["id" => $cart_id])) {
            if ($cart->cc == 1) {
                $this->db->query("DELETE FROM transaction_status          WHERE transaction_id = ?", [$transaction_id]);
                $this->db->query("DELETE FROM transaction_payment_status  WHERE transaction_id = ?", [$transaction_id]);
                $this->db->query("DELETE FROM transaction_delivery_status WHERE transaction_id = ?", [$transaction_id]);
                $this->db->query("DELETE FROM transaction WHERE id = ?",                            [$transaction_id]);
            }
            $cp = $this->getTransactionStatus($person_id, 'PENDING', 'client');
            $true  += ["message" => "Removed from cart!", "cart_pending" => $cp];
            $ret    = $true;
        } else {
            $false += ["message" => "Failed to remove!"];
            $ret    = $false;
        }

        $this->db->trans_status() === false
            ? $this->db->trans_rollback()
            : $this->db->trans_commit();

        echo json_encode($ret);
    }

    public function remove_promo_from_cart()
    {
        $this->db->trans_begin();
        $person_id      = $this->session->agrishop_person_id;
        $cart_id        = (int) $this->input->post('cart_id');
        $transaction_id = (int) $this->input->post('transaction_id');
        $true  = ["success" => true];
        $false = ["success" => false];

        // Count remaining promo items + farm produce items in this transaction
        $promo_count   = $this->db->query("SELECT COUNT(1) AS cc FROM my_cart_promo WHERE transaction_id = ?", [$transaction_id])->row()->cc;
        $produce_count = $this->db->query("SELECT COUNT(1) AS cc FROM my_cart_farm_produce WHERE transaction_id = ?", [$transaction_id])->row()->cc;

        if ($this->db->delete("my_cart_promo", ["id" => $cart_id])) {
            // If this was the last item in the transaction, clean up the transaction
            if (($promo_count + $produce_count) == 1) {
                $this->db->query("DELETE FROM transaction_status WHERE transaction_id = ?", [$transaction_id]);
                $this->db->query("DELETE FROM transaction WHERE id = ?", [$transaction_id]);
            }
            $cp = $this->getTransactionStatus($person_id, 'PENDING', 'client');
            $true += ["message" => "Promo item removed!", "cart_pending" => $cp];
            $ret = $true;
        } else {
            $false += ["message" => "Failed to remove promo item!"];
            $ret = $false;
        }

        $this->db->trans_status() === false
            ? $this->db->trans_rollback()
            : $this->db->trans_commit();

        echo json_encode($ret);
    }

    public function getCartListing()
    {
        $person_id   = $this->session->agrishop_person_id;
        $requestData = $_REQUEST;
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        list($limit, $offset) = $this->calculatePagination($requestData);

        // Unified cart: farm produce orders + supply orders + promo orders all shown as PENDING
        $thisQuery = $this->db->query("
            SELECT COUNT(1) AS total
            FROM transaction t1
            JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
            LEFT JOIN (SELECT transaction_id, SUM(sub_total) AS payable FROM my_cart_farm_produce GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
            LEFT JOIN (SELECT transaction_id, SUM(sub_total) AS supply_payable FROM my_cart_supply GROUP BY transaction_id) t3s ON t1.id = t3s.transaction_id
            LEFT JOIN (SELECT transaction_id, SUM(sub_total) AS promo_payable FROM my_cart_promo GROUP BY transaction_id) t3p ON t1.id = t3p.transaction_id
            LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
            LEFT JOIN (SELECT supplier_id, MIN(store_name) AS store_name, MIN(img_path) AS img_path FROM supplier_store WHERE is_active=1 GROUP BY supplier_id) ss ON t1.supplier_id = ss.supplier_id
            WHERE t2.status = 'PENDING' AND t1.person_id = $person_id
            AND CONCAT(COALESCE(t4.farm_name,''), COALESCE(ss.store_name,''), t2.status, COALESCE(t3.payable,''), COALESCE(t3s.supply_payable,''), COALESCE(t3p.promo_payable,''))
                COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
        ");
        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("
            SELECT
                t1.id AS transaction_id,
                DATE_FORMAT(t1.transaction_date,'%m/%d/%y') AS date_,
                t2.status,
                t1.farm_id,
                t1.supplier_id,
                t4.img_path      AS farm_img,
                t4.farm_name,
                ss.img_path      AS store_img,
                ss.store_name,
                COALESCE(t3.payable, 0)         AS payable,
                COALESCE(t3s.supply_payable, 0) AS supply_payable,
                COALESCE(t3p.promo_payable, 0)  AS promo_payable
            FROM transaction t1
            JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
            LEFT JOIN (SELECT transaction_id, SUM(sub_total) AS payable FROM my_cart_farm_produce GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
            LEFT JOIN (SELECT transaction_id, SUM(sub_total) AS supply_payable FROM my_cart_supply GROUP BY transaction_id) t3s ON t1.id = t3s.transaction_id
            LEFT JOIN (SELECT transaction_id, SUM(sub_total) AS promo_payable FROM my_cart_promo GROUP BY transaction_id) t3p ON t1.id = t3p.transaction_id
            LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
            LEFT JOIN (SELECT supplier_id, MIN(store_name) AS store_name, MIN(img_path) AS img_path FROM supplier_store WHERE is_active=1 GROUP BY supplier_id) ss ON t1.supplier_id = ss.supplier_id
            WHERE t2.status = 'PENDING' AND t1.person_id = $person_id
            AND CONCAT(COALESCE(t4.farm_name,''), COALESCE(ss.store_name,''), t2.status, COALESCE(t3.payable,''), COALESCE(t3s.supply_payable,''), COALESCE(t3p.promo_payable,''))
                COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
            ORDER BY t1.id DESC
            LIMIT $limit OFFSET $offset
        ");

        $data = array();
        foreach ($query->result() as $value) {
            // Determine if this is a supply, farm produce, or promo cart
            $is_supply   = !empty($value->supplier_id) && empty($value->farm_id);
            $raw_payable = $is_supply
                ? $value->supply_payable
                : ($value->payable + $value->promo_payable);
            $total = $raw_payable + ($raw_payable * 0.01);

            if ($is_supply) {
                $img        = !empty($value->store_img) ? base_url($value->store_img) : base_url('dist/img/media/icons/1x1.png');
                $label      = $value->store_name ?: 'Supply Order';
            } else {
                $img        = !empty($value->farm_img) ? base_url($value->farm_img) : base_url('dist/img/media/icons/1x1.png');
                $label      = $value->farm_name ?: 'Farm Order';
            }

            $image_path   = "<img src='$img' width='50' height='50' class='rounded' style='object-fit:cover;'>";
            $status_badge = $this->statusBadge($value->status);

            $data[] = array(
                '<div class="d-flex align-items-start p-2" style="gap:10px; width:100%; line-height:1.15">
                    <div>' . $image_path . '</div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div style="font-size:12px; color:#999;">' . $value->date_ . '</div>
                                <div style="font-size:15px; font-weight:600; color:#111;">' . $label . '</div>
                            </div>
                            <span class="badge badge-success" style="font-size:14px;">
                                &#8369; ' . $this->format_price($total) . '
                            </span>
                        </div>
                        <div class="d-flex align-items-center mt-1" style="gap:6px;">
                            <span class="badge" style="background:#222;color:#fff;cursor:pointer;font-size:11px;"
                                onclick="viewTransactionDetails(' . $value->transaction_id . ')">
                                <i class="fa fa-eye"></i> view details
                            </span>
                            ' . $status_badge . '
                        </div>
                    </div>
                </div>
                <hr style="margin:4px 0;">'
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

    public function getOrderListing()
    {
        $person_id  = $this->session->agrishop_person_id;
        $requestData = $_REQUEST;
        $farm_id = $requestData['search']['farm_id'];
        $status = $requestData['search']['status'];
        if ($status) {
            $status_condition = "t2.status = '$status' OR t5.status = '$status' AND (t2.status != 'COMPLETED' AND t2.status != 'CANCELLED')";
        } else {
            $status_condition = "(t2.status != 'PENDING' AND t2.status != 'COMPLETED' AND t2.status != 'CANCELLED')";
        }
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Query to get total record count
        $thisQuery = $this->db->query("SELECT count(1) AS total FROM transaction t1 
                                    JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
		                                    LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce mcfp
		                                    GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                                    LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                                    LEFT JOIN (SELECT * FROM transaction_delivery_status WHERE is_latest IS TRUE) t5 ON t1.id = t5.transaction_id
                                    WHERE t1.person_id = $person_id AND $status_condition AND CONCAT(t4.farm_name,t2.status,t3.payable) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'");

        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT t1.id AS transaction_id, t4.img_path, DATE_FORMAT(t1.transaction_date,'%m/%d/%y') date_, t4.farm_name,t2.status,t3.payable FROM transaction t1 
                                    JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
		                                    LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce mcfp
		                                    GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                                    LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                                    LEFT JOIN (SELECT * FROM transaction_delivery_status WHERE is_latest IS TRUE) t5 ON t1.id = t5.transaction_id
                                    WHERE $status_condition AND t1.person_id = $person_id AND CONCAT(t4.farm_name,t2.status,t3.payable) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
                                    ORDER BY t1.id DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        foreach ($query->result() as $value) {
            // Determine if this is a supply or farm produce cart
            $is_supply = !empty($value->supplier_id) && empty($value->farm_id);
            $raw_payable = $is_supply ? $value->supply_payable : $value->payable;
            $total = $raw_payable + ($raw_payable * 0.01);

            if ($is_supply) {
                $img        = !empty($value->store_img) ? base_url($value->store_img) : base_url('dist/img/media/icons/1x1.png');
                $label      = $value->store_name ?: 'Supply Order';
            } else {
                $img        = !empty($value->farm_img) ? base_url($value->farm_img) : base_url('dist/img/media/icons/1x1.png');
                $label      = $value->farm_name ?: 'Farm Order';
            }

            $image_path   = "<img src='$img' width='50' height='50' class='rounded' style='object-fit:cover;'>";
            $status_badge = $this->statusBadge($value->status);

            $data[] = array(
                '<div class="d-flex align-items-start p-2" style="gap:10px; width:100%; line-height:1.15">
                    <div>' . $image_path . '</div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div style="font-size:12px; color:#999;">' . $value->date_ . '</div>
                                <div style="font-size:15px; font-weight:600; color:#111;">' . $label . '</div>
                            </div>
                            <span class="badge badge-success" style="font-size:14px;">
                                &#8369; ' . $this->format_price($total) . '
                            </span>
                        </div>
                        <div class="d-flex align-items-center mt-1" style="gap:6px;">
                            <span class="badge" style="background:#222;color:#fff;cursor:pointer;font-size:11px;"
                                onclick="viewTransactionDetails(' . $value->transaction_id . ')">
                                <i class="fa fa-eye"></i> view details
                            </span>
                            ' . $status_badge . '
                        </div>
                    </div>
                </div>
                <hr style="margin:4px 0;">'
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

    public function getCompletedOrderListing()
    {
        $person_id  = $this->session->agrishop_person_id;
        $requestData = $_REQUEST;
        $farm_id = $requestData['search']['farm_id'];
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Query to get total record count
        $thisQuery = $this->db->query("SELECT count(1) AS total FROM transaction t1 
                                    JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
		                                    LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce mcfp
		                                    GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                                    LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                                    WHERE (t2.status = 'COMPLETED') AND t1.person_id = $person_id AND CONCAT(t4.farm_name,t2.status,t3.payable) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'");

        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT t1.id AS transaction_id, tr.rating, t4.img_path, DATE_FORMAT(t1.transaction_date,'%m/%d/%y') date_, t4.farm_name,t2.status,t3.payable FROM transaction t1 
                                    JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
		                                    LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce mcfp
		                                    GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                                    LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                                    LEFT JOIN transaction_ratings tr ON t1.id = tr.transaction_id
                                    WHERE (t2.status = 'COMPLETED') AND t1.person_id = $person_id AND CONCAT(t4.farm_name,t2.status,t3.payable) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
                                    ORDER BY t1.id DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        foreach ($query->result() as $value) {
            $total = $value->payable + ($value->payable * 0.01);
            $img = $value->img_path ? base_url($value->img_path) : base_url('dist/img/media/icons/1x1.png');
            $image_path = "<img src='$img' width='50' height='50' class='rounded' data-toggle='tooltip' data-placement='top' title=''>";
            $status_badge = $this->statusBadge($value->status);
            $stars = $this->renderStars($value->rating);
            $data[] = array(
                '<div class="d-flex align-items-start p-2" style="gap:10px; width:100%; line-height:1.15">

                    <!-- IMAGE -->
                    <div>
                        ' . $image_path . '
                    </div>

                    <!-- INFO -->
                    <div class="flex-grow-1">

                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div style="font-size:12px; color:#777;">
                                    ' . $value->date_ . '
                                </div>

                                <div style="font-size:14px; font-weight:600; color:#000;">
                                    ' . $value->farm_name . '
                                </div>
                                <div style="font-size:12px;">
                                    ' . $stars . '
                                </div>
                            </div>

                            <span class="badge bg-success" style="font-size:14px;">
                                ₱ ' . $this->format_price($total) . '
                            </span>
                        </div>

                        <div class="d-flex align-items-center mt-1" style="gap:6px;">
                            <span class="badge bg-black"
                                style="cursor:pointer; font-size:11px;"
                                onclick="viewTransactionDetails(' . $value->transaction_id . ')">
                                <i class="fa fa-eye"></i> view details
                            </span>

                            ' . $status_badge . '
                        </div>

                    </div>
                </div>
                <hr style="margin:4px 0;">
                '
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

    public function getCancelledOrderListing()
    {
        $person_id  = $this->session->agrishop_person_id;
        $requestData = $_REQUEST;
        $farm_id = $requestData['search']['farm_id'];
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Query to get total record count
        $thisQuery = $this->db->query("SELECT count(1) AS total FROM transaction t1 
                                    JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
		                                    LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce mcfp
		                                    GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                                    LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                                    WHERE (t2.status = 'CANCELLED') AND t1.person_id = $person_id AND CONCAT(t4.farm_name,t2.status,t3.payable) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'");

        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT t1.id AS transaction_id, t4.img_path, DATE_FORMAT(t1.transaction_date,'%m/%d/%y') date_, t4.farm_name,t2.status,t3.payable FROM transaction t1 
                                    JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
		                                    LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce mcfp
		                                    GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                                    LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                                    WHERE (t2.status = 'CANCELLED') AND t1.person_id = $person_id AND CONCAT(t4.farm_name,t2.status,t3.payable) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
                                    ORDER BY t1.id DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        foreach ($query->result() as $value) {
            // Determine if this is a supply or farm produce cart
            $is_supply = !empty($value->supplier_id) && empty($value->farm_id);
            $raw_payable = $is_supply ? $value->supply_payable : $value->payable;
            $total = $raw_payable + ($raw_payable * 0.01);

            if ($is_supply) {
                $img        = !empty($value->store_img) ? base_url($value->store_img) : base_url('dist/img/media/icons/1x1.png');
                $label      = $value->store_name ?: 'Supply Order';
            } else {
                $img        = !empty($value->farm_img) ? base_url($value->farm_img) : base_url('dist/img/media/icons/1x1.png');
                $label      = $value->farm_name ?: 'Farm Order';
            }

            $image_path   = "<img src='$img' width='50' height='50' class='rounded' style='object-fit:cover;'>";
            $status_badge = $this->statusBadge($value->status);

            $data[] = array(
                '<div class="d-flex align-items-start p-2" style="gap:10px; width:100%; line-height:1.15">
                    <div>' . $image_path . '</div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div style="font-size:12px; color:#999;">' . $value->date_ . '</div>
                                <div style="font-size:15px; font-weight:600; color:#111;">' . $label . '</div>
                            </div>
                            <span class="badge badge-success" style="font-size:14px;">
                                &#8369; ' . $this->format_price($total) . '
                            </span>
                        </div>
                        <div class="d-flex align-items-center mt-1" style="gap:6px;">
                            <span class="badge" style="background:#222;color:#fff;cursor:pointer;font-size:11px;"
                                onclick="viewTransactionDetails(' . $value->transaction_id . ')">
                                <i class="fa fa-eye"></i> view details
                            </span>
                            ' . $status_badge . '
                        </div>
                    </div>
                </div>
                <hr style="margin:4px 0;">'
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

    public function getRateOrderListing()
    {
        $person_id  = $this->session->agrishop_person_id;
        $requestData = $_REQUEST;
        $farm_id = $requestData['search']['farm_id'];
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Query to get total record count
        $thisQuery = $this->db->query("SELECT count(1) AS total FROM transaction t1 
                                    JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
		                                    LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce mcfp
		                                    GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                                    LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                                    LEFT JOIN transaction_ratings tr ON tr.transaction_id = t1.id
                                    WHERE (t2.status = 'COMPLETED' AND tr.rating IS NULL) AND t1.person_id = $person_id AND CONCAT(t4.farm_name,t2.status,t3.payable) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'");

        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT t1.id AS transaction_id, tr.rating, t4.img_path, DATE_FORMAT(t1.transaction_date,'%m/%d/%y') date_, t4.farm_name,t2.status,t3.payable FROM transaction t1 
                                    JOIN (SELECT * FROM transaction_status WHERE is_latest IS TRUE) t2 ON t1.id = t2.transaction_id
		                                    LEFT JOIN (SELECT transaction_id, sum(sub_total) AS payable FROM my_cart_farm_produce mcfp
		                                    GROUP BY transaction_id) t3 ON t1.id = t3.transaction_id
                                    LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                                    LEFT JOIN transaction_ratings tr ON tr.transaction_id = t1.id
                                    WHERE (t2.status = 'COMPLETED' AND tr.rating IS NULL) AND t1.person_id = $person_id AND CONCAT(t4.farm_name,t2.status,t3.payable) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
                                    ORDER BY t1.id DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        foreach ($query->result() as $value) {
            $total = $value->payable + ($value->payable * 0.01);
            $img = $value->img_path ? base_url($value->img_path) : base_url('dist/img/media/icons/1x1.png');
            $image_path = "<img src='$img' width='50' height='50' class='rounded' data-toggle='tooltip' data-placement='top' title=''>";
            $status_badge = $this->statusBadge($value->status);
            if ($value->rating > 0) {
                $stars = $this->renderStars($value->rating);
            } else {
                $stars = '<span style="cursor:pointer;color:#007bff;font-size:12px"
                onclick="rateTransaction(' . $value->transaction_id . ')">
                ⭐ Rate Now
              </span>';
            }
            $data[] = array(
                '<div class="d-flex align-items-start p-2" style="gap:10px; width:100%; line-height:1.15">

                    <!-- IMAGE -->
                    <div>
                        ' . $image_path . '
                    </div>

                    <!-- INFO -->
                    <div class="flex-grow-1">

                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div style="font-size:12px; color:#777;">
                                    ' . $value->date_ . '
                                </div>

                                <div style="font-size:14px; font-weight:600; color:#000;">
                                    ' . $value->farm_name . '
                                </div>
                            </div>

                            <span class="badge bg-success" style="font-size:14px;">
                                ₱ ' . $this->format_price($total) . '
                            </span>
                        </div>

                        <div class="d-flex align-items-center mt-1" style="gap:6px;">
                            <span class="badge bg-black"
                                style="cursor:pointer; font-size:11px;"
                                onclick="viewTransactionDetails(' . $value->transaction_id . ')">
                                <i class="fa fa-eye"></i> view details
                            </span>
                            <div style="font-size:12px;">
                                ' . $stars . '
                            </div>
                        </div>

                    </div>
                </div>
                <hr style="margin:4px 0;">
                '
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

    public function save_rating()
    {
        $person_id = $this->session->agrishop_person_id;
        $data = [
            'transaction_id' => $this->input->post('transaction_id'),
            'rating' => $this->input->post('rating_value'),
            'comment' => $this->input->post('review_comment'),
            'created_at' => date('Y-m-d H:i:s'),
            'created_by_person_id' => $person_id,
        ];

        $this->db->insert('transaction_ratings', $data);

        echo json_encode(['status' => true]);
    }


    private function renderStars($rating)
    {
        $html = '';

        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
                $html .= '<i class="fa fa-star text-warning"></i>';
            } else {
                $html .= '<i class="fa fa-star text-secondary"></i>';
            }
        }

        return $html;
    }

    public function getCartDetails()
    {
        $person_id      = $this->session->agrishop_person_id;
        $requestData    = $_REQUEST;
        $transaction_id = (int)($requestData['search']['transaction_id'] ?? 0);

        if (!$transaction_id) {
            echo json_encode(['draw' => 1, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
            return;
        }

        list($limit, $offset) = $this->calculatePagination($requestData);

        // ── Detect cart type: supply vs farm produce ─────────────────────
        $trans = $this->db->query("
            SELECT t.farm_id, t.supplier_id
            FROM transaction t WHERE t.id = ? LIMIT 1
        ", [$transaction_id])->row();

        $is_supply = $trans && !empty($trans->supplier_id) && empty($trans->farm_id);

        // ── Fetch status rows ─────────────────────────────────────────────
        $status_row = $this->db->query("
            SELECT ts.status AS t_status, tps.status AS t_p_status, tds.status AS t_d_status
            FROM transaction t
            LEFT JOIN (SELECT * FROM transaction_status          WHERE transaction_id=? AND is_latest IS TRUE) ts  ON t.id=ts.transaction_id
            LEFT JOIN (SELECT * FROM transaction_payment_status  WHERE transaction_id=? AND is_latest IS TRUE) tps ON t.id=tps.transaction_id
            LEFT JOIN (SELECT * FROM transaction_delivery_status WHERE transaction_id=? AND is_latest IS TRUE) tds ON t.id=tds.transaction_id
            WHERE t.id=? LIMIT 1
        ", [$transaction_id, $transaction_id, $transaction_id, $transaction_id])->row();

        $q_status   = $status_row->t_status   ?? '';
        $q_p_status = $status_row->t_p_status ?? '';
        $q_d_status = $status_row->t_d_status ?? '';

        // ── Payment to admin row ──────────────────────────────────────────
        $pay_to_admin = $this->db->query(
            "SELECT * FROM transaction_payment_to_admin WHERE transaction_id=? LIMIT 1",
            [$transaction_id]
        );
        $t_p_approved_by = '';
        $t_p_id          = '';
        if ($pay_to_admin->num_rows() > 0) {
            $t_p_approved_by = $pay_to_admin->row()->approved_by;
            $t_p_id          = $pay_to_admin->row()->id;
        }

        $data     = [];
        $subtotal = 0;

        // ══════════════════════════════════════════════════════════════════
        //  SUPPLY CART
        // ══════════════════════════════════════════════════════════════════
        if ($is_supply) {

            $items = $this->db->query("
                SELECT
                    mcs.id          AS cart_id,
                    mcs.transaction_id,
                    mcs.qty,
                    mcs.sub_total,
                    mcs.price_during_transact,
                    ss.name         AS item_name,
                    ss.uom,
                    ss.img_path
                FROM my_cart_supply mcs
                JOIN supplier_supply ss ON mcs.supplier_supply_id = ss.id
                WHERE mcs.transaction_id = ?
                ORDER BY mcs.id DESC
                LIMIT $limit OFFSET $offset
            ", [$transaction_id]);

            foreach ($items->result() as $v) {
                $price     = $v->price_during_transact * $v->qty;
                $subtotal += $price;
                $img       = !empty($v->img_path)
                    ? base_url($v->img_path)
                    : base_url('dist/img/media/icons/1x1.png');

                $trash = '';
                if ($q_status == 'PENDING') {
                    $trash = '<span class="text-danger" style="cursor:pointer;font-size:13px;"
                                title="Remove"
                                onclick="removeSupplyCart(' . $v->cart_id . ',' . $transaction_id . ')">
                                <i class="fa fa-trash"></i>
                              </span>';
                }

                $data[] = [
                    '<div class="d-flex align-items-start p-2" style="gap:10px;width:100%">
                        <img src="' . $img . '" width="56" height="56" class="rounded shadow-sm" style="object-fit:cover">
                        <div class="flex-grow-1" style="line-height:1.15">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div style="font-size:14px;font-weight:600;color:#222;margin-bottom:2px;">'
                        . htmlspecialchars($v->item_name) . '
                                    </div>
                                    <div class="text-muted" style="font-size:12px;">
                                        ' . $v->qty . ' ' . $v->uom . ' &times; &#8369;&nbsp;' . $this->format_price($v->price_during_transact) . '
                                    </div>
                                </div>
                                ' . $trash . '
                            </div>
                            <div style="margin-top:4px;">
                                <span class="badge badge-success px-2 py-1" style="font-size:12px;">
                                    &#8369; ' . $this->format_price($price) . '
                                </span>
                            </div>
                        </div>
                    </div>
                    <hr style="margin:4px 0;">'
                ];
            }

            // ── Supplier GCash payment method ─────────────────────────────
            $g_pay = '';
            if ($q_status == 'PENDING') {
                $sup_pay = $this->db->query("
                    SELECT spm.*
                    FROM my_cart_supply mcs
                    JOIN supplier_supply ss  ON mcs.supplier_supply_id = ss.id
                    JOIN supplier sup        ON ss.supplier_id = sup.id
                    JOIN supplier_payment_method spm ON sup.person_id = spm.person_id
                    WHERE mcs.transaction_id = ? AND spm.is_active = 1
                    LIMIT 1
                ", [$transaction_id]);

                if ($sup_pay->num_rows() > 0) {
                    $g        = $sup_pay->row();
                    $gcash_icon = base_url('dist/img/credit/gcash_50x50.png');
                    $g_pay    = '
                        <div class="form-check form-check-inline" style="margin-right:10px;">
                            <input class="form-check-input" type="radio" name="payment_method" id="pay_gcash" value="gcash">
                            <label class="form-check-label pay_gcash"
                                data-name="'  . $g->account_name . '"
                                data-number="' . $g->number . '"
                                data-qr="'    . base_url($g->qr) . '"
                                for="pay_gcash" style="cursor:pointer;">
                                <img src="' . $gcash_icon . '" style="width:20px;height:20px;margin-left:5px;"> GCash
                            </label>
                        </div>';
                }
            }

            // ══════════════════════════════════════════════════════════════════
            //  FARM PRODUCE CART
            // ══════════════════════════════════════════════════════════════════
        } else {

            $items = $this->db->query("
                SELECT
                    t1.id AS cart_id, t1.transaction_id, t1.qty,
                    t4.price, t4.price_wholesale, t2.uom,
                    t3.name AS item_name, t3.img_path,
                    t1.is_wholesale
                FROM my_cart_farm_produce t1
                LEFT JOIN farm_produce t2 ON t1.farm_produce_id = t2.id
                LEFT JOIN produce t3 ON t2.produce_id = t3.id
                LEFT JOIN (SELECT * FROM price_monitoring_farm_produce WHERE is_latest IS TRUE) t4
                    ON t1.price_id_during_transact = t4.id
                WHERE t1.transaction_id = $transaction_id
                ORDER BY t1.id DESC
                LIMIT $limit OFFSET $offset
            ");

            foreach ($items->result() as $v) {
                $pricing   = $v->is_wholesale ? $v->price_wholesale : $v->price;
                $price     = $pricing * $v->qty;
                $subtotal += $price;
                $img       = $v->img_path
                    ? base_url($v->img_path)
                    : base_url('dist/img/media/icons/1x1.png');

                $trash = '';
                if ($q_status == 'PENDING') {
                    $trash = '<span class="text-danger" style="cursor:pointer;font-size:13px;"
                                title="Remove"
                                onclick="removeCart(' . $v->cart_id . ',' . $transaction_id . ')">
                                <i class="fa fa-trash"></i>
                              </span>';
                }

                $ws_badge = $v->is_wholesale
                    ? ' <span class="badge badge-secondary" style="font-size:10px;">wholesale</span>'
                    : '';

                $data[] = [
                    '<div class="d-flex align-items-start p-2" style="gap:10px;width:100%">
                        <img src="' . $img . '" width="56" height="56" class="rounded shadow-sm" style="object-fit:cover">
                        <div class="flex-grow-1" style="line-height:1.15">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div style="font-size:14px;font-weight:600;color:#222;margin-bottom:2px;">'
                        . $v->item_name . '
                                    </div>
                                    <div class="text-muted" style="font-size:12px;">
                                        ' . $v->qty . ' ' . $v->uom . ' &times; &#8369;&nbsp;' . $this->format_price($pricing) . $ws_badge . '
                                    </div>
                                </div>
                                ' . $trash . '
                            </div>
                            <div style="margin-top:4px;">
                                <span class="badge badge-success px-2 py-1" style="font-size:12px;">
                                    &#8369; ' . $this->format_price($price) . '
                                </span>
                            </div>
                        </div>
                    </div>
                    <hr style="margin:4px 0;">'
                ];
            }

            // ── Promo cart items (my_cart_promo) ──────────────────────────
            $promo_items = $this->db->query("
                SELECT mcp.id AS cart_id, mcp.transaction_id, mcp.qty,
                       mcp.price_at_add, mcp.sub_total,
                       pd.title, pd.img_path, pd.discount_percent,
                       pr.name AS produce_name
                FROM my_cart_promo mcp
                JOIN promo_discount pd ON mcp.promo_discount_id = pd.id
                LEFT JOIN farm_produce fp ON pd.farm_produce_id = fp.id
                LEFT JOIN produce pr      ON fp.produce_id = pr.id
                WHERE mcp.transaction_id = $transaction_id
                ORDER BY mcp.id DESC
            ");

            foreach ($promo_items->result() as $pv) {
                $subtotal += (float) $pv->sub_total;
                $pimg = $pv->img_path
                    ? base_url($pv->img_path)
                    : base_url('dist/img/media/icons/1x1.png');

                $p_trash = '';
                if ($q_status == 'PENDING') {
                    $p_trash = '<span class="text-danger" style="cursor:pointer;font-size:13px;" title="Remove"
                                    onclick="removePromoCart(' . $pv->cart_id . ',' . $transaction_id . ')">
                                    <i class="fa fa-trash"></i>
                                </span>';
                }

                $data[] = [
                    '<div class="d-flex align-items-start p-2" style="gap:10px;width:100%">
                        <img src="' . $pimg . '" width="56" height="56" class="rounded shadow-sm" style="object-fit:cover">
                        <div class="flex-grow-1" style="line-height:1.15">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="badge badge-danger" style="font-size:10px;margin-bottom:3px;">'
                        . $pv->discount_percent . '% OFF PROMO</span>
                                    <div style="font-size:14px;font-weight:600;color:#222;margin-bottom:2px;">'
                        . htmlspecialchars($pv->title) . '
                                    </div>
                                    <div class="text-muted" style="font-size:12px;">
                                        ' . $pv->qty . ' unit(s) &times; &#8369;&nbsp;' . $this->format_price($pv->price_at_add) . '
                                    </div>
                                </div>
                                ' . $p_trash . '
                            </div>
                            <div style="margin-top:4px;">
                                <span class="badge badge-success px-2 py-1" style="font-size:12px;">
                                    &#8369; ' . $this->format_price($pv->sub_total) . '
                                </span>
                            </div>
                        </div>
                    </div>
                    <hr style="margin:4px 0;">'
                ];
            }

            // ── Farmer GCash payment method ───────────────────────────────
            $g_pay = '';
            if ($q_status == 'PENDING') {
                // Try gcash from farm produce first, fallback to promo farmer
                $gcash_details = $this->db->query("
                    SELECT fpm.*
                    FROM my_cart_farm_produce mcfp
                    JOIN farm_produce fp  ON mcfp.farm_produce_id = fp.id
                    JOIN farmer_farm ff   ON fp.farm_id = ff.id
                    JOIN farmer f         ON ff.farmer_id = f.id
                    JOIN farmer_payment_method fpm ON f.person_id = fpm.person_id
                    WHERE mcfp.transaction_id = $transaction_id LIMIT 1
                ");

                // If no farm produce gcash, check promo farmer gcash
                if ($gcash_details->num_rows() == 0) {
                    $gcash_details = $this->db->query("
                        SELECT fpm.*
                        FROM my_cart_promo mcp
                        JOIN promo_discount pd ON mcp.promo_discount_id = pd.id
                        JOIN farmer f          ON pd.farmer_id = f.id
                        JOIN farmer_payment_method fpm ON f.person_id = fpm.person_id
                        WHERE mcp.transaction_id = $transaction_id LIMIT 1
                    ");
                }

                if ($gcash_details->num_rows() > 0) {
                    $g          = $gcash_details->row();
                    $gcash_icon = base_url('dist/img/credit/gcash_50x50.png');
                    $g_pay      = '
                        <div class="form-check form-check-inline" style="margin-right:10px;">
                            <input class="form-check-input" type="radio" name="payment_method" id="pay_gcash" value="gcash">
                            <label class="form-check-label pay_gcash"
                                data-name="'   . $g->account_name . '"
                                data-number="' . $g->number . '"
                                data-qr="'     . base_url($g->qr) . '"
                                for="pay_gcash" style="cursor:pointer;">
                                <img src="' . $gcash_icon . '" style="width:20px;height:20px;margin-left:5px;"> GCash
                            </label>
                        </div>';
                }
            }

            // ── Shared summary / checkout / status rows ───────────────────────
            $percent         = 0.01;
            $convenience_fee = $subtotal * $percent;
            $total_payment   = $subtotal + $convenience_fee;
            $convenience_fee_fmt = '&#8369; ' . $this->format_price($convenience_fee);

            $to_admin_payment = $t_p_id == ''
                ? '<span class="text-dark"><i class="fa fa-times-circle"></i> To be paid ' . $convenience_fee_fmt . '</span>'
                : ($t_p_id && $t_p_approved_by == ''
                    ? '<span class="text-warning"><i class="fa fa-exclamation-circle"></i> To be verified ' . $convenience_fee_fmt . '</span>'
                    : '<span class="text-success"><i class="fa fa-check-circle"></i> Verified ' . $convenience_fee_fmt . '</span>');

            $to_admin_payment_stat = $t_p_id == '' ? 'TO_BE_PAID'
                : ($t_p_id && $t_p_approved_by == '' ? 'TO_BE_VERIFIED' : 'VERIFIED');

            if ($q_status == 'PENDING') {
                // Checkout summary row
                $data[] = [
                    '
                <div class="p-2" style="width:100%;font-size:13px;line-height:1.2">
                    <div class="mb-2">
                        <div style="font-weight:600;margin-bottom:4px;">Delivery</div>
                        <div class="form-check form-check-inline mr-3">
                            <input class="form-check-input" type="radio" name="delivery_option" id="pickup" value="pickup" checked>
                            <label class="form-check-label" for="pickup" style="cursor:pointer;">Pick-up</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="delivery_option" id="cod" value="cod">
                            <label class="form-check-label" for="cod" style="cursor:pointer;">COD (Cash on Delivery)</label>
                        </div>
                    </div>
                    <div class="mb-2">
                        <div style="font-weight:600;margin-bottom:4px;">Payment</div>
                        <div class="form-check form-check-inline mr-3">
                            <input class="form-check-input" type="radio" name="payment_method" id="pay_cash"
                                data-total="'            . $total_payment . '"
                                data-trans_id="'         . $transaction_id . '"
                                data-convenience_fee="'  . $convenience_fee . '"
                                data-subtotal="'         . $subtotal . '"
                                data-percentage="'       . $percent . '"
                                data-to_admin_payment_stat="' . $to_admin_payment_stat . '"
                                value="cash" checked>
                            <label class="form-check-label" for="pay_cash" style="cursor:pointer;">
                                <i class="fa fa-money-bill mr-1"></i> Cash
                            </label>
                        </div>
                        ' . $g_pay . '
                    </div>
                    <hr style="margin:6px 0;">
                    <div class="d-flex justify-content-between"><span>Subtotal</span><span>&#8369; ' . $this->format_price($subtotal) . '</span></div>
                    <div class="d-flex justify-content-between text-muted">
                        <span>Fee (1%) <small>(Convenience Fee)</small></span>
                        <span>' . $to_admin_payment . '</span>
                    </div>
                    <hr style="margin:6px 0;">
                    <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background:#e9f7ef;font-size:15px;">
                        <span style="font-weight:700;">Total</span>
                        <span style="font-weight:bold;">&#8369; ' . $this->format_price($total_payment) . '</span>
                    </div>
                </div>'
                ];

                // GCash details box
                $data[] = [
                    '
                <div id="gcashDetailsBox" class="p-3 mt-2 border rounded" style="display:none;font-size:13px;background:#f9fbff;">
                    <div style="font-weight:600;margin-bottom:6px;">How to Pay via GCash</div>
                    <ol style="padding-left:18px;margin-bottom:6px;">
                        <li>Open your <b>GCash</b> app</li>
                        <li>Tap <b>Scan QR</b> and scan the QR below</li>
                        <li>Enter the <b>exact amount</b> then confirm</li>
                        <li>Upload <b>proof of payment</b> below</li>
                    </ol>
                    <small class="text-muted">&#9888; Payment will be verified after submission</small>
                    <hr style="margin:6px 0;">
                    <div class="text-center mb-2">
                        <img id="gcashQR" src="" alt="GCash QR" style="max-width:180px;" class="shadow-sm rounded">
                    </div>
                    <div class="text-center mb-2">
                        <div style="font-weight:600;" id="gcashName"></div>
                        <div class="text-muted" id="gcashNumber"></div>
                    </div>
                    <hr style="margin:6px 0;">
                    <label style="font-weight:600;">Upload Proof of Payment <i style="color:red;">(Required)</i></label>
                    <input type="file" class="form-control form-control-sm mt-1" name="proof_of_payment" accept="image/*">
                </div>'
                ];

                // Checkout button
                $data[] = ['<button class="btn btn-primary btn-block w-100" onclick="checkout()">
                <i class="fa fa-shopping-cart mr-1"></i> Checkout
            </button>'];
            } else {
                // Non-pending: show statuses only
                $data[] = [
                    '
                <div class="p-2" style="width:100%;font-size:13px;line-height:1.2">
                    <div class="mb-2">
                        <div style="font-weight:600;margin-bottom:4px;">Delivery Status</div>
                        ' . $this->statusBadge($q_d_status) . '
                    </div>
                    <div class="mb-2">
                        <div style="font-weight:600;margin-bottom:4px;">Payment Status</div>
                        ' . $this->statusBadge($q_p_status) . '
                    </div>
                    <hr style="margin:6px 0;">
                    <div class="d-flex justify-content-between"><span>Subtotal</span><span>&#8369; ' . $this->format_price($subtotal) . '</span></div>
                    <div class="d-flex justify-content-between text-muted">
                        <span>Fee (1%) <small>(Convenience Fee)</small></span>
                        <span>' . $to_admin_payment . '</span>
                    </div>
                    <hr style="margin:6px 0;">
                    <div class="d-flex justify-content-between align-items-center p-2 rounded" style="background:#e9f7ef;font-size:15px;">
                        <span style="font-weight:700;">Total</span>
                        <span style="font-weight:bold;">&#8369; ' . $this->format_price($total_payment) . '</span>
                    </div>
                </div>'
                ];

                if ($q_status == 'RESERVED') {
                    $data[] = ['<button class="btn btn-danger btn-block w-100" onclick="cancelOrder()">
                    <i class="fa fa-times mr-1"></i> Cancel Order
                </button>'];
                }
            }

            echo json_encode([
                'draw'            => intval($requestData['draw']),
                'recordsTotal'    => 10000,
                'recordsFiltered' => 10000,
                'data'            => $data,
            ]);
        }
    }


    public function payprocessingfee()
    {

        $this->db->trans_begin();
        $person_id = $this->session->agrishop_person_id;
        $dateNow = $this->now();
        $true = ["success"   => true];
        $false = ["success"   => false];
        $trans_id = $this->input->post('trans_id');
        $convenience_fee = $this->input->post('convenience_fee');
        $data_transaction_proof_of_payment = [];
        if (!empty($_FILES['paymentProof'])) {
            if (isset($_FILES['paymentProof']) && $_FILES['paymentProof']['error'] === UPLOAD_ERR_OK) {
                // Normal upload
                $upload = $this->uploadImg($_FILES['paymentProof'], $person_id . $trans_id, 'payment_to_admin', 'paymentProof');
                $data_transaction_proof_of_payment = [
                    "transaction_id" => $trans_id,
                    "amount" => $convenience_fee,
                    "img_path" => $upload,
                    "approved_at" => $dateNow, //for test to be delete
                    "approved_by" => 1, //for test to be delete
                ];
            }
        }

        if ($this->db->insert("transaction_payment_to_admin", $data_transaction_proof_of_payment)) {
            $this->getTransactionStatus($person_id, 'TO_BE_VERIFIED', 'client');
            $true += ["message"   => "Successfully paid processing fee!"];
            $ret = $true;
        } else {
            $false += ["message"   => "Failed to add to cart!"];
            $ret = $false;
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }

        echo json_encode($ret);
    }


    public function submit_order()
    {
        $this->db->trans_begin();
        $person_id = $this->session->agrishop_person_id;
        $dateNow = $this->now();
        $true = ["success"   => true];
        $false = ["success"   => false];


        $pay      = $this->input->post('pay');
        $total    = $this->input->post('total');
        $delivery = $this->input->post('delivery');     // pickup / cod
        $transaction_id = $this->input->post('trans_id');
        $number   = $this->input->post('number');
        $name     = $this->input->post('name');
        $subtotal = $this->input->post('subtotal');
        $percentage = $this->input->post('percentage');
        $proof    = $this->input->post('proof_of_payment');


        // $check_qty_left = $this->db->query("SELECT pql.qty_left, name FROM price_qty_left pql
        //                                     LEFT JOIN produce p on pql.produce_id = p.id
        //                                     WHERE pql.id = $farm_produce_id LIMIT 1")->row();
        $price_qty_left = $this->price_qty_left();
        $check_qty_left = $this->db->query("SELECT pql.id,pql.qty_left,qq.qty_to_be_checkout,p.name FROM ($price_qty_left) pql 
            JOIN  (SELECT mcfp.farm_produce_id, sum(mcfp.qty) AS qty_to_be_checkout FROM my_cart_farm_produce mcfp  
                    WHERE mcfp.transaction_id = $transaction_id
                    GROUP BY farm_produce_id) qq ON pql.id=qq.farm_produce_id AND qq.qty_to_be_checkout>pql.qty_left
            JOIN produce p ON pql.produce_id = p.id")->result();
        if (!empty($check_qty_left)) {
            $item = $check_qty_left[0];
            echo json_encode([
                "success" => false,
                "message" => "Quantity for {$item->name} left is {$item->qty_left}, not enough!"
            ]);
            return;
        }

        // Add promo subtotal into the overall subtotal for this transaction
        $promo_sub = $this->db->query(
            "SELECT COALESCE(SUM(sub_total),0) AS total FROM my_cart_promo WHERE transaction_id = ?",
            [$transaction_id]
        )->row()->total;
        $subtotal = (float)$subtotal + (float)$promo_sub;


        // FILE (GCash proof)
        if (!empty($_FILES['proof_of_payment'])) {
            if (isset($_FILES['proof_of_payment']) && $_FILES['proof_of_payment']['error'] === UPLOAD_ERR_OK) {
                // Normal upload
                $upload = $this->uploadImg($_FILES['proof_of_payment'], $person_id . $pay, 'proof_payment', 'proof_of_payment');
                $data_transaction_proof_of_payment = [
                    "transaction_id" => $transaction_id,
                    "img" => $upload,
                    "total_amount" => $total,
                    "contact_number" => $number,
                    "name" => $name,
                ];
                $this->db->insert("transaction_proof_of_payment", $data_transaction_proof_of_payment);
            }
        }


        // 💳 PAYMENT STATUS
        if ($pay == 'gcash') {
            $payment_status = 'VERIFYING';
        } else {
            $payment_status = 'UNPAID';
        }

        // 🚚 DELIVERY STATUS
        if ($delivery == 'pickup') {
            $delivery_status = 'TO_BE_PICKUP';
        } else {
            $delivery_status = 'TO_BE_DELIVER';
        }

        // 🚚 DELIVERY STATUS
        $data_delivery_status = [
            'transaction_id' => $transaction_id,
            'status' => $delivery_status,
            'created_by_person_id' => $person_id,
        ];
        $this->transaction_delivery_status($data_delivery_status);

        // 🧾 PAYMENT STATUS
        $data_payment_status = [
            'transaction_id' => $transaction_id,
            'status' => $payment_status,
            'created_by_person_id' => $person_id,
        ];
        $this->transaction_payment_status($data_payment_status);

        // 🧾 TRANSACTION STATUS
        $data_transaction_status = [
            'transaction_id' => $transaction_id,
            'status' => 'RESERVED',
            'created_by_person_id' => $person_id,
        ];
        $this->transaction_status($data_transaction_status);

        // 🧾 TRANSACTION DONE
        $data_transaction = [
            'is_done' => true,
            'done_at' => $this->now(),
        ];
        $this->db->update("transaction", $data_transaction, ["id" => $transaction_id]);



        $data_transaction_details = [
            'transaction_id' => $transaction_id,
            'checkout_at' => $this->now(),
            'payment_method' => $pay,
            'delivery_method' => $delivery,
            'total_payment' => $total,
            'to_admin' => $subtotal * $percentage,
            'to_farmer' => $subtotal,
            'to_admin_percent' => $percentage,
        ];

        if ($this->db->insert("transaction_details", $data_transaction_details)) {
            $cp = $this->getTransactionStatus($person_id, 'PENDING', 'client');
            $true += ["message" => "Checkout success!", "cart_pending" => $cp];
            $ret = $true;

            // 🔔 Notify supplier if this is a supply transaction
            $trans_check = $this->db->query(
                "SELECT t.supplier_id, sup.person_id AS supplier_person_id
                 FROM transaction t
                 JOIN supplier sup ON t.supplier_id = sup.id
                 WHERE t.id = ? LIMIT 1",
                [$transaction_id]
            )->row();

            if ($trans_check && $trans_check->supplier_person_id) {
                $buyer_name = $this->getPersonName($person_id);
                $this->notify(
                    $trans_check->supplier_person_id,
                    '🛒 New Order Received!',
                    "You have a new supply order from {$buyer_name}. Please review and prepare.",
                    'SUCCESS',
                    'transaction',
                    $transaction_id
                );
            }
        } else {
            $false += ["message" => "Failed to add to cart!"];
            $ret = $false;
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }

        echo json_encode($ret);
    }

    public function cancel_order()
    {

        $this->db->trans_begin();
        $person_id = $this->session->agrishop_person_id;
        $dateNow = $this->now();
        $true = ["success"   => true];
        $false = ["success"   => false];

        $reason = $this->input->post('cancel_reason');
        $transaction_id = $this->input->post('trans_id');

        $check = $this->checkTransactionStatus($transaction_id);

        if ($check == 'PREPARING' || $check == 'DELIVERED' || $check == 'CANCELLED') {
            $false += ["message"   => "You cannot cancel anymore this order!"];
            $ret = $false;
            echo json_encode($ret);
            return;
        }

        // 🧾 TRANSACTION STATUS
        $data_transaction_status = [
            'transaction_id' => $transaction_id,
            'status' => 'CANCELLED',
            'created_by_person_id' => $person_id,
        ];
        $this->transaction_status($data_transaction_status);


        $data_transaction_cancel_details = [
            'transaction_id' => $transaction_id,
            'reason' => $reason,
            'created_at' =>  $this->now(),
            'created_by' => $person_id,
        ];


        // 🧾 TRANSACTION DONE
        $data_transaction = [
            'is_done' => true,
            'done_at' => $this->now(),
        ];
        $this->db->update("transaction", $data_transaction, ["id" => $transaction_id]);

        if ($this->db->insert("transaction_cancel", $data_transaction_cancel_details)) {
            $cp = $this->getTransactionStatus($person_id, 'PENDING', 'client');
            $true += ["message"   => "Order cancelled!", "cart_pending"   => $cp];
            $ret = $true;
        } else {
            $false += ["message"   => "Failed to cancel order!"];
            $ret = $false;
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
        } else {
            $this->db->trans_commit();
        }

        echo json_encode($ret);
    }

    public function search_barangay()
    {
        $keyword = $this->input->post('keyword');
        $limit = $this->input->post('limit') ?: 10;
        if (strlen($keyword) < 3) {
            echo json_encode([]);
            return;
        }

        // Split the keyword into individual words
        $words = explode(' ', $keyword);

        // Build dynamic "LIKE" filters
        $conditions = "";
        foreach ($words as $w) {
            $w = trim($w);
            if ($w !== "") {
                $conditions .= " AND CONCAT(
                t1.description,' ',
                t2.description,', ',
                t3.description,', ',
                t4.region
            ) COLLATE utf8mb4_general_ci LIKE '%" . $this->db->escape_like_str($w) . "%' ";
            }
        }

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
                                    WHERE 1=1 
                                    $conditions
                                    ORDER BY t1.description 
                                    LIMIT $limit");

        $results = [];
        foreach ($query->result() as $row) {
            $results[] = [
                'id'   => $row->id,
                'text' => $row->address
            ];
        }

        echo json_encode($results);
    }

    public function search_barangay_caraga()
    {
        $keyword = $this->input->post('keyword');
        $limit = $this->input->post('limit') ?: 10;
        if (strlen($keyword) < 3) {
            echo json_encode([]);
            return;
        }

        // Split the keyword into individual words
        $words = explode(' ', $keyword);

        // Build dynamic "LIKE" filters
        $conditions = "";
        foreach ($words as $w) {
            $w = trim($w);
            if ($w !== "") {
                $conditions .= " AND CONCAT(
                t1.description,' ',
                t2.description,', ',
                t3.description,', ',
                t4.region
            ) COLLATE utf8mb4_general_ci LIKE '%" . $this->db->escape_like_str($w) . "%' ";
            }
        }

        $query = $this->db->query("SELECT 
                                        t1.id,
                                        t1.gid,
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
                                    WHERE 1=1 
                                    $conditions
                                    ORDER BY t1.description 
                                    LIMIT $limit");

        $results = [];
        foreach ($query->result() as $row) {
            $results[] = [
                'id'   => $row->id,
                'gid'   => $row->gid,
                'text' => $row->address
            ];
        }

        echo json_encode($results);
    }
}

// farmer_id int4 NOT NULL,
// farm_name varchar(255) NOT NULL,
// total_area_sqm numeric(10, 2) NULL,
// barangay_id int8 NULL,
// geo_polygon text NULL,
// soil_type varchar(100) NULL,
// is_active bool DEFAULT true NOT NULL,
// created_at timestamp(6) DEFAULT now() NULL,
// coordinates text NULL,
// img_path text NULL,
// created_by_person_id int4 DEFAULT 1 NOT NULL,
// created_by_person_id int4 DEFAULT 1 NOT NULL,