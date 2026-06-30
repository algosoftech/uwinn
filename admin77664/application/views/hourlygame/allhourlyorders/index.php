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
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Orders</a></li>
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentControllerPath('index'); ?>">Lotto Orders</a></li>
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
                        <h5>Lotto Orders</h5>
                        <a href="javaScript:void(0)" class="btn btn-sm btn-primary pull-right" data-toggle="modal" data-target="#exportModal">Export excel</a>
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
                                              <option value="order_id" <?php if ($searchField == 'order_id') echo 'selected="selected"'; ?>>Order ID</option>
                                              <option value="products_name" <?php if ($searchField == 'products_name') echo 'selected="selected"'; ?>>Game Name</option>
                                              <option value="users_mobile" <?php if ($searchField == 'users_mobile') echo 'selected="selected"'; ?>>Seller Mobile</option>
                                              <option value="users_email" <?php if ($searchField == 'users_email') echo 'selected="selected"'; ?>>Seller Email</option>
                                            <?php /* <option value="users.users_type" <?php if ($searchField == 'users.users_type') echo 'selected="selected"'; ?>>Seller Type</option> */ ?>
                                              <option value="pos_number" <?php if ($searchField == 'pos_number') echo 'selected="selected"'; ?>>Seller POS Number</option>
                                              <?php /* <option value="users.bind_person_name" <?php if ($searchField == 'users.bind_person_name') echo 'selected="selected"'; ?>>Seller Bind Person Name</option> */ ?>
                                              <option value="winning_status" <?php if ($searchField == 'winning_status') echo 'selected="selected"'; ?>>Winning Status (Paid/Unpaid)</option>
                                              <option value="winning_amount" <?php if ($searchField == 'winning_amount') echo 'selected="selected"'; ?>>Winning Amount</option>
                                              <option value="settler_pos_number" <?php if ($searchField == 'settler_pos_number') echo 'selected="selected"'; ?>>Settler POS Number</option>
                                              <option value="settler_mobile" <?php if ($searchField == 'settler_mobile') echo 'selected="selected"'; ?>>Settler Mobile</option>
                                              <option value="buyer_mobile" <?php if ($searchField == 'buyer_mobile') echo 'selected="selected"'; ?>>Buyer Mobile</option>
                                              <option value="buyer_email" <?php if ($searchField == 'buyer_email') echo 'selected="selected"'; ?>>Buyer Email</option>
                                              <option value="draw_time_string" <?php if ($searchField == 'draw_time_string') echo 'selected="selected"'; ?>>Draw DateTime</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3 col-md-3">
                                            <!-- <input type="text" name="searchValue" id="searchValue" value="<?php echo htmlspecialchars($searchValue ?? ''); ?>" class="form-control form-control-sm" placeholder="Enter Search Text"> -->
                                            <input type="<?php echo ($searchField === 'draw_time_string') ? 'datetime-local' : 'text'; ?>" name="searchValue" id="searchValue" value="<?php echo ($searchField === 'draw_time_string' && !empty($searchValue)) ? date('Y-m-d\TH:i', strtotime($searchValue)) : htmlspecialchars($searchValue ?? ''); ?>" class="form-control form-control-sm" placeholder="<?php echo ($searchField === 'draw_time_string') ? 'Select Draw DateTime' : 'Enter Search Text'; ?>">
                                        </div>
                                        <div class="col-sm-6 col-md-6">
                                            <div class="row">
                                                <div class="col-sm-12 col-md-4">
                                                    <input type="datetime-local" name="fromDate" id="fromDate" autocomplete="off" value="<?php echo $fromDate ? date('Y-m-d\TH:i', strtotime($fromDate)) : ''; ?>" class="form-control form-control-sm" placeholder="From Date">
                                                </div>
                                                <div class="col-sm-12 col-md-4">
                                                    <input type="datetime-local" name="toDate" id="toDate" autocomplete="off" value="<?php echo $toDate ? date('Y-m-d\TH:i', strtotime($toDate)) : ''; ?>" class="form-control form-control-sm" placeholder="To Date">
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
                                                    <th width="15%">Order ID</th>
                                                    <th width="10%">Seller Details</th>
                                                    <th width="10%">Delivery Address</th>
                                                    <th width="20%">Game</th>
                                                    <th width="8%">Qty</th>
                                                    <th width="12%">Total Price</th>
                                                    <th width="15%">Created Date</th>
                                                    <th width="15%">Draw Date & Time</th>
                                                    <th width="10%">Buyer Details</th>
                                                    <th width="10%">Winning Details</th>
                                                    <th width="10%">Status</th>
                                                    <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody style="text-align: center;">
                                                <?php
                                                if (!empty($ALLDATA)):
                                                    $i = $first;
                                                    $j = 0;
                                                    foreach ($ALLDATA as $row):
                                                        $rowClass = ($j % 2 == 0) ? 'odd' : 'even';
                                                        $productOid = '';
                                                        if (!empty($row['products_id'])) {
                                                            $productOid = is_object($row['products_id']) && isset($row['products_id']->{'$id'}) ? $row['products_id']->{'$id'} : (string) $row['products_id'];
                                                        }
                                                         
                                                ?>
                                                <tr role="row" class="<?php echo $rowClass; ?>">
                                                    <td><?php echo $i++; ?></td>
                                                    <td><?php echo htmlspecialchars($row['order_id'] ?? 'N/A'); ?></td>
                                                    <td class="text-left">
                                                        <b>Name </b>           : <?=htmlspecialchars($row['seller_full_name'] ?? 'N/A'); ?><br/>
                                                        <b>Mobile </b>         : <?=htmlspecialchars($row['seller_users_mobile'] ?? 'N/A'); ?><br/>
                                                        <b>Email </b>          : <?=htmlspecialchars($row['seller_users_email'] ?? 'N/A'); ?><br/>
                                                        <b>Type </b>           : <?=htmlspecialchars($row['seller_users_type'] ?? 'N/A'); ?><br/>
                                                        <b>POS Number </b>     : <?=htmlspecialchars($row['seller_users_pos_number'] ?? 'N/A'); ?><br/>
                                                        <b>Bind with Name </b> : <?=htmlspecialchars($row['seller_users_bind_person_name'] ?? 'N/A'); ?>
                                                    </td>
                                                    <td>
                                                        <div class="text-center">
                                                          <?php if($row['delivery_address']): ?>
                                                            <p>
                                                              <b>Delivery Address</b> 
                                                              <br> <?=stripslashes($row['delivery_address']??'N/A')?>
                                                            </p>
                                                            <p> 
                                                              <b>Delivery charges</b> AED <?=stripslashes($row['delivery_charge']??'N/A');?> 
                                                            </p>
                                                          <?php else: ?>
                                                            --
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($row['products_name'] ?? 'N/A'); ?></td>
                                                    <td><?php echo (int) ($row['qty'] ?? 0); ?></td>
                                                    <td>AED <?php echo number_format((float) ($row['total_price'] ?? 0), 2); ?></td>
                                                    <td><?=date('d-m-Y H:i:s', $row['created_at']); ?></td>
                                                    <td>
                                                        <?php
                                                          $ca = $row['draw_time'] ?? null;
                                                          if ($ca) {
                                                              echo is_numeric($ca) ? date('d M Y h:i:s A', $ca) : date('d M Y h:i:s A', strtotime($ca));
                                                          } else {
                                                              echo 'N/A';
                                                          }
                                                        ?>
                                                    </td>
                                                    <td class="text-left">
                                                      <b>Sent via </b> : <?=htmlspecialchars($row['sms_type'] ?? 'N/A'); ?><br/>
                                                      <b>Mobile </b>   : <?=htmlspecialchars($row['buyer_mobile'] ?? 'N/A'); ?><br/>
                                                      <b>Email </b>    : <?=htmlspecialchars($row['buyer_email'] ?? 'N/A'); ?><br/>
                                                    </td>
                                                    <td>
                                                      <?php if($row['redeemed_at']): ?>
                                                        Winning Amount: <?=$row['winning_amount']??'N/A'; ?> <br>
                                                        Redeemed at: <?=$row['redeemed_at']?date('d-m-Y H:i:s', $row['redeemed_at']):'N/A'; ?><br>
                                                        Redeemed by: <?=$row['settler_full_name']??'N/A'; ?>  <br>
                                                        Redeemed POS ID: <?=$row['settler_pos_number']??'N/A'; ?> <br>
                                                        Redeemed Date: <?=$row['redeemed_at']?date('d-m-Y H:i:s', $row['redeemed_at']):'N/A'; ?><br>
                                                      <?php elseif($row['winning_status'] == 'unpaid'): ?>
                                                        Winning Amount: <?=$row['winning_amount']??'N/A'; ?> <br>
                                                        Winning Status: <?=$row['winning_status']??'N/A'; ?> <br>
                                                      <?php else: ?>
                                                        --
                                                      <?php endif; ?>
                                                    </td>
                                                    
                                                    <td><?php echo isset($row['status']) ? showStatus($row['status']) : 'N/A'; ?></td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                                            <ul class="dropdown-menu" role="menu">
                                                                <li><a href="<?php echo getCurrentControllerPath('addeditdata/'.$row['_id']->{'$id'});?>"><i class="far fa-eye"></i> View</a></li>
                                                              <?php if($row['status'] == 'A'): ?>
                                                                <li><a href="<?php echo getCurrentControllerPath('cancelationorder/'.$row['_id']->{'$id'}.'/CL'); ?>" onclick="return confirm('Set this order to Cancelled?');"><i class="fas fa-times-circle"></i> Cancel Order</a></li>
                                                              <?php endif; ?>
                                                                <li>
                                                                    <a href="<?=getCurrentControllerPath('sendsms/'.$row['_id']->{'$id'})?>" ><i class="fa fa-envelope"></i>Send SMS</a>
                                                                </li>
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
                                                    <td colspan="9" style="text-align:center;">No Tambola orders found.</td>
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
        <h5 class="modal-title" id="exampleModalLabel">Download Reports</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?=getCurrentControllerPath('exportexcel')?>" method="post" autocomplete="off">
      <div class="modal-body">
      <div class="row mb-2">
          <div class="col-sm-12 col-md-12">
            <div class="form-check form-check-inline">
              <input class="form-check-input export-type-checkbox" type="checkbox" name="exportType[]" id="exportTypeAccounts" value="accounts">
              <label class="form-check-label" for="exportTypeAccounts">Accounts</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input export-type-checkbox" type="checkbox" name="exportType[]" id="exportTypeDraw" value="winners">
              <label class="form-check-label" for="exportTypeDraw">Winners</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input export-type-checkbox" type="checkbox" name="exportType[]" id="exportTypeDraw" value="draw" checked>
              <label class="form-check-label" for="exportTypeDraw">Draw</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="includeDrawTime" id="includeDrawTime" value="1">
              <label class="form-check-label" for="includeDrawTime">Draw Time</label>
            </div>
          </div>
        </div>
      <div class="row mt-2">
          <div class="col-sm-12 col-md-6">
            <select name="searchField" id="searchField" class="custom-select custom-select-sm form-control form-control-sm">
              <option value="">Select Field</option>
              <option value="order_id" <?php if ($searchField == 'order_id') echo 'selected="selected"'; ?>>Order ID</option>
              <option value="products_name" <?php if ($searchField == 'products_name') echo 'selected="selected"'; ?>>Game Name</option>
              <option value="users_mobile" <?php if ($searchField == 'users_mobile') echo 'selected="selected"'; ?>>Seller Mobile</option>
              <option value="users_email" <?php if ($searchField == 'users_email') echo 'selected="selected"'; ?>>Seller Email</option>
            <?php /* <option value="users.users_type" <?php if ($searchField == 'users.users_type') echo 'selected="selected"'; ?>>Seller Type</option> */ ?>
              <option value="pos_number" <?php if ($searchField == 'pos_number') echo 'selected="selected"'; ?>>Seller POS Number</option>
              <?php /* <option value="users.bind_person_name" <?php if ($searchField == 'users.bind_person_name') echo 'selected="selected"'; ?>>Seller Bind Person Name</option> */ ?>
              <option value="winning_status" <?php if ($searchField == 'winning_status') echo 'selected="selected"'; ?>>Winning Status (Paid/Unpaid)</option>
              <option value="winning_amount" <?php if ($searchField == 'winning_amount') echo 'selected="selected"'; ?>>Winning Amount</option>
              <option value="settler_pos_number" <?php if ($searchField == 'settler_pos_number') echo 'selected="selected"'; ?>>Settler POS Number</option>
              <option value="settler_mobile" <?php if ($searchField == 'settler_mobile') echo 'selected="selected"'; ?>>Settler Mobile</option>
              <option value="buyer_mobile" <?php if ($searchField == 'buyer_mobile') echo 'selected="selected"'; ?>>Buyer Mobile</option>
              <option value="buyer_email" <?php if ($searchField == 'buyer_email') echo 'selected="selected"'; ?>>Buyer Email</option>
              <option value="draw_time_string" <?php if ($searchField == 'draw_time_string') echo 'selected="selected"'; ?>>Draw DateTime</option>
            </select>
          </div>
          <div class="col-sm-12 col-md-6">
            <!-- <input type="text" name="searchValue" id="searchValue" value="<?php echo $searchValue; ?>" class="form-control form-control-sm" placeholder="Enter Search Text"> -->
            <input type="<?php echo ($searchField === 'draw_time_string') ? 'datetime-local' : 'text'; ?>" name="searchValue" id="searchValue" value="<?php echo ($searchField === 'draw_time_string' && !empty($searchValue)) ? date('Y-m-d\TH:i', strtotime($searchValue)) : htmlspecialchars($searchValue ?? ''); ?>" class="form-control form-control-sm" placeholder="<?php echo ($searchField === 'draw_time_string') ? 'Select Draw DateTime' : 'Enter Search Text'; ?>">
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
              <input type="text" name="fromDate" id="fromDate" value="<?php echo $fromDate; ?>" class="form-control form-control-sm" placeholder="From Date">
            </div>
            <div class="col-sm-12 col-md-6">
              <input type="text" name="toDate" id="toDate" value="<?php echo $toDate; ?>" class="form-control form-control-sm" placeholder="From Date">
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

<script>
  (function () {
    var searchField = document.getElementById('searchField');
    var searchValue = document.getElementById('searchValue');
    if (searchField && searchValue) {
      searchField.addEventListener('change', function () {
        var isDrawDateTime = searchField.value === 'draw_time_string';
        searchValue.value = '';
        searchValue.type = isDrawDateTime ? 'datetime-local' : 'text';
        searchValue.placeholder = isDrawDateTime ? 'Select Draw DateTime' : 'Enter Search Text';
      });
    }
  })();
  
  (function () {
    var checkboxes = document.querySelectorAll('.export-type-checkbox');
    if (!checkboxes || !checkboxes.length) {
      return;
    }
    checkboxes.forEach(function (checkbox) {
      checkbox.addEventListener('change', function () {
        if (!this.checked) {
          var hasChecked = Array.prototype.some.call(checkboxes, function (item) {
            return item.checked;
          });
          if (!hasChecked) {
            this.checked = true;
          }
          return;
        }
        checkboxes.forEach(function (item) {
          if (item !== checkbox) {
            item.checked = false;
          }
        });
      });
    });
  })();
</script>