<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script>
    $(function(){
     $("#date").datepicker({dateFormat:'yy-mm-dd',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
 });
</script>
<div class="pcoded-main-container">
    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title"></div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo base_url('maindashboard'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLSALESDATA',getCurrentControllerPath('index')); ?>"> User</a></li>
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
                        <a href="<?php echo correctLink('ALLSALESDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>
                    <div class="card-body">
                        <div class="basic-login-inner">
                            <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="users_id"/>
                                <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['users_id']?>"/>
                                <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['users_id']?>"/>
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">

                                <div class="row">
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('category_id')): ?>error<?php endif; ?>">
                                        <label>Select Super User<span class="required">*</span></label>
                                        <select name="user_type" id="user_type" class="form-control required">
                                            <option >Select user type</option>
                                            <option value="Super Retailer">Super Retailer</option>
                                            <option value="Super Salesperson">Super Salesperson</option>
                                        </select>
                                        <?php if(form_error('user_type')): ?>
                                          <span for="user_type" generated="true" class="help-inline"><?php echo form_error('user_type'); ?></span>
                                      <?php endif; ?>
                                  </div>
                                 
                                  <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 error" id="user_list">
                                    <label>Show/Hide All User <span class="required">*</span></label>
                                    <?php if(set_value('selected_user')): $selected_users = $_POST['selected_user']; else: $selected_users = array(); endif; ?>
                                    <select name="selected_user[]" id="selected_user" class="form-control required">
                                        <option value="Show All">Show All</option>
                                        <option value="Show Seleted only">Show Selected only</option>
                                    </select>
                                    <?php if(form_error('selected_user')): ?>
                                    <label for="selected_user" generated="true" class="error"><?php echo form_error('selected_user'); ?></label>
                                    <?php endif; ?>
                                  </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-12">

                                    <?php foreach ($salesPerson as $key => $user): ?>
                                     <fieldset>

                                        <legend>
                                            <h6><?php if($user['users_name']): echo $user['users_name']; endif; ?>    
                                            <?php if($user['users_mobile']): echo '('.$user['users_mobile'].')'; endif; ?>    
                                            <?php if($user['users_email']):  echo '('.$user['users_email'].')';  endif; ?>    </h6>
                                        </legend>
                                        <div class="row">
                                            <div class="col-sm-3 col-md-3 col-lg-3">
                                                <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" value="show_all_<?=$key?>" class="show_all" id="show_all_<?=$key?>">
                                                  <label class="form-check-label" for="show_all_<?=$key?>">
                                                    Show All
                                                  </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-3 col-md-3 col-lg-3">
                                                <div class="form-check">
                                                  <input class="form-check-input" type="checkbox" data-user-id="<?=$user['users_id'];?>" data- ="" id="selected_only_<?=$key?>">
                                                  <label class="form-check-label" for="selected_only_<?=$key?>">
                                                    Selected Only
                                                  </label>
                                                </div>
                                            </div>


                                            <?php

                                                $tableName      = "da_users";
                                                $where['where'] =   array(
                                                      'status'=>'A',
                                                      'bind_person_id' => (string)$user['users_id']
                                                    );
                                                $shortField             =   array('_id'=> -1);
                                                $fields                 =   array('users_id','users_name','users_mobile','users_email','users_type');
                                                $binded_user  = $this->common_model->getDataByNewQuery($fields,'multiple','da_users',$where,$shortField);


                                            ?>

                                            <?php if($binded_user): ?>
                                            <div class="col-sm-12 col-md-12 col-lg-12">
                                             <div class="row">
                                                <div class="col-sm-12 col-md-12 col-lg-12"> 
                                                    <hr>
                                                </div>
                                                <?php foreach ($binded_user as $key => $item): ?>
                                                    <div class="col-sm-12 col-md-12 col-lg-12"> 
                                                        <div class="form-check">
                                                          <input class="form-check-input" type="checkbox" data-user-id="<?=$item['users_id'];?>" data-binded-user-id="<?=$user['users_id'];?>" id="selected_only_<?=$key?>">
                                                          <label class="form-check-label" for="selected_only_<?=$key?>"> 
                                                            <?php if($item['users_name']): echo $item['users_name']; endif; ?>    
                                                            <?php if($item['users_mobile']): echo '('.$item['users_mobile'].')'; endif; ?>    
                                                            <?php if($item['users_email']):  echo '('.$item['users_email'].')';  endif; ?>     </label>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                             </div>
                                            </div>
                                            <?php endif; ?>

                                        </div>

                                    </fieldset>
                                <?php endforeach; ?>

                                </div>
                            </div>












                      <div class="row">
                        <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="inline-remember-me mt-4">
                                <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                <button class="btn btn-primary mb-4 submit-btn">Submit</button>
                                <a href="<?php echo correctLink('ALLSALESDATA',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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

$(".selected_user").on('change' , function(){

   let Selected_user = $(this).val();
    console.log();

});

   
</script>
