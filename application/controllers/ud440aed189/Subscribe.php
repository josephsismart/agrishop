<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Subscribe extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $uri = $this->session->agrishop_login_uri;
        $data = $this->system();
        $data += [
            "page_title"    => "Subscribe",
            "current_location"  => "subscribe",
        ];
        $this->load->view('interface/' . $uri . '/Subscribe', $data);
    }
}

/* End of file Subscribe.php */
/* Location: ./application/controllers/Subscribe.php */