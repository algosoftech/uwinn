<style type="text/css">
    .form-check-input {
        position: unset; 
        margin-top: unset;
        margin-left: unset; 
    }
</style>
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
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLLOTOGAMEDATA',getCurrentControllerPath('index')); ?>"> Lotto Games</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Settings</a></li>
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
                        <h5><?=$EDITDATA?'Edit':'Add'?> Settings</h5>
                        <a href="<?php echo correctLink('ALLLOTOGAMEDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>

                    <div class="card-body">
                        <div class="basic-login-inner">
                            <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                                
                                <div class="row">
                                  <div class="form-group-inner col-lg-4 col-md-4 col-sm-12 col-xs-12 <?php if(form_error('title')): ?>error<?php endif; ?>">
                                    <label>Prize Title<span class="required">*</span></label>
                                    <input type="text" name="prize_title" id="prize_title" class="form-control required" value="<?php if(set_value('prize_title')): echo set_value('prize_title'); else: echo stripslashes($EDITDATA['prize_title']);endif; ?>" placeholder="Prize Title">
                                    <?php if(form_error('prize_title')): ?>
                                        <span for="prize_title" generated="true" class="help-inline"><?php echo form_error('prize_title'); ?></span>
                                    <?php endif; ?>
                                  </div>
                                  <div class="form-group-inner col-lg-2 col-md-2 col-sm-2 col-xs-12 <?php if(form_error('game_image')): ?>error<?php endif; ?>">
                                    <label>Prize Image</label><br>
                                    <input type="file" name="prize_image" id="prize_image" class="" value="<?php if(set_value('prize_image')): echo set_value('prize_image'); endif; ?>" accept="image/png, image/jpeg, image/webp" <?php if(empty($EDITDATA['prize_image'])){ ?> required <?php } ?> >
                                    <p style="font-family:italic; color:red;">[Image Size : 241 x 136 px in jpg/jpeg/png]</p>
                                    <?php if($EDITDATA['prize_image']): ?>
                                        <div id="ImageDiv2">
                                        <img src="<?php echo fileBaseUrl.$EDITDATA['prize_image']; ?>" width="50" border="0" alt=""> 
                                        </div>
                                    <?php endif; ?>
                                    <?php if(form_error('prize_image')): ?>
                                        <span for="prize_image" generated="true" class="help-inline"><?php echo form_error('prize_image'); ?></span>
                                    <?php endif; ?>
                                  </div>
                                </div>

                                <div class="row">
                                  
                                  <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('title')): ?>error<?php endif; ?>">
                                    <fieldset>
                                      <legend>Game Mode Settings</legend>
                                      <div class="row">
                                          <div class="form-group-inner col-lg-3 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('is_straight_enable')): ?>error<?php endif; ?>">
                                            <label> Straight (Enable/Disable) <span class="required">*</span></label>
                                            <select name="is_straight_enable" id="is_straight_enable" class="form-control required">
                                                <option value="Y" <?php if($EDITDATA['is_straight_enable']== 'Y' ):echo 'selected'; endif; ?> >Yes</option>
                                                <option value="N" <?php if($EDITDATA['is_straight_enable']== 'N' ):echo 'selected'; endif; ?> >No</option>
                                            </select>
                                            <?php if(form_error('is_straight_enable')): ?>
                                              <span for="is_straight_enable" generated="true" class="help-inline"><?php echo form_error('is_straight_enable'); ?></span>
                                            <?php endif; ?>
                                          </div>
                                          <div class="form-group-inner col-lg-3 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('is_straight_show_hide')): ?>error<?php endif; ?>">
                                            <label> Straight (show/hide) <span class="required">*</span></label>
                                            <select name="is_straight_show_hide" id="is_straight_show_hide" class="form-control required">
                                                <option value="Y" <?php if($EDITDATA['is_straight_show_hide']== 'Y' ):echo 'selected'; endif; ?> >Yes</option>
                                                <option value="N" <?php if($EDITDATA['is_straight_show_hide']== 'N' ):echo 'selected'; endif; ?> >No</option>
                                            </select>
                                            <?php if(form_error('is_straight_show_hide')): ?>
                                              <span for="is_straight_show_hide" generated="true" class="help-inline"><?php echo form_error('is_straight_show_hide'); ?></span>
                                            <?php endif; ?>
                                          </div>
                                          <div class="form-group-inner col-lg-3 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('straight_settings_default_check')): ?>error<?php endif; ?>">
                                            <label> Straight (Checkbox) <span class="required">*</span></label>
                                            <select name="is_straight_checkbox" id="is_straight_checkbox" class="form-control required">
                                                <option value="Checked" <?php if($EDITDATA['is_straight_checkbox']== 'Checked' ):echo 'selected'; endif; ?> >Checked</option>
                                                <option value="Unchecked" <?php if($EDITDATA['is_straight_checkbox']== 'Unchecked' ):echo 'selected'; endif; ?> >Unchecked</option>
                                            </select>
                                            <?php if(form_error('is_straight_checkbox')): ?>
                                              <span for="is_straight_checkbox" generated="true" class="help-inline"><?php echo form_error('is_straight_checkbox'); ?></span>
                                            <?php endif; ?>
                                          </div>
                                          <div class="form-group-inner col-lg-3 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('straight_settings_default_check')): ?>error<?php endif; ?>">
                                            <label> Name of the checkbox <span class="required">*</span></label>
                                            <input type="text" name="straigt_heading_name" id="straigt_heading_name" class="form-control required"   placeholder="Straight Heading Name" value="Straight" >
                                            <?php if(form_error('straigt_heading_name')): ?>
                                              <span for="straigt_heading_name" generated="true" class="help-inline"><?php echo form_error('straigt_heading_name'); ?></span>
                                            <?php endif; ?>
                                          </div>

                                      </div>
                                      <div class="row">
                                        <div class="form-group-inner col-lg-3 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('straight_settings_default_check')): ?>error<?php endif; ?>">
                                          <label> Rumble (enable/disable) <span class="required">*</span></label>
                                          <select name="is_rumble_enable" id="is_rumble_enable" class="form-control required">
                                              <option value="Y" <?php if($EDITDATA['is_rumble_enable']== 'Y' ):echo 'selected'; endif; ?> >Yes</option>
                                              <option value="N" <?php if($EDITDATA['is_rumble_enable']== 'N' ):echo 'selected'; endif; ?> >No</option>
                                          </select>
                                          <?php if(form_error('is_rumble_enable')): ?>
                                            <span for="is_rumble_enable" generated="true" class="help-inline"><?php echo form_error('is_rumble_enable'); ?></span>
                                          <?php endif; ?>
                                        </div>
                                        <div class="form-group-inner col-lg-3 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('is_rumble_show_hide')): ?>error<?php endif; ?>">
                                          <label> Rumble (show/hide) <span class="required">*</span></label>
                                          <select name="is_rumble_show_hide" id="is_rumble_show_hide" class="form-control required">
                                              <option value="Y" <?php if($EDITDATA['is_rumble_show_hide']== 'Y' ):echo 'selected'; endif; ?> >Yes</option>
                                              <option value="N" <?php if($EDITDATA['is_rumble_show_hide']== 'N' ):echo 'selected'; endif; ?> >No</option>
                                          </select>
                                          <?php if(form_error('is_rumble_show_hide')): ?>
                                            <span for="is_rumble_show_hide" generated="true" class="help-inline"><?php echo form_error('is_rumble_show_hide'); ?></span>
                                          <?php endif; ?>
                                        </div>
                                        <div class="form-group-inner col-lg-3 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('straight_settings_default_check')): ?>error<?php endif; ?>">
                                          <label> Rumble (Checkbox) <span class="required">*</span></label>
                                          <select name="is_rumble_checkbox" id="is_rumble_checkbox" class="form-control required">
                                              <option value="Checked" <?php if($EDITDATA['is_straight_checkbox']== 'Checked' ):echo 'selected'; endif; ?> >Checked</option>
                                              <option value="Unchecked" <?php if($EDITDATA['is_rumble_checkbox']== 'Unchecked' ):echo 'selected'; endif; ?> >Unchecked</option>
                                          </select>
                                          <?php if(form_error('is_rumble_checkbox')): ?>
                                            <span for="is_rumble_checkbox" generated="true" class="help-inline"><?php echo form_error('is_rumble_checkbox'); ?></span>
                                          <?php endif; ?>
                                        </div>
                                        <div class="form-group-inner col-lg-3 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('rumble_heading_name')): ?>error<?php endif; ?>">
                                          <label> Name of the checkbox <span class="required">*</span></label>
                                          <input type="text" name="rumble_heading_name" id="rumble_heading_name" class="form-control required"   placeholder="Rumble Heading Name" value="Rumble" >
                                          
                                          <?php if(form_error('rumble_heading_name')): ?>
                                            <span for="rumble_heading_name" generated="true" class="help-inline"><?php echo form_error('rumble_heading_name'); ?></span>
                                          <?php endif; ?>
                                        </div>
                                      </div>
                                      <div class="row">
                                          <div class="form-group-inner col-lg-3 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('is_chance_enable')): ?>error<?php endif; ?>">
                                            <label> Chance (enable/disable) <span class="required">*</span></label>
                                            <select name="is_chance_enable" id="is_chance_enable" class="form-control required">
                                                <option value="Y" <?php if($EDITDATA['is_chance_enable']== 'Y' ):echo 'selected'; endif; ?> >Yes</option>
                                                <option value="N" <?php if($EDITDATA['is_chance_enable']== 'N' ):echo 'selected'; endif; ?> >No</option>
                                            </select>
                                            <?php if(form_error('is_chance_enable')): ?>
                                              <span for="is_chance_enable" generated="true" class="help-inline"><?php echo form_error('is_chance_enable'); ?></span>
                                            <?php endif; ?>
                                          </div>
                                          <div class="form-group-inner col-lg-3 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('is_chance_show_hide')): ?>error<?php endif; ?>">
                                            <label> Chance (show/hide) <span class="required">*</span></label>
                                            <select name="is_chance_show_hide" id="is_chance_show_hide" class="form-control required">
                                                <option value="Y" <?php if($EDITDATA['is_chance_show_hide']== 'Y' ):echo 'selected'; endif; ?> >Yes</option>
                                                <option value="N" <?php if($EDITDATA['is_chance_show_hide']== 'N' ):echo 'selected'; endif; ?> >No</option>
                                            </select>
                                            <?php if(form_error('is_chance_show_hide')): ?>
                                              <span for="is_chance_show_hide" generated="true" class="help-inline"><?php echo form_error('is_chance_show_hide'); ?></span>
                                            <?php endif; ?>
                                          </div>
                                          <div class="form-group-inner col-lg-3 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('is_chance_checkbox')): ?>error<?php endif; ?>">
                                            <label> Chance (Checkbox) <span class="required">*</span></label>
                                            <select name="is_chance_checkbox" id="is_chance_checkbox" class="form-control required">
                                                <option value="Checked" <?php if($EDITDATA['is_chance_checkbox']== 'Checked' ):echo 'selected'; endif; ?> >Checked</option>
                                                <option value="Unchecked" <?php if($EDITDATA['is_chance_checkbox']== 'Unchecked' ):echo 'selected'; endif; ?> >Unchecked</option>
                                            </select>
                                            <?php if(form_error('is_chance_checkbox')): ?>
                                              <span for="is_chance_checkbox" generated="true" class="help-inline"><?php echo form_error('is_chance_checkbox'); ?></span>
                                            <?php endif; ?>
                                          </div>
                                          <div class="form-group-inner col-lg-3 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('chance_heading_name')): ?>error<?php endif; ?>">
                                            <label> Name of the checkbox <span class="required">*</span></label>
                                            <input type="text" name="chance_heading_name" id="chance_heading_name" class="form-control required"   placeholder="Chance Heading Name" value="Chance" >
                                            
                                            <?php if(form_error('chance_heading_name')): ?>
                                              <span for="chance_heading_name" generated="true" class="help-inline"><?php echo form_error('chance_heading_name'); ?></span>
                                            <?php endif; ?>
                                          </div>
                                          
                                      </div>
                                    </fieldset>
                                  </div>

                                  <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('straight_configuration')): ?>error<?php endif; ?> straight_configuration" <?=$EDITDATA['is_straight_enable'] == 'Y' || empty($EDITDATA['is_straight_enable'])  ? 'style="display: block;"' : 'style="display: none;"'?> >
                                    <fieldset>
                                        <legend>Straight Configuration</legend>
                                        <div class="row">
                                          <div class="form-group-inner col-lg-4 col-md-4 col-sm-12 col-xs-12 <?php if(form_error('straight_prize_title')): ?>error<?php endif; ?>">
                                            <label>Straight Prize Title<span class="required">*</span></label>
                                            <input type="text" name="straight_prize_title" id="straight_prize_title" class="form-control required" value="<?php if(set_value('straight_prize_title')): echo set_value('straight_prize_title'); else: echo stripslashes($EDITDATA['straight_prize_title']);endif; ?>" placeholder="Straight Prize Title">
                                            <?php if(form_error('straight_prize_title')): ?>
                                                <span for="straight_prize_title" generated="true" class="help-inline"><?php echo form_error('straight_prize_title'); ?></span>
                                            <?php endif; ?>
                                          </div>
                                          <?php if(!empty($EDITDATA['game_type'])):?>
                                            <?php  for($i=1; $i<=$EDITDATA['game_type']; $i++): ?>  
                                              <div class="form-group-inner col-lg-4 col-md-4 col-sm-12 col-xs-12 <?php if(form_error('title')): ?>error<?php endif; ?>">
                                                <label>prize <?=$i?><span class="required">*</span></label>
                                                <input type="number" name="straight_prize_<?=$i?>" id="straight_prize_<?=$i?>" class="form-control required" value="<?php if(set_value('straight_prize_'.$i)): echo set_value('straight_prize_'.$i); else: echo stripslashes($EDITDATA['straight_prize_'.$i]);endif; ?>" placeholder="Prize Amount">
                                                <?php if(form_error('straight_prize_<?=$i?>')): ?>
                                                    <span for="straight_prize_<?=$i?>" generated="true" class="help-inline"><?php echo form_error('straight_prize_<?=$i?>'); ?></span>
                                                <?php endif; ?>
                                              </div>
                                            <?php endfor;?>
                                          <?php endif;?>
                                        </div>
                                    </fieldset>
                                  </div>

                                  <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('rumble_configuration')): ?>error<?php endif; ?> rumble_configuration" <?=$EDITDATA['is_rumble_enable'] == 'Y' || empty($EDITDATA['is_rumble_enable'])  ? 'style="display: block;"' : 'style="display: none;"'?> >
                                    <fieldset>
                                        <legend>Rumble Configuration</legend>
                                        <div class="row">
                                          <div class="form-group-inner col-lg-4 col-md-4 col-sm-12 col-xs-12 <?php if(form_error('rumble_prize_title')): ?>error<?php endif; ?>">
                                            <label>Rumble Prize Title<span class="required">*</span></label>
                                            <input type="text" name="rumble_prize_title" id="rumble_prize_title" class="form-control required" value="<?php if(set_value('rumble_prize_title')): echo set_value('rumble_prize_title'); else: echo stripslashes($EDITDATA['rumble_prize_title']);endif; ?>" placeholder="Rumble Prize Title">
                                            <?php if(form_error('rumble_prize_title')): ?>
                                                <span for="rumble_prize_title" generated="true" class="help-inline"><?php echo form_error('rumble_prize_title'); ?></span>
                                            <?php endif; ?>
                                          </div>
                                          <?php if(!empty($EDITDATA['game_type'])):?>
                                            <?php  for($i=1; $i<=$EDITDATA['game_type']; $i++): ?>  
                                              <div class="form-group-inner col-lg-4 col-md-4 col-sm-12 col-xs-12 <?php if(form_error('title')): ?>error<?php endif; ?>">
                                                <label>prize <?=$i?><span class="required">*</span></label>
                                                <input type="number" name="rumble_prize_<?=$i?>" id="rumble_prize_<?=$i?>" class="form-control required" value="<?php if(set_value('rumble_prize_'.$i)): echo set_value('rumble_prize_'.$i); else: echo stripslashes($EDITDATA['rumble_prize_'.$i]);endif; ?>" placeholder="Prize Amount">
                                                <?php if(form_error('rumble_prize_<?=$i?>')): ?>
                                                    <span for="rumble_prize_<?=$i?>" generated="true" class="help-inline"><?php echo form_error('rumble_prize_<?=$i?>'); ?></span>
                                                <?php endif; ?>
                                              </div>
                                            <?php endfor;?>
                                          <?php endif;?>
                                        </div>
                                    </fieldset>
                                  </div>

                                  <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('chance_configuration')): ?>error<?php endif; ?> chance_configuration" <?=$EDITDATA['is_chance_enable'] == 'Y' || empty($EDITDATA['is_chance_enable'])  ? 'style="display: block;"' : 'style="display: none;"'?> >
                                    <fieldset>
                                        <legend>Chance Configuration</legend>
                                        <div class="row">
                                          <div class="form-group-inner col-lg-4 col-md-4 col-sm-12 col-xs-12 <?php if(form_error('chance_prize_title')): ?>error<?php endif; ?>">
                                            <label>Chance Prize Title<span class="required">*</span></label>
                                            <input type="text" name="chance_prize_title" id="chance_prize_title" class="form-control required" value="<?php if(set_value('chance_prize_title')): echo set_value('chance_prize_title'); else: echo stripslashes($EDITDATA['chance_prize_title']);endif; ?>" placeholder="Chance Prize Title">
                                            <?php if(form_error('chance_prize_title')): ?>
                                                <span for="chance_prize_title" generated="true" class="help-inline"><?php echo form_error('chance_prize_title'); ?></span>
                                            <?php endif; ?>
                                          </div>
                                          <?php if(!empty($EDITDATA['game_type'])):?>
                                            <?php  for($i=1; $i<=$EDITDATA['game_type']; $i++): ?>  
                                              <div class="form-group-inner col-lg-4 col-md-4 col-sm-12 col-xs-12 <?php if(form_error('title')): ?>error<?php endif; ?>">
                                                <label>prize <?=$i?><span class="required">*</span></label>
                                                <input type="number" name="chance_prize_<?=$i?>" id="chance_prize_<?=$i?>" class="form-control required" value="<?php if(set_value('chance_prize_'.$i)): echo set_value('chance_prize_'.$i); else: echo stripslashes($EDITDATA['chance_prize_'.$i]);endif; ?>" placeholder="Prize Amount">
                                                <?php if(form_error('chance_prize_<?=$i?>')): ?>
                                                    <span for="chance_prize_<?=$i?>" generated="true" class="help-inline"><?php echo form_error('chance_prize_<?=$i?>'); ?></span>
                                                <?php endif; ?>
                                              </div>
                                            <?php endfor;?>
                                          <?php endif;?>
                                        </div>
                                    </fieldset>
                                  </div>


                                <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('chance_configuration')): ?>error<?php endif; ?> chance_configuration">
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

                                <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('chance_configuration')): ?>error<?php endif; ?> chance_configuration">
                                  <fieldset>
                                        <legend>Mobile App Section</legend>
                                        <div class="row">
                                          <div class="form-group-inner col-lg-4 col-md-4 col-sm-12 col-xs-12 <?php if(form_error('text_field_1')): ?>error<?php endif; ?>">
                                            <label>Text Field 1<span class="required">*</span></label>
                                            <input type="text" name="text_field_1" id="text_field_1" class="form-control required" value="<?php if(set_value('text_field_1')): echo set_value('text_field_1'); else: echo stripslashes($EDITDATA['text_field_1']);endif; ?>" placeholder="Text Field 1">
                                            <?php if(form_error('text_field_1')): ?>
                                                <span for="text_field_1" generated="true" class="help-inline"><?php echo form_error('text_field_1'); ?></span>
                                            <?php endif; ?>
                                          </div>
                                          <div class="form-group-inner col-lg-4 col-md-4 col-sm-12 col-xs-12 <?php if(form_error('text_field_2')): ?>error<?php endif; ?>">
                                            <label>Text Field 2<span class="required">*</span></label>
                                            <input type="text" name="text_field_2" id="text_field_2" class="form-control required" value="<?php if(set_value('text_field_2')): echo set_value('text_field_2'); else: echo stripslashes($EDITDATA['text_field_2']);endif; ?>" placeholder="Text Field 2">
                                            <?php if(form_error('text_field_2')): ?>
                                                <span for="text_field_2" generated="true" class="help-inline"><?php echo form_error('text_field_2'); ?></span>
                                            <?php endif; ?>
                                          </div>
                                          <div class="form-group-inner col-lg-4 col-md-4 col-sm-12 col-xs-12 <?php if(form_error('text_field_3')): ?>error<?php endif; ?>">
                                            <label>Text Field 3<span class="required">*</span></label>
                                            <input type="text" name="text_field_3" id="text_field_3" class="form-control required" value="<?php if(set_value('text_field_3')): echo set_value('text_field_3'); else: echo stripslashes($EDITDATA['text_field_3']);endif; ?>" placeholder="Text Field 3">
                                            <?php if(form_error('text_field_3')): ?>
                                                <span for="text_field_3" generated="true" class="help-inline"><?php echo form_error('text_field_3'); ?></span>
                                            <?php endif; ?>
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
                                            <a href="<?php echo correctLink('ALLLOTOGAMEDATA',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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
      $('#is_straight_enable').change(function(){
          $('.straight_configuration').toggle($(this).val() == 'Y');
      });
      $('#is_rumble_enable').change(function(){
          $('.rumble_configuration').toggle($(this).val() == 'Y');
      });
      $('#is_chance_enable').change(function(){
          $('.chance_configuration').toggle($(this).val() == 'Y');
      });
  });

</script>


   