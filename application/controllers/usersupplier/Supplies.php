<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Supplies extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->redirect();
        $this->load->helper('date');
        date_default_timezone_set("Asia/Manila");
    }

    public function index()
    {
        $page_data = $this->system();
        $uri = $this->session->agrishop_login_uri;
        $page_data += [
            "page_title"        => "My Supplies",
            "current_location"  => "Supplies",
            "content"           => [$this->load->view('interface/' . $uri . '/Supplies', [
                "billing"     => $this->supplier_billing_count(),
                "categories"  => $this->getCategories(),
                "stores"      => $this->getMyStores(),
            ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }

    // ── Supplier billing count (unpaid invoices) ──────────────
    public function supplier_billing_count()
    {
        $supplier_id = (int) $this->session->agrishop_login_supplier_id;
        if (!$supplier_id) return ["count" => 0];

        $result = $this->db->query("
            SELECT COUNT(1) AS count
            FROM supplier_invoice_billing
            WHERE supplier_id = ?
              AND is_paid = false
        ", [$supplier_id]);

        if (!$result) return ["count" => 0];
        $row = $result->row();
        return ["count" => (int) ($row->count ?? 0)];
    }

    // ── Datatable: supply list ────────────────────────────────
    public function getSupplyList()
    {
        $requestData    = $_REQUEST;
        $supplier_id    = (int) $this->session->agrishop_login_supplier_id;
        $searchValue    = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        // Guard: supplier_id must exist
        if (!$supplier_id) {
            echo json_encode(['draw' => intval($requestData['draw'] ?? 1), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
            return;
        }

        list($limit, $offset) = $this->calculatePagination($requestData);

        $countResult = $this->db->query("
            SELECT COUNT(1) AS total
            FROM supplier_supply ss
            JOIN supply_category sc ON ss.supply_category_id = sc.id
            WHERE ss.supplier_id = ?
              AND CONCAT(ss.name, sc.name, COALESCE(ss.brand,''), ss.uom,
                         CASE WHEN ss.is_active = 1 THEN 'ACTIVE' ELSE 'INACTIVE' END)
                  COLLATE utf8mb4_general_ci LIKE ?
        ", [$supplier_id, '%' . $searchValue . '%']);

        $totalRecords = ($countResult && $countResult->row()) ? (int) $countResult->row()->total : 0;

        $query = $this->db->query("
            SELECT ss.id, ss.name, ss.brand, ss.uom, ss.price, ss.qty_available,
                   ss.img_path, ss.is_active, ss.created_at,
                   sc.name AS category,
                   st.store_name
            FROM supplier_supply ss
            JOIN supply_category sc ON ss.supply_category_id = sc.id
            LEFT JOIN supplier_store st ON ss.store_id = st.id
            WHERE ss.supplier_id = ?
              AND CONCAT(ss.name, sc.name, COALESCE(ss.brand,''), ss.uom,
                         CASE WHEN ss.is_active = 1 THEN 'ACTIVE' ELSE 'INACTIVE' END)
                  COLLATE utf8mb4_general_ci LIKE ?
            ORDER BY ss.created_at DESC
            LIMIT $limit OFFSET $offset
        ", [$supplier_id, '%' . $searchValue . '%']);

        $data = [];
        if (!$query) {
            echo json_encode(['draw' => intval($requestData['draw'] ?? 1), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
            return;
        }
        foreach ($query->result() as $value) {
            $img = (!empty($value->img_path) && file_exists(FCPATH . $value->img_path))
                ? "<img src='" . base_url($value->img_path) . "' width='50' height='50' class='rounded'>"
                : "<i class='fas fa-box fa-2x text-muted'></i>";

            $is_active = $value->is_active
                ? "<span class='badge bg-success'>ACTIVE</span>"
                : "<span class='badge bg-danger'>INACTIVE</span>";

            $data[] = [
                $img,
                $value->name . ($value->brand ? "<br><small class='text-muted'>" . $value->brand . "</small>" : ""),
                $value->category,
                $value->store_name ?? "—",
                "₱ " . number_format($value->price, 2),
                number_format($value->qty_available, 0) . " " . $value->uom,
                $is_active,
                "<button class='btn btn-sm btn-primary' onclick='editSupply($value->id)'>
                    <i class='fa fa-edit'></i>
                </button>
                <button class='btn btn-sm btn-danger ml-1' onclick='toggleSupply($value->id, " . ($value->is_active ? 0 : 1) . ")'>
                    <i class='fa fa-" . ($value->is_active ? "ban" : "check") . "'></i>
                </button>"
            ];
        }

        echo json_encode([
            'draw'            => intval($requestData['draw']),
            'recordsTotal'    => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords),
            'data'            => $data,
        ]);
    }

    // ── Get single supply for edit modal ─────────────────────
    public function getSupply()
    {
        $id          = $this->input->post('id');
        $supplier_id = (int) $this->session->agrishop_login_supplier_id;

        if (!$id || !$supplier_id) { echo json_encode([]); return; }

        $result = $this->db->query("
            SELECT ss.*, sc.name AS category_name, st.store_name
            FROM supplier_supply ss
            JOIN supply_category sc ON ss.supply_category_id = sc.id
            LEFT JOIN supplier_store st ON ss.store_id = st.id
            WHERE ss.id = ? AND ss.supplier_id = ?
            LIMIT 1
        ", [$id, $supplier_id]);

        echo json_encode(($result && $result->row()) ? $result->row() : []);
    }

    // ── Save supply (create or update) ───────────────────────
    public function saveSupply()
    {
        $this->db->trans_begin();
        $true  = ["success" => true];
        $false = ["success" => false];

        $supplier_id    = (int) $this->session->agrishop_login_supplier_id;
        $person_id      = $this->session->agrishop_person_id;

        if (!$supplier_id) {
            echo json_encode(["success" => false, "message" => "Session expired. Please login again."]);
            return;
        }
        $id             = $this->input->post('id');
        $name           = strtoupper(trim($this->input->post('name')));
        $brand          = strtoupper(trim($this->input->post('brand')));
        $category_id    = $this->input->post('supply_category_id');
        $store_id       = $this->input->post('store_id');
        $uom            = strtoupper(trim($this->input->post('uom')));
        $price          = $this->input->post('price');
        $qty_available  = $this->input->post('qty_available');
        $description    = $this->input->post('description');
        $tags           = $this->input->post('tags');

        if (!$name || !$price || !$uom || !$category_id) {
            echo json_encode(["fill" => true]);
            return;
        }

        $data = [
            "supplier_id"        => $supplier_id,
            "supply_category_id" => $category_id,
            "store_id"           => $store_id ?: null,
            "name"               => $name,
            "brand"              => $brand,
            "uom"                => $uom,
            "price"              => $price,
            "qty_available"      => $qty_available ?: 0,
            "description"        => $description,
            "tags"               => $tags,
        ];

        // Handle image upload
        if (isset($_FILES['picSupply']) && $_FILES['picSupply']['error'] === UPLOAD_ERR_OK) {
            $upload = $this->uploadImg($_FILES['picSupply'], $name, 'supplier/supply', 'picSupply');
            $data["img_path"] = $upload;
        }

        if ($id) {
            // Update
            $result = $this->db->update("supplier_supply", $data, ["id" => $id, "supplier_id" => $supplier_id]);
        } else {
            // Insert
            $data["created_at"]            = date('Y-m-d H:i:s');
            $data["created_by_person_id"]  = $person_id;
            $data["is_active"]             = 1;
            $result = $this->db->insert("supplier_supply", $data);
        }

        if ($result) {
            $true  += ["message" => $id ? "Supply updated!" : "Supply added!"];
            $ret    = $true;
        } else {
            $false += ["message" => "Something went wrong!"];
            $ret    = $false;
        }

        $this->db->trans_status() === false
            ? $this->db->trans_rollback()
            : $this->db->trans_commit();

        echo json_encode($ret);
    }

    // ── Toggle supply active/inactive ────────────────────────
    public function toggleSupply()
    {
        $id          = $this->input->post('id');
        $is_active   = $this->input->post('is_active');
        $supplier_id = (int) $this->session->agrishop_login_supplier_id;

        if (!$supplier_id || !$id) { echo json_encode(["success" => false]); return; }

        $result = $this->db->update(
            "supplier_supply",
            ["is_active" => $is_active],
            ["id" => $id, "supplier_id" => $supplier_id]
        );

        echo json_encode(["success" => (bool) $result]);
    }

    // ── Store management ──────────────────────────────────────
    public function getMyStores()
    {
        $supplier_id = (int) $this->session->agrishop_login_supplier_id;
        if (!$supplier_id) return [];

        $result = $this->db->query("
            SELECT id, store_name, address_text, lat, lon
            FROM supplier_store
            WHERE supplier_id = ? AND is_active = 1
        ", [$supplier_id]);

        return $result ? $result->result() : [];
    }

    public function saveStore()
    {
        $this->db->trans_begin();
        $true  = ["success" => true];
        $false = ["success" => false];

        $supplier_id  = (int) $this->session->agrishop_login_supplier_id;
        $person_id    = $this->session->agrishop_person_id;

        if (!$supplier_id) {
            echo json_encode(["success" => false, "message" => "Session expired. Please login again."]);
            return;
        }
        $id           = $this->input->post('id');
        $store_name   = strtoupper(trim($this->input->post('store_name')));
        $barangay_id  = $this->input->post('barangay_id');
        $address_text = strtoupper(trim($this->input->post('address_text')));
        $lat          = $this->input->post('lat');
        $lon          = $this->input->post('lon');

        if (!$store_name) {
            echo json_encode(["fill" => true]);
            return;
        }

        $data = [
            "supplier_id"  => $supplier_id,
            "store_name"   => $store_name,
            "barangay_id"  => $barangay_id ?: null,
            "address_text" => $address_text,
            "lat"          => $lat ?: null,
            "lon"          => $lon ?: null,
        ];

        if (isset($_FILES['picStore']) && $_FILES['picStore']['error'] === UPLOAD_ERR_OK) {
            $upload = $this->uploadImg($_FILES['picStore'], $store_name, 'supplier/store', 'picStore');
            $data["img_path"] = $upload;
        }

        if ($id) {
            $this->db->update("supplier_store", $data, ["id" => $id, "supplier_id" => $supplier_id]);
        } else {
            $data["created_at"]           = date('Y-m-d H:i:s');
            $data["created_by_person_id"] = $person_id;
            $data["is_active"]            = 1;
            $this->db->insert("supplier_store", $data);
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $false += ["message" => "Something went wrong! DB Error: " . $this->db->error()['message']];
            echo json_encode($false);
        } else {
            $this->db->trans_commit();
            $true += ["message" => $id ? "Store updated!" : "Store added!"];
            echo json_encode($true);
        }
    }

    // ── Helper: categories for dropdowns ─────────────────────
    public function getCategories()
    {
        $result = $this->db->query("SELECT id, name FROM supply_category WHERE is_active = 1 ORDER BY name");
        return $result ? $result->result() : [];
    }
}

/* End of file Supplies.php */
/* Location: ./application/controllers/usersupplier/Supplies.php */