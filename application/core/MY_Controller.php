<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{

    public $global_requestid = null;
    public $global_requestid_personnel = null;

    // just add these new ones below
    public $session;
    public $db;
    public $load;
    public $benchmark;
    public $hooks;
    public $config;
    public $uri;
    public $router;
    public $output;
    public $security;
    public $input;
    public $lang;
    public $form_validation;
    public $pagination;
    public $upload;
    public $email;
    public $cart;
    public $encrypt;
    public $migration;
    public $cache;
    public $zip;
    public $ftp;
    public $xmlrpc;
    public $unit;
    public $trackback;
    public $typography;
    public $template_parser;
    public $javascript;
    public $calendar;
    public $table;
    public $shopping_cart;
    public $image_lib;
    public $MainModel;
    public $mainModel;

    public function system()
    {
        $data = [
            "system_title"  => "AgriShop",
            // "system_title"  => "Agusan National High School",
            "system_logo"   => base_url("dist/layout_shop/img/media/icons/icon.png"),
            "system_svg"    => base_url("dist/layout_shop/images/logo1.svg"),
            "system_op"    => base_url("dist/img/media/icons/icon_op.png"),
            "system_svg_1x1"    => base_url("dist/img/media/icons/1x1.png"),
            "system_bg_l_front_id"    => base_url("dist/img/media/bg/id/2024-2025/learner/v2/v2_front.png"),
            "system_bg_l_back_id"    => base_url("dist/img/media/bg/id/2024-2025/learner/v2/v2_back.png"),
            "system_bg_nt_front_id"    => base_url("dist/img/media/bg/id/2024-2025/non-teaching/v1/v1_front.png"),
            "system_bg_nt_back_id"    => base_url("dist/img/media/bg/id/2024-2025/non-teaching/v1/v1_back.png"),
            "system_bg_t_front_id"    => base_url("dist/img/media/bg/id/2024-2025/teaching/v1/v1_front.png"),
            "system_bg_t_back_id"    => base_url("dist/img/media/bg/id/2024-2025/teaching/v1/v1_back.png"),
            "system_bg_v_front_id"    => base_url("dist/img/media/bg/id/2024-2025/visitor/v1/v1_front.png"),
            "system_bg_v_back_id"    => base_url("dist/img/media/bg/id/2024-2025/visitor/v1/v1_back.png"),
            "system_esig"    => base_url("dist/img/media/esig/roa.png"),

            "system_deped_1x1"    => base_url("dist/img/media/icons/deped_1x1.png"),
            "system_depeddiv_1x1"    => base_url("dist/img/media/icons/depeddiv_1x1.png"),

        ];
        return $data;
    }

    public function public_create_page($data = [])
    {
        $level = $this->session->agrishop_login_level;
        $defaultPassword = $this->session->agrishop_change_password;
        $uri = $this->session->agrishop_login_uri;
        if ($level != "") {
            if ($defaultPassword == 't') {
                return $this->load->view('interface/userpassword/layout/Page', $data, false);
            } else {
                return $this->load->view('interface/' . $uri . '/layout/Page', $data, false);
            }
        }
    }

    public function getNotificationCount()
    {
        $this->getNotificationCount1();
    }

    public function getUnreadNotifications()
    {
        $this->getUnreadNotifications1();
    }

    public function markNotificationsRead()
    {
        $this->markNotificationsRead1();
    }

    public function user_create_page($data = [])
    {
        return $this->load->view('interface/user/layout/Page', $data, false);
    }

    public function redirect()
    {
        // $this->check_subscription2();
        $login = $this->session->agrishop_login_id;
        $defaultPassword = $this->session->agrishop_change_password;
        $uri = $this->session->agrishop_login_uri;
        $landing = $this->session->agrishop_login_landing;
        if (!$login) {
            redirect(base_url('/'));
        }
        if (isset($login) && $this->uri->segment(1) != $uri) {
            if ($defaultPassword == 1) {
                redirect(base_url('userpassword/changepassword'));
            } else {
                redirect(base_url($uri . '/' . $landing));
            }
        }
    }
    public function redirect2()
    {
        $login = $this->session->agrishop_login_id;
        if (!$login) {
            redirect(base_url('/'));
        }
    }

    public function redirect_home()
    {
        $this->check_subscription2();
        $level = $this->session->agrishop_login_level;
        $defaultPassword = $this->session->agrishop_change_password;
        $uri = $this->session->agrishop_login_uri;
        $landing = $this->session->agrishop_login_landing;
        // if (isset($this->session->agrishop_login_id) && $this->uri->segment(1) == "" || $this->uri->segment(1) == "login" || $this->uri->segment(1) == "map") {
        if (isset($this->session->agrishop_login_id) && $this->uri->segment(1) == "" || $this->uri->segment(1) == "login" || $this->uri->segment(1) == "map") {
            if ($level != "") {
                if ($uri == "userconsumer") {
                    redirect(base_url('index'));
                } else {
                    redirect(base_url($uri . '/' . $landing));
                }
            }
        }
    }

    public function check_qty_left($farm_produce_id, $qty)
    {
        $price_qty_left = $this->price_qty_left();
        $check_qty_left = $this->db->query("SELECT pql.qty_left, name FROM ($price_qty_left) pql
                                            LEFT JOIN produce p on pql.produce_id = p.id
                                            WHERE pql.id = $farm_produce_id LIMIT 1")->row();
        if ($check_qty_left->qty_left < $qty) {
            $false = ["success"   => false, "message" => "Quantity for " . $check_qty_left->name . " left is $check_qty_left->qty_left, not enough!"];
            echo json_encode($false);
            return;
        }
    }

    public function subscription_count()
    {
        $farmer_id  = $this->session->agrishop_login_farmer_id;
        $count_billing = $this->db->query("SELECT count(1) as count FROM invoice_billing
                WHERE farmer_id = ?
                AND (payment_for = 'SERVICE_FEE' OR payment_for = 'SUBSCRIPTION')
                AND is_paid = false
            ", [$farmer_id])->row();
        return [
            "count" => $count_billing->count,
        ];
    }

    public function check_subscription2()
    {
        $farmer_id = $this->session->agrishop_login_farmer_id;

        // check if subscription is active and the next billing date has lapsed
        $subscription_active_lapsed = $this->db->query("SELECT id, billing_due_date FROM subscription_history 
                                                WHERE farmer_id=? 
                                                AND billing_due_date<now() 
                                                ORDER BY id DESC 
                                                LIMIT 1", [$farmer_id])->row();
        if ($subscription_active_lapsed) {
            // check if there is unpaid invoice
            $invoice_billing = $this->db->query("SELECT t1.id FROM invoice_billing t1 
            WHERE t1.farmer_id=? and t1.is_paid is false
            LIMIT 1", [$farmer_id])->row();
            if ($invoice_billing != null) {
            } else {

                // //check the last subscription history to then get the last date to be inserted in subscription history and invoice billing
                // $invoice_billing_last_billing = $this->db->query("SELECT t1.subscription_to, t1.billing_due_date FROM subscription_history t1 
                //                             WHERE t1.farmer_id=? ORDER BY t1.id DESC
                //                             LIMIT 1", [$farmer_id])->row();
                // // data for last subscription history to insert

                // if ($invoice_billing_last_billing) {
                //     $data_invoice_billing = [
                //         "farmer_id" => $farmer_id,
                //         "payment_for" => "SUBSCRIPTION",
                //         "total_payment" => 99,
                //         "billing_due_date" => $subscription_active_lapsed->billing_due_date,
                //     ];
                //     $billing_data = $this->db->insert("invoice_billing", $data_invoice_billing);

                //     if ($billing_data) {
                //         $data_invoice_billing = [
                //             "invoice_billing_id" => $data_invoice_billing['id'],
                //             "status" => "PENDING",
                //         ];
                //         $invoice_billing = $this->db->insert("invoice_billing", $data_invoice_billing);
                //         if ($invoice_billing) {
                //             //insert also status billing
                //             $data_invoice_billing_status = [
                //                 "invoice_billing_id" => $data_invoice_billing['id'],
                //                 "status" => "PENDING",
                //             ];
                //             $this->db->insert("invoice_billing_status", $data_invoice_billing_status);
                //         }
                //     }
                // }
            }
        }

        $over_due_invoice = $this->db->query("SELECT t1.id, t2.status, t1.is_paid, t1.billing_due_date, CASE WHEN t1.billing_due_date>now() THEN 1 else 0 end as over_due
            FROM invoice_billing t1 
            LEFT JOIN (select * from invoice_billing_status WHERE is_latest IS TRUE ORDER BY id desc) t2 ON t1.id =t2.invoice_billing_id
            WHERE t1.farmer_id=? and t1.is_paid is false
            LIMIT 1
        ", [$farmer_id])->row();
        if ($over_due_invoice) {
            if ($over_due_invoice->status == 'PENDING' && $over_due_invoice->over_due == 1) {
                $data_session = [
                    "agrishop_login_uri" => 'ud440aed189',
                    "agrishop_login_landing" => 'subscribe',
                ];

                $this->session->set_userdata($data_session);
                // redirect(base_url('ud440aed189/subscribe'));
            } else if ($over_due_invoice->status == 'TO_BE_VERIFIED' && $over_due_invoice->over_due == 1) {
                $data_session = [
                    "agrishop_login_uri" => "ud440aed188v",
                    "agrishop_login_landing" => "validation"
                ];

                $this->session->set_userdata($data_session);
                // redirect(base_url('ud440aed188v/validation'));
            } else {
                $data_session = [
                    "agrishop_login_uri" => "userfarmer",
                    "agrishop_login_landing" => "dashboard"
                ];

                $this->session->set_userdata($data_session);
                // redirect(base_url('userfarmer/dashboard'));
            }
        }
    }

    public function check_subscription()
    {
        $farmer_id = $this->session->agrishop_login_farmer_id;

        if (!$farmer_id) {
            // echo json_encode(["status" => false]);
            return;
        }

        $current_date = date('Y-m-d');

        /*
        ----------------------------
        1️⃣ GET SUBSCRIPTION
        ----------------------------
        */
        $sub = $this->db->query("
            SELECT *
            FROM subscription_history
            WHERE farmer_id = ?
            ORDER BY subscription_to DESC
            LIMIT 1
        ", [$farmer_id])->row();

        /*
        ----------------------------
        2️⃣ GET UNPAID INVOICE
        ----------------------------
        */
        $invoice = $this->db->query("
            SELECT *
            FROM invoice_billing
            WHERE farmer_id = ?
            AND is_paid = false
            ORDER BY billing_due_date ASC
            LIMIT 1
        ", [$farmer_id])->row();

        $response = [
            "status" => true,
            "subscription" => $sub,
            "invoice" => $invoice,
            "allowed" => false
        ];

        if ($sub) {

            // compute grace end
            $grace_end = date(
                'Y-m-d',
                strtotime($sub->billing_due_date . " +{$sub->grace_period_days} days")
            );

            // auto expire
            if ($current_date > $grace_end && $sub->is_active) {
                $this->db->query("
                UPDATE subscription_history
                SET is_active = false
                WHERE id = ?
            ", [$sub->id]);

                $sub->is_active = false;
            }

            if ($sub->is_active) {
                $response["allowed"] = true;
            }
        }

        return $response;
    }

    public function billing_page()
    {
        $farmer_id = $this->session->agrishop_login_farmer_id;
        $billing = $this->billing();


        // CURRENT SUBSCRIPTION
        $subscription = $this->db->query("
            SELECT *
            FROM subscription_history
            WHERE farmer_id = ? AND is_active = true
            ORDER BY subscription_to DESC
            LIMIT 1
        ", [$farmer_id])->row_array();


        // SUBSCRIPTION BILLING
        $subscription_invoice = $this->db->query("
            SELECT * FROM ($billing) as b
            WHERE b.farmer_id = ?
            AND b.payment_for = 'SUBSCRIPTION'
            AND b.is_paid = false
            ORDER BY b.id DESC
            LIMIT 1
        ", [$farmer_id])->row_array();


        // SERVICE FEE BILLING
        $service_invoice = $this->db->query("
            SELECT * FROM ($billing) as b
            WHERE b.farmer_id = ?
            AND b.payment_for = 'SERVICE_FEE'
            AND b.is_paid = false
            ORDER BY b.id DESC
            LIMIT 1
        ", [$farmer_id])->row_array();

        return [
            "subscription" => $subscription,
            "subscription_invoice" => $subscription_invoice,
            "service_invoice" => $service_invoice,
        ];
    }

    public function insert_production_status($id, $data)
    {
        $this->db->update("farmer_produce_production_status", ["is_latest" => 0], [
            "id" => $id,
        ]);

        $this->db->insert("farmer_produce_production_status", $data);
    }

    public function insert_billing_status($id, $status, $remarks = null, $payment_for = null, $farmer_id = null)
    {
        $person_id = $this->session->agrishop_person_id;

        // Mark previous statuses as not latest
        $this->db->update("invoice_billing_status", ["is_latest" => 0], ["invoice_billing_id" => $id]);

        $this->db->insert("invoice_billing_status", [
            "invoice_billing_id"    => $id,
            "status"                => $status,
            "remarks"               => $remarks,
            "is_latest"             => true,
            "created_at"            => date('Y-m-d H:i:s'),
            "created_by_person_id"  => $person_id,
        ]);

        if ($status === 'PAID') {
            $this->db->update("invoice_billing", [
                "is_paid"                           => true,
                "paid_at"                           => date("Y-m-d H:i:s"),
                "approved_payment_by_person_id"     => $person_id,
            ], ["id" => $id]);

            // ✅ FIX: was inserting into "billing" (wrong table)
            // Now correctly extends subscription_history
            if ($payment_for === 'SUBSCRIPTION' && $farmer_id) {
                // Get last subscription to chain from its end date
                $last = $this->db->query("
                    SELECT subscription_to FROM subscription_history
                    WHERE farmer_id = ? ORDER BY id DESC LIMIT 1
                ", [$farmer_id])->row();

                $from = $last ? $last->subscription_to : date('Y-m-d');
                $to   = date('Y-m-d', strtotime($from . ' +1 month'));

                $this->db->insert("subscription_history", [
                    "farmer_id"             => $farmer_id,
                    "subscription_type"     => 'REGULAR',
                    "is_active"             => true,
                    "subscription_from"     => $from,
                    "subscription_to"       => $to,
                    "billing_due_date"      => $to,
                    "grace_period_days"     => 7,
                    "created_at"            => date("Y-m-d H:i:s"),
                    "created_by_person_id"  => $person_id,
                ]);
            }

            // Notify the farmer/supplier that payment was approved
            $this->_notify_payment_approved($id, $payment_for);
        }

        if ($status === 'REJECTED') {
            // Notify the farmer/supplier that payment was rejected
            $this->_notify_payment_rejected($id, $remarks);
        }
    }


    public function insert_supplier_billing_status($id, $status, $remarks = null, $supplier_id = null)
    {
        $person_id = $this->session->agrishop_person_id;

        $this->db->update("supplier_invoice_billing_status", ["is_latest" => 0], ["invoice_billing_id" => $id]);

        $this->db->insert("supplier_invoice_billing_status", [
            "invoice_billing_id"    => $id,
            "status"                => $status,
            "remarks"               => $remarks,
            "is_latest"             => true,
            "created_at"            => date('Y-m-d H:i:s'),
            "created_by_person_id"  => $person_id,
        ]);

        if ($status === 'PAID') {
            $this->db->update("supplier_invoice_billing", [
                "is_paid"                       => true,
                "paid_at"                       => date("Y-m-d H:i:s"),
                "approved_payment_by_person_id" => $person_id,
            ], ["id" => $id]);

            if ($supplier_id) {
                $last = $this->db->query("
                    SELECT subscription_to FROM supplier_subscription_history
                    WHERE supplier_id = ? ORDER BY id DESC LIMIT 1
                ", [$supplier_id])->row();

                $from = $last ? $last->subscription_to : date('Y-m-d');
                $to   = date('Y-m-d', strtotime($from . ' +1 month'));

                $this->db->insert("supplier_subscription_history", [
                    "supplier_id"           => $supplier_id,
                    "subscription_type"     => 'REGULAR',
                    "is_active"             => true,
                    "subscription_from"     => $from,
                    "subscription_to"       => $to,
                    "billing_due_date"      => $to,
                    "grace_period_days"     => 7,
                    "created_at"            => date("Y-m-d H:i:s"),
                    "created_by_person_id"  => $person_id,
                ]);
            }
        }
    }



    public function notify($person_id, $title, $message, $type = 'INFO', $ref_type = null, $ref_id = null)
    {
        if (!$person_id) return;
        $this->db->insert("notification", [
            "person_id"      => $person_id,
            "title"          => $title,
            "message"        => $message,
            "type"           => $type,
            "is_read"        => 0,
            "reference_type" => $ref_type,
            "reference_id"   => $ref_id,
            "created_at"     => date('Y-m-d H:i:s'),
        ]);
    }

    public function getUnreadNotifications1()
    {
        $person_id = $this->session->agrishop_person_id;
        if (!$person_id) {
            echo json_encode([]);
            return;
        }
        $rows = $this->db->query("
            SELECT id, title, message, type, reference_type, reference_id,
                   DATE_FORMAT(created_at, '%b %d %h:%i%p') AS time_ago
            FROM notification
            WHERE person_id = ? AND is_read = 0
            ORDER BY created_at DESC
            LIMIT 20
        ", [$person_id])->result();
        echo json_encode($rows);
    }

    public function markNotificationsRead1()
    {
        $person_id = $this->session->agrishop_person_id;
        if (!$person_id) return;
        $this->db->update("notification", ["is_read" => 1], ["person_id" => $person_id]);
        echo json_encode(["success" => true]);
    }

    public function getNotificationCount1()
    {
        $person_id = $this->session->agrishop_person_id;
        if (!$person_id) {
            echo json_encode(["count" => 0]);
            return;
        }
        $row = $this->db->query("
            SELECT COUNT(1) AS count FROM notification
            WHERE person_id = ? AND is_read = 0
        ", [$person_id])->row();
        echo json_encode(["count" => (int) $row->count]);
    }

    // Internal: notify farmer/supplier on payment approved
    private function _notify_payment_approved($billing_id, $payment_for)
    {
        // Get farmer person_id from billing
        $b = $this->db->query("
            SELECT ib.farmer_id, f.person_id
            FROM invoice_billing ib
            JOIN farmer f ON ib.farmer_id = f.id
            WHERE ib.id = ? LIMIT 1
        ", [$billing_id])->row();

        if ($b && $b->person_id) {
            $this->notify(
                $b->person_id,
                'Payment Approved ✅',
                'Your ' . ($payment_for ?: 'billing') . ' payment has been verified and approved.',
                'SUCCESS',
                'invoice_billing',
                $billing_id
            );
        }
    }

    // Internal: notify farmer/supplier on payment rejected
    private function _notify_payment_rejected($billing_id, $reason)
    {
        $b = $this->db->query("
            SELECT ib.farmer_id, f.person_id
            FROM invoice_billing ib
            JOIN farmer f ON ib.farmer_id = f.id
            WHERE ib.id = ? LIMIT 1
        ", [$billing_id])->row();

        if ($b && $b->person_id) {
            $this->notify(
                $b->person_id,
                'Payment Rejected ❌',
                'Your payment was rejected. Reason: ' . ($reason ?: 'No reason given') . '. Please submit a new proof of payment.',
                'DANGER',
                'invoice_billing',
                $billing_id
            );
        }
    }

    // ── PATCH 5: Order notification helper ────────────────────
    // Call this from FarmProduce/Orders when a new order arrives (RESERVED status)
    // ADD this method:

    public function notify_new_order($farmer_person_id, $transaction_id)
    {
        $this->notify(
            $farmer_person_id,
            'New Order Received 🛒',
            'You have a new order waiting for your confirmation.',
            'INFO',
            'transaction',
            $transaction_id
        );
    }


    public function price_qty_left()
    {
        return "
            SELECT
                fp2.id,
                fp2.farm_id,
                f2.person_id AS farmer_person_id,
                fp2.produce_id,
                fp2.harvest_schedule,
                pmfp.price,
                pmfp.id AS latest_price_id,
                pmfp.price_wholesale,
                pmfp.wholesale_at_qty,
                fp2.uom,
                tt1.total_qty,
                t2.qty_sold,
                CAST(tt1.total_qty AS DOUBLE) - COALESCE(t2.qty_sold, 0) AS qty_left
            FROM farm_produce fp2
            LEFT JOIN farmer_farm ff2 ON fp2.farm_id = ff2.id
            LEFT JOIN farmer f2 ON ff2.farmer_id = f2.id
            LEFT JOIN (
                SELECT farm_produce_supply.farm_produce_id,
                    SUM(farm_produce_supply.qty) AS total_qty
                FROM farm_produce_supply
                GROUP BY farm_produce_supply.farm_produce_id
            ) tt1 ON fp2.id = tt1.farm_produce_id
            LEFT JOIN (
                SELECT pmfp1.id, pmfp1.farm_produce_id, pmfp1.price,
                    pmfp1.is_latest, pmfp1.created_at, pmfp1.created_by_person_id,
                    pmfp1.price_wholesale, pmfp1.wholesale_at_qty
                FROM price_monitoring_farm_produce pmfp1
                WHERE pmfp1.is_latest = 1
            ) pmfp ON fp2.id = pmfp.farm_produce_id
            LEFT JOIN (
                SELECT mcfp.farm_produce_id, SUM(mcfp.qty) AS qty_sold
                FROM my_cart_farm_produce mcfp
                LEFT JOIN transaction t ON mcfp.transaction_id = t.id
                LEFT JOIN (
                    SELECT ts.id, ts.transaction_id, ts.status,
                        ts.is_latest, ts.created_at AS create_at,
                        ts.created_by_person_id
                    FROM transaction_status ts
                    WHERE ts.is_latest = 1
                ) t1 ON t.id = t1.transaction_id
                WHERE t1.status = 'COMPLETED' OR t1.status = 'RESERVED'
                GROUP BY mcfp.farm_produce_id
            ) t2 ON fp2.id = t2.farm_produce_id
        ";
    }

    public function billing()
    {
        return "
            SELECT 
                t3.img_path AS proof_img_path,
                t3.is_approved AS proof_is_approved,
                t2.status,
                t2.remarks AS status_remarks,
                t1.id,
                t1.farmer_id,
                CONCAT(t5.first_name, ' ', t5.last_name) AS farmer,
                t1.payment_for,
                t1.total_payment,
                t1.billing_period_from,
                t1.billing_period_to,
                t1.billing_due_date,
                t1.is_paid,
                t1.paid_at,
                t1.approved_payment_by_person_id,
                t1.invoice_no,
                t1.created_at
            FROM invoice_billing t1
            LEFT JOIN (
                SELECT 
                    invoice_billing_status.id,
                    invoice_billing_status.invoice_billing_id,
                    invoice_billing_status.status,
                    invoice_billing_status.is_latest,
                    invoice_billing_status.created_at,
                    invoice_billing_status.created_by_person_id,
                    invoice_billing_status.remarks
                FROM invoice_billing_status
                WHERE invoice_billing_status.is_latest = 1
            ) t2 ON t1.id = t2.invoice_billing_id
            LEFT JOIN (
                SELECT 
                    t.id,
                    t.invoice_billing_id,
                    t.img_path,
                    t.is_approved,
                    t.approved_at,
                    t.approved_by_person_id,
                    t.remarks,
                    t.created_at,
                    t.created_by_person_id,
                    t.rn
                FROM (
                    SELECT 
                        invoice_billing_proof_of_payment.id,
                        invoice_billing_proof_of_payment.invoice_billing_id,
                        invoice_billing_proof_of_payment.img_path,
                        invoice_billing_proof_of_payment.is_approved,
                        invoice_billing_proof_of_payment.approved_at,
                        invoice_billing_proof_of_payment.approved_by_person_id,
                        invoice_billing_proof_of_payment.remarks,
                        invoice_billing_proof_of_payment.created_at,
                        invoice_billing_proof_of_payment.created_by_person_id,
                        ROW_NUMBER() OVER (
                            PARTITION BY invoice_billing_proof_of_payment.invoice_billing_id 
                            ORDER BY invoice_billing_proof_of_payment.created_at DESC
                        ) AS rn
                    FROM invoice_billing_proof_of_payment
                ) t
                WHERE t.rn = 1
            ) t3 ON t1.id = t3.invoice_billing_id
            LEFT JOIN farmer t4 ON t1.farmer_id = t4.id
            LEFT JOIN person t5 ON t4.person_id = t5.id";
    }

    public function transaction_status($data)
    {
        $this->db->where('transaction_id', $data["transaction_id"]);
        $this->db->update('transaction_status', ['is_latest' => 0]);
        $this->db->insert("transaction_status", $data);
        return true;
    }

    public function transaction_delivery_status($data)
    {
        $this->db->where('transaction_id', $data["transaction_id"]);
        $this->db->update('transaction_delivery_status', ['is_latest' => 0]);
        $this->db->insert("transaction_delivery_status", $data);
        return true;
    }

    public function transaction_payment_status($data)
    {
        $this->db->where('transaction_id', $data["transaction_id"]);
        $this->db->update('transaction_payment_status', ['is_latest' => 0]);
        $this->db->insert("transaction_payment_status", $data);
        return true;
    }

    public function update_transaction_status($data, $table)
    {
        $this->db->where('transaction_id', $data["transaction_id"]);
        $this->db->update($table, ['is_latest' => 0]);
        $this->db->insert($table, $data);
        return true;
    }

    public function redirect_session()
    {
        $login = $this->session->agrishop_login_id;
        if (!$login) {
            redirect(base_url('/'));
        }
    }

    public function removeCharacter($text)
    {
        return preg_replace("/[^0-9]/", "", $text);
    }

    public function clean($string)
    {
        $string = str_replace(' ', '-', $string);
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    }

    public function cleanQuote($string)
    {
        $string = str_replace('"', '$', $string);  // Replace double quotes with ??
        $string = str_replace("'", '?', $string);   // Replace single quotes with ?
        return $string;
    }

    public function readQuote($string)
    {
        $string = str_replace('$', '"', $string);  // Replace double quotes with ??
        $string = str_replace('?', "'", $string);   // Replace single quotes with ?
        return $string;
    }

    public function returnNull($a)
    {
        $return = !$a ? NULL : $a;
        return $return;
    }

    public function returnEmptyArr($a)
    {
        $return = !$a ? 0 : count($a);
        return $return;
    }

    public function returnZero($a)
    {
        $return = !$a ? 0 : $a;
        return $return;
    }

    public function getAddress($filter)
    {
        $fltr = $filter ? $filter : 0;
        $address = "";
        $query = $this->db->query("SELECT 
                                        t1.id,
                                        UPPER(CONCAT(
                                            t1.description, ' ',
                                            t2.description, ', ',
                                            t3.description, ', ',
                                            t4.region
                                        )) AS address
                                    FROM tbl_barangay t1
                                    LEFT JOIN tbl_citymun t2 ON t1.citymun_id = t2.id
                                    LEFT JOIN tbl_province t3 ON t2.province_id = t3.id
                                    LEFT JOIN tbl_region t4 ON t3.region_id = t4.id
                                    WHERE t1.id=$fltr");
        if ($query->num_rows() > 0) {
            $address = $query->row()->address;
        }
        return $address;
    }

    public function getPersonName($filter)
    {
        $fltr = $filter ? $filter : 0;
        $name = "";
        $query = $this->db->query("SELECT * FROM person t1 WHERE t1.id=$fltr");
        if ($query->num_rows() > 0) {
            $mname = $query->row()->middle_name != "" ? " " . substr($query->row()->middle_name, 0, 1) . ". " : " ";
            $name = $query->row()->first_name . $mname . $query->row()->last_name;
        }
        return $name;
    }

    public function getTransactionStatus($id, $status, $type, $status_table = 'transaction_status')
    {
        if ($type === 'farmer') {
            $FILTR = "t4.farmer_id = ?";
        } else {
            $FILTR = "t1.person_id = ?";
        }

        // IMPORTANT: validate table name (never trust dynamic table names)
        $allowed_tables = ['transaction_status', 'transaction_delivery_status']; // add your valid tables here

        if (!in_array($status_table, $allowed_tables)) {
            die('Invalid status table.');
        }

        if ($status_table != 'transaction_status') {

            $sql = "SELECT COUNT(1) AS total
                        FROM transaction t1
                        JOIN (
                            SELECT * FROM {$status_table} WHERE is_latest = 1
                        ) t2 ON t1.id = t2.transaction_id
                        LEFT JOIN (
                            SELECT * FROM transaction_status WHERE is_latest = 1
                        ) t22 ON t1.id = t22.transaction_id
                        LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                        WHERE {$FILTR}
                        AND t2.status = ?
                        AND (t22.status != 'COMPLETED' AND t22.status != 'CANCELLED')
                    ";

            $thisQuery = $this->db->query($sql, [$id, $status]);
        } else {

            $sql = "SELECT COUNT(1) AS total
                    FROM transaction t1
                    JOIN (
                        SELECT * FROM {$status_table} WHERE is_latest = 1
                    ) t2 ON t1.id = t2.transaction_id
                    LEFT JOIN farmer_farm t4 ON t1.farm_id = t4.id
                    WHERE {$FILTR}
                    AND t2.status = ?
                ";

            $thisQuery = $this->db->query($sql, [$id, $status]);
        }

        // Safe result handling
        $c = ($thisQuery && $thisQuery->num_rows() > 0)
            ? (int)$thisQuery->row()->total
            : 0;
        $cc = $c > 0 ? $c : '';
        if ($type == 'farmer') {
            $this->session->agrishop_reserved_trans_count = $cc;
        } else {
            $this->session->agrishop_pending_trans_count = $cc;
        }

        return $cc;
    }

    public function format_price($amount)
    {
        return number_format((float)$amount, 2, '.', ',');
    }

    public function generate_gcash_qr($amount = 0, $gcash_number = '', $merchant_name = '', $reference = '')
    {
        // Set defaults if empty
        if (empty($gcash_number)) {
            $gcash_number = '09171234567'; // Your default GCash number
        }
        if (empty($merchant_name)) {
            $merchant_name = 'My Business';
        }
        if (empty($reference)) {
            $reference = 'INV' . date('YmdHis') . rand(100, 999);
        }

        // Format amount to 2 decimal places
        $formatted_amount = number_format((float)$amount, 2, '.', '');

        /**
         * Option 1: GCash Deep Link (EASIEST - opens GCash directly)
         * Format: gcash://pay?pa=GCASH_NUMBER&pn=MERCHANT&am=AMOUNT
         */
        $qr_data = "gcash://pay?" . http_build_query([
            'pa' => $gcash_number,
            'pn' => $merchant_name,
            'am' => $formatted_amount,
            'tid' => $reference
        ]);

        /**
         * Option 2: Simple amount only (User inputs recipient)
         * $qr_data = "gcash://scan?amount=" . $formatted_amount;
         */

        // Generate QR code URL using api.qrserver.com
        $qr_size = 300; // QR code size in pixels
        $qr_url = "https://api.qrserver.com/v1/create-qr-code/";
        $qr_url .= "?size=" . $qr_size . "x" . $qr_size;
        $qr_url .= "&data=" . urlencode($qr_data);
        $qr_url .= "&format=png";
        $qr_url .= "&margin=10";
        $qr_url .= "&color=000000";
        $qr_url .= "&bgcolor=FFFFFF";
        $qr_url .= "&qzone=1";

        // Return everything you need
        return [
            'qr_url' => $qr_url,          // Direct URL to QR image
            'qr_data' => $qr_data,        // The encoded data
            'amount' => $formatted_amount,
            'merchant_name' => $merchant_name,
            'gcash_number' => $gcash_number,
            'reference' => $reference,
            'instructions' => "1. Open GCash app\n2. Tap 'Scan QR'\n3. Scan this code\n4. Confirm payment"
        ];
    }

    public function getAddress2($filter)
    {
        $fltr = $filter ? $filter : 0;
        $address = "";
        $query = $this->db->query("SELECT 
                                        t1.id,
                                        UPPER(CONCAT(
                                            t1.description, ' ',
                                            t2.description, ', ',
                                            t3.description, ', ',
                                            t4.region
                                        )) AS address
                                    FROM tbl_barangay_2 t1
                                    JOIN tbl_citymun t2 ON t1.adm3_psgc = t2.ref_id
                                    JOIN tbl_province t3 ON t2.province_id = t3.id
                                    JOIN tbl_region t4 ON t3.region_id = t4.id AND t4.is_active = true
                                    WHERE t1.id=$fltr");
        if ($query->num_rows() > 0) {
            $address = $query->row()->address;
        }
        return $address;
    }

    public function allow_schema()
    {
        $this->db->query("GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA account TO xnyiyspvjvppjz;

                            GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA address TO xnyiyspvjvppjz;
                            
                            GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA building_sectioning TO xnyiyspvjvppjz;
                            
                            GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA global TO xnyiyspvjvppjz;
                            
                            GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA profile TO xnyiyspvjvppjz;
                            
                            GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO xnyiyspvjvppjz;");


        // $this->db->query("GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA account TO xnyiyspvjvppjz;

        // GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA address TO xnyiyspvjvppjz;

        // GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA building_sectioning TO xnyiyspvjvppjz;

        // GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA global TO xnyiyspvjvppjz;

        // GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA profile TO xnyiyspvjvppjz;

        // GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO xnyiyspvjvppjz;");
    }

    public function PartyList($filter)
    {
        $data = ["data" => []];
        $thisQuery = $this->db->query("SELECT * FROM global.tbl_party t1 
                                        WHERE t1.party_type_id=$filter 
                                        AND t1.is_active=true
                                        ORDER BY t1.order_by");
        foreach ($thisQuery->result() as $key => $value) {
            $data["data"][] = [
                "id" => $value->id,
                "item" => $value->description,
            ];
        }
        return $data;
    }

    public function PartyTypeList($filter)
    {
        $data = ["data" => []];
        $thisQuery = $this->db->query("SELECT * FROM global.tbl_partytype t1 
                                        WHERE t1.group_id=$filter 
                                        AND t1.is_active=true
                                        ORDER BY t1.order_by");
        foreach ($thisQuery->result() as $key => $value) {
            $data["data"][] = [
                "id" => $value->id,
                "item" => $value->description,
            ];
        }
        return $data;
    }

    public function SchoolPersonnelList($filter)
    {
        $w = $filter ? "WHERE t1.employeeTypeId=$filter" : "";
        $data = ["data" => []];
        $thisQuery = $this->db->query("SELECT * FROM profile.view_schoolpersonnel t1 $w ORDER BY t1.first_name");
        foreach ($thisQuery->result() as $key => $value) {
            $data["data"][] = [
                "id" => $value->schoolpersonnel_id,
                "item" => $value->full_name,
            ];
        }
        return $data;
    }

    public function StatusList($filter)
    {
        $data = ["data" => []];
        $thisQuery = $this->db->query("SELECT * FROM global.tbl_status t1 
                                        WHERE t1.status_type_id=$filter 
                                        AND t1.is_active=true
                                        ORDER BY t1.order_by");
        foreach ($thisQuery->result() as $key => $value) {
            $data["data"][] = [
                "id" => $value->id,
                "item" => $value->description,
            ];
        }
        return $data;
    }

    public function GradesSectionList($filter)
    {
        $data = ["data" => []];
        $thisQuery = $this->db->query("SELECT * FROM global.tbl_status t1 
                                        WHERE t1.status_type_id=$filter 
                                        AND t1.is_active=true
                                        ORDER BY t1.order_by");
        foreach ($thisQuery->result() as $key => $value) {
            $data["data"][] = [
                "id" => $value->id,
                "item" => $value->description,
            ];
        }
        return $data;
    }

    public function getOnLoad()
    {
        $query = $this->db->query("SELECT * FROM global.tbl_sy t1 WHERE t1.is_active=true");
        $row = $query->row();
        $sy_id = $row->id;
        $sy = $row->description;
        $qrtr = $row->qrtr;
        $enroll_stat = $row->enrollment_stat;
        $enroll_dl = $row->enrollment_deadline;
        $grade_stat = $row->grading_stat;
        $grade_dl = $row->grading_deadline;
        $edit = $row->edit_student;
        $unenroll = $row->unenroll;
        $v_grades = $row->view_grades;
        $v_grades_date = $row->view_grades_until;
        $input_grades_qrtr = $row->input_grades_qrtr;
        $qrtrR = $qrtr == 1 ? "1st" : ($qrtr == 2 ? "2nd" : ($qrtr == 3 ? "3rd" : ($qrtr == 4 ? "4th" : "--")));
        $edl = "";
        $edl1 = "";
        $gdl = "";
        $gdl1 = "";
        $vgd = "";
        if ($enroll_dl) {
            $edl = $this->dateFormat($enroll_dl);
            $edl1 = "<br/>" . $edl;
        }
        if ($grade_dl) {
            $gdl = $this->dateFormat($grade_dl);
            $gdl1 = "<br/>" . $gdl;
        }
        if ($v_grades == 't') {
            $vgd = $this->dateFormat($v_grades_date);
        }

        $data = [
            "sy_id" => $sy_id,
            "sy" => $sy,
            "qrtr" => $qrtr,
            "qrtrR" => $qrtrR,
            "enroll_stat" => $enroll_stat,
            "enroll_dl" => $enroll_dl,
            "grade_stat" => $grade_stat,
            "grade_dl" => $grade_dl,
            "edl" => $edl1,
            "gdl" => $gdl1,
            "edit" => $edit,
            "unenroll" => $unenroll,
            "v_grades" => $v_grades,
            "vgd" => $vgd,
            "input_grades_qrtr" => $input_grades_qrtr,
            // "sy_qrtr_e_g" => "<b>SY:</b> " . $sy . " | <b>Q:</b> " . $qrtrR,
            "sy_qrtr_e_g" => "<b>SY:</b> " . $sy . " | <b>Q:</b> |" . $qrtrR .
                "<div class='d-none d-sm-block d-lg-block'>" .
                ($enroll_stat == 't' ? " <small class='text-success text-bold' style='white-space: nowrap;'><b>ENR: </b>" . $edl . "</small>" : "") .
                ($grade_stat == 't' ? " | <small class='text-success text-bold' style='white-space: nowrap;'><b>GRD: </b>" . $gdl . "</small>" : "") .
                "</div>",
        ];
        return $data;
    }

    public function getSHdboard()
    {
        $sy = $this->getOnLoad()["sy_id"];
        $query = $this->db->query("SELECT SUM(CASE WHEN t1.sex_bool=TRUE THEN 1 END) AS male,
                                    SUM(CASE WHEN t1.sex_bool=FALSE THEN 1 END) AS female
                                    FROM sy$sy.bs_view_enrollment t1
                                    WHERE t1.status_id=5");

        $query1 = $this->db->query("SELECT SUM(CASE WHEN t1.sex_bool=TRUE THEN 1 END) AS male,
                                    SUM(CASE WHEN t1.sex_bool=FALSE THEN 1 END) AS female
                                    FROM profile.view_schoolpersonnel t1
                                    WHERE t1.person_id != 1197 
                                    AND t1.person_id != 1 
                                    AND t1.person_id != 1431 
                                    AND t1.person_id != 1102 
                                    -- AND t1.is_active=TRUE
                                    AND t1.is_active_schl_personnel>0");

        $query2 = $this->db->query("SELECT t1.role_id,t1.user_description, COUNT(1) AS cc FROM account.view_useraccount t1
                                    GROUP BY t1.user_description,t1.role_id");

        $row = $query->row();
        $row1 = $query1->row();
        $emale =  number_format($row->male);
        $efmale =  number_format($row->female);
        $tenroll =  number_format($row->male + $row->female);

        $tpmale =  number_format($row1->male);
        $tpfemale =  number_format($row1->female);
        $ttpenroll =  number_format($row1->male + $row1->female);

        foreach ($query2->result() as $key => $value) {
            $r = $value->role_id;
            if ($r == 3) {
                $dephead = (int) $value->cc;
            }
            if ($r == 7) {
                $teacher = (int) $value->cc;
            }
            if ($r == 8) {
                $learner = (int) $value->cc;
            }
        }

        $data = [
            "emale" => $emale,
            "efmale" => $efmale,
            "tenroll" => $tenroll,

            "tpmale" => $tpmale,
            "tpfemale" => $tpfemale,
            "ttpenroll" => $ttpenroll,

            "dephead" => $dephead,
            "teacher" => $teacher,
            "learner" => $learner,
        ];
        return $data;
    }

    public function filterAndFormatDate($date)
    {
        $dateFormats = [
            'm-d-Y',
            'm/d/Y',
            'm-d-y',
            'm/d/y',
            'Y-m-d',
            'Ymd', // For dates like '20030112'
            'Y/m/d',
            'Y.m.d',
        ];

        if (is_numeric($date) && $date >= 1 && $date <= 99999) {
            // Convert $date to date format (assuming it's an Excel date serial)
            $excelDateOrigin = 25569; // Adjust for Excel's date origin (January 1, 1970, in Unix timestamp)
            $unixTimestamp = ($date - $excelDateOrigin) * 86400; // 86400 seconds in a day
            return $birthdate = date('Y-m-d', $unixTimestamp);
        } else {
            foreach ($dateFormats as $format) {
                $z = trim($date);
                $dateTime = DateTime::createFromFormat($format, $z);
                if ($dateTime !== false) {
                    return $birthdate = $dateTime->format('Y-m-d');
                    break; // Exit the loop once a valid format is found
                }
            }
        }

        if ($date === null) {
            // Handle the case where the date format doesn't match either expected format
            // You can add your error handling logic here
            return null;
        }

        // Return null if none of the formats matched
    }

    public function get_ip()
    {
        $ip = "";
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (!empty($_SERVER["HTTP_X_FORWARDED"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED"];
        } elseif (!empty($_SERVER["REMOTE_ADDR"])) {
            $ip = $_SERVER["REMOTE_ADDR"];
        }
        if ($ip == "::1") {
            $ip = "127.0.0.1";
        }
        return $ip;
    }

    public function confirmPassword($a)
    {
        $pwd = md5($a);
        $login_id = $this->session->agrishop_login_id;
        $query = $this->db->query("SELECT 1 AS pwd FROM tbl_user WHERE id=$login_id AND password='$pwd' LIMIT 1");
        return $query->row("pwd");
    }

    public function now()
    {
        date_default_timezone_set("Asia/Manila");
        $now = date("Y-m-d H:i:s");
        return $now;
    }

    public function do_upload($input_name, $upload_path, $file_name)
    {
        $path = "";
        // $num = mt_rand(1, 1000000);

        $config['upload_path']      = $upload_path;
        $config['allowed_types']    = 'pdf|docx|xls|ppt|jpg|png|jpeg|txt';
        $config['max_size']         = '100000';
        $config['overwrite']        = true;
        $config['file_name']        = $file_name;
        // $config['max_width']         = '5000';
        // $config['max_height']        = '5000';

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        $upload = $this->upload->do_upload($input_name);
        if ($upload) {
            $path = $file_name;
        }
        return $path;
    }

    public function userlog($action)
    {
        $login_id = $this->session->agrishop_login_id;
        $login_alias = $this->session->agrishop_login_uname;
        $now = $this->now();
        $action = addslashes($action);
        $ip = $this->get_ip();
        $data = [
            "date" => $now,
            "action" => $action,
            "user_id" => $login_id,
            "user_name" => $login_alias,
            "ip" => $ip,
        ];
        if ($login_id) {
            $this->db->insert("global.tbl_userlogs", $data);
        }
    }

    public function calculatePagination($requestData)
    {
        $limit = isset($requestData['length']) ? intval($requestData['length']) : 10;
        $offset = isset($requestData['start']) ? intval($requestData['start']) : 0;
        return array($limit, $offset);
    }

    public function checkTransactionStatus($transaction_id)
    {
        $query = $this->db->query("SELECT t2.status FROM transaction t1
                                    JOIN (SELECT * FROM transaction_status WHERE transaction_id=$transaction_id AND is_latest=TRUE) t2 ON t1.id=t2.transaction_id
                                    WHERE t1.id=$transaction_id LIMIT 1");
        return $query->row("status");
    }

    public function checkTransactionDeliveryStatus($transaction_id)
    {
        $query = $this->db->query("SELECT t2.status FROM transaction t1
                                    JOIN (SELECT * FROM transaction_delivery_status WHERE transaction_id=$transaction_id AND is_latest=TRUE) t2 ON t1.id=t2.transaction_id
                                    WHERE t1.id=$transaction_id LIMIT 1");
        return $query->row("status");
    }

    public function checkTransactionPaymentStatus($transaction_id)
    {
        $query = $this->db->query("SELECT t2.status FROM transaction t1
                                    JOIN (SELECT * FROM transaction_payment_status WHERE transaction_id=$transaction_id AND is_latest=TRUE) t2 ON t1.id=t2.transaction_id
                                    WHERE t1.id=$transaction_id LIMIT 1");
        return $query->row("status");
    }

    public function statusBadge($status)
    {
        $map = [
            // 🧾 TRANSACTION
            'PENDING'    => 'bg-warning text-dark',
            'RESERVED'   => 'bg-info text-dark',
            'PREPARING'  => 'bg-orange',
            'ORDER_IS_READY'  => 'bg-info',
            'COMPLETED'  => 'bg-success',
            'CANCELLED'  => 'bg-danger',

            // 💳 PAYMENT
            'UNPAID'     => 'bg-secondary',
            'VERIFYING'  => 'bg-info text-dark',
            'PAID'       => 'bg-success',
            'FAILED'     => 'bg-danger',

            // 🚚 DELIVERY
            'TO_PICKUP'  => 'bg-warning text-dark',
            'TO_DELIVER' => 'bg-info',
            'ON_THE_WAY' => 'bg-info text-dark',
            'DELIVERED'  => 'bg-success',
        ];

        $color = $map[$status] ?? 'bg-dark';

        return '<span style="font-size:12px; color:#fff !important;" class="badge ' . $color . '">' . $status . '</span>';
    }

    public function defaultImage($img_path_, $produce_id)
    {
        $query = $this->db->query("SELECT pc.img_path as default_img FROM produce p
                                        JOIN produce_classification pc ON p.produce_classification_id = pc.id
                                        WHERE p.id = $produce_id LIMIT 1");
        $default_img_path = $query->row("default_img");

        return (!empty($img_path_) && file_exists(FCPATH . $img_path_))
            ? base_url($img_path_)
            : base_url($default_img_path);
    }

    public function scanlog($x, $type, $scanned_id, $io, $g_name, $g_id)
    {
        $exist = false;
        $query = $this->db->query("SELECT EXISTS (
                                    SELECT 1
                                    FROM pg_tables
                                    WHERE schemaname = 'logs' AND tablename = 'tbl_scan_logs$x'
                                ) AS t;");
        if ($query->num_rows() > 0) {
            $exist =  $query->row()->t;
        } else {
            $q = "";
            $qq = "";
            $q  = $this->db->query("CREATE SEQUENCE logs.tbl_scan_logs_seq$x
                                    INCREMENT BY 1
                                    MINVALUE 1
                                    MAXVALUE 9223372036854775807
                                    START 1
                                    CACHE 1
                                    NO CYCLE;");
            if ($q) {
                $qq = $this->db->query("CREATE TABLE logs.tbl_scan_logs$x (
                                            id int8 NOT NULL DEFAULT nextval('logs.tbl_scan_logs_seq$x'::regclass),
                                            date timestamp NOT NULL DEFAULT now(),
                                            action text NOT NULL,
                                            scan_data text NULL,
                                            scanned_by text NOT NULL,
                                            gate_scanned text NOT NULL,
                                            ip text NULL,
                                            CONSTRAINT tbl_userlogs_pkey PRIMARY KEY (id)
                                        );");
            }
            if ($qq) {
                $exist = true;
            }
        }
        if ($exist == true) {
            $login_id = $this->session->agrishop_login_id;
            $login_alias = $this->session->agrishop_login_uname;
            $now = $this->now();
            // $action = $action; //addslashes($action);
            $ip = $this->get_ip();
            $data = [
                "date" => $now,
                "action" => '{"IO":"' . $io . '"}',
                "scan_data" => '{"tpye":"' . $type . '","id":"' . $scanned_id . '"}',
                "scanned_by" => '{"user":"' . $login_alias . '","id":"' . $login_id . '"}',
                "gate_scanned" => '{"name":"' . $g_name . '","id":"' . $g_id . '"}',
                "ip" => $ip,
            ];
            if ($login_id) {
                $this->db->insert("logs.tbl_scan_logs$x", $data);
            }
        }
    }

    public function learnerlog($action)
    {
        $sy = $this->getOnLoad()["sy_id"];
        $login_id = $this->session->agrishop_login_id;
        $login_alias = $this->session->agrishop_login_uname;
        $now = $this->now();
        $action = addslashes($action);
        $ip = $this->get_ip();
        $data = [
            "date_time" => $now,
            "action" => $action,
            "user_id" => $login_id,
            "user_name" => $login_alias,
            "ip" => $ip,
        ];
        if ($login_id) {
            $this->db->insert("sy$sy.g_tbl_userlogs_learner", $data);
        }
    }

    public function uploadImg($pic, $picname, $path_, $dupload)
    {
        $newImageName = null;
        $isUploaded = false;

        if (isset($pic) && !$isUploaded) {
            $config['upload_path'] = "dist/img/media/$path_/";

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload($dupload)) {
                $myPic = null;
            } else {
                $isUploaded = true;
                $myPic = $this->upload->data();

                // Determine the file extension
                $extension = pathinfo($myPic['file_name'], PATHINFO_EXTENSION);

                // Final new image name
                $cleanName = preg_replace('/[^a-z0-9_-]/', '', strtolower($picname));
                $newImageName = $cleanName . "_" . time() . "." . $extension;
                $newImagePath = $config['upload_path'] . $newImageName;

                // Config for image_lib (to resize/copy)
                $config['image_library'] = 'gd2';
                $config['source_image'] = $myPic['full_path'];   // original uploaded file
                $config['new_image'] = $newImagePath;

                $this->load->library('image_lib', $config);
                $this->image_lib->resize();

                // 🔑 Remove the original uploaded file (with random/original name)
                if (file_exists($myPic['full_path']) && $myPic['full_path'] !== $newImagePath) {
                    unlink($myPic['full_path']);
                }

                return $newImagePath;
            }
        }
    }

    public function getImg($a)
    {
        // Check if the provided path is a URL
        if (filter_var($a, FILTER_VALIDATE_URL)) {
            $pathExists = get_headers($a);
            if ($pathExists && strpos($pathExists[0], '200')) {
                return $a;
            }
        } else {
            // Check if the provided path is a local file
            $localPath = realpath($a);
            if ($localPath && is_file($localPath)) {
                // Check if the file is an image
                $imageInfo = getimagesize($localPath);
                if ($imageInfo !== false) {
                    return base_url() . $a;
                }
            }
        }

        // Return the default image URL
        return base_url('dist/img/media/icons/1x1.png');
    }

    public function dateFormat($a)
    {
        $b = "-";
        if ($a != null) {
            $c = date_create($a);
            $b = date_format($c, "M d, Y");
        }
        return strtoUpper($b);
    }
}

/* End of file MY_Controller.php */
/* Location: ./application/core/MY_Controller.php */