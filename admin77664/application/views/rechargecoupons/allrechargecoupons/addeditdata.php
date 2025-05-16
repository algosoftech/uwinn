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
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLRECHARGECOUPONDATA',getCurrentControllerPath('index')); ?>">Recharge Coupon</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Recharge Coupon</a></li>
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
                        <h5><?=$EDITDATA?'Edit':'Add'?> Recharge Coupon</h5>
                        <a href="<?php echo correctLink('ALLRECHARGECOUPONDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>
                    <div class="card-body">
                        <div class="basic-login-inner">
                            <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="rc_id"/>
                                <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['rc_id']?>"/>
                                <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['rc_id']?>"/>
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">

                                <div class="row">
                                    
                                    <?php /*
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('users')): ?>error<?php endif; ?>" id="user_list">
                                        <label>Users List<span class="required">*</span></label><br>
                                        <select name="users" id="users" class="form-control required select-search">
                                        <option value="">Select User</option>
                                        <?php if($ALLUSERS <> ""): foreach($ALLUSERS as $user):    ?>
                                            <option value="<?php echo $user['users_id']; ?>" ><?php echo ucfirst($user['users_name']).' ('.$user['users_email'].') ('.$user['users_mobile'].')'; ?></option>
                                        <?php endforeach; endif; ?>
                                        </select>
                                        <?php if(form_error('users')): ?>
                                        <label for="users" generated="true" class="error"><?php echo form_error('users'); ?></label>
                                        <?php endif; ?>
                                    </div>

                                    */ ?>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('generate_for')): ?>error<?php endif; ?>">
                                        <label>Generate For<!-- <span class="required">*</span> --></label>
                                        <input type="text" name="generate_for" id="generate_for" class="form-control" value="<?php if(set_value('generate_for')): echo set_value('generate_for'); else: echo stripslashes($EDITDATA['generate_for']);endif; ?>" placeholder="Generate For">
                                        <?php if(form_error('generate_for')): ?>
                                        <span for="generate_for" generated="true" class="help-inline"><?php echo form_error('generate_for'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('title')): ?>error<?php endif; ?>">
                                        <label>AED<span class="required">*</span></label>
                                        <input type="number" min="0" name="aed" id="aed" class="form-control required" value="<?php if(set_value('aed')): echo set_value('aed'); else: echo stripslashes($EDITDATA['aed']);endif; ?>" placeholder="AED">
                                        <?php if(form_error('title')): ?>
                                        <span for="aed" generated="aed" class="help-inline"><?php echo form_error('aed'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('upoints')): ?>error<?php endif; ?>">
                                        <label>U Points<span class="required">*</span></label>
                                        <input type="number" min="0" name="upoints" id="upoints" class="form-control required" value="<?php if(set_value('upoints')): echo set_value('upoints'); else: echo stripslashes($EDITDATA['upoints']);endif; ?>" placeholder="U Points">
                                        <?php if(form_error('upoints')): ?>
                                        <span for="upoints" generated="true" class="help-inline"><?php echo form_error('upoints'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('qty')): ?>error<?php endif; ?>">
                                        <label>Quantity<span class="required">*</span></label>
                                        <input type="number" min="0" name="qty" id="qty" class="form-control required" value="<?php if(set_value('qty')): echo set_value('qty'); else: echo stripslashes($EDITDATA['qty']);endif; ?>" placeholder="Quantity">
                                        <?php if(form_error('qty')): ?>
                                        <span for="qty" generated="qty" class="help-inline"><?php echo form_error('qty'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('coupon_length')): ?>error<?php endif; ?>">
                                        <label>Coupon Length<span class="required">*</span></label>
                                        <input type="number" min="8" name="coupon_length" id="coupon_length" class="form-control required" value="<?php if(set_value('coupon_length')): echo set_value('coupon_length'); else: echo stripslashes($EDITDATA['coupon_length']);endif; ?>" placeholder="Coupon Length">
                                        <?php if(form_error('coupon_length')): ?>
                                        <span for="coupon_length" generated="qcoupon_lengthty" class="help-inline"><?php echo form_error('coupon_length'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                               
                                
                                
                                <div class="row">
                                    <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="inline-remember-me mt-4">
                                            <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                            <button class="btn btn-primary mb-4">Generate</button>
                                            <a href="<?php echo correctLink('ALLRECHARGECOUPONDATA',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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