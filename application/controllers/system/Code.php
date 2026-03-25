<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Code extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SQL_BIG_SELECTS=1');
    }

    public function index()
    {
        $this->redirect_home();
        $data  = $this->system();
        $data += ["page_title" => "Relevant Source Code Snippets", "current_location" => "code"];
        $this->load->view('interface/system/Code', $data);
    }
}

/* End of file Code.php */
/* Location: ./application/controllers/system/Code.php */