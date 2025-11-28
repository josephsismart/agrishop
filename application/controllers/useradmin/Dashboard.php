<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->redirect();
    }

    public function index()
    {       
        $page_data = $this->system();
        $uri = $this->session->schoolmis_login_uri;
        $page_data += [
            "page_title"        => "Dashboard",
            "current_location"  => "dashboard",
            "content"           =>  [$this->load->view('interface/'.$uri.'/Dashboard', [
                                        // "dashboard_data" => $this->get_covid_dashboard(),
                                        // "getYearMonth" => $this->getDateYearMonth(),
                                        //"useraccount"      => $this->get_useraccount(),
                                        //"pending"      => $this->get_pending(),
                                        //"documents"      => $this->get_documents(),
                                    ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }
}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */