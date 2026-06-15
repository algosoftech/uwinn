<style>
  .upload-btn-wrapper {
    position: relative;
    overflow: hidden;
    display: inline-block;
  }

  .select-btn {
      border: 1px solid gray;
      color: gray;
      background-color: white;
      padding: 2px 10px;
      font-size: 16px;
      min-width: 200px;
  }

  .upload-btn-wrapper input[type=file] {
    font-size: 100px;
    position: absolute;
    left: 0;
    top: 0;
    opacity: 0;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  
</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>
<script>
$(function(){
   $("#fromDate").datepicker({dateFormat:'yy-mm-dd',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
   $("#toDate").datepicker({dateFormat:'yy-mm-dd',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
});
</script>
<div class="pcoded-main-container">
    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">All Eligible Raffle Winner List</a></li>
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
                <h5>Manage Eligible Raffle Winner List</h5>
                <a href="javaScriptcript:void{0}" class="btn btn-sm btn-primary pull-right" data-toggle="modal" data-target="#exportModal">Export excel</a>
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
                                      <option value="2" <?php if($perpage == '2')echo 'selected="selected"'; ?>>2</option>
                                      <option value="10" <?php if($perpage == '10')echo 'selected="selected"'; ?>>10</option>
                                      <option value="25" <?php if($perpage == '25')echo 'selected="selected"'; ?>>25</option>
                                      <option value="50" <?php if($perpage == '50')echo 'selected="selected"'; ?>>50</option>
                                      <option value="100" <?php if($perpage == '100')echo 'selected="selected"'; ?>>100</option>
                                      <option value="All" <?php if($perpage == 'All')echo 'selected="selected"'; ?>>All</option>
                                    </select>
                                    entries
                                  </label>
                                </div>
                              </div>
                              <div class="col-sm-3 col-md-3">
                                  <select name="searchField" id="searchField" class="custom-select custom-select-sm form-control form-control-sm">
                                    <option value="">Select Field</option>
                                    <option value="order_id" <?php if($searchField == 'order_id')echo 'selected="selected"'; ?>>Order ID </option>
                                    <option value="country_code" <?php if($searchField == 'country_code')echo 'selected="selected"'; ?>>Country Code </option>
                                    <option value="mobile_number" <?php if($searchField == 'mobile_number')echo 'selected="selected"'; ?>>Mobile Number </option>
                                  </select>
                              </div>
                              <div class="col-sm-3 col-md-3">
                                <input type="text" name="searchValue" id="searchValue" value="<?php echo $searchValue; ?>" class="form-control form-control-sm" placeholder="Enter Search Text">
                              </div>
                              <div class="col-sm-6 col-md-6">
                                  <div class="row" >
                                    <div class="col-sm-12 col-md-4">
                                      <input type="datetime-local" name="fromDate" id="fromDate" autocomplete="off" value="<?php echo $fromDate; ?>" class="form-control form-control-sm" placeholder="From Date">
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                      <input type="datetime-local" name="toDate" id="toDate" autocomplete="off" value="<?php echo $toDate; ?>" class="form-control form-control-sm" placeholder="To Date">
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                      <input type="submit" name="Search" value="Search" class="btn btn-sm btn-primary">
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
                                  <th width="5%" style="text-align: center;">S.No.</th>
                                  <th width="20%">Order ID</th>
                                  <th width="20%">Added By</th>
                                  <th width="20%">Name</th>
                                  <th width="20%">Contact Number</th>
                                  <th width="10%">status</th>
                                  <th width="10%">Created At</th>
                                  <th width="10%">Action</th>
                                </tr>
                              </thead>
                              <tbody style="text-align: center;">
                                <?php if($ALLDATA <> ""): $i=$first; $j=0; foreach($ALLDATA as $ALLDATAINFO): 
                                if($j%2==0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;
                                ?>
                                <tr role="row" class="<?php echo $rowClass; ?>">
                                  <td style="text-align: center;"><?=$i++?></td>
                                  
                                  <td> <?=$ALLDATAINFO['order_id'];?> </td>
                                  <td> <?=$ALLDATAINFO['pos_number'];?> </td>
                                  <td> <?=$ALLDATAINFO['first_name'].' '.$ALLDATAINFO['last_name'];?> </td>
                                  <td> <?=$ALLDATAINFO['country_code'].' '.$ALLDATAINFO['mobile_number'];?> </td>
                                  <td style="text-align: center;"><?=showStatus($ALLDATAINFO['status']);?>  </td>
                                  <td> <?=$ALLDATAINFO['created_at'];?></td>
                                  <td>
                                    <div class="btn-group">
                                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                      <ul class="dropdown-menu" role="menu">
                                        <li>
                                            <a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['order_id'])?>" 
                                               onClick="return confirm('Do you want to change status');">
                                               <i class="fas <?=$ALLDATAINFO['status'] == "A" ? "fa-thumbs-down" : "fa-thumbs-up";?>"></i>
                                               <?=$ALLDATAINFO['status'] == "A" ? "Inactive" : "Active";?> 
                                            </a>
                                        </li>
                                        <?php if($ALLDATAINFO['status'] == "A"): ?>
                                        <li>
                                            <a href="<?php echo getCurrentControllerPath('resendconfirmation/'.$ALLDATAINFO['order_id'])?>" 
                                               onClick="return confirm('Resend confirmation sms to user');">
                                               <i class="fas fa-envelope"></i> Resend confirmation sms
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                      </ul>
                                    </div>
                                  </td>
                                </tr>
                                <?php $j++; endforeach; else: ?>
                                <tr>
                                  <td colspan="6" style="text-align:center;">No Data Available In Table</td>
                                </tr>
                                <?php endif; ?>
                              </tbody>
                              </table>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-12 col-md-5">
                          <div class="dataTables_info" role="status" aria-live="polite"><?php echo $noOfContent; ?></div>
                        </div>
                        <div class="col-sm-12 col-md-7">
                          <div class="dataTables_paginate paging_simple_numbers">
                            <?php echo $PAGINATION; ?>
                          </div>
                        </div>
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
        <h5 class="modal-title" id="exampleModalLabel">Download Order Reports</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?=getCurrentControllerPath('exportexcel')?>" method="post" autocomplete="off">
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-12 col-md-6">
            <label for="recipient-name" class="col-form-label">Form:</label>
          </div>
          <div class="col-sm-12 col-md-6">
            <label for="recipient-name" class="col-form-label">To:</label>
          </div>
          <div class="col-sm-12 col-md-6">
            <input type="datetime-local" name="fromDate" id="fromDate1" value="<?php echo $fromDate; ?>" class="form-control form-control-sm" placeholder="From Date">
          </div>
          <div class="col-sm-12 col-md-6">
            <input type="datetime-local" name="toDate" id="toDate1" value="<?php echo $toDate; ?>" class="form-control form-control-sm" placeholder="From Date">
          </div>
        </div>
        <div class="row mt-2">
          <div class="col-sm-12 col-md-6">
            <select name="searchField" id="searchField" class="custom-select custom-select-sm form-control form-control-sm">
              <option value="">Select Field</option>
              <option value="order_id" <?php if($searchField == 'order_id')echo 'selected="selected"'; ?>>Order ID </option>
              <option value="country_code" <?php if($searchField == 'country_code')echo 'selected="selected"'; ?>>Country Code </option>
              <option value="mobile_number" <?php if($searchField == 'mobile_number')echo 'selected="selected"'; ?>>Mobile Number </option>
            </select>
          </div>
          <div class="col-sm-12 col-md-6">
            <input type="text" name="searchValue" id="searchValue" value="<?php echo $searchValue; ?>" class="form-control form-control-sm" placeholder="Enter Search Text">
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