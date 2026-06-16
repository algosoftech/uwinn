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
                        <li class="breadcrumb-item"><a href="<?php echo getCurrentControllerPath(''); ?>">All Voucher List</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">View  List</a></li>
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
                

                <form id="Data_Form" name="Data_Form" method="GET" action="<?=$forAction;?>">
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
                                    <option value="users.users_name" <?php if($searchField == 'users.users_name')echo 'selected="selected"'; ?>>First Name </option>
                                    <option value="users.last_name" <?php if($searchField == 'users.last_name')echo 'selected="selected"'; ?>>Last Name </option>
                                    <option value="order_id" <?php if($searchField == 'order_id')echo 'selected="selected"'; ?>>Order ID </option>
                                    <option value="winning_status" <?php if($searchField == 'winning_status')echo 'selected="selected"'; ?>>Status (Paid, Unpaid)</option>
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
                                  <th width="20%">Seller Name</th>
                                  <th width="20%">Order ID</th>
                                  <th width="20%">Store Name</th>
                                  <th width="20%">Coupon Code</th>
                                  <th width="20%">Matching Code</th>
                                  <th width="20%">Amount</th>
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

                                  <?php $sNO = $i++;  ?>

                                  <td style="text-align: center;">  <?= $sNO; ?></td>
                                  <td><?=stripslashes($ALLDATAINFO['seller_users_name'].' '.$ALLDATAINFO['seller_users_last_name'] )?></td>
                                  <!-- <td>
                                    <?php if($ALLDATAINFO["winning_status"] !='unpaid'): ?>
                                      <?=stripslashes($ALLDATAINFO['order_id']?$ALLDATAINFO['order_id']:"N/A")?>
                                    <?php else: ?>
                                     <?=stripslashes($ALLDATAINFO['order_id']?$ALLDATAINFO['order_id']:"N/A")?>
                                    <?php endif; ?>
                                  </td> -->
                                  <td><?=stripslashes($ALLDATAINFO['order_id']?$ALLDATAINFO['order_id']:"N/A")?></td>
                                  <td><?=$ALLDATAINFO['seller_store_name']?></td>
                                  <td><?=$ALLDATAINFO['coupon_code']?></td>
                                  <td><?=$ALLDATAINFO['matching_coupons']?></td>
                                  <td><?=number_format($ALLDATAINFO['winning_amount'],2)?></td>
                                  <td>
                                    <?php
                                      $createdAt = $ALLDATAINFO['winner_uploaded_at'];

                                      if (is_numeric($createdAt)) {
                                          // If it's too large → milliseconds
                                          if ($createdAt > 1000000000000) {
                                              $createdAt = $createdAt / 1000;
                                          }
                                          echo date('Y-m-d H:i:s', $createdAt);
                                      } else {
                                          // Assume it's a valid date string
                                          echo date('Y-m-d H:i:s', strtotime($createdAt));
                                      }
                                    ?>
                                  </td>
                                  <td>
                                    <div class="badge badge-light-<?=$ALLDATAINFO["winning_status"] =='paid' ? 'success' : 'danger';?>">
                                      <?=$ALLDATAINFO["winning_status"];?>
                                    </div>
                                  </td>
                                  <td>
                                     
                                    <div class="btn-group">
                                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                      <ul class="dropdown-menu" role="menu">
                                        <?php if($ALLDATAINFO['winning_status'] == 'unpaid'): ?>
                                          <li>
                                            <a href="<?php echo getCurrentControllerPath('redeemorder/'.$ALLDATAINFO['_id']->{'$id'})?>" onClick="return confirm('Do you want to redeem this voucher?');"><i class="fas fa-money-bill"></i> Redeem</a>
                                          </li>
                                          <li>
                                            <a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['_id']->{'$id'}.'/I')?>" onClick="return confirm('Do you want to change status');" ><i class="fas fa-thumbs-down"></i>Inactive</a>
                                          </li>
                                        <?php elseif($ALLDATAINFO['winning_status'] == 'paid'): ?>
                                          <li>
                                            <a href="<?php echo getCurrentControllerPath('cancelredeem/'.$ALLDATAINFO['_id']->{'$id'})?>" onClick="return confirm('Do you want to cancel this redemption?');"><i class="fas fa-undo"></i> Cancel Redeem</a>
                                          </li>
                                        <?php elseif($ALLDATAINFO['winning_status'] == 'Inactive'): ?>
                                          <li>
                                            <a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['_id']->{'$id'}.'/A')?>" onClick="return confirm('Do you want to change status');"><i class="fas fa-thumbs-up"></i> Active</a>
                                          </li>
                                        <?php endif; ?>
                                        <?php if(empty($ALLDATAINFO['soft_delete']) || $ALLDATAINFO['soft_delete'] == 0): ?>
                                          <li> 
                                              <a href="<?php echo getCurrentControllerPath('deletedata/'.$ALLDATAINFO['_id']->{'$id'})?>" onClick="return confirm('Do you want to delete');" ><i class="fas fa-trash"></i>Delete</a> 
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

<script>
  $('#daily-dealz-plus').on('change', function(){
    let file = $(this).val();
    $('#daily-upload-btn').attr('disabled' , false);
  });

  $('#inactive-csv').on('change', function(){
    let file = $(this).val();
    $('#inactive-upload-btn').attr('disabled' , false);
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

//  $('#delete-selected-orders').on('click', function(){
//     if(confirm('Do you Want to delete?')){
//         var checkedCheckboxes = $('.delete:checked');
//         if(checkedCheckboxes.length > 0){
//             showLoadingOverlay();
//             checkedCheckboxes.each(function(){
//                 var voucher_id = $(this).val();
//                 $.ajax({
//                   url: '<?= base_url('uwin/voucher/deletedata/') ?>'+voucher_id,
//                   type: 'GET',
//                   success: function(response) {
//                       // All data uploaded, hide loading overlay
//                       hideLoadingOverlay();
//                       // Show success alert and redirect
//                       alertMessageModelPopup('<?php echo $this->session->flashdata('alert_success'); ?>','success');
//                       window.location.href = '<?= base_url('uwin/voucher/index') ?>'; // Replace with your redirect URL
//                   },
//                   error: function() {
//                     console.error('Failed to upload data for row ');
//                     // Hide loading overlay in case of error
//                     hideLoadingOverlay();
//                   }
//                 });
//                 $(this).closest('tr').remove();
//             });
//         } else {
//             alert('Please select which row(s) you want to delete.');
//         }
//     }
// });

function multipleChangeStatus(status = 0) {
    if (confirm('Do you want to change the status?')) {
        const checkedCheckboxes = $('.delete:checked');

        if (checkedCheckboxes.length === 0) {
            alert('Please select which row(s) you want to update.');
            return;
        }

        showLoadingOverlay();

        const data = [];
        checkedCheckboxes.each(function () {
            data.push({
                voucher_id: $(this).val(),
                status: status
            });
        });

        // Disable button to prevent multiple clicks
        $('#inactive-selected-orders').prop('disabled', true);

        $.ajax({
            url: '<?= base_url('uwin/voucher/multiplechangestatus') ?>',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function (response) {
                hideLoadingOverlay();
                // $('#inactive-selected-orders').prop('disabled', false);

                // Parse JSON if it's not already an object
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }

                if (response.status === 'success') {
                    alertMessageModelPopup('Status updated for selected vouchers','success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    alert('Something went wrong!');
                }
            },
            error: function () {
                hideLoadingOverlay();
                $('#inactive-selected-orders').prop('disabled', false);
                alert('Failed to update status.');
            }
        });
    }
}


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