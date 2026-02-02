<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mt-2 mb-n1">
            <div class="col-sm-12">
                <h5><i class="nav-icon fas fa-shopping-basket"></i> Client Orders</h5>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 col-12">
                <div class="card">
                    <div class="card-header bg-info rounded-0">
                        <h1 class="card-title"><i class="fa fa-list"></i> Incoming Orders (Reserved)</h1>
                    </div>
                    <div class="card-body p-2" style="overflow: auto;">
                        <table id="tblCartListing" class="table table-sm table-hover table-striped table-bordered mb-0" width="100%">
                            <thead class="small">
                                <tr>
                                    <!-- <th width="1">Image</th> -->
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- dynamic rows -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 col-12">
                <div class="card">
                    <div class="card-header bg-success rounded-0">
                        <h1 class="card-title"><i class="fa fa-list"></i> Completed Orders</h1>
                    </div>
                    <div class="card-body p-2" style="overflow: auto;">
                        <table id="tblCartListingCompleted" class="table table-sm table-striped table-bordered mb-0" width="100%">
                            <thead class="small">
                                <tr>
                                    <!-- <th width="1">Image</th> -->
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- dynamic rows -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 col-12">
                <div class="card collapsed-card cancelledOrders">
                    <div class="card-header bg-danger rounded-0">
                        <h1 class="card-title"><i class="fa fa-list"></i> Cancelled Orders</h1>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" onclick="$('.cancelledOrders').toggleClass('collapsed-card');">
                                <i class="fas fa-plus text-white"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-2" style="overflow: auto;">
                        <table id="tblCartListingCancelled" class="table table-sm table-hover table-striped table-bordered mb-0" width="100%">
                            <thead class="small">
                                <tr>
                                    <!-- <th width="1">Image</th> -->
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- dynamic rows -->
                            </tbody>
                        </table>
                    </div>
                    

                </div>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>