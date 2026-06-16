<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" />
<div class="pcoded-main-container">
    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title"></div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">International Recharge</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentControllerPath('index'); ?>">Ding</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        <style>
            .ding-badge-success { display:inline-block; padding:.35em .65em; font-size:.8125rem; font-weight:600; border-radius:4px; background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
            .ding-badge-cancelled { display:inline-block; padding:.35em .65em; font-size:.8125rem; font-weight:600; border-radius:4px; background:#fce8e8; color:#c0392b; border:1px solid #f5c6cb; }
            .ding-action-dd .btn-outline-danger { border-color:#dc3545; color:#dc3545; background:#fff; }
            .ding-action-dd .btn-outline-danger:hover { background:#dc3545; color:#fff; }
            .ding-action-dd .btn-danger:disabled { opacity:1; cursor:not-allowed; }
            .ding-action-dd .dropdown-menu { min-width:10rem; border:1px solid #e0e0e0; box-shadow:0 2px 8px rgba(0,0,0,.08); }
            .ding-datetime-input { font-family: ui-monospace, "Cascadia Mono", Consolas, monospace; font-size: 0.8125rem; }
        </style>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        
                        <a href="javaScript:void(0)" class="btn btn-sm btn-primary pull-right" data-toggle="modal" data-target="#exportModal">Export excel</a>
                        <a href="<?=getCurrentControllerPath('settings'); ?>" class="btn btn-sm btn-primary pull-right mr-2">Settings</a>
                    </div>
                    <div class="card-body">
                        <form id="Data_Form" name="Data_Form" method="get" action="<?php echo $forAction; ?>">
                            <div class="dt-responsive table-responsive">
                                <div id="simpletable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                                    <div class="row">
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
                                        <div class="col-sm-3 col-md-3">
                                            <select name="searchField" id="searchField" class="custom-select custom-select-sm form-control form-control-sm">
                                                <option value="">Select Field</option>
                                                <option value="transaction_id" <?php if (isset($searchField) && $searchField == 'transaction_id') echo 'selected="selected"'; ?>>Transaction ID</option>
                                                <option value="provider_name" <?php if (isset($searchField) && $searchField == 'provider_name') echo 'selected="selected"'; ?>>Provider Name</option>
                                                <option value="account_number" <?php if ($searchField == 'account_number') echo 'selected="selected"'; ?>>Account Number</option>
                                                <option value="recharge_state" <?php if ($searchField == 'recharge_state') echo 'selected="selected"'; ?>>Recharge State</option>
                                                <option value="recharge_state" <?php if ($searchField == 'recharge_state') echo 'selected="selected"'; ?>>POS Number</option>
                                                <option value="seller_users_mobile" <?php if ($searchField == 'seller_users_mobile') echo 'selected="selected"'; ?>>Seller Mobile no</option>
                                                <option value="seller_users_type" <?php if ($searchField == 'seller_users_type') echo 'selected="selected"'; ?>>Seller User Type</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3 col-md-3">
                                            <input type="text" name="searchValue" id="searchValue" value="<?php echo htmlspecialchars($searchValue ?? ''); ?>" class="form-control form-control-sm" placeholder="Enter Search Text">
                                        </div>
                                        <div class="col-sm-6 col-md-6">
                                            <div class="row">
                                                
                                                <div class="col-sm-12 col-md-4">
                                                    <input type="submit" name="Search" value="Search" class="btn btn-sm btn-primary">
                                                </div>
                                            </div>
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
                                                    <th width="15%">Transaction ID</th>
                                                    <th width="15%">Provider </th>
                                                    <th width="15%">Mobile Number</th>
                                                    <th width="15%">Selling Amount</th>
                                                    <th width="12%">Commission</th>
                                                    <th width="15%">Seller Details</th>
                                                    <th width="15%">Created At</th>
                                                    <th width="12%">Status</th>
                                                    <th width="12%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody style="text-align: center;">
                                                <?php
                                                if (!empty($ALLDATA)):
                                                    $i = $first;
                                                    $j = 0;
                                                    foreach ($ALLDATA as $row):
                                                        $row = is_object($row) ? json_decode(json_encode($row), true) : $row;
                                                        if (!is_array($row)) {
                                                            continue;
                                                        }
                                                        $histMongoId = '';
                                                        if (!empty($row['_id'])) {
                                                            $idEnc = is_array($row['_id']) ? $row['_id'] : json_decode(json_encode($row['_id']), true);
                                                            if (is_array($idEnc)) {
                                                                if (isset($idEnc['$id'])) {
                                                                    $histMongoId = (string) $idEnc['$id'];
                                                                } elseif (isset($idEnc['$oid'])) {
                                                                    $histMongoId = (string) $idEnc['$oid'];
                                                                }
                                                            }
                                                        }
                                                        $rechargeState = (string) ($row['recharge_state'] ?? '');
                                                        $isReversed = ($rechargeState === 'Cancelled' || !empty($row['cancelled_at']));
                                                        $canReverse = ($rechargeState === 'Complete' && !$isReversed);
                                                        $rowClass = ($j % 2 == 0) ? 'odd' : 'even'; ?>
                                                        <tr role="row" class="<?php echo $rowClass; ?>">
                                                             
                                                            <td><?php echo $i++; ?></td>
                                                            <td><?php echo htmlspecialchars((string) ($row['transaction_id'] ?? '')); ?></td>
                                                            <td>
                                                                <img src="<?php echo htmlspecialchars((string) ($row['provider_logo'] ?? '')); ?>" alt="<?php echo htmlspecialchars((string) ($row['provider_name'] ?? '')); ?>" width="50" height="50">
                                                                <br><span><?php echo htmlspecialchars((string) ($row['provider_name'] ?? '')); ?></span>
                                                            </td>
                                                            <td><?php echo htmlspecialchars((string) ($row['account_number'] ?? '')); ?></td>
                                                            <td><?php echo htmlspecialchars((string) ($row['amount'] ?? '')); ?> <?php echo htmlspecialchars((string) ($row['send_currency_iso'] ?? '')); ?></td>
                                                            <td><?php echo number_format((float) ($row['commission_amount'] ?? 0), 2, '.', ''); ?></td>
                                                            <td>
                                                                POS Number: <?php echo htmlspecialchars((string) ($row['seller_users_pos_number'] ?? '')); ?><br>
                                                                Type: <?php echo htmlspecialchars((string) ($row['seller_users_type'] ?? '')); ?><br>
                                                                Mobile: <?php echo htmlspecialchars((string) ($row['seller_users_mobile'] ?? '')); ?><br>
                                                            </td>
                                                            <td><?php echo isset($row['created_at']) ? date('d-m-Y H:i:s', (int) $row['created_at']) : '—'; ?></td>
                                                            <td>
                                                                <?php if ($isReversed): ?>
                                                                    <span class="ding-badge-cancelled">Cancelled</span>
                                                                <?php elseif ($rechargeState === 'Complete'): ?>
                                                                    <span class="ding-badge-success">Success</span>
                                                                <?php else: ?>
                                                                    <span class="text-muted"><?php echo htmlspecialchars($rechargeState !== '' ? $rechargeState : '—'); ?></span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="ding-action-dd">
                                                                <?php if ($canReverse && $histMongoId !== ''): ?>
                                                                    <div class="btn-group">
                                                                        <button type="button" class="btn btn-sm btn-outline-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                                                        <div class="dropdown-menu dropdown-menu-right">
                                                                            <a class="dropdown-item" href="<?php echo htmlspecialchars(getCurrentControllerPath('cancel/' . $histMongoId)); ?>" onclick="return confirm('Cancel this Ding recharge? The user recharge balance and commission will be reversed.');">
                                                                                <i class="fas fa-undo-alt"></i> Cancelled
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                <?php elseif ($isReversed): ?>
                                                                    <div class="btn-group">
                                                                        <button type="button" class="btn btn-sm btn-danger dropdown-toggle" disabled title="Already cancelled">Action</button>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <span class="text-muted">—</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php $j++; endforeach; else: ?>
                                                        <tr>
                                                            <td colspan="10" style="text-align:center;">No Ding recharge history found.</td>
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
        <!-- [ Main Content ] end -->
    </div>
</div>


<?php
$exportDefaultFrom = date('Y-m-d 00:00');
$exportDefaultTo = date('Y-m-d 23:59');
$exportFromValue = isset($fromDate) && trim((string) $fromDate) !== '' ? (string) $fromDate : $exportDefaultFrom;
$exportToValue = isset($toDate) && trim((string) $toDate) !== '' ? (string) $toDate : $exportDefaultTo;
?>
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Download Ding Recharge Report</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?=getCurrentControllerPath('exportexcel')?>" method="post" autocomplete="off">
      <div class="modal-body">
      <div class="row mt-2">
          <div class="col-sm-12 col-md-6">
            <select name="searchField" id="export_modal_searchField" class="custom-select custom-select-sm form-control form-control-sm">
                <option value="">Select Field</option>
                <option value="provider_name" <?php if (isset($searchField) && $searchField == 'provider_name') echo 'selected="selected"'; ?>>Provider Name</option>
                <option value="account_number" <?php if (isset($searchField) && $searchField == 'account_number') echo 'selected="selected"'; ?>>Account Number</option>
                <option value="recharge_state" <?php if (isset($searchField) && $searchField == 'recharge_state') echo 'selected="selected"'; ?>>Recharge State</option>
                <option value="seller_users_pos_number" <?php if (isset($searchField) && $searchField == 'seller_users_pos_number') echo 'selected="selected"'; ?>>Seller POS Number</option>
                <option value="seller_users_mobile" <?php if (isset($searchField) && $searchField == 'seller_users_mobile') echo 'selected="selected"'; ?>>Seller Mobile</option>
                <option value="seller_users_type" <?php if (isset($searchField) && $searchField == 'seller_users_type') echo 'selected="selected"'; ?>>Seller User Type</option>
                <option value="transaction_id" <?php if (isset($searchField) && $searchField == 'transaction_id') echo 'selected="selected"'; ?>>Transaction ID</option>
            </select>
          </div>
          <div class="col-sm-12 col-md-6">
            <input type="text" name="searchValue" id="export_modal_searchValue" value="<?php echo isset($searchValue) ? htmlspecialchars((string) $searchValue) : ''; ?>" class="form-control form-control-sm" placeholder="Enter Search Text">
          </div>
        </div>
          <div class="row">
            <div class="col-sm-12 col-md-6">
              <label class="col-form-label">From:</label>
            </div>
            <div class="col-sm-12 col-md-6">
              <label class="col-form-label">To:</label>
            </div>
            <div class="col-sm-12 col-md-6">
              <input type="text" name="fromDate" id="export_fromDate" value="<?php echo htmlspecialchars($exportFromValue); ?>" class="form-control form-control-sm ding-datetime-input" placeholder="YYYY-MM-DD HH:mm">
            </div>
            <div class="col-sm-12 col-md-6">
              <input type="text" name="toDate" id="export_toDate" value="<?php echo htmlspecialchars($exportToValue); ?>" class="form-control form-control-sm ding-datetime-input" placeholder="YYYY-MM-DD HH:mm">
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

<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>
<script>
$(function () {
  var dtOpts = {
    format: 'Y-m-d H:i',
    step: 1,
    yearStart: 1970,
    yearEnd: <?php echo (int) date('Y'); ?>
  };
  $('#fromDate').datetimepicker(dtOpts);
  $('#toDate').datetimepicker(dtOpts);
  $('#export_fromDate').datetimepicker(dtOpts);
  $('#export_toDate').datetimepicker(dtOpts);
});
</script>
