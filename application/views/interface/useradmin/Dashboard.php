<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!$this->session->agrishop_login_level) {
    redirect(base_url('login'));
}
?>
<?php
/* ================= MOCK DATA ================= */

// dashboard mock if empty
$dashboard["farmers"] = $dashboard["farmers"] ?? 324;
$dashboard["users"] = $dashboard["users"] ?? 1520;
$dashboard["subscriptions"] = $dashboard["subscriptions"] ?? 210;
$dashboard["remittance"] = $dashboard["remittance"] ?? "₱125,430.00";

// farmer remittance list
$farmer_remittance = [
    ["name" => "Juan Dela Cruz", "amount" => "₱12,500", "status" => "Paid"],
    ["name" => "Alfredo Esperanza", "amount" => "₱8,200", "status" => "Pending"],
    ["name" => "Marcelo Kalaw", "amount" => "₱6,900", "status" => "Paid"],
    ["name" => "Danielo Plaza", "amount" => "₱4,300", "status" => "Pending"],
];

// top farmers mock
$top_farmers = [
    ["name" => "Juan Dela Cruz", "sales" => "₱52,000"],
    ["name" => "Alfredo Esperanza", "sales" => "₱44,200"],
    ["name" => "Marcelo Kalaw", "sales" => "₱39,800"],
    ["name" => "Danielo Plaza", "sales" => "₱32,500"],
];
?>
<!-- Highcharts -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mt-2">
            <div class="col-sm-6">
                <h1><i class="nav-icon fas fa-edit"></i> Dashboard </h1>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <div class="row mb-3">

            <div class="col-md-3 col-6">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-primary">
                        <i class="fa fa-user"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Users</span>
                        <span class="info-box-number fs-4"><?= $dashboard["user"] ?></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-success">
                        <i class="fa fa-tractor"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Farmers</span>
                        <span class="info-box-number fs-4"><?= $dashboard["farmer"] ?></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-warning">
                        <i class="fa fa-crown"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Subscriptions</span>
                        <span class="info-box-number fs-4"><?= $dashboard["subscriptions"] ?></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-danger">
                        <i class="fa fa-money-bill-wave"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Farmer Remittance</span>
                        <span class="info-box-number fs-4"><?= $dashboard["revenue"] ?></span>
                    </div>
                </div>
            </div>

        </div>

        <div class="row">

            <!-- TOP FARMERS -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title"><i class="fa fa-trophy"></i> Top Farmers</h5>
                    </div>

                    <div class="card-body p-0">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th width="1">#</th>
                                    <th>Farmer</th>
                                    <th>Sales</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $counter = 1;
                                foreach (json_decode($dashboard["top_farmer"]) as $f) : ?>
                                    <tr>
                                        <td><?= $counter++ ?></td>
                                        <td><?= $f->farmer ?></td>
                                        <td class="text-success fw-bold">₱<?= number_format($f->revenue, 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- FARMER REMITTANCE -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h5 class="card-title"><i class="fa fa-wallet"></i> Farmer Remittance</h5>
                    </div>

                    <div class="card-body p-0">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Farmer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (json_decode($dashboard["farmer_remittance"]) as $r) : ?>
                                    <tr>
                                        <td><?= $r->farmer ?></td>
                                        <td>₱<?= number_format($r->amount, 2) ?></td>
                                        <td>
                                            <span class="badge <?= $r->status == "t" ? "bg-success" : "bg-warning" ?>">
                                                <?= $r->status == "t" ? "PAID" : "PENDING" ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <!-- /.container-fluid -->

    <div class="container-fluid">
        <div class="row">

            <!-- REVENUE TREND -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title"><i class="fa fa-wallet"></i> Revenue Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueTrend"></canvas>
                    </div>
                </div>
            </div>

            <!-- ORDERS ANALYTICS -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title"><i class="fa fa-boxes"></i> Orders Analytics</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="ordersAnalytics"></canvas>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <?php 
                $query_produce = "SELECT p.id,  CASE WHEN p.img_path IS NOT NULL THEN p.img_path ELSE pc.img_path END AS img_path , p.\"name\", SUM(mcfp.qty) sum_qty FROM my_cart_farm_produce mcfp 
                                    LEFT JOIN \"transaction\" t ON mcfp.transaction_id =t.id 
                                    LEFT JOIN transaction_cancel tc ON t.id= tc.transaction_id 
                                    LEFT JOIN farm_produce fp ON mcfp.farm_produce_id = fp.id
                                    LEFT JOIN produce p ON fp.produce_id = p.id
                                    LEFT JOIN produce_classification pc ON p.produce_classification_id = pc.id
                                    WHERE tc.id IS NULL
                                    GROUP BY p.id, p.name,pc.img_path ";
                $fast_moving_produce = $this->db->query($query_produce . "ORDER BY SUM(mcfp.qty) DESC LIMIT 10")->result_array();
                $slow_moving_produce = $this->db->query($query_produce . "ORDER BY SUM(mcfp.qty) ASC LIMIT 10")->result_array();
                $counter_fast = 1;
                $counter_slow = 1;

            ?>
            <!-- REVENUE TREND -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title"><i class="fa fa-wallet"></i> Fast moving produce</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Produce</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($fast_moving_produce as $produce): ?>
                                    <tr>
                                        <td><?php echo $counter_fast++; ?></td>
                                        <td><img src="<?php echo base_url() . $produce['img_path']; ?>" alt="<?php echo $produce['name']; ?>" style="width: 50px; height: 50px;"></td>
                                        <td><?php echo $produce['name']; ?></td>
                                        <td><?php echo $produce['sum_qty']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ORDERS ANALYTICS -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h5 class="card-title"><i class="fa fa-boxes"></i> Slow moving produce</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped p-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Produce</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($slow_moving_produce as $produce): ?>
                                    <tr>
                                        <td><?php echo $counter_slow++; ?></td>
                                        <td><img src="<?php echo base_url() . $produce['img_path']; ?>" alt="<?php echo $produce['name']; ?>" style="width: 50px; height: 50px;"></td>
                                        <td><?php echo $produce['name']; ?></td>
                                        <td><?php echo $produce['sum_qty']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>


        <div class="row" hidden>

            <!-- SUBSCRIPTION GROWTH -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h5 class="card-title"><i class="fa fa-crown"></i> Subscription Growth</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="subscriptionChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- FARMER EARNINGS -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title"><i class="fa fa-truck"></i> Farmer Earnings</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="farmerEarnings"></canvas>
                    </div>
                </div>
            </div>

            <!-- ORDER STATUS -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title"><i class="fa fa-chart-pie"></i> Order Status</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>

</section>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    /* ================= MOCK DATA ================= */

    const ordersData = [120, 180, 150, 220, 260, 310];
    const subscriptions = [20, 45, 70, 95, 140, 210];

    const farmerNames = ["Juan", "Alfredo", "Marcelo", "Danielo"];
    const farmerIncome = [52000, 44200, 39800, 32500];

    const revenueData = <?php echo $dashboard['revnue_trendGraph']; ?>;
    const orderAnalyticsGraph = <?php echo $dashboard['orderAnalyticsGraph']; ?>;

    /* ================= REVENUE TREND ================= */
    new Chart(document.getElementById('revenueTrend'), {
        type: 'line',
        data: {
            labels: revenueData.map(item => item.month),
            datasets: [{
                label: 'Revenue',
                data: revenueData.map(item => item.revenue),
                borderWidth: 3,
                fill: true,
                tension: .4
            }]
        }
    });


    /* ================= ORDERS ANALYTICS ================= */
    new Chart(document.getElementById('ordersAnalytics'), {
        type: 'bar',
        data: {
            labels: orderAnalyticsGraph.map(item => item.month),
            datasets: [{
                label: 'Orders',
                data: orderAnalyticsGraph.map(item => item.orders),
                borderWidth: 1
            }]
        }
    });


    /* ================= SUBSCRIPTION GROWTH ================= */
    new Chart(document.getElementById('subscriptionChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'New Subscribers',
                data: subscriptions,
                borderWidth: 3,
                fill: true,
                tension: .4
            }]
        }
    });


    /* ================= FARMER EARNINGS ================= */
    new Chart(document.getElementById('farmerEarnings'), {
        type: 'bar',
        data: {
            labels: farmerNames,
            datasets: [{
                label: 'Earnings',
                data: farmerIncome
            }]
        }
    });


    /* ================= ORDER STATUS DOUGHNUT ================= */
    new Chart(document.getElementById('orderStatusChart'), {
        type: 'doughnut',
        data: {
            labels: ["Pending", "Processing", "Shipped", "Delivered"],
            datasets: [{
                data: [45, 32, 18, 120]
            }]
        }
    });
</script>

<!-- /.content -->