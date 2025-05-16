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
                        <h5>Enable/Disable Quick Purchase</h5>
                        <a href="<?= base_url('users/allusers/index');?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>
                    <div class="card-body">
                        <div class="basic-login-inner">
                            <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="users_id"/>
                                <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['users_id']?>"/>
                                <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['users_id']?>"/>
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">

                                <div class="row">
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('buy_ticket')): ?>error<?php endif; ?>">
                                        <label>Buy Ticket<span class="required">*</span></label>
                                        <select name="buy_ticket" id="buy_ticket" class="form-control required">
                                            <option value="N" <?php if ($EDITDATA['buy_ticket'] == 'N') {?> selected <?php } ?>>No</option>
                                            <option value="Y" <?php if ($EDITDATA['buy_ticket'] == 'Y') {?> selected <?php } ?>>Yes</option>
                                        </select>
                                        <?php if(form_error('buy_ticket')): ?>
                                        <span for="buy_ticket" generated="true" class="help-inline"><?php echo form_error('buy_ticket'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('buy_voucher')): ?>error<?php endif; ?>">
                                        <label>Buy Voucher<span class="required">*</span></label>
                                        <select name="buy_voucher" id="buy_voucher" class="form-control required">
                                            <option value="N" <?php if ($EDITDATA['buy_voucher'] == 'N') {?> selected <?php } ?>>No</option>
                                            <option value="Y" <?php if ($EDITDATA['buy_voucher'] == 'Y') {?> selected <?php } ?>>Yes</option>
                                        </select>
                                        <?php if(form_error('buy_voucher')): ?>
                                        <span for="buy_voucher" generated="true" class="help-inline"><?php echo form_error('buy_voucher'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('company_name')): ?>error<?php endif; ?>">
                                        <label>Company Name</label>
                                        <input type="text" name="company_name" id="company_name" class="form-control" value="<?php if(set_value('company_name')): echo set_value('company_name'); else: echo stripslashes($EDITDATA['company_name']);endif; ?>" placeholder="Company Name">
                                        <?php if(form_error('company_name')): ?>
                                        <span for="name" generated="true" class="help-inline"><?php echo form_error('company_name'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                     <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('company_address')): ?>error<?php endif; ?>">
                                        <label>Company Address</label>
                                        <input type="text" name="company_address" id="company_address" class="form-control" value="<?php if(set_value('company_address')): echo set_value('company_address'); else: echo stripslashes($EDITDATA['company_address']);endif; ?>" placeholder="Company Address">
                                        <?php if(form_error('company_address')): ?>
                                        <span for="name" generated="true" class="help-inline"><?php echo form_error('company_address'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                </div>
                    
                                <div class="row">
                                    <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="inline-remember-me mt-4">
                                            <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                            <button class="btn btn-primary mb-4 submit-btn">Submit</button>
                                            <a href="<?= base_url('users/allusers/index');?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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

    if(b == 'Retailer' || b == 'Promoter' || b == 'Freelancer'){
        $('#pos').show();
    }else{
        $('#pos').hide();
    }


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
        
        if(a == 'Retailer' || a == 'Promoter' || a == 'Freelancer' || a == 'Sales Person'){
            $('#pos').show();
        }else{
            $('#pos').hide();
        }


        if(a == 'Retailer' || a == 'Promoter'){ 
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