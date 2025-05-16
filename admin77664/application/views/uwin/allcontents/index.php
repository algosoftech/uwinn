<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
 
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
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Contents</a></li>
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
                <h5>Manage Contents</h5>
                <a href="<?php echo getCurrentControllerPath('addeditdata'); ?>" class="btn btn-sm btn-primary pull-right">Add Contents</a>
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
                                  <th width="15%">Image / Video</th>
                                  <th width="10%" >Added For</th>
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
                                  <td>
                                    <?php if($ALLDATAINFO['link_thumbnail']): ?>
                                      <img src="<?php echo fileBaseUrl.$ALLDATAINFO['link_thumbnail']; ?>" width="250" border="0" alt="">
                                    <?php endif;?>
                                    <?php if($ALLDATAINFO['image']): ?>
                                      <img src="<?php echo fileBaseUrl.$ALLDATAINFO['image']; ?>" width="250" border="0" alt="">
                                    <?php endif;?>
                                    <?php if($ALLDATAINFO['video']): ?>
                                      <video width="250" height="250" controls >
                                          <source src="<?php echo fileBaseUrl.$ALLDATAINFO['video']; ?>" type="video/mp4">
                                          Your browser does not support the video tag.
                                      </video>
                                    <?php endif;?>
                                  </td>
                                  <td>
                                      <?=ucwords(str_replace('_', ' ', $ALLDATAINFO['upload_type']));?>
                                      <hr>
                                      <div class="row">
                                        <div class="col-12">
                                            <div class="form-group-inner">
                                              <input type="checkbox" name="added_for[]" value="Top Banner" id="top_banner" <?= isset($EDITDATA['added_for']) && in_array('Top Banner', $EDITDATA['added_for']) ? 'checked' : ''; ?> disabled >
                                              <label for="top_banner">Top Banner</label>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group-inner">
                                              <input type="checkbox" name="Recent_Winners" id="Result_Page" <?=isset($EDITDATA['added_for']) && in_array('Recent Winners', $ALLDATAINFO['added_for'])? 'checked' : '' ;?> disabled>
                                              <label for="Result_Page">Recent Winners</label>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group-inner">
                                              <input type="checkbox" name="Result_Page" id="Result_Page" <?=isset($EDITDATA['added_for']) && in_array('Result Page', $ALLDATAINFO['added_for'])? 'checked' : '' ;?> disabled>
                                              <label for="Result_Page">Result Page</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group-inner">
                                              <input type="checkbox" name="Winner_Gallery" id="Winner_Gallery" <?=isset($EDITDATA['added_for']) && in_array('Winner Gallery', $ALLDATAINFO['added_for'])? 'checked' : '' ;?>  disabled>
                                              <label for="Winner_Gallery">Winner Gallery</label>
                                            </div>
                                        </div>
                                      </div>
                                  </td>
                                  
                                  <td ><?=showStatus($ALLDATAINFO['status'])?></td>
                                  <td>
                                    <div class="btn-group">
                                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                      <ul class="dropdown-menu" role="menu">
                                        <li><a href="<?php echo getCurrentControllerPath('addeditdata/'.$ALLDATAINFO['content_id'])?>"><i class="fas fa-edit"></i> Edit Details</a></li>
                                        <?php if($ALLDATAINFO['status'] == 'A'): ?>
                                          <li><a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['content_id'].'/I')?>"><i class="fas fa-thumbs-down"></i> Inactive</a></li>
                                        <?php elseif($ALLDATAINFO['status'] == 'I' || $ALLDATAINFO['status'] == 'N'): ?>
                                          <li><a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['content_id'].'/A')?>"><i class="fas fa-thumbs-up"></i> Active</a></li>
                                        <?php endif; ?>
                                          <li><a href="<?php echo getCurrentControllerPath('deletedata/'.$ALLDATAINFO['content_id'])?>" onClick="return confirm('Want to delete!');"><i class="fas fa-trash"></i> Delete</a></li>
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
