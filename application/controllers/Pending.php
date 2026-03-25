<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Root-level Pending controller
 * Accessible via: yoursite.com/pending
 * Delegates to system/Pending logic directly here
 * so CI can route /pending without subfolder confusion.
 */
class Pending extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SQL_BIG_SELECTS=1');
    }

    public function index()
    {
        // Must be logged in
        if (!$this->session->agrishop_login_id) {
            redirect(base_url('login'));
            return;
        }

        $level     = $this->session->agrishop_login_level;
        $person_id = $this->session->agrishop_person_id;

        // If farmer — check if already approved; redirect if so
        if ($level == 2) {
            $farmer = $this->db->query("
                SELECT approved_by_person_id, id FROM farmer
                WHERE person_id = ? LIMIT 1
            ", [$person_id])->row();

            if ($farmer && $farmer->approved_by_person_id) {
                $this->session->set_userdata([
                    'agrishop_login_uri'      => 'userfarmer',
                    'agrishop_login_landing'  => 'dashboard',
                    'agrishop_login_farmer_id'=> $farmer->id,
                ]);
                redirect(base_url('userfarmer/dashboard'));
                return;
            }

        } elseif ($level == 4) {
            $supplier = $this->db->query("
                SELECT approved_by_person_id, id FROM supplier
                WHERE person_id = ? LIMIT 1
            ", [$person_id])->row();

            if ($supplier && $supplier->approved_by_person_id) {
                $this->session->set_userdata([
                    'agrishop_login_uri'         => 'usersupplier',
                    'agrishop_login_landing'     => 'dashboard',
                    'agrishop_login_supplier_id' => $supplier->id,
                ]);
                redirect(base_url('usersupplier/dashboard'));
                return;
            }

        } elseif ($level == 1) {
            redirect(base_url('index'));
            return;
        } elseif ($level == 3) {
            redirect(base_url('useradmin/dashboard'));
            return;
        }

        // Show pending page
        $data  = $this->system();
        $data += ["page_title" => "Pending Approval"];
        $this->load->view('interface/system/Pending', $data);
    }

    // ── Called via AJAX from Pending page every 15s ──────────
    public function checkApprovalStatus()
    {
        $person_id = $this->session->agrishop_person_id;
        $level     = $this->session->agrishop_login_level;

        if (!$person_id) {
            echo json_encode(['approved' => false]);
            return;
        }

        if ($level == 2) {
            $row = $this->db->query("
                SELECT f.approved_by_person_id, f.id AS farmer_id
                FROM farmer f WHERE f.person_id = ? LIMIT 1
            ", [$person_id])->row();

            if ($row && $row->approved_by_person_id) {
                $this->session->set_userdata([
                    'agrishop_login_uri'       => 'userfarmer',
                    'agrishop_login_landing'   => 'dashboard',
                    'agrishop_login_farmer_id' => $row->farmer_id,
                ]);
                echo json_encode(['approved' => true, 'redirect' => base_url('userfarmer/dashboard')]);
            } else {
                echo json_encode(['approved' => false]);
            }

        } elseif ($level == 4) {
            $row = $this->db->query("
                SELECT s.approved_by_person_id, s.id AS supplier_id
                FROM supplier s WHERE s.person_id = ? LIMIT 1
            ", [$person_id])->row();

            if ($row && $row->approved_by_person_id) {
                $this->session->set_userdata([
                    'agrishop_login_uri'         => 'usersupplier',
                    'agrishop_login_landing'     => 'dashboard',
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
}
