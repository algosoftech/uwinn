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
                        <li class="breadcrumb-item"><a href="{FULL_SITE_URL}dashboard"><i class="feather icon-home"></i></a></li>
                       
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Push Notification</a></li>
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
                <h5>Manage Notifications</h5>
                <a href="javaScriptcript:void{0}" class="btn btn-sm btn-primary pull-right" style="margin-left: 5px;" data-toggle="modal" data-target="#exportModal">Export excel</a>
                <a href="<?php echo getCurrentControllerPath('addeditdata'); ?>" class="btn btn-sm btn-primary pull-right">Send Notification</a>
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
                          <div class="dataTables_length" id="simpletable_length" style="margin-left:-70%">
                            <label>Notification Type </label>
                              <select name="notification_type" id="showLength" class="custom-select custom-select-sm form-control form-control-sm">
                                <option value="All" <?php if($notification_type == 'All')echo 'selected="selected"'; ?>>All</option>
                                <option value="individual" <?php if($notification_type == 'individual')echo 'selected="selected"'; ?>>Individual</option>
                                <option value="all" <?php if($notification_type == 'all')echo 'selected="selected"'; ?>>Broadcast All</option>
                              </select>
                             
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-12">
                          <table id="simpletable" class="table table-striped table-bordered nowrap dataTable" role="grid" aria-describedby="simpletable_info">
                            <thead style="text-align: center">
                              <tr role="row">
                                <th width="5%">S.No.</th>
                                <th width="15%">Title</th>
                                <th width="60%">Description</th>
                                <th width="10%">Read</th>
                                <th width="10%">Unread</th>
                                <th width="60%">Date</th>
                                <th width="10%">Status</th>
                                <!-- <th width="10%">Action</th> -->
                              </tr>
                            </thead>
                            <tbody style="text-align: center">
                              <?php if($ALLDATA <> ""): $i=$first; $j=0; foreach($ALLDATA as $ALLDATAINFO): 
                                if($j%2==0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;
                              ?>
                                <tr role="row" class="<?php echo $rowClass; ?>">
                                  <td ><?=$i++?></td>
                                  <td><?=(strlen($ALLDATAINFO['notific_title'])> 30) ? wordwrap($ALLDATAINFO['notific_title'], 30, "<br>\n", true) : $ALLDATAINFO['notific_title'];?></td>
                                  <td><?= (strlen($ALLDATAINFO['notific_message'])> 70) ? wordwrap($ALLDATAINFO['notific_message'], 70, "<br>\n", true) : $ALLDATAINFO['notific_message'];?></td>
                                  <td> <a href="javascript:void(0)" class="btn btn-sm btn-primary view-users-btn" data-toggle="modal" data-target="#userlistModal" data-notification-id="<?= $ALLDATAINFO['notification_id']; ?>" data-ntype="Y">
                                 <?= $ALLDATAINFO['read_count'] ?> <i class="feather icon-eye"></i></a></td>
                                 <td><a href="javascript:void(0)" class="btn btn-sm btn-primary view-users-btn" data-toggle="modal" data-target="#userlistModal" data-notification-id="<?= $ALLDATAINFO['notification_id']; ?>" data-ntype="N"><?=$ALLDATAINFO['unread_count'] ?> <i class="feather icon-eye"></i></a></td>
                                  
                                   <td><?php 
                                    try {
                                      $dateValue = $ALLDATAINFO['creation_date'] ?? '';
                                      if (empty($dateValue)) {
                                        echo '';
                                      } elseif (is_numeric($dateValue)) {
                                        // Unix timestamp
                                        $date = new DateTime('@' . (int)$dateValue);
                                        $date->setTimezone(new DateTimeZone('Asia/Dubai'));
                                        echo $date->format('Y-m-d H:i');
                                      } else {
                                        // Date string - parse and convert to Dubai timezone
                                        $date = new DateTime($dateValue);
                                        $date->setTimezone(new DateTimeZone('Asia/Dubai'));
                                        echo $date->format('Y-m-d H:i');
                                      }
                                    } catch (\Exception $e) {
                                      echo htmlspecialchars($dateValue ?? '');
                                    }
                                   ?></td>
                                  <td><?=showStatus($ALLDATAINFO['status'])?></td>
                                  <!-- <td>
                                    <div class="btn-group">
                                      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                      <ul class="dropdown-menu" role="menu">
                                        <li><a href="<?php echo getCurrentControllerPath('addeditdata/'.$ALLDATAINFO['notification_temp_id'])?>"><i class="fas fa-edit"></i> Edit Details</a></li>
                                        <?php if($ALLDATAINFO['status'] == 'A'): ?>
                                          <li><a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['notification_temp_id'].'/I')?>"><i class="fas fa-thumbs-down"></i> Inactive</a></li>
                                        <?php elseif($ALLDATAINFO['status'] == 'I'): ?>
                                          <li><a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['notification_temp_id'].'/A')?>"><i class="fas fa-thumbs-up"></i> Active</a></li>
                                        <?php endif; ?>
                                        <?php if($ALLDATAINFO['notification_used'] == 'N'): ?>
                                          <li><a href="<?php echo getCurrentControllerPath('deletedata/'.$ALLDATAINFO['notification_temp_id'])?>" onClick="return confirm('Want to delete!');"><i class="fas fa-trash"></i> Delete</a></li>
                                        <?php endif; ?>
                                      </ul>
                                    </div>
                                  </td> -->
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

<div class="modal fade" id="userlistModal" tabindex="-1" role="dialog" aria-labelledby="userListLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">User List</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div id="userListContainer">
          Loading....
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Notification Reports</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?=getCurrentControllerPath('exportexcel');?>" method="post" autocomplete="off">
      <div class="modal-body">
          
            

           

            <div class="row mt-2"  style="margin:0px;">
                <label class="col-sm-12 col-md-12">Notification Type</label>
              <div class="col-sm-12 col-md-6">
                  <select name="searchField1" id="searchField1" class="custom-select custom-select-sm form-control form-control-sm">
                    <option value="">Select Field</option>
                    <option value="All" <?php if($notification_type == 'All')echo 'selected="selected"'; ?>>All</option>
                    <option value="individual" <?php if($notification_type == 'individual')echo 'selected="selected"'; ?>>Individual</option>
                    <option value="all" <?php if($notification_type == 'all')echo 'selected="selected"'; ?>>Broadcast All</option>
                  </select>
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
$(document).on('click', '.view-users-btn', function () {
    const notificationId = $(this).data('notification-id');
    const ntype = $(this).data('ntype');
    $('#userListContainer').html('<li class="list-group-item">getting...</li>');

    $.ajax({
        url: '<?= base_url("cms/notifications/getnotificationuser") ?>',
        type: 'POST',
        data: { notification_id: notificationId,type:ntype },
        dataType: 'json',
        success: function (res) {
            if (res.status === 'success' && res.data.length > 0) {
                let html = '';
                
                $('#userListContainer').html(res.data);
            } else {
                $('#userListContainer').html('<li class="list-group-item">No users found.</li>');
            }
        },
        error: function () {
            $('#userListContainer').html('<li class="list-group-item text-danger">Error loading data.</li>');
        }
    });
});
</script>