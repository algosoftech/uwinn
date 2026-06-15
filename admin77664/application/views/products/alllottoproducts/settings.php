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
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLLOTOPRODUCTSDATA',getCurrentControllerPath('index')); ?>"> Product</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Campaign Settings</a></li>
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
                        <h5><?=$EDITDATA?'Edit':'Add'?> Campaign Setting</h5>
                        <a href="<?php echo correctLink('ALLLOTOPRODUCTSDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>
                    <div class="card-body">
                            <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="<?=$EDITDATA['lotto_settings_id']?'lotto_settings_id':'products_id'; ?> "/>
                                <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['lotto_settings_id']?$EDITDATA['lotto_settings_id']:$EDITDATA['products_id']; ?>"/>
                                <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['lotto_settings_id']?$EDITDATA['lotto_settings_id']:$EDITDATA['products_id']; ?>"/>
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                              
                            <div class="row">
                                  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <fieldset>
                                        <legend>Checkout - Game Mode Name</legend>
                                        <div class="row">
                                           <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('straight_game_name')): ?>error<?php endif; ?>">
                                                <label> Option 1 <span class="required">*</span></label>
                                                <input type="text" name="straight_game_name" id="straight_game_name" class="form-control required" value="<?=$EDITDATA['straight_game_name']?>">
                                                <?php if(form_error('straight_game_name')): ?>
                                                  <span for="straight_game_name" generated="true" class="help-inline"><?php echo form_error('straight_game_name'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('rumble_game_name')): ?>error<?php endif; ?>">
                                                <label> Option 2 <span class="required">*</span></label>
                                                <input type="text" name="rumble_game_name" id="rumble_game_name" class="form-control required" value="<?=$EDITDATA['rumble_game_name']?>">
                                                <?php if(form_error('rumble_game_name')): ?>
                                                  <span for="rumble_game_name" generated="true" class="help-inline"><?php echo form_error('rumble_game_name'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('reverse_game_name')): ?>error<?php endif; ?>">
                                                <label> Option 3 <span class="required">*</span></label>
                                                <input type="text" name="reverse_game_name" id="reverse_game_name" class="form-control required" value="<?=$EDITDATA['reverse_game_name']?>">
                                                <?php if(form_error('reverse_game_name')): ?>
                                                  <span for="reverse_game_name" generated="true" class="help-inline"><?php echo form_error('reverse_game_name'); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <legend>Checkout - Game Mode Enable/Disable</legend>
                                        <div class="row">
                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('straight_settings')): ?>error<?php endif; ?>">
                                                <label> Straight (iPoints) <span class="required">*</span></label>
                                                <select name="straight_settings" id="straight_settings" class="form-control required">
                                                    <option value="">Select</option>
                                                    <option value="Enable" <?php if($EDITDATA['straight_settings']== 'Enable' ):echo 'selected'; endif; ?> >Enable</option>
                                                    <option value="Disable" <?php if($EDITDATA['straight_settings']== 'Disable' ):echo 'selected'; endif; ?> >Disable</option>
                                                </select>
                                                <?php if(form_error('straight_settings')): ?>
                                                  <span for="straight_settings" generated="true" class="help-inline"><?php echo form_error('straight_settings'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('rumble_settings')): ?>error<?php endif; ?>">
                                                <label> Rumble Mix (iPoints) <span class="required">*</span></label>
                                                <select name="rumble_settings" id="rumble_settings" class="form-control required">
                                                    <option value="">Select</option>
                                                    <option value="Enable" <?php if($EDITDATA['rumble_settings']== 'Enable' ):echo 'selected'; endif; ?> >Enable</option>
                                                    <option  value="Disable" <?php if($EDITDATA['rumble_settings']== 'Disable' ):echo 'selected'; endif; ?> >Disable</option>
                                                </select>
                                                <?php if(form_error('rumble_settings')): ?>
                                                  <span for="rumble_settings" generated="true" class="help-inline"><?php echo form_error('rumble_settings'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('reverse_settings')): ?>error<?php endif; ?>">
                                                <label> Chance ( iPoints ) <span class="required">*</span></label>
                                                <select name="reverse_settings" id="reverse_settings" class="form-control required">
                                                    <option value="">Select</option>
                                                    <option <?php if($EDITDATA['reverse_settings']== 'Enable' ):echo 'selected'; endif; ?>   value="Enable">Enable</option>
                                                    <option <?php if($EDITDATA['reverse_settings']== 'Disable' ):echo 'selected'; endif; ?>  value="Disable">Disable</option>
                                                </select>
                                                <?php if(form_error('reverse_settings')): ?>
                                                  <span for="reverse_settings" generated="true" class="help-inline"><?php echo form_error('reverse_settings'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                        </div>

                                        <legend> Checkbox Settings</legend>
                                        <div class="row">
                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('straight_settings_default_check')): ?>error<?php endif; ?>">
                                                <label> Straight (Checkbox) <span class="required">*</span></label>
                                                <select name="straight_settings_default_check" id="straight_settings_default_check" class="form-control required">
                                                    <option value="">Select</option>
                                                    <option value="Unchecked" <?php if($EDITDATA['straight_settings_default_check']== 'Unchecked' ):echo 'selected'; endif; ?> >Unchecked</option>
                                                    <option value="Checked" <?php if($EDITDATA['straight_settings_default_check']== 'Checked' ):echo 'selected'; endif; ?> >Checked</option>
                                                </select>
                                                <?php if(form_error('straight_settings_default_check')): ?>
                                                  <span for="straight_settings_default_check" generated="true" class="help-inline"><?php echo form_error('straight_settings_default_check'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('rumble_settings_default_check')): ?>error<?php endif; ?>">
                                                <label> Rumble Mix (Checkbox) <span class="required">*</span></label>
                                                <select name="rumble_settings_default_check" id="rumble_settings_default_check" class="form-control required">
                                                    <option value="">Select</option>
                                                    <option  value="Unchecked" <?php if($EDITDATA['rumble_settings_default_check']== 'Unchecked' ):echo 'selected'; endif; ?> >Unchecked</option>
                                                    <option value="Checked" <?php if($EDITDATA['rumble_settings_default_check']== 'Checked' ):echo 'selected'; endif; ?> >Checked</option>
                                                </select>
                                                <?php if(form_error('rumble_settings_default_check')): ?>
                                                  <span for="rumble_settings_default_check" generated="true" class="help-inline"><?php echo form_error('rumble_settings_default_check'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('reverse_settings_default_check')): ?>error<?php endif; ?>">
                                                <label> Chance ( Checkbox ) <span class="required">*</span></label>
                                                <select name="reverse_settings_default_check" id="reverse_settings_default_check" class="form-control required">
                                                    <option value="">Select</option>
                                                    <option <?php if($EDITDATA['reverse_settings_default_check']== 'Unchecked' ):echo 'selected'; endif; ?>  value="Unchecked">Unchecked</option>
                                                    <option <?php if($EDITDATA['reverse_settings_default_check']== 'Checked' ):echo 'selected'; endif; ?>   value="Checked">Checked</option>
                                                </select>
                                                <?php if(form_error('reverse_settings_default_check')): ?>
                                                  <span for="reverse_settings_default_check" generated="true" class="help-inline"><?php echo form_error('reverse_settings_default_check'); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                    </fieldset>

                                    <fieldset>
                                        <legend>Campaign Freezing ( Enable/Disable )</legend>
                                        <div class="row">

                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('campaign_auto_freezing_mode')): ?>error<?php endif; ?>">
                                                <label> Campaign Auto Freezing Mode <span class="required">*</span></label>
                                                <select name="campaign_auto_freezing_mode" id="campaign_auto_freezing_mode" class="form-control required">
                                                    <option value="">Select</option>
                                                    <option <?php if($EDITDATA['campaign_auto_freezing_mode']== 'Enable' ):echo 'selected'; endif; ?>   value="Enable">Enable</option>
                                                    <option <?php if($EDITDATA['campaign_auto_freezing_mode']== 'Disable' ):echo 'selected'; endif; ?>  value="Disable">Disable</option>
                                                </select>
                                                <?php if(form_error('campaign_auto_freezing_mode')): ?>
                                                  <span for="campaign_auto_freezing_mode" generated="true" class="help-inline"><?php echo form_error('campaign_auto_freezing_mode'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('campaign_freezing_start_time')): ?>error<?php endif; ?>">
                                                <label>Campaign Freezing Start Time<span class="required">*</span></label>
                                                <input type="time" name="campaign_freezing_start_time" id="campaign_freezing_start_time" class="form-control required" value="<?php if(set_value('campaign_freezing_start_time')): echo set_value('campaign_freezing_start_time'); else: echo stripslashes($EDITDATA['campaign_freezing_start_time']);endif; ?>" placeholder="Campaign Freezing Start Time">
                                                <?php if(form_error('campaign_freezing_start_time')): ?>
                                                    <span for="campaign_freezing_start_time" generated="true" class="help-inline"><?php echo form_error('campaign_freezing_start_time'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('campaign_freezing_end_time')): ?>error<?php endif; ?>">
                                                <label>Campaign Freezing End Time<span class="required">*</span></label>
                                                <input type="time" name="campaign_freezing_end_time" id="campaign_freezing_end_time" class="form-control required" value="<?php if(set_value('campaign_freezing_end_time')): echo set_value('campaign_freezing_end_time'); else: echo stripslashes($EDITDATA['campaign_freezing_end_time']);endif; ?>" placeholder="Campaign Freezing Start Time">
                                                <?php if(form_error('campaign_freezing_end_time')): ?>
                                                    <span for="campaign_freezing_end_time" generated="true" class="help-inline"><?php echo form_error('campaign_freezing_end_time'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                        </div>
                                    </fieldset>

                                    <fieldset>
                                        <legend>Reffle setings</legend>
                                        <div class="row">

                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('enable_raffle_ticket')): ?>error<?php endif; ?>">
                                                <label> Enable Raffle Ticket <span class="required">*</span></label>
                                                <select name="enable_raffle_ticket" id="enable_raffle_ticket" class="form-control required">
                                                    <option value="">Select</option>
                                                    <option value="Enable" <?php if($EDITDATA['enable_raffle_ticket']== 'Enable' ):echo 'selected'; endif; ?> >Enable</option>
                                                    <option value="Disable" <?php if($EDITDATA['enable_raffle_ticket']== 'Disable' ):echo 'selected'; endif; ?> >Disable</option>
                                                </select>
                                                <?php if(form_error('enable_raffle_ticket')): ?>
                                                  <span for="enable_raffle_ticket" generated="true" class="help-inline"><?php echo form_error('enable_raffle_ticket'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('reffle_prefix')): ?>error<?php endif; ?>">
                                                <label> Reffle Prefix <span class="required">*</span></label>
                                                <input type="text" name="reffle_prefix" id="reffle_prefix" class="form-control required" value="<?=$EDITDATA['reffle_prefix']?>">
                                                <?php if(form_error('reffle_prefix')): ?>
                                                  <span for="reffle_prefix" generated="true" class="help-inline"><?php echo form_error('reffle_prefix'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('reffle_length')): ?>error<?php endif; ?>">
                                                <label> Reffle legth <span class="required">*</span></label>
                                                <input type="text" name="reffle_length" id="reffle_length" class="form-control required" value="<?=$EDITDATA['reffle_length']?>">
                                                <?php if(form_error('reffle_length')): ?>
                                                  <span for="reffle_length" generated="true" class="help-inline"><?php echo form_error('reffle_length'); ?></span>
                                                <?php endif; ?>
                                            </div>

                                        </div>
                                    </fieldset>

                                    <fieldset>
                                        <legend>How to Play in Uwinn?</legend>
                                        <div class="row">
                                            
                                            <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('game_rule_image')): ?>error<?php endif; ?>">
                                                <label>Image</label><br>
                                                <input type="file" name="game_rule_image" id="game_rule_image" class="" value="<?php if(set_value('game_rule_image')): echo set_value('game_rule_image'); endif; ?>" accept="image/png, image/jpeg, image/webp" <?php if(empty($EDITDATA['game_rule_image'])){ ?> required <?php } ?> >
                                                <p style="font-family:italic; color:red;">[Image Size : 241 x 136 px in jpg/jpeg/png]</p>
                                                <?php if($EDITDATA['game_rule_image']): ?>
                                                    <img src="<?php echo fileBaseUrl.$EDITDATA['game_rule_image']; ?>" width="400" border="0" alt="">&nbsp;
                                                <?php endif; ?>
                                                <?php if(form_error('game_rule_image')): ?>
                                                  <span for="game_rule_image" generated="true" class="help-inline"><?php echo form_error('game_rule_image'); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('game_description')): ?>error<?php endif; ?>">
                                                <label>Game Description</label>
                                                <textarea id="dgame_description" placeholder="Game Description"  rows="10" class="form-control" name="game_description"><?php if(set_value('game_description')): echo set_value('game_description'); else: echo stripslashes($EDITDATA['game_description']);endif; ?></textarea>
                                            </div>
                                            
                                        </div>
                                    </fieldset>

                                    <fieldset>
                                        <legend>Choose Colors</legend>
                                        <div class="row">
                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                <label for="primary_color">Primary Color</label>
                                                <input type="color" id="primary_color" name="primary_color" class="form-control" value="<?= isset($EDITDATA['primary_color']) ? $EDITDATA['primary_color'] : '#000000' ?>">
                                            </div>
                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                <label for="secondary_color">Secondary Color</label>
                                                <input type="color" id="secondary_color" name="secondary_color" class="form-control" value="<?= isset($EDITDATA['secondary_color']) ? $EDITDATA['secondary_color'] : '#ffffff' ?>">
                                            </div>
                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                                <label>Gradient Preview</label>
                                                <div id="gradient_preview" style="width: 100%; height: 100px; border: 1px solid #ddd; border-radius: 4px; background: linear-gradient(90deg, <?= isset($EDITDATA['primary_color']) ? $EDITDATA['primary_color'] : '#000000' ?>, <?= isset($EDITDATA['secondary_color']) ? $EDITDATA['secondary_color'] : '#ffffff' ?>);"></div>
                                            </div>
                                        </div>
                                        <script>
                                            // Update gradient preview on color input change
                                            $(document).ready(function() {
                                                function updateGradientPreview() {
                                                    var primary = $("#primary_color").val() || "#000000";
                                                    var secondary = $("#secondary_color").val() || "#ffffff";
                                                    $("#gradient_preview").css("background", "linear-gradient(90deg, " + primary + ", " + secondary + ")");
                                                }
                                                $("#primary_color, #secondary_color").on('input change', updateGradientPreview);
                                                updateGradientPreview(); // Initial set
                                            });
                                        </script>
                                    </fieldset>


                                  </div>
                            </div>      
                                
                            <div class="row">
                                <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="inline-remember-me mt-4">
                                        <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                        <button class="btn btn-primary mb-4">Submit</button>
                                        <a href="<?php echo correctLink('ALLLOTOPRODUCTSDATA',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
                                        <span class="tools pull-right">Note:- <strong><span style="color:#FF0000;">*</span> Indicates Required Fields</strong> </span> 
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        <!-- [ Main Content ] end -->
        </div>
    </div>
</div>

<script type="text/javascript">
  $(function(){create_editor_for_textarea('game_description')});
</script>