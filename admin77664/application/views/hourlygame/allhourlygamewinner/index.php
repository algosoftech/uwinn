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
                        <?php /* ?><h5 class="m-b-10">Welcome <?=sessionData('HCAP_ADMIN_FIRST_NAME')?></h5><?php */ ?>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">All Hourly Game Winner List</a></li>
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
                <h5>Manage Hourly Game Winner List</h5>
              </div>
              <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                      <form class="" action="<?=getCurrentControllerPath('checkpreview');?>" method="post" enctype="multipart/form-data" >
                        <div class="row">
                          <div class="col-sm-12 col-md-12 col-lg-12">
                            <fieldset>
                              <legend>Hourly Game Winner </legend>
                              <h6>
                                  Upload Hourly Game Winner CSV 
                                  <sup>
                                    <a href="<?=fileBaseUrl.'assets/hourly-game-winner-sample.csv'?>">Sample</a>
                                  </sup> 
                                </h6>
                                <div class="upload-btn-wrapper">
                                  <button class="select-btn">Upload a file</button>
                                  <input type="file" name="csvFile"  id="daily-dealz-plus" accept=".csv" />
                                </div>
                                <div class="upload-btn-wrapper">
                                  <button class="uplaod-btn btn btn-sm btn-primary" id="daily-upload-btn" disabled title="Upload CSV file first" >Check Preview</button>
                                </div>
                                <div class="upload-btn-wrapper">
                                  <input type="reset" class="btn btn-sm btn-secondary" value="Cancel">
                                </div>
                                <!-- <div class="upload-btn-wrapper">
                                  <a href="javascript:coid(0)" class="btn btn-sm btn-primary pull-right" id="delete-selected-orders" style="margin-left: 5px;">Delete Seleted Orders</a>
                                  <a href="javascript:coid(0)" class="btn btn-sm btn-primary pull-right" id="batch-selected-orders" style="margin-left: 5px;">Delete Complate Uploaded batch</a>
                                  <a href="javaScriptcript:void{0}" class="btn btn-sm btn-primary pull-right" style="margin-left: 5px;" data-toggle="modal" data-target="#exportModal">Export excel</a>
                                </div> -->
                                <!-- <a href="javaScriptcript:void{0}" class="btn btn-sm btn-primary pull-right mr-2" data-toggle="modal" data-target="#bulkInactive">Bulk Inactive</a> -->
                                

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
                                  <select name="searchField" id="searchField" class="custom-select custom-select-sm form-control form-control-sm">
                                    <option value="">Select Field</option>
                                    <option value="status" <?php if($searchField == 'status')echo 'selected="selected"'; ?>>Status (Active, Inactive )</option>
                                    <option value="batch_id" <?php if($searchField == 'batch_id')echo 'selected="selected"'; ?>>Batch ID </option>
                                  </select>
                              </div>
                              <div class="col-sm-3 col-md-3">
                                <input type="text" name="searchValue" id="searchValue" value="<?php echo $searchValue; ?>" class="form-control form-control-sm" placeholder="Enter Search Text">
                              </div>
                              <div class="col-sm-6 col-md-3">
                                  <div class="row" >
                                    <div class="col-sm-12 col-md-6">
                                      <input type="text" name="fromDate" id="fromDate" autocomplete="off" value="<?php echo $fromDate; ?>" class="form-control form-control-sm" placeholder="From Date">
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
                                  <th width="5%" style="text-align: center;">
                                    <div class="btn-group-toggle" data-toggle="buttons">
                                      <label class="btn btn-sm btn-warning active" id="select-all-label"> 
                                        <input type="checkbox" autocomplete="off" id="select-all"> Select All
                                      </label>
                                    </div>
                                  </th>
                                  <th width="20%">Batch ID</th>
                                  <th width="20%">Information</th>
                                  <th width="20%">Created Date</th>
                                  <th width="20%">Status</th>
                                  <th width="10%">Action</th>
                                </tr>
                              </thead>
                              <tbody style="text-align: center;">
                                <?php if($ALLDATA <> ""): $i=$first; $j=0; foreach($ALLDATA as $ALLDATAINFO): 
                                if($j%2==0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;
                                ?>
                                <tr role="row" class="<?php echo $rowClass; ?>">
                                  <td style="text-align: center;"><?=$i++?></td>
                                  <td>
                                    <input type="checkbox" name="delete"  class="delete" value="<?=$ALLDATAINFO['voucher_id'];?>">
                                  </td>
                                  
                                  <td> <?=$ALLDATAINFO['batch_id'];?> </td>
                                  <td>
                                    <?php if($ALLDATAINFO['winning_amount'] >0 ): ?>

                                      <div>
                                       <b> Total Winners :-  </b> <?= $ALLDATAINFO['total_count'] ?>   <br>
                                       <b> Total Winning Amount :- </b> <?= $ALLDATAINFO['winning_amount'] ?>   <br>
                                       <b>Total Amount Redeemed : <?= $ALLDATAINFO['paid'] ?> </b><br>
                                       <b>Total Amount Unpaid   : <?=$ALLDATAINFO['unpaid']?> </b><br>
                                       <b>Total Amount Deleted  : <?=$ALLDATAINFO['deleted']?> </b><br>
                                       <b>CSV : </b><?=$ALLDATAINFO['csv_name'];?> 

                                      </div>

                                    <?php else: ?>

                                      <p>Please Check CSV And Upload Again.  <b>CSV : <?=$ALLDATAINFO['csv_name'];?> </b> </p>

                                   <?php endif; ?>

                                  </td>
                                  
                                  <td> <?=date('Y-m-d H:i:s',$ALLDATAINFO['winner_uploaded_at']); ?> </td>
                                  <td>
                                    Active   : <?=$ALLDATAINFO['active']?> <br>
                                    Inactive : <?=$ALLDATAINFO['inactive']?> <br>
                                    Redeemed : <?=$ALLDATAINFO['redeemed_count']?> <br>
                                    Deleted  : <?=$ALLDATAINFO['deleted']?> <br>
                                  </td>  
                                  <td>
                                    <div class="btn-group">
                                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                      <ul class="dropdown-menu" role="menu">
                                        <li>
                                            <a href="<?php echo getCurrentControllerPath('addeditdata/'.$ALLDATAINFO['batch_id'])?>" ><i class="fas fa-eye"></i>View Details</a>
                                        </li>
                                        <li>
                                            <a href="<?php echo getCurrentControllerPath('changestatusbatch/'.$ALLDATAINFO['batch_id'].'/A')?>" onClick="return confirm('Do you want to change status');" ><i class="fas fa-thumbs-up"></i>Active</a>
                                          </li>
                                          <li>
                                            <a href="<?php echo getCurrentControllerPath('changestatusbatch/'.$ALLDATAINFO['batch_id'].'/N')?>" onClick="return confirm('Do you want to change status');"><i class="fas fa-thumbs-down"></i> Inactive</a>
                                        </li>
                                        <li>
                                            <a href="<?php echo getCurrentControllerPath('changestatusbatch/'.$ALLDATAINFO['batch_id'].'/D')?>" onClick="return confirm('Do you want to change status');"><i class="fas fa-trash"></i> Deleted</a>
                                        </li>
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

<div class="modal fade" id="bulkInactive" tabindex="-1" role="dialog" aria-labelledby="bulkInactiveModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bulkInactiveModalLabel">Upload CSV</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?=getCurrentControllerPath('checkInactivepreview');?>" method="post" enctype="multipart/form-data" autocomplete="off">
          <div class="modal-body">
           <div class="row">
                <div class="col-sm-12 col-md-12">
                    <h6>
                      Uplaod Draw List CSV 
                      <sup><a href="<?=fileBaseUrl.'assets/inactivewinnerlist.csv'?>">Sample</a></sup>
                    <div class="upload-inactive-btn-wrapper">
                      <input type="file" name="csvFile"  id="inactive-csv" accept=".csv" />
                    </div>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="uplaod-btn btn btn-primary" id="inactive-upload-btn" disabled title="Upload CSV file first" >Check Preview</button>
          </div>
      </form>
    </div>
  </div>
</div>
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Download WInner Reports</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div> 
      <form action="<?=getCurrentControllerPath('exportexcel');?>" method="post" autocomplete="off">
      <div class="modal-body">
          <div class="row">
            <div class="col-sm-12 col-md-6">
              <label for="recipient-name" class="col-form-label">Form:</label>
            </div>
            <div class="col-sm-12 col-md-6">
              <label for="recipient-name" class="col-form-label">To:</label>
            </div>
            <div class="col-sm-12 col-md-6">
              <input type="datetime-local" name="fromDate" id="fromDate" autocomplete="off" value="<?php echo $fromDate; ?>" class="form-control form-control-sm" placeholder="From Date">
            </div>
            <div class="col-sm-12 col-md-6">
             <input type="datetime-local" name="toDate" id="ToDate" autocomplete="off" value="<?php echo $toDate; ?>" class="form-control form-control-sm" placeholder="To Date">
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



  $('#inactive-csv').on('change', function(){
    let file = $(this).val();
    $('#inactive-upload-btn').attr('disabled' , false);
  });

  
  $('#daily-dealz-plus').on('change', function(){
    let file = $(this).val();
    $('#daily-upload-btn').attr('disabled' , false);
  });


 $('#select-all').on('change', function(){

    if($(this).is(':checked')) {
        $('#select-all-label').addClass('btn-warning');
        $('#select-all-label').removeClass('btn-primary');
        $('.delete').prop('checked', false);

    } else {
        $('#select-all-label').addClass('btn-primary');
        $('#select-all-label').removeClass('btn-warning');
        $('.delete').prop('checked', true);
    }


  });

 $('#delete-selected-orders').on('click', function(){
    if(confirm('Do you Want to delete?')){
        var checkedCheckboxes = $('.delete:checked');
        if(checkedCheckboxes.length > 0){
            showLoadingOverlay();
            checkedCheckboxes.each(function(){
                var voucher_id = $(this).val();
                $.ajax({
                  url: '<?= base_url('uwin/voucher/deletedata/') ?>'+voucher_id,
                  type: 'GET',
                  success: function(response) {
                      // All data uploaded, hide loading overlay
                      hideLoadingOverlay();
                      // Show success alert and redirect
                      window.location.href = '<?= base_url('uwin/voucher/index') ?>'; // Replace with your redirect URL
                  },
                  error: function() {
                    console.error('Failed to upload data for row ');
                    // Hide loading overlay in case of error
                    hideLoadingOverlay();
                  }
                });
                $(this).closest('tr').remove();
            });
        } else {
            alert('Please select which row(s) you want to delete.');
        }
    }
});


 $('#batch-selected-orders').on('click', function(){
    if(confirm('Do you Want to delete complete uploaded batch?')){
        var checkedCheckboxes = $('.delete:checked');
        if(checkedCheckboxes.length > 0){

         let batch_id = checkedCheckboxes.parent('td').find('input[name^="batch_id"]').val();  // console.log(batch_id)
         if(batch_id !=''){
            $.ajax({
                  url: '<?= base_url('uwin/voucher/deletebatchdata/') ?>'+batch_id,
                  type: 'GET',
                  success: function(response) {
                      // All data uploaded, hide loading overlay
                      hideLoadingOverlay();
                      // Show success alert and redirect
                      window.location.href = '<?= base_url('uwin/voucher/index') ?>'; // Replace with your redirect URL
                  },
                  error: function() {
                    console.error('Failed to upload data for row ');
                    // Hide loading overlay in case of error
                    hideLoadingOverlay();
                  }
                });



         }else{
            alert('Batch ID not Found Please delete manually');
         }
          


             
        } else {
            alert('Please select which row(s) you want to delete.');
        }
    }
});


 function showLoadingOverlay() {
  // Create overlay element
  var overlay = $('<div id="loadingOverlay"></div>');
  // Style overlay
  overlay.css({
    'position': 'fixed',
    'top': 0,
    'left': 0,
    'width': '100%',
    'height': '100%',
    'background-color': 'rgba(0, 0, 0, 0.5)',
    'z-index': 9999,
    'display': 'flex',
    'justify-content': 'center',
    'align-items': 'center'
  });
  // Add loading spinner
  var spinner = $('<div class="spinner"></div>');
  spinner.css({
    'border': '7px solid #f3f3f3',
    'border-top': '7px solid #3498db',
    'border-radius': '50%',
    'width': '100px',
    'height': '100px',
    'animation': 'spin 1s linear infinite'
  });
  overlay.append(spinner);
  // Append overlay to body
  $('body').append(overlay);
}


function hideLoadingOverlay() {
  // Remove overlay element
  $('#loadingOverlay').remove();
}



</script>