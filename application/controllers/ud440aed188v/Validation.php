<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Validation extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->redirect();
        $uri = $this->session->agrishop_login_uri;
        $data = $this->system();
        $data += [
            "page_title"    => "Validation",
            "current_location"  => "validation",
        ];
        $this->load->view('interface/' . $uri . '/Validation', $data);
    }
}

/* End of file Subscribe.php */
/* Location: ./application/controllers/Subscribe.php */