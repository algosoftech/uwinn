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
                <li class="breadcrumb-item"><a href="javascript:void(0);">Withdraw Request</a></li>
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
              <h5>Manage Withdraw Request</h5>
              <a href="javaScriptcript:void{0}" class="btn btn-sm btn-primary pull-right" data-toggle="modal" data-target="#exportModal">Export excel</a>
              <!-- <a href="<?php echo getCurrentControllerPath('addeditdata'); ?>" class="btn btn-sm btn-primary pull-right">Add Campaign</a> -->
            </div>
            <div class="card-body">
              <form id="Data_Form" name="Data_Form" method="get" action="<?php echo $forAction; ?>">
                <div class="dt-responsive table-responsive">
                  <div id="simpletable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                    
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
                              <option value="request_id" <?php if($searchField == 'request_id')echo 'selected="selected"'; ?>>Request ID</option>
                              <option value="type" <?php if($searchField == 'type')echo 'selected="selected"'; ?>>Request Type</option>
                              <option value="status" <?php if($searchField == 'status')echo 'selected="selected"'; ?>>Status</option>
                              <option value="amount" <?php if($searchField == 'amount')echo 'selected="selected"'; ?>>Amount</option>
                              <option value="orderIds" <?php if($searchField == 'orderIds')echo 'selected="selected"'; ?>>Order id</option>
                              <option value="user_mobile" <?php if($searchField == 'user_mobile')echo 'selected="selected"'; ?>>Mobile No.</option>
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
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-sm-12">
                        <div class="table-responsive">
                          <table id="simpletable" class="table table-striped table-bordered nowrap dataTable" role="grid" aria-describedby="simpletable_info">
                            <thead style="text-align: center;">
                              <tr role="row">
                                <th width="5%" style="text-align: center;">S.No.</th>
                                <th width="15%">Request ID</th>
                                <th width="20%">Request Type</th>
                                <th width="20%">Amount</th>
                                <th width="20%">Account Details</th>
                                <th width="20%">Winning Details</th>
                                <th width="20%">User's Details</th>
                                <th width="25%">Request Date</th>
                                <th width="10%" style="text-align: right;">Status</th>
                                <th width="10%">Action</th>
                              </tr>
                            </thead>
                            <tbody style="text-align: center;">
                              <?php if($ALLDATA <> ""): $i=$first; $j=0; foreach($ALLDATA as $ALLDATAINFO): 
                                if($j%2==0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;
                                ?>
                                <tr role="row" class="<?php echo $rowClass; ?>">
                                  <td style="text-align: center;"><?=$i++?></td>
                                  <td><?=substr($ALLDATAINFO['request_id'],8)?></td>
                                  <td><?=stripslashes($ALLDATAINFO['type'])?></td>
                                  <td>AED <?=number_format($ALLDATAINFO['amount'],2)?></td>
                                  <?php if($ALLDATAINFO['type'] === 'Bank' || $ALLDATAINFO['type'] === 'Bank Transfer'): ?>
                                    <td>
                                      <span>Name : <?=stripslashes($ALLDATAINFO['account_holder_name'])?></span> <br/>
                                      <span class='show-account'>
                                        A/C No :
                                        <span class='encrypted'>**********</span> 
                                        <span class='decrypted' style='display:none;'><?=base64_decode($ALLDATAINFO['account_no'])?></span> 
                                        <i class='fa fa-eye'></i> 
                                      </span> <br/>

                                      <?php if($ALLDATAINFO['bank_name']): ?>
                                          <span>Bank Name : <?=$ALLDATAINFO['bank_name'];?></span> <br/>
                                      <?php endif; ?>

                                      <?php if($ALLDATAINFO['ifsc_code']): ?>
                                          <span>IFSC : <?=$ALLDATAINFO['ifsc_code']?></span> <br/>
                                      <?php endif; ?>

                                      <?php if($ALLDATAINFO['swiftBicCode']): ?>
                                          <span>Swift/Bic Code : <?=base64_decode($ALLDATAINFO['swiftBicCode'])?></span> <br/>
                                      <?php endif; ?>

                                      <?php if($ALLDATAINFO['iben']): ?>
                                          <span>IBEN : <?=$ALLDATAINFO['iben']?></span> <br/>
                                      <?php endif; ?>

                                    </td>
                                  <?php elseif($ALLDATAINFO['type'] === 'Cripto' || $ALLDATAINFO['type'] === 'Cryto Transfer') : ?>
                                    <td><span>Cripto ID : <?=base64_decode($ALLDATAINFO['cryto_account_id']);?></span></td>
                                  <?php else: ?>
                                    <td>
                                      <span>Name : <?=stripslashes($ALLDATAINFO['full_name'])?></span> <br/>
                                      <span>Phone : <?=stripslashes($ALLDATAINFO['phone'])?></span> <br/>
                                      <span>City : <?=stripslashes($ALLDATAINFO['city'])?></span> <br/>
                                    </td>
                                  <?php endif; ?>

                                  <td>

                                     <?php if(!empty($ALLDATAINFO['orderData']->order_id)): 
                                        $loopCount = count($ALLDATAINFO['orderData']->order_id);
                                        for ($i=0; $i<$loopCount; $i++): ?>
                                            <?=$ALLDATAINFO['orderData']->order_id[$i];?>  : <?=$ALLDATAINFO['orderData']->total_amount[$i];?> <br>
                                         <?php endfor; ?>
                                            <strong>Total:</strong> : <?=$ALLDATAINFO['amount'];?>
                                    <?php endif; ?>

                                  </td>

                                  <td>
                                    <span>Name  : <?=stripslashes($ALLDATAINFO['users_name'].' '.$ALLDATAINFO['last_name'] )?></span><br/>
                                    <span>Phone : <?=stripslashes($ALLDATAINFO['user_mobile'])?></span> <br/>
                                    <span>Email : <?=stripslashes($ALLDATAINFO['user_email'])?></span> <br/>
                                  </td>


                                  <td><?=date('d-M-y h:i A',strtotime($ALLDATAINFO['created_at']))?></td>
                                  <td style="text-align: right;"><?=showStatus($ALLDATAINFO['status'])?></td>
                                  <td>
                                    <div class="btn-group">
                                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=$ALLDATAINFO['status'] == 'P'?'Action':'Details'?></button>
                                      <ul class="dropdown-menu" role="menu">

                                         <?php /* <li><a href="<?php echo getCurrentControllerPath('addeditdata/'.$ALLDATAINFO['_id']->{'$id'})?>"><i class="fas fa-eye"></i> View</a></li> */?>


                                        <?php if($ALLDATAINFO['status'] == 'C'): ?>
                                          <li><span>Date : <?=date('d-M-y h:i A',strtotime($ALLDATAINFO['updated_at']))?></span></li>
                                        <?php elseif($ALLDATAINFO['status'] == 'P'): ?>
                                          <li>
                                            <a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['request_id'].'/C')?>" onClick="return confirm(' Do you want to continue?');">
                                              <i class="fas fa-thumbs-up"></i> Mark as Completed
                                            </a>
                                          </li>

                                          <li><a href="javaScript:void(0)" data-toggle="modal" data-target="#rejectModal" data-req_id='<?=$ALLDATAINFO['request_id']?>'><i class="fas fa-times"></i> Reject</a></li>
                                          
                                        <?php elseif($ALLDATAINFO['status'] == 'R'): ?>
                                          <li>
                                            <span>Reject At : <?=date('d-M-y h:i A',strtotime($ALLDATAINFO['updated_at']))?></span><br/>
                                            <span>Reason : <?=$ALLDATAINFO['reason'];?></span>
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
  <!-- Export Model Start -->
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
              <input type="text" name="fromDate" id="fromDate1" value="<?php echo $fromDate; ?>" class="form-control form-control-sm" placeholder="From Date">
            </div>
            <div class="col-sm-12 col-md-6">
              <input type="text" name="toDate" id="toDate1" value="<?php echo $toDate; ?>" class="form-control form-control-sm" placeholder="From Date">
            </div>
          </div>
          <div class="row mt-2"  style="margin:0px;">
            <div class="col-sm-12 col-md-6">
                <select name="searchField" id="searchField" class="custom-select custom-select-sm form-control form-control-sm">
                  <option value="">Select Field</option>
                  <option value="request_id" <?php if($searchField == 'request_id')echo 'selected="selected"'; ?>>Request ID</option>
                  <option value="type" <?php if($searchField == 'type')echo 'selected="selected"'; ?>>Request Type</option>
                  <option value="status" <?php if($searchField == 'status')echo 'selected="selected"'; ?>>Status</option>
                  <option value="amount" <?php if($searchField == 'amount')echo 'selected="selected"'; ?>>Amount</option>
                  <option value="orderIds" <?php if($searchField == 'orderIds')echo 'selected="selected"'; ?>>Order id</option>
                  <option value="user_mobile" <?php if($searchField == 'user_mobile')echo 'selected="selected"'; ?>>Mobile No.</option>
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
  <!-- Export Model End -->

  <!-- Reject Model Start -->
  <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Are you sure! You want to reject?</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="<?=getCurrentControllerPath('rejectrequest');?>" method="post" autocomplete="off">
          <input type="text" name="req_id" id="req_id" class="form-control form-control-sm">
          <div class="modal-body">
            <div class="row">
              <div class="col-sm-12 col-md-12">
                <label for="recipient-name" class="col-form-label">Reason of Rejection:</label>
              </div>
              <div class="col-sm-12 col-md-12">
                <textarea name="reason" id="reason" class="form-control form-control-sm" placeholder="Enter reason of rejection"></textarea>
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
  <!-- Reject Model End -->

  <script>
    $(document).ready(function(){
      $('.show-account').click(function(){
        $(this).children('.encrypted, .decrypted').toggle();
      });
    });
  </script>

  <script>
    $(document).ready(function() {
      $('#rejectModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var req_id = button.data('req_id'); // Extract info from data-* attributes
        $('#req_id').val(req_id);
      });
    });
  </script>