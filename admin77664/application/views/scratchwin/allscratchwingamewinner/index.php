<div class="pcoded-main-container">
    <div class="pcoded-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title"></div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Scratch Win</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentControllerPath('index'); ?>">Winners</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Scratch Win Winners</h5>
                        <a href="javascript:void(0)" class="btn btn-sm btn-primary pull-right" data-toggle="modal" data-target="#exportModal">Export excel</a>
                    </div>
                    <div class="card-body">
                        <form id="Data_Form" name="Data_Form" method="get" action="<?php echo $forAction; ?>">
                            <div class="dt-responsive table-responsive">
                                <div id="simpletable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                                    <div class="row align-items-center mb-2">
                                        <div class="col-sm-12 col-md-12">
                                            <div class="dataTables_length" id="simpletable_length">
                                                <label>Show
                                                    <select name="showLength" id="showLength" class="custom-select custom-select-sm form-control form-control-sm">
                                                        <option value="2"   <?php if ($perpage == '2') echo 'selected="selected"'; ?>>2</option>
                                                        <option value="10"  <?php if ($perpage == '10') echo 'selected="selected"'; ?>>10</option>
                                                        <option value="25"  <?php if ($perpage == '25') echo 'selected="selected"'; ?>>25</option>
                                                        <option value="50"  <?php if ($perpage == '50') echo 'selected="selected"'; ?>>50</option>
                                                        <option value="100" <?php if ($perpage == '100') echo 'selected="selected"'; ?>>100</option>
                                                        <option value="All" <?php if ($perpage == 'All') echo 'selected="selected"'; ?>>All</option>
                                                    </select>
                                                    entries
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row align-items-center mb-2">
                                        <div class="col-sm-3 col-md-2">
                                            <select name="searchField" id="searchField" class="custom-select custom-select-sm form-control form-control-sm">
                                                <option value="">Select Field</option>
                                                <option value="order_id" <?php if ($searchField == 'order_id') echo 'selected="selected"'; ?>>Order ID</option>
                                                <option value="txn_id" <?php if ($searchField == 'txn_id') echo 'selected="selected"'; ?>>Txn ID</option>
                                                <option value="products_name" <?php if ($searchField == 'products_name') echo 'selected="selected"'; ?>>Game Name</option>
                                                <option value="users_id" <?php if ($searchField == 'users_id') echo 'selected="selected"'; ?>>User ID</option>
                                                <option value="buyer_mobile" <?php if ($searchField == 'buyer_mobile') echo 'selected="selected"'; ?>>Buyer Mobile</option>
                                                <option value="buyer_email" <?php if ($searchField == 'buyer_email') echo 'selected="selected"'; ?>>Buyer Email</option>
                                                <option value="winning_type" <?php if ($searchField == 'winning_type') echo 'selected="selected"'; ?>>Winning Type</option>
                                                <option value="winning_amount" <?php if ($searchField == 'winning_amount') echo 'selected="selected"'; ?>>Winning Amount</option>
                                                <option value="game_mode" <?php if ($searchField == 'game_mode') echo 'selected="selected"'; ?>>Game Mode</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3 col-md-2">
                                            <input type="text" name="searchValue" id="searchValue" value="<?php echo htmlspecialchars($searchValue ?? ''); ?>" class="form-control form-control-sm" placeholder="Enter Search Text">
                                        </div>
                                        <div class="col-sm-3 col-md-3">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">From</span>
                                                </div>
                                                <input type="datetime-local" name="fromDate" id="fromDate" autocomplete="off" value="<?php echo $fromDate ? date('Y-m-d\TH:i', strtotime($fromDate)) : ''; ?>" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="col-sm-3 col-md-3">
                                            <div class="input-group input-group-sm">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">To</span>
                                                </div>
                                                <input type="datetime-local" name="toDate" id="toDate" autocomplete="off" value="<?php echo $toDate ? date('Y-m-d\TH:i', strtotime($toDate)) : ''; ?>" class="form-control form-control-sm">
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-md-2">
                                            <input type="submit" name="Search" value="Search" class="btn btn-sm btn-primary">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-responsive">
                                        <table id="simpletable" class="table table-striped table-bordered nowrap dataTable" role="grid" aria-describedby="simpletable_info">
                                            <thead style="text-align: center;">
                                                <tr role="row">
                                                    <th width="5%">S.No.</th>
                                                    <th width="15%">Order ID</th>
                                                    <th width="15%">Game</th>
                                                    <th width="12%">Buyer Details</th>
                                                    <th width="10%">Winning Type</th>
                                                    <th width="10%">Winning Amount</th>
                                                    <th width="10%">Order Amount</th>
                                                    <th width="12%">Created Date</th>
                                                    <th width="8%">Status</th>
                                                    <th width="8%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody style="text-align: center;">
                                                <?php
                                                if (!empty($ALLDATA)):
                                                    $i = $first;
                                                    $j = 0;
                                                    foreach ($ALLDATA as $row):
                                                        $rowClass = ($j % 2 == 0) ? 'odd' : 'even';
                                                        $orderOid = !empty($row['_id']->{'$id'}) ? $row['_id']->{'$id'} : (string) $row['_id'];
                                                ?>
                                                <tr role="row" class="<?php echo $rowClass; ?>">
                                                    <td><?php echo $i++; ?></td>
                                                    <td class="text-left">
                                                        <?php echo htmlspecialchars($row['order_id'] ?? 'N/A'); ?>
                                                    </td>
                                                    <td class="text-left">
                                                        <?php echo htmlspecialchars($row['products_name'] ?? 'N/A'); ?>
                                                    </td>
                                                    <td class="text-left">
                                                        <b>User ID</b>: <?php echo htmlspecialchars($row['users_id'] ?? 'N/A'); ?><br/>
                                                        <b>Mobile</b>: <?php echo htmlspecialchars(($row['buyer_country_code'] ?? '') . ' ' . ($row['buyer_mobile'] ?? 'N/A')); ?><br/>
                                                        <b>Email</b>: <?php echo htmlspecialchars($row['buyer_email'] ?? 'N/A'); ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($row['winning_type'] ?? 'N/A'); ?></td>
                                                    <td>AED <?php echo number_format((float) ($row['winning_amount'] ?? 0), 2); ?></td>
                                                    <td>AED <?php echo number_format((float) ($row['total_price'] ?? 0), 2); ?></td>
                                                    <td><?php echo !empty($row['created_at']) ? date('d-m-Y H:i:s', $row['created_at']) : 'N/A'; ?></td>
                                                    <td><?php echo isset($row['status']) ? showStatus($row['status']) : 'N/A'; ?></td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                                            <ul class="dropdown-menu" role="menu">
                                                                <li><a href="<?php echo getCurrentControllerPath('addeditdata/' . $orderOid); ?>"><i class="far fa-eye"></i> View</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                                    $j++;
                                                    endforeach;
                                                else:
                                                ?>
                                                <tr>
                                                    <td colspan="10" style="text-align:center;">No Scratch Win winners found.</td>
                                                </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-5">
                                    <div class="dataTables_info" role="status" aria-live="polite"><?php echo $noOfContent ?? ''; ?></div>
                                </div>
                                <div class="col-sm-12 col-md-7">
                                    <div class="dataTables_paginate paging_simple_numbers">
                                        <?php echo $PAGINATION ?? ''; ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exportModalLabel">Download Winner Reports</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?php echo getCurrentControllerPath('exportexcel'); ?>" method="post" autocomplete="off">
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <select name="searchField" class="custom-select custom-select-sm form-control form-control-sm">
              <option value="">Select Field</option>
              <option value="order_id" <?php if ($searchField == 'order_id') echo 'selected="selected"'; ?>>Order ID</option>
              <option value="txn_id" <?php if ($searchField == 'txn_id') echo 'selected="selected"'; ?>>Txn ID</option>
              <option value="products_name" <?php if ($searchField == 'products_name') echo 'selected="selected"'; ?>>Game Name</option>
              <option value="users_id" <?php if ($searchField == 'users_id') echo 'selected="selected"'; ?>>User ID</option>
              <option value="buyer_mobile" <?php if ($searchField == 'buyer_mobile') echo 'selected="selected"'; ?>>Buyer Mobile</option>
              <option value="buyer_email" <?php if ($searchField == 'buyer_email') echo 'selected="selected"'; ?>>Buyer Email</option>
              <option value="winning_type" <?php if ($searchField == 'winning_type') echo 'selected="selected"'; ?>>Winning Type</option>
              <option value="winning_amount" <?php if ($searchField == 'winning_amount') echo 'selected="selected"'; ?>>Winning Amount</option>
              <option value="game_mode" <?php if ($searchField == 'game_mode') echo 'selected="selected"'; ?>>Game Mode</option>
            </select>
          </div>
          <div class="col-sm-12 col-md-6">
            <input type="text" name="searchValue" value="<?php echo htmlspecialchars($searchValue ?? ''); ?>" class="form-control form-control-sm" placeholder="Enter Search Text">
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-sm-12 col-md-6">
            <label class="col-form-label">From Date:</label>
            <input type="datetime-local" name="fromDate" value="<?php echo !empty($fromDate) ? date('Y-m-d\TH:i', strtotime($fromDate)) : ''; ?>" class="form-control form-control-sm">
          </div>
          <div class="col-sm-12 col-md-6">
            <label class="col-form-label">To Date:</label>
            <input type="datetime-local" name="toDate" value="<?php echo !empty($toDate) ? date('Y-m-d\TH:i', strtotime($toDate)) : ''; ?>" class="form-control form-control-sm">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Download Report</button>
      </div>
      </form>
    </div>
  </div>
</div>
