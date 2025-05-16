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
                        <div class="page-header-title">
                            <?php /* ?>
                            <h5 class="m-b-10">Welcome <?=sessionData('HCAP_ADMIN_FIRST_NAME')?></h5>
                            <?php */ ?>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
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
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('users_name')): ?>error<?php endif; ?>">
                                        <label>Name<span class="required">*</span></label>
                                        <input type="text" name="users_name" id="users_name" class="form-control required" value="<?php if(set_value('users_name')): echo set_value('users_name'); else: echo stripslashes($EDITDATA['users_name']);endif; ?>" placeholder="Name">
                                        <?php if(form_error('users_name')): ?>
                                        <span for="name" generated="true" class="help-inline"><?php echo form_error('users_name'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('last_name')): ?>error<?php endif; ?>">
                                        <label>Last Name<span class="required">*</span></label>
                                        <input type="text" name="last_name" id="last_name" class="form-control required" value="<?php if(set_value('last_name')): echo set_value('last_name'); else: echo stripslashes($EDITDATA['last_name']);endif; ?>" placeholder="Name">
                                        <?php if(form_error('last_name')): ?>
                                        <span for="name" generated="true" class="help-inline"><?php echo form_error('last_name'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('category_id')): ?>error<?php endif; ?>">
                                    <label>User Type<span class="required">*</span></label>
                                    <select name="user_type" id="user_type" class="form-control required">
                                        <option >Select user type</option>
                                        
                                        <option value="Freelancer" <?php if ($EDITDATA['users_type'] == 'Freelancer') {?> selected <?php } ?>>Freelancer</option>

                                        <option value="Sales Person" <?php if ($EDITDATA['users_type'] == 'Sales Person') {?> selected <?php } ?>>Sales Person</option>
                                        
                                        <option value="Retailer" <?php if ($EDITDATA['users_type'] == 'Retailer') {?> selected <?php } ?>>Retailer</option>
                                        
                                        <option value="Promoter" <?php if ($EDITDATA['users_type'] == 'Promoter') {?> selected <?php } ?>>Promoter</option>

                                        <option value="Users" <?php if ($EDITDATA['users_type'] == 'Users') {?> selected <?php } ?>>Users</option>
                                        
                                        <option value="Admin" <?php if ($EDITDATA['users_type'] == 'Admin') {?> selected <?php } ?>>Admin</option>
                                       
                                        <option value="Freelancer Promoter" <?php if ($EDITDATA['users_type'] == 'Freelancer Promoter') {?> selected <?php } ?>>Freelancer Promoter</option>

                                    </select>
                                    <?php if(form_error('user_type')): ?>
                                      <span for="user_type" generated="true" class="help-inline"><?php echo form_error('user_type'); ?></span>
                                    <?php endif; ?>
                                  </div>

                                  <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12" id="area_block">
                                
                                    <label>Area<span class="required">*</span></label>
                                    <input type="text" name="area" id="area" class="form-control" value="<?php if(set_value('area')): echo set_value('area'); else: echo stripslashes($EDITDATA['area']);endif; ?>" placeholder="Area">
                                    <?php if(form_error('area')): ?>
                                    <span for="name" generated="true" class="help-inline"><?php echo form_error('area'); ?></span>
                                    <?php endif; ?>
                                  </div>

                                </div>
                                
                                <div class="row" id="store">
                                    <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('store_name')): ?>error<?php endif; ?>" id="store_name_block">
                                        <label>Store Name<span class="required">*</span></label>
                                        <input type="text" name="store_name" id="store_name" class="form-control required" value="<?php if(set_value('store_name')): echo set_value('store_name'); else: echo stripslashes($EDITDATA['store_name']);endif; ?>" placeholder="Name">
                                        <?php if(form_error('store_name')): ?>
                                        <span for="name" generated="true" class="help-inline"><?php echo form_error('store_name'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('bind_user_type')): ?>error<?php endif; ?>">
                                        <label>Bind with<span class="required">*</span></label>
                                        <select name="bind_user_type" id="bind_user_type" class="form-control required">
                                            <option value="Sales Person" <?php if ($EDITDATA['bind_user_type'] == 'Sales Person') {?> selected <?php } ?>>Sales Person</option>
                                            <option value="Freelancer" <?php if ($EDITDATA['bind_user_type'] == 'Freelancer') {?> selected <?php } ?>>Freelancer</option>
                                            <!-- <option value="Retailer" <?php if ($EDITDATA['bind_user_type'] == 'Retailer') {?> selected <?php } ?>>Retailer</option> -->
                                        </select>
                                        <?php if(form_error('bind_user_type')): ?>
                                        <span for="bind_user_type" generated="true" class="help-inline"><?php echo form_error('bind_user_type'); ?></span>
                                        <?php endif; ?>
                                    </div>


                                    <!-- Sales Person List -->
                                    <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('sales_man')): ?>error<?php endif; ?>" id="sales_person_block">
                                        <label>Select Sales Person<span class="required">*</span></label>
                                        <input type="text" list="sales_man_list" name="sales_person" id="sales_person" class="form-control required" value="<?php if(isset($EDITDATA['bind_person_id'])): echo $EDITDATA['bind_person_id'].'|'.$EDITDATA['bind_person_name']; endif; ?>" placeholder="Enter Sales Person" />
                                        <datalist id="sales_man_list">
                                            <?php foreach ($sales_man_list as $key => $item) { ?>
                                                <option value="<?php echo stripcslashes($item['users_id']).'|'.stripcslashes($item['users_name']).'|'.stripcslashes($item['users_mobile']); ?>"><?php echo stripcslashes($item['users_id']).'|'.stripcslashes($item['users_name']).'|'.stripcslashes($item['users_mobile']); ?></option>   
                                            <?php } ?>
                                        </datalist>
                                        <?php if(form_error('products_list')): ?>
                                        <span for="products_list" generated="true" class="help-inline"><?php echo form_error('products_list'); ?></span>
                                        <?php else: ?>
                                        <span for="products_list" id="products_list_error" generated="true" class="help-inline" style="color: red;"><?php if(isset($email_id_error)): echo $email_id_error; endif; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <!-- Freelancer List -->
                                    <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('sales_man')): ?>error<?php endif; ?>" id="freelancer_person_block">
                                        <label>Select Freelancer<span class="required">*</span></label>
                                        <input type="text" list="freelancer_list" name="freelancer_person" id="freelancer_person" class="form-control required" value="<?php if(isset($EDITDATA['bind_person_id'])): echo $EDITDATA['bind_person_id'].'|'.$EDITDATA['bind_person_name']; endif; ?>" placeholder="Enter Freelancer" />
                                        <datalist id="freelancer_list">
                                            <?php foreach ($freelancer_list as $key => $item) { ?>
                                                <option value="<?php echo stripcslashes($item['users_id']).'|'.stripcslashes($item['users_name']).'|'.stripcslashes($item['users_mobile']); ?>"><?php echo stripcslashes($item['users_id']).'|'.stripcslashes($item['users_name']).'|'.stripcslashes($item['users_mobile']); ?></option>   
                                            <?php } ?>
                                        </datalist>
                                        <?php if(form_error('freelancer_person')): ?>
                                            <span for="freelancer_person" generated="true" class="help-inline"><?php echo form_error('freelancer_person'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="row" id="pos">
                                     <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('pos_device_id')): ?>error<?php endif; ?>" id="pos_device_id_block">
                                        <label>POS device Id<span class="required">*</span></label>
                                        <input type="text" name="pos_device_id" id="pos_device_id" class="form-control" value="<?php if(set_value('pos_device_id')): echo set_value('pos_device_id'); else: echo stripslashes($EDITDATA['pos_device_id']);endif; ?>" placeholder="POS Device">
                                        <?php if(form_error('pos_device_id')): ?>
                                        <span for="name" generated="true" class="help-inline"><?php echo form_error('pos_device_id'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                 </div>
                                    
                                <div class="row">

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('country_code')): ?>error<?php endif; ?>">
                                        
                                        <label>Country Code<span class="required">*</span></label><br>
                                        <select name="country_code" id="country_code" class="form-control required select-search">
                                        <option value="">Select Country Code</option>
                                         <?php if($countryCodeData): foreach($countryCodeData as $countryCodeKey=>$countryCodeValue): ?>
                                                <option value="<?php echo $countryCodeKey; ?>" <?php if($EDITDATA['country_code'] == $countryCodeKey): echo 'selected="selected"'; endif; ?>><?php echo $countryCodeValue; ?></option>
                                            <?php endforeach; endif; ?>
                                        </select>
                                        <?php if(form_error('country_code')): ?>
                                        <label for="country_code" generated="true" class="error"><?php echo form_error('country_code'); ?></label>
                                        <?php endif; ?>
                                    </div>


                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('users_mobile')): ?>error<?php endif; ?>">
                                        <label>Mobile<span class="required">*</span></label>
                                        <input type="number" min="0" name="users_mobile" id="users_mobile" class="form-control required" value="<?php if(set_value('users_mobile')): echo set_value('users_mobile'); else: echo stripslashes($EDITDATA['users_mobile']);endif; ?>" placeholder="Mobile No.">
                                        <input type="number" min="0" hidden id="old_users_mobile" class="form-control required" value="<?php if(set_value('users_mobile')): echo set_value('users_mobile'); else: echo stripslashes($EDITDATA['users_mobile']);endif; ?>" placeholder="Mobile No.">
                                        <span style="color: red;" id="m_validationError"></span>
                                        <?php if(form_error('users_mobile')): ?>
                                        <span for="users_mobile" generated="true" class="help-inline"><?php echo form_error('users_mobile'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('users_email')): ?>error<?php endif; ?>">
                                        <label>Email</label>
                                        <input type="email" name="users_email" id="users_email" class="form-control" value="<?php if(set_value('users_email')): echo set_value('users_email'); else: echo stripslashes($EDITDATA['users_email']);endif; ?>" placeholder="Email">
                                        <input type="email" hidden id="old_users_email" class="form-control" value="<?php if(set_value('users_email')): echo set_value('users_email'); else: echo stripslashes($EDITDATA['users_email']);endif; ?>" placeholder="Email">
                                        <span style="color: red;" id="validationError"></span>
                                        <?php if(form_error('users_email')): ?>
                                        <span for="users_email" generated="true" class="help-inline"><?php echo form_error('users_email'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('totalArabianPoints')): ?>error<?php endif; ?>">
                                        <label>Total Arabian Points<span class="required">*</span></label>
                                        <input type="number" min="0" name="totalArabianPoints" id="totalArabianPoints" class="form-control required" value="<?php if(set_value('totalArabianPoints')): echo set_value('totalArabianPoints'); else: echo stripslashes($EDITDATA['totalArabianPoints']);endif; ?>" placeholder="Total Arabian Points" <?php 
                                        if($EDITDATA['totalArabianPoints']){ ?>
                                            readonly
                                        <?php } ?> >
                                        <?php if(form_error('totalArabianPoints')): ?>
                                        <span for="totalArabianPoints" generated="true" class="help-inline"><?php echo form_error('totalArabianPoints'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('availableArabianPoints')): ?>error<?php endif; ?>">
                                        <label>Available Arabian Points<span class="required">*</span></label>
                                        <input type="number" min="0" name="availableArabianPoints" id="availableArabianPoints" class="form-control required" value="<?php if(set_value('availableArabianPoints')): echo set_value('availableArabianPoints'); else: echo stripslashes($EDITDATA['availableArabianPoints']);endif; ?>" placeholder="Available Arabian Points" <?php 
                                        if($EDITDATA['availableArabianPoints']){ ?>
                                            readonly
                                        <?php } ?> >
                                        <?php if(form_error('availableArabianPoints')): ?>
                                        <span for="availableArabianPoints" generated="true" class="help-inline"><?php echo form_error('availableArabianPoints'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if($EDITDATA['password']): ?>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('Checkbox_password')): ?>error<?php endif; ?>">
                                        <input type="checkbox" name="Checkbox_password" id="Checkbox_password" class="form-check-input"  <?php if(set_value('Checkbox_password')): echo "checked"; endif; ?>>
                                        <label for="Checkbox_password">Add/Edit Password</label>
                                    </div>

                                <?php endif; ?>

                              
                                <div class="row password-section   <?php if(!set_value('Checkbox_password')  && $EDITDATA['password'] ): echo "d-none"; endif; ?> ">
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('password')): ?>error<?php endif; ?>">
                                        <label>Password<span class="required">*</span></label>
                                        <input type="password" name="password" id="password" class="form-control required" placeholder="Password" <?php if(!set_value('Checkbox_password') && $EDITDATA['password'] ): echo "disabled"; endif; ?> value="<?php if(set_value('Checkbox_password')): echo set_value('password'); endif; ?>"    >
                                        <?php if(form_error('password')): ?>
                                        <span for="password" generated="true" class="help-inline"><?php echo form_error('password'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('cpassword')): ?>error<?php endif; ?>">
                                        <label>Confirm Password<span class="required">*</span></label>
                                        <input type="password" name="cpassword" id="cpassword" class="form-control required" placeholder="Confirm Password" <?php if(!set_value('Checkbox_password') && $EDITDATA['password'] ): echo "disabled"; endif; ?> >
                                        <?php if(form_error('cpassword')): ?>
                                        <span for="cpassword" generated="true" class="help-inline"><?php echo form_error('cpassword'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                               
                                <div class="row">
                                    <div class="form-group-inner col-lg-3 col-md-2 col-sm-3 col-xs-12 <?php if(form_error('pickup_point_holder')): ?>error<?php endif; ?>">
                                        <label>Pickup Point Holder<span class="required">*</span></label>
                                        <select name="pickup_point_holder" id="pickup_point_holder" class="form-control required">
                                            <option value="N" <?php if ($EDITDATA['pickup_point_holder'] == 'N') {?> selected <?php } ?>>No</option>
                                            <option value="Y" <?php if ($EDITDATA['pickup_point_holder'] == 'Y') {?> selected <?php } ?>>Yes</option>
                                        </select>
                                        <?php if(form_error('pickup_point_holder')): ?>
                                        <span for="pickup_point_holder" generated="true" class="help-inline"><?php echo form_error('pickup_point_holder'); ?></span>
                                        <?php endif; ?>
                                        
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

<!-- <script>
$(document).ready(function(){
var b = $('#user_type'). val();
if(b == 'Retailer'){ $("#store").show();}
else{ $("#store").hide(); }
$("#user_type").change(function(){
var a = $(this).val();
//alert(a);
if(a == 'Retailer'){ $("#store").show();}
else{ $("#store").hide(); }
});
});
</script> -->
<link href="{ASSET_INCLUDE_URL}dist/css/fSelect.css" rel="stylesheet">
<script src="{ASSET_INCLUDE_URL}dist/js/fSelect.js"></script> 
<script type="text/javascript">
  $(document).ready(function(){  
    $('.select-search').fSelect();


    $('#Checkbox_password').on('change', function(){

        if($(this).prop('checked')) {
            $('.password-section').removeClass('d-none');
            $('#password').attr('disabled',false);
            $('#cpassword').attr('disabled',false);
        } else {
            $('.password-section').addClass('d-none');
            $('#password').attr('disabled',true);
            $('#cpassword').attr('disabled',true);


        }
    });
  });
</script>
<script>
$(document).ready(function(){
    var b = $('#user_type').val();

    if(b == 'Retailer' || b == 'Promoter' || b == 'Freelancer'|| b == 'Sales Person'){ $('#pos').show(); }else{ $('#pos').hide(); }

    if(b == 'Retailer' || b == 'Promoter'){ 
        $("#store").show();
        $('#store_name_block').show();
        // $('#area_block').show();
    }
    if(b == 'Freelancer'){ 
        $("#store").show();
        $('#store_name_block').show();
        // $('#area_block').show();
    }
    if(b == 'Users'){ $("#store").hide();}
    if(b == 'Sales Person'){ $("#store").hide();}
    if(b == 'Select user type'){ $("#store").hide();}
    
    $("#user_type").change(function(){


        var a = $(this).val();
        console.log(a)

        if(a == 'Retailer' || a == 'Promoter' || a == 'Freelancer' || a == 'Sales Person'){ $('#pos').show(); }else{ $('#pos').hide(); }

        if(a == 'Retailer' || a == 'Promoter' || a == 'Freelancer Promoter' ){ 
            $("#store").show();
            $('#store_name_block').show();
            // $('#area_block').show();
            $("#bind_user_type option:contains('Freelancer')").removeAttr("disabled");
        }
        if(a == 'Freelancer'){ 
            $("#store").show();
            $('#store_name_block').hide();
            // $('#area_block').hide();
            $("#bind_user_type option:contains('Freelancer')").attr("disabled","disabled");
        }
        if(a == 'Users'){ $("#store").hide();}
        if(a == 'Sales Person'){ $("#store").hide();}
        if(a == 'Select user type'){ $("#store").hide();}
    });

    var c = $('#bind_user_type').val();
    if(c == 'Freelancer'){ 
        $('#freelancer_person_block').show(); 
        $('#sales_person_block').hide();
    }
    if(c == 'Sales Person'){ 
        $('#sales_person_block').show();
        $('#freelancer_person_block').hide()
    }
    $('#bind_user_type').change(function(){
        var c = $('#bind_user_type').val();
        if(c == 'Freelancer'){ 
            $('#freelancer_person_block').show();
            $('#sales_person_block').hide();
        }
        if(c == 'Sales Person'){ 
            $('#sales_person_block').show();
            $('#freelancer_person_block').hide();
        }
    });
});
</script>

<script type="text/javascript">
$(document).ready(function(){
    $('#users_email').change(function(){
        var user = $(this).val();
        var oldemail = $('#old_users_email').val();

        if(user != oldemail){
            var ur  = '<?=base_url().'/users/allusers/checkDeplicacy'?>';
           
            $.ajax({
                url : ur,
                method: "POST", 
                data: {user: user},
                success: function(data){
                    $('#validationError').empty().append(data);

                    if(data == ""){
                        $('.submit-btn').attr('disabled', false);
                    }else{
                        $('.submit-btn').attr('disabled', true);
                    }

                }
            });

        }else{
            $('.submit-btn').attr('disabled', false);
            $('#validationError').empty();
        }
    });

$('#users_mobile').change(function(){
    var user = $(this).val();
    var ur      = '<?=base_url().'/users/allusers/checkDeplicacy'?>';
    var oldenumber = $('#old_users_mobile').val();

    if(user != oldenumber){
    $.ajax({
        url : ur,
        method: "POST", 
        data: {user: user},
        success: function(data){
            $('#m_validationError').empty().append(data);

            if(data == ""){
                $('.submit-btn').attr('disabled', false);
            }else{
                $('.submit-btn').attr('disabled', true);
            }


        }
    });
  }else{
            $('.submit-btn').attr('disabled', false);
            $('#m_validationError').empty();
        }

});

});


</script>


<script type="text/javascript">
//  $(function(){create_editor_for_textarea('description')});
//  $(function(){create_editor_for_textarea('image')});
</script>