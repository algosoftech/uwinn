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
                                <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?= isset($EDITDATA['users_id']) ? htmlspecialchars((string) $EDITDATA['users_id'], ENT_QUOTES, 'UTF-8') : '' ?>"/>
                                <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?= isset($EDITDATA['users_id']) ? htmlspecialchars((string) $EDITDATA['users_id'], ENT_QUOTES, 'UTF-8') : '' ?>"/>
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
                                        <option value="">Select user type</option>
                                        <option value="Freelancer" <?php if ($EDITDATA['users_type'] == 'Freelancer') {?> selected <?php } ?>>Freelancer</option>
                                        <option value="Sales Person" <?php if ($EDITDATA['users_type'] == 'Sales Person') {?> selected <?php } ?>>Sales Person</option>
                                        <option value="Retailer" <?php if ($EDITDATA['users_type'] == 'Retailer') {?> selected <?php } ?>>Retailer</option>
                                        <option value="Promoter" <?php if ($EDITDATA['users_type'] == 'Promoter') {?> selected <?php } ?>>Promoter</option>
                                        <option value="Users" <?php if ($EDITDATA['users_type'] == 'Users') {?> selected <?php } ?>>Users</option>
                                        <option value="Tester" <?php if ($EDITDATA['users_type'] == 'Tester') {?> selected <?php } ?>>Tester</option>
                                        <option value="Api User" <?php if ($EDITDATA['users_type'] == 'Api User') {?> selected <?php } ?>>Api User</option>
                                        <option value="BDM" <?php if ($EDITDATA['users_type'] == 'BDM') {?> selected <?php } ?>>BDM</option>
                                        <option value="Manager" <?php if ($EDITDATA['users_type'] == 'Manager') {?> selected <?php } ?>>Manager</option>
                                        <option value="Sales Supervisor" <?php if ($EDITDATA['users_type'] == 'Sales Supervisor') {?> selected <?php } ?>>Sales Supervisor</option>

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

                                <div class="row">
                                    <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('store_name')): ?>error<?php endif; ?>" id="store_name_block">
                                        <label>Store Name<span class="required">*</span></label>
                                        <input type="text" name="store_name" id="store_name" class="form-control required" value="<?php if(set_value('store_name')): echo set_value('store_name'); else: echo stripslashes($EDITDATA['store_name']);endif; ?>" placeholder="Name">
                                        <?php if(form_error('store_name')): ?>
                                        <span for="name" generated="true" class="help-inline"><?php echo form_error('store_name'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('bind_user_type')): ?>error<?php endif; ?>" id="bind_with_section">
                                        <label>Bind with<span class="required">*</span></label>
                                        <select name="bind_user_type" id="bind_user_type" class="form-control required">
                                            <option value="">Select</option>
                                            <option value="Sales Person" <?php if ($EDITDATA['bind_user_type'] == 'Sales Person') {?> selected <?php } ?>>Sales Person</option>
                                            <option value="Freelancer" <?php if ($EDITDATA['bind_user_type'] == 'Freelancer') {?> selected <?php } ?>>Freelancer</option>
                                            <option value="BDM" <?php if ($EDITDATA['bind_user_type'] == 'BDM') {?> selected <?php } ?>>BDM</option>
                                            <option value="Manager" <?php if ($EDITDATA['bind_user_type'] == 'Manager') {?> selected <?php } ?>>Manager</option>
                                            <option value="Sales Supervisor" <?php if ($EDITDATA['bind_user_type'] == 'Sales Supervisor') {?> selected <?php } ?>>Sales Supervisor</option>
                                            <!-- <option value="Retailer" <?php if ($EDITDATA['bind_user_type'] == 'Retailer') {?> selected <?php } ?>>Retailer</option> -->
                                        </select>
                                        <?php if(form_error('bind_user_type')): ?>
                                        <span for="bind_user_type" generated="true" class="help-inline"><?php echo form_error('bind_user_type'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                   
                                    <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('contract_given_by')): ?>error<?php endif; ?>" id="contract_given_by_section">
                                        <label>Contract given by</label>
                                        <input type="text" name="contract_given_by" id="contract_given_by" class="form-control" value="<?php if(set_value('contract_given_by')): echo set_value('contract_given_by'); else: echo stripslashes($EDITDATA['contract_given_by']);endif; ?>" placeholder="Name">
                                        <?php if(form_error('contract_given_by')): ?>
                                        <span for="contract_given_by" generated="true" class="help-inline"><?php echo form_error('contract_given_by'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Sales Person List -->
                                    <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('bind_with_person_name')): ?>error<?php endif; ?>" id="bind_with_list_block">
                                        <label>Select Bind with Person<span class="required">*</span></label>
                                        <input type="text" list="bind_with_list" name="bind_with_person_name" id="bind_with_person_name" class="form-control required" value="<?php if(isset($EDITDATA['bind_person_id'])): echo $EDITDATA['bind_person_id'].'|'.$EDITDATA['bind_person_name']; endif; ?>" placeholder="Enter Sales Person" />
                                        <datalist id="bind_with_list">
                                            <?php foreach ($bind_with_list as $key => $item) { ?>
                                                <option value="<?php echo stripcslashes($item['users_id']).'|'.stripcslashes($item['users_name']).'|'.stripcslashes($item['users_mobile']); ?>"><?php echo stripcslashes($item['users_id']).'|'.stripcslashes($item['users_name']).'|'.stripcslashes($item['users_mobile']); ?></option>   
                                            <?php } ?>
                                        </datalist>
                                        <?php if(form_error('products_list')): ?>
                                        <span for="products_list" generated="true" class="help-inline"><?php echo form_error('products_list'); ?></span>
                                        <?php else: ?>
                                        <span for="products_list" id="products_list_error" generated="true" class="help-inline" style="color: red;"><?php if(isset($email_id_error)): echo $email_id_error; endif; ?></span>
                                        <?php endif; ?>
                                    </div>


                                </div>

                                <div class="row" id="pos_section">
                                     <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('pos_number')): ?>error<?php endif; ?>" id="pos_number_block">
                                        <label>POS device Number<span class="required">*</span></label>
                                        <input type="text" name="pos_number" id="pos_number" class="form-control" value="<?php if(set_value('pos_number')): echo set_value('pos_number'); else: echo stripslashes($EDITDATA['pos_number']);endif; ?>" placeholder="POS Device">
                                        <?php if(form_error('pos_number')): ?>
                                        <span for="name" generated="true" class="help-inline"><?php echo form_error('pos_number'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('pos_device_id')): ?>error<?php endif; ?>" id="pos_device_id_block">
                                        <label>POS device Id<span class="required">*</span></label>
                                        <input type="text" name="pos_device_id" id="pos_device_id" class="form-control" value="<?php if(set_value('pos_device_id')): echo set_value('pos_device_id'); else: echo stripslashes($EDITDATA['pos_device_id']);endif; ?>" placeholder="POS Device">
                                        <?php if(form_error('pos_device_id')): ?>
                                        <span for="name" generated="true" class="help-inline"><?php echo form_error('pos_device_id'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php
                                    $simNoValue = set_value('sim_no');
                                    if($simNoValue === ''):
                                        $simNoValue = !empty($EDITDATA['sim_no']) ? stripslashes($EDITDATA['sim_no']) : '';
                                    endif;
                                    $simNoValue = substr(preg_replace('/\D/', '', (string)$simNoValue), 0, 19);
                                    ?>
                                    <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('sim_no')): ?>error<?php endif; ?>" id="sim_no_block">
                                        <label>SIM No</label>
                                        <input type="text" name="sim_no" id="sim_no" class="form-control" maxlength="19" inputmode="numeric" value="<?php echo htmlspecialchars($simNoValue, ENT_QUOTES, 'UTF-8'); ?>" placeholder="SIM No (optional)">
                                        <?php if(form_error('sim_no')): ?>
                                        <span for="sim_no" generated="true" class="help-inline"><?php echo form_error('sim_no'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('current_datamode')): ?>error<?php endif; ?>">
                                        <label>Current Data Using Mode </label>
                                        <input type="text" name="current_datamode" id="current_datamode" class="form-control" value="<?php if(set_value('current_datamode')): echo set_value('current_datamode'); else: echo stripslashes($EDITDATA['current_datamode']);endif; ?>" placeholder="Current Data Using Mode" disabled>
                                        <?php if(form_error('current_datamode')): ?>
                                        <span for="current_datamode" generated="true" class="help-inline"><?php echo form_error('current_datamode'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <fieldset class="commission_container">
                                        <legend>Commission Percentage</legend>
                                        <div class="row">
                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('commission_percentage')): ?>error<?php endif; ?>" id="commission_percentage_block">
                                                <label>Commission Percentage<span class="required">*</span></label>
                                                <input type="text" name="commission_percentage" id="commission_percentage" class="form-control" value="<?php if(set_value('commission_percentage')): echo set_value('commission_percentage'); else: echo stripslashes($EDITDATA['commission_percentage']?$EDITDATA['commission_percentage']:'10');endif; ?>" placeholder="Commission Percentage">
                                                <?php if(form_error('commission_percentage')): ?>
                                                <span for="name" generated="true" class="help-inline"><?php echo form_error('commission_percentage'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('recharge_commission_percentage')): ?>error<?php endif; ?>" id="recharge_commission_percentage_block">
                                                <label>Online Recharge and Voucher Commission Percentage<span class="required">*</span></label>
                                                <input type="text" name="recharge_commission_percentage" id="recharge_commission_percentage" class="form-control" value="<?php if(set_value('recharge_commission_percentage')): echo set_value('recharge_commission_percentage'); else: echo stripslashes($EDITDATA['recharge_commission_percentage']?$EDITDATA['recharge_commission_percentage']:'15');endif; ?>" placeholder="Recharge Commission Percentage">
                                                <?php if(form_error('recharge_commission_percentage')): ?>
                                                <span for="name" generated="true" class="help-inline"><?php echo form_error('recharge_commission_percentage'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('redeeming_commission_percentage')): ?>error<?php endif; ?>" id="redeeming_commission_percentage_block">
                                                <label>Online redeeming Commission Percentage<span class="required">*</span></label>
                                                <input type="text" name="redeeming_commission_percentage" id="redeeming_commission_percentage" class="form-control" value="<?php if(set_value('redeeming_commission_percentage')): echo set_value('redeeming_commission_percentage'); else: echo stripslashes($EDITDATA['redeeming_commission_percentage']?$EDITDATA['redeeming_commission_percentage']:'5');endif; ?>" placeholder="Recharge Commission Percentage">
                                                <?php if(form_error('redeeming_commission_percentage')): ?>
                                                <span for="name" generated="true" class="help-inline"><?php echo form_error('redeeming_commission_percentage'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('hourly_games_commission_percentage')): ?>error<?php endif; ?>" id="hourly_games_commission_percentage_block">
                                                <label>Hourly Games Commission Percentage<span class="required">*</span></label>
                                                <input type="text" name="hourly_games_commission_percentage" id="hourly_games_commission_percentage" class="form-control" value="<?php if(set_value('hourly_games_commission_percentage')): echo set_value('hourly_games_commission_percentage'); else: echo stripslashes($EDITDATA['hourly_games_commission_percentage']?$EDITDATA['hourly_games_commission_percentage']:'10');endif; ?>" placeholder="Hourly Games Commission Percentage">
                                                <?php if(form_error('hourly_games_commission_percentage')): ?>
                                                <span for="name" generated="true" class="help-inline"><?php echo form_error('hourly_games_commission_percentage'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('ding_commission_percentage')): ?>error<?php endif; ?>" id="ding_commission_percentage_block">
                                                <label>International (Ding) Commission Percentage<span class="required">*</span></label>
                                                <input type="text" name="ding_commission_percentage" id="ding_commission_percentage" class="form-control" value="<?php if(set_value('ding_commission_percentage')): echo set_value('ding_commission_percentage'); else: echo stripslashes(isset($EDITDATA['ding_commission_percentage']) && $EDITDATA['ding_commission_percentage'] !== '' ? $EDITDATA['ding_commission_percentage'] : '0');endif; ?>" placeholder="International (Ding) Commission Percentage">
                                                <?php if(form_error('ding_commission_percentage')): ?>
                                                <span for="ding_commission_percentage" generated="true" class="help-inline"><?php echo form_error('ding_commission_percentage'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('scratch_card_commission_percentage')): ?>error<?php endif; ?>" id="scratch_card_commission_percentage_block">
                                                <label> Scratch Card Commission Percentage<span class="required">*</span></label>
                                                <input type="text" name="scratch_card_commission_percentage" id="scratch_card_commission_percentage" class="form-control" value="<?php if(set_value('scratch_card_commission_percentage')): echo set_value('scratch_card_commission_percentage'); else: echo stripslashes(isset($EDITDATA['scratch_card_commission_percentage']) && $EDITDATA['scratch_card_commission_percentage'] !== '' ? $EDITDATA['scratch_card_commission_percentage'] : '0');endif; ?>" placeholder="Scratch Card Commission Percentage">
                                                <?php if(form_error('scratch_card_commission_percentage')): ?>
                                                <span for="scratch_card_commission_percentage" generated="true" class="help-inline"><?php echo form_error('scratch_card_commission_percentage'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                        </div>
                                    </fieldset>

                                    <fieldset class="redeem_limit_container d-none">
                                        <legend>Redeem Limit</legend>
                                        <div class="row">
                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('redeeming_amount_limit')): ?>error<?php endif; ?>" id="redeeming_amount_limit_block">
                                                <label>Redeeming Amount Limit (AED)<span class="required">*</span></label>
                                                <input type="number" step="0.01" min="0.01" name="redeeming_amount_limit" id="redeeming_amount_limit" class="form-control" value="<?php if(set_value('redeeming_amount_limit')): echo set_value('redeeming_amount_limit'); else: echo stripslashes(isset($EDITDATA['redeeming_amount_limit']) && $EDITDATA['redeeming_amount_limit'] !== '' ? $EDITDATA['redeeming_amount_limit'] : '499');endif; ?>" placeholder="Redeeming Amount Limit">
                                                <?php if(form_error('redeeming_amount_limit')): ?>
                                                <span for="redeeming_amount_limit" generated="true" class="help-inline"><?php echo form_error('redeeming_amount_limit'); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <?php
                                            $editRedeemMode = 'fixed';
                                            if (set_value('redeem_limit_mode')) {
                                                $editRedeemMode = set_value('redeem_limit_mode');
                                            } elseif (!empty($EDITDATA['redeem_limit_mode'])) {
                                                $editRedeemMode = strtolower((string) $EDITDATA['redeem_limit_mode']);
                                            }
                                            if ($editRedeemMode !== 'global') {
                                                $editRedeemMode = 'fixed';
                                            }
                                            ?>
                                            <div class="form-group-inner col-lg-8 col-md-8 col-sm-8 col-xs-12 <?php if(form_error('redeem_limit_mode')): ?>error<?php endif; ?>">
                                                <label>Redeem Limit Type <span class="required">*</span></label>
                                                <div class="mt-2">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="redeem_limit_mode" id="edit_redeem_limit_mode_global" value="global" <?php echo $editRedeemMode === 'global' ? 'checked="checked"' : ''; ?>>
                                                        <label class="form-check-label" for="edit_redeem_limit_mode_global">Global</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="redeem_limit_mode" id="edit_redeem_limit_mode_fixed" value="fixed" <?php echo $editRedeemMode === 'fixed' ? 'checked="checked"' : ''; ?>>
                                                        <label class="form-check-label" for="edit_redeem_limit_mode_fixed">Fixed</label>
                                                    </div>
                                                </div>
                                                <small class="text-muted d-block">Global: The custom limit will remain unchanged after redeem. Fixed: The limit will reset to 499 AED after redeem.</small>
                                                <?php if(form_error('redeem_limit_mode')): ?>
                                                <span for="redeem_limit_mode" generated="true" class="help-inline"><?php echo form_error('redeem_limit_mode'); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </fieldset>
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

                                    <?php if (!empty($IS_EDIT)): ?>
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <label>International Balance</label>
                                        <input type="text" class="form-control" readonly value="<?php echo number_format((float) ($EDITDATA['availableReachargePoints'] ?? 0), 2, '.', ''); ?>" placeholder="International Balance">
                                    </div>
                                    <?php endif; ?>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('users_otp')): ?>error<?php endif; ?>">
                                        <label>OTP Numbers</label>
                                        <input type="number" min="0" name="users_otp" id="users_otp" class="form-control" value="<?php if(set_value('users_otp')): echo set_value('users_otp'); else: echo stripslashes($EDITDATA['users_otp']);endif; ?>" placeholder="OTP Numbers" <?php 
                                        if($EDITDATA['users_otp']){ ?>
                                            readonly
                                        <?php } ?> >
                                        <?php if(form_error('users_otp')): ?>
                                        <span for="users_otp" generated="true" class="help-inline"><?php echo form_error('users_otp'); ?></span>
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

                                <fieldset class="tambola_games_container">
                                    <legend>Enable/Disable Features</legend>
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
                                            
                                            <div class="form-group-inner col-lg-3 col-md-2 col-sm-3 col-xs-12 <?php if(form_error('show_lotto_campaign')): ?>error<?php endif; ?>">
                                                <label>Show Lotto Campaign<span class="required">*</span></label>
                                                <select name="show_lotto_campaign" id="show_lotto_campaign" class="form-control required">
                                                    <option value="N" <?php if ($EDITDATA['show_lotto_campaign'] == 'N') {?> selected <?php } ?>>No</option>
                                                    <option value="Y" <?php if ($EDITDATA['show_lotto_campaign'] == 'Y') {?> selected <?php } ?>>Yes</option>
                                                </select>
                                                <?php if(form_error('show_lotto_campaign')): ?>
                                                <span for="show_lotto_campaign" generated="true" class="help-inline"><?php echo form_error('show_lotto_campaign'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group-inner col-lg-3 col-md-2 col-sm-3 col-xs-12 <?php if(form_error('show_raffle_campaign')): ?>error<?php endif; ?>">
                                                <label>Show Raffle Campaign<span class="required">*</span></label>
                                                <select name="show_raffle_campaign" id="show_raffle_campaign" class="form-control required">
                                                    <option value="N" <?php if ($EDITDATA['show_raffle_campaign'] == 'N') {?> selected <?php } ?>>No</option>
                                                    <option value="Y" <?php if ($EDITDATA['show_raffle_campaign'] == 'Y') {?> selected <?php } ?>>Yes</option>
                                                </select>
                                                <?php if(form_error('show_raffle_campaign')): ?>
                                                <span for="show_raffle_campaign" generated="true" class="help-inline"><?php echo form_error('show_raffle_campaign'); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="form-group-inner col-lg-3 col-md-2 col-sm-3 col-xs-12 <?php if(form_error('enable_raffle_entries')): ?>error<?php endif; ?>">
                                                <label>Enable Raffle Entries<span class="required">*</span></label>
                                                <select name="enable_raffle_entries" id="enable_raffle_entries" class="form-control required">
                                                    <option value="N" <?php if ($EDITDATA['enable_raffle_entries'] == 'N') {?> selected <?php } ?>>No</option>
                                                    <option value="Y" <?php if ($EDITDATA['enable_raffle_entries'] == 'Y') {?> selected <?php } ?>>Yes</option>
                                                </select>
                                                <?php if(form_error('enable_raffle_entries')): ?>
                                                <span for="enable_raffle_entries" generated="true" class="help-inline"><?php echo form_error('enable_raffle_entries'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group-inner col-lg-3 col-md-2 col-sm-3 col-xs-12 <?php if(form_error('enable_summary_otp')): ?>error<?php endif; ?>">
                                                <label>Enable summary OTP<span class="required">*</span></label>
                                                <select name="enable_summary_otp" id="enable_summary_otp" class="form-control required">
                                                    <option value="N" <?php if ($EDITDATA['enable_summary_otp'] == 'N') {?> selected <?php } ?>>No</option>
                                                    <option value="Y" <?php if ($EDITDATA['enable_summary_otp'] == 'Y') {?> selected <?php } ?>>Yes</option>
                                                </select>
                                                <?php if(form_error('enable_summary_otp')): ?>
                                                <span for="enable_summary_otp" generated="true" class="help-inline"><?php echo form_error('enable_summary_otp'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group-inner col-lg-3 col-md-2 col-sm-3 col-xs-12 <?php if(form_error('enable_tambola_games')): ?>error<?php endif; ?>">
                                                <label>Enable Tambola Games<span class="required">*</span></label>
                                                <select name="enable_tambola_games" id="enable_tambola_games" class="form-control required">
                                                    <option value="N" <?php if ($EDITDATA['enable_tambola_games'] == 'N') {?> selected <?php } ?>>No</option>
                                                    <option value="Y" <?php if ($EDITDATA['enable_tambola_games'] == 'Y') {?> selected <?php } ?>>Yes</option>
                                                </select>
                                                <?php if(form_error('enable_tambola_games')): ?>
                                                <span for="enable_tambola_games" generated="true" class="help-inline"><?php echo form_error('enable_tambola_games'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group-inner col-lg-3 col-md-2 col-sm-3 col-xs-12 <?php if(form_error('enable_tambola_games')): ?>error<?php endif; ?>">
                                                <label>Enable Hourly Games<span class="required">*</span></label>
                                                <select name="enable_hourly_games" id="enable_hourly_games" class="form-control required">
                                                    <option value="N" <?php if ($EDITDATA['enable_hourly_games'] == 'N') {?> selected <?php } ?>>No</option>
                                                    <option value="Y" <?php if ($EDITDATA['enable_hourly_games'] == 'Y') {?> selected <?php } ?>>Yes</option>
                                                </select>
                                                <?php if(form_error('enable_hourly_games')): ?>
                                                <span for="enable_hourly_games" generated="true" class="help-inline"><?php echo form_error('enable_hourly_games'); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="form-group-inner col-lg-3 col-md-2 col-sm-3 col-xs-12 <?php if(form_error('enable_ding')): ?>error<?php endif; ?>">
                                                <label>Enable International (Ding)<span class="required">*</span></label>
                                                <select name="enable_ding" id="enable_ding" class="form-control required">
                                                    <?php $enableDingValue = (!empty($EDITDATA['enable_ding']) && $EDITDATA['enable_ding'] == 'Y') ? 'Y' : 'N'; ?>
                                                    <option value="N" <?php if ($enableDingValue == 'N') {?> selected <?php } ?>>No</option>
                                                    <option value="Y" <?php if ($enableDingValue == 'Y') {?> selected <?php } ?>>Yes</option>
                                                </select>
                                                <?php if(form_error('enable_ding')): ?>
                                                <span for="enable_ding" generated="true" class="help-inline"><?php echo form_error('enable_ding'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group-inner col-lg-3 col-md-2 col-sm-3 col-xs-12 <?php if(form_error('enable_scratch_win')): ?>error<?php endif; ?>">
                                                <label>Enable Scratch & Win<span class="required">*</span></label>
                                                <select name="enable_scratch_win" id="enable_scratch_win" class="form-control required">
                                                    <?php $enableScratchWinValue = (!empty($EDITDATA['enable_scratch_win']) && $EDITDATA['enable_scratch_win'] == 'Y') ? 'Y' : 'N'; ?>
                                                    <option value="N" <?php if ($enableScratchWinValue == 'N') {?> selected <?php } ?>>No</option>
                                                    <option value="Y" <?php if ($enableScratchWinValue == 'Y') {?> selected <?php } ?>>Yes</option>
                                                </select>
                                                <?php if(form_error('enable_scratch_win')): ?>
                                                <span for="enable_scratch_win" generated="true" class="help-inline"><?php echo form_error('enable_scratch_win'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group-inner col-lg-3 col-md-2 col-sm-3 col-xs-12 <?php if(form_error('enable_buy_win')): ?>error<?php endif; ?>">
                                                <label>Enable Buy & Win<span class="required">*</span></label>
                                                <select name="enable_buy_win" id="enable_buy_win" class="form-control required">
                                                    <?php $enableBuyWinValue = (!empty($EDITDATA['enable_buy_win']) && $EDITDATA['enable_buy_win'] == 'Y') ? 'Y' : 'N'; ?>
                                                    <option value="N" <?php if ($enableBuyWinValue == 'N') {?> selected <?php } ?>>No</option>
                                                    <option value="Y" <?php if ($enableBuyWinValue == 'Y') {?> selected <?php } ?>>Yes</option>
                                                </select>
                                                <?php if(form_error('enable_scratch_win')): ?>
                                                <span for="enable_buy_win" generated="true" class="help-inline"><?php echo form_error('enable_buy_win'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                        </div>
                                </fieldset>
                               
                            <?php /*    <div class="row">
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
                                    <div class="form-group-inner col-lg-3 col-md-2 col-sm-3 col-xs-12 <?php if(form_error('show_raffle_campaign')): ?>error<?php endif; ?>">
                                        <label>Show Raffle Campaign<span class="required">*</span></label>
                                        <select name="show_raffle_campaign" id="show_raffle_campaign" class="form-control required">
                                            <option value="N" <?php if ($EDITDATA['show_raffle_campaign'] == 'N') {?> selected <?php } ?>>No</option>
                                            <option value="Y" <?php if ($EDITDATA['show_raffle_campaign'] == 'Y') {?> selected <?php } ?>>Yes</option>
                                        </select>
                                        <?php if(form_error('show_raffle_campaign')): ?>
                                        <span for="show_raffle_campaign" generated="true" class="help-inline"><?php echo form_error('show_raffle_campaign'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group-inner col-lg-3 col-md-2 col-sm-3 col-xs-12 <?php if(form_error('show_raffle_campaign')): ?>error<?php endif; ?>">
                                        <label>Enable Raffle Entries<span class="required">*</span></label>
                                        <select name="enable_raffle_entries" id="enable_raffle_entries" class="form-control required">
                                            <option value="N" <?php if ($EDITDATA['enable_raffle_entries'] == 'N') {?> selected <?php } ?>>No</option>
                                            <option value="Y" <?php if ($EDITDATA['enable_raffle_entries'] == 'Y') {?> selected <?php } ?>>Yes</option>
                                        </select>
                                        <?php if(form_error('enable_raffle_entries')): ?>
                                        <span for="enable_raffle_entries" generated="true" class="help-inline"><?php echo form_error('enable_raffle_entries'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-3 col-md-2 col-sm-3 col-xs-12 <?php if(form_error('show_raffle_campaign')): ?>error<?php endif; ?>">
                                        <label>Enable summary OTP<span class="required">*</span></label>
                                        <select name="enable_summary_otp" id="enable_summary_otp" class="form-control required">
                                            <option value="N" <?php if ($EDITDATA['enable_summary_otp'] == 'N') {?> selected <?php } ?>>No</option>
                                            <option value="Y" <?php if ($EDITDATA['enable_summary_otp'] == 'Y') {?> selected <?php } ?>>Yes</option>
                                        </select>
                                        <?php if(form_error('enable_summary_otp')): ?>
                                        <span for="enable_summary_otp" generated="true" class="help-inline"><?php echo form_error('enable_summary_otp'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    
                                </div> */ ?>
                                
                    
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
 $(document).ready(function(e){
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

        //Default field hiding.
        $('#bind_with_list_block ,.commission_container ,.redeem_limit_container , #bind_with_section , #store_name_block , #pos_section , #contract_given_by_section').addClass('d-none');

        // Showing EditData..
        let current_userType = "<?= isset($EDITDATA['users_type']) ? $EDITDATA['users_type'] : '' ?>";

        if(current_userType == 'Sales Person' || current_userType == 'Freelancer' ){
            $('#pos_section , #bind_with_section , #bind_with_list_block , .redeem_limit_container').removeClass('d-none');

            if(current_userType == 'Freelancer'){
              $("#bind_user_type option:contains('Sales Person')").prop("disabled", false);
            }else if(current_userType == 'Sales Person'){
              $("#bind_user_type option:contains('Sales Supervisor') , #bind_user_type option:contains('Manager') ").prop("disabled", false);
            }
        
        }else if( current_userType == "Retailer" || current_userType == 'Promoter' ){
           $(' #store_name_block , #bind_with_section , #bind_with_list_block , #pos_section , .commission_container , .redeem_limit_container , #contract_given_by_section').removeClass('d-none');

          $("#bind_user_type option:contains('Sales Person') , #bind_user_type option:contains('Sales Supervisor') , #bind_user_type option:contains('Manager') ").prop("disabled", false);

            if(current_userType == 'Promoter'){
                $('.commission_percentage_block , #contract_given_by_section').addClass('d-none');
            }

        }else if(current_userType == 'Manager'){
            $('#bind_with_section , #bind_with_list_block').removeClass('d-none');
            $("#bind_user_type option:contains('Manager')").prop("disabled", false);

        }else if(current_userType == 'Sales Supervisor'){
            $('#bind_with_section , #bind_with_list_block').removeClass('d-none');
            $("#bind_user_type option:contains('Manager')").prop("disabled", false);
        }else if(current_userType == 'Users'){
            $('#pos_section ').removeClass('d-none');
        }


        let bind_person_id = "<?= isset($EDITDATA['bind_person_id']) ? $EDITDATA['bind_person_id'] : '' ?>";

        if(bind_person_id != ""){
            let bind_user_type = $('#bind_user_type').val();
            // console.log(bind_user_type);
            var path      = '<?=getCurrentDashboardPath('allusers/getbindwith');?>';
            $.ajax({
                 url      : path,
                 method   : "POST", 
                 dataType : "json",
                 data     : {bindWith : bind_user_type},
                 success  : function(response){
                    $('#bind_with_list_block').removeClass('d-none');
                    let datalist = $("#bind_with_list");
                    datalist.empty(); // Clear existing options
                     $.each(response, function(index, item) {
                       // Construct value just like in your PHP code
                        let value = item.users_id + "|" + item.users_name + "|" + item.users_mobile;
                        datalist.append(
                            $("<option>", { value: value, text: value })
                        );
                    });
                }
            });
        }

        $('#user_type').on('change', function(){
            $('#bind_with_list_block ,.commission_container , #bind_with_section , #store_name_block , #pos_section, #contract_given_by_section').addClass('d-none');
            $("#bind_user_type option").prop("disabled", true);

            let userType      = $(this).val();
            let POSNumber     = $('#pos_number').val();
            var existingPOSNO = "<?= isset($EDITDATA['pos_number']) ? $EDITDATA['pos_number'] : '' ?>";
            if(userType != 'Users' && POSNumber == "" && existingPOSNO == ''){
                var path      = '<?=getCurrentDashboardPath('allusers/generatePosNumber');?>';
                $.ajax({
                    url : path,
                    method: "GET", 
                    success: function(data){
                        var jsonObject =  $.parseJSON(data)
                        $('#pos_number').val(jsonObject.counter);
                    }
                });
            }

            // console.log(userType);

            if( userType == 'Freelancer'){
                $('#bind_with_section , #pos_section , .redeem_limit_container').removeClass('d-none');
                $("#bind_user_type option:contains('Sales Person')").prop("disabled", false);

            } else if(userType == 'Sales Person' ){
                $('#bind_with_section , #pos_section , .redeem_limit_container').removeClass('d-none');
                $("#bind_user_type option:contains('Manager')").prop("disabled", false);
                $("#bind_user_type option:contains('Sales Supervisor')").prop("disabled", false);

            } else if(userType == 'Retailer' || userType == 'Promoter' ){
               $('#bind_with_section , #store_name_block , #pos_section , .commission_container , .redeem_limit_container , #contract_given_by_section').removeClass('d-none');
               if(userType == 'Promoter'){
                $('#commission_percentage_block , #contract_given_by_section').addClass('d-none');
               }

               $("#bind_user_type option:contains('Sales Person') , #bind_user_type option:contains('Sales Supervisor'),#bind_user_type option:contains('Manager')").prop("disabled", false);

            } else if(userType == 'Manager' ){
               $('#bind_with_section').removeClass('d-none');
               $("#bind_user_type option:contains('BDM')").prop("disabled", false);

            }else if(userType == 'Sales Supervisor' ){
               $('#bind_with_section').removeClass('d-none');
               $("#bind_user_type option:contains('Manager')").prop("disabled", false);
            }
        });

        $('#users_mobile').on('keyup' , function(){
            let mobile = $(this).val();
            let path   = '<?=getCurrentDashboardPath('allusers/checkDeplicacy');?>';
            let oldnumber = $('#old_users_mobile').val();
            if(mobile != oldnumber){
                $.ajax({
                    url : path,
                    method: "POST", 
                    data: {user: mobile},
                    success: function(data){
                        $('#m_validationError').empty().append(data);

                        if(data == ""){
                            $('.submit-btn').attr('disabled', false);
                        }else{
                            $('.submit-btn').attr('disabled', true);
                        }
                    }
                });
            }
        });

        $('#users_email').on('keyup' , function(){
            var email = $(this).val();
            var oldmail = $('#old_users_email').val();

            if(email != oldmail){
                let path = '<?=getCurrentDashboardPath('allusers/checkDeplicacy');?>';
               
                $.ajax({
                    url : path,
                    method: "POST", 
                    data: {user: email},
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

        // Bind With Type changes option showing..
        $('#bind_user_type').on('change', function(){
            let bindWith = $(this).val();
            console.log(bindWith);
            var path      = '<?=getCurrentDashboardPath('allusers/getbindwith');?>';
            $.ajax({
                 url      : path,
                 method   : "POST", 
                 dataType : "json",
                 data     : {bindWith : bindWith},
                 success  : function(response){
                    $('#bind_with_list_block').removeClass('d-none');
                    let datalist = $("#bind_with_list");
                    datalist.empty(); // Clear existing options
                     $.each(response, function(index, item) {
                       // Construct value just like in your PHP code
                        let value = item.users_id + "|" + item.users_name + "|" + item.users_mobile;
                        datalist.append(
                            $("<option>", { value: value, text: value })
                        );
                    });
                }
            });
        });

        

    });
</script>

<script type="text/javascript">
//  $(function(){create_editor_for_textarea('description')});
//  $(function(){create_editor_for_textarea('image')});
</script>