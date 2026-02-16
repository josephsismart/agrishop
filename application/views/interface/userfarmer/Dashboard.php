<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if (!$this->session->agrishop_login_level) {
    redirect(base_url('login'));
}
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
                    <span class="info-box-icon bg-success">
                        <i class="fa fa-sack-dollar"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Revenue</span>
                        <span class="info-box-number fs-4"><?= $dashboard["revenue"] ?></span>
                        <!-- <small class="text-success">▲ +12%</small> -->
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-info">
                        <i class="fa fa-basket-shopping"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Orders</span>
                        <span class="info-box-number fs-4"><?= $dashboard["total_orders"] ?></span>
                        <!-- <small class="text-info">steady</small> -->
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-warning">
                        <i class="fa fa-seedling"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Products</span>
                        <span class="info-box-number fs-4"><?= $dashboard["products"] ?></span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-6">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-danger">
                        <i class="fa-solid fa-house-chimney-window"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Farms</span>
                        <span class="info-box-number fs-4"><?= $dashboard["farms"] ?></span>
                    </div>
                </div>
            </div>

        </div>

        <?php
        $p_selling = json_decode($dashboard["p_selling"], true) ?? [];

        $default = [
            'img_path' => 'dist/img/media/icons/1x1.png', // put your placeholder
            'name'     => '--',
            'qty'      => '--',
            'price'    => '--',
            'uom'      => '--'
        ];

        // ensure at least 4 items
        for ($i = count($p_selling); $i < 4; $i++) {
            $p_selling[] = $default;
        }
        ?>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title"><i class="fa fa-arrow-up"></i> Top Selling Produce</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- PRODUCE CARD -->
                            <div class="col-6 mb-3">
                                <div class="card shadow-sm">
                                    <img src="<?= base_url($p_selling[0]['img_path']) ?>" class="card-img-top" style="height:120px;object-fit:cover;">
                                    <div class="card-body p-2">
                                        <h6 class="mb-1"><?= $p_selling[0]['name'] ?></h6>
                                        <span class="badge bg-success"> <?= $p_selling[0]['qty'] ?> sold</span>
                                        <p class="mb-0 text-muted"> <?= $p_selling[0]['price'] ?> / <?= $p_selling[0]['uom'] ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 mb-3">
                                <div class="card shadow-sm">
                                    <img src="<?= base_url($p_selling[1]['img_path']) ?>" class="card-img-top" style="height:120px;object-fit:cover;">
                                    <div class="card-body p-2">
                                        <h6 class="mb-1"><?= $p_selling[1]['name'] ?></h6>
                                        <span class="badge bg-success"> <?= $p_selling[1]['qty'] ?> sold</span>
                                        <p class="mb-0 text-muted"> <?= $p_selling[1]['price'] ?> / <?= $p_selling[1]['uom'] ?></p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h5 class="card-title"><i class="fa fa-arrow-down"></i> Slow Moving Produce</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">

                            <div class="col-6 mb-3">
                                <div class="card">
                                    <img src="<?= base_url($p_selling[count($p_selling) - 1]['img_path']) ?>" class="card-img-top" style="height:120px;object-fit:cover;">
                                    <div class="card-body p-2">
                                        <h6 class="mb-1"><?= $p_selling[count($p_selling) - 1]['name'] ?></h6>
                                        <span class="badge bg-warning"><?= $p_selling[count($p_selling) - 1]['qty'] ?> sold</span>
                                        <p class="mb-0 text-muted"> <?= $p_selling[count($p_selling) - 1]['price'] ?> / <?= $p_selling[count($p_selling) - 1]['uom'] ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 mb-3">
                                <div class="card">
                                    <img src="<?= base_url($p_selling[count($p_selling) - 2]['img_path']) ?>" class="card-img-top" style="height:120px;object-fit:cover;">
                                    <div class="card-body p-2">
                                        <h6 class="mb-1"><?= $p_selling[count($p_selling) - 2]['name'] ?></h6>
                                        <span class="badge bg-warning"><?= $p_selling[count($p_selling) - 2]['qty'] ?> sold</span>
                                        <p class="mb-0 text-muted"> <?= $p_selling[count($p_selling) - 2]['price'] ?> / <?= $p_selling[count($p_selling) - 2]['uom'] ?></p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->


    <div class="row m-0">

        <div class="col-md-5 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><i class="fa fa-chart-line"></i> Monthly Orders</h5>

                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <div class="btn-group show">

                        </div>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- <p class="text-center">
                                <strong>Sales: 1 Jan, 2014 - 30 Jul, 2014</strong>
                            </p> -->
                            <canvas id="ordersChart" style="height:520px;"></canvas>

                        </div>
                    </div>
                </div>
                <!-- <div class="overlay dark container1">
                    <i style="font-size:100px;color:#fff;" class="fa fa-circle-notch fa-spin"></i>
                </div> -->
            </div>

        </div>

        <div class="col-md-5 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title"><i class="fa fa-chart-bar"></i> Produce Sales Volume</h5>

                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <div class="btn-group show">

                        </div>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">

                            <canvas id="produceChart" style="height:520px;"></canvas>
                        </div>
                    </div>
                </div>
                <!-- <div class="overlay dark container2">
                    <i style="font-size:100px;color:#fff;" class="fa fa-circle-notch fa-spin"></i>
                </div> -->
            </div>
        </div>


        <div class="col-md-2 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fa fa-pie-chart"></i> Sold Products
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="wholesaleVsRetail"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-8 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        📊 Orders vs Revenue Trend
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueOrdersChart" height="138"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fa fa-pie-chart"></i> Product Classification
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="buyingTrendChart" height="90"></canvas>
                </div>
            </div>
        </div>

    </div>

