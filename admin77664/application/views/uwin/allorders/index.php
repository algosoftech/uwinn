<style>
.coupon-container{
    display: inline-flex;
}
.coupon-code-circle {
    border: 1px solid #40ABA8;
    color: #0E4391;
    border-radius: 50%;
    padding: 12px;
    font-weight: 900;
}

</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script>
// $(function(){
//    $("#fromDate").datepicker({dateFormat:'yy-mm-dd',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
//    $("#toDate").datepicker({dateFormat:'yy-mm-dd',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});

//    $("#fromDate1").datepicker({dateFormat:'yy-mm-dd',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
//    $("#toDate1").datepicker({dateFormat:'yy-mm-dd',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
// });
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Orders</a></li>
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
                <h5>Manage Orders</h5>
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
                                    <option value="order_id" <?php if($searchField == 'order_id')echo 'selected="selected"'; ?>>Ticket ID</option>
                                    <option value="ticket" <?php if($searchField == 'ticket')echo 'selected="selected"'; ?>>Coupon Search</option>
                                    <option value="created_at" <?php if($searchField == 'created_at')echo 'selected="selected"'; ?>>Purchase Date</option>
                                    <option value="product_id" <?php if($searchField == 'product_id')echo 'selected="selected"'; ?>>Campaign ID </option>
                                    <option value="product_title" <?php if($searchField == 'product_title')echo 'selected="selected"'; ?>>Campaign Name </option>
                                    <option value="user_email" <?php if($searchField == 'user_email')echo 'selected="selected"'; ?>>Seller Email</option>
                                    <option value="user_phone" <?php if($searchField == 'user_phone')echo 'selected="selected"'; ?>>Seller Mobile</option>
                                    <!-- <option value="product_is_donate" <?php if($searchField == 'product_is_donate')echo 'selected="selected"'; ?>>Product Donate (Y/N)</option> -->
                                    <option value="status" <?php if($searchField == 'status')echo 'selected="selected"'; ?>>Order Status (CL) </option>
                                    <option value="pos_number" <?php if($searchField == 'pos_number')echo 'selected="selected"'; ?>>POS No. </option>
                                    <option value="status" <?php if($searchField == 'status')echo 'selected="selected"'; ?>> Status ( CL = Cancelled , A = Active ) </option>
                                    <option value="order_last_name" <?php if($searchField == 'order_last_name')echo 'selected="selected"'; ?>> Last Name </option>
                                    <option value="available_coupon" <?php if($searchField == 'available_coupon')echo 'selected="selected"'; ?>>Available Coupon (Search By Product ID ) </option>
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
                    </div>
                     

                      <?php if($result): ?>
                      <div class="row">
                        <div class="col-sm-12">
                          <div class="table-responsive">
                            <table id="simpletable" class="table table-striped table-bordered nowrap dataTable" role="grid" aria-describedby="simpletable_info">
                              <thead style="text-align: center;">
                                <tr role="row">
                                  <th width="5%">S.No.</th>
                                  <th width="20%">Available Coupons</th>
                                </tr>
                              </thead>
                              <tbody style="text-align: center;">
                                <?php if($result['unique_coupons'] <> ""): $i=$first; $j=0; foreach($result['unique_coupons'] as $couponList): 
                                if($j%2==0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;
                                ?>
                                <tr role="row" class="<?php echo $rowClass; ?>">
                                  <td><?=$i++?></td>
                                  <td>
                                    <?php foreach ($couponList as $cpnkey => $coupons): ?>
                                      <div class="coupon-container">
                                          <span class="coupon-code-circle"><?=$coupons;?></span> 
                                      </div>
                                      <!-- <?=$coupons;?> -->
                                    <?php  endforeach; ?>

                                  </td>
                                </tr>
                                <?php $j++; endforeach; else: ?>
                                <tr>
                                  <td colspan="2" style="text-align:center;">No Data Available In Table</td>
                                </tr>
                                <?php endif; ?>
                              </tbody>
                              </table>
                          </div>
                        </div>
                      </div>

                    <?php else: ?>
                       <div class="row">
                        <div class="col-sm-12">
                          <div class="table-responsive">
                            <table id="simpletable" class="table table-striped table-bordered nowrap dataTable" role="grid" aria-describedby="simpletable_info">
                              <thead style="text-align: center;">
                                <tr role="row">
                                <th width="5%">S.No.</th>
                                <th width="20%">POS No.</th>
                                <th width="20%">Order Id.</th>
                                <th width="20%">Product</th>
                                <th width="10%">Seller Details</th>
                                <th width="10%">Bind With</th>
                                <th width="10%">Purchase Date</th>
                                <th width="10%">Total Amount</th>
                                <th width="10%">Payment Mode</th>
                                <th width="10%">Payment Status</th>
                                <th width="10%">Status</th>
                                <th width="10%">Action</th>
                                </tr>
                              </thead>
                              <tbody style="text-align: center;">
                                <?php if($ALLDATA <> ""): $i=$first; $j=0; foreach($ALLDATA as $ALLDATAINFO): 
                                if($j%2==0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;

                                 $seller_details = json_decode($ALLDATAINFO['seller_details']);
                                  
                                 $words = explode(' ', $seller_details->Country);
                                 $initials = '';
                                 $countryPrefrx = '';
                                 foreach ($words as $word):
                                  $countryPrefrx .= $word[0];
                                 endforeach;

                                 $Seller_POS = isset($seller_details->posid) ? $countryPrefrx.'_'.$seller_details->posid : (isset($ALLDATAINFO['pos_number']) ? $ALLDATAINFO['pos_number'] : 'N/A');

                                ?>
                                <tr role="row" class="<?php echo $rowClass; ?>">
                                  <td><?=$i++?></td>
                                  <td><?=stripslashes($Seller_POS)?></td>
                                  <td><?=stripslashes($ALLDATAINFO['order_id'])?></td>
                                  <td>
                                    <?php  echo stripslashes($ALLDATAINFO['product_title']). ' * '.$ALLDATAINFO['product_qty'].'<br>';  ?>
                                  </td>
                                  
                                 <!--  <td>
                                    <?php if(!empty($ALLDATAINFO['order_first_name'])  && !empty($ALLDATAINFO['order_first_name'])): ?>
                                      Name : <?=stripslashes($ALLDATAINFO['order_first_name'].' '.$ALLDATAINFO['order_last_name'])?><br>
                                      Mobile : <?=stripslashes($ALLDATAINFO['user_phone'])?><br>
                                      <?php if($ALLDATAINFO['order_users_email']): ?>
                                        Email : <?=stripslashes($ALLDATAINFO['order_users_email'])?>
                                      <?php endif ?>
                                    <?php else: ?>  -- <?php endif;?>
                                  </td> -->

                                  <td>
                                    <?php 

                                      $wcon['where'] = array('users_id'=> $ALLDATAINFO['user_id'] );
                                      $sellersDetails = $this->common_model->getData('single','uw_users',$wcon);

                                      $sellers_Name = isset($seller_details->Name) ? $seller_details->Name : (isset($sellersDetails['users_name']) ? $sellersDetails['users_name'] : 'N/A');

                                      $sellers_Mobile = isset($seller_details->mobile) ? $seller_details->mobile : (isset($sellersDetails['users_mobile']) ? $sellersDetails['users_mobile'] : 'N/A');
                                    ?>
                                      Name : <?=stripslashes($sellers_Name)?>
                                      <br/>Type : <?=stripslashes($sellersDetails['users_type']?$sellersDetails['users_type']:'N/A')?>
                                     
                                     <?php if($sellersDetails['users_email']): ?>
                                        <br/>Email : <?=stripslashes($sellersDetails['users_email']?$sellersDetails['users_email']:'N/A')?>
                                     <?php endif; ?> 

                                      <?php if($sellersDetails['users_mobile']): ?>
                                        <br/>Mobile : <?=stripslashes($sellers_Mobile);?>
                                     <?php endif; ?> 
                                  </td>

                                  <td>
                                    <?php if(!empty($ALLDATAINFO['user_type']) && $ALLDATAINFO['user_type'] == "Users" ): ?>
                                          Name : Admin </br>
                                          Type : Admin
                                      <?php else:  ?>

                                       <?php 
                                       $bindwithName = $seller_details->FoName;
                                      if($sellersDetails['bind_user_type'] == "Admin"):
                                        $wcon['where'] = array('admin_id'=> (int)$sellersDetails['bind_person_id'] );
                                        $bindWITH = $this->common_model->getData('single','uw_admin',$wcon);

                                        $bindWITH['users_name']   =  $bindWITH['admin_first_name'];
                                        $bindWITH['users_type']   =  $sellersDetails['bind_user_type'];
                                        $bindWITH['users_email']  =  $bindWITH['admin_email'];
                                        $bindWITH['users_mobile'] =  $bindWITH['admin_phone'];
                                      else:
                                        $wcon['where'] = array('users_id'=> (int)$sellersDetails['bind_person_id'] );
                                        $bindWITH = $this->common_model->getData('single','uw_users',$wcon);
                                      endif;
                                      ?>

                                      <?php if($bindWITH || $bindwithName): ?>

                                         Name : <?=stripslashes($bindWITH['users_name']?$bindWITH['users_name']:$bindwithName)?>
                                         <?php if($bindWITH['users_type']): ?>
                                          <br/>Type : <?=stripslashes($bindWITH['users_type'])?>
                                        <?php endif;?>

                                        <?php if($bindWITH['users_email']): ?>
                                          <br/>Email : <?=stripslashes($bindWITH['users_email'])?>
                                        <?php endif; ?> 

                                        <?php if($bindWITH['users_mobile']): ?>
                                          <br/>Mobile : <?=stripslashes($bindWITH['users_mobile'])?>
                                        <?php endif; ?> 
                                      <?php else: ?>
                                          --
                                      <?php endif; ?>
                                    <?php endif; ?>
                                  </td>

                                   
 
                                 
                                  <!-- <td><?php echo $ALLDATAINFO['product_is_donate']=='Y'?'Yes':'No'; ?></td> -->
                                  <td><?=date('d M Y h:i:s A', strtotime($ALLDATAINFO['created_at']))?></td>
                                  <td>AED <?=number_format($ALLDATAINFO['total_price'],2)?></td>
                                  

                                 <!--  <td> 
                                  <?php 

                                  if($ALLDATAINFO['availableArabianPoints']):
                                  echo 'AED ' .number_format($ALLDATAINFO['availableArabianPoints'],2);

                                  else:
                                   echo  '-';
                                  endif; ?>

                                 </td>

                                  <td>

                                  <?php 

                                  if($ALLDATAINFO['end_balance']):
                                  echo 'AED' .number_format($ALLDATAINFO['end_balance'],2);

                                  else:
                                   echo  '-';
                                  endif; ?>

                                  </td> -->

                                  <td>
                                      <?php  echo $ALLDATAINFO['payment_mode']; ?>
                                  </td>
                                   
                                  <td><?php  echo $ALLDATAINFO['order_status']; ?></td>
                                  <td style="text-align: right;"><?=showStatus($ALLDATAINFO['status'])?></td>
                                  <td>
                                  <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                    <ul class="dropdown-menu" role="menu">
                                    <li><a href="<?php echo getCurrentControllerPath('addeditdata/'.$ALLDATAINFO['order_id'])?>"><i class="far fa-eye"></i> View Details</a></li>
                                    <?php if($ALLDATAINFO['status'] != "CL"):  ?>
                                      <li><a href="<?php echo getCurrentControllerPath('cancelationorder/'.$ALLDATAINFO['order_id'])?>" onClick='return confirm("<?=$drawDates;?> Do you want to Cancel!");' ><i class="fa fa-times-circle"></i>Order Cancelation</a></li>
                                    <?php endif;  ?>
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
                    <?php endif; ?>
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

<!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" data-whatever="@getbootstrap">Open modal for @getbootstrap</button> -->

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
              <option value="order_id" <?php if($searchField == 'order_id')echo 'selected="selected"'; ?>>Ticket ID</option>
              <option value="ticket" <?php if($searchField == 'ticket')echo 'selected="selected"'; ?>>Coupon Search</option>
              <option value="created_at" <?php if($searchField == 'created_at')echo 'selected="selected"'; ?>>Purchase Date</option>
              <option value="product_id" <?php if($searchField == 'product_id')echo 'selected="selected"'; ?>>Campaign ID </option>
              <option value="product_title" <?php if($searchField == 'product_title')echo 'selected="selected"'; ?>>Campaign Name </option>
              <option value="user_email" <?php if($searchField == 'user_email')echo 'selected="selected"'; ?>>Seller Email</option>
              <option value="user_phone" <?php if($searchField == 'user_phone')echo 'selected="selected"'; ?>>Seller Mobile</option>
              <option value="status" <?php if($searchField == 'status')echo 'selected="selected"'; ?>>Order Status (CL) </option>
              <option value="pos_number" <?php if($searchField == 'pos_number')echo 'selected="selected"'; ?>>POS No. </option>
              <option value="available_coupon" <?php if($searchField == 'available_coupon')echo 'selected="selected"'; ?>>Available Coupon (Search By Product ID ) </option>
              <option value="order_last_name" <?php if($searchField == 'order_last_name')echo 'selected="selected"'; ?>>Last Name</option>

              
            </select>
          </div>
          <div class="col-sm-12 col-md-6">
            <input type="text" name="searchValue" id="searchValue" value="<?php echo $searchValue; ?>" class="form-control form-control-sm" placeholder="Enter Search Text">
          </div>
        </div>
        <div class="row mt-2">
        <?php /* <div class="col-sm-12 col-md-6">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="draw_time_one" id="draw_time_one">
              <label class="form-check-label" for="draw_time_one">  10:00 PM Draw </label>
            </div>
          </div>
          
          <div class="col-sm-12 col-md-6">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="draw_time_two" id="draw_time_two">
              <label class="form-check-label" for="draw_time_two">  11:30 PM Draw </label>
            </div>
          </div> 
          */ ?>
          <div class="col-sm-12 col-md-6">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="cancelled_order" id="cancelled_order">
              <label class="form-check-label" for="cancelled_order">  Cancelled Orders </label>
            </div>
          </div>
        </div>
        
        <div class="row mt-2 draw-time-group">
          <?php if($ALLPRODUCT <> ""): foreach($ALLPRODUCT as $ALLPRODUCTINFO): ?>
            <div class="col-sm-12 col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="productIds[]" data-draw-time="<?=$ALLPRODUCTINFO['draw_time']?>" value="<?=$ALLPRODUCTINFO['products_id']?>" id="<?=$ALLPRODUCTINFO['products_id']?>">
                <label class="form-check-label" for="<?=$ALLPRODUCTINFO['products_id']?>"><?=$ALLPRODUCTINFO['title']?> ( <?=$ALLPRODUCTINFO['draw_time']?> ) </label>
              </div>
            </div>
          <?php endforeach; endif; ?>
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
  
<?php /* $('#draw_time_one').on('change', function () {
    if (this.checked) {
      $('#draw_time_two').prop('checked', false);

      let fromDate1 = $('#fromDate1').val(); // e.g., "2025-10-07T14:20"
      let toDate1   = $('#toDate1').val();   // e.g., "2025-10-08T09:15"
      // Extract only the date part (before 'T')
      let fromDateOnly = fromDate1.split('T')[0];
      let toDateOnly   = toDate1.split('T')[0];
      // Add fixed time "22:31"
      let finalFromDate = fromDateOnly + 'T22:01';
      let finalToDate   = toDateOnly + 'T22:00';

      // Set back to input fields
      $('#fromDate1').val(finalFromDate);
      $('#toDate1').val(finalToDate);

    }  
});

$('#draw_time_two').on('change', function () {
    if (this.checked) {
      $('#draw_time_one').prop('checked', false);
  
      let fromDate1 = $('#fromDate1').val(); // e.g., "2025-10-07T14:20"
      let toDate1   = $('#toDate1').val();   // e.g., "2025-10-08T09:15"
      // Extract only the date part (before 'T')
      let fromDateOnly = fromDate1.split('T')[0];
      let toDateOnly   = toDate1.split('T')[0];
      // Add fixed time "22:31"
      let finalFromDate = fromDateOnly + 'T23:31';
      let finalToDate   = toDateOnly + 'T23:30';

      // Set back to input fields
      $('#fromDate1').val(finalFromDate);
      $('#toDate1').val(finalToDate);

    }else{
        let fromDate1 = "<?= date('Y-m-d', strtotime('-1 day')) . 'T22:01'; ?>" ;
        let toDate1   = "<?= date('Y-m-d') . 'T22:00'; ?>";
        $('#fromDate1').val(fromDate1);
        $('#toDate1').val(toDate1);
    }
});
*/ ?>
// Ensure only one checkbox with the same data-draw-time can be selected
$(document).on('change', 'input[name="productIds[]"]', function() {

  let drawTime  = $(this).attr('data-draw-time');  // get the draw-time of the current checkbox

  let fromDate1 = new Date("<?=date('Y-m-d', strtotime('-1 day')) ?>T" + drawTime);
  fromDate1.setMinutes(fromDate1.getMinutes() + 1);
  fromDate1 = fromDate1.toLocaleString('sv-SE').replace(' ', 'T'); // keeps local time in YYYY-MM-DDTHH:MM:SS
  let toDate1   = "<?= date('Y-m-d') ?>T" + drawTime;

  $('#fromDate1').val(fromDate1);
  $('#toDate1').val(toDate1); 

  // disable other data-draw-time in group
  // Disable all product checkboxes that are not in the selected draw-time group
  $('input[name="productIds[]"]').each(function() {
    if ($(this).attr('data-draw-time') !== drawTime) {
      $(this).prop('disabled', true);
    } else {
      $(this).prop('disabled', false);
    }
  });

  // If no checkboxes are checked, re-enable all
  if ($('input[name="productIds[]"]:checked').length === 0) {
    $('input[name="productIds[]"]').prop('disabled', false);

    let fromDate1 = "<?= date('Y-m-d', strtotime('-1 day')) . 'T22:01'; ?>" ;
    let toDate1   = "<?= date('Y-m-d') . 'T22:00'; ?>";
    $('#fromDate1').val(fromDate1);
    $('#toDate1').val(toDate1);
  }
  
  <?php /*
  console.log($(this).is(':checked'));
  if ($(this).is(':checked')) {
      var currentDrawTime = $(this).data('draw-time');
      // Uncheck all other checkboxes with the same data-draw-time
      $('input[name="productIds[]"]').each(function() {
          if ($(this).attr('id') !== $(this).attr('id') && $(this).data('draw-time') === currentDrawTime) {
              $(this).prop('checked', true);
          }
      });
      // Actually, let's use a better approach - uncheck all with same draw-time except current
      $('input[name="productIds[]"][data-draw-time="' + currentDrawTime + '"]').not(this).prop('checked', false);
  }
  */ ?>
});
</script>