<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->db->query('SET SQL_BIG_SELECTS=1');
        $this->redirect();
    }

    public function index()
    {
        $page_data = $this->system();
        $uri = $this->session->agrishop_login_uri;
        $page_data += [
            "page_title"        => "Dashboard",
            "current_location"  => "dashboard",
            "content"           =>  [$this->load->view('interface/' . $uri . '/Dashboard', [
                "dashboard" => $this->getDashboard(),
                "billing" => $this->billing_page(true),
            ], TRUE)]
        ];
        $this->public_create_page($page_data);
    }


    function getDashboard()
    {
        $farmer_id = $this->session->agrishop_login_farmer_id;
        $revenue = $this->db->query("SELECT count(t.id) AS total_orders, SUM(td.total_payment)-SUM(td.to_admin) AS revenue,
                                        (SELECT COUNT(DISTINCT fp.produce_id) AS products
                                            FROM farm_produce fp
                                            JOIN farmer_farm ff ON fp.farm_id = ff.id
                                            WHERE ff.farmer_id = $farmer_id) as products,
                                        (SELECT count(ff.id) FROM farmer_farm ff
                                            WHERE ff.farmer_id = $farmer_id) as farms
                                        FROM (SELECT ff.farmer_id,t.* FROM transaction t
                                        JOIN farmer_farm ff  ON t.farm_id = ff.id
                                        WHERE ff.farmer_id = $farmer_id) t 
                                        LEFT JOIN transaction_details td ON t.id = td.transaction_id
                                        LEFT JOIN transaction_cancel tc ON t.id= tc.transaction_id
                                        WHERE tc.id IS null")->row();

        $data = [
            "revenue" => $revenue->revenue == null ? 0 : number_format($revenue->revenue, 0),
            "total_orders" => $revenue->total_orders == null ? 0 : number_format($revenue->total_orders, 0),
            "products" => $revenue->products == null ? 0 : number_format($revenue->products, 0),
            "farms" => $revenue->farms == null ? 0 : number_format($revenue->farms, 0),
        ];

        $products_selling = $this->db->query("SELECT p.id,p.name,COALESCE(p.img_path,pc.img_path) AS img_path ,sum(mcfp.qty) AS qty,pmfp.price, fp.uom FROM (SELECT ff.farmer_id,t.* FROM transaction t
                                                JOIN farmer_farm ff  ON t.farm_id = ff.id
                                                WHERE ff.farmer_id = $farmer_id) t 
                                                LEFT JOIN transaction_details td ON t.id = td.transaction_id
                                                LEFT JOIN transaction_cancel tc ON t.id= tc.transaction_id
                                                LEFT JOIN my_cart_farm_produce mcfp ON t.id = mcfp.transaction_id
                                                LEFT JOIN farm_produce fp ON mcfp.farm_produce_id = fp.id
                                                LEFT JOIN produce p ON fp.produce_id = p.id
                                                LEFT JOIN price_monitoring_farm_produce pmfp ON mcfp.price_id_during_transact = pmfp.id 
                                                LEFT JOIN produce_classification pc ON p.produce_classification_id = pc.id
                                                WHERE tc.id IS null
                                                GROUP BY p.id,pmfp.price,fp.uom,pc.img_path ORDER BY sum(mcfp.qty) desc")->result();
        $p_selling = json_encode($products_selling);

        $data += [
            "p_selling" => $p_selling
        ];

        $current_year = date('Y');
        $orders = $this->db->query("SELECT DATE_FORMAT(t.transaction_date,'%b') mon,sum(mcfp.qty) AS qty,sum(td.total_payment - td.to_admin) as revenue FROM (SELECT ff.farmer_id,t.* FROM transaction t
                                                JOIN farmer_farm ff  ON t.farm_id = ff.id
                                                WHERE ff.farmer_id = $farmer_id AND DATE_FORMAT(t.transaction_date,'%Y')=$current_year ) t 
                                                LEFT JOIN transaction_details td ON t.id = td.transaction_id
                                                LEFT JOIN transaction_cancel tc ON t.id= tc.transaction_id
                                                LEFT JOIN my_cart_farm_produce mcfp ON t.id = mcfp.transaction_id
                                                LEFT JOIN farm_produce fp ON mcfp.farm_produce_id = fp.id
                                                LEFT JOIN produce p ON fp.produce_id = p.id
                                                LEFT JOIN price_monitoring_farm_produce pmfp ON mcfp.price_id_during_transact = pmfp.id 
                                                LEFT JOIN produce_classification pc ON p.produce_classification_id = pc.id
                                                WHERE tc.id IS null
                                                GROUP BY DATE_FORMAT(t.transaction_date,'%b') ,DATE_FORMAT(t.transaction_date,'%m')  ORDER BY DATE_FORMAT(t.transaction_date,'%m')")->result();
        $ordersGraph = json_encode($orders);

        $data += [
            "ordersGraph" => $ordersGraph
        ];

        $classification = $this->db->query("SELECT pc.class_name, count(p.id) as count  FROM farm_produce fp
                                                LEFT JOIN farmer_farm ff ON fp.farm_id = ff.id
                                                LEFT JOIN produce p ON fp.produce_id = p.id
                                                LEFT JOIN produce_classification pc ON p.produce_classification_id = pc.id
                                                WHERE ff.farmer_id =$farmer_id
                                                GROUP BY pc.class_name")->result();
        $classificationGraph = json_encode($classification);

        $data += [
            "classificationGraph" => $classificationGraph
        ];

        $wholesale_retail = $this->db->query("SELECT CASE WHEN pmfp.price_wholesale IS NOT NULL THEN 'WHOLESALE' ELSE 'RETAIL' END w_r, sum(mcfp.qty) AS qty,sum(td.total_payment - td.to_admin) as revenue FROM (SELECT ff.farmer_id,t.* FROM transaction t
                                                JOIN farmer_farm ff  ON t.farm_id = ff.id
                                                WHERE ff.farmer_id = $farmer_id AND DATE_FORMAT(t.transaction_date,'%Y')=$current_year ) t 
                                                LEFT JOIN transaction_details td ON t.id = td.transaction_id
                                                LEFT JOIN transaction_cancel tc ON t.id= tc.transaction_id
                                                LEFT JOIN my_cart_farm_produce mcfp ON t.id = mcfp.transaction_id
                                                LEFT JOIN farm_produce fp ON mcfp.farm_produce_id = fp.id
                                                LEFT JOIN produce p ON fp.produce_id = p.id
                                                LEFT JOIN price_monitoring_farm_produce pmfp ON mcfp.price_id_during_transact = pmfp.id 
                                                LEFT JOIN produce_classification pc ON p.produce_classification_id = pc.id
                                                WHERE tc.id IS NULL
                                                GROUP BY CASE WHEN pmfp.price_wholesale IS NOT NULL THEN 'WHOLESALE' ELSE 'RETAIL' END")->result();
        $wholesale_retail_graph = json_encode($wholesale_retail);

        $data += [
            "wholesale_retail_graph" => $wholesale_retail_graph
        ];

        return $data;
    }
}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */