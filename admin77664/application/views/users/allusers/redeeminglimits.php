<div class="pcoded-main-container">
    <div class="pcoded-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentControllerPath('index'); ?>">Users</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Redeem Limits</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Customer Redeem Limits</h5>
                        <a href="<?php echo getCurrentControllerPath('index'); ?>" class="btn btn-sm btn-primary pull-right">Back to Users</a>
                    </div>
                    <div class="card-body">
                        <!-- <p class="text-muted mb-3">Set a fixed prize redeem limit per customer. If not configured, the system default of <strong><?php echo (int) ($defaultRedeemLimit ?? 499); ?> AED</strong> is used.</p> -->
                        <form id="Data_Form" name="Data_Form" method="get" action="<?php echo $forAction; ?>">
                            <div class="row mb-3">
                                <div class="col-sm-12 col-md-2">
                                    <select name="showLength" id="showLength" class="custom-select custom-select-sm form-control form-control-sm" onchange="this.form.submit();">
                                        <option value="10" <?php if ($perpage == '10') echo 'selected="selected"'; ?>>10</option>
                                        <option value="25" <?php if ($perpage == '25') echo 'selected="selected"'; ?>>25</option>
                                        <option value="50" <?php if ($perpage == '50') echo 'selected="selected"'; ?>>50</option>
                                        <option value="100" <?php if ($perpage == '100') echo 'selected="selected"'; ?>>100</option>
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md-3">
                                    <select name="searchField" id="searchField" class="custom-select custom-select-sm form-control form-control-sm">
                                        <option value="">Select Field</option>
                                        <option value="users_id" <?php if ($searchField == 'users_id') echo 'selected="selected"'; ?>>User ID</option>
                                        <option value="users_name" <?php if ($searchField == 'users_name') echo 'selected="selected"'; ?>>Name</option>
                                        <option value="users_mobile" <?php if ($searchField == 'users_mobile') echo 'selected="selected"'; ?>>Mobile</option>
                                        <option value="pos_number" <?php if ($searchField == 'pos_number') echo 'selected="selected"'; ?>>POS Number</option>
                                        <option value="users_type" <?php if ($searchField == 'users_type') echo 'selected="selected"'; ?>>User Type</option>
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md-3">
                                    <input type="text" name="searchValue" id="searchValue" value="<?php echo htmlspecialchars($searchValue ?? ''); ?>" class="form-control form-control-sm" placeholder="Enter Search Text">
                                </div>
                                <div class="col-sm-12 col-md-2">
                                    <input type="submit" name="Search" value="Search" class="btn btn-sm btn-primary">
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered nowrap dataTable">
                                    <thead style="text-align:center;">
                                        <tr>
                                            <th>S.No.</th>
                                            <th>User ID</th>
                                            <th>Name</th>
                                            <th>Mobile</th>
                                            <th>Type</th>
                                            <th>POS No.</th>
                                            <th>Redeem Limit (AED)</th>
                                            <th>Limit Type</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody style="text-align:center;">
                                        <?php
                                        if (!empty($ALLDATA)):
                                            $i = isset($first) ? (int) $first : 1;
                                            foreach ($ALLDATA as $row):
                                                $row = is_object($row) ? (array) $row : $row;
                                                $hasCustom = isset($row['redeeming_amount_limit']) && $row['redeeming_amount_limit'] !== '' && $row['redeeming_amount_limit'] !== null;
                                                $displayLimit = $hasCustom ? (float) $row['redeeming_amount_limit'] : (float) ($defaultRedeemLimit ?? 499);
                                                $limitMode = !empty($row['redeem_limit_mode']) ? strtolower((string) $row['redeem_limit_mode']) : 'fixed';
                                                if ($limitMode !== 'global') {
                                                    $limitMode = 'fixed';
                                                }
                                                ?>
                                                <tr>
                                                    <td><?php echo $i++; ?></td>
                                                    <td><?php echo htmlspecialchars((string) ($row['users_id'] ?? '')); ?></td>
                                                    <td><?php echo htmlspecialchars(stripslashes((string) ($row['users_name'] ?? ''))); ?></td>
                                                    <td><?php echo htmlspecialchars((string) ($row['users_mobile'] ?? '')); ?></td>
                                                    <td><?php echo htmlspecialchars((string) ($row['users_type'] ?? '')); ?></td>
                                                    <td><?php echo htmlspecialchars((string) ($row['pos_number'] ?? '')); ?></td>
                                                    <td>
                                                        <?php echo number_format($displayLimit, 2, '.', ''); ?>
                                                        <?php if (!$hasCustom): ?><br><small class="text-muted">Default</small><?php endif; ?>
                                                    </td>
                                                    <td><?php echo $limitMode === 'global' ? 'Global' : 'Fixed'; ?></td>
                                                    <td>
                                                        <a href="<?php echo getCurrentControllerPath('redeeminglimit/' . (int) ($row['users_id'] ?? 0)); ?>" class="btn btn-sm btn-primary">Configure</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; else: ?>
                                            <tr><td colspan="9">No customers found.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row mt-3">
                                <div class="col-sm-12 col-md-5"><div class="dataTables_info"><?php echo $noOfContent ?? ''; ?></div></div>
                                <div class="col-sm-12 col-md-7"><div class="dataTables_paginate paging_simple_numbers"><?php echo $PAGINATION ?? ''; ?></div></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