</section>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script type="text/javascript">
    const ordersCtx = document.getElementById('ordersChart').getContext('2d');
    const ordersData = <?php echo $dashboard['ordersGraph']; ?>;
    new Chart(ordersCtx, {
        type: 'line',
        data: {
            labels: ordersData.map(item => item.mon),
            datasets: [{
                label: 'Orders',
                data: ordersData.map(item => item.qty),
                fill: true,
                borderWidth: 3,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const produceCtx = document.getElementById('produceChart').getContext('2d');
    const p_selling = <?php echo $dashboard["p_selling"]; ?>;

    // get top 5 once (cleaner)
    const topSelling = p_selling.slice(0, 5);

    new Chart(produceCtx, {
        type: 'bar',
        data: {
            // short label for display
            labels: topSelling.map(item => item.name.substring(0, 3)),

            datasets: [{
                label: 'Units Sold',
                data: topSelling.map(item => item.qty),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },

                // ⭐ THIS PART — show full name on hover
                tooltip: {
                    callbacks: {
                        title: function(context) {
                            return topSelling[context[0].dataIndex].name;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });


    new Chart(document.getElementById('revenueOrdersChart'), {
        type: 'line',
        data: {
            labels: ordersData.map(item => item.mon),
            datasets: [{
                    label: 'Orders',
                    data: ordersData.map(item => item.qty),
                    borderWidth: 3,
                    tension: 0.4
                },
                {
                    label: 'Revenue (₱)',
                    data: ordersData.map(item => item.revenue),
                    borderWidth: 3,
                    tension: 0.4
                }
            ]
        },
        options: {
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });


    const trendCtx = document.getElementById('buyingTrendChart').getContext('2d');
    const classificationData = <?php echo $dashboard["classificationGraph"]; ?>;

    // get all counts
    const counts = classificationData.map(item => item.count);

    // ⭐ sum all item.count
    const total = counts.reduce((sum, value) => sum + Number(value), 0);

    new Chart(trendCtx, {
        type: 'doughnut',
        data: {
            labels: classificationData.map(item => item.class_name),
            datasets: [{
                data: counts,
                cutout: '60%'
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'bottom'
                },

                // ⭐ show percentage in tooltip
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let value = context.raw;
                            let percentage = ((value / total) * 100).toFixed(1);

                            return ` ${context.label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });


    const trendCtx2 = document.getElementById('wholesaleVsRetail').getContext('2d');
    const wholesaleRetailData = <?php echo $dashboard["wholesale_retail_graph"]; ?>;

    // get qty
    const counts_wr = wholesaleRetailData.map(item => Number(item.qty));

    // get revenue
    const revenue_wr = wholesaleRetailData.map(item => Number(item.revenue));

    // total qty
    const total_wr = counts_wr.reduce((sum, value) => sum + value, 0);

    // total revenue (optional if needed later)
    const total_revenue_wr = revenue_wr.reduce((sum, value) => sum + value, 0);

    new Chart(trendCtx2, {
        type: 'pie',
        data: {
            labels: wholesaleRetailData.map(item => item.w_r),
            datasets: [{
                data: counts_wr
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'bottom'
                },

                tooltip: {
                    callbacks: {
                        label: function(context) {

                            let index = context.dataIndex;

                            let qty = counts_wr[index];
                            let revenue = revenue_wr[index];

                            let percentage = ((qty / total_wr) * 100).toFixed(1);

                            return [
                                `Qty Sold: ${qty}`,
                                `Revenue: ₱${revenue.toLocaleString()}`,
                                `Share: ${percentage}%`
                            ];
                        }
                    }
                }
            }
        }
    });
</script>

<!-- /.content -->