<?php
// MongoDB/common_model may return stdClass; convert to array for safe [] access
$order = isset($orderData) ? json_decode(json_encode($orderData), true) : array();
$user  = isset($userData) ? json_decode(json_encode($userData), true) : array();
$game  = isset($gameData) ? json_decode(json_encode($gameData), true) : array();

?>
<style>
.tambola-order-detail .card-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
.tambola-order-detail .card-header h5 { margin: 0; }
.tambola-order-detail .detail-section { margin-bottom: 1.5rem; }
.tambola-order-detail .detail-section:last-child { margin-bottom: 0; }
.tambola-order-detail .table th { background: #f8f9fa; width: 38%; }
.tambola-order-detail .winning-table { margin-top: 0.5rem; }
.tambola-order-detail .winning-table th { width: auto; }
@media (max-width: 767px) { .tambola-order-detail .col-md-6 { margin-bottom: 1rem; } }
</style>
<div class="pcoded-main-container tambola-order-detail">
    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Orders</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentControllerPath('index'); ?>">Tambola Orders</a></li>
                            <li class="breadcrumb-item">Order Details</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Tambola Order: <?php echo htmlspecialchars($order['order_id'] ?? 'N/A'); ?></h5>
                        <a href="<?php echo getCurrentControllerPath('index'); ?>" class="btn btn-sm btn-secondary">Back to List</a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 col-md-6">
                                <div class="detail-section">
                                    <h6 class="text-muted mb-3">Order &amp; User</h6>
                                    <table class="table table-bordered table-sm">
                                        <tr>
                                            <th>Order ID</th>
                                            <td><?php echo htmlspecialchars($order['order_id'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>User ID</th>
                                            <td><?php echo (int) ($order['users_id'] ?? 0); ?></td>
                                        </tr>
                                        <?php if (!empty($user)): ?>
                                        <tr>
                                            <th>seller Full Name</th>
                                            <td><?php echo htmlspecialchars(trim(($user['users_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))); ?></td>
                                        </tr>
                                        <tr>
                                            <th>seller Email</th>
                                            <td><?php echo htmlspecialchars($user['users_email'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>seller Mobile</th>
                                            <td><?php echo htmlspecialchars($user['users_mobile'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <th>seller POS Number</th>
                                            <td><?php echo htmlspecialchars($user['pos_number'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>seller POS Device ID</th>
                                            <td><?php echo htmlspecialchars($user['pos_device_id'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>seller Store Name</th>
                                            <td><?php echo htmlspecialchars($user['store_name'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Qty (Tickets)</th>
                                            <td><?php echo (int) ($order['qty'] ?? 0); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Total Price</th>
                                            <td>AED <?php echo number_format((float) ($order['total_price'] ?? 0), 2); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Created Date</th>
                                            <td>
                                                <?php
                                                $ca = $order['created_at'] ?? null;
                                                if ($ca) {
                                                    echo is_numeric($ca) ? date('d M Y h:i A', $ca) : date('d M Y h:i A', strtotime($ca));
                                                } else {
                                                    echo 'N/A';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td><?php echo htmlspecialchars($order['status'] ?? 'N/A'); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <?php if (!empty($game)): ?>
                                <div class="detail-section">
                                    <h6 class="text-muted mb-3">Game</h6>
                                    <table class="table table-bordered table-sm">
                                        <tr>
                                            <th>Game</th>
                                            <td><?php echo htmlspecialchars($game['title'] ?? 'N/A'); ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($order['tickets']) && is_array($order['tickets'])): ?>
                                <div class="detail-section">
                                    <h6 class="text-muted mb-3">Tickets</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered winning-table">
                                            <thead><tr><th>#</th><th>Numbers</th></tr></thead>
                                            <tbody>
                                            <?php foreach ($order['tickets'] as $idx => $ticket): ?>
                                                <tr>
                                                    <td><?php echo $idx + 1; ?></td>
                                                    <td><?php echo is_array($ticket) ? implode(', ', array_map('intval', $ticket)) : htmlspecialchars($ticket); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($order['winning_numbers']) && is_array($order['winning_numbers'])): ?>
                                <div class="detail-section">
                                    <h6 class="text-muted mb-3">Winning Numbers (Rounds)</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered winning-table">
                                            <thead><tr><th>Round</th><th>Numbers</th><th>Prize</th></tr></thead>
                                            <tbody>
                                            <?php foreach ($order['winning_numbers'] as $r => $round): ?>
                                                <tr>
                                                    <td><?php echo $r + 1; ?></td>
                                                    <td><?php ?> <?=$round['numbers']?$round['numbers']:'N/A' ?> </td>
                                                    <td>AED <?php echo number_format((float) ($round['prize'] ?? 0), 2); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
