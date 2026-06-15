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

                  <div class=" pull-right">
                     <input type="submit" class="btn btn-sm btn-primary pull-right" onclick="return confirm('Do you want to uplaod?')" style="margin-left: 5px;" value="Inactive Selected Orders"> 
                     <a href="javascript:coid(0)" class="btn btn-sm btn-primary pull-right" id="delete-selected-orders" style="margin-left: 5px;">Delete Check Orders</a>
                  </div>
                  <div class=" pull-left">
                    <b><label class="btn btn-sm btn-primary pull-left total-upload" data-count="<?=count($ALLDATA);?>" >Total Data To Uplaod : <?=count($ALLDATA);?></label></b>
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
                                        <input type="checkbox" autocomplete="off" id="select-all"> Select All
                                      </label>
                                    </div>
                                  </th>
                                  <th width="20%">ORDER ID</th>
                                  <th width="20%">Coupon Code</th>
                                  <th width="20%">Amount</th>
                                  <th width="20%">Draw Date</th>
                                  <th width="20%">Setted Status</th>
                                  <th width="20%">status</th>
                                </tr>
                              </thead>
                              <tbody style="text-align: center;">
                                <?php if($ALLDATA <> ""): $i=$first; $j=0; foreach($ALLDATA as $key => $ALLDATAINFO ): 
                                if($j%2==0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;
                                ?>
                                <tr role="row" class="<?php echo $rowClass; ?>">
                                  <td>
                                    <input type="checkbox" name="delete" class="delete">
                                    <input type="hidden" name="user_id[]" class="d-none"  value="<?=$ALLDATAINFO['user_id'];?>">
                                  </td>
                                  <td>
                                    <?=$ALLDATAINFO['order_id']?>
                                    <input type="hidden" name="order_id[]" value="<?=$ALLDATAINFO['order_id'];?>">
                                  </td>
                                  <td>
                                    <?=$ALLDATAINFO['coupon_code']?>
                                    <input type="hidden" name="coupon_code[]" value="<?=$ALLDATAINFO['coupon_code'];?>">
                                  </td>
                                  <td>
                                    <?=number_format($ALLDATAINFO['settled_amount'],2)?>
                                    <input type="hidden" name="settled_amount[]" value="<?=$ALLDATAINFO['settled_amount'];?>">
                                  </td>
                                  <td>
                                    <?=$ALLDATAINFO['draw_date']?>
                                    <input type="hidden" name="draw_date[]" value="<?=$ALLDATAINFO['draw_date'];?>">
                                  </td>

                                  <td>
                                    <?=$ALLDATAINFO['setted_status']?>
                                    <input type="hidden" name="setted_status[]" value="<?=$ALLDATAINFO['setted_status'];?>">
                                  </td>
                                   <td>
                                    <?=$ALLDATAINFO['status']?>
                                    <input type="hidden" name="status[]" value="<?=$ALLDATAINFO['status'];?>">
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
      var order_id = row.find('input[name^="order_id"]').val();
      $.ajax({
        url: "<?= base_url('/uwin/voucher/changestatusByorderID') ?>",
        type: 'POST',
        data: {
          order_id: order_id,
          status : '0',
        },
        success: function(response) {
          uploadedCount++;

          if (uploadedCount === totalRows) {
            // All data uploaded, hide loading overlay
            hideLoadingOverlay();

            // Show success alert and redirect
            alert('All ' + totalRows + 'orders inactived successfully.');
            window.location.href = '<?= base_url('uwin/voucher/index') ?>'; // Replace with your redirect URL
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