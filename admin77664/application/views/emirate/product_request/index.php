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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Product Request List</a></li>
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
                <h5>Manage Producr Request</h5>
                <!-- <a href="<?php echo getCurrentControllerPath('addeditdata/'.$collection_point_id); ?>" class="btn btn-sm btn-primary pull-right">Add Inventory</a> -->
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
                                <option value="emirate_name" <?php if($searchField == 'emirate_name')echo 'selected="selected"'; ?>>Emirate</option>
                                <option value="area_name" <?php if($searchField == 'area_name')echo 'selected="selected"'; ?>>Emirate Area</option>
                                <option value="users_email" <?php if($searchField == 'users_email')echo 'selected="selected"'; ?>>Retailer's Email</option>
                                <!-- <option value="users_mobile" <?php if($searchField == 'users_mobile')echo 'selected="selected"'; ?>>Retailer's Mobile</option> -->
                                <option value="collection_point_name" <?php if($searchField == 'collection_point_name')echo 'selected="selected"'; ?>>Collection Point</option>
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
										<th width="5%">S.No.</th>
										<th width="20%">Product ID</th>
                    <th width="20%">Product Name</th>
                    <th width="20%">Retailer's Details</th>
										<th width="25%">Request Quantity</th>
                    <th width="25%">Requested Date</th>
                    <th width="25%">Sent Quantity</th>
										<th width="25%">Sent Date</th>
                    <!-- <th width="25%">updated Date</th> -->
										<th width="10%" style="text-align: right;">Status</th>
										<th width="10%">Action</th>
									  </tr>
									</thead>
									<tbody style="text-align: center;">
									  <?php if($ALLDATA <> ""): $i=$first; $j=0; foreach($ALLDATA as $ALLDATAINFO): 
										if($j%2==0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;
									  ?>
										<tr role="row" class="<?php echo $rowClass; ?>">
										  <td><?=$i++?></td>
										  <td><?=stripslashes($ALLDATAINFO['product_seq_id'])?></td>
                      <td><?=stripslashes($ALLDATAINFO['product_name'])?></td>
                      <td>
                        <?php if(isset($ALLDATAINFO['users_email']) && isset($ALLDATAINFO['users_mobile'])): ?>
                          Email ID : <?php echo stripslashes($ALLDATAINFO['users_email'])?><br>
                          Mobile No. : <?php echo stripslashes($ALLDATAINFO['users_mobile'])?>
                        <?php elseif(isset($ALLDATAINFO['users_email'])): ?>  
                          Email ID : <?php echo stripslashes($ALLDATAINFO['users_email'])?>
                        <?php else: ?>
                          Mobile No. : <?php echo stripslashes($ALLDATAINFO['users_mobile'])?>
                        <?php endif; ?>
                      </td>
										  <td><?=stripslashes($ALLDATAINFO['request_qty'])?> Nos.</td>
                      <td><?=$this->timezone->location_date($ALLDATAINFO['creation_date'],'D,F,Y',DEFAULT_TIMEZONE);?></td>
                      <td><?=stripslashes($ALLDATAINFO['sent_qty'])?> Nos.</td>
                      <?php if($ALLDATAINFO['sent_date']): ?>
                        <td><?=$this->timezone->location_date($ALLDATAINFO['sent_date'],'D,F,Y',DEFAULT_TIMEZONE);?></td>
                      <?php else: ?>
                        <td>-</td>
                      <?php endif; ?>
										  <td style="text-align: right;"><?=showStatus($ALLDATAINFO['status'])?></td>
										  <td>
                      <?php if($ALLDATAINFO['status'] == 'C'): ?>
                        -
                      <?php elseif($ALLDATAINFO['status'] == 'RJ'): ?>
                        -
                      <?php else: ?>
                        <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
											  <ul class="dropdown-menu" role="menu">
                          <li>
                            <a href="<?php echo getCurrentControllerPath('addeditdata/'.$ALLDATAINFO['request_id'])?>">Approved</a>
                          </li>
                          <li>
                            <a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['request_id'].'/RJ')?>">Cancel</a>
                          </li>
											</div>
                      <?php endif; ?>
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