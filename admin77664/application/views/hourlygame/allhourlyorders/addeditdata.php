 
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
                        <h5>Lotto Order: <?php echo htmlspecialchars($order['order_id'] ?? 'N/A'); ?></h5>
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
                                        <?php if (!empty($order)): ?>
                                        <tr>
                                            <th>seller Full Name</th>
                                            <td><?php echo htmlspecialchars(trim(($order['seller_full_name'] ?? '') )); ?></td>
                                        </tr>
                                        <tr>
                                            <th>seller Email</th>
                                            <td><?php echo htmlspecialchars($order['seller_users_email'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>seller Mobile</th>
                                            <td><?php echo htmlspecialchars($order['seller_users_mobile'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <?php endif; ?>
                                        <tr>
                                            <th>seller POS Number</th>
                                            <td><?php echo htmlspecialchars($order['seller_users_pos_number'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>seller POS Device ID</th>
                                            <td><?php echo htmlspecialchars($order['seller_pos_device_id'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <th>seller Store Name</th>
                                            <td><?php echo htmlspecialchars($order['seller_store_name'] ?? 'N/A'); ?></td>
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
                                            <th>Coupons</th>
                                            <td colspan="2">
                                               <ul>
                                                <?php foreach($order['tickets'] as $items): ?>
                                                    <li><?php echo "Ticket: ".$items->ticket ?? 'N/A'; ?></li>
                                                    <li><?php echo "Points: ".$items->points .' AED'?? 'N/A'; ?></li>
                                                    <li><?php echo "Type: ".$items->type ?? 'N/A'; ?></li>
                                                    <hr>
                                                <?php endforeach; ?>
                                               </ul>
                                            </td>
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
                                            <th>Start Date</th>
                                            <td>
                                                <?php
                                                $ca = $order['start_date'] ?? null;
                                                if ($ca) {
                                                    echo is_numeric($ca) ? date('d M Y h:i A', $ca) : date('d M Y h:i A', strtotime($ca));
                                                } else {
                                                    echo 'N/A';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Draw Time</th>
                                            <td>
                                                <?php
                                                $ca = $order['draw_time'] ?? null;
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
