<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script>
$(function(){
   // $("#fromDate").datepicker({dateFormat:'yy-mm-dd',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
   // $("#toDate").datepicker({dateFormat:'yy-mm-dd',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});

   // $("#fromDate1").datepicker({dateFormat:'yy-mm-dd',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
   // $("#toDate1").datepicker({dateFormat:'yy-mm-dd',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
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
                        <li class="breadcrumb-item"><a href="<?=getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
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
                                    <option value="created_at" <?php if($searchField == 'created_at')echo 'selected="selected"'; ?>>Purchase Date</option>
                                    <option value="product_id" <?php if($searchField == 'product_id')echo 'selected="selected"'; ?>>Campaign ID </option>
                                    <option value="product_title" <?php if($searchField == 'product_title')echo 'selected="selected"'; ?>>Campaign Name </option>
                                    <option value="user_email" <?php if($searchField == 'user_email')echo 'selected="selected"'; ?>>Seller Email</option>
                                    <option value="user_phone" <?php if($searchField == 'user_phone')echo 'selected="selected"'; ?>>Seller Mobile</option>
                                    <option value="status" <?php if($searchField == 'status')echo 'selected="selected"'; ?>>Order Status (CL) </option>
                                    <option value="pos_number" <?php if($searchField == 'pos_number')echo 'selected="selected"'; ?>>POS No. </option>
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
                      <div class="row">
                        <div class="col-sm-12">
                          <div class="table-responsive">
                            <table id="simpletable" class="table table-striped table-bordered nowrap dataTable" role="grid" aria-describedby="simpletable_info">
                              <thead style="text-align: center;">
                                <tr role="row">
                                <th width="5%">S.No.</th>
                              
                                <th width="10%">Order ID</th>
                                <th width="10%">Product Name</th>
                                <th width="10%">User Details</th>
                                <th width="10%">Delivery Address</th>
                                <th width="10%">Created Date</th>
                                <th width="10%">Updated Date</th>
                                <th width="10%">Status</th>
                                <th width="10%">Action</th>
                                </tr>
                              </thead>
                              <tbody style="text-align: center;">
                                <?php if($ALLDATA <> ""): $i=$first; $j=0; foreach($ALLDATA as $ALLDATAINFO): 
                                if($j%2==0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;
                                ?>
                                <tr role="row" class="<?php echo $rowClass; ?>">
                                  <td><?=$i++?></td>
                                  <td>
                                     Order ID : <?=stripslashes($ALLDATAINFO['order_id'])?>
                                  </td>

                                  <td>
                                    <?php  echo stripslashes($ALLDATAINFO['product_name']). ' * '.$ALLDATAINFO['product_qty'].'<br>';  ?>
                                  </td>

                                  <td>
                                      Name : <?=stripslashes($ALLDATAINFO['users_name']?$ALLDATAINFO['users_name']:'N/A')?>
                                      <br/>Type : <?=stripslashes($ALLDATAINFO['users_type']?$ALLDATAINFO['users_type']:'N/A')?>
                                     
                                     <?php if($ALLDATAINFO['users_email']): ?>
                                        <br/>Email : <?=stripslashes($ALLDATAINFO['users_email']?$ALLDATAINFO['users_email']:'N/A')?>
                                     <?php endif; ?> 

                                      <?php if($ALLDATAINFO['user_phone']): ?>
                                        <br/>Mobile : <?=stripslashes($ALLDATAINFO['user_phone']?$ALLDATAINFO['user_phone']:'N/A')?>
                                     <?php endif; ?> 
                                  </td>
                                  <td>
                                      <div class="text-center">
                                        <?php if($ALLDATAINFO['pickup_point']): ?>
                                          <p>
                                             <b>Pick Up Point</b> 
                                             <br> <?=stripslashes($ALLDATAINFO['pickup_point'])?>
                                          </p>
                                        <?php else: ?>
                                          <p>
                                             <b>Delivery Address</b> 
                                             <br> <?=stripslashes($ALLDATAINFO['delivery_address'])?>
                                          </p>
                                        <?php endif; ?>
                                          <p> <b>Delivery charges</b> AED <?=stripslashes($ALLDATAINFO['delivery_charge'])?> </p>
                                      </div>
                                  </td>
                                 
                                  <td> <?=date('d M Y h:i:s A', strtotime($ALLDATAINFO['created_at']))?> </td>
                                  <td> <?=$ALLDATAINFO['order_status'];?> </td>
                                  <td> <?=showStatus($ALLDATAINFO['status'])?> </td>

                                  <td>
                                  <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                    <ul class="dropdown-menu" role="menu">
                                      <li>
                                        <a href="<?php echo getCurrentControllerPath('viewdata/'.$ALLDATAINFO['_id']->{'$id'})?>">
                                          <i class="fa fa-expand"></i> View Details</a>
                                      </li>
                                      <li>
                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#changestatus" data-url="<?= getCurrentControllerPath('changestatus/'.$ALLDATAINFO['_id']->{'$id'} .'/P') ?>">
                                           <i class="fas fa-info-circle"></i> Pending
                                        </a>
                                      </li>
                                      <li>
                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#changestatus" data-url="<?= getCurrentControllerPath('changestatus/'.$ALLDATAINFO['_id']->{'$id'} .'/Disp') ?>">
                                           <i class="fas fa-info-circle"></i> Dispatched
                                        </a>
                                      </li>
                                      <li>
                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#changestatus" data-url="<?= getCurrentControllerPath('changestatus/'.$ALLDATAINFO['_id']->{'$id'} .'/Dlvi') ?>">
                                           <i class="fas fa-info-circle"></i> Delivered
                                        </a>
                                      </li>
                                      <li>
                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#changestatus" data-url="<?= getCurrentControllerPath('changestatus/'.$ALLDATAINFO['_id']->{'$id'} .'/C') ?>">
                                           <i class="fas fa-info-circle"></i> Completed
                                        </a>
                                      </li>
                                      <li>
                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#changestatus" data-url="<?= getCurrentControllerPath('changestatus/'.$ALLDATAINFO['_id']->{'$id'} .'/RJ') ?>">
                                           <i class="fas fa-info-circle"></i> Rejected
                                        </a>
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


<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Download Order Reports</h5>
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
              <input type="datetime-local" name="fromDate" id="fromDate1" value="<?php echo $fromDate; ?>" class="form-control form-control-sm" placeholder="From Date">
            </div>
            <div class="col-sm-12 col-md-6">
              <input type="datetime-local" name="toDate" id="toDate1" value="<?php echo $toDate; ?>" class="form-control form-control-sm" placeholder="From Date">
            </div>
            
          </div>

           <div class="row mt-2"  style="margin:0px;">
              <div class="col-sm-12 col-md-6">
                <select name="searchField" id="searchField" class="custom-select custom-select-sm form-control form-control-sm">
                  <option value="">Select Field</option>
                  <option value="order_id" <?php if($searchField == 'order_id')echo 'selected="selected"'; ?>>Ticket ID</option>
                  <option value="created_at" <?php if($searchField == 'created_at')echo 'selected="selected"'; ?>>Purchase Date</option>
                  <option value="product_id" <?php if($searchField == 'product_id')echo 'selected="selected"'; ?>>Campaign ID </option>
                  <option value="product_title" <?php if($searchField == 'product_title')echo 'selected="selected"'; ?>>Campaign Name </option>
                  <option value="user_email" <?php if($searchField == 'user_email')echo 'selected="selected"'; ?>>Seller Email</option>
                  <option value="user_phone" <?php if($searchField == 'user_phone')echo 'selected="selected"'; ?>>Seller Mobile</option>
                  <option value="status" <?php if($searchField == 'status')echo 'selected="selected"'; ?>>Order Status (CL) </option>
                  <option value="pos_number" <?php if($searchField == 'pos_number')echo 'selected="selected"'; ?>>POS No. </option>
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

<div class="modal fade" id="changestatus" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Reason</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="changestatusForm" method="post" autocomplete="off">
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-12 col-md-12">
              <label for="Reason" class="col-form-label">Reason:</label>
              <textarea name="reason" class="form-control form-control-sm" id="Reason"></textarea>
            </div>
          </div>
         
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function confirmAndHide(message) {
  if (confirm(message)) {
    document.getElementById('order-cancel').style.display = 'none';
    return true;
  }
  return false;
}
 
// Dynamically set the form's action attribute based on the data-url attribute of the clicked link
  document.addEventListener('DOMContentLoaded', () => {
    $('#changestatus').on('show.bs.modal', function (event) {
      const button = $(event.relatedTarget); // Button that triggered the modal
      const url = button.data('url'); // Extract data-url value
      const form = $('#changestatusForm'); 
      form.attr('action', url); // Set form action dynamically
    });
  });
</script>