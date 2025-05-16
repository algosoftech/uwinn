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
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Profile</a></li>
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
                <h5>Profile</h5>
              </div>
              <div class="card-body">
                <div class="dt-responsive table-responsive">
                  <div id="simpletable_wrapper" class="dataTables_wrapper dt-bootstrap4">
                    <div class="row">
                      <div class="col-sm-12">
                        <table id="simpletable" class="table table-striped table-bordered nowrap dataTable" role="grid" aria-describedby="simpletable_info">
                          <thead>
                            <tr role="row">
                              <th>Title</th>
                              <th>Name</th>
                              <th>Email Id</th>
                              <th>Mobile No</th>
                              <th>Action</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr role="row" class="odd">
                              <td><?=stripslashes($ADMINDATA['admin_title'])?></td>
                              <td><?=stripslashes($ADMINDATA['admin_first_name'].' '.$ADMINDATA['admin_middle_name'].' '.$ADMINDATA['admin_last_name'])?></td>
                              <td><?=stripslashes($ADMINDATA['admin_email'])?></td>
                              <td><?=stripslashes($ADMINDATA['admin_phone'])?></td>
                              <td>
                                <div class="btn-group">
                                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                  <ul class="dropdown-menu" role="menu">
                                    <li><a href="{FULL_SITE_URL}editprofile/<?=$ADMINDATA['admin_id']?>"><i class="fas fa-edit"></i> Edit Details</a></li>
                                    <li><a href="{FULL_SITE_URL}change-password/<?=$ADMINDATA['admin_id']?>"><i class="fas fa-edit"></i> Change Password</a></li>
                                  </ul>
                                </div>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>