<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Report extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->redirect();
        $this->load->model('mainModel');
        $this->load->helper('date');
        date_default_timezone_set("Asia/Manila");
    }

    public function index()
    {
        $page_data = $this->system();
        $uri = $this->session->schoolmis_login_uri;
        $page_data += [
            "page_title"        => "Report",
            "current_location"  => "report",
            "content"           =>  [$this->load->view('interface/' . $uri . '/Report', [
                "getOnLoad" => $this->getOnLoad(),
            ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }

    
}
