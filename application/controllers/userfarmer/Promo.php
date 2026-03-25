<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Promo extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->redirect();
        date_default_timezone_set('Asia/Manila');
    }

    public function index()
    {
        $page_data  = $this->system();
        $uri        = $this->session->agrishop_login_uri;
        $page_data += [
            'page_title'       => 'Promo & Discounts',
            'current_location' => 'Promo',
            'content'          => [$this->load->view('interface/' . $uri . '/Promo', [
                'billing' => $this->subscription_count(),
            ], TRUE)],
        ];
        $this->public_create_page($page_data);
    }

    // ── DataTable list ──────────────────────────────────────
    public function getPromoList()
    {
        $requestData = $_REQUEST;
        $farmer_id   = (int) $this->session->agrishop_login_farmer_id;
        $searchValue = $this->db->escape_like_str(
            isset($requestData['search']['value']) ? $requestData['search']['value'] : ''
        );
        list($limit, $offset) = $this->calculatePagination($requestData);

        $base = "FROM promo_discount pd
            LEFT JOIN farm_produce fp ON pd.farm_produce_id = fp.id
            LEFT JOIN produce pr      ON fp.produce_id = pr.id
            WHERE pd.farmer_id = $farmer_id
            AND CONCAT(COALESCE(pd.title,''), COALESCE(pr.name,''), COALESCE(pd.description,''))
                COLLATE utf8mb4_general_ci LIKE '%$searchValue%'";

        $total = (int) $this->db->query("SELECT COUNT(1) AS t $base")->row()->t;

        $rows = $this->db->query("
            SELECT pd.*, pr.name AS produce_name,
                   DATEDIFF(pd.valid_until, CURDATE()) AS days_left
            $base ORDER BY pd.id DESC LIMIT $limit OFFSET $offset
        ")->result();

        $data = [];
        $cc   = $offset + 1;
        foreach ($rows as $r) {
            $img = !empty($r->img_path)
                ? base_url($r->img_path)
                : base_url('dist/img/media/icons/1x1.png');

            $days_badge = $r->days_left < 0
                ? "<span class='badge badge-danger'>Expired</span>"
                : ($r->days_left == 0
                    ? "<span class='badge badge-warning'>Ends Today</span>"
                    : "<span class='badge badge-info'>{$r->days_left} days left</span>");

            $active_badge = $r->is_active
                ? "<span class='badge badge-success'>Active</span>"
                : "<span class='badge badge-secondary'>Inactive</span>";

            $data[] = [
                $cc++,
                "<img src='$img' width='55' height='55' class='rounded' style='object-fit:cover;'>",
                "<b>" . htmlspecialchars($r->title) . "</b><br>"
                    . "<small class='text-muted'>" . htmlspecialchars($r->produce_name ?? '—') . "</small>",
                "<del class='text-muted'>₱" . number_format($r->original_price, 2) . "</del>"
                    . " &rarr; <b class='text-success'>₱" . number_format($r->discounted_price, 2) . "</b>"
                    . "<br><span class='badge badge-danger'>" . $r->discount_percent . "% OFF</span>",
                $r->valid_from . "<br>to<br>" . $r->valid_until . "<br>" . $days_badge,
                $active_badge,
                "<button class='btn btn-xs btn-warning mr-1' onclick='editPromo({$r->id})'>"
                    . "<i class='fa fa-edit'></i></button>"
                    . "<button class='btn btn-xs btn-danger' onclick='deletePromo({$r->id})'>"
                    . "<i class='fa fa-trash'></i></button>",
            ];
        }

        echo json_encode([
            'draw'            => intval($requestData['draw']),
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $data,
        ]);
    }

    // ── Save (insert/update) ────────────────────────────────
    public function savePromo()
    {
        $this->db->trans_begin();
        $farmer_id = (int) $this->session->agrishop_login_farmer_id;
        $person_id = (int) $this->session->agrishop_person_id;
        $promo_id  = (int) $this->input->post('promo_id');

        $data = [
            'farmer_id'        => $farmer_id,
            'farm_produce_id'  => $this->input->post('farm_produce_id') ?: null,
            'title'            => $this->input->post('title'),
            'description'      => $this->input->post('description'),
            'original_price'   => $this->input->post('original_price'),
            'discounted_price' => $this->input->post('discounted_price'),
            'valid_from'       => $this->input->post('valid_from'),
            'valid_until'      => $this->input->post('valid_until'),
            'max_qty'          => $this->input->post('max_qty') ?: null,
            'is_active'        => 1,
            'created_by_person_id' => $person_id,
        ];

        // Image upload
        if (!empty($_FILES['promo_img']['name'])) {
            $upload = $this->uploadImg($_FILES['promo_img'], $person_id . '_promo', 'promo', 'promo_img');
            if ($upload) $data['img_path'] = $upload;
        }

        if ($promo_id) {
            unset($data['created_by_person_id']);
            $this->db->update('promo_discount', $data, ['id' => $promo_id, 'farmer_id' => $farmer_id]);
            $msg = 'Promo updated!';
        } else {
            $this->db->insert('promo_discount', $data);
            $msg = 'Promo created!';
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            echo json_encode(['success' => false, 'message' => 'Something went wrong.']);
        } else {
            $this->db->trans_commit();
            echo json_encode(['success' => true, 'message' => $msg]);
        }
    }

    // ── Get single promo for edit ───────────────────────────
    public function getPromo()
    {
        $id        = (int) $this->input->get('id');
        $farmer_id = (int) $this->session->agrishop_login_farmer_id;
        $row = $this->db->query("SELECT * FROM promo_discount WHERE id=? AND farmer_id=? LIMIT 1",
            [$id, $farmer_id])->row();
        echo json_encode($row ?: null);
    }

    // ── Delete ──────────────────────────────────────────────
    public function deletePromo()
    {
        $id        = (int) $this->input->post('id');
        $farmer_id = (int) $this->session->agrishop_login_farmer_id;
        $this->db->delete('promo_discount', ['id' => $id, 'farmer_id' => $farmer_id]);
        echo json_encode(['success' => true]);
    }

    // ── Toggle active ───────────────────────────────────────
    public function togglePromo()
    {
        $id        = (int) $this->input->post('id');
        $val       = (int) $this->input->post('is_active');
        $farmer_id = (int) $this->session->agrishop_login_farmer_id;
        $this->db->update('promo_discount', ['is_active' => $val], ['id' => $id, 'farmer_id' => $farmer_id]);
        echo json_encode(['success' => true]);
    }

    // ── Public: get active promos for landing page ──────────
    public function getActivePromos()
    {
        $today = date('Y-m-d');
        $rows  = $this->db->query("
            SELECT pd.id, pd.title, pd.description, pd.original_price,
                   pd.discounted_price, pd.discount_percent, pd.img_path,
                   pd.valid_until, pd.max_qty,
                   pr.name AS produce_name,
                   CONCAT(p.first_name, ' ', p.last_name) AS farmer_name,
                   person.img_path AS farmer_img
            FROM promo_discount pd
            LEFT JOIN farm_produce fp  ON pd.farm_produce_id = fp.id
            LEFT JOIN produce pr       ON fp.produce_id = pr.id
            LEFT JOIN farmer f         ON pd.farmer_id = f.id
            LEFT JOIN person p         ON f.person_id = p.id
            LEFT JOIN person person    ON f.person_id = person.id
            WHERE pd.is_active = 1 AND pd.valid_from <= '$today' AND pd.valid_until >= '$today'
            ORDER BY pd.discount_percent DESC
            LIMIT 12
        ")->result();

        $data = [];
        foreach ($rows as $r) {
            $data[] = [
                'id'                => $r->id,
                'title'             => $r->title,
                'description'       => $r->description,
                'original_price'    => number_format($r->original_price, 2),
                'discounted_price'  => number_format($r->discounted_price, 2),
                'discount_percent'  => $r->discount_percent,
                'valid_until'       => $r->valid_until,
                'produce_name'      => $r->produce_name,
                'farmer_name'       => $r->farmer_name,
                'img'               => $r->img_path ? base_url($r->img_path) : base_url('dist/img/media/icons/1x1.png'),
                'farmer_img'        => $r->farmer_img ? base_url($r->farmer_img) : base_url('dist/img/media/icons/1x1.png'),
            ];
        }
        echo json_encode($data);
    }
}

/* End of file Promo.php */
