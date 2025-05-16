<style>
  .coupon-code-circle{
    border:1px solid #40ABA8;
    color: #0E4391;
    border-radius: 50%;
    padding: 12px;
    font-weight: 900;


  }
</style>

<div class="pcoded-main-container">
    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Lotto Winners</a></li>
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
                <h5>Manage Winnerss</h5>
                <!-- <a href="<?php echo getCurrentControllerPath('exportexcel'); ?>" class="btn btn-sm btn-primary pull-right" style="margin-left: 5px;">Export excel</a> -->
                <!-- <a href="<?php echo getCurrentControllerPath('addeditdata'); ?>" class="btn btn-sm btn-primary pull-right">Generate Winners</a> -->
                <a href="<?php echo getCurrentControllerPath('generatewinner'); ?>" class="btn btn-sm btn-primary pull-right">Generate Winners</a>
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
                                <!-- <option value="winners_seq_id" <?php if($searchField == 'winners_seq_id')echo 'selected="selected"'; ?>>Winners ID</option> -->
                                <option value="Winners_name" <?php if($searchField == 'Winners_name')echo 'selected="selected"'; ?>>Winners Name</option>
                                <option value="category_name" <?php if($searchField == 'category_name')echo 'selected="selected"'; ?>>Category Name</option>
                                <option value="sub_category_name" <?php if($searchField == 'sub_category_name')echo 'selected="selected"'; ?>>Sub Category Name</option>
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
            										<th width="20%">Product ID</th>
                                <th width="40%">Coupon Code</th>
                                <th width="15%">Created Date</th>
                                <th width="15%">Status</th>
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
                                    <a href="<?=base_url('products/alllottoproducts/addeditdata/'.$ALLDATAINFO['product_id']);?>">
                                      <?=substr($ALLDATAINFO['product_id'], -5)?>
                                    </a>
                                  </td>
                                  <td>
                                    <div>
                                      <?php foreach ($ALLDATAINFO['coupon_code'] as $key => $item): ?>
                                         <span class="coupon-code-circle"><?=$item;?></span> 
                                      <?php endforeach; ?>
                                    </div>
                                  </td>
                                  <td><?=stripslashes($ALLDATAINFO['creation_date'])?></td>
                                  <td style="text-align: right;"><?=showStatus($ALLDATAINFO['status'])?></td>
            										  <td>
              											<div class="btn-group">
              											  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
              											  <ul class="dropdown-menu" role="menu">
              												<li><a href="<?php echo getCurrentControllerPath('addeditdata/'.$ALLDATAINFO['lotto_winners_id'])?>"><i class="fas fa-eye"></i> View Details</a></li>
              												<?php if($ALLDATAINFO['status'] == 'A'): ?>
              												  <li><a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['lotto_winners_id'].'/I')?>"><i class="fas fa-thumbs-down"></i> Inactive</a></li>
              												<?php elseif($ALLDATAINFO['status'] == 'I'): ?>
              												  <li><a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['lotto_winners_id'].'/A')?>"><i class="fas fa-thumbs-up"></i> Active</a></li>
              												<?php endif; ?>
              												  <li><a href="<?php echo getCurrentControllerPath('deletedata/'.$ALLDATAINFO['lotto_winners_id'])?>" onClick="return confirm('Want to delete!');"><i class="fas fa-trash"></i> Delete</a></li>
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