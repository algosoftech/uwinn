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
</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
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
                        <?php /* ?><h5 class="m-b-10">Welcome <?=sessionData('HCAP_ADMIN_FIRST_NAME')?></h5><?php */ ?>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">All Voucher List</a></li>
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
                <h5>Manage Voucher List</h5>
              </div>
              <div class="card-body">
                <div class="row">
                    <div class="col-sm-6 col-md-6 col-lg-6">
                      <form class="" method="post" enctype="multipart/form-data" >
                        <div class="row">
                          <div class="col-sm-12 col-md-12 col-lg-12">
                            <fieldset>
                              <legend>Daily Dealz Plus</legend>
                              <h6>
                                  Uplaod Daily Draws CSV 
                                  <sup>
                                    <a href="<?=fileBaseUrl.'assets/csv-sample.csv'?>">Sample</a>
                                  </sup>
                                </h6>
                                <div class="upload-btn-wrapper">
                                  <button class="select-btn">Upload a file</button>
                                  <input type="file" name="csvFile"  id="daily-dealz-plus" accept=".csv" />
                                </div>
                                <div class="upload-btn-wrapper">
                                  <button class="uplaod-btn btn btn-sm btn-primary" id="daily-upload-btn" disabled title="Upload CSV file first" >Upload</button>
                                </div>
                                <div class="upload-btn-wrapper">
                                  <input type="reset" class="btn btn-sm btn-secondary" value="Cancel">
                                </div>
                            </fieldset>
                          </div>
                        </div>
                      </form>
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-6">
                      <form class="" method="post" action="<?=getCurrentControllerPath('upload_subwinners')?>"  enctype="multipart/form-data" >
                        <div class="row">
                          <div class="col-sm-12 col-md-12 col-lg-12">
                            <fieldset>
                              <legend>Sub Winners</legend>
                              <h6>
                                Uplaod Daily Draws CSV 
                                <sup>
                                  <a href="<?=fileBaseUrl.'assets/csv-sample.csv'?>">Sample</a>
                                </sup>
                              </h6>
                                <div class="upload-btn-wrapper">
                                  <button class="select-btn">Upload a file</button>
                                  <input type="file" name="csvFile"  id="subwinner" accept=".csv" />
                                </div>
                                <div class="upload-btn-wrapper">
                                  <button class="uplaod-btn btn btn-sm btn-primary" id="subwinner-upload-btn" disabled title="Upload CSV file first" >Upload</button>
                                </div>
                                <div class="upload-btn-wrapper">
                                  <input type="reset" class="btn btn-sm btn-secondary" value="Cancel">
                                </div>
                            </fieldset>
                          </div>
                        </div>
                      </form>
                    </div>
                </div>


                <form id="Data_Form" name="Data_Form" method="get" action="<?php echo $forAction; ?>">
                  <div class="dt-responsive table-responsive">
                    <div id="simpletable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                      
                      <div class="row">
                        <div class="col-sm-3 col-md-3">
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
                          <select id="select-module" class="custom-select custom-select-sm form-control form-control-sm" name="select-module">
                            <option <?php if($selected_module == "Daily Dealz Plus"): echo 'selected'; endif; ?> value="Daily Dealz Plus">Daily Dealz Plus</option>
                            <option <?php if($selected_module == "Sub Winners"): echo 'selected'; endif; ?>  value="Sub Winners">Sub Winners</option>
                          </select>
                        </div>
                        <div class="col-sm-2 col-md-2">
                          <select name="searchField" id="searchField" class="custom-select custom-select-sm form-control form-control-sm">
                            <option value="">Select Field</option>
                            <option value="status" <?php if($searchField == 'status')echo 'selected="selected"'; ?>>Status (Active, Inactive )</option>
                            <option value="order_id" <?php if($searchField == 'order_id')echo 'selected="selected"'; ?>>Order ID </option>
                            <option value="code" <?php if($searchField == 'code')echo 'selected="selected"'; ?>>Coupon Code </option>
                            <option value="created_at" <?php if($searchField == 'created_at')echo 'selected="selected"'; ?>>Created Date</option>
                          </select>
                        </div>
                        <div class="col-sm-3 col-md-3">
                          <input type="text" name="searchValue" id="searchValue" value="<?php echo $searchValue; ?>" class="form-control form-control-sm" placeholder="Enter Search Text">
                        </div>
                        <div class="col-sm-1 col-md-1">
                          <input type="submit" name="Search" value="Search" class="btn btn-sm btn-primary">
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-sm-12">
                          <div class="table-responsive">
                            <table id="simpletable" class="table table-striped table-bordered nowrap dataTable" role="grid" aria-describedby="simpletable_info">
                              <thead style="text-align: center;">
                                <tr role="row">
                                  <th width="5%" style="text-align: center;">S.No.</th>
                                  <th width="20%">Name</th>
                                  <th width="20%">Order ID</th>
                                  <th width="20%">Coupon Code</th>
                                  <th width="20%">Amount</th>
                                  <th width="20%">Created Date</th>
                                  <th width="20%">status</th>
                                  <th width="10%">Action</th>
                                </tr>
                              </thead>
                              <tbody style="text-align: center;">
                                <?php if($ALLDATA <> ""): $i=$first; $j=0; foreach($ALLDATA as $ALLDATAINFO): 
                                if($j%2==0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;
                                ?>
                                <tr role="row" class="<?php echo $rowClass; ?>">
                                  <td style="text-align: center;"><?=$i++?></td>
                                  <td><?=stripslashes($ALLDATAINFO['first_name'].' '.$ALLDATAINFO['last_name'] )?></td>
                                  <td><?=stripslashes($ALLDATAINFO['order_id']?$ALLDATAINFO['order_id']:"N/A")?></td>
                                  <td><?=$ALLDATAINFO['code']?></td>
                                  <td><?=number_format($ALLDATAINFO['amount'],2)?></td>
                                  <td><?=$ALLDATAINFO['created_at'];?></td>
                                    <td style="text-align: right;">
                                      <?php if($ALLDATAINFO['status'] == 1 ): ?>
                                        <?=showStatus('A')?>
                                      <?php else: ?>
                                        <?=showStatus('I')?>
                                      <?php endif; ?>
                                  </td>
                                  <td>
                                    <div class="btn-group">
                                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                      <ul class="dropdown-menu" role="menu">
                                        <li>
                                          <?php if($ALLDATAINFO['status'] == 1): ?>
                                            <a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['voucher_id'].'/0')?>" onClick="return confirm('Do you want to change status');" ><i class="fas fa-thumbs-down"></i>Inactive</a>
                                          <?php elseif($ALLDATAINFO['status'] == 0): ?>
                                            <a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['voucher_id'].'/1')?>" onClick="return confirm('Do you want to change status');"><i class="fas fa-thumbs-up"></i> Active</a>
                                          <?php endif; ?>
                                        </li>
                                        <?php if($ALLDATAINFO['soft_delete'] == 0): ?>
                                          <li> 
                                              <a href="<?php echo getCurrentControllerPath('deletedata/'.$ALLDATAINFO['voucher_id'])?>" onClick="return confirm('Do you want to delete');" ><i class="fas fa-trash"></i>Delete</a> 
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
      <form action="<?=getCurrentControllerPath('exportexcel'.$excelExportCondition);?>" method="post" autocomplete="off">
      <div class="modal-body">
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

           <div class="row mt-2"  style="margin:0px;">
              <div class="col-sm-12 col-md-6">
                <select name="searchField" id="searchField" class="custom-select custom-select-sm form-control form-control-sm">
                  <option value="">Select Field</option>
                  <option value="record_type" <?php if($searchField == 'record_type')echo 'selected="selected"'; ?>>Record Type (Credit / Debit) </option>
                  <option value="recharge_by" <?php if($searchField == 'recharge_by')echo 'selected="selected"'; ?>>Recharge By (Email) </option>
                  <option value="recharge_to" <?php if($searchField == 'recharge_to')echo 'selected="selected"'; ?>>Recharge To ( Email/Number ) </option>
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

<script>
  $('#daily-dealz-plus').on('change', function(){
    let file = $(this).val();
    $('#daily-upload-btn').attr('disabled' , false);
  });

  $('#subwinner').on('change', function(){
    let file = $(this).val();
    $('#subwinner-upload-btn').attr('disabled' , false);
  });

  $("#select-module").on('change' ,function(){
    let selectedModule = $(this).val();
    if(selectedModule == "Daily Dealz Plus"){
      window.location.href ="<?=base_url('draws/voucher/index')?>";
    }else{
      window.location.href ="<?=base_url('draws/subwinner/index')?>"
    }

  });

</script>