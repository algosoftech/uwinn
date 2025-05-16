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
                          
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('TGPNOTIFICATIONDATA',getCurrentControllerPath('index')); ?>">Push Notification</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Notification</a></li>
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
                <h5><?=$EDITDATA?'Edit':'Add'?> Notification</h5>
                <a href="<?php echo correctLink('TGPNOTIFICATIONDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
              </div>
              <div class="card-body">
                <div class="basic-login-inner">
                  <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="notification_temp_id"/>
                    <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['notification_temp_id']?>"/>
                    <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['notification_temp_id']?>"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                    <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('title')): ?>error<?php endif; ?>">
                        <label>Notification Type<span class="required">*</span></label><br>
                        <?php if(set_value('notific_type')): $notific_type = set_value('notific_type'); endif; ?>
                        <select name="notific_type" id="notific_type" class="form-control required">
                          <option value="">Select type</option>
                          <option value="individual-user" <?php if($notific_type == 'individual-user'): echo 'selected="selected"'; endif; ?>>Individual user</option>
                          <option value="all-users" <?php if($notific_type == 'all-users'): echo 'selected="selected"'; endif; ?>>Broadcast to all</option>
                          <!-- <option value="new-campaign" <?php if($notific_type == 'new-campaign'): echo 'selected="selected"'; endif; ?>>New campaign</option>
                          <option value="new-0ffers-discounts" <?php if($notific_type == 'new-0ffers-discounts '): echo 'selected="selected"'; endif; ?>>New Offers & Discounts </option> -->
                        </select>
                        <?php if(form_error('notific_type')): ?>
                          <label for="notific_type" generated="true" class="error"><?php echo form_error('notific_type'); ?></label>
                        <?php endif; ?>
                      </div>
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('title')): ?>error<?php endif; ?>" id="user_list">
                        <label>User Name<span class="required">*</span></label><br>
                        <?php if(set_value('student_id')): $student_ids = $_POST['student_id']; else: $student_ids = array(); endif; ?>
                        <select name="student_id[]" id="student_id" class="form-control select-search required" multiple="">
                          <option value="">Select Users</option>
                          <?php if($usersdata <> ""): foreach($usersdata as $user):    ?>
                            <option value="<?php echo $user['users_id']; ?>" <?php if(in_array($user['users_id'],$student_ids)): echo "selected"; endif; if($user['sms_notification']== 'off' ): echo "disabled"; endif; ?>  ><?php echo ucfirst($user['users_name']).' ('.$user['users_email'].')'.' ('.$user['users_mobile'].')'; ?></option>
                          <?php endforeach; endif; ?>
                        </select>
                        <?php if(form_error('student_id')): ?>
                          <label for="student_id" generated="true" class="error"><?php echo form_error('student_id'); ?></label>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('title')): ?>error<?php endif; ?>">
                        <label>Title<span class="required">*</span></label>
                        <input type="text" name="notific_title" id="notific_title" class="form-control required" value="<?php if(set_value('notific_title')): echo set_value('notific_title'); else: echo stripslashes($EDITDATA['notific_title']);endif; ?>" placeholder="Title">
                        <?php if(form_error('notific_title')): ?>
                        <label for="notific_title" generated="true" class="error"><?php echo form_error('notific_title'); ?></label>
                        <?php endif; ?>
                      </div>
                      <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('title')): ?>error<?php endif; ?>">
                        <label>Message<span class="required">*</span></label>
                        <input type="text" name="notific_message" id="notific_message" class="form-control required" value="<?php if(set_value('notific_message')): echo set_value('notific_message'); else: echo stripslashes($EDITDATA['notific_message']);endif; ?>" placeholder="Message">
                        <?php if(form_error('notific_message')): ?>
                        <label for="notific_message" generated="true" class="error"><?php echo form_error('notific_message'); ?></label>
                        <?php endif; ?>
                      </div>

                      <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('link')): ?>error<?php endif; ?>">
                        <label>Link<span class="required">*</span></label>
                        <input type="text" name="link" id="link" class="form-control" value="<?php if(set_value('link')): echo set_value('link'); else: echo stripslashes($EDITDATA['link']);endif; ?>" placeholder="Link">
                        <?php if(form_error('link')): ?>
                        <label for="link" generated="true" class="error"><?php echo form_error('link'); ?></label>
                        <?php endif; ?>
                      </div>

                      <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('image')): ?>error<?php endif; ?>">
                        <label>Image<span class="required">*</span></label>
                        <input type="file" name="image" id="image" class="form-control" value="<?php if(set_value('image')): echo set_value('image'); else: echo stripslashes($EDITDATA['notific_title']);endif; ?>" placeholder="Title">
                        <?php if(form_error('image')): ?>
                        <label for="image" generated="true" class="error"><?php echo form_error('image'); ?></label>
                        <?php endif; ?>
                      </div>
                    </div>

                    <div class="row">
                      <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="inline-remember-me mt-4">
                          <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                          <button class="btn btn-primary mb-4">Send</button>
                          <a href="<?php echo correctLink('TGPNOTIFICATIONDATA',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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
<link href="{ASSET_INCLUDE_URL}dist/css/fSelect.css" rel="stylesheet">
<script src="{ASSET_INCLUDE_URL}dist/js/fSelect.js"></script> 
<script type="text/javascript">
  $(document).ready(function(){  
    $('.select-search').fSelect();
  });
</script>
<script>
  $('#select_all').click(function() {
    alert('working');
        $('#student_id option').prop('selected', true);
    });
</script>
<script>
  $(document).ready(function(){
    $('#notific_type').change(function(){
      var value = $('#notific_type').val();
      if(value === 'all-users'){
        $('#user_list').css('display','none');
      } else {
        $('#user_list').css('display','block');
      }
      
    });
  });
</script>
<?php /* ?>
<script type="text/javascript">
  $(function(){create_editor_for_textarea('description')});
</script>
<script>

  function ImageDelete(imageName,id)
  {//alert(id);
    //var a = FULLSITEURL+'website/'+CURRENTCLASS+'/imageDelete';alert(a);
    if(confirm("Sure to delete?"))
    {//alert(CURRENTCLASS);
      $.ajax({
            type: 'post',
             url: FULLSITEURL+'website/'+CURRENTCLASS+'/imageDelete',
            data: {imageName:imageName,id,id},
         success: function(rdata) { 
              if(parseInt(rdata.status) == 1) {
                $('#image').val('');
                $('#ImageDiv').html('');
              }
              return false;
            }
      });
    }
  }
</script>
<?php */ ?>