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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Testimonials</a></li>
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
                <h5>Manage Testimonials</h5>
                <a href="<?php echo getCurrentControllerPath('addeditdata'); ?>" class="btn btn-sm btn-primary pull-right">Add Testimonials</a>
              </div>
              <div class="card-body">
                <form id="Data_Form" name="Data_Form" method="get" action="<?php echo $forAction; ?>">
                  <div class="dt-responsive table-responsive">
                    <div id="simpletable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                     
                      <div class="row">
                        <div class="col-sm-12">
							<div class="table-responsive">
								<table id="simpletable" class="table table-striped table-bordered nowrap dataTable" role="grid" aria-describedby="simpletable_info">
                            <thead style="text-align: center;">
                              <tr role="row">
                                <th width="5%">S.No.</th>
                                <th width="15%">Image</th>
                                <th width="10%" >Status</th>
                                <th width="10%">Action</th>
                              </tr>
                            </thead>
                            <tbody style="text-align: center;">
                              <?php if($ALLDATA <> ""): $i=$first; $j=0; foreach($ALLDATA as $ALLDATAINFO): 
                                if($j%2==0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;
                              ?>
                                <tr role="row" class="<?php echo $rowClass; ?>">
                                  <td><?=$i++?></td>
                                  <td><img src="<?php echo fileBaseUrl.$ALLDATAINFO['image']; ?>" width="200" border="0" alt=""></td>
                                  <td ><?=showStatus($ALLDATAINFO['status'])?></td>
                                  <td>
                                    <div class="btn-group">
                                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                      <ul class="dropdown-menu" role="menu">
                                        <li><a href="<?php echo getCurrentControllerPath('addeditdata/'.$ALLDATAINFO['testimonial_id'])?>"><i class="fas fa-edit"></i> Edit Details</a></li>
                                        <?php if($ALLDATAINFO['status'] == 'A'): ?>
                                          <li><a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['testimonial_id'].'/I')?>"><i class="fas fa-thumbs-down"></i> Inactive</a></li>
                                        <?php elseif($ALLDATAINFO['status'] == 'I' || $ALLDATAINFO['status'] == 'N'): ?>
                                          <li><a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['testimonial_id'].'/A')?>"><i class="fas fa-thumbs-up"></i> Active</a></li>
                                        <?php endif; ?>
                                          <li><a href="<?php echo getCurrentControllerPath('deletedata/'.$ALLDATAINFO['testimonial_id'])?>" onClick="return confirm('Want to delete!');"><i class="fas fa-trash"></i> Delete</a></li>
                                       </ul>
                                    </div>
                                  </td>
                                </tr>
                              <?php $j++; endforeach; else: ?>
                                <tr>
                                  <td colspan="4" style="text-align:center;">No Data Available In Table</td>
                                </tr>
                              <?php endif; ?>
                            </tbody>
                          </table>
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