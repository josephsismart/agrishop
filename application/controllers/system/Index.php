<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Index extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->load->model('mainModel');
        $this->load->helper('date');
        date_default_timezone_set("Asia/Manila");
    }

    public function index()
    {
        $this->redirect_home();
        $data = $this->system();
        $data += [
            "page_title"    => "Fresh Organic | Farm-to-Table",
            "current_location"  => "Index",
        ];
        $this->load->view('interface/system/Index', $data);
    }

}

/* End of file Login_admin.php */
/* Location: ./application/controllers/system/Login_admin.php */