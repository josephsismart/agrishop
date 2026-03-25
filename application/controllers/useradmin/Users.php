<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->redirect();
    }

    public function index()
    {
        $page_data = $this->system();
        $uri = $this->session->agrishop_login_uri;
        $page_data += [
            "page_title"       => "Users",
            "current_location" => "users",
            "content"          => [$this->load->view('interface/' . $uri . '/Users', [
                "pending_farmers"   => $this->_countPending('farmer'),
                "pending_suppliers" => $this->_countPending('supplier'),
            ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }

    // ── Count pending approvals ───────────────────────────────
    private function _countPending($type)
    {
        $table = $type === 'supplier' ? 'supplier' : 'farmer';
        $r = $this->db->query("
            SELECT COUNT(1) AS c FROM $table
            WHERE approved_by_person_id IS NULL AND is_active = 1
        ")->row();
        return (int) ($r->c ?? 0);
    }

    // ── Datatable: all users ──────────────────────────────────
    public function getUsersInfo()
    {
        $requestData = $_REQUEST;
        $tab         = isset($requestData['search']['tab']) ? $requestData['search']['tab'] : 'all';
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        list($limit, $offset) = $this->calculatePagination($requestData);

        // Tab filter
        $tab_where = '';
        if ($tab === 'farmer')   $tab_where = "AND t3.id IS NOT NULL AND t_sup.id IS NULL";
        if ($tab === 'supplier') $tab_where = "AND t_sup.id IS NOT NULL";
        if ($tab === 'customer') $tab_where = "AND t3.id IS NULL AND t_sup.id IS NULL";
        if ($tab === 'pending')  $tab_where = "AND (
            (t3.id IS NOT NULL AND t3.approved_by_person_id IS NULL) OR
            (t_sup.id IS NOT NULL AND t_sup.approved_by_person_id IS NULL)
        )";

        $base_query = "FROM user t1
            LEFT JOIN person t2 ON t1.person_id = t2.id
            LEFT JOIN farmer t3 ON t2.id = t3.person_id
            LEFT JOIN supplier t_sup ON t2.id = t_sup.person_id
            WHERE CONCAT(
                COALESCE(t1.username,''), COALESCE(t2.first_name,''),
                COALESCE(t2.last_name,''), COALESCE(t2.contact_num,''),
                COALESCE(t2.email_address,'')
            ) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
            $tab_where";

        $totalRecords = $this->db->query("SELECT COUNT(1) AS total $base_query")->row()->total ?? 0;

        $query = $this->db->query("
            SELECT
                t1.id, t1.username, t1.is_active, t1.created_at,
                t2.id AS person_id, t2.first_name, t2.last_name,
                t2.contact_num, t2.email_address, t2.barangay_id, t2.img_path,
                t3.id AS farmer_id, t3.id_img_path AS farmer_id_img,
                t3.farmer_selling_type, t3.approved_by_person_id AS farmer_approved_by,
                t3.approved_at AS farmer_approved_at,
                t_sup.id AS supplier_id, t_sup.id_img_path AS supplier_id_img,
                t_sup.organization AS supplier_business,
                t_sup.approved_by_person_id AS supplier_approved_by,
                t_sup.approved_at AS supplier_approved_at
            $base_query
            ORDER BY t1.id DESC
            LIMIT $limit OFFSET $offset
        ");

        if (!$query) {
            echo json_encode(['draw' => intval($requestData['draw']), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
            return;
        }

        $data = [];
        $cc   = $offset + 1;
        foreach ($query->result() as $v) {

            // User type & role badge
            if ($v->supplier_id) {
                $role_badge = "<span class='badge bg-warning text-dark'>Supplier</span>";
                $type       = 'supplier';
                $approved_by = $v->supplier_approved_by;
                $approved_at = $v->supplier_approved_at;
                $id_img      = $v->supplier_id_img;
                $entity_id   = $v->supplier_id;
            } elseif ($v->farmer_id) {
                $seller = $v->farmer_selling_type == 1 ? 'Produce' : ($v->farmer_selling_type == 2 ? 'Tools' : 'Both');
                $role_badge = "<span class='badge bg-success'>Farmer ($seller)</span>";
                $type       = 'farmer';
                $approved_by = $v->farmer_approved_by;
                $approved_at = $v->farmer_approved_at;
                $id_img      = $v->farmer_id_img;
                $entity_id   = $v->farmer_id;
            } else {
                $role_badge  = "<span class='badge bg-primary'>Customer</span>";
                $type        = 'customer';
                $approved_by = null;
                $approved_at = null;
                $id_img      = null;
                $entity_id   = null;
            }

            // Approval badge
            if ($type !== 'customer') {
                $id_img_url = !empty($id_img) ? base_url($id_img) : '';
                if ($approved_by) {
                    $approval_badge = "<span class='badge bg-info'><i class='fa fa-check mr-1'></i>"
                        . date('m-d-Y', strtotime($approved_at)) . "</span>";
                } else {
                    $approval_badge = "<span class='badge bg-warning text-dark' style='cursor:pointer;'
                        onclick='openApproveModal(\"$type\", $entity_id, \"$id_img_url\")'
                        id='approveBtn_{$type}_{$entity_id}'>
                        <i class='fa fa-exclamation-triangle mr-1'></i> FOR APPROVAL
                    </span>";
                }
            } else {
                $approval_badge = "—";
            }

            // Profile image
            $img_src = (!empty($v->img_path) && file_exists(FCPATH . $v->img_path))
                ? base_url($v->img_path)
                : base_url('dist/img/media/icons/1x1.png');

            $is_active_badge = $v->is_active
                ? "<span class='badge bg-success'>Active</span>"
                : "<span class='badge bg-danger'>Inactive</span>";

            // Active/Deactivate button
            $toggle_btn = "<button class='btn btn-xs " . ($v->is_active ? "btn-danger" : "btn-success") . " ml-1'
                onclick='toggleUser({$v->id}, " . ($v->is_active ? 0 : 1) . ")'
                title='" . ($v->is_active ? "Deactivate" : "Activate") . "'>
                <i class='fa fa-" . ($v->is_active ? "ban" : "check") . "'></i>
            </button>";

            $reset_btn = "<button class='btn btn-xs btn-warning ml-1'
                onclick='resetPassword({$v->id})'
                title='Reset Password to agrishop123'>
                <i class='fa fa-key'></i>
            </button>";

            $data[] = [
                $cc++,
                "<img src='$img_src' width='45' height='45' class='rounded-circle'>",
                "<b>" . $this->getPersonName($v->person_id) . "</b><br>$role_badge",
                "<small class='text-muted'><i class='fa fa-user mr-1'></i>" . $v->username . "</small>",
                (!empty($v->created_at) ? date('M d, Y', strtotime($v->created_at)) : '—') . "<br>$approval_badge",
                "📧 " . ($v->email_address ?: '—') . "<br>📞 " . ($v->contact_num ?: '—'),
                "<small>" . $this->getAddress($v->barangay_id) . "</small>",
                "$is_active_badge $toggle_btn $reset_btn",
            ];
        }

        echo json_encode([
            'draw'            => intval($requestData['draw']),
            'recordsTotal'    => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords),
            'data'            => $data,
        ]);
    }

    // ── Approve farmer ────────────────────────────────────────
    public function approveFarmer()
    {
        $farmer_id = $this->input->post('farmer_id');
        $person_id = $this->session->agrishop_person_id;

        $farmer = $this->db->query("SELECT person_id FROM farmer WHERE id = ? LIMIT 1", [$farmer_id])->row();

        $this->db->update('farmer', [
            'approved_by_person_id' => $person_id,
            'approved_at'           => date('Y-m-d H:i:s'),
            'date_registered'       => date('Y-m-d H:i:s'),
            'is_active'             => 1,
        ], ['id' => $farmer_id]);
        // Also make sure the user account is active
        $this->db->query(
            "UPDATE user u JOIN farmer f ON u.person_id = f.person_id SET u.is_active = 1 WHERE f.id = ?",
            [$farmer_id]
        );

        // Notify the farmer
        if ($farmer) {
            $this->notify(
                $farmer->person_id,
                'Application Approved! ✅',
                'Congratulations! Your farmer registration has been approved. You can now access your dashboard.',
                'SUCCESS',
                'farmer',
                $farmer_id
            );
        }

        echo json_encode(['success' => true, 'message' => 'Farmer approved!']);
    }

    // ── Approve supplier ──────────────────────────────────────
    public function approveSupplier()
    {
        $supplier_id = $this->input->post('supplier_id');
        $person_id   = $this->session->agrishop_person_id;

        $supplier = $this->db->query("SELECT person_id FROM supplier WHERE id = ? LIMIT 1", [$supplier_id])->row();

        $this->db->update('supplier', [
            'approved_by_person_id' => $person_id,
            'approved_at'           => date('Y-m-d H:i:s'),
            'date_registered'       => date('Y-m-d H:i:s'),
            'is_active'             => 1,
        ], ['id' => $supplier_id]);
        // Also make sure the user account is active
        $this->db->query(
            "UPDATE user u JOIN supplier s ON u.person_id = s.person_id SET u.is_active = 1 WHERE s.id = ?",
            [$supplier_id]
        );

        // Notify the supplier
        if ($supplier) {
            $this->notify(
                $supplier->person_id,
                'Application Approved! ✅',
                'Congratulations! Your supplier registration has been approved. You can now access your dashboard.',
                'SUCCESS',
                'supplier',
                $supplier_id
            );
        }

        echo json_encode(['success' => true, 'message' => 'Supplier approved!']);
    }

    // ── Reject farmer/supplier ────────────────────────────────
    public function rejectUser()
    {
        $type      = $this->input->post('type');   // farmer | supplier
        $entity_id = $this->input->post('id');
        $reason    = $this->input->post('reason');
        $person_id = $this->session->agrishop_person_id;

        if ($type === 'farmer') {
            $row = $this->db->query("SELECT person_id FROM farmer WHERE id = ? LIMIT 1", [$entity_id])->row();
            // Deactivate user account
            $this->db->query("UPDATE user u JOIN farmer f ON u.person_id = f.person_id
                SET u.is_active = 0 WHERE f.id = ?", [$entity_id]);
            $this->db->update('farmer', ['is_active' => 0], ['id' => $entity_id]);
        } else {
            $row = $this->db->query("SELECT person_id FROM supplier WHERE id = ? LIMIT 1", [$entity_id])->row();
            $this->db->query("UPDATE user u JOIN supplier s ON u.person_id = s.person_id
                SET u.is_active = 0 WHERE s.id = ?", [$entity_id]);
            $this->db->update('supplier', ['is_active' => 0], ['id' => $entity_id]);
        }

        if ($row) {
            $this->notify(
                $row->person_id,
                'Application Rejected ❌',
                'Your ' . $type . ' registration was not approved. Reason: ' . ($reason ?: 'No reason given.'),
                'DANGER',
                $type,
                $entity_id
            );
        }

        echo json_encode(['success' => true, 'message' => ucfirst($type) . ' rejected.']);
    }

    // ── Toggle user active/inactive ───────────────────────────
    public function toggleUser()
    {
        $user_id   = $this->input->post('user_id');
        $is_active = $this->input->post('is_active');
        $this->db->update('user', ['is_active' => $is_active], ['id' => $user_id]);
        echo json_encode(['success' => true]);
    }

    // ── Check approval status (called from Pending page) ──────
    public function checkApprovalStatus()
    {
        $person_id = $this->session->agrishop_person_id;
        $level     = $this->session->agrishop_login_level;

        if ($level == 2) {
            // Farmer
            $row = $this->db->query("
                SELECT f.approved_by_person_id, f.id AS farmer_id
                FROM farmer f WHERE f.person_id = ? LIMIT 1
            ", [$person_id])->row();

            if ($row && $row->approved_by_person_id) {
                // Update session
                $this->session->set_userdata([
                    'agrishop_login_farmer_id' => $row->farmer_id,
                ]);
                echo json_encode(['approved' => true, 'redirect' => base_url('userfarmer/dashboard')]);
            } else {
                echo json_encode(['approved' => false]);
            }
        } elseif ($level == 4) {
            // Supplier
            $row = $this->db->query("
                SELECT s.approved_by_person_id, s.id AS supplier_id
                FROM supplier s WHERE s.person_id = ? LIMIT 1
            ", [$person_id])->row();

            if ($row && $row->approved_by_person_id) {
                $this->session->set_userdata([
                    'agrishop_login_supplier_id' => $row->supplier_id,
                ]);
                echo json_encode(['approved' => true, 'redirect' => base_url('usersupplier/dashboard')]);
            } else {
                echo json_encode(['approved' => false]);
            }
        } else {
            echo json_encode(['approved' => false]);
        }
    }

    // ── Reset user password to default ───────────────────────────
    public function resetPassword()
    {
        $user_id   = $this->input->post('user_id');
        $person_id = $this->session->agrishop_person_id;

        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid user.']);
            return;
        }

        $default_password = md5('agrishop123');

        $updated = $this->db->update(
            'user',
            ['password' => $default_password],
            ['id' => $user_id]
        );

        if ($updated) {
            // Get user's person_id to notify them
            $user = $this->db->query(
                "SELECT person_id FROM user WHERE id = ? LIMIT 1",
                [$user_id]
            )->row();

            if ($user) {
                $this->notify(
                    $user->person_id,
                    '🔑 Password Reset',
                    'Your password has been reset by admin. New password: agrishop123 — please change it after logging in.',
                    'WARNING'
                );
            }

            echo json_encode(['success' => true, 'message' => 'Password reset to agrishop123 successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to reset password.']);
        }
    }
}
/* End of file Users.php */
/* Location: ./application/controllers/useradmin/Users.php */
