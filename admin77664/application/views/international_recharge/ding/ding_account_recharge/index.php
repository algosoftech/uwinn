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
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('DINGACCOUNTRECHARGEDATA',getCurrentControllerPath('index')); ?>">Ding Account Recharge</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Ding Account Recharge List</a></li>
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
                        <a href="javaScript:void(0)" class="btn btn-sm btn-primary pull-right" data-toggle="modal" data-target="#exportModal">Export excel</a>
                        <a href="<?=getCurrentControllerPath('addeditdata'); ?>" class="btn btn-sm btn-primary pull-right mr-2">Add account recharge</a>
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
                                                <option value="users_name" <?php if ($searchField == 'users_name' || $searchField == 'users_first_name') echo 'selected="selected"'; ?>>First Name</option>
                                                <option value="users_mobile" <?php if ($searchField == 'users_mobile') echo 'selected="selected"'; ?>>Mobile Number</option>
                                                <option value="remarks" <?php if ($searchField == 'remarks') echo 'selected="selected"'; ?>>Remarks</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3 col-md-3">
                                            <input type="text" name="searchValue" id="searchValue" value="<?php echo htmlspecialchars($searchValue ?? ''); ?>" class="form-control form-control-sm" placeholder="Enter Search Text">
                                        </div>
                                        <div class="col-sm-6 col-md-6">
                                            <div class="row">
                                                <div class="col-sm-12 col-md-4">
                                                    <input type="text" name="fromDate" id="fromDate" autocomplete="off" value="<?php echo isset($fromDate) ? htmlspecialchars((string) $fromDate) : ''; ?>" class="form-control form-control-sm" style="font-family:ui-monospace,Consolas,monospace;font-size:0.8125rem" placeholder="YYYY-MM-DD HH:mm" title="Default: today 00:00">
                                                </div>
                                                <div class="col-sm-12 col-md-4">
                                                    <input type="text" name="toDate" id="toDate" autocomplete="off" value="<?php echo isset($toDate) ? htmlspecialchars((string) $toDate) : ''; ?>" class="form-control form-control-sm" style="font-family:ui-monospace,Consolas,monospace;font-size:0.8125rem" placeholder="YYYY-MM-DD HH:mm" title="Default: today 23:59">
                                                </div>
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
                                                    <th width="15%">Remarks</th>
                                                    <th width="15%">Amount</th>
                                                    <th width="15%">Available Recharge Amount</th>
                                                    <th width="15%">End Recharge Amount</th>
                                                    <th width="18%">Created Date</th>
                                                    <th width="15%">Status</th>
                                                    <!-- <th width="15%">ACTION</th> -->
                                                </tr>
                                            </thead>
                                            <tbody style="text-align: center;">
                                                <?php
                                                if (!empty($ALLDATA)):
                                                    $i = $first;
                                                    $j = 0;
                                                    foreach ($ALLDATA as $row): $rowClass = ($j % 2 == 0) ? 'odd' : 'even'; ?>
                                                        <tr role="row" class="<?php echo $rowClass; ?>">
                                                            <td><?php echo $i++; ?></td>
                                                            <td><?php echo $row['remarks']; ?></td>
                                                            <td><?php echo $row['upoints']; ?></td>
                                                            <td><?php echo number_format(abs((float) ($row['availablerechargeArabianPoints'] ?? 0)), 2, '.', ''); ?></td>
                                                            <td><?php echo $row['end_balance_recharge']; ?></td>
                                                            <td><?php echo !empty($row['created_at']) ? date('d-M-Y h:i A', strtotime((string) $row['created_at'])) : '-'; ?></td>
                                                            <td><?=showStatus($row['status']) ?></td>
                                                            <!-- <td>
                                                                <div class="btn-group">
                                                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                                                    <ul class="dropdown-menu" role="menu">
                                                                    <?php if($row['status'] == 'A'): ?>
                                                                    <li>
                                                                        <a href="<?php echo getCurrentControllerPath('changestatus/'.$row['_id']->{'$id'}.'/I')?>"> <i class="fas fa-thumbs-down"></i> Inactive</a>
                                                                        </a>
                                                                    </li>
                                                                    <?php elseif($row['status'] == 'I'): ?>
                                                                    <li>
                                                                        <a href="<?php echo getCurrentControllerPath('changestatus/'.$row['_id']->{'$id'}.'/A')?>"> <i class="fas fa-thumbs-up"></i> Active</a>
                                                                    </li>
                                                                    <?php endif; ?>
                                                                    </ul>
                                                                </div>
                                                            </td> -->
                                                            
                                                        </tr>
                                                    <?php $j++; endforeach; else: ?>
                                                        <tr>
                                                            <td colspan="8" style="text-align:center;">No Ding Provider List found.</td>
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


<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Download Ding Account Recharge Report</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?=getCurrentControllerPath('exportexcel')?>" method="post" autocomplete="off">
      <div class="modal-body">
      <div class="row mt-2">
          <div class="col-sm-12 col-md-6">
            <select name="searchField" id="searchField" class="custom-select custom-select-sm form-control form-control-sm">
                <option value="">Select Field</option>
                <option value="users_name" <?php if ($searchField == 'users_name' || $searchField == 'users_first_name') echo 'selected="selected"'; ?>>First Name</option>
                <option value="users_mobile" <?php if ($searchField == 'users_mobile') echo 'selected="selected"'; ?>>Mobile Number</option>
                <option value="remarks" <?php if ($searchField == 'remarks') echo 'selected="selected"'; ?>>Remarks</option>
            </select>
          </div>
          <div class="col-sm-12 col-md-6">
            <input type="text" name="searchValue" id="searchValue" value="<?php echo $searchValue; ?>" class="form-control form-control-sm" placeholder="Enter Search Text">
          </div>
        </div>
          <div class="row">
            <div class="col-sm-12 col-md-6">
              <label for="recipient-name" class="col-form-label">Form:</label>
            </div>
            <div class="col-sm-12 col-md-6">
              <label for="recipient-name" class="col-form-label">To:</label>
            </div>
            <div class="col-sm-12 col-md-6">
              <input type="text" name="fromDate" id="export_fromDate" autocomplete="off" value="<?php echo isset($fromDate) ? htmlspecialchars((string) $fromDate) : ''; ?>" class="form-control form-control-sm" style="font-family:ui-monospace,Consolas,monospace;font-size:0.8125rem" placeholder="YYYY-MM-DD HH:mm">
            </div>
            <div class="col-sm-12 col-md-6">
              <input type="text" name="toDate" id="export_toDate" autocomplete="off" value="<?php echo isset($toDate) ? htmlspecialchars((string) $toDate) : ''; ?>" class="form-control form-control-sm" style="font-family:ui-monospace,Consolas,monospace;font-size:0.8125rem" placeholder="YYYY-MM-DD HH:mm">
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
