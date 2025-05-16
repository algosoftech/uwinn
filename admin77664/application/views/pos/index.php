<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script>
$(function(){
   $("#fromDate").datepicker({dateFormat:'yy-mm-dd',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
   $("#toDate").datepicker({dateFormat:'yy-mm-dd',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
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
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?=base_url('/') ?>"><i class="feather icon-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Pos Devices</a></li>
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
                <h5>Manage Pos Devices</h5>
                
              </div>
              <div class="card-body">
                <form id="Data_Form" name="Data_Form" method="get" action="<?php echo $forAction; ?>">
                  <div class="dt-responsive table-responsive">
                    <div id="simpletable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                     <div class="row">
                        <div class="col-sm-12 col-md-6">
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
                        <div class="col-sm-12 col-md-6">
                          <div class="row" style="margin:0px;">
                            <div class="col-sm-12 col-md-6">
                              <select name="searchField" id="searchField" class="custom-select custom-select-sm form-control form-control-sm">
                                <option value="">Select Field</option>
                                <option value="area" <?php if($searchField == 'area')echo 'selected="selected"'; ?>>Area</option>
                                <option value="users_email" <?php if($searchField == 'users_email')echo 'selected="selected"'; ?>>Email</option>
                                <option value="users_mobile" <?php if($searchField == 'users_mobile')echo 'selected="selected"'; ?>>Mobile</option>
                                <option value="users_type" <?php if($searchField == 'users_type')echo 'selected="selected"'; ?>>User Type</option>
                                <option value="users_name" <?php if($searchField == 'users_name')echo 'selected="selected"'; ?>>First Name</option>
                               <option value="bind_person_name" <?php if($searchField == 'bind_person_name')echo 'selected="selected"'; ?>>Bind With</option>
                                <option value="last_name" <?php if($searchField == 'last_name')echo 'selected="selected"'; ?>>Last Name</option>
                                <option value="pos_device_id" <?php if($searchField == 'pos_device_id')echo 'selected="selected"'; ?>>POS Device ID </option>
                                <option value="users_mobile" <?php if($searchField == 'users_mobile')echo 'selected="selected"'; ?>>Users Mobile</option>
                                <option value="app_version" <?php if($searchField == 'app_version')echo 'selected="selected"'; ?>>App Version</option>
                                <option value="pos_number" <?php if($searchField == 'pos_number')echo 'selected="selected"'; ?>>POS No.</option>
                              </select>
                            </div>
                            <div class="col-sm-12 col-md-6">
                              <input type="text" name="searchValue" id="searchValue" value="<?php echo $searchValue; ?>" class="form-control form-control-sm" placeholder="Enter Search Text">
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
                                <th width="15%">POS NO.</th>
                                <th width="15%">Users ID</th>
                                <th width="20%">User Type</th>
                                <th width="20%">First Name</th>
                                <th width="20%">Last Name</th>
                                <th width="25%">Email</th>
                                <th width="25%">Mobile</th>
                                <th width="25%">Available Points</th>
                                <th width="25%">Area</th>
                                <th width="25%">Store Name</th>
                                <th width="25%">POS Device</th>
                                <th width="25%">Bind With</th>
                                <th width="25%">Device Type/version</th>
                                <th width="25%">Select Campaign</th>
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
                                  <td><?=stripslashes($ALLDATAINFO['pos_number'])?></td>
                                  <td><?=stripslashes($ALLDATAINFO['users_seq_id'])?></td>
                                  <td><?=stripslashes($ALLDATAINFO['users_type'])?></td>
                                  <td><?=stripslashes($ALLDATAINFO['users_name'])?></td>
                                  <td><?=stripslashes($ALLDATAINFO['last_name'])?></td>
                                  <td><?=stripslashes($ALLDATAINFO['users_email'])?></td>
                                  <td><?=stripslashes($ALLDATAINFO['users_mobile'])?></td>
                                  <td><?=stripslashes($ALLDATAINFO['availableArabianPoints'])?></td>
                                  <td><?=stripslashes($ALLDATAINFO['area'])?></td>
                                  <td><?=stripslashes($ALLDATAINFO['store_name'])?></td>
                                  <td><?=stripslashes($ALLDATAINFO['pos_device_id'])?></td>
                                  <?php if($ALLDATAINFO['bind_person_id']): ?>
                                    <td>
                                    Name : <?=stripslashes($ALLDATAINFO['bind_person_name'])?><br>
                                    User Type : <?=stripslashes($ALLDATAINFO['bind_user_type'])?><br>
                                    <a href="#">View All</a>
                                  </td>
                                  <?php else: ?>
                                    <td>
                                     -
                                    </td>
                                  <?php endif; ?>
                                  <td>
                                    <?php
                                    if($ALLDATAINFO['device_type']):
                                      echo 'Device Type : '.stripslashes($ALLDATAINFO['device_type']);
                                      echo  '<br>App Version : '.stripslashes($ALLDATAINFO['app_version']);
                                    else:
                                      echo '-';
                                    endif;
                                    ?>
                                  </td>

                                  <!-- <td><?=date('d-M-y h:i A',strtotime($ALLDATAINFO['created_at']))?></td> -->
                                  <td>

                                    <?php
                                      if(!empty($ALLDATAINFO['isSelectedCampaign']) && $ALLDATAINFO['isSelectedCampaign'] ==  "option1"):
                                        $option1 = 'selected'; 
                                      else:
                                         $default = 'selected'; 
                                      endif;
                                    ?>
                                    <select class="form-control w-100 select_option" id="select_option"  data-userId="<?=$ALLDATAINFO['users_id']?>" >
                                      <option value="default" <?php if($ALLDATAINFO['isSelectedCampaign'] == 'default'):echo 'selected'; endif;?>  > Default </option>
                                      <option value="option1" <?php if($ALLDATAINFO['isSelectedCampaign'] == 'option1'):echo 'selected'; endif;?> > Option 1 </option>
                                    </select>

                                  </td>
                                  
                                  <td style="text-align: right;"><?=showStatus($ALLDATAINFO['status'])?></td>
                                  <td>
                                  <div class="btn-group">
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                    <ul class="dropdown-menu" role="menu">
                                      <li>
                                        <a href="<?php echo getCurrentControllerPath('addeditdata/'.$ALLDATAINFO['users_id'])?>"><i class="fas fa-edit"></i> Edit Details</a>
                                      </li>
                                      <!-- <li>
                                        <a href="<?php echo getCurrentControllerPath('assigncapiagn/'.$ALLDATAINFO['users_id'])?>"><i class="fa fa-tasks"></i> Assign Campaigns</a>
                                      </li> -->


                                      <?php if($ALLDATAINFO['status'] == 'A'): ?>
                                      <li>
                                          <a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['users_id'].'/I')?>"><i class="fas fa-thumbs-down"></i> Inactive</a>
                                      </li>
                                      <?php elseif($ALLDATAINFO['status'] == 'I' || $ALLDATAINFO['status'] == 'N'): ?>
                                      <li>
                                          <a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['users_id'].'/A')?>"><i class="fas fa-thumbs-up"></i> Active</a>
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
        <h5 class="modal-title" id="exampleModalLabel">Download User Reports</h5>
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
                  <option value="users_seq_id" <?php if($searchField == 'users_seq_id')echo 'selected="selected"'; ?>>Users ID</option>
                  <option value="area" <?php if($searchField == 'area')echo 'selected="selected"'; ?>>Area</option>
                  <option value="users_type" <?php if($searchField == 'users_type')echo 'selected="selected"'; ?>>User Type</option>
                  <option value="users_name" <?php if($searchField == 'users_name')echo 'selected="selected"'; ?>>First Name</option>
                  <option value="last_name" <?php if($searchField == 'last_name')echo 'selected="selected"'; ?>>Last Name</option>
                  <option value="users_email" <?php if($searchField == 'users_email')echo 'selected="selected"'; ?>>Email</option>
                  <option value="users_mobile" <?php if($searchField == 'users_mobile')echo 'selected="selected"'; ?>>Users Mobile</option>
                  <option value="bind_person_name" <?php if($searchField == 'bind_person_name')echo 'selected="selected"'; ?>>Bind With</option>
                  <option value="pos_users" <?php if($searchField == 'pos_users')echo 'selected="selected"'; ?>>Pos Users list (Y/N)</option>
                  <option value="balance_equal_to" <?php if($searchField == 'balance_equal_to')echo 'selected="selected"'; ?>>Balance equels to</option>
                  <option value="balance_less_than" <?php if($searchField == 'balance_less_than')echo 'selected="selected"'; ?>>Balance less than</option>
                  <option value="balance_greater_than" <?php if($searchField == 'balance_less_than')echo 'selected="selected"'; ?>>Balance greater than</option>
                  <option value="device_type" <?php if($searchField == 'device_type')echo 'selected="selected"'; ?>>Device Type </option>
                  <option value="app_version" <?php if($searchField == 'app_version')echo 'selected="selected"'; ?>>App Version</option>
                </select>
              </div>
              <div class="col-sm-12 col-md-6">
                <input type="text" name="searchValue" id="searchValue" value="<?php echo $searchValue; ?>" class="form-control form-control-sm" placeholder="Enter Search Text">
              </div>
            </div>

            <div class="row mt-2"  style="margin:0px;">
                <label class="col-sm-12 col-md-12">Balance</label>
              <div class="col-sm-12 col-md-6">
                <select name="searchField1" id="searchField1" class="custom-select custom-select-sm form-control form-control-sm">
                  <option value="">Select Field</option>
                  <option value="balance_equal_to" <?php if($searchField == 'balance_equal_to')echo 'selected="selected"'; ?>>Balance equels to</option>
                  <option value="balance_less_than" <?php if($searchField == 'balance_less_than')echo 'selected="selected"'; ?>>Balance less than</option>
                  <option value="balance_greater_than" <?php if($searchField == 'balance_less_than')echo 'selected="selected"'; ?>>Balance greater than</option>
                </select>
              </div>
              <div class="col-sm-12 col-md-6">
                <input type="text" name="searchValue2" id="searchValue2" value="<?php echo $searchValue; ?>" class="form-control form-control-sm" placeholder="Enter Search Text">
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

<script>
 $(document).ready(function(){
    $(".select_option").on('click', function(){
        let userId             = $(this).attr('data-userid');
        let isSelectedCampaign = $(this).val();
        $.ajax({
            type: "POST",
            url: "<?=base_url('pos/pos_device_lists/addOption');?>",
            data: {isSelectedCampaign:isSelectedCampaign , userId:userId }, // Corrected the data parameter
            success: function(data) {
                // console.log(data);
            } 
        });
    });
});

</script>