<style type="text/css">
  .col-1, .col-2, .col-3, .col-4, .col-5, .col-6, .col-7, .col-8, .col-9, .col-10, .col-11, .col-12, .col, .col-auto, .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm, .col-sm-auto, .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12, .col-md, .col-md-auto, .col-lg-1, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg, .col-lg-auto, .col-xl-1, .col-xl-2, .col-xl-3, .col-xl-4, .col-xl-5, .col-xl-6, .col-xl-7, .col-xl-8, .col-xl-9, .col-xl-10, .col-xl-11, .col-xl-12, .col-xl, .col-xl-auto {
    position: relative;
    width: 100%;
    padding-right: 15px;
    padding-left: 15px;
    float: left;
}
.check_repeat {border-top: 1px solid #49CCED;}
.f_check_repeat {border-top: 1px solid #4680ff;}
.s_check_repeat {border-top: 1px solid #9ACD32;}
.no-boder {border-bottom: 0px;}
.module_rows .row {margin:0px;}
.text-aline-center {text-align:center;}
.l_padding_no {padding-left:0px;}
.l_padding_15 {padding-left:15px;}
.l_padding_40 {padding-left:40px;}
</style>
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
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('usersCMPOPData',getCurrentControllerPath('index')); ?>">Users</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> User</a></li>
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
                <h5><?=$EDITDATA?'Edit':'Add'?> User</h5>
                <a href="<?php echo correctLink('usersCMPOPData',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
              </div>
              <div class="card-body">
                <div class="basic-login-inner">
                  <form id="currentPageFormSubadmin" name="currentPageFormSubadmin" class="form-auth-small" method="post" action="">
                    <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="admin_id"/>
                    <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['admin_id']?>"/>
                    <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['admin_id']?>"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                    <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('department_id')): ?>error<?php endif; ?>">
                        <label>Department<span class="required">*</span></label>
                        <?php if(set_value('department_id')): $departmentData = explode('_____',set_value('department_id')); $departmentid = $departmentData[0]; elseif($EDITDATA['department_id']): $departmentid = stripslashes($EDITDATA['department_id']); else: $departmentid = ''; endif; ?>
                        <select name="department_id" id="department_id" class="form-control required">
                          <?php echo $this->admin_model->getDepartment($departmentid); ?>
                        </select>                        
                        <?php if(form_error('department_id')): ?>
                          <span for="department_id" generated="true" class="help-inline"><?php echo form_error('department_id'); ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('designation_id')): ?>error<?php endif; ?>">
                        <label>Designation<span class="required">*</span></label>
                        <?php if(set_value('designation_id')): $designationData = explode('_____',set_value('designation_id')); $designationid = $designationData[0]; elseif($EDITDATA['designation_id']): $designationid = stripslashes($EDITDATA['designation_id']); else: $designationid = ''; endif; ?>
                        <select name="designation_id" id="designation_id" class="form-control required">
                          <?php echo $this->admin_model->getDesignation($designationid); ?>
                        </select>
                        <?php if(form_error('designation_id')): ?>
                          <span for="designation_id" generated="true" class="help-inline"><?php echo form_error('designation_id'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_title')): ?>error<?php endif; ?>">
                        <label>Title<span class="required">*</span></label>
                        <?php if(set_value('admin_title')): $usertitle = set_value('admin_title'); elseif($EDITDATA['admin_title']): $usertitle = stripslashes($EDITDATA['admin_title']); else: $usertitle = ''; endif; ?>
                        <select name="admin_title" id="admin_title" class="form-control required">
                          <option value="Mr" <?php if($usertitle=='Mr'): echo 'selected="selected"'; endif; ?>>Mr</option>
                          <option value="Mrs" <?php if($usertitle=='Mrs'): echo 'selected="selected"'; endif; ?>>Mrs</option>
                          <option value="Miss" <?php if($usertitle=='Miss'): echo 'selected="selected"'; endif; ?>>Miss</option>
                          <option value="Ms" <?php if($usertitle=='Ms'): echo 'selected="selected"'; endif; ?>>Ms</option>
                          <option value="Master" <?php if($usertitle=='Master'): echo 'selected="selected"'; endif; ?>>Master</option>
                        </select>
                        <?php if(form_error('admin_title')): ?>
                          <span for="admin_title" generated="true" class="help-inline"><?php echo form_error('admin_title'); ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_first_name')): ?>error<?php endif; ?>">
                        <label>First Name<span class="required">*</span></label>
                        <input type="text" name="admin_first_name" id="admin_first_name" value="<?php if(set_value('admin_first_name')): echo set_value('admin_first_name'); else: echo stripslashes($EDITDATA['admin_first_name']);endif; ?>" class="form-control required" placeholder="First Name">
                        <?php if(form_error('admin_first_name')): ?>
                          <span for="admin_first_name" generated="true" class="help-inline"><?php echo form_error('admin_first_name'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_middle_name')): ?>error<?php endif; ?>">
                        <label>Middle Name</label>
                        <input type="text" name="admin_middle_name" id="admin_middle_name" value="<?php if(set_value('admin_middle_name')): echo set_value('admin_middle_name'); else: echo stripslashes($EDITDATA['admin_middle_name']);endif; ?>" class="form-control" placeholder="Middle Name">
                        <?php if(form_error('admin_middle_name')): ?>
                          <span for="admin_middle_name" generated="true" class="help-inline"><?php echo form_error('admin_middle_name'); ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_last_name')): ?>error<?php endif; ?>">
                        <label>Last Name<span class="required">*</span></label>
                        <input type="text" name="admin_last_name" id="admin_last_name" value="<?php if(set_value('admin_last_name')): echo set_value('admin_last_name'); else: echo stripslashes($EDITDATA['admin_last_name']);endif; ?>" class="form-control required" placeholder="Last Name">
                        <?php if(form_error('admin_last_name')): ?>
                          <span for="admin_last_name" generated="true" class="help-inline"><?php echo form_error('admin_last_name'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_email')): ?>error<?php endif; ?>">
                        <label>Email<span class="required">*</span></label>
                        <input type="text" name="admin_email" id="admin_email" value="<?php if(set_value('admin_email')): echo set_value('admin_email'); else: echo stripslashes($EDITDATA['admin_email']);endif; ?>" class="form-control required email" placeholder="Email">
                        <?php if(form_error('admin_email')): ?>
                          <span for="admin_email" generated="true" class="help-inline"><?php echo form_error('admin_email'); ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_phone')): ?>error<?php endif; ?>">
                        <label>Phone<span class="required">*</span></label>
                        <input type="text" name="admin_phone" id="admin_phone" value="<?php if(set_value('admin_phone')): echo set_value('admin_phone'); else: echo stripslashes($EDITDATA['admin_phone']);endif; ?>" class="form-control required" placeholder="Phone">
                        <?php if(form_error('admin_phone')): ?>
                          <span for="admin_phone" generated="true" class="help-inline"><?php echo form_error('admin_phone'); ?></span>
                        <?php endif; if($mobileerror):  ?>
                          <span for="admin_phone" generated="true" class="help-inline"><?php echo $mobileerror; ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <?php if($EDITDATA <> ""): ?>
                      <div class="row">
                        <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('password')): ?>error<?php endif; ?>">
                          <label>New Password</label>
                          <input type="password" name="password" id="password" value="<?php if(set_value('password')): echo set_value('password'); endif; ?>" class="form-control" placeholder="New Password">
                          <?php if(form_error('password')): ?>
                            <span for="password" generated="true" class="help-inline"><?php echo form_error('password'); ?></span>
                          <?php endif; ?>
                        </div>
                        <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('conf_password')): ?>error<?php endif; ?>">
                          <label>Confirm Password</label>
                          <input type="password" name="conf_password" id="conf_password" value="<?php if(set_value('conf_password')): echo set_value('conf_password'); endif; ?>" class="form-control" placeholder="Confirm Password">
                          <?php if(form_error('conf_password')): ?>
                            <span for="conf_password" generated="true" class="help-inline"><?php echo form_error('conf_password'); ?></span>
                          <?php endif; ?>
                        </div>
                      </div>
                    <?php else: ?>
                      <div class="row">
                        <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('password')): ?>error<?php endif; ?>">
                          <label>Password<span class="required">*</span></label>
                          <input type="password" name="password" id="password" value="<?php if(set_value('password')): echo set_value('password'); endif; ?>" class="form-control required" placeholder="Password">
                          <?php if(form_error('password')): ?>
                            <span for="password" generated="true" class="help-inline"><?php echo form_error('password'); ?></span>
                          <?php endif; ?>
                        </div>
                        <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('conf_password')): ?>error<?php endif; ?>">
                          <label>Confirm Password<span class="required">*</span></label>
                          <input type="password" name="conf_password" id="conf_password" value="<?php if(set_value('conf_password')): echo set_value('conf_password'); endif; ?>" class="form-control required" placeholder="Confirm Password">
                          <?php if(form_error('conf_password')): ?>
                            <span for="conf_password" generated="true" class="help-inline"><?php echo form_error('conf_password'); ?></span>
                          <?php endif; ?>
                        </div>
                      </div>
                    <?php endif; ?>
                    <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_address')): ?>error<?php endif; ?>">
                        <label>Address</label>
                        <input type="text" name="admin_address" id="admin_address" value="<?php if(set_value('admin_address')): echo set_value('admin_address'); else: echo stripslashes($EDITDATA['admin_address']);endif; ?>" class="form-control" placeholder="Address">
                        <?php if(form_error('admin_address')): ?>
                          <span for="admin_address" generated="true" class="help-inline"><?php echo form_error('admin_address'); ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_city')): ?>error<?php endif; ?>">
                        <label>City</label>
                        <input type="text" name="admin_city" id="admin_city" value="<?php if(set_value('admin_city')): echo set_value('admin_city'); else: echo stripslashes($EDITDATA['admin_city']);endif; ?>" class="form-control" placeholder="City">
                        <?php if(form_error('admin_city')): ?>
                          <span for="admin_city" generated="true" class="help-inline"><?php echo form_error('admin_city'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_state')): ?>error<?php endif; ?>">
                        <label>State</label>
                        <input type="text" name="admin_state" id="admin_state" value="<?php if(set_value('admin_state')): echo set_value('admin_state'); else: echo stripslashes($EDITDATA['admin_state']);endif; ?>" class="form-control" placeholder="State">
                        <?php if(form_error('admin_state')): ?>
                          <span for="admin_state" generated="true" class="help-inline"><?php echo form_error('admin_state'); ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_pincode')): ?>error<?php endif; ?>">
                        <label>Pincode</label>
                        <input type="text" name="admin_pincode" id="admin_pincode" value="<?php if(set_value('admin_pincode')): echo set_value('admin_pincode'); else: echo stripslashes($EDITDATA['admin_pincode']);endif; ?>" class="form-control" placeholder="Pincode">
                        <?php if(form_error('admin_pincode')): ?>
                          <span for="admin_pincode" generated="true" class="help-inline"><?php echo form_error('admin_pincode'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <fieldset>
                          <legend>Permission Section User</legend>
                          <div class="sparkline12-list mg-b-15">
                            <div class="basic-login-form-ad">
                              <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                  <div class="check_repeat row modul_head">
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                      <div class="col-md-12 col-sm-12 col-xs-12">
                                        <?php if($PERERROR): echo '<p style="color:red;">'.$PERERROR.'</p>'; endif; ?>
                                      </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                      <div class="col-md-3 col-sm-3 col-xs-12 text-aline-center">
                                        <p>View data</p>
                                      </div>
                                      <div class="col-md-3 col-sm-3 col-xs-12 text-aline-center">
                                        <p>Add data</p>
                                      </div>
                                      <div class="col-md-3 col-sm-3 col-xs-12 text-aline-center">
                                        <p>Edit data</p>
                                      </div>
                                      <div class="col-md-3 col-sm-3 col-xs-12 text-aline-center">
                                        <p>Delete data</p>
                                      </div>
                                    </div>
                                  </div>
                                  <?php if($Modirectory <> ""): $i=1; foreach($Modirectory as $MODinfo): 
                                        $mmc                          =   $MODinfo['module_id'];
                                        if($_POST['mainmodule_'.$mmc]): 
                                          $mainmodactive              =   'Y';
                                          if($_POST['mainmodule_view_data_'.$mmc]):
                                            $mainmod_view_active      =   'Y';
                                          else:
                                            $mainmod_view_active      =   'N';
                                          endif;
                                          if($_POST['mainmodule_add_data_'.$mmc]):
                                            $mainmod_add_active       =   'Y';
                                          else:
                                            $mainmod_add_active       =   'N';
                                          endif;
                                          if($_POST['mainmodule_edit_data_'.$mmc]):
                                            $mainmod_edit_active      =   'Y';
                                          else:
                                            $mainmod_edit_active      =   'N';
                                          endif;
                                          if($_POST['mainmodule_delete_data_'.$mmc]):
                                            $mainmod_delete_active    =   'Y';
                                          else:
                                            $mainmod_delete_active    =   'N';
                                          endif;
                                        elseif($MODULEDATA['mainmodule_'.$mmc]):
                                          $mainmodactive              =   'Y';
                                          if($MODULEDATA['mainmodule_view_data_'.$mmc]):
                                            $mainmod_view_active      =   'Y';
                                          else:
                                            $mainmod_view_active      =   'N';
                                          endif;
                                          if($MODULEDATA['mainmodule_add_data_'.$mmc]):
                                            $mainmod_add_active       =   'Y';
                                          else:
                                            $mainmod_add_active       =   'N';
                                          endif;
                                          if($MODULEDATA['mainmodule_edit_data_'.$mmc]):
                                            $mainmod_edit_active      =   'Y';
                                          else:
                                            $mainmod_edit_active      =   'N';
                                          endif;
                                          if($MODULEDATA['mainmodule_delete_data_'.$mmc]):
                                            $mainmod_delete_active    =   'Y';
                                          else:
                                            $mainmod_delete_active    =   'N';
                                          endif;
                                        else:
                                          $mainmodactive              =   'N';
                                          $mainmod_view_active        =   'N';
                                          $mainmod_add_active         =   'N';
                                          $mainmod_edit_active        =   'N';
                                          $mainmod_delete_active      =   'N';
                                        endif;
                                    ?>
                                    <div class="module_rows modul">
                                      <div class="check_repeat row">
                                        <div class="col-md-2 col-sm-2 col-xs-12">
                                          <div class="col-md-12 col-sm-12 col-xs-12">
                                            <P>Module <?php echo $i; ?></P>
                                          </div>
                                        </div>
                                        <?php if($MODinfo['first_data']): ?>
                                          <div class="col-md-10 col-sm-10 col-xs-12">
                                             <div class="col-md-4 col-sm-4 col-xs-12 l_padding_no">
                                              <label for="mainmodule_<?php echo $mmc; ?>">
                                                <input type="checkbox" name="mainmodule_<?php echo $mmc; ?>" id="mainmodule_<?php echo $mmc; ?>" value="Y" class="parentmodule" <?php if($mainmodactive == 'Y'): echo 'checked="checked"'; endif; ?>/>
                                                <?php echo ucfirst($MODinfo['module_display_name']); ?>
                                              </label>
                                            </div>
                                            <div class="col-md-2 col-sm-2 col-xs-12">
                                              <p>&nbsp;</p>
                                            </div>
                                            <div class="col-md-2 col-sm-2 col-xs-12">
                                              <p>&nbsp;</p>
                                            </div>
                                            <div class="col-md-2 col-sm-2 col-xs-12">
                                              <p>&nbsp;</p>
                                            </div>
                                            <div class="col-md-2 col-sm-2 col-xs-12">
                                              <p>&nbsp;</p>
                                            </div>
                                          </div>
                                        <?php else: ?>
                                          <div class="col-md-10 col-sm-10 col-xs-12">
                                            <div class="col-md-4 col-sm-4 col-xs-12 l_padding_no">
                                              <label for="mainmodule_<?php echo $mmc; ?>">
                                                <input type="checkbox" name="mainmodule_<?php echo $mmc; ?>" id="mainmodule_<?php echo $mmc; ?>" value="Y" class="parentmodule" <?php if($mainmodactive == 'Y'): echo 'checked="checked"'; endif; ?>/>
                                                <?php echo ucfirst($MODinfo['module_display_name']); ?>
                                              </label>
                                            </div>
                                            <div class="col-md-2 col-sm-2 col-xs-12 text-aline-center">
                                              <label>
                                                <input type="checkbox" name="mainmodule_view_data_<?php echo $mmc; ?>" id="mainmodule_view_data_<?php echo $mmc; ?>" value="Y" class="parentpermission" <?php if($mainmod_view_active == 'Y'): echo 'checked="checked"'; endif; ?>/>
                                              </label>
                                            </div>
                                            <div class="col-md-2 col-sm-2 col-xs-12 text-aline-center">
                                              <label>
                                                <input type="checkbox" name="mainmodule_add_data_<?php echo $mmc; ?>" id="mainmodule_add_data_<?php echo $mmc; ?>" value="Y" class="parentpermission" <?php if($mainmod_add_active == 'Y'): echo 'checked="checked"'; endif; ?>/>
                                              </label>
                                            </div>
                                            <div class="col-md-2 col-sm-2 col-xs-12 text-aline-center">
                                              <label>
                                                <input type="checkbox" name="mainmodule_edit_data_<?php echo $mmc; ?>" id="mainmodule_edit_data_<?php echo $mmc; ?>" value="Y" class="parentpermission" <?php if($mainmod_edit_active == 'Y'): echo 'checked="checked"'; endif; ?>/>
                                              </label>
                                            </div>
                                            <div class="col-md-2 col-sm-2 col-xs-12 text-aline-center">
                                              <label>
                                                <input type="checkbox" name="mainmodule_delete_data_<?php echo $mmc; ?>" id="mainmodule_delete_data_<?php echo $mmc; ?>" value="Y" class="parentpermission" <?php if($mainmod_delete_active == 'Y'): echo 'checked="checked"'; endif; ?>/>
                                              </label>
                                            </div>
                                          </div>
                                        <?php endif; ?>
                                      </div>
                                      <?php if($MODinfo['first_data']): 
                                            foreach($MODinfo['first_data'] as $CDinfo):
                                              $fcmc                      =   $CDinfo->module_id; 
                                              if($_POST['firstchildmodule_'.$mmc.'_'.$fcmc]):
                                                $firstchildmodactive                 =   'Y';
                                                if($_POST['firstchildmodule_view_data_'.$mmc.'_'.$fcmc]):
                                                  $firstchildmod_view_active         =   'Y';
                                                else:
                                                  $firstchildmod_view_active         =   'N';
                                                endif;
                                                if($_POST['firstchildmodule_add_data_'.$mmc.'_'.$fcmc]):
                                                  $firstchildmod_add_active          =   'Y';
                                                else:
                                                  $firstchildmod_add_active          =   'N';
                                                endif;
                                                if($_POST['firstchildmodule_edit_data_'.$mmc.'_'.$fcmc]):
                                                  $firstchildmod_edit_active         =   'Y';
                                                else:
                                                  $firstchildmod_edit_active         =   'N';
                                                endif;
                                                if($_POST['firstchildmodule_delete_data_'.$mmc.'_'.$fcmc]):
                                                  $firstchildmod_delete_active       =   'Y';
                                                else:
                                                  $firstchildmod_delete_active       =   'N';
                                                endif;
                                              elseif($MODULEDATA['firstchildmodule_'.$mmc.'_'.$fcmc]):
                                                $firstchildmodactive                 =   'Y';
                                                if($MODULEDATA['firstchildmodule_view_data_'.$mmc.'_'.$fcmc]):
                                                  $firstchildmod_view_active         =   'Y';
                                                else:
                                                  $firstchildmod_view_active         =   'N';
                                                endif;
                                                if($MODULEDATA['firstchildmodule_add_data_'.$mmc.'_'.$fcmc]):
                                                  $firstchildmod_add_active          =   'Y';
                                                else:
                                                  $firstchildmod_add_active          =   'N';
                                                endif;
                                                if($MODULEDATA['firstchildmodule_edit_data_'.$mmc.'_'.$fcmc]):
                                                  $firstchildmod_edit_active         =   'Y';
                                                else:
                                                  $firstchildmod_edit_active         =   'N';
                                                endif;
                                                if($MODULEDATA['firstchildmodule_delete_data_'.$mmc.'_'.$fcmc]):
                                                  $firstchildmod_delete_active       =   'Y';
                                                else:
                                                  $firstchildmod_delete_active       =   'N';
                                                endif;
                                              else:
                                                $firstchildmodactive                 =   'N';
                                                $firstchildmod_view_active           =   'N';
                                                $firstchildmod_add_active            =   'N';
                                                $firstchildmod_edit_active           =   'N';
                                                $firstchildmod_delete_active         =   'N';
                                              endif;
                                        ?>
                                        <div class="module_rows firstsubmodul">
                                          <div class="row inside-row">
                                            <div class="col-md-2 col-sm-2 col-xs-12">
                                              <div class="col-md-12 ol-sm-12col-xs-12 no-boder">
                                                <p>&nbsp;</p>
                                              </div>
                                            </div>
                                            <?php if($CDinfo->second_data): ?>
                                              <div class="col-md-10 col-sm-10 col-xs-12">
                                                <div class="f_check_repeat col-md-4 col-sm-4 col-xs-12 l_padding_15">
                                                  <label for="firstchildmodule_<?php echo $mmc;?>_<?php echo $fcmc;?>">
                                                    <input type="checkbox" name="firstchildmodule_<?php echo $mmc;?>_<?php echo $fcmc;?>" id="firstchildmodule_<?php echo $mmc;?>_<?php echo $fcmc;?>" value="Y" class="firstchildmodule"  <?php if($firstchildmodactive == 'Y'): echo 'checked="checked"'; endif; ?>/>
                                                    <?php echo ucfirst($CDinfo->module_display_name); ?>
                                                  </label>
                                                </div>
                                                <div class="f_check_repeat col-md-2 col-sm-2 col-xs-12">
                                                  <p>&nbsp;</p>
                                                </div>
                                                <div class="f_check_repeat col-md-2 col-sm-2 col-xs-12">
                                                  <p>&nbsp;</p>
                                                </div>
                                                <div class="f_check_repeat col-md-2 col-sm-2 col-xs-12">
                                                  <p>&nbsp;</p>
                                                </div>
                                                <div class="f_check_repeat col-md-2 col-sm-2 col-xs-12">
                                                  <p>&nbsp;</p>
                                                </div>
                                              </div>
                                            <?php else: ?>
                                              <div class="col-md-10 col-sm-10 col-xs-12">
                                                <div class="f_check_repeat col-md-4 col-sm-4 col-xs-12 l_padding_15">
                                                  <label for="firstchildmodule_<?php echo $mmc;?>_<?php echo $fcmc;?>">
                                                    <input type="checkbox" name="firstchildmodule_<?php echo $mmc;?>_<?php echo $fcmc;?>" id="firstchildmodule_<?php echo $mmc;?>_<?php echo $fcmc;?>" value="Y" class="firstchildmodule"  <?php if($firstchildmodactive == 'Y'): echo 'checked="checked"'; endif; ?>/>
                                                    <?php echo ucfirst($CDinfo->module_display_name); ?>
                                                  </label>
                                                </div>
                                                <div class="f_check_repeat col-md-2 col-sm-2 col-xs-12 text-aline-center">
                                                  <label>
                                                    <input type="checkbox" name="firstchildmodule_view_data_<?php echo $mmc; ?>_<?php echo $fcmc;?>" id="firstchildmodule_view_data_<?php echo $mmc; ?>_<?php echo $fcmc;?>" value="Y" class="firstchildpermission" <?php if($firstchildmod_view_active == 'Y'): echo 'checked="checked"'; endif; ?>>
                                                  </label>
                                                </div>
                                                <div class="f_check_repeat col-md-2 col-sm-2 col-xs-12 text-aline-center">
                                                  <label>
                                                    <input type="checkbox" name="firstchildmodule_add_data_<?php echo $mmc; ?>_<?php echo $fcmc;?>" id="firstchildmodule_add_data_<?php echo $mmc; ?>_<?php echo $fcmc;?>" value="Y" class="firstchildpermission" <?php if($firstchildmod_add_active == 'Y'): echo 'checked="checked"'; endif; ?>>
                                                  </label>
                                                </div>
                                                <div class="f_check_repeat col-md-2 col-sm-2 col-xs-12 text-aline-center">
                                                  <label>
                                                    <input type="checkbox" name="firstchildmodule_edit_data_<?php echo $mmc; ?>_<?php echo $fcmc;?>" id="firstchildmodule_edit_data_<?php echo $mmc; ?>_<?php echo $fcmc;?>" value="Y" class="firstchildpermission" <?php if($firstchildmod_edit_active == 'Y'): echo 'checked="checked"'; endif; ?>>
                                                  </label>
                                                </div>
                                                <div class="f_check_repeat col-md-2 col-sm-2 col-xs-12 text-aline-center">
                                                  <label>
                                                    <input type="checkbox" name="firstchildmodule_delete_data_<?php echo $mmc; ?>_<?php echo $fcmc;?>" id="firstchildmodule_delete_data_<?php echo $mmc; ?>_<?php echo $fcmc;?>" value="Y" class="firstchildpermission" <?php if($firstchildmod_delete_active == 'Y'): echo 'checked="checked"'; endif; ?>>
                                                  </label>
                                                </div>
                                              </div>
                                            <?php endif; ?>
                                          </div>
                                            <?php if($CDinfo->second_data): 
                                                foreach($CDinfo->second_data as $CCDinfo):
                                                  $scmc                      =   $CCDinfo->module_id; 
                                                  if($_POST['secondchildmodule_'.$mmc.'_'.$fcmc.'_'.$scmc]):
                                                    $secondchildmodactive                 =   'Y';
                                                    if($_POST['secondchildmodule_view_data_'.$mmc.'_'.$fcmc.'_'.$scmc]):
                                                      $secondchildmod_view_active         =   'Y';
                                                    else:
                                                      $secondchildmod_view_active         =   'N';
                                                    endif;
                                                    if($_POST['secondchildmodule_add_data_'.$mmc.'_'.$fcmc.'_'.$scmc]):
                                                      $secondchildmod_add_active          =   'Y';
                                                    else:
                                                      $secondchildmod_add_active          =   'N';
                                                    endif;
                                                    if($_POST['secondchildmodule_edit_data_'.$mmc.'_'.$fcmc.'_'.$scmc]):
                                                      $secondchildmod_edit_active         =   'Y';
                                                    else:
                                                      $secondchildmod_edit_active         =   'N';
                                                    endif;
                                                    if($_POST['secondchildmodule_delete_data_'.$mmc.'_'.$fcmc.'_'.$scmc]):
                                                      $secondchildmod_delete_active       =   'Y';
                                                    else:
                                                      $secondchildmod_delete_active       =   'N';
                                                    endif;
                                                  elseif($MODULEDATA['secondchildmodule_'.$mmc.'_'.$fcmc.'_'.$scmc]):
                                                    $secondchildmodactive                 =   'Y';
                                                    if($MODULEDATA['secondchildmodule_view_data_'.$mmc.'_'.$fcmc.'_'.$scmc]):
                                                      $secondchildmod_view_active         =   'Y';
                                                    else:
                                                      $secondchildmod_view_active         =   'N';
                                                    endif;
                                                    if($MODULEDATA['secondchildmodule_add_data_'.$mmc.'_'.$fcmc.'_'.$scmc]):
                                                      $secondchildmod_add_active          =   'Y';
                                                    else:
                                                      $secondchildmod_add_active          =   'N';
                                                    endif;
                                                    if($MODULEDATA['secondchildmodule_edit_data_'.$mmc.'_'.$fcmc.'_'.$scmc]):
                                                      $secondchildmod_edit_active         =   'Y';
                                                    else:
                                                      $secondchildmod_edit_active         =   'N';
                                                    endif;
                                                    if($MODULEDATA['secondchildmodule_delete_data_'.$mmc.'_'.$fcmc.'_'.$scmc]):
                                                      $secondchildmod_delete_active       =   'Y';
                                                    else:
                                                      $secondchildmod_delete_active       =   'N';
                                                    endif;
                                                  else:
                                                    $secondchildmodactive                 =   'N';
                                                    $secondchildmod_view_active           =   'N';
                                                    $secondchildmod_add_active            =   'N';
                                                    $secondchildmod_edit_active           =   'N';
                                                    $secondchildmod_delete_active         =   'N';
                                                  endif;
                                            ?>
                                            <div class="row inside-row secondsubmodul">
                                              <div class="col-md-2 col-sm-2 col-xs-12">
                                                <div class="col-md-12 ol-sm-12col-xs-12 no-boder">
                                                  <p>&nbsp;</p>
                                                </div>
                                              </div>
                                              <div class="col-md-10 col-sm-10 col-xs-12">
                                                <div class="s_check_repeat col-md-4 col-sm-4 col-xs-12 l_padding_40">
                                                  <label for="secondchildmodule_<?php echo $mmc;?>_<?php echo $fcmc;?>_<?php echo $scmc;?>">
                                                    <input type="checkbox" name="secondchildmodule_<?php echo $mmc;?>_<?php echo $fcmc;?>_<?php echo $scmc;?>" id="secondchildmodule_<?php echo $mmc;?>_<?php echo $fcmc;?>_<?php echo $scmc;?>" value="Y" class="secondchildmodule"  <?php if($secondchildmodactive == 'Y'): echo 'checked="checked"'; endif; ?>/>
                                                    <?php echo ucfirst($CCDinfo->module_display_name); ?>
                                                  </label>
                                                </div>
                                                <div class="s_check_repeat col-md-2 col-sm-2 col-xs-12 text-aline-center">
                                                  <label>
                                                    <input type="checkbox" name="secondchildmodule_view_data_<?php echo $mmc; ?>_<?php echo $fcmc;?>_<?php echo $scmc;?>" id="secondchildmodule_view_data_<?php echo $mmc; ?>_<?php echo $fcmc;?>_<?php echo $scmc;?>" value="Y" class="secondchildpermission" <?php if($secondchildmod_view_active == 'Y'): echo 'checked="checked"'; endif; ?>>
                                                  </label>
                                                </div>
                                                <div class="s_check_repeat col-md-2 col-sm-2 col-xs-12 text-aline-center">
                                                  <label>
                                                    <input type="checkbox" name="secondchildmodule_add_data_<?php echo $mmc; ?>_<?php echo $fcmc;?>_<?php echo $scmc;?>" id="secondchildmodule_add_data_<?php echo $mmc; ?>_<?php echo $fcmc;?>_<?php echo $scmc;?>" value="Y" class="secondchildpermission" <?php if($secondchildmod_add_active == 'Y'): echo 'checked="checked"'; endif; ?>>
                                                  </label>
                                                </div>
                                                <div class="s_check_repeat col-md-2 col-sm-2 col-xs-12 text-aline-center">
                                                  <label>
                                                    <input type="checkbox" name="secondchildmodule_edit_data_<?php echo $mmc; ?>_<?php echo $fcmc;?>_<?php echo $scmc;?>" id="secondchildmodule_edit_data_<?php echo $mmc; ?>_<?php echo $fcmc;?>_<?php echo $scmc;?>" value="Y" class="secondchildpermission" <?php if($secondchildmod_edit_active == 'Y'): echo 'checked="checked"'; endif; ?>>
                                                  </label>
                                                </div>
                                                <div class="s_check_repeat col-md-2 col-sm-2 col-xs-12 text-aline-center">
                                                  <label>
                                                    <input type="checkbox" name="secondchildmodule_delete_data_<?php echo $mmc; ?>_<?php echo $fcmc;?>_<?php echo $scmc;?>" id="secondchildmodule_delete_data_<?php echo $mmc; ?>_<?php echo $fcmc;?>_<?php echo $scmc;?>" value="Y" class="secondchildpermission" <?php if($secondchildmod_delete_active == 'Y'): echo 'checked="checked"'; endif; ?>>
                                                  </label>
                                                </div>
                                              </div>
                                            </div>
                                          <?php endforeach; endif; ?>
                                        </div>
                                      <?php endforeach; endif; ?>
                                    </div>
                                  <?php $i++; endforeach; endif; ?>
                                </div>
                              </div>
                            </div>
                          </div>
                        </fieldset>
                      </div>
                    </div>
                    <div class="row">
                      <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="inline-remember-me mt-4">
                          <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                          <button class="btn btn-primary mb-4">Submit</button>
                          <a href="<?php echo correctLink('usersCMPOPData',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
                          <span class="tools pull-right">Note:- <strong><span style="color:#FF0000;">*</span> Indicates Required Fields</strong> </span> 
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    // Form Validation
    $("#currentPageFormSubadmin").validate({
      rules:{
        password: { minlength: 6, maxlength: 25 },
        conf_password: { minlength: 6, equalTo: "#password", }
      },
      messages: {
        password: { minlength: "Password at least 6 chars!" },
        conf_password: { equalTo: "Password fields have to match !!", minlength: "Confirm password at least 6 chars!" }
      },
      errorClass: "help-inline",
      errorElement: "span",
      highlight:function(element, errorClass, validClass) {
        $(element).parents('.form-group-inner').removeClass('success');
        $(element).parents('.form-group-inner').addClass('error');
      },
      unhighlight: function(element, errorClass, validClass) {
        $(element).parents('.form-group-inner').removeClass('error');
        $(element).parents('.form-group-inner').addClass('success');
      }
    });

    jQuery.validator.addMethod("numberonly", function(value, element) 
    {
      return this.optional(element) || /^[0-9]+$/i.test(value);
    }, "Number only please");
    jQuery.validator.addMethod("alphanumeric", function(value, element) 
    {
      return this.optional(element) || /^([a-zA-Z0-9]+)$/i.test(value);
    }, "Alphanumeric only please");
  });
</script>
<script>
  $(document).on('click','#currentPageFormSubadmin .parentmodule',function(){ 
    var curobj  = $(this); 
    if(curobj.prop("checked") == true){ 
      curobj.closest('.modul').find('.parentpermission').prop('checked', true);
      curobj.closest('.modul').children('.firstsubmodul').find('.firstchildmodule').prop('checked', true);
      curobj.closest('.modul').children('.firstsubmodul').find('.firstchildpermission').prop('checked', true);
      curobj.closest('.modul').find('.secondsubmodul').find('.secondchildmodule').prop('checked', true);
      curobj.closest('.modul').find('.secondsubmodul').find('.secondchildpermission').prop('checked', true);
    }
    else if(curobj.prop("checked") == false){
      curobj.closest('.modul').find('.parentpermission').prop('checked', false);
      curobj.closest('.modul').children('.firstsubmodul').find('.firstchildmodule').prop('checked', false);
      curobj.closest('.modul').children('.firstsubmodul').find('.firstchildpermission').prop('checked', false);
      curobj.closest('.modul').find('.secondsubmodul').find('.secondchildmodule').prop('checked', false);
      curobj.closest('.modul').find('.secondsubmodul').find('.secondchildpermission').prop('checked', false);
    }
  });

  $(document).on('click','#currentPageFormSubadmin .parentpermission',function(){
    var curobj  = $(this); 
    var count = 0;
    curobj.closest('.modul').find('.parentpermission').each(function(){
      if($(this).prop("checked") == true){
        count = 1;
      }
    });
    if(count == 1){
      curobj.closest('.modul').find('.parentmodule').prop('checked', true);
    }
    else {
      curobj.closest('.modul').find('.parentmodule').prop('checked', false);
    }
  });

  $(document).on('click','#currentPageFormSubadmin .firstchildmodule',function(){ 
    var curobj  = $(this);  
    if(curobj.prop("checked") == true){
      curobj.closest('.firstsubmodul').find('.firstchildpermission').prop('checked', true);
      curobj.closest('.firstsubmodul').children('.secondsubmodul').find('.secondchildmodule').prop('checked', true);
      curobj.closest('.firstsubmodul').children('.secondsubmodul').find('.secondchildpermission').prop('checked', true);
    }
    else if(curobj.prop("checked") == false){
      curobj.closest('.firstsubmodul').find('.firstchildpermission').prop('checked', false);
      curobj.closest('.firstsubmodul').children('.secondsubmodul').find('.secondchildmodule').prop('checked', false);
      curobj.closest('.firstsubmodul').children('.secondsubmodul').find('.secondchildpermission').prop('checked', false);
    }
    var count = 0;
    curobj.closest('.modul').find('.firstchildmodule').each(function(){
      if($(this).prop("checked") == true){
        count = 1;
      }
    });  
    if(count == 1){
      curobj.closest('.modul').find('.parentmodule').prop('checked', true);
    }
    else {
      curobj.closest('.modul').find('.parentmodule').prop('checked', false);
    }
  });

  $(document).on('click','#currentPageFormSubadmin .firstchildpermission',function(){
    var curobj  = $(this); 
    var count = 0;
    curobj.closest('.firstsubmodul').find('.firstchildpermission').each(function(){
      if($(this).prop("checked") == true){
        count = 1;
      }
    });
    if(count == 1){
      curobj.closest('.firstsubmodul').find('.firstchildmodule').prop('checked', true);
    }
    else {
      curobj.closest('.firstsubmodul').find('.firstchildmodule').prop('checked', false);
    }
    var counts  = 0;
    curobj.closest('.modul').find('.firstchildmodule').each(function(){
      if($(this).prop("checked") == true){
        counts = 1;
      }
    });  
    if(counts == 1){
      curobj.closest('.modul').find('.parentmodule').prop('checked', true);
    }
    else {
      curobj.closest('.modul').find('.parentmodule').prop('checked', false);
    }
  });

  $(document).on('click','#currentPageFormSubadmin .secondchildmodule',function(){ 
    var curobj  = $(this);  
    if(curobj.prop("checked") == true){
      curobj.closest('.secondsubmodul').find('.secondchildpermission').prop('checked', true);
    }
    else if(curobj.prop("checked") == false){
      curobj.closest('.secondsubmodul').find('.secondchildpermission').prop('checked', false);
    }
    var count = 0;
    curobj.closest('.firstsubmodul').find('.secondchildmodule').each(function(){
      if($(this).prop("checked") == true){
        count = 1;
      }
    }); 
    if(count == 1){
      curobj.closest('.firstsubmodul').find('.firstchildmodule').prop('checked', true);
    }
    else {
      curobj.closest('.firstsubmodul').find('.firstchildmodule').prop('checked', false);
    }
    var count1 = 0;
    curobj.closest('.modul').find('.firstchildmodule').each(function(){
      if($(this).prop("checked") == true){
        count1 = 1;
      }
    });  
    if(count1 == 1){
      curobj.closest('.modul').find('.parentmodule').prop('checked', true);
    }
    else {
      curobj.closest('.modul').find('.parentmodule').prop('checked', false);
    }
  });
  
  $(document).on('click','#currentPageFormSubadmin .secondchildpermission',function(){
    var curobj  = $(this); 
    var count = 0;
    curobj.closest('.secondsubmodul').find('.secondchildpermission').each(function(){
      if($(this).prop("checked") == true){
        count = 1;
      }
    });
    if(count == 1){
      curobj.closest('.secondsubmodul').find('.secondchildmodule').prop('checked', true);
    }
    else {
      curobj.closest('.secondsubmodul').find('.secondchildmodule').prop('checked', false);
    }
    var count = 0;
    curobj.closest('.firstsubmodul').find('.secondchildmodule').each(function(){
      if($(this).prop("checked") == true){
        count = 1;
      }
    }); 
    if(count == 1){
      curobj.closest('.firstsubmodul').find('.firstchildmodule').prop('checked', true);
    }
    else {
      curobj.closest('.firstsubmodul').find('.firstchildmodule').prop('checked', false);
    }
    var count1 = 0;
    curobj.closest('.modul').find('.firstchildmodule').each(function(){
      if($(this).prop("checked") == true){
        count1 = 1;
      }
    });  
    if(count1 == 1){
      curobj.closest('.modul').find('.parentmodule').prop('checked', true);
    }
    else {
      curobj.closest('.modul').find('.parentmodule').prop('checked', false);
    }
  });
</script>