<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
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
            "revenue" => number_format($revenue->revenue, 0),
            "total_orders" => number_format($revenue->total_orders, 0),
            "products" => number_format($revenue->products, 0),
            "farms" => number_format($revenue->farms, 0),
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
        $orders = $this->db->query("SELECT to_char(t.transaction_date,'MON') mon,sum(mcfp.qty) AS qty,sum(td.total_payment - td.to_admin) as revenue FROM (SELECT ff.farmer_id,t.* FROM transaction t
                                                JOIN farmer_farm ff  ON t.farm_id = ff.id
                                                WHERE ff.farmer_id = $farmer_id AND to_char(t.transaction_date,'yyyy')::int=$current_year ) t 
                                                LEFT JOIN transaction_details td ON t.id = td.transaction_id
                                                LEFT JOIN transaction_cancel tc ON t.id= tc.transaction_id
                                                LEFT JOIN my_cart_farm_produce mcfp ON t.id = mcfp.transaction_id
                                                LEFT JOIN farm_produce fp ON mcfp.farm_produce_id = fp.id
                                                LEFT JOIN produce p ON fp.produce_id = p.id
                                                LEFT JOIN price_monitoring_farm_produce pmfp ON mcfp.price_id_during_transact = pmfp.id 
                                                LEFT JOIN produce_classification pc ON p.produce_classification_id = pc.id
                                                WHERE tc.id IS null
                                                GROUP BY to_char(t.transaction_date,'MON') ,to_char(t.transaction_date,'mm')  ORDER BY to_char(t.transaction_date,'mm')")->result();
        $ordersGraph = json_encode($orders);

        $data += [
            "ordersGraph" => $ordersGraph
        ];

        $classification = $this->db->query("SELECT pc.class_name, count(p.id)  FROM farm_produce fp
                                                LEFT JOIN farmer_farm ff ON fp.farm_id = ff.id
                                                LEFT JOIN produce p ON fp.produce_id = p.id
                                                LEFT JOIN produce_classification pc ON p.produce_classification_id = pc.id
                                                WHERE ff.farmer_id =3
                                                GROUP BY pc.class_name")->result();
        $classificationGraph = json_encode($classification);

        $data += [
            "classificationGraph" => $classificationGraph
        ];

        return $data;
    }
}

/* End of file Home.php */
/* Location: ./application/controllers/Home.php */