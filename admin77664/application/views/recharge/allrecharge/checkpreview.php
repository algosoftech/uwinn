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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">All Voucher Preview List</a></li>
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
                <h5>Manage Voucher Preview List</h5>
               <!-- <form method="post" action="<?=base_url('uwin/voucher/uploadVoucher')?>"> -->
               <form  id="allwinners">
                  <input type="hidden" name="recharge_type" id="bulk_preview_recharge_type" value="<?php echo !empty($recharge_type) ? $recharge_type : 'upoint'; ?>">

                  <div class=" pull-right">
                     <input type="submit" class="btn btn-sm btn-primary pull-right" onclick="return confirm('Do you want to uplaod?')" style="margin-left: 5px;" value="Upload"> 
                     <a href="javascript:coid(0)" class="btn btn-sm btn-primary pull-right" id="delete-selected-orders" style="margin-left: 5px;">Delete Check Orders</a>
                  </div>
                  <div class=" pull-left">
                    <b><label class="btn btn-sm btn-primary pull-left total-upload" data-count="<?php echo !empty($ALLDATA) ? count($ALLDATA) : 0; ?>" >Total Data To Uplaod : <?php echo !empty($ALLDATA) ? count($ALLDATA) : 0; ?></label></b>
                    <label class="btn btn-sm btn-<?php echo (!empty($recharge_type) && $recharge_type == 'recharge_point') ? 'info' : 'secondary'; ?> pull-left ml-2">
                      Type : <?php echo (!empty($recharge_type) && $recharge_type == 'recharge_point') ? 'Recharge Point (Ding)' : 'UPOINT'; ?>
                    </label>
                  </div>
               
              </div>
              <div class="card-body">
                <div class="row">
                </div>
                  <div class="dt-responsive table-responsive">
                    <div id="simpletable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                      <div class="row">
                        <div class="col-sm-12">
                          <div class="table-responsive">
                            <table id="simpletable" class="table table-striped table-bordered nowrap dataTable" role="grid" aria-describedby="simpletable_info">
                              <thead style="text-align: center;">
                                <tr role="row">
                                  <th width="5%" style="text-align: center;">
                                    <div class="btn-group-toggle" data-toggle="buttons">
                                      <label class="btn btn-sm btn-warning active" id="select-all-label"> 
                                        <input type="checkbox" autocomplete="off" id="select-all"> Delete All
                                      </label>
                                    </div>

                                  </th>
                                  <th width="20%">Sl No.</th>
                                  <th width="20%">POS ID</th>
                                  <th width="20%">Store Name</th>
                                  <th width="20%">Bind With</th>
                                  <th width="20%">Mobile No.</th>
                                  <th width="20%">Topup</th>
                                  <th width="15%">Type</th>
                                  <th width="20%">Created Date</th>
                                </tr>
                              </thead>
                              <tbody style="text-align: center;">
                                <?php if(!empty($ALLDATA) && is_array($ALLDATA)): $i=isset($first)?$first:1; $j=0; foreach($ALLDATA as $key => $ALLDATAINFO ): 
                                if($j%2==0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;
                                $rowRechargeType = !empty($ALLDATAINFO['recharge_type']) ? $ALLDATAINFO['recharge_type'] : (!empty($recharge_type) ? $recharge_type : 'upoint');
                                ?>
                                <tr role="row" class="<?php echo $rowClass; ?>">
                                  <td>
                                    <input type="checkbox" name="delete" class="delete">
                                  </td>
                                  <td>
                                    <?=stripslashes($ALLDATAINFO['sl_no'])?>
                                    <input type="hidden" name="sl_no[]" value="<?=$ALLDATAINFO['sl_no'];?>">
                                  </td>
                                  <td>
                                    <?=stripslashes($ALLDATAINFO['pos_id'])?>
                                    <input type="hidden" name="pos_id[]" value="<?=$ALLDATAINFO['pos_id'];?>">
                                  </td>
                                  <td>
                                    <?=stripslashes($ALLDATAINFO['store_name'])?>
                                    <input type="hidden" name="store_name[]" value="<?=$ALLDATAINFO['store_name'];?>">
                                  </td>
                                  <td>
                                    <?=$ALLDATAINFO['bind_with']?>
                                    <input type="hidden" name="bind_with[]" value="<?=$ALLDATAINFO['bind_with'];?>">
                                  </td>
                                  <td>
                                    <?=$ALLDATAINFO['mobile_no'];?>
                                    <input type="hidden" name="mobile_no[]" value="<?=$ALLDATAINFO['mobile_no'];?>">
                                  </td>
                                  <td>
                                    <?=$ALLDATAINFO['topup'];?>
                                    <input type="hidden" name="topup[]" value="<?=$ALLDATAINFO['topup'];?>">
                                  </td>
                                  <td>
                                    <?php echo ($rowRechargeType === 'recharge_point') ? 'Recharge Point' : 'UPOINT'; ?>
                                    <input type="hidden" name="recharge_type_row[]" value="<?=$rowRechargeType;?>">
                                  </td>
                                  
                                  <td>
                                    <?=$ALLDATAINFO['created_date'];?>
                                    <input type="hidden" name="created_date[]" value="<?=$ALLDATAINFO['created_date'];?>">
                                  </td>
                                </tr>
                                <?php $j++; endforeach; else: ?>
                                <tr>
                                  <td colspan="9" style="text-align:center;">No Data Available In Table</td>
                                </tr>
                                <?php endif; ?>
                              </tbody>
                              </table>
                          </div>
                        </div>
                      </div>
                       
                    </div>
                  </div>
              </div>
              </form>
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

  $(document).ready(function(){
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

     let totalUploadCount = $('.total-upload').data('count');
     let Deletedow        = $('.delete:checked').length;
     if(totalUploadCount != '' && totalUploadCount != ''){
       let finalCount     = totalUploadCount-Deletedow;
       $('.total-upload').attr('data-count', finalCount);
       $('.total-upload').empty().append('Total Data To Uplaod : ', finalCount);
     }
     if(confirm('Do you Want to delete?')){
        if($('.delete:checked').is(':checked')){
          $('.delete:checked').parents('tr').remove();
        }else{
          alert('Please select! Which row you want to delete.');
        }
     }

    });

  });
 

  $('#allwinners').on('submit', function(e) {
    e.preventDefault();
    // Show loading overlay
    showLoadingOverlay();
    var uploadedCount = 0;
    var totalRows = $('tbody tr').length;
    function uploadRow(row) {
      var sl_no       = row.find('input[name^="sl_no"]').val();
      var pos_id      = row.find('input[name^="pos_id"]').val();
      var store_name  = row.find('input[name^="store_name"]').val();
      var bind_with   = row.find('input[name^="bind_with"]').val();
      var mobile_no   = row.find('input[name^="mobile_no"]').val();
      var topup       = row.find('input[name^="topup"]').val();
      var created_date= row.find('input[name^="created_date"]').val();
      var recharge_type = $('#bulk_preview_recharge_type').val();

      $.ajax({
        url: '<?= base_url('recharge/allrecharge/uploadVoucher') ?>',
        type: 'POST',
        data: {
          sl_no      : sl_no,
          pos_id     : pos_id,
          store_name : store_name,
          bind_with  : bind_with,
          mobile_no  : mobile_no,
          topup      : topup,
          created_date: created_date,
          recharge_type: recharge_type,
        },
        success: function(response) {
          uploadedCount++;

          if (uploadedCount === totalRows) {
            // All data uploaded, hide loading overlay
            hideLoadingOverlay();

            // Show success alert and redirect
            alert('All ' + totalRows + ' data uploaded successfully!');
            window.location.href = '<?= base_url('recharge/allrecharge/index') ?>'; // Replace with your redirect URL
          }
        },
        error: function() {
          console.error('Failed to upload data for row ' + (uploadedCount + 1));
          // Hide loading overlay in case of error
          hideLoadingOverlay();
        }
      });
    }
    $('tbody tr').each(function(index) {
      uploadRow($(this));
    });
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