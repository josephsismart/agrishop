<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
$uri          = $this->session->agrishop_login_uri;
$pending_f    = $pending_farmers   ?? 0;
$pending_s    = $pending_suppliers ?? 0;
$pending_total = $pending_f + $pending_s;
?>
<script src="<?= base_url() ?>plugins/jquery/jquery.form.min.js"></script>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mt-2 mb-n1">
            <div class="col-sm-12">
                <h5>
                    <i class="nav-icon fas fa-users"></i> User Management
                    <?php if ($pending_total > 0): ?>
                        <span class="badge bg-danger ml-2"><?= $pending_total ?> Pending</span>
                    <?php endif; ?>
                </h5>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-0" id="userTabs" style="border-bottom:2px solid #dee2e6;">
            <li class="nav-item">
                <a class="nav-link active" href="#" id="tab-all" onclick="loadUserTab('all',this);return false;">
                    <i class="fa fa-users mr-1"></i> All
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" id="tab-pending" onclick="loadUserTab('pending',this);return false;">
                    <i class="fa fa-clock mr-1"></i> Pending
                    <?php if ($pending_total > 0): ?>
                        <span class="badge bg-danger"><?= $pending_total ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" id="tab-farmer" onclick="loadUserTab('farmer',this);return false;">
                    <i class="fa fa-tractor mr-1"></i> Farmers
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" id="tab-supplier" onclick="loadUserTab('supplier',this);return false;">
                    <i class="fa fa-store mr-1"></i> Suppliers
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" id="tab-customer" onclick="loadUserTab('customer',this);return false;">
                    <i class="fa fa-user mr-1"></i> Customers
                </a>
            </li>
        </ul>

        <div class="card" style="border-radius:0 0 8px 8px;">
            <div class="card-header bg-navy py-2">
                <h6 class="card-title m-0">
                    <i class="fa fa-list mr-1"></i>
                    <span id="tabLabel">All Users</span>
                </h6>
            </div>
            <div class="card-body p-2">
                <div class="mb-2 text-info" style="font-size:13px;">
                    <i class="fas fa-info-circle mr-1"></i>
                    Click <strong>FOR APPROVAL</strong> badge to review and approve/reject.
                </div>
                <table id="tblUsersInfo" class="table table-sm table-striped table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th width="30">#</th>
                            <th width="55">Photo</th>
                            <th>Name / Role</th>
                            <th>Username</th>
                            <th>Joined / Approval</th>
                            <th>Contact</th>
                            <th>Address</th>
                            <th width="100">Active</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div>
</section>

<!-- Approve/Reject Modal -->
<div class="modal fade" id="modalApprove" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fa fa-id-card mr-2"></i>
                    Review Application — <span id="approveTypeLbl"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <img id="idCardImg" src="" width="280"
                         style="border-radius:8px;border:2px solid #dee2e6;max-width:100%;"
                         onerror="this.src='<?= base_url('dist/img/media/icons/1x1.png') ?>'">
                    <div class="mt-1 text-muted" style="font-size:12px;">Submitted Government ID</div>
                </div>
                <div id="rejectReasonDiv" style="display:none;">
                    <label class="font-weight-bold text-danger">Rejection Reason:</label>
                    <textarea id="rejectReason" class="form-control" rows="3"
                              placeholder="Explain why the application is rejected..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="btnRejectShow"
                        onclick="showRejectReason()">
                    <i class="fa fa-times mr-1"></i> Reject
                </button>
                <button type="button" class="btn btn-success" id="btnApprove"
                        onclick="confirmApprove()">
                    <i class="fa fa-check mr-1"></i> Approve
                </button>
                <button type="button" class="btn btn-danger d-none" id="btnConfirmReject"
                        onclick="confirmReject()">
                    <i class="fa fa-times mr-1"></i> Confirm Rejection
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ── State ─────────────────────────────────────────────────
var userTab      = 'all';
var approveType  = '';
var approveId    = 0;

// ── Reset password ─────────────────────────────────────────────
function resetPassword(user_id) {
    Swal.fire({
        title: 'Reset Password?',
        html: 'This will reset the user\'s password to <strong>agrishop123</strong>.<br>The user will be notified.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e67e22',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fa fa-key mr-1"></i> Yes, Reset It',
        cancelButtonText: 'Cancel'
    }).then(function(result) {
        if (result.isConfirmed) {
            $.post("<?= base_url($uri . '/Users/resetPassword') ?>",
                { user_id: user_id },
                function(res) {
                    var d = JSON.parse(res);
                    if (d.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Password Reset!',
                            html: 'Password has been reset to <strong>agrishop123</strong>',
                            timer: 2500,
                            showConfirmButton: false
                        });
                        getTable('UsersInfo', 0, 10);
                    } else {
                        Swal.fire('Error', d.message, 'error');
                    }
                }
            );
        }
    });
}
var usersTable   = null;

var tabLabels = {
    'all': 'All Users', 'pending': 'Pending Approval',
    'farmer': 'Farmers', 'supplier': 'Suppliers', 'customer': 'Customers'
};

// ── Init on DOM ready ─────────────────────────────────────
$(function() {
    initUsersTable();
});

function loadUserTab(tab, el) {
    userTab = tab;
    $('#userTabs .nav-link').removeClass('active');
    $(el).addClass('active');
    $('#tabLabel').text(tabLabels[tab] || 'Users');

    // Reload table with new tab filter
    if (usersTable) {
        usersTable.ajax.reload();
    }
}

function initUsersTable() {
    if (usersTable) { usersTable.destroy(); }

    usersTable = $("#tblUsersInfo").DataTable({
        order: [[0, "asc"]],
        dom: 'frtip',
        processing: true,
        serverSide: true,
        language: { searchPlaceholder: "Search..." },
        ajax: {
            url: "<?= base_url($uri . '/Users/getUsersInfo') ?>",
            type: "POST",
            data: function(d) {
                // ── KEY FIX: send tab as part of search object ──
                d.search.tab = userTab;
                return d;
            }
        },
        columns: [
            { width: "30px" },
            { width: "55px", orderable: false },
            null,
            { width: "110px" },
            null,
            null,
            null,
            { width: "120px", orderable: false }
        ],
        lengthMenu: [10, 25, 50],
        pageLength: 10,
    });
}

// ── Approval modal ────────────────────────────────────────
function openApproveModal(type, id, imgUrl) {
    approveType = type;
    approveId   = id;
    $('#approveTypeLbl').text(type === 'farmer' ? 'Farmer' : 'Supplier');
    $('#idCardImg').attr('src', imgUrl || '');
    $('#rejectReasonDiv').hide();
    $('#rejectReason').val('');
    $('#btnRejectShow').removeClass('d-none');
    $('#btnApprove').removeClass('d-none');
    $('#btnConfirmReject').addClass('d-none');
    $('#modalApprove').modal('show');
}

function showRejectReason() {
    $('#rejectReasonDiv').slideDown();
    $('#btnRejectShow').addClass('d-none');
    $('#btnApprove').addClass('d-none');
    $('#btnConfirmReject').removeClass('d-none');
}

function confirmApprove() {
    var url  = approveType === 'farmer'
        ? "<?= base_url($uri . '/Users/approveFarmer') ?>"
        : "<?= base_url($uri . '/Users/approveSupplier') ?>";
    var data = approveType === 'farmer'
        ? { farmer_id: approveId }
        : { supplier_id: approveId };

    $.post(url, data, function(res) {
        var d = JSON.parse(res);
        d.success ? successAlert(d.message) : failAlert('Something went wrong!');
        $('#modalApprove').modal('hide');
        if (usersTable) usersTable.ajax.reload();
    });
}

function confirmReject() {
    var reason = $('#rejectReason').val().trim();
    if (!reason) { failAlert('Please provide a rejection reason.'); return; }

    $.post("<?= base_url($uri . '/Users/rejectUser') ?>",
        { type: approveType, id: approveId, reason: reason },
        function(res) {
            var d = JSON.parse(res);
            d.success ? successAlert(d.message) : failAlert('Something went wrong!');
            $('#modalApprove').modal('hide');
            if (usersTable) usersTable.ajax.reload();
        }
    );
}

function toggleUser(userId, isActive) {
    $.post("<?= base_url($uri . '/Users/toggleUser') ?>",
        { user_id: userId, is_active: isActive },
        function(res) {
            var d = JSON.parse(res);
            if (d.success && usersTable) usersTable.ajax.reload();
        }
    );
}
</script>
