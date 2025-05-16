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
                          <li class="breadcrumb-item"><a href="javascript:void(0);">All Raffle Winner List</a></li>
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
            <form  id="allwinners">
                <div class="card-header">
                  <h5>Manage winner preview</h5>
                    <div class=" pull-right">
                       <input type="submit" class="btn btn-sm btn-primary pull-right" onclick="return confirm('Do you want to uplaod?')" style="margin-left: 5px;" value="Upload"> 
                       <a href="javascript:coid(0)" class="btn btn-sm btn-primary pull-right" id="delete-selected-orders" style="margin-left: 5px;">Delete Check Orders</a>
                    </div>
                    <div class=" pull-left">
                      <b><label class="btn btn-sm btn-primary pull-left total-upload mr-2" data-count="<?=count($ALLDATA);?>" >Total Data To Uplaod : <?=count($ALLDATA);?></label></b>
                      <?php 
                       $totalAMount = 0; 
                        foreach($ALLDATA as $key =>  $itemD): 
                          $AmountArray = explode(' ', $itemD['amount']);
                          if(is_numeric($AmountArray[0])):
                            $totalAMount += $itemD['amount']; 
                          endif;
                        endforeach; 
                      ?>

                      <b><label class="btn btn-sm btn-primary pull-left total-amount" data-amount="<?=$totalAMount;?>" >Total Amount : <?=$totalAMount;?></label></b>
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
                                    <th width="20%">Winner Type</th>
                                    <th width="20%">Winning Amount</th>
                                    <th width="20%">Seller First Name</th>
                                    <th width="20%">Seller Last Name</th>
                                    <th width="20%">Seller Mobile</th>
                                    <th width="20%">Store Name</th>
                                    <th width="20%">Pos Number</th>
                                    <th width="20%">Coupons</th>
                                  </tr>
                                </thead>
                                <tbody style="text-align: center;">
                                  <?php if($ALLDATA <> ""): $i=$first; $j=0; foreach($ALLDATA as $key => $ALLDATAINFO ): 
                                  if($j%2==0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;
                                  ?>
                                  <tr role="row" class="<?php echo $rowClass; ?>">
                                    <td>
                                      <input type="checkbox" name="delete" class="delete">
                                      <input type="hidden" name="batch_id[]" class="d-none" value="<?=$ALLDATAINFO['batch_id'];?>">
                                      <input type="hidden" name="csv_name[]" class="d-none" value="<?=$ALLDATAINFO['csv_name'];?>">
                                    </td>
                                    <td>
                                      <?=$ALLDATAINFO['order_id']?>
                                      <input type="hidden" name="order_id[]" value="<?=$ALLDATAINFO['order_id'];?>">
                                    </td>
                                    <td>
                                      <?=$ALLDATAINFO['winner_type'];?>
                                      <input type="hidden" name="winner_type[]" value="<?=$ALLDATAINFO['winner_type'];?>">
                                    </td>
                                    <td>
                                      <?=$ALLDATAINFO['amount'];?>
                                      <input type="hidden" name="amount[]" value="<?=$ALLDATAINFO['amount'];?>">
                                      <?php 
                                        $AmountArray = explode(' ', $ALLDATAINFO['amount']);
                                        $totalAMount  = is_numeric($AmountArray[0])?$AmountArray[0]:0;  
                                      ?>
                                        <input type="hidden" name="amount_in_number[]" value="<?=$totalAMount;?>">
                                    </td>
                                    <td>
                                      <?=stripslashes($ALLDATAINFO['seller_first_name'])?>
                                      <input type="hidden" name="seller_first_name[]" value="<?=$ALLDATAINFO['seller_first_name'];?>">
                                    </td>
                                    <td>
                                      <?=stripslashes($ALLDATAINFO['seller_last_name'])?>
                                      <input type="hidden" name="seller_last_name[]" value="<?=$ALLDATAINFO['seller_last_name'];?>">
                                    </td>
                                    <td>
                                      <?=stripslashes($ALLDATAINFO['seller_mobile'])?>
                                      <input type="hidden" name="seller_mobile[]" value="<?=$ALLDATAINFO['seller_mobile'];?>">
                                    </td>

                                    <td>
                                      <?=$ALLDATAINFO['store_name'];?>
                                      <input type="hidden" name="store_name[]" value="<?=$ALLDATAINFO['store_name'];?>">
                                    </td>

                                    <td>
                                      <?=$ALLDATAINFO['pos_number'];?>
                                      <input type="hidden" name="pos_number[]" value="<?=$ALLDATAINFO['pos_number'];?>">
                                    </td>

                                    <td>
                                      <?=$ALLDATAINFO['coupons'];?>
                                      <input type="hidden" name="coupons[]" value="<?=$ALLDATAINFO['coupons'];?>">
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

      // $('.amount').each(function() {
      //       totalAmount += parseFloat($(this).val());
      // });


     if(totalUploadCount != '' && totalUploadCount != ''){
       let finalCount     = totalUploadCount-Deletedow;
       $('.total-upload').attr('data-count', finalCount);
       $('.total-upload').empty().append('Total Data To Uplaod : ', finalCount);

       totalAmount()


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

  function totalAmount() {
   $(document).ready(function () {
      let sum = 0;
      // Iterate over each input field with name "amount[]"
      $('input[name="amount[]"]').each(function () {
         let value = $(this).val().trim();
         // Split the input value by space
         let amountArray = value.split(' ');
         // Check if the first part is numeric
         if (!isNaN(parseFloat(amountArray[0]))) {
            sum += parseFloat(amountArray[0]); // Add the numeric value to the sum
         }
      });

      // Update the total amount in the HTML
      $('.total-amount').empty().append('Total Amount: ' + sum.toFixed(2));
   });
}

$('#allwinners').on('submit', function(e) {
    e.preventDefault();
    showLoadingOverlay();
    var uploadedCount = 0;
    var totalRows = $('tbody tr').length;
    var batchSize = 100;

    function uploadBatch(startIndex) {
        var endIndex = Math.min(startIndex + batchSize, totalRows);
        var batchData = [];

        for (var i = startIndex; i < endIndex; i++) {
            var row = $('tbody tr').eq(i);
            var batch_id    = row.find('input[name^="batch_id"]').val();
            var csv_name    = row.find('input[name^="csv_name"]').val();
            var order_id    = row.find('input[name^="order_id"]').val();
            var winner_type = row.find('input[name^="winner_type"]').val();
            var amount      = row.find('input[name^="amount"]').val();
            var amount_in_number  = row.find('input[name^="amount_in_number"]').val();
            var seller_first_name = row.find('input[name^="seller_first_name"]').val();
            var seller_last_name  = row.find('input[name^="seller_last_name"]').val();
            var seller_mobile     = row.find('input[name^="seller_mobile"]').val();
            var coupons = row.find('input[name^="coupons"]').val();
            var store_name = row.find('input[name^="store_name"]').val();
            var pos_number = row.find('input[name^="pos_number"]').val();

            batchData.push({
                batch_id: batch_id,
                csv_name: csv_name,
                order_id: order_id,
                winner_type: winner_type,
                amount: amount,
                amount_in_number: amount_in_number,
                seller_first_name: seller_first_name,
                seller_last_name: seller_last_name,
                seller_mobile: seller_mobile,
                coupons: coupons,
                store_name: store_name,
                pos_number: pos_number
            });
        }

        $.ajax({
            url: '<?= base_url('uwinn_raffle/allrafflewiner/uploadVoucher') ?>',
            type: 'POST',
            data: { batch: batchData },
            success: function(response) {
                uploadedCount += batchData.length;
                updateLoadingOverlay(uploadedCount, totalRows);

                if (endIndex < totalRows) {
                    uploadBatch(endIndex);
                } else {
                    hideLoadingOverlay();
                    alert('All ' + totalRows + ' data uploaded successfully!');
                    window.location.href = '<?= base_url('uwinn_raffle/allrafflewiner/index') ?>'; // Replace with your redirect URL
                }
            },
            error: function() {
                console.error('Failed to upload data for batch starting at row ' + (startIndex + 1));
                hideLoadingOverlay();
            }
        });
    }

    uploadBatch(0);
});

function showLoadingOverlay() {
    var overlay = $('<div id="loadingOverlay"><span id="uploadPercentage">0%</span></div>');
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
    // Append overlay to body
    $('body').append(overlay);
}

function updateLoadingOverlay(uploadedCount, totalRows) {
    var percentage = Math.floor((uploadedCount / totalRows) * 100);
    $('#uploadPercentage').text(percentage + '%');
}

function hideLoadingOverlay() {
  // Remove overlay element
  $('#loadingOverlay').remove();
}

</script>