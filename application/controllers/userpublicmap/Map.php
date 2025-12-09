<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Map extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        //$this->redirect();

        $this->load->model('mainModel');
        $this->load->helper('date');
        date_default_timezone_set("Asia/Manila");
    }

    function searchProduce()
    {
        $data =  [];
        $value = $this->input->post("value");
        foreach ($this->db->query("SELECT  fp.farm_id, ff.img_path, ff.farm_name, p.img_path as farmer_img_path,CONCAT(p.first_name,' ',p.last_name) as farmer_name
                                        ,ff.lat,ff.lon,
                                        json_agg(json_build_object('id',fp.produce_id, 'img_path',fp.produce_img_path , 'name',fp.produce_name,'harvest_at',fp.harvest_schedule,'price',fp.price,'uom',fp.uom,'qty_left',fp.qty_left)) AS produce 
                                    FROM (SELECT t11.*,t22.img_path  AS produce_img_path,t22.name AS produce_name FROM price_qty_left t11
                                        JOIN produce t22 ON t11.produce_id = t22.id 
                                        WHERE t11.qty_left>0 AND t22.name ILIKE '%$value%' LIMIT 3) fp
                                    LEFT JOIN farmer_farm ff ON fp.farm_id = ff.id
                                    LEFT JOIN farmer f ON ff.farmer_id = f.id
                                    LEFT JOIN person p ON f.person_id = p.id
                                    GROUP BY fp.farm_id, ff.img_path,p.img_path, CONCAT(p.first_name,' ',p.last_name), ff.farm_name,ff.lat,ff.lon")->result() as $key => $value) {
            $farm_image = $value->img_path ? base_url($value->img_path) : base_url('dist/img/media/icons/1x1.png');
            $farmer_image = $value->farmer_img_path ? base_url($value->farmer_img_path) : base_url('dist/img/media/icons/1x1.png');
            $farm_image_path = "<img src='$farm_image' width='50' height='50' class='rounded pr-2' data-toggle='tooltip' data-placement='top' title=''>";
            $farmer_image_path = "<img src='$farmer_image' width='50' height='50' class='rounded pr-2' data-toggle='tooltip' data-placement='top' title=''>";
            $data[] = [
                "id"    => $value->farm_id,
                "farm_img_path"    => $farm_image_path,
                "farm_name"    => $value->farm_name,
                "farmer_img_path" => $farmer_image_path,
                "farmer_name" => $value->farmer_name,
                "lat"  => $value->lat,
                "lon"  => $value->lon,
                "produce"    => $value->produce,
            ];
        }
        echo json_encode($data);
    }


    function getFarmProduceList()
    {
        $requestData = $_REQUEST;
        $person_id  = $this->session->agrishop_login_id;
        $farm_id = $requestData['search']['farm_id'];
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';

        // Calculate pagination parameters using the separate function
        list($limit, $offset) = $this->calculatePagination($requestData);

        // Query to get total record count
        $thisQuery = $this->db->query("SELECT count(1) AS total FROM public.farm_produce fp
                                    LEFT JOIN public.produce p ON fp.produce_id = p.id
                                    LEFT JOIN public.produce_classification pc ON p.produce_classification_id = pc.id
                                    LEFT JOIN price_qty_left pql ON fp.farm_id = pql.farm_id AND fp.produce_id = pql.produce_id
                                    WHERE fp.farm_id = $farm_id AND CONCAT(p.name,pc.class_name,p.description,(CASE WHEN p.is_seasonal = true THEN 'SEASONAL' ELSE 'NON-SEASONAL' END),(CASE WHEN p.is_active = true THEN 'ACTIVE' ELSE 'INACTIVE' END)) 
                                    ILIKE '%$searchValue%'");

        $totalRecords = $thisQuery->row()->total;

        $query = $this->db->query("SELECT fp.id as fp_id,p.id,p.name as produce,pql.harvest_schedule,pql.uom,pql.price,pql.qty_left ,pc.class_name,p.description,
                                    p.is_seasonal,p.is_active,p.created_at, p.img_path 
                                    FROM public.farm_produce fp
                                    LEFT JOIN public.produce p ON fp.produce_id = p.id
                                    LEFT JOIN public.produce_classification pc ON p.produce_classification_id = pc.id
                                    LEFT JOIN price_qty_left pql ON fp.farm_id = pql.farm_id AND fp.produce_id = pql.produce_id
                                    WHERE fp.farm_id = $farm_id AND CONCAT(p.name,pc.class_name,p.description,(CASE WHEN p.is_seasonal = true THEN 'SEASONAL' ELSE 'NON-SEASONAL' END),(CASE WHEN p.is_active = true THEN 'ACTIVE' ELSE 'INACTIVE' END)) 
                                    ILIKE '%$searchValue%'
                                    ORDER BY p.created_at DESC
                                    LIMIT $limit OFFSET $offset
                                    ");

        $data = array();
        $cc = $offset + 1;
        foreach ($query->result() as $key => $value) {
            $poi = null;
            $is_a_v = $value->is_active;
            $img = $value->img_path ? base_url($value->img_path) : base_url('dist/img/media/icons/1x1.png');
            $is_active = $is_a_v == 't' ? "<span class='badge bg-success'>ACTIVE</span>" : "<span class='badge bg-danger'>INACTIVE</span>";
            $is_seasonal = $value->is_seasonal == 't' ? "<span class='badge bg-blue'>SEASONAL</span>" : "<span class='badge bg-gray'>NON-SEASONAL</span>";
            $image_path = "<img src='$img' width='50' height='50' class='rounded' data-toggle='t0
            .0.ooltip' data-placement='top' title=''>";

            $add_to_cart = "<span class='badge bg-warning text-black' type='button' onclick='add_qty({
                                id: \"$value->fp_id\",
                                img_path: \"$img\",
                                produce: \"$value->produce\",
                                harvest_schedule: \"$value->harvest_schedule\",
                                qty_left: \"$value->qty_left\",
                                price: \"$value->price\",
                                uom: \"$value->uom\",
                                class_name: \"$value->class_name\",
                                is_seasonal: \"$value->is_seasonal\",
                                is_active: \"$value->is_active\"
                            })'><i class='fa fa-cart'></i> Add to cart</span>";
            $data[] = array(
                $add_to_cart,
                '<input type="number" style="width: 100px;" id="qty'.$value->fp_id.'" min="1" max="'.$value->qty_left.'" value="1" class="form-control form-control-sm">',
                $image_path,
                $value->produce,
                $value->harvest_schedule,
                $value->qty_left,
                $value->price.'/'.$value->uom,
                // $value->class_name,
                // $is_seasonal,
                // $is_active,
            );
        } // Prepare the response data in the required format
        $response = array(
            'draw' => intval($requestData['draw']),
            'recordsTotal' => intval($totalRecords),
            'recordsFiltered' => intval($totalRecords), // For simplicity, assuming no filtering is applied
            'data' => $data,
        );
        echo json_encode($response);
    }
}

// farmer_id int4 NOT NULL,
// farm_name varchar(255) NOT NULL,
// total_area_sqm numeric(10, 2) NULL,
// barangay_id int8 NULL,
// geo_polygon text NULL,
// soil_type varchar(100) NULL,
// is_active bool DEFAULT true NOT NULL,
// created_at timestamp(6) DEFAULT now() NULL,
// coordinates text NULL,
// img_path text NULL,
// created_by_person_id int4 DEFAULT 1 NOT NULL,