<?php
$order = isset($order) ? $order : array();
$userData = isset($userData) ? $userData : array();
?>
<div class="pcoded-main-container">
    <div class="pcoded-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentControllerPath('index'); ?>">Scratch Win Winners</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Winner Details</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Winner Details - <?php echo htmlspecialchars($order['order_id'] ?? 'N/A'); ?></h5>
                        <a href="<?php echo correctLink('ALLSCRATCHWINGAMEWINNERDATA', getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-secondary pull-right">Back</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Order Info</h6>
                                <table class="table table-bordered table-sm">
                                    <tr><th width="40%">Order ID</th><td><?php echo htmlspecialchars($order['order_id'] ?? 'N/A'); ?></td></tr>
                                    <tr><th>Txn ID</th><td><?php echo htmlspecialchars($order['txn_id'] ?? 'N/A'); ?></td></tr>
                                    <tr><th>Game</th><td><?php echo htmlspecialchars($order['products_name'] ?? 'N/A'); ?></td></tr>
                                    <tr><th>Game Mode</th><td><?php echo htmlspecialchars($order['game_mode'] ?? 'N/A'); ?></td></tr>
                                    <tr><th>Qty</th><td><?php echo (int) ($order['qty'] ?? 0); ?></td></tr>
                                    <tr><th>Total Price</th><td>AED <?php echo number_format((float) ($order['total_price'] ?? 0), 2); ?></td></tr>
                                    <tr><th>Status</th><td><?php echo isset($order['status']) ? showStatus($order['status']) : 'N/A'; ?></td></tr>
                                    <tr><th>Created At</th><td><?php echo !empty($order['created_at']) ? date('d-m-Y H:i:s', $order['created_at']) : 'N/A'; ?></td></tr>
                                    <tr><th>Used RTP</th><td><?php echo htmlspecialchars($order['used_rtp'] ?? 'N/A'); ?></td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Buyer / Seller Info</h6>
                                <table class="table table-bordered table-sm">
                                    <tr><th width="40%">User ID</th><td><?php echo htmlspecialchars($order['users_id'] ?? 'N/A'); ?></td></tr>
                                    <tr><th>Seller Name</th><td><?php echo htmlspecialchars(trim(($userData['users_first_name'] ?? '') . ' ' . ($userData['users_last_name'] ?? '')) ?: 'N/A'); ?></td></tr>
                                    <tr><th>Seller Mobile</th><td><?php echo htmlspecialchars($userData['users_mobile'] ?? 'N/A'); ?></td></tr>
                                    <tr><th>Seller Email</th><td><?php echo htmlspecialchars($userData['users_email'] ?? 'N/A'); ?></td></tr>
                                    <tr><th>POS Number</th><td><?php echo htmlspecialchars($userData['pos_number'] ?? 'N/A'); ?></td></tr>
                                    <tr><th>Buyer Mobile</th><td><?php echo htmlspecialchars(($order['buyer_country_code'] ?? '') . ' ' . ($order['buyer_mobile'] ?? 'N/A')); ?></td></tr>
                                    <tr><th>Buyer Email</th><td><?php echo htmlspecialchars($order['buyer_email'] ?? 'N/A'); ?></td></tr>
                                    <tr><th>SMS Type</th><td><?php echo htmlspecialchars($order['sms_type'] ?? 'N/A'); ?></td></tr>
                                </table>

                                <h6 class="mt-3">Winning Info</h6>
                                <table class="table table-bordered table-sm">
                                    <tr><th width="40%">Winning Status</th><td><span class="badge badge-success"><?php echo htmlspecialchars($order['winning_status'] ?? 'Y'); ?></span></td></tr>
                                    <tr><th>Winning Type</th><td><?php echo htmlspecialchars($order['winning_type'] ?? 'N/A'); ?></td></tr>
                                    <tr><th>Winning Amount</th><td>AED <?php echo number_format((float) ($order['winning_amount'] ?? 0), 2); ?></td></tr>
                                </table>
                            </div>
                        </div>

                        <?php if (!empty($order['scratch_result'])): ?>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <h6>Scratch Result</h6>
                                <pre class="bg-light p-3" style="max-height:300px;overflow:auto;"><?php echo htmlspecialchars(json_encode($order['scratch_result'], JSON_PRETTY_PRINT)); ?></pre>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
