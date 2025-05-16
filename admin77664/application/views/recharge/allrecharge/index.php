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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Recharge</a></li>
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
                <h5>Manage Recharge</h5>
                <a href="<?php echo getCurrentControllerPath('addeditdata'); ?>" class="btn btn-sm btn-primary pull-right" style="margin-left:10px;">Recharge</a>
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
                            <option value="record_type" <?php if($searchField == 'record_type')echo 'selected="selected"'; ?>>Record Type (Credit / Debit) </option>
                            <option value="recharge_by" <?php if($searchField == 'recharge_by')echo 'selected="selected"'; ?>>Recharge By (Email/Number) </option>
                            <option value="recharge_to" <?php if($searchField == 'recharge_to')echo 'selected="selected"'; ?>>Recharge To ( Email/Number ) </option>
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
                              <th width="20%">UPoints</th>
                              <th width="20%">Recharge By</th>
                              <th width="20%">User Type</th>
                              <th width="20%">Recharge To </th>
                              <th width="20%">Remarks</th>
                              <th width="20%">Recharge At</th>
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

                                <?php if($ALLDATAINFO['rechargeDetails']): ?>
                                  <td><?=stripslashes($ALLDATAINFO['sum_arabian_points'])?></td>
                                <?php else: ?>
                                  <td><?=stripslashes($ALLDATAINFO['upoints'])?></td>
                                <?php endif; ?>
                                 <td>

                                    <?php

                                     $created_by =  $ALLDATAINFO['created_by'];

                                    if($created_by == 'ADMIN'):

                                      $recharged_by = $this->common_model->getDataByParticularField('uw_admin','admin_id',(int)$ALLDATAINFO['created_user_id']);

                                      echo "Email : ". $recharged_by['admin_email'].'</br>' . "Name : ". $recharged_by['admin_first_name'].' '.$recharged_by['admin_last_name'];
                                    else:

                                      $recharged_by = $this->common_model->getDataByParticularField('uw_users','users_id',(int)$ALLDATAINFO['created_by']);

                                      echo "POS no : ". $recharged_by['pos_number'].'</br>' ."Name : ". $recharged_by['users_name'].' '.$recharged_by['last_name'].'</br>' ."Mobile : ". $recharged_by['users_mobile'];

                                    endif;
                                    ?>
                                  </td>
                                 
                                <?php if($ALLDATAINFO['record_type'] == 'Debit'){ 
                                  $id = $ALLDATAINFO['user_id_deb'];
                                }elseif ($ALLDATAINFO['record_type'] == 'Credit' && $ALLDATAINFO['created_by'] != 'ADMIN') {
                                  $id = $ALLDATAINFO['user_id_cred'];
                                }elseif ($ALLDATAINFO['record_type'] == 'Credit' && $ALLDATAINFO['created_by'] == 'ADMIN'){
                                  $id = $ALLDATAINFO['user_id_cred'];
                                } ?>
                                <?php
                                $fields = array('users_type','users_email','users_name','users_mobile','bind_person_id','bind_person_name','bind_user_type');
                                $wcon['where']  = array('users_id'=>(int)$id); 
                                $users_type = $this->common_model->getDataByNewQuery($fields,'single','uw_users',$wcon); 
                                ?>
                                <td><?=stripslashes($users_type['users_type'])?></td>
                                <td>
                                  Name : <?=stripslashes($users_type['users_name'])?> <br>
                                  Phone : <?=stripslashes($users_type['users_mobile'])?> <br>
                                  <?php if($users_type['users_email'] != ''): ?>
                                    Email : <?=stripslashes($users_type['users_email'])?>
                                  <?php endif; ?>
                                  <?php if($users_type['bind_person_id'] != ''): ?>
                                    <br> Bind with : <?=stripslashes($users_type['bind_person_name'])?> <br>
                                    User Type : <?=stripslashes($users_type['users_type'])?>
                                  <?php endif; ?>
                                </td>
                                <td><?= @$ALLDATAINFO['remarks'];?></td>
                                <td><?=date('d-F-Y h:i A',strtotime($ALLDATAINFO['created_at']))?></td>
                                <td>
                                  <?php if($ALLDATAINFO['status'] == 'A'): ?>
                                    <label class="badge badge-light-success">Success</label>
                                  <?php elseif($ALLDATAINFO['status'] == 'R'): ?>
                                    <label class="badge badge-light-danger">Reverse</label>
                                  <?php endif; ?>
                                </td>
                                <td>
                                <div class="btn-group">
                                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                  <ul class="dropdown-menu" role="menu">
                                  <!-- <li><a href="<?php echo getCurrentControllerPath('addeditdata/'.$ALLDATAINFO['load_balance_id'])?>"><i class="fas fa-edit"></i> Edit Details</a></li> -->
                                  <?php if($ALLDATAINFO['status'] == 'A'): ?>
                                    <!-- <li><a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['load_balance_id'].'/I')?>"><i class="fas fa-thumbs-down"></i> Inactive</a></li> -->
                                  <?php elseif($ALLDATAINFO['status'] == 'I'): ?>
                                    <!-- <li><a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['load_balance_id'].'/A')?>"><i class="fas fa-thumbs-up"></i> Active</a></li> -->
                                  <?php endif; ?>
                                    <!-- <li><a href="<?php echo getCurrentControllerPath('deletedata/'.$ALLDATAINFO['load_balance_id'])?>" onClick="return confirm('Want to delete!');"><i class="fas fa-trash"></i> Delete</a></li>
                                   </ul> -->
                                   <?php if($ALLDATAINFO['status'] == 'A'): ?>
                                   <li><a href="<?php echo getCurrentControllerPath('reverse/'.$ALLDATAINFO['load_balance_id'])?>" onClick="return confirm('Do you want to reverse this recharge!');"><i class="fas fa-recycle"></i> Reverse</a></li>
                                    <?php endif; ?>
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
        <h5 class="modal-title" id="exampleModalLabel">Download Recharge Reports</h5>
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

          <div class="row mt-2">
            <div class="col-sm-12 col-md-6">
              <select name="searchField" id="searchField" class="custom-select custom-select-sm form-control form-control-sm">
                <option value="">Select Field</option>
                <option value="record_type" <?php if($searchField == 'record_type')echo 'selected="selected"'; ?>>Record Type (Credit / Debit) </option>
                 <option value="recharge_by" <?php if($searchField == 'recharge_by')echo 'selected="selected"'; ?>>Recharge By (Email/Number) </option>
                <option value="recharge_to" <?php if($searchField == 'recharge_to')echo 'selected="selected"'; ?>>Recharge To ( Email/Number ) </option>
              </select>
            </div>
            <div class="col-sm-12 col-md-6">
              <input type="text" name="searchValue" id="searchValue" value="<?php echo $searchValue; ?>" class="form-control form-control-sm" placeholder="Enter Search Text">
            </div>
          </div>

          <div class="row mt-2">
            <div class="col-sm-12 col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="bind_with_admin" id="bind_with_admin">
                <label class="form-check-label" for="cancelled_order">  Bind with admin</label>
              </div>
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