<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Learner extends MY_Controller
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
        $uri = 'userteacher';
        $page_data += [
            "page_title"        => "Learners",
            "current_location"  => "learner",
            "content"           =>  [$this->load->view('interface/' . $uri . '/Dataentry', [
                "getOnLoad" => $this->getOnLoad(),
            ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }
}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */