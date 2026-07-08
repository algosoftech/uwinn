<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>

<script>
$(function(){
   $("#fromDate").datetimepicker({dateFormat:'yy-mm-dd H:i',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
   $("#toDate").datetimepicker({dateFormat:'yy-mm-dd H:i',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
   
   $("#fromDate1").datetimepicker({dateFormat:'yy-mm-dd H:i',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
   $("#toDate1").datetimepicker({dateFormat:'yy-mm-dd H:i',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
});
</script>
<style type="text/css">
  .payment-status-red{
    color:red;text-transform: capitalize;
  }
</style>
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
                            <li class="breadcrumb-item"><a href="<?php echo base_url('maindashboard'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Daily Winner List</a></li>
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
                <h5>Manage Daily Winner List</h5>
                  <a href="javaScriptcript:void{0}" class="btn btn-sm btn-primary pull-right" data-toggle="modal" data-target="#exportModal">Export excel</a>
                  <a href="javascript:void(0)" class="btn btn-sm btn-info pull-right mr-2" data-toggle="modal" data-target="#combinedExportModal">Combined Excel (Hourly + Big Winners)</a>
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
                            <option value="order_id" <?php if($searchField == 'order_id')echo 'selected="selected"'; ?>>Ticket ID </option>
                            <option value="code" <?php if($searchField == 'code')echo 'selected="selected"'; ?>>Coupon Code </option>
                            <option value="amount" <?php if($searchField == 'amount')echo 'selected="selected"'; ?>> Amount </option>
                            <option value="products_id" <?php if($searchField == 'products_id')echo 'selected="selected"'; ?>> Campaign ID </option>
                            <option value="redeem_status" <?php if($searchField == 'redeem_status')echo 'selected="selected"'; ?>>Status ( paid, unpaid, settled ) </option>
                            <option value="settler_mobile" <?php if($searchField == 'settler_mobile')echo 'selected="selected"'; ?>>Settler Mobile </option>
                            <option value="seller_mobile" <?php if($searchField == 'seller_mobile')echo 'selected="selected"'; ?>>Seller Mobile </option>
                          </select>
                        </div>
                        <div class="col-sm-3 col-md-3">
                          <input type="text" name="searchValue" id="searchValue" value="<?php echo $searchValue; ?>" class="form-control form-control-sm" placeholder="Enter Search Text">
                        </div>
                        <div class="col-sm-6 col-md-6">
                            <div class="row" >
                              <div class="col-sm-12 col-md-4">
                                <input type="text" name="fromDate" id="fromDate" autocomplete="off" value="<?php echo $fromDate; ?>" class="form-control form-control-sm" placeholder="From Date">
                              </div>
                              <div class="col-sm-12 col-md-4">
                                <input type="text" name="toDate" id="toDate" autocomplete="off" value="<?php echo $toDate; ?>" class="form-control form-control-sm" placeholder="To Date">
                              </div>
                              <div class="col-sm-12 col-md-4">
                                <input type="submit" name="Search" value="Search" class="btn btn-sm btn-primary">
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
                                <th width="20%">Ticket ID</th>
                                <th width="20%">Coupon Code</th>
                                <th width="20%">SETTELD STATUS</th>
                                <th width="20%">Setteld Status</th>
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
                                    <?php if($ALLDATAINFO['redeem_status'] != 'paid' && $ALLDATAINFO['redeem_status'] != 'settled'): ?>
                                          UWINNXXXXXX
                                    <?php else: ?>
                                      <?=stripslashes($ALLDATAINFO['order_id'])?>
                                    <?php endif; ?>
                                  </td>
                                  <td><?=$ALLDATAINFO['code']?></td>
                                  <td>

                                    <?php 

                                     if($ALLDATAINFO['settle_by'] == 'admin'):
                                       $tblName            =  "uw_admin";
                                       $whereCon['where']  =  array('admin_id' => (int)$ALLDATAINFO['seller_id']);
                                       $userDetails        =  $this->common_model->getData('single',$tblName,$whereCon,$shortField,$perPage,$page);
                                       
                                      $userDetails['users_name'] = $userDetails['admin_first_name'];
                                      $userDetails['last_name']  = $userDetails['admin_last_name'];

                                     echo  '<br><b>Settle By :</b> Admin <br>'  ;
                                     else:
                                      $tblName            =  "uw_users";
                                      $whereCon['where']  =  array('users_id' => $ALLDATAINFO['seller_id']);
                                      $userDetails        =  $this->common_model->getData('single',$tblName,$whereCon,$shortField,$perPage,$page);

                                     endif;  
                                    ?>
                                    <?= "<b>Settler Name :</b> ".$userDetails['users_name']. ' '.$userDetails['last_name'];?>
                                     <?="<br><b>Settler Mobile :</b> ".$userDetails['users_mobile'];?>
                                    <?php
                                      if($userDetails['bind_person_name']):
                                        '<br><b>Bind with :</b> '.  $userDetails['bind_person_name']; 
                                      endif;
                                    ?>
                                      <?='<br><b>Settled Amount :</b> '.number_format($ALLDATAINFO['amount'],2)?> 
                                      <!-- <?='<br><b>Settled Date :</b> '.date('d-M-Y H:m:A',strtotime($ALLDATAINFO['modified_at']))?>  -->

                                      <?php if($ALLDATAINFO['redeem_status'] == 'paid'): ?>
                                        <?='<br><b>Settled Date :</b> '.date('d-M-Y H:i:A',strtotime($ALLDATAINFO['modified_at']))?> 
                                      <?php elseif($ALLDATAINFO['redeem_status'] == 'reverse'): ?>
                                        <?='<br><b>Settled Date :</b> '.date('d-M-Y H:i:A',strtotime($ALLDATAINFO['modified_at']))?> 
                                      <?php elseif($ALLDATAINFO['redeem_status'] == 'settled'): ?>
                                        <?='<br><b>Settled Date :</b> '.date('d-M-Y H:i:A',strtotime($ALLDATAINFO['modified_at']))?> 
                                      <?php else: ?>
                                      <?='<br><b>Created Date :</b> '.date('d-M-Y H:i:A',strtotime($ALLDATAINFO['created_at']))?> 
                                      <?php endif; ?>


                                  </td>
                                  <td>
                                  <?php if($ALLDATAINFO['redeem_status'] == 'paid'): ?>
                                    <span  style="color:green;"><?=$ALLDATAINFO['redeem_status'];?></span>
                                  <?php elseif($ALLDATAINFO['redeem_status'] == 'reverse'): ?>
                                    <span  style="color:red;text-transform: capitalize;"><?=$ALLDATAINFO['redeem_status'];?></span>
                                   <?php elseif($ALLDATAINFO['redeem_status'] == 'settled'): ?>
                                    <span  class ="payment-status-red">
                                      <span  style="color:red;text-transform: capitalize;"><?=$ALLDATAINFO['redeem_status'];?></span>
                                    </span>
                                   <?php else: ?>
                                    <span  class ="payment-status-red">Due</span>
                                  <?php endif; ?>
                                  </td>
                                  <td><?=($ALLDATAINFO['status'] == 1 )?showStatus("A"):showStatus("I");?> </td>
                                  <td>
                                      <div class="btn-group">
                                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                        <ul class="dropdown-menu" role="menu">
                                            <?php if($ALLDATAINFO['redeem_status'] == 'paid'): ?>
                                            <li> 
                                                 <a class="btn btn-sm " href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['_id']->{'$id'}.'/settle')?>" onClick="return confirm('DO you want to Settle!');" ><i class="fa fa-undo"></i> Settle</a>
                                            </li>
                                            <li>
                                              <a class="btn btn-sm " href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['_id']->{'$id'}.'/unpaid')?>" onClick="return confirm('Do you want to proceess this request');" ><i class="fa fa-money-bill"></i> Mark As Unpaid</a>
                                            </li>
                                            <?php endif; ?>

                                         <?php if($ALLDATAINFO['redeem_status'] == ''): ?>
                                          <li>
                                           <a class="btn btn-sm " href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['_id']->{'$id'}.'/paid')?>" onClick="return confirm('Do you want to proceess this request');" ><i class="fa fa-money-bill"></i> Pay</a>
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
        <h5 class="modal-title" id="exampleModalLabel">Download Daily Winner Reports</h5>
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
              <option value="order_id" <?php if($searchField == 'order_id')echo 'selected="selected"'; ?>>Ticket ID </option>
              <option value="code" <?php if($searchField == 'code')echo 'selected="selected"'; ?>>Coupon Code </option>
              <option value="amount" <?php if($searchField == 'amount')echo 'selected="selected"'; ?>> Amount </option>
              <option value="products_id" <?php if($searchField == 'products_id')echo 'selected="selected"'; ?>> Campaign ID </option>
              <option value="redeem_status" <?php if($searchField == 'redeem_status')echo 'selected="selected"'; ?>>Status ( paid, unpaid, settled ) </option>
              <option value="settler_mobile" <?php if($searchField == 'settler_mobile')echo 'selected="selected"'; ?>>Settler Mobile </option>
              <option value="seller_mobile" <?php if($searchField == 'seller_mobile')echo 'selected="selected"'; ?>>Seller Mobile </option>
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

<div class="modal fade" id="combinedExportModal" tabindex="-1" role="dialog" aria-labelledby="combinedExportModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="combinedExportModalLabel">Download Combined Report</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?=getUwinControllerPath('alllottoorders', 'combinedexportexcel')?>" method="post" autocomplete="off" id="combinedExportForm">
        <input type="hidden" name="searchField" id="combinedSearchField" value="">
        <input type="hidden" name="searchValue" id="combinedSearchValue" value="">
        <input type="hidden" name="cancelled_order" id="combinedCancelledOrder" value="">
        <div class="modal-body">
          <div class="row">
            <div class="col-sm-12 col-md-6">
              <label class="col-form-label">From:</label>
              <input type="datetime-local" name="fromDate" id="combinedFromDate" value="<?php
                $combinedFrom = !empty($combinedFromDate) ? $combinedFromDate : date('Y-m-d 16:00', strtotime('-1 day'));
                echo date('Y-m-d\TH:i', strtotime(str_replace('T', ' ', $combinedFrom)));
              ?>" class="form-control form-control-sm" placeholder="From Date">
            </div>
            <div class="col-sm-12 col-md-6">
              <label class="col-form-label">To:</label>
              <input type="datetime-local" name="toDate" id="combinedToDate" value="<?php
                $combinedTo = !empty($combinedToDate) ? $combinedToDate : date('Y-m-d 22:00');
                echo date('Y-m-d\TH:i', strtotime(str_replace('T', ' ', $combinedTo)));
              ?>" class="form-control form-control-sm" placeholder="To Date">
            </div>
          </div>
          <p class="text-muted small mt-2 mb-0">Hourly winners and Big Winners will both be exported for the selected date range.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-info">Download Combined Report</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).on('submit', '#combinedExportForm', function() {
  var $listForm = $('#Data_Form');
  if ($listForm.length) {
    $('#combinedSearchField').val($listForm.find('[name="searchField"]').val() || '');
    $('#combinedSearchValue').val($listForm.find('[name="searchValue"]').val() || '');
  }
});
</script>