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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">All Coupons</a></li>
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
                <h5>Manage Coupons</h5>
                <a href="<?php echo base_url('products/allproducts/couponExportExcel/').$products_id; ?>" class="btn btn-sm btn-primary pull-right" style="margin-left: 5px;">Export excel</a>
                <!-- <a href="<?php echo getCurrentControllerPath('addeditdata'); ?>" class="btn btn-sm btn-primary pull-right">Add product</a> -->
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
                                <option value="product_name" <?php if($searchField == 'product_name')echo 'selected="selected"'; ?>>Product Name</option>
                                <option value="coupon_code" <?php if($searchField == 'coupon_code')echo 'selected="selected"'; ?>>Coupon Code</option>
                                <option value="order_id" <?php if($searchField == 'order_id')echo 'selected="selected"'; ?>>Order ID</option>
                                <option value="users_email" <?php if($searchField == 'users_email')echo 'selected="selected"'; ?>>Email</option>
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
                    <th width="15%">Name</th>
                    <th width="15%">Product ID</th>
										<th width="20%">Product Name</th>
                    <th width="20%">AED</th>
                    <!-- <th width="25%">Payment Mode</th> -->
                    <th width="25%">Coupon Code</th>
                    <th width="25%">Date & Time</th>
                    <th width="10%">Coupon Status</th>
                    <!-- <th width="10%" style="text-align: right;">Status</th> 
										<th width="10%">Action</th>-->
									  </tr>
									</thead>
									<tbody style="text-align: center;">
									  <?php if($ALLDATA <> ""): $i=$first; $j=0; foreach($ALLDATA as $ALLDATAINFO): 
										if($j%2==0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;
									  ?>
										<tr role="row" class="<?php echo $rowClass; ?>">
										  <td style="text-align: center;"><?=$i++?></td>
                      <td><?=stripslashes($ALLDATAINFO['users_name'])?></td>
                      <td><?=substr($ALLDATAINFO['product_id'],8,15)?></td>
										  <td><?=stripslashes($ALLDATAINFO['product_name'])?></td>
                      <td><?=stripslashes($ALLDATAINFO['adepoints'])?></td>
                      <!-- <td><?=stripslashes($ALLDATAINFO['payment_mode'])?></td> -->
                      <td><?=stripslashes($ALLDATAINFO['coupon_code'])?></td>
                      <td><?=date('d-M-y h:i A',strtotime($ALLDATAINFO['created_at']))?></td>
                      <td><?=stripslashes($ALLDATAINFO['coupon_status'])?></td>
                      
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