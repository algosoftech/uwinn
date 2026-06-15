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
                <form method="post" id="allwinners">
                  <div class=" pull-right">
                     <input type="submit" class="btn btn-sm btn-primary pull-right" onclick="return confirm('Do you want to uplaod?')" style="margin-left: 5px;" value="Upload"> 
                  </div>
                  <div class=" pull-left">
                    <label class="btn btn-sm btn-primary pull-left total-upload mr-2" data-count="<?=count($ALLDATA);?>" >Total Data To Uplaod : <?=count($ALLDATA);?></label>
                    <label class="btn btn-sm btn-primary pull-left total-amount" data-amount="<?=$totalAMount;?>" >Total Amount : <?=$totalAMount;?></label> 
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
                                  <!-- <th width="5%" style="text-align: center;">
                                    <div class="btn-group-toggle" data-toggle="buttons">
                                      <label class="btn btn-sm btn-warning active" id="select-all-label"> 
                                        <input type="checkbox" autocomplete="off" id="select-all"> Select All
                                      </label>
                                    </div>

                                  </th> -->
                                  <th width="20%">ORDER ID</th>
                                  <th width="20%">Coupon</th>
                                  <th width="20%">Matching Coupon</th>
                                  <th width="20%">Winner Type</th>
                                  <th width="20%">Winning Amount</th>
                                </tr>
                              </thead>
                              <tbody style="text-align: center;">
                                <?php if($ALLDATA <> ""): $i=$first; $j=0; foreach($ALLDATA as $key => $ALLDATAINFO ): 
                                if($j%2==0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;
                                ?>
                                <tr role="row" class="<?php echo $rowClass; ?>">
                                  <td>
                                    <input type="hidden" name="batch_id[]" class="d-none" value="<?=$ALLDATAINFO['batch_id'] ?? '';?>">
                                    <input type="hidden" name="csv_name[]" class="d-none" value="<?=$ALLDATAINFO['csv_name'] ?? '';?>">
                                    <?=$ALLDATAINFO['order_id']?>
                                    <input type="hidden" name="order_id[]" value="<?=$ALLDATAINFO['order_id'];?>">
                                  </td>
                                  <td>
                                    <?=$ALLDATAINFO['coupon_code']?>
                                    <input type="hidden" name="coupon_code[]" value="<?=$ALLDATAINFO['coupon_code'];?>">
                                  </td>
                                  <td>
                                    <?=$ALLDATAINFO['matching_coupon']?>
                                    <input type="hidden" name="matching_coupon[]" value="<?=$ALLDATAINFO['matching_coupon'];?>">
                                  </td>
                                  <td>
                                    <?=$ALLDATAINFO['winner_type']??'N/A';?>
                                    <input type="hidden" name="winner_type[]" value="<?=$ALLDATAINFO['winner_type'];?>">
                                  </td>
                                  <td>
                                    <?=$ALLDATAINFO['winning_amount']?>
                                    <input type="hidden" name="winning_amount[]" value="<?=$ALLDATAINFO['winning_amount'];?>">
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

  $(document).ready(function(){
      let sum = 0;
      $('input[name="winning_amount[]"]').each(function() {
          sum += parseFloat($(this).val());
          $('.total-amount').empty().append('Total Amount : ',sum);
      });
  });

$('#allwinners').on('submit', function(e) {
    e.preventDefault();
    showLoadingOverlay();
    var chunkSize = 500;
    var orderIds = $('input[name="order_id[]"]').map(function(){ return $(this).val(); }).get();
    var couponCodes = $('input[name="coupon_code[]"]').map(function(){ return $(this).val(); }).get();
    var matchingCoupons = $('input[name="matching_coupon[]"]').map(function(){ return $(this).val(); }).get();
    var winnerTypes = $('input[name="winner_type[]"]').map(function(){ return $(this).val(); }).get();
    var winningAmounts = $('input[name="winning_amount[]"]').map(function(){ return $(this).val(); }).get();
    var batchIds = $('input[name="batch_id[]"]').map(function(){ return $(this).val(); }).get();
    var csvNames = $('input[name="csv_name[]"]').map(function(){ return $(this).val(); }).get();

    var totalRows = orderIds.length;
    var uploadedCount = 0;

    // Upload chunks sequentially to avoid request timeouts.
    function uploadChunk(startIndex) {
      if(startIndex >= totalRows) {
        hideLoadingOverlay();
        alert('All ' + totalRows + ' data uploaded successfully!');
        window.location.href = '<?=getCurrentControllerPath('index');?>';
        return;
      }

      var endIndex = Math.min(startIndex + chunkSize, totalRows);
      var len = endIndex - startIndex;

      var payload = {
        order_id        : orderIds.slice(startIndex, endIndex),
        coupon_code     : couponCodes.slice(startIndex, endIndex),
        matching_coupon : matchingCoupons.slice(startIndex, endIndex),
        winner_type     : winnerTypes.slice(startIndex, endIndex),
        winning_amount  : winningAmounts.slice(startIndex, endIndex),
        batch_id        : batchIds.slice(startIndex, endIndex),
        csv_name        : csvNames.slice(startIndex, endIndex)
      };

      console.log(payload)

      $.ajax({
        url: '<?=getCurrentControllerPath('uploadVoucher');?>',
        type: 'POST',
        data: payload,
        success: function(response) {
          uploadedCount += len;
          updateLoadingOverlay(uploadedCount, totalRows);
          uploadChunk(endIndex);
        },
        error: function() {
          hideLoadingOverlay();
          alert('Error uploading rows ' + (startIndex + 1) + ' to ' + endIndex + '. Please try again.');
        }
      });
    }

    uploadChunk(0);
});

// Loading overlay functions
function showLoadingOverlay() {
    var overlay = $('<div id="loadingOverlay"><span id="uploadPercentage">Uploading</span></div>');
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
        'align-items': 'center',
        'flex-direction': 'column',
        'color': '#fff',
        'font-size': '24px'
    });
    var spinner = $('<div class="spinner"></div>');
    spinner.css({
        'border': '7px solid #f3f3f3',
        'border-top': '7px solid #3498db',
        'border-radius': '50%',
        'width': '100px',
        'height': '100px',
        'margin-bottom': '20px',
        'animation': 'spin 1s linear infinite'
    });
    overlay.append(spinner);
    $('body').append(overlay);
}

function updateLoadingOverlay(uploadedCount, totalRows) {
    var percentage = Math.floor((uploadedCount / totalRows) * 100); // Calculate the upload progress
    // $('#uploadPercentage').text('Uploading');
}

function hideLoadingOverlay() {
    $('#loadingOverlay').remove(); // Remove overlay when done
}
</script>