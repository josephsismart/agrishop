<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url'); // <-- FIX: load URL helper so base_url() works
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->redirect();
    }

    public function index()
    {
        $page_data = $this->system();
        $uri = $this->session->agrishop_login_uri;
        $page_data += [
            "page_title"        => "Users",
            "current_location"  => "users",
            "content"           =>  [$this->load->view('interface/' . $uri . '/Users', [], TRUE)]
        ];
        $this->public_create_page($page_data);
    }

    public function getUsersInfo()

    {
        $requestData = $_REQUEST;
        $person_id  = $this->session->agrishop_person_id;
        $farmer_id = $this->session->agrishop_login_farmer_id;
        $status_production = isset($requestData['search']['status_production']) ? $requestData['search']['status_production'] : '';
        if ($status_production == '') {
            $status_production = "AND (status!='COMPLETED' AND status!='CANCELLED' AND status!='DAMAGED')";
        } else {
            $status_production = "AND (status='COMPLETED' OR status='CANCELLED' OR status='DAMAGED')";
        }

        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Query to get total record count
        $thisQuery = $this->db->query("SELECT count(*) as total FROM user t1
                                        LEFT JOIN person t2 ON t1.person_id = t2.id
                                        LEFT JOIN farmer t3 ON t2.id = t3.person_id
                                        WHERE CONCAT(t2.first_name, t2.last_name, t2.contact_num, t2.email_address, CASE WHEN t3.id IS NOT NULL AND t3.approved_by_person_id IS NULL THEN 'FOR APPROVAL' ELSE 'APPROVED' END) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'");
        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT t1.*,t2.contact_num
                                        , t2.id as person_id, t2.email_address,t2.barangay_id,t2.img_path customer_img, t3.id as farmer_id
                                        , t3.id_img_path, t3.farmer_selling_type, t3.approved_by_person_id
                                        , t3.approved_at FROM user t1
                                        LEFT JOIN person t2 ON t1.person_id = t2.id
                                        LEFT JOIN farmer t3 ON t2.id = t3.person_id
                                        WHERE CONCAT(t2.first_name, t2.last_name, t2.contact_num, t2.email_address, CASE WHEN t3.id IS NOT NULL AND t3.approved_by_person_id IS NULL THEN 'FOR APPROVAL' ELSE 'APPROVED' END) COLLATE utf8mb4_general_ci LIKE '%$searchValue%'
                                    ORDER BY t1.id DESC
                                    LIMIT $limit OFFSET $offset");

        $data = array();
        $cc = 1;
        foreach ($query->result() as $value) {
            $img_path = $value->customer_img;
            $date_created = !empty($value->created_at)
                ? date('m-d-Y', strtotime($value->created_at))
                : '';
            $is_active = "<small class='badge bg-" . ($value->is_active == 1 ? "success" : "danger") . "'>" . ($value->is_active == 1 ? "ACTIVE" : "INACTIVE") . "</small>";

            // FIX: safely handle null id_img_path before passing to base_url()
            $id_img_url = !empty($value->id_img_path) ? base_url($value->id_img_path) : '';

            $approved_at = $value->farmer_id
                ? ($value->approved_by_person_id
                    ? "<span class='badge bg-info'> <i class='fas fa-check'></i> " .
                    (!empty($value->approved_at) ? date('m-d-Y', strtotime($value->approved_at)) : '') .
                    "</span>"
                    : "<span class='badge bg-warning' onclick='approveFarmer(" . $value->farmer_id . ")' 
                        id='approveFarmerBtn" . $value->farmer_id . "' 
                        data-img='" . $id_img_url . "' 
                        style='cursor: pointer;'> 
                        <i class='fas fa-triangle-exclamation'></i> FOR APPROVAL
                    </span>"): "";

            $seller_type = $value->farmer_selling_type == 1 ? "PRODUCE" : ($value->farmer_selling_type == 2 ? "MACHINERY" : ($value->farmer_selling_type == 3 ? "BOTH" : ""));

            // FIX: safely handle null img_path before passing to base_url()
            $img_ = (!empty($img_path) && file_exists(FCPATH . $img_path))
                ? base_url($img_path)
                : base_url('dist/img/media/icons/1x1.png');

            $img = "<img src='$img_' width='50' height='50' class='rounded' data-toggle='tooltip' data-placement='top' title=''>";
            $data[] = array(
                $cc++,
                $img,
                $this->getPersonName($value->person_id) . "<br/><small style='color: #fff !important;' class='text-muted badge bg-" . ($value->farmer_id ? ($value->farmer_selling_type == 1 ? "success" : ($value->farmer_selling_type == 2 ? "orange" : "warning")) : "primary") . "'>" . ($value->farmer_id ? "Farmer" . " (" . $seller_type . ")" : "Customer") . "</small>",
                $date_created . "<br/>" . $approved_at,
                'EMAIL: ' . $value->email_address . '<br/>' . 'CNTCT: ' . $value->contact_num,
                "<small>" . $this->getAddress($value->barangay_id) . "</small>",
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

    public function approveFarmer()
    {
        $farmer_id = $this->input->post('farmer_id');
        $this->db->where('id', $farmer_id);
        $this->db->update('farmer', array('approved_by_person_id' => $this->session->agrishop_person_id, 'approved_at' => date('Y-m-d H:i:s')));
        echo json_encode(array('success' => true, 'message' => 'Farmer approved successfully'));
    }
}

/* End of file Users.php */
/* Location: ./application/controllers/useradmin/Users.php */