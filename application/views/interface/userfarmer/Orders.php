<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mt-2 mb-n1">
            <div class="col-sm-12">
                <h5><i class="nav-icon fas fa-shopping-basket"></i> Farmer Orders</h5>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0 bg-primary">
                        <h1 class="card-title"><i class="fa fa-list"></i> Incoming Orders</h1>
                    </div>
                    <div class="card-body p-2" style="overflow: auto;">
                        <table id="tblIncomingOrders" class="table table-sm table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="1"></th>
                                    <th>Order Details</th>
                                    <th width="1">Total</th>
                                    <th width="1">Status</th>
                                    <th width="1">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<script type="text/javascript">
    $(function() {
        getIncomingOrders();
    });

    function getIncomingOrders() {
        $('#tblIncomingOrders').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= base_url('userfarmer/getIncomingOrders') ?>",
                "type": "POST",
                "data": function(d) {
                    d.farm_id = $('#farmList').val(); // Assuming you have a farmList dropdown
                }
            },
             "columns": [
                { "data": "order_details" },
                { "data": "payable" },
                { "data": "status" },
                { "data": "action" }
            ],
            "responsive": true,
            "autoWidth": false,
            "destroy": true,
        });
    }

    function acceptOrder(transaction_id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to accept this order!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, accept it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url('userfarmer/accept_order') ?>",
                    type: "POST",
                    data: {
                        trans_id: transaction_id
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Accepted!',
                                'The order has been accepted.',
                                'success'
                            );
                            getIncomingOrders(); // Reload the table
                        } else {
                            Swal.fire(
                                'Failed!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire(
                            'Error!',
                            'An error occurred while accepting the order.',
                            'error'
                        );
                    }
                });
            }
        })
    }
</script>
