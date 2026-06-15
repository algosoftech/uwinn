<link rel="stylesheet" href="{ASSET_INCLUDE_URL}canvasCrop/about.image.canvasCrop.css">
<script type="text/javascript" src="{ASSET_INCLUDE_URL}canvasCrop/jquery.canvasCrop.js"></script>
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
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('CMSGENERALDATA',getCurrentControllerPath('index')); ?>">Website</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> General Data</a></li>
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
                <h5><?=$EDITDATA?'Edit':'Add'?> General Data</h5>
                <a href="<?php echo correctLink('CMSGENERALDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
              </div>
              <div class="card-body">
                <div class="basic-login-inner">
                  <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="general_data_id"/>
                    <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['general_data_id'] ?? ''?>"/>
                    <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['general_data_id'] ?? ''?>"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                    <fieldset>
                      <legend>General Data</legend>
                     
                      <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('image')): ?>error<?php endif; ?>">
                             <label> Logo <span class="required"> </span></label><br>
                        <input type="file" name="image" class="image"><br>
                        <?php if(!empty($EDITDATA['logo'] ?? '')): ?>
                           <div id="ImageDiv"><img src="<?php echo fileBaseUrl.$EDITDATA['logo']; ?>" width="50" border="0" alt="">&nbsp;
                           <a href="javascript:void(0);" onclick="ImageDelete('<?php echo $EDITDATA['logo'] ?? ''; ?>','<?php echo $EDITDATA['general_data_id'] ?? ''; ?>');"> 
                              <img src="{ASSET_INCLUDE_URL}images/cross.png" border="0" alt="">
                            </a></div>
                          <?php endif; ?>
                        <?php if(form_error('image')): ?>
                          <span for="image" generated="true" class="help-inline"><?php echo form_error('image'); ?></span>
                        <?php endif; ?>
                        </div>

                        <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('website_name')): ?>error<?php endif; ?>">
                          <label>WEBSITE<span class="required">*</span></label>
                          <input type="text" name="website_name" id="website_name" value="<?php if(set_value('website_name')): echo set_value('website_name'); else: echo stripslashes($EDITDATA['website_name'] ?? '');endif; ?>" class="form-control required" placeholder="Website">
                          <?php if(form_error('website_name')): ?>
                            <span for="website_name" generated="true" class="help-inline"><?php echo form_error('website_name'); ?></span>
                          <?php endif; ?>
                        </div>

                        <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('alt_text')): ?>error<?php endif; ?>">
                        <label>Alt Text<span class="required">*</span></label>
                        <input type="text" name="alt_text" id="alt_text" value="<?php if(set_value('alt_text')): echo set_value('alt_text'); else: echo stripslashes($EDITDATA['alt_text'] ?? '');endif; ?>" class="form-control required" placeholder="Alt Text">
                        <?php if(form_error('alt_text')): ?>
                          <span for="alt_text" generated="true" class="help-inline"><?php echo form_error('alt_text'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                        <div class="row">
                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('email_id')): ?>error<?php endif; ?>">
                            <label>Email<span class="required">*</span></label>
                            <input type="text" name="email_id" id="email_id" value="<?php if(set_value('email_id')): echo set_value('email_id'); else: echo stripslashes($EDITDATA['email_id'] ?? '');endif; ?>" class="form-control email required" placeholder="Email">
                            <?php if(form_error('email_id')): ?>
                              <span for="email_id" generated="true" class="help-inline"><?php echo form_error('email_id'); ?></span>
                            <?php endif; ?>
                          </div>
                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('contact_no')): ?>error<?php endif; ?>">
                            <label>Contact<span class="required">*</span></label>
                            <input type="text" name="contact_no" id="contact_no" value="<?php if(set_value('contact_no')): echo set_value('contact_no'); else: echo stripslashes($EDITDATA['contact_no'] ?? '');endif; ?>" class="form-control number required" placeholder="Contact">
                            <?php if(form_error('contact_no')): ?>
                              <span for="contact_no" generated="true" class="help-inline"><?php echo form_error('contact_no'); ?></span>
                            <?php endif; ?>
                          </div>
                        </div>
                        <div class="row">
                          <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('address')): ?>error<?php endif; ?>">
                            <label>Address<span class="required">*</span></label>
                            <textarea id="address" name="address" class=" form-control required" rows="4"><?php if(set_value('address')): echo set_value('address'); else: echo stripslashes($EDITDATA['address'] ?? '');endif; ?></textarea>
                            <?php if(form_error('address')): ?>
                              <span for="address" generated="true" class="help-inline"><?php echo form_error('address'); ?></span>
                            <?php endif; ?>
                          </div>
                        </div>
                        <div class="row">
                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('facebook_link')): ?>error<?php endif; ?>">
                            <label>Facebook Link<span class="required">*</span></label>
                            <input type="text" name="facebook_link" id="facebook_link" value="<?php if(set_value('facebook_link')): echo set_value('facebook_link'); else: echo stripslashes($EDITDATA['facebook_link'] ?? '');endif; ?>" class="form-control url required" placeholder="Facebook Link">
                            <?php if(form_error('facebook_link')): ?>
                              <span for="facebook_link" generated="true" class="help-inline"><?php echo form_error('facebook_link'); ?></span>
                            <?php endif; ?>
                          </div>
                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('linkedin_link')): ?>error<?php endif; ?>">
                            <label>Linkedin Link<span class="required">*</span></label>
                            <input type="text" name="linkedin_link" id="linkedin_link" value="<?php if(set_value('linkedin_link')): echo set_value('linkedin_link'); else: echo stripslashes($EDITDATA['linkedin_link'] ?? '');endif; ?>" class="form-control url required" placeholder="Linkedin Link">
                            <?php if(form_error('linkedin_link')): ?>
                              <span for="linkedin_link" generated="true" class="help-inline"><?php echo form_error('linkedin_link'); ?></span>
                            <?php endif; ?>
                          </div>
                        </div>
                        <div class="row">
                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('twitter_link')): ?>error<?php endif; ?>">
                            <label>Twitter Link<span class="required">*</span></label>
                            <input type="text" name="twitter_link" id="twitter_link" value="<?php if(set_value('twitter_link')): echo set_value('twitter_link'); else: echo stripslashes($EDITDATA['twitter_link'] ?? '');endif; ?>" class="form-control url required" placeholder="Twitter Link">
                            <?php if(form_error('twitter_link')): ?>
                              <span for="twitter_link" generated="true" class="help-inline"><?php echo form_error('twitter_link'); ?></span>
                            <?php endif; ?>
                          </div>
                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('insta_link')): ?>error<?php endif; ?>">
                            <label>Instagram Link<span class="required">*</span></label>
                            <input type="text" name="insta_link" id="insta_link" value="<?php if(set_value('insta_link')): echo set_value('insta_link'); else: echo stripslashes($EDITDATA['insta_link'] ?? '');endif; ?>" class="form-control url required" placeholder="Twitter Link">
                            <?php if(form_error('insta_link')): ?>
                              <span for="insta_link" generated="true" class="help-inline"><?php echo form_error('insta_link'); ?></span>
                            <?php endif; ?>
                          </div>
                        </div>
                        <div class="row">
                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('you_tube')): ?>error<?php endif; ?>">
                            <label>You Tube<span class="required">*</span></label>
                            <input type="text" name="you_tube" id="you_tube" value="<?php if(set_value('you_tube')): echo set_value('you_tube'); else: echo stripslashes($EDITDATA['you_tube'] ?? '');endif; ?>" class="form-control url required" placeholder="YouTube Link">
                            <?php if(form_error('you_tube')): ?>
                              <span for="you_tube" generated="true" class="help-inline"><?php echo form_error('you_tube'); ?></span>
                            <?php endif; ?>
                          </div>
                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('slider_type')): ?>error<?php endif; ?>">
                            <label>Slider Type<span class="required">*</span></label>
                            <select name="slider_type" id="slider_type" class="form-control required">
                              <option>Select Slider Type</option>
                              <option value="Video" <?php if($EDITDATA['slider_type'] ?? '' == 'Video'): echo 'selected'; endif; ?> >Video</option>
                              <option value="Image" <?php if($EDITDATA['slider_type'] ?? '' == 'Image'): echo 'selected'; endif; ?>>Image</option>
                            </select>
                            <!-- <input type="text" name="slider_type" id="slider_type" value="<?php if(set_value('slider_type')): echo set_value('slider_type'); else: echo stripslashes($EDITDATA['slider_type'] ?? '');endif; ?>" class="form-control url required" placeholder="Slider Type"> -->
                            <?php if(form_error('slider_type')): ?>
                              <span for="slider_type" generated="true" class="help-inline"><?php echo form_error('slider_type'); ?></span>
                            <?php endif; ?>
                          </div>

                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('whatsapp_no')): ?><?php endif; ?>">
                            <label>WhatsApp Number<span class="required">*</span></label>
                            <input type="text" name="whatsapp_no" id="whatsapp_no" value="<?php if(set_value('whatsapp_no')): echo set_value('whatsapp_no'); else: echo stripslashes($EDITDATA['whatsapp_no'] ?? '');endif; ?>" class="form-control valid required" placeholder="WhatsApp Number">
                            <?php if(form_error('whatsapp_no')): ?>
                              <span for="whatsapp_no" generated="true" class="help-inline"><?php echo form_error('whatsapp_no'); ?></span>
                            <?php endif; ?>
                          </div>

                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('whatsapp_authorization_key')): ?><?php endif; ?>">
                            <label>WhatsApp Authorization Key<span class="required">*</span></label>
                            <input type="text" name="whatsapp_authorization_key" id="whatsapp_authorization_key" value="<?php if(set_value('whatsapp_authorization_key')): echo set_value('whatsapp_authorization_key'); else: echo stripslashes($EDITDATA['whatsapp_authorization_key'] ?? '');endif; ?>" class="form-control valid required" placeholder="WhatsApp Authorization Key">
                            <?php if(form_error('whatsapp_authorization_key')): ?>
                              <span for="whatsapp_authorization_key" generated="true" class="help-inline"><?php echo form_error('whatsapp_authorization_key'); ?></span>
                            <?php endif; ?>
                          </div>

                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('android_version')): ?><?php endif; ?>">
                            <label>Android Version <span class="required">*</span></label>
                            <input type="text" name="android_version" id="android_version" value="<?php if(set_value('android_version')): echo set_value('android_version'); else: echo stripslashes($EDITDATA['android_version'] ?? '');endif; ?>" class="form-control valid required" placeholder="Appication Version">
                            <?php if(form_error('android_version')): ?>
                              <span for="android_version" generated="true" class="help-inline"><?php echo form_error('android_version'); ?></span>
                            <?php endif; ?>
                          </div>

                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('ios_version')): ?><?php endif; ?>">
                            <label>IOS Version <span class="required">*</span></label>
                            <input type="text" name="ios_version" id="ios_version" value="<?php if(set_value('ios_version')): echo set_value('ios_version'); else: echo stripslashes($EDITDATA['ios_version'] ?? '');endif; ?>" class="form-control valid required" placeholder="Appication Version">
                            <?php if(form_error('ios_version')): ?>
                              <span for="ios_version" generated="true" class="help-inline"><?php echo form_error('ios_version'); ?></span>
                            <?php endif; ?>
                          </div>

                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('draw_time_start')): ?><?php endif; ?>">
                            <label>Draw Time Start <span class="required">*</span></label>
                            <input type="time" name="draw_time_start" id="draw_time_start" value="<?php if(set_value('draw_time_start')): echo set_value('draw_time_start'); else: echo stripslashes($EDITDATA['draw_time_start'] ?? '');endif; ?>" class="form-control valid required" placeholder="Draw Time Start">
                            <?php if(form_error('draw_time_start')): ?>
                              <span for="draw_time_start" generated="true" class="help-inline"><?php echo form_error('draw_time_start'); ?></span>
                            <?php endif; ?>
                          </div>

                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('draw_time_end')): ?><?php endif; ?>">
                            <label>Draw Time End <span class="required">*</span></label>
                            <input type="time" name="draw_time_end" id="draw_time_end" value="<?php if(set_value('draw_time_end')): echo set_value('draw_time_end'); else: echo stripslashes($EDITDATA['draw_time_end'] ?? '');endif; ?>" class="form-control valid required" placeholder="Draw Time End">
                            <?php if(form_error('draw_time_end')): ?>
                              <span for="draw_time_end" generated="true" class="help-inline"><?php echo form_error('draw_time_end'); ?></span>
                            <?php endif; ?>
                          </div>

                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('app_url')): ?><?php endif; ?>">
                            <label>App URL <span class="required">*</span></label>
                            <input type="url" name="app_url" id="app_url" value="<?php if(set_value('app_url')): echo set_value('app_url'); else: echo stripslashes($EDITDATA['app_url'] ?? '');endif; ?>" class="form-control valid required" placeholder="Application URL">
                            <?php if(form_error('app_url')): ?>
                              <span for="app_url" generated="true" class="help-inline"><?php echo form_error('app_url'); ?></span>
                            <?php endif; ?>
                          </div>

                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('delivery_charge')): ?><?php endif; ?>">
                            <label>Delivery Charge <span class="required">*</span></label>
                            <input type="number" name="delivery_charge" id="delivery_charge" value="<?php if(set_value('delivery_charge')): echo set_value('delivery_charge'); else: echo stripslashes($EDITDATA['delivery_charge'] ?? '');endif; ?>" class="form-control valid required" placeholder="Delivery Charge">
                            <?php if(form_error('delivery_charge')): ?>
                              <span for="delivery_charge" generated="true" class="help-inline"><?php echo form_error('delivery_charge'); ?></span>
                            <?php endif; ?>
                          </div>

                           <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('drawdata_pin')): ?><?php endif; ?>">
                            <label>Draw Data Pin <span class="required">*</span></label>
                            <input type="text" name="drawdata_pin" id="drawdata_pin" value="<?php if(set_value('drawdata_pin')): echo set_value('drawdata_pin'); else: echo stripslashes($EDITDATA['drawdata_pin'] ?? '');endif; ?>" class="form-control valid required" placeholder="Draw Data Pin">
                            <?php if(form_error('drawdata_pin')): ?>
                              <span for="drawdata_pin" generated="true" class="help-inline"><?php echo form_error('drawdata_pin'); ?></span>
                            <?php endif; ?>
                          </div>

                           <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('show_merchant_id')): ?>error<?php endif; ?>">
                            <label>Show Merchant Id<span class="required">*</span></label>
                            <select name="show_merchant_id" id="show_merchant_id" class="form-control required">
                              <option>Select </option>
                              <option value="N" <?php if($EDITDATA['show_merchant_id'] ?? '' == 'N'): echo 'selected'; endif; ?> >No</option>
                              <option value="Y" <?php if($EDITDATA['show_merchant_id'] ?? '' == 'Y'): echo 'selected'; endif; ?>>Yes</option>
                            </select>
                            <?php if(form_error('show_merchant_id')): ?>
                              <span for="show_merchant_id" generated="true" class="help-inline"><?php echo form_error('show_merchant_id'); ?></span>
                            <?php endif; ?>
                          </div>
                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('show_merchant_name')): ?>error<?php endif; ?>">
                            <label>Show Merchant Name<span class="required">*</span></label>
                            <select name="show_merchant_name" id="show_merchant_name" class="form-control required">
                              <option>Select </option>
                              <option value="N" <?php if($EDITDATA['show_merchant_name'] ?? '' == 'N'): echo 'selected'; endif; ?> >No</option>
                              <option value="Y" <?php if($EDITDATA['show_merchant_name'] ?? '' == 'Y'): echo 'selected'; endif; ?>>Yes</option>
                            </select>
                            <?php if(form_error('show_merchant_name')): ?>
                              <span for="show_merchant_name" generated="true" class="help-inline"><?php echo form_error('show_merchant_name'); ?></span>
                            <?php endif; ?>
                          </div>

                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('show_bank_widhdrawal')): ?>error<?php endif; ?>">
                            <label>Enable/Disable Bank Withdrawal<span class="required">*</span></label>
                            <select name="show_bank_widhdrawal" id="show_bank_widhdrawal" class="form-control required">
                              <option>Select </option>
                              <option value="N" <?php if($EDITDATA['show_bank_widhdrawal'] ?? '' == 'N'): echo 'selected'; endif; ?> >No</option>
                              <option value="Y" <?php if($EDITDATA['show_bank_widhdrawal'] ?? '' == 'Y'): echo 'selected'; endif; ?>>Yes</option>
                            </select>
                            <?php if(form_error('show_bank_widhdrawal')): ?>
                              <span for="show_bank_widhdrawal" generated="true" class="help-inline"><?php echo form_error('show_bank_widhdrawal'); ?></span>
                            <?php endif; ?>
                          </div>

                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('btc_bank_withdrawal')): ?><?php endif; ?>">
                            <label>b2c Bank withdrawal  <span class="required">*</span></label>
                            <input type="number" name="btc_bank_withdrawal" id="btc_bank_withdrawal" value="<?php if(set_value('btc_bank_withdrawal')): echo set_value('btc_bank_withdrawal'); else: echo stripslashes($EDITDATA['btc_bank_withdrawal'] ?? '');endif; ?>" class="form-control valid required" placeholder="b2c Bank withdrawal">
                            <?php if(form_error('btc_bank_withdrawal')): ?>
                              <span for="btc_bank_withdrawal" generated="true" class="help-inline"><?php echo form_error('btc_bank_withdrawal'); ?></span>
                            <?php endif; ?>
                          </div>

                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('show_crypto')): ?>error<?php endif; ?>">
                            <label>Enable/Disable Crypto Withdrawal<span class="required">*</span></label>
                            <select name="show_crypto" id="show_crypto" class="form-control required">
                              <option>Select </option>
                              <option value="N" <?php if($EDITDATA['show_crypto'] ?? '' == 'N'): echo 'selected'; endif; ?> >No</option>
                              <option value="Y" <?php if($EDITDATA['show_crypto'] ?? '' == 'Y'): echo 'selected'; endif; ?>>Yes</option>
                            </select>
                            <?php if(form_error('show_crypto')): ?>
                              <span for="show_crypto" generated="true" class="help-inline"><?php echo form_error('show_crypto'); ?></span>
                            <?php endif; ?>
                          </div>

                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('btc_crypto_limit')): ?><?php endif; ?>">
                            <label>b2c Crypto withdrawal limit  <span class="required">*</span></label>
                            <input type="number" name="btc_crypto_limit" id="btc_crypto_limit" value="<?php if(set_value('btc_crypto_limit')): echo set_value('btc_crypto_limit'); else: echo stripslashes($EDITDATA['btc_crypto_limit'] ?? '');endif; ?>" class="form-control valid required" placeholder="b2c Crypto withdrawal">
                            <?php if(form_error('btc_crypto_limit')): ?>
                              <span for="btc_crypto_limit" generated="true" class="help-inline"><?php echo form_error('btc_crypto_limit'); ?></span>
                            <?php endif; ?>
                          </div>

                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('summary_time')): ?><?php endif; ?>">
                            <label>Summary Time  <span class="required">*</span></label>
                            <input type="time" name="summary_time" id="summary_time" value="<?php if(set_value('summary_time')): echo set_value('summary_time'); else: echo stripslashes($EDITDATA['summary_time'] ?? '');endif; ?>" class="form-control valid required" placeholder="Summary Time">
                            <?php if(form_error('summary_time')): ?>
                              <span for="summary_time" generated="true" class="help-inline"><?php echo form_error('summary_time'); ?></span>
                            <?php endif; ?>
                          </div>

                          <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('global_freezing')): ?>error<?php endif; ?>">
                            <label>Global Freezing<span class="required">*</span></label>
                            <select name="global_freezing" id="global_freezing" class="form-control required">
                              <option value="" >Select </option>
                              <option value="disable" <?php if($EDITDATA['global_freezing'] ?? '' == 'disable'): echo 'selected'; endif; ?> >No</option>
                              <option value="enable" <?php if($EDITDATA['global_freezing'] ?? '' == 'enable'): echo 'selected'; endif; ?>>Yes</option>
                            </select>
                            <?php if(form_error('global_freezing')): ?>
                              <span for="global_freezing" generated="true" class="help-inline"><?php echo form_error('global_freezing'); ?></span>
                            <?php endif; ?>
                          </div>

                          <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <fieldset>
                              <legend>Recharge Topup Section</legend>
                              <div class="row">
                                  <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('recharge_topup_btn')): ?>error<?php endif; ?>">
                                    <label> Recharge Topup <span class="required">*</span> </label>
                                    <select name="recharge_topup_btn" id="recharge_topup_btn" class="form-control  required">
                                      <option value="">Select </option>
                                      <option value="N" <?php if($EDITDATA['recharge_topup_btn'] ?? '' == 'N'): echo 'selected'; endif; ?> >No</option>
                                      <option value="Y" <?php if($EDITDATA['recharge_topup_btn'] ?? '' == 'Y'): echo 'selected'; endif; ?>>Yes</option>
                                    </select>
                                    <?php if(form_error('recharge_topup_btn')): ?>
                                      <span for="recharge_topup_btn" generated="true" class="help-inline"><?php echo form_error('recharge_topup_btn'); ?></span>
                                    <?php endif; ?>
                                  </div>

                                  <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('recharge_topup_start_time')): ?><?php endif; ?>">
                                    <label> Recharge Topup Start Time  <span class="required">*</span></label>
                                    <input type="time" name="recharge_topup_start_time" id="recharge_topup_start_time" value="<?php if(set_value('recharge_topup_start_time')): echo set_value('recharge_topup_start_time'); else: echo stripslashes($EDITDATA['recharge_topup_start_time'] ?? '');endif; ?>" class="form-control valid required" placeholder="Summary Time">
                                    <?php if(form_error('recharge_topup_start_time')): ?>
                                      <span for="recharge_topup_start_time" generated="true" class="help-inline"><?php echo form_error('recharge_topup_start_time'); ?></span>
                                    <?php endif; ?>
                                  </div>

                                  <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('recharge_topup_end_time')): ?><?php endif; ?>">
                                    <label> Recharge Topup End Time  <span class="required">*</span></label>
                                    <input type="time" name="recharge_topup_end_time" id="recharge_topup_end_time" value="<?php if(set_value('recharge_topup_end_time')): echo set_value('recharge_topup_end_time'); else: echo stripslashes($EDITDATA['recharge_topup_end_time'] ?? '');endif; ?>" class="form-control valid required" placeholder="Summary Time">
                                    <?php if(form_error('recharge_topup_end_time')): ?>
                                      <span for="recharge_topup_end_time" generated="true" class="help-inline"><?php echo form_error('recharge_topup_end_time'); ?></span>
                                    <?php endif; ?>
                                  </div>

                                  <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('recharge_topup_msg')): ?><?php endif; ?>">
                                    <label> Recharge Topup Messsage  <span class="required">*</span></label>
                                    <input type="text" name="recharge_topup_msg" id="recharge_topup_msg" value="<?php if(set_value('recharge_topup_msg')): echo set_value('recharge_topup_msg'); else: echo stripslashes($EDITDATA['recharge_topup_msg'] ?? '');endif; ?>" class="form-control valid required" placeholder="Summary Time">
                                    <?php if(form_error('recharge_topup_msg')): ?>
                                      <span for="recharge_topup_msg" generated="true" class="help-inline"><?php echo form_error('recharge_topup_msg'); ?></span>
                                    <?php endif; ?>
                                  </div>

                              </div>
                            </fieldset>
                          </div>

                          <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <fieldset>
                              <legend>Page Heading section</legend>

                              <div class="row">
                                  <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('comming_soon_pro_btn')): ?>error<?php endif; ?>">
                                    <label>Comming Soon Button <span class="required">*</span> <sub> ( Pos Campaign in App )</sub></label>
                                    <select name="comming_soon_pro_btn" id="comming_soon_pro_btn" class="form-control required">
                                      <option>Select </option>
                                      <option value="N" <?php if($EDITDATA['comming_soon_pro_btn'] ?? '' == 'N'): echo 'selected'; endif; ?> >No</option>
                                      <option value="Y" <?php if($EDITDATA['comming_soon_pro_btn'] ?? '' == 'Y'): echo 'selected'; endif; ?>>Yes</option>
                                    </select>
                                    <?php if(form_error('comming_soon_pro_btn')): ?>
                                      <span for="comming_soon_pro_btn" generated="true" class="help-inline"><?php echo form_error('comming_soon_pro_btn'); ?></span>
                                    <?php endif; ?>
                                  </div>

                                  <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('comming_soon_text')): ?><?php endif; ?>">
                                    <label>Comming Soon <span class="required">*</span></label>
                                    <input type="text" name="comming_soon_text" id="comming_soon_text" value="<?php if(set_value('comming_soon_text')): echo set_value('comming_soon_text'); else: echo stripslashes($EDITDATA['comming_soon_text'] ?? '');endif; ?>" class="form-control valid required" placeholder="Comming soon text">
                                    <?php if(form_error('comming_soon_text')): ?>
                                      <span for="comming_soon_text" generated="true" class="help-inline"><?php echo form_error('comming_soon_text'); ?></span>
                                    <?php endif; ?>
                                  </div>

                                  <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('home_botttom_slider_header')): ?><?php endif; ?>">
                                    <label>Slider Title ( Home Page ) <span class="required">*</span></label>
                                    <input type="text" name="home_botttom_slider_header" id="home_botttom_slider_header" value="<?php if(set_value('home_botttom_slider_header')): echo set_value('home_botttom_slider_header'); else: echo stripslashes($EDITDATA['home_botttom_slider_header'] ?? '');endif; ?>" class="form-control valid required" placeholder="slider Title">
                                    <?php if(form_error('home_botttom_slider_header')): ?>
                                      <span for="home_botttom_slider_header" generated="true" class="help-inline"><?=form_error('home_botttom_slider_header');?></span>
                                    <?php endif; ?>
                                  </div>

                                  <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('prize_title')): ?><?php endif; ?>">
                                    <label>Prize Title ( Product Detail Page ) <span class="required">*</span></label>
                                    <input type="text" name="prize_title" id="prize_title" value="<?php if(set_value('prize_title')): echo set_value('prize_title'); else: echo stripslashes($EDITDATA['prize_title'] ?? '');endif; ?>" class="form-control valid required" placeholder="Prize Title">
                                    <?php if(form_error('prize_title')): ?>
                                      <span for="prize_title" generated="true" class="help-inline"><?=form_error('prize_title');?></span>
                                    <?php endif; ?>
                                  </div>

                                  <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('prize_title')): ?><?php endif; ?>">
                                    <label>Hourly Game Blank Page Title <span class="required">*</span></label>
                                    <input type="text" name="hourly_game_blank_page_title" id="hourly_game_blank_page_title" value="<?php if(set_value('hourly_game_blank_page_title')): echo set_value('hourly_game_blank_page_title'); else: echo stripslashes($EDITDATA['hourly_game_blank_page_title'] ?? '');endif; ?>" class="form-control valid required" placeholder="Hourly Game Blank Page Title">
                                    <?php if(form_error('hourly_game_blank_page_title')): ?>
                                      <span for="hourly_game_blank_page_title" generated="true" class="help-inline"><?=form_error('hourly_game_blank_page_title');?></span>
                                    <?php endif; ?>
                                  </div>

                                   <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('enable_u_points_in_pos')): ?>error<?php endif; ?>">
                                    <label>U points in POS <span class="required">*</span> <sub> ( Pos Campaign in App )</sub></label>
                                    <select name="enable_u_points_in_pos" id="enable_u_points_in_pos" class="form-control required">
                                      <option>Select </option>
                                      <option value="N" <?php if($EDITDATA['enable_u_points_in_pos'] ?? '' == 'N'): echo 'selected'; endif; ?> >No</option>
                                      <option value="Y" <?php if($EDITDATA['enable_u_points_in_pos'] ?? '' == 'Y'): echo 'selected'; endif; ?>>Yes</option>
                                    </select>
                                    <?php if(form_error('enable_u_points_in_pos')): ?>
                                      <span for="enable_u_points_in_pos" generated="true" class="help-inline"><?php echo form_error('enable_u_points_in_pos'); ?></span>
                                    <?php endif; ?>
                                  </div>

                                  <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('enable_u_points_in_pos')): ?>error<?php endif; ?>">
                                    <label>Enable Raffle Entries <span class="required">*</span> </label>
                                    <select name="enable_raffle_entries" id="enable_raffle_entries" class="form-control required">
                                      <option>Select </option>
                                      <option value="N" <?php if($EDITDATA['enable_raffle_entries'] ?? '' == 'N'): echo 'selected'; endif; ?> >No</option>
                                      <option value="Y" <?php if($EDITDATA['enable_raffle_entries'] ?? '' == 'Y'): echo 'selected'; endif; ?>>Yes</option>
                                    </select>
                                    <?php if(form_error('enable_raffle_entries')): ?>
                                      <span for="enable_raffle_entries" generated="true" class="help-inline"><?php echo form_error('enable_raffle_entries'); ?></span>
                                    <?php endif; ?>
                                  </div>

                              </div>
                              
                            </fieldset>
                          </div>
                          
                           <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <fieldset>
                              <legend>B2B Mobile </legend>

                              <div class="row">
                                 
                                  <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('b2b_mobile_qr_code')): ?>error<?php endif; ?>">
                                    <label>Show QR Code  <span class="required">*</span> <sub> ( Pos Campaign in App )</sub></label>
                                    <select name="b2b_mobile_qr_code" id="b2b_mobile_qr_code" class="form-control required">
                                      <option>Select </option>
                                      <option value="N" <?php if($EDITDATA['b2b_mobile_qr_code'] ?? '' == 'N'): echo 'selected'; endif; ?> >No</option>
                                      <option value="Y" <?php if($EDITDATA['b2b_mobile_qr_code'] ?? '' == 'Y'): echo 'selected'; endif; ?>>Yes</option>
                                    </select>
                                    <?php if(form_error('b2b_mobile_qr_code')): ?>
                                      <span for="b2b_mobile_qr_code" generated="true" class="help-inline"><?php echo form_error('b2b_mobile_qr_code'); ?></span>
                                    <?php endif; ?>
                                  </div>

                                  <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('b2b_mobile_qr_code_time_sec')): ?><?php endif; ?>">
                                    <label>Qr Code Time (sec) <span class="required">*</span></label>
                                    <input type="text" name="b2b_mobile_qr_code_time_sec" id="b2b_mobile_qr_code_time_sec" value="<?php if(set_value('b2b_mobile_qr_code_time_sec')): echo set_value('b2b_mobile_qr_code_time_sec'); else: echo stripslashes($EDITDATA['b2b_mobile_qr_code_time_sec'] ?? '');endif; ?>" class="form-control valid required" placeholder="Comming soon text">
                                    <?php if(form_error('b2b_mobile_qr_code_time_sec')): ?>
                                      <span for="b2b_mobile_qr_code_time_sec" generated="true" class="help-inline"><?php echo form_error('b2b_mobile_qr_code_time_sec'); ?></span>
                                    <?php endif; ?>
                                  </div>
                              </div>
                              
                            </fieldset>
                          </div>
                                      

                        </div>
                         
                    </fieldset>
                   <div class="row">
                      <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="inline-remember-me mt-4">
                          <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                          <button class="btn btn-primary mb-4">Submit</button>
                          <a href="<?php echo correctLink('CMSGENERALDATA',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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
  $(function(){create_editor_for_textarea('contact_description1')});
      $(function(){create_editor_for_textarea('address')});
      $(function(){create_editor_for_textarea('description2')});
      $(function(){create_editor_for_textarea('recharge_description')});

</script>
<script>

  function VideoDelete(imageName,id)
  {//alert(id);
    if(confirm("Sure to delete?"))
    {//alert(CURRENTCLASS);
      $.ajax({
            type: 'post',
             url: FULLSITEURL+'website/'+CURRENTCLASS+'/videoDelete',
            data: {imageName:imageName,id,id},
         success: function(rdata) { 
              if(parseInt(rdata.status) == 1) {
                $('#image').val('');
                $('#videoDiv').html('');
              }
              return false;
            }
      });
    }
  }
</script>
<script>

  function ImageDelete(imageName,id)
  {//alert(id);
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