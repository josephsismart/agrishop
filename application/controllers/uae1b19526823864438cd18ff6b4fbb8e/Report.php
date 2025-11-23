<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends MY_Controller {

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
            "content"           =>  [$this->load->view('interface/'.$uri.'/Report', [
                                        // "getBarangay" => $this->getBarangayAssigned(),
                                        // "getStatus" => $this->getStatus(),
                                        //"useraccount"      => $this->get_useraccount(),
                                        //"pending"      => $this->get_pending(),
                                        //"documents"      => $this->get_documents(),
                                    ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }

    function reportMemberUser(){
        $data = ["data" => []];
        $dataTemp = [];
        $user_id = $this->session->schoolmis_login_id;
        parse_str($this->input->post("a"),$filter);
        $id = $filter['filterMemberUser'];
        $where = $id==0?"":"WHERE partyId=$id";
        $thisQuery=$this->db->query("SELECT * FROM view_person $where ORDER BY date_added DESC");
        $cc=1;
        foreach ($thisQuery->result() as $key => $value) {
            $dateAdded = date_create($value->date_added);
            $data["data"][] = [
                $cc++,
                date_format($dateAdded,"M d, Y"),
                $value->fullName,
                $value->sex==1?"MALE":"FEMALE",
                $value->address,
                $value->description,
            ];
        }
        echo json_encode($data);
    }

    function reportInvPO(){
        $data = ["data" => []];
        $dataTemp = [];
        $totalCount = 0;
        $totalWeight = 0;
        $totalSacks = 0;
        $totalDeduction = 0;
        $totalKilo = 0;
        $price = 0;
        $user_id = $this->session->schoolmis_login_id;
        parse_str($this->input->post("a"),$filter);
        $from = $filter['filterInvPOfromDate'];
        $to = $filter['filterInvPOtoDate'];
        $thisQuery=$this->db->query("SELECT * FROM view_inventory 
                                    WHERE dateTrans BETWEEN '$from' AND '$to'
                                    ORDER BY date_added DESC");
        $cc=1;
        foreach ($thisQuery->result() as $key => $value) {
            $dateTrans = $this->dateFormat($value->dateTrans);
            $purchaseDate = $this->dateFormat($value->purchaseDate);

            $totalCount = $totalCount + $value->totalCount??0;
            $totalWeight = $totalWeight + $value->totalWeight??0;
            $totalSacks = $totalSacks + $value->totalSacks??0;
            $totalDeduction = $totalDeduction + $value->totalDeduction??0;
            $totalKilo = $totalKilo + $value->totalKilo??0;
            $price = $price + $value->price??0;

            $data["data"][] = [
                $cc++,
                $value->inventoryCode??"-",
                $dateTrans,
                $value->fullName??"-",
                $value->variantName??"-",
                "<p class='mr-1 mb-0' align='right'>".number_format($value->totalCount)??"-"."</p>",
                "<p class='mr-1 mb-0' align='right'>".number_format($value->totalWeight)??"-"."</p>",
                "<p class='mr-1 mb-0' align='right'>".number_format($value->totalSacks)??"-"."</p>",
                
                $value->purchaseOrderCode??"-",
                $purchaseDate,
                "<p class='mr-1 mb-0' align='right'>".number_format($value->totalDeduction)??"-"."</p>",
                "<p class='mr-1 mb-0' align='right'>".number_format($value->totalKilo)??"-"."</p>",
                "<p class='mr-1 mb-0' align='right'>".number_format($value->price)??"-"."</p>",
            ];
        }
        if($totalCount!=0){
            $data["data"][] = [
                "",
                "<b>Total</b>",
                "",
                "",
                "",
                "<b><p class='mr-1 mb-0' align='right'>".number_format($totalCount)??"-"."</p></b>",
                "<b><p class='mr-1 mb-0' align='right'>".number_format($totalWeight)??"-"."</p></b>",
                "<b><p class='mr-1 mb-0' align='right'>".number_format($totalSacks)??"-"."</p></b>",
                
                "",
                "",
                "<b><p class='mr-1 mb-0' align='right'>".number_format($totalDeduction)??"-"."</p></b>",
                "<b><p class='mr-1 mb-0' align='right'>".number_format($totalKilo)??"-"."</p></b>",
                "<b><p class='mr-1 mb-0' align='right'>".number_format($price)??"-"."</p></b>",
            ];
        }
        echo json_encode($data);
    }

    function reportStckPR(){
        $data = ["data" => []];
        $dataTemp = [];
        $stockSacks = 0;
        $stockKilos = 0;
        $pRequestNumKilos = 0;
        $pRequestPrice = 0;
        $user_id = $this->session->schoolmis_login_id;
        parse_str($this->input->post("a"),$filter);
        $from = $filter['filterStckPRfromDate'];
        $to = $filter['filterStckPRtoDate'];
        $thisQuery=$this->db->query("SELECT * FROM view_stock 
                                    WHERE date_stock BETWEEN '$from' AND '$to'
                                    ORDER BY date_stock DESC");
        $cc=1;
        foreach ($thisQuery->result() as $key => $value) {
            $date_stock = $this->dateFormat($value->date_stock);
            $datePRequest = $this->dateFormat($value->datePRequest);
            $stockSacks = $stockSacks + $value->stockSacks??0;
            $stockKilos = $stockKilos + $value->stockKilos??0;
            $pRequestNumKilos = $pRequestNumKilos + $value->pRequestNumKilos??0;
            $pRequestPrice = $pRequestPrice + $value->pRequestPrice??0;

            $data["data"][] = [
                $cc++,
                $value->purchaseOrderCode??"-",
                $value->stockCode??"-",
                $date_stock,
                "<p class='mr-1 mb-0' align='right'>".number_format($value->stockSacks)??"-"."</p>",
                "<p class='mr-1 mb-0' align='right'>".number_format($value->stockKilos)??"-"."</p>",

                $value->purchaseRequestCode??"-",
                $datePRequest,
                $value->soldToPersonFullName??"-",
                $value->category??"-",
                $value->subclass??"-",
                "<p class='mr-1 mb-0' align='right'>".number_format($value->pRequestNumKilos)??"-"."</p>",
                "<p class='mr-1 mb-0' align='right'>".number_format($value->pRequestPrice)??"-"."</p>",
            ];
        }
        if($stockSacks!=0){
            $data["data"][] = [
                "",
                "<b>Total</b>",
                "",
                "",
                "<b><p class='mr-1 mb-0' align='right'>".number_format($stockSacks)??"-"."</p></b>",
                "<b><p class='mr-1 mb-0' align='right'>".number_format($stockKilos)??"-"."</p></b>",

                "",
                "",
                "",
                "",
                "",
                "<b><p class='mr-1 mb-0' align='right'>".number_format($pRequestNumKilos)??"-"."</p></b>",
                "<b><p class='mr-1 mb-0' align='right'>".number_format($pRequestPrice)??"-"."</p></b>",
            ];
        }
        echo json_encode($data);
    }

}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */