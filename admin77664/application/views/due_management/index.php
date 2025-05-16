<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script>
$(function(){
   $("#fromDate").datepicker({dateFormat:'yy-mm-dd',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
   $("#toDate").datepicker({dateFormat:'yy-mm-dd',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
   $("#fromDate2").datepicker({dateFormat:'yy-mm-dd',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
   $("#toDate2").datepicker({dateFormat:'yy-mm-dd',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Due Management</a></li>
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
                <h5>Manage Due Management</h5>
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
                            <option value="recharge_amt" <?php if($searchField == 'recharge_amt')echo 'selected="selected"'; ?>>Recharge Amount</option>
                            <option value="cash_collected" <?php if($searchField == 'cash_collected')echo 'selected="selected"'; ?>>Cash Collected</option>
                            <option value="advanced_amount" <?php if($searchField == 'advanced_amount')echo 'selected="selected"'; ?>>Advanced Amount</option>
                            <option value="users_name" <?php if($searchField == 'users_name')echo 'selected="selected"'; ?>>User Name</option>
                            <option value="last_name" <?php if($searchField == 'last_name')echo 'selected="selected"'; ?>>Last Name</option>
                            <option value="users_mobile" <?php if($searchField == 'users_mobile')echo 'selected="selected"'; ?>>User Mobile</option>
                            <option value="users_email" <?php if($searchField == 'users_email')echo 'selected="selected"'; ?>>User Email</option>
                            
                            <option value="sender_users_name" <?php if($searchField == 'sender_users_name')echo 'selected="selected"'; ?>>Recharge By (First Name)</option>
                            <option value="sender_last_name" <?php if($searchField == 'sender_last_name')echo 'selected="selected"'; ?>>Recharge By (Last Name)</option>
                            <option value="sender_users_mobile" <?php if($searchField == 'sender_users_mobile')echo 'selected="selected"'; ?>>Recharge By (Mobile Number)</option>
                            <option value="sender_users_email" <?php if($searchField == 'sender_users_email')echo 'selected="selected"'; ?>>Recharge By (Email ID)</option>
                            
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
                      <?php if(!empty($salespersonList)): ?>
                        <div class="row mt-2">
                          <div class="col-sm-3 col-md-3">
                            <select name="salesperson" class="salesperson custom-select-sm form-control form-control-sm">
                                <option value=""> Select Sales Person</option>
                              <?php foreach ($salespersonList as $key => $items):
                                $Saleperson_fullName = $items['users_name'].' '.$items['last_name'];
                                $Saleperson_email = $items['users_email'];
                                $Saleperson_mobile = $items['users_mobile'];

                                  if($Saleperson_email):
                                    $optionTitle = $Saleperson_fullName .' '.' ('.$Saleperson_email.')';
                                  elseif($Saleperson_mobile):
                                    $optionTitle = $Saleperson_fullName .' '.' ('.$Saleperson_mobile.')';
                                  else:
                                     $optionTitle = $Saleperson_fullName;
                                  endif;
                                 ?>
                                <option value="<?=$Saleperson_mobile;?>"  <?php if($Saleperson_mobile == $salesperson): echo "selected"; endif; ?> > <?=$optionTitle;?> </option>
                              <?php endforeach;?>
                          </select>
                          </div>
                        </div>
                        <?php if($salesperson):
                            $DueManagement = $Salesperson_Due;
                          endif; ?>
                        <?php endif; ?>
                      <div class="row">
                        <div class="col-sm-12">
                          <div class="table-responsive">
                            <table id="simpletable" class="table table-striped table-bordered nowrap dataTable" role="grid" aria-describedby="simpletable_info">
                              <thead style="text-align: center;">
                                <tr role="row">
                                  <th style="text-align:center;">S.No</th>
                                  <th style="text-align:center;">Recharge By</th>
                                  <th style="text-align:center;">User's Details</th>
                                  <th style="text-align:center;">Total Recharges</th>
                                  <th style="text-align:center;">Recharge Amount</th>
                                  <th style="text-align:center;">Cash Collected Amount</th>
                                  <th style="text-align:center;">Due Amount</th>
                                  <th style="text-align:center;">Advance Amount</th>
                                  <th style="text-align:center;">Recharge Date</th>
                                  <th style="text-align:center;">Action</th>
                                </tr>
                              </thead>
                              <tbody style="text-align: center;">
                                <?php if($DueManagement): ?>
                                  <?php foreach ($DueManagement as $key => $item): ?>
                                    <tr>
                                      <td><?php echo $key+1;  ?> </td>
                                      <td>
                                        <?= 'Name : ' .$item['sender_users_name'].' ' .$item['sender_last_name'] . "<br>";  ?> 
                                        <?= 'mobile : ' .$item['sender_country_code'].' ' .$item['sender_users_mobile'] . "<br>";  ?> 
                                        <?= 'Email :  ' .$item['sender_users_email'] . "<br>" ;  ?> 
                                      </td>
                                      <td>
                                        <?= 'Name : ' .$item['users_name'].' ' .$item['last_name'] . "<br>";  ?> 
                                        <?= 'mobile : ' .$item['country_code'].' ' .$item['users_mobile'] . "<br>";  ?> 
                                        <?= 'Email :  ' .$item['users_email'] . "<br>" ;  ?> 
                                      </td>

                                      <td><?= $item['count'];  ?> </td>
                                      <td><?= $item['recharge_amt'];  ?> </td>
                                      <td><?= $item['cash_collected'];  ?> </td>
                                      <!-- <td><?= $item['due_amount'];  ?> </td> -->
                                      <td><?=  ($item['advanced_amount'] >0) ?  '0': $item['due_amount'];  ?> </td>

                                      <td>

                                        <?php 

                                        if($item['advanced_amount'] <=0 ):
                                          echo '0';
                                        else:
                                         echo (float)$item['advanced_amount']-(float)$item['due_amount']; 

                                       endif;


                                     ?>
                                     </td>
                                    <td><?= date('d-M-Y' , strtotime($item['created_at']));  ?> </td>

                                    <td>
                                      <div class="btn-group">
                                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                        <ul class="dropdown-menu" role="menu">
                                          <li>
                                            <a href="<?php echo getCurrentControllerPath('view_due_management/'.$item['user_id_to'].'/'.$item['user_id_deb'])?>"><i class="far fa-eye"></i> View Details</a>
                                          </li>
                                        </ul>
                                      </div>
                                    </td>


                                  </tr>

                                <?php endforeach ?>

                              <?php else: ?>
                                <tr><td colspan="8"> Data not found. </td></tr>
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

<!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" data-whatever="@getbootstrap">Open modal for @getbootstrap</button> -->

<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Download Due Management Reports</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?=base_url('due_management/due_management/exportexcel')?>" method="post" autocomplete="off">
      <div class="modal-body">
           <div class="row mt-2"  style="margin:0px;">
              <div class="col-sm-12 col-md-6">
                <select name="searchField" id="searchField" class="custom-select custom-select-sm form-control form-control-sm">
                  <option value="">Select Field</option>
                  <option value="recharge_amt" <?php if($searchField == 'recharge_amt')echo 'selected="selected"'; ?>>Recharge Amount</option>
                  <option value="cash_collected" <?php if($searchField == 'cash_collected')echo 'selected="selected"'; ?>>Cash Collected</option>
                  <option value="advanced_amount" <?php if($searchField == 'advanced_amount')echo 'selected="selected"'; ?>>Advanced Amount</option>
                  <option value="users_name" <?php if($searchField == 'users_name')echo 'selected="selected"'; ?>>User Name</option>
                  <option value="last_name" <?php if($searchField == 'last_name')echo 'selected="selected"'; ?>>Last Name</option>
                  <option value="users_mobile" <?php if($searchField == 'users_mobile')echo 'selected="selected"'; ?>>User Mobile</option>
                  <option value="users_email" <?php if($searchField == 'users_email')echo 'selected="selected"'; ?>>User Email</option>
                  <option value="sender_users_name" <?php if($searchField == 'sender_users_name')echo 'selected="selected"'; ?>>Recharge By (First Name)</option>
                  <option value="sender_last_name" <?php if($searchField == 'sender_last_name')echo 'selected="selected"'; ?>>Recharge By (Last Name)</option>
                  <option value="sender_users_mobile" <?php if($searchField == 'sender_users_mobile')echo 'selected="selected"'; ?>>Recharge By (Mobile Number)</option>
                  <option value="sender_users_email" <?php if($searchField == 'sender_users_email')echo 'selected="selected"'; ?>>Recharge By (Email ID)</option>
                </select>
              </div>
              <div class="col-sm-12 col-md-6">
                <input type="text" name="searchValue" id="searchValue" value="<?php echo $searchValue; ?>" class="form-control form-control-sm" placeholder="Enter Search Text">
              </div>
              <div class="col-sm-12 col-md-12  mt-2">
                <div class="row" >
                  <div class="col-sm-6 col-md-6">
                    <input type="date" name="fromDate" id="fromDate2" autocomplete="off" value="<?php echo $fromDate; ?>" class="form-control form-control-sm" placeholder="From Date">
                  </div>
                  <div class="col-sm-6 col-md-6">
                    <input type="date" name="toDate" id="toDate2" autocomplete="off" value="<?php echo $toDate; ?>" class="form-control form-control-sm" placeholder="To Date">
                  </div>
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
<!-- 
<script>
$('.salesperson').on('change' , function() {
  let Saleperson_eamil =  $(this).val();
  console.log(Saleperson_eamil);

});
</script> -->
