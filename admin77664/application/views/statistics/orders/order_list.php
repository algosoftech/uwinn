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
                        <?php /* ?><h5 class="m-b-10">Welcome <?=sessionData('HCAP_ADMIN_FIRST_NAME')?></h5><?php */ ?>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Orders List</a></li>
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
                <h5>Statistics Order List</h5>
                <!-- <a href="<?php echo getCurrentControllerPath('addeditdata'); ?>" class="btn btn-sm btn-primary pull-right" style="margin-left:10px;">Recharge</a>
                <a href="<?php echo getCurrentControllerPath('exportexcel'.$excelExportCondition); ?>" class="btn btn-sm btn-primary pull-right">Export excel</a> -->
              </div>
              <div class="card-body">
                <form id="Data_Form" name="Data_Form" method="get" action="<?php echo $forAction; ?>">
                  <div class="dt-responsive table-responsive">
                    <div id="simpletable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                    <div class="row">
                        <div class="col-sm-12 col-md-4">
                            <input type="text" name="email" id="email" value="<?php echo $email; ?>" class="form-control form-control-sm" placeholder="Enter a email id" autocomplete="off">
                        </div>
                        
                        <div class="col-sm-12 col-md-2">
                            <input type="submit" name="getDataByUser" value="Search" class="btn btn-sm btn-primary">
                            <input type="submit" name="clearAllSearch" value="Clear Search" class="btn btn-sm btn-primary pull-right">
                        </div>
                    </div>
                    <br>
                    <hr>
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
                          <div class="row" style="margin:10px 0 0 0;">
                            <div class="col-sm-12 col-md-4">
                              <input type="text" name="fromDate" id="fromDate" value="<?php echo $fromDate; ?>" class="form-control form-control-sm" placeholder="From Date">
                            </div>
                            <div class="col-sm-12 col-md-4">
                              <input type="text" name="toDate" id="toDate" value="<?php echo $toDate; ?>" class="form-control form-control-sm" placeholder="From Date">
                            </div>
                            <div class="col-sm-12 col-md-4">
                              <input type="submit" name="cearchFormSubmit" value="Search" class="btn btn-sm btn-primary">
                              <a href="<?php echo getCurrentControllerPath('statistics_report/orders/exportexcel/'.$excelExportCondition); ?>" class="btn btn-sm btn-primary pull-right">Export excel</a>
                            </div>
                          </div>
                        </div>
                      </div>
                      <?php if($ALLDATA <> ""): ?> 
                      <div class="row">
                        <div class="col-sm-12">
							<div class="table-responsive">
                            
								<table id="simpletable" class="table table-striped table-bordered nowrap dataTable" role="grid" aria-describedby="simpletable_info">
									<thead style="text-align: center;">
									  <tr role="row">
                      <th width="5%" style="text-align: center;">S.No.</th>
                      <th width="20%">Order Id.</th>
                      <th width="20%">Product</th>
                      <th width="10%">Qty.</th>
                      <th width="10%">User Email</th>
                      <th width="10%">User Phone</th>
                      <th width="20%">Donated</th>
                      <th width="10%">Purchase Date</th>
                      <th width="10%">Total Amount</th>
                      <th width="10%">Action</th>
									  </tr>
									</thead>
									<tbody style="text-align: center;">
                    <?php if($ALLDATA <> ""): $i=$first; $j=0; foreach($ALLDATA as $ALLDATAINFO): 
                      $ordDetails = $this->common_model->getDataByParticularField('da_orders_details', 'order_id', $ALLDATAINFO['order_id']);
                    if($j%2==0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;
                    ?>
                    <tr role="row" class="<?php echo $rowClass; ?>">
                      <td><?=$i++?></td>
                      <td><?=stripslashes($ALLDATAINFO['order_id'])?></td>
                      <td><?php echo $ordDetails['product_name'];  ?></td>
                      <td><?=$ordDetails['quantity']?></td>
                      <td><?=stripslashes($ALLDATAINFO['user_email'])?></td>
                      <td><?=stripslashes($ALLDATAINFO['user_phone'])?></td>
                      <td><?php echo $ALLDATAINFO['product_is_donate']=='Y'?'Yes':'No'; ?></td>
                      <td><?=date('d M Y', strtotime($ALLDATAINFO['created_at']))?></td>
                      <td>AED<?=number_format($ALLDATAINFO['total_price'],2)?></td>
                      <td>
                      <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                        <ul class="dropdown-menu" role="menu">
                        <li><a href="<?php echo getCurrentControllerPath('addeditdata/'.$ALLDATAINFO['order_id'])?>"><i class="far fa-eye"></i> View Details</a></li>
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
                    <?php endif; ?>
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
