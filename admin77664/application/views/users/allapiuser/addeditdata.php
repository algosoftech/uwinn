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

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('users_type')): ?>error<?php endif; ?>">
                                       <label>User Type<span class="required">*</span></label>
                                       <select name="user_type" id="user_type" class="form-control required">
                                            <option >Select user type</option>
                                            <option value="Api User" <?php if ($EDITDATA['users_type'] == 'Api User') {?> selected <?php } ?>>Api User</option>
                                       </select>
                                       <?php if(form_error('user_type')): ?>
                                          <span for="user_type" generated="true" class="help-inline"><?php echo form_error('user_type'); ?></span>
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

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('totalArabianPoints')): ?>error<?php endif; ?>">
                                        <label>Total UPoints<span class="required">*</span></label>
                                        <input type="number" min="0" name="totalArabianPoints" id="totalArabianPoints" class="form-control required" value="<?php if(set_value('totalArabianPoints')): echo set_value('totalArabianPoints'); else: echo stripslashes($EDITDATA['totalArabianPoints']);endif; ?>" placeholder="Total UPoints" <?php 
                                        if($EDITDATA['totalArabianPoints']){ ?>
                                            readonly
                                        <?php } ?> >
                                        <?php if(form_error('totalArabianPoints')): ?>
                                        <span for="totalArabianPoints" generated="true" class="help-inline"><?php echo form_error('totalArabianPoints'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('availableArabianPoints')): ?>error<?php endif; ?>">
                                        <label>Available UPoints<span class="required">*</span></label>
                                        <input type="number" min="0" name="availableArabianPoints" id="availableArabianPoints" class="form-control required" value="<?php if(set_value('availableArabianPoints')): echo set_value('availableArabianPoints'); else: echo stripslashes($EDITDATA['availableArabianPoints']);endif; ?>" placeholder="Available UPoints" <?php 
                                        if($EDITDATA['availableArabianPoints']){ ?>
                                            readonly
                                        <?php } ?> >
                                        <?php if(form_error('availableArabianPoints')): ?>
                                        <span for="availableArabianPoints" generated="true" class="help-inline"><?php echo form_error('availableArabianPoints'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('app_name')): ?>error<?php endif; ?>">
                                        <label>App Name<span class="required">*</span></label>
                                        <input type="text" name="app_name" id="app_name" class="form-control required" value="<?php if(set_value('app_name')): echo set_value('app_name'); else: echo stripslashes($EDITDATA['app_name']);endif; ?>" placeholder="App Name">
                                        <?php if(form_error('app_name')): ?>
                                        <span for="name" generated="true" class="help-inline"><?php echo form_error('app_name'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('store_name')): ?>error<?php endif; ?>" id="store_name_block">
                                        <label>Store Name<span class="required">*</span></label>
                                        <input type="text" name="store_name" id="store_name" class="form-control required" value="<?php if(set_value('store_name')): echo set_value('store_name'); else: echo stripslashes($EDITDATA['store_name']);endif; ?>" placeholder="Store Name">
                                        <?php if(form_error('store_name')): ?>
                                        <span for="name" generated="true" class="help-inline"><?php echo form_error('store_name'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('time_zone')): ?>error<?php endif; ?>" id="time_zone_block">
                                        <label>Time Zone<span class="required">*</span></label>
                                        <input type="text" name="time_zone" id="time_zone" class="form-control required" value="<?php if(set_value('time_zone')): echo set_value('time_zone'); else: echo stripslashes($EDITDATA['time_zone']);endif; ?>" placeholder="Time Zone">
                                        <?php if(form_error('time_zone')): ?>
                                        <span for="name" generated="true" class="help-inline"><?php echo form_error('time_zone'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('whitelisted_ips')): ?>error<?php endif; ?>">
                                        <label>White Listed IPs <span class="required">*</span></label>
                                        <input type="text" name="whitelisted_ips" id="whitelisted_ips" class="form-control required" value="<?php if(set_value('whitelisted_ips')): echo set_value('whitelisted_ips'); else: echo stripslashes($EDITDATA['whitelisted_ips']);endif; ?>" placeholder="White Listed IPs" <?php 
                                        if($EDITDATA['whitelisted_ips']){ ?>
                                            readonly
                                        <?php } ?> >
                                        <?php if(form_error('whitelisted_ips')): ?>
                                        <span for="whitelisted_ips" generated="true" class="help-inline"><?php echo form_error('whitelisted_ips'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('api_key')): ?>error<?php endif; ?>">
                                        <label>API Key <span class="required">*</span></label>
                                        <input type="text" name="api_key" id="api_key" class="form-control required" value="<?php if(set_value('api_key')): echo set_value('api_key'); else: echo stripslashes($EDITDATA['api_key']);endif; ?>" placeholder="API Key" <?php 
                                        if($EDITDATA['api_key']){ ?>
                                            readonly
                                        <?php } ?> >
                                        <?php if(form_error('api_key')): ?>
                                        <span for="api_key" generated="true" class="help-inline"><?php echo form_error('api_key'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('created_at')): ?>error<?php endif; ?>">
                                        <label>API Date</label>
                                        <input type="text" name="created_at" id="created_at" class="form-control" value="<?php if(set_value('created_at')): echo set_value('created_at'); else: echo stripslashes($EDITDATA['created_at']);endif; ?>" placeholder="API Date" <?php 
                                        if($EDITDATA['created_at']){ ?>
                                            readonly
                                        <?php } ?> >
                                        <?php if(form_error('created_at')): ?>
                                        <span for="created_at" generated="true" class="help-inline"><?php echo form_error('created_at'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                 
                                    <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 SaveChanges">
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
        POSNumber = $('#pos_number').val();
        if(a != 'Users' && POSNumber == "" ){
            var ur  = "<?=base_url('/users/allusers/generatePosNumber')?>";
            $.ajax({
                url : ur,
                method: "GET", 
                success: function(data){
                    var jsonObject =  $.parseJSON(data)
                    $('#pos_number').val(jsonObject.counter);
                }
            });
        }

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