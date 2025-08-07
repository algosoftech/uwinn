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
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLLOTOPRODUCTSDATA',getCurrentControllerPath('index')); ?>"> Product</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Product</a></li>
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
                        <h5><?=$EDITDATA?'Edit':'Add'?> Product</h5>
                        <a href="<?php echo correctLink('ALLLOTOPRODUCTSDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>

                    <div class="card-body">
                        <div class="basic-login-inner">
                            <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="products_id"/>
                                <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['products_id']?>"/>
                                <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['products_id']?>"/>
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">

                                <div class="row">
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('category_id')): ?>error<?php endif; ?>">
                                        <label>Category<span class="required">*</span></label>
                                        <?php if(set_value('category_id')): $categoryiddata = explode('_____',set_value('category_id')); $category_id = $categoryiddata[0]; elseif($EDITDATA['category_id']): $category_id = stripslashes($EDITDATA['category_id']); else: $category_id = ''; endif; ?>
                                        <select name="category_id" id="category_id" class="form-control required">
                                          <?php echo $this->admin_model->getCategoryList($category_id); ?>
                                      </select>
                                      <?php if(form_error('category_id')): ?>
                                          <span for="category_id" generated="true" class="help-inline"><?php echo form_error('category_id'); ?></span>
                                      <?php endif; ?>
                                    </div>
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('sub_category_id')): ?>error<?php endif; ?>">
                                      <label>Sub Category<span class="required">*</span></label>
                                      <select id="sub_category_data" name="sub_category_id" class="form-control required">
                                         <option value="">Select sub_category</option>
                                      </select>
                                      <?php if(form_error('sub_category_id')): ?>
                                        <span for="sub_category_id" generated="true" class="help-inline"><?php echo form_error('sub_category_id'); ?></span>
                                      <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('title')): ?>error<?php endif; ?>">
                                        <label>Title<span class="required">*</span></label>
                                        <input type="text" name="title" id="title" class="form-control required" value="<?php if(set_value('title')): echo set_value('title'); else: echo stripslashes($EDITDATA['title']);endif; ?>" placeholder="Title">
                                        <?php if(form_error('title')): ?>
                                            <span for="title" generated="true" class="help-inline"><?php echo form_error('title'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('Description')): ?>error<?php endif; ?>">
                                        <label>Description</label>
                                        <textarea id="description" placeholder="Description" class="form-control" name="description"><?php if(set_value('description')): echo set_value('description'); else: echo stripslashes($EDITDATA['description']);endif; ?></textarea>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group-inner col-lg-3 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('product_image')): ?>error<?php endif; ?>">
                                            <label>Image</label><br>
                                            <input type="file" name="product_image" id="product_image" class="" value="<?php if(set_value('product_image')): echo set_value('product_image'); endif; ?>" accept="image/png, image/jpeg, image/webp" <?php if(empty($EDITDATA['product_image'])){ ?> required <?php } ?> >
                                            <p style="font-family:italic; color:red;">[Image Size : 241 x 136 px in jpg/jpeg/png]</p>
                                            <?php if($EDITDATA['product_image']): ?>
                                             <div id="ImageDiv2">
                                                <img src="<?php echo fileBaseUrl.$EDITDATA['product_image']; ?>" width="50" border="0" alt=""> 
                                             </div>
                                          <?php endif; ?>
                                          <?php if(form_error('product_image')): ?>
                                              <span for="product_image" generated="true" class="help-inline"><?php echo form_error('product_image'); ?></span>
                                          <?php endif; ?>
                                    </div>

                                   

                                    <div class="form-group-inner col-lg-3 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('product_image')): ?>error<?php endif; ?>">
                                            <label>App Image</label><br>
                                            <input type="file" name="app_image" id="app_image" class="" value="<?php if(set_value('app_image')): echo set_value('app_image'); endif; ?>" accept="image/png, image/jpeg, image/webp" <?php if(empty($EDITDATA['app_image'])){ ?> required <?php } ?> >
                                            <p style="font-family:italic; color:red;">[Image Size : 241 x 136 px in jpg/jpeg/png]</p>
                                            <?php if($EDITDATA['app_image']): ?>
                                            <div id="ImageDiv2">
                                                <img src="<?php echo fileBaseUrl.$EDITDATA['app_image']; ?>" width="50" border="0" alt=""> 
                                             </div>
                                          <?php endif; ?>
                                          <?php if(form_error('app_image')): ?>
                                              <span for="app_image" generated="true" class="help-inline"><?php echo form_error('app_image'); ?></span>
                                          <?php endif; ?>
                                    </div>


                                     

                                     <div class="form-group-inner col-lg-3 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('product_image_alt')): ?>error<?php endif; ?>">
                                        <label>Image Alt Text</label>
                                        <input type="text" name="product_image_alt" id="product_image_alt" class="form-control" value="<?php if(set_value('product_image_alt')): echo set_value('product_image_alt'); else: echo stripslashes($EDITDATA['product_image_alt']);endif; ?>" placeholder="Alt Text">
                                        <?php if(form_error('product_image_alt')): ?>
                                          <span for="product_image_alt" generated="true" class="help-inline"><?php echo form_error('product_image_alt'); ?></span>
                                      <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-3 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('product_image_alt')): ?>error<?php endif; ?>">
                                        <label>APP Alt Text</label>
                                        <input type="text" name="product_image_alt" id="product_image_alt" class="form-control" value="<?php if(set_value('product_image_alt')): echo set_value('product_image_alt'); else: echo stripslashes($EDITDATA['product_image_alt']);endif; ?>" placeholder="Alt Text">
                                        <?php if(form_error('product_image_alt')): ?>
                                          <span for="product_image_alt" generated="true" class="help-inline"><?php echo form_error('product_image_alt'); ?></span>
                                      <?php endif; ?>
                                    </div>

                                     
                                </div>

                                <div class="row">
                                    <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('straight_add_on_amount')): ?>error<?php endif; ?>">
                                        <label>Straight (ADE / iPoints)<span class="required">*</span></label>
                                        <input type="number" min="0" name="straight_add_on_amount" id="straight_add_on_amount" class="form-control required" value="<?php if(set_value('straight_add_on_amount')): echo set_value('straight_add_on_amount'); else: echo stripslashes($EDITDATA['straight_add_on_amount']);endif; ?>" placeholder="ADE / iPoints">
                                        <?php if(form_error('straight_add_on_amount')): ?>
                                            <span for="straight_add_on_amount" generated="true" class="help-inline"><?php echo form_error('straight_add_on_amount'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('rumble_add_on_amount')): ?>error<?php endif; ?>">
                                        <label>Rumble Mix ( ADE / iPoints )<span class="required">*</span></label>
                                        <input type="number" min="0" name="rumble_add_on_amount" id="rumble_add_on_amount" class="form-control required" value="<?php if(set_value('rumble_add_on_amount')): echo set_value('rumble_add_on_amount'); else: echo stripslashes($EDITDATA['rumble_add_on_amount']);endif; ?>" placeholder="ADE / iPoints">
                                        <?php if(form_error('rumble_add_on_amount')): ?>
                                            <span for="rumble_add_on_amount" generated="true" class="help-inline"><?php echo form_error('rumble_add_on_amount'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('reverse_add_on_amount')): ?>error<?php endif; ?>">
                                        <label>Chance ( ADE / iPoints )<span class="required">*</span></label>
                                        <input type="number" min="0" name="reverse_add_on_amount" id="reverse_add_on_amount" class="form-control required" value="<?php if(set_value('reverse_add_on_amount')): echo set_value('reverse_add_on_amount'); else: echo stripslashes($EDITDATA['reverse_add_on_amount']);endif; ?>" placeholder="ADE / iPoints">
                                        <?php if(form_error('reverse_add_on_amount')): ?>
                                            <span for="reverse_add_on_amount" generated="true" class="help-inline"><?php echo form_error('reverse_add_on_amount'); ?></span>
                                        <?php endif; ?>
                                    </div>


                                    <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('ticket_count_per_campaign')): ?>error<?php endif; ?>">
                                        <label> Ticket count Per Campaign <span class="required">*</span></label>
                                        <input type="number" min="0" name="ticket_count_per_campaign" id="ticket_count_per_campaign" class="form-control required" value="<?php if(set_value('ticket_count_per_campaign')): echo set_value('ticket_count_per_campaign'); else: echo stripslashes($EDITDATA['ticket_count_per_campaign']);endif; ?>" placeholder="Show Tickect For Campaign">
                                        <?php if(form_error('ticket_count_per_campaign')): ?>
                                            <span for="ticket_count_per_campaign" generated="true" class="help-inline"><?php echo form_error('ticket_count_per_campaign'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('enable_ticket_for_campaign')): ?>error<?php endif; ?>">
                                        <label>Enable ticket for Campaign </label>
                                        <select name="enable_ticket_for_campaign" id="enable_ticket_for_campaign" class="form-control required">
                                            <option hidden>Select</option>
                                            <option value="Y" <?=$EDITDATA['enable_ticket_for_campaign'] == 'Y' ? 'SELECTED':'';?> > Yes </option>
                                            <option value="N" <?=$EDITDATA['enable_ticket_for_campaign'] == 'N' ? 'SELECTED':'';?> > No  </option>
                                        </select>
                                        <?php if(form_error('enable_ticket_for_campaign')): ?>
                                            <span for="enable_ticket_for_campaign" generated="true" class="help-inline"><?php echo form_error('enable_ticket_for_campaign'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('commingSoon')): ?>error<?php endif; ?>">
                                        <label>Coming Soon</label>
                                        <select name="commingSoon" id="commingSoon" class="form-control required">
                                            <option hidden>Select Coming Soon</option>
                                            <?php if(stripslashes($EDITDATA['commingSoon']) == 'Y'){ ?>
                                                <option value="Y" hidden selected>Yes</option>
                                            <?php }else{?>
                                                <option value="N" hidden selected>No</option>
                                            <?php }?>
                                            <option value="Y" >Yes</option>
                                            <option value="N">No</option>
                                        </select>
                                        <?php if(form_error('commingSoon')): ?>
                                            <span for="commingSoon" generated="true" class="help-inline"><?php echo form_error('commingSoon'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                 
                                    <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('countdown_status')): ?>error<?php endif; ?>">
                                        <label>countdown Show/Hide</label>
                                        <select name="countdown_status" id="countdown_status" class="form-control required">

                                            <?php if(stripslashes($EDITDATA['countdown_status']) == 'Y'){ ?>
                                                <option value="Y" hidden selected>Yes</option>
                                            <?php }else{?>
                                                <option value="N" hidden selected>No</option>
                                            <?php }?>
                                            <option value="Y" >Yes</option>
                                            <option value="N">No</option>
                                        </select>
                                        <?php if(form_error('countdown_status')): ?>
                                            <span for="countdown_status" generated="true" class="help-inline"><?php echo form_error('countdown_status'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-2 col-md-2 col-sm-2 col-xs-12 <?php if(form_error('validuptodate')): ?>error<?php endif; ?>">
                                        <label>Valid Upto Date<span class="required">*</span></label>
                                        <input type="date" name="validuptodate" id="validuptodate" class="form-control required" value="<?php if(set_value('validuptodate')): echo set_value('validuptodate'); else: echo stripslashes($EDITDATA['validuptodate']);endif; ?>">
                                        <?php if(form_error('validuptodate')): ?>
                                            <span for="validuptodate" generated="true" class="help-inline"><?php echo form_error('validuptodate'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-2 col-md-2 col-sm-2 col-xs-12 <?php if(form_error('validuptotime')): ?>error<?php endif; ?>">
                                        <label>Valid Upto Time<span class="required">*</span></label>
                                        <input type="time" name="validuptotime" id="validuptotime" class="form-control required" value="<?php if(set_value('validuptotime')): echo set_value('validuptotime'); else: echo stripslashes($EDITDATA['validuptotime']);endif; ?>">
                                        <?php if(form_error('validuptotime')): ?>
                                            <span for="validuptotime" generated="true" class="help-inline"><?php echo form_error('validuptotime'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-2 col-md-2 col-sm-2 col-xs-12 <?php if(form_error('lotto_type')): ?>error<?php endif; ?>">
                                        <label>Lotto Type<span class="required">*</span></label>
                                        <input type="text" name="lotto_type" id="lotto_type" class="form-control required" value="<?php if(set_value('lotto_type')): echo set_value('lotto_type'); else: echo stripslashes($EDITDATA['lotto_type']);endif; ?>">
                                        <?php if(form_error('lotto_type')): ?>
                                            <span for="lotto_type" generated="true" class="help-inline"><?php echo form_error('lotto_type'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                
                                    <div class="form-group-inner col-lg-2 col-md-2 col-sm-2 col-xs-12 <?php if(form_error('draw_date')): ?>error<?php endif; ?>">
                                        <label>Campaigns closing<span class="required">*</span></label>
                                        <select name="is_show_closing" id="is_show_closing" class="form-control required">
                                            <?php if(stripslashes($EDITDATA['is_show_closing']) == 'Hide'){ ?>
                                                <option value="Hide" hidden selected>Hide</option>
                                            <?php }else{?>
                                                <option value="Show" hidden selected>Show</option>
                                            <?php }?>
                                            <option value="Show" >Show</option>
                                            <option value="Hide">Hide</option>
                                        </select>

                                        <?php if(form_error('is_show_closing')): ?>
                                            <span for="is_show_closing" generated="true" class="help-inline"><?php echo form_error('is_show_closing'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('draw_date')): ?>error<?php endif; ?>">
                                        <label>Campaigns Draw Date<span class="required">*</span></label>
                                        <input type="date" name="draw_date" id="draw_date" class="form-control required" value="<?php if(set_value('draw_date')): echo set_value('draw_date'); else: echo stripslashes($EDITDATA['draw_date']);endif; ?>">
                                        <?php if(form_error('draw_date')): ?>
                                            <span for="draw_date" generated="true" class="help-inline"><?php echo form_error('draw_date'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('draw_time')): ?>error<?php endif; ?>">
                                        <label>Campaigns Draw Time<span class="required">*</span></label>
                                        <input type="time" name="draw_time" id="draw_time" class="form-control required" value="<?php if(set_value('draw_time')): echo set_value('draw_time'); else: echo stripslashes($EDITDATA['draw_time']);endif; ?>">
                                        <?php if(form_error('draw_time')): ?>
                                            <span for="draw_time" generated="true" class="help-inline"><?php echo form_error('draw_time'); ?></span>
                                        <?php endif; ?>
                                    </div> 

                                    <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('enable_number_prefix')): ?>error<?php endif; ?>">
                                        <label>Enable Number Range Prefix<span class="required">*</span></label>
                                        <select name="enable_number_prefix" id="enable_number_prefix" class="form-control required">
                                            <?php if(stripslashes($EDITDATA['enable_number_prefix']) == 'Y'): ?>
                                            <option value="Y" hidden selected>Yes</option>
                                            <?php else: ?>
                                            <option value="N" hidden selected>No</option>
                                            <?php endif; ?>
                                            <option value="Y" >Yes</option>
                                            <option value="N">No</option>
                                        </select>
                                        <?php if(form_error('enable_number_prefix')): ?>
                                         <span for="enable_number_prefix" generated="true" class="help-inline">
                                            <?php echo form_error('enable_number_prefix'); ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner lotto_range_prefix_section col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if($EDITDATA['enable_number_prefix'] == "N" || $EDITDATA['enable_number_prefix'] == "" ):  echo 'd-none'; endif; ?>   <?php if(form_error('lotto_range_prefix')): ?>error<?php endif; ?>">
                                        <label>Number Range Prefix <span class="required">*</span></label>
                                        <input type="number" min="0"  max="9" name="lotto_range_prefix" id="lotto_range_prefix" class="form-control required" value="<?php if(set_value('lotto_range_prefix')): echo set_value('lotto_range_prefix'); elseif($EDITDATA['lotto_range_prefix']): echo stripslashes($EDITDATA['lotto_range_prefix']); endif; ?>" >
                                        <div class="range-section"></div>
                                        <?php if(form_error('lotto_range_prefix')): ?>
                                            <span for="lotto_range_prefix" generated="true" class="help-inline"><?php echo form_error('lotto_range_prefix'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('ticket_number_repeat')): ?>error<?php endif; ?>">
                                        <label>Ticket Number Repeating<span class="required">*</span></label>
                                        <select name="ticket_number_repeat" id="ticket_number_repeat" class="form-control required">
                                            <?php if(stripslashes($EDITDATA['ticket_number_repeat']) == 'Y'): ?>
                                            <option value="Y" hidden selected>Yes</option>
                                            <?php else: ?>
                                            <option value="N" hidden selected>No</option>
                                            <?php endif; ?>
                                            <option value="Y" >Yes</option>
                                            <option value="N">No</option>
                                        </select>
                                        <?php if(form_error('ticket_number_repeat')): ?>
                                         <span for="ticket_number_repeat" generated="true" class="help-inline">
                                            <?php echo form_error('ticket_number_repeat'); ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                     
                                    <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('lotto_range_start')): ?>error<?php endif; ?>">
                                        <label>Number Range Start<span class="required">*</span></label>
                                        <input type="number" min="0"  max="100" name="lotto_range_start" id="lotto_range_start" class="form-control required" value="<?php if(set_value('lotto_range_start')): echo set_value('lotto_range_start'); elseif($EDITDATA['lotto_range_start'] == 0): echo '0';  else: echo $EDITDATA['lotto_range_start']; endif; ?>" >
                                        <div class="range-section"></div>
                                        <?php if(form_error('lotto_range_start')): ?>
                                            <span for="lotto_range_start" generated="true" class="help-inline"><?php echo form_error('lotto_range_start'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('lotto_range_end')): ?>error<?php endif; ?>">
                                        <label>Number Range End<span class="required">*</span></label>
                                        <input type="number" min="1"  max="100" name="lotto_range_end" id="lotto_range_end" class="form-control required" value="<?php if(set_value('lotto_range_end')): echo set_value('lotto_range_end'); elseif($EDITDATA['lotto_range_end']): echo stripslashes($EDITDATA['lotto_range_end']); endif; ?>" >
                                        <div class="range-section"></div>
                                        <?php if(form_error('lotto_range_end')): ?>
                                            <span for="lotto_range_end" generated="true" class="help-inline"><?php echo form_error('lotto_range_end'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-2 col-md-2 col-sm-2 col-xs-12 <?php if(form_error('seq_order')): ?>error<?php endif; ?>">
                                        <label>Sequence Order</label>
                                        <input type="number" min="1" name="seq_order" id="seq_order" class="form-control" value="<?php if(set_value('seq_order')): echo set_value('seq_order'); elseif($EDITDATA['seq_order']): echo stripslashes($EDITDATA['seq_order']); endif; ?>" placeholder="Sequence Order">
                                        <?php if(form_error('seq_order')): ?>
                                            <span for="seq_order" generated="true" class="help-inline"><?php echo form_error('seq_order'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-2 col-xs-12 <?php if(form_error('show_as_banner')): ?>error<?php endif; ?>">
                                        <input type="checkbox"   name="show_as_banner" id="show_as_banner" class="form-check-input"  <?php if($EDITDATA['show_as_banner'] == 'on'): echo 'Checked'; endif;?> placeholder="Show As Banner">
                                        <label class="form-check-label" for="show_as_banner"> Show As Banner </label>
                                    </div>
                                </div>

                                 <fieldset>
                                    <legend>Super Ball Section</legend>

                                    <div class="row">
                                    
                                        <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('enable_super_ball')): ?>error<?php endif; ?>">
                                            <label>Enable Super Ball <span class="required">*</span></label>
                                            <select name="enable_super_ball" id="enable_super_ball" class="enable_super_ball form-control required">
                                                <?php if(stripslashes($EDITDATA['enable_super_ball']) == 'Y'): ?>
                                                <option value="Y" hidden selected>Yes</option>
                                                <?php else: ?>
                                                <option value="N" hidden selected>No</option>
                                                <?php endif; ?>
                                                <option value="Y" >Yes</option>
                                                <option value="N">No</option>
                                            </select>
                                            <?php if(form_error('enable_super_ball')): ?>
                                             <span for="enable_super_ball" generated="true" class="help-inline">
                                                <?php echo form_error('enable_super_ball'); ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>  

                                        <div class="super_ball_type d-none form-group-inner col-lg-2 col-md-2 col-sm-2 col-xs-12 <?php if(form_error('super_ball_type')): ?>error<?php endif; ?>">
                                            <label>Super Ball Type<span class="required">*</span></label>
                                            <input type="number"  min="0"  max="100" name="super_ball_type" id="super_ball_type" class="super_ball_type form-control required" value="<?php if(set_value('super_ball_type')): echo set_value('super_ball_type'); else: echo stripslashes($EDITDATA['super_ball_type']);endif; ?>">
                                            <?php if(form_error('super_ball_type')): ?>
                                                <span for="super_ball_type" generated="true" class="help-inline"><?php echo form_error('super_ball_type'); ?></span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="superbal_range_start d-none form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('superbal_range_start')): ?>error<?php endif; ?>">
                                            <label>Super ball Number Range Start<span class="required">*</span></label>
                                            <input type="number" min="0"  max="100" name="superbal_range_start" id="superbal_range_start" class="superbal_range_start form-control required" value="<?php if(set_value('superbal_range_start')): echo set_value('superbal_range_start'); elseif($EDITDATA['superbal_range_start'] == 0): echo '0';  else: echo $EDITDATA['superbal_range_start']; endif; ?>" >
                                            <div class="range-section"></div>
                                            <?php if(form_error('superbal_range_start')): ?>
                                                <span for="superbal_range_start" generated="true" class="help-inline"><?php echo form_error('superbal_range_start'); ?></span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="superbal_range_end d-none form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('superbal_range_end')): ?>error<?php endif; ?>">
                                            <label>Super ball Number Range end<span class="required">*</span></label>
                                            <input type="number" min="0"  max="100" name="superbal_range_end" id="superbal_range_end" class="superbal_range_end form-control required" value="<?php if(set_value('superbal_range_end')): echo set_value('superbal_range_end'); elseif($EDITDATA['superbal_range_end'] == 0): echo '0';  else: echo $EDITDATA['superbal_range_end']; endif; ?>" >
                                            <div class="range-section"></div>
                                            <?php if(form_error('superbal_range_end')): ?>
                                                <span for="superbal_range_end" generated="true" class="help-inline"><?php echo form_error('superbal_range_end'); ?></span>
                                            <?php endif; ?>
                                        </div>

                                    </div>
                                </fieldset>

                                <div class="row">
                                    <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('show_on')): ?>error<?php endif; ?>">
                                        <label>Show ON <sub class="text-danger"> ( Website, App , POS ) </sub></label>
                                        <select name="show_on[]" id="show_on" class="form-control required" multiple required>
                                            
                                            <?php 
                                             $show_on = $EDITDATA['show_on'];

                                            if(is_array($show_on)) :
                                                $show_on = $EDITDATA['show_on'];
                                            else:
                                                 $show_on = array();
                                            endif;

                                            ?>
                                            <option value=""> Select </option>
                                            <option value="Website" <?= in_array('Website',$show_on )  ? 'selected':''; ?> > Website </option>
                                            <option value="App"     <?= in_array('App', $show_on)      ? 'selected':''; ?> > App </option>
                                            <option value="POS"     <?= in_array('POS', $show_on)      ? 'selected':''; ?> > POS </option>
                                            <option value="coming_soon" <?= in_array('coming_soon', $show_on)      ? 'selected':''; ?> > Coming Soon </option>
                                        </select>
                                        <?php if(form_error('show_on')): ?>
                                            <span for="show_on" generated="true" class="help-inline"><?php echo form_error('show_on'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                               <fieldset>
                                   <legend>Text Fields</legend>

                                    <div class="row">
                                        <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('text_field_1')): ?>error<?php endif; ?>">
                                            <label>Text 1<span class="required">*</span></label>
                                            <input type="text" name="text_field_1" id="text_field_1" class="form-control required" value="<?php if(set_value('text_field_1')): echo set_value('text_field_1'); else: echo stripslashes($EDITDATA['text_field_1']);endif; ?>">
                                            <?php if(form_error('text_field_1')): ?>
                                                <span for="text_field_1" generated="true" class="help-inline"><?php echo form_error('text_field_1'); ?></span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('text_field_2')): ?>error<?php endif; ?>">
                                            <label>Text 2<span class="required">*</span></label>
                                            <input type="text" name="text_field_2" id="text_field_2" class="form-control required" value="<?php if(set_value('text_field_2')): echo set_value('text_field_2'); else: echo stripslashes($EDITDATA['text_field_2']);endif; ?>">
                                            <?php if(form_error('text_field_2')): ?>
                                                <span for="text_field_2" generated="true" class="help-inline"><?php echo form_error('text_field_2'); ?></span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('text_field_3')): ?>error<?php endif; ?>">
                                            <label>Text 3<span class="required">*</span></label>
                                            <input type="text" name="text_field_3" id="text_field_3" class="form-control required" value="<?php if(set_value('text_field_3')): echo set_value('text_field_3'); else: echo stripslashes($EDITDATA['text_field_3']);endif; ?>">
                                            <?php if(form_error('text_field_3')): ?>
                                                <span for="text_field_3" generated="true" class="help-inline"><?php echo form_error('text_field_3'); ?></span>
                                            <?php endif; ?>
                                        </div>

                                    </div>


                               </fieldset>

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
           </div>
       </div>
    <!-- [ Main Content ] end -->
    </div>
</div>
<script type="text/javascript">


    $('#enable_number_prefix').on('change',function(){

        let EnableNumberPrefix = $(this).val();
        if(EnableNumberPrefix == 'Y'){
            $('.lotto_range_prefix_section').removeClass('d-none');
        }else{
            $('.lotto_range_prefix_section').addClass('d-none');
        }

    });

    $('#category_id').on('change',function(){
        var category_id =  $(this).val();   
        $.ajax({
            url:FULLSITEURL+'products/alluwinnproducts/getsubcategoryData',
            type:'post',
            data:{category_id:category_id},
            success:function(data){
                $('#sub_category_data').html(data);
            }
        });
    });

    $(document).ready(function() {
        // Function to toggle visibility, 'required' attribute, and reset values
        function toggleSuperBallFields() {
            const enableSuperBall = $('#enable_super_ball').val();
            if (enableSuperBall === 'Y') {
                // Show fields and add 'required' attribute
                $('.super_ball_type, .superbal_range_start, .superbal_range_end').removeClass('d-none').attr('required', true);
            } else {
                // Hide fields, remove 'required' attribute, and reset values
                $('.super_ball_type, .superbal_range_start, .superbal_range_end').addClass('d-none').removeAttr('required').val(''); 

            }
        }

        // Initial check on page load
        toggleSuperBallFields();
        // Event listener for changes in the dropdown
        $('#enable_super_ball').change(function() {
            toggleSuperBallFields();
        });
    });



    $(document).ready(function(){
        var category_id =  $('#category_id').val();
        var sub_category_id  =  '<?php echo $EDITDATA['sub_category_id']?>';
        $.ajax({
            url:FULLSITEURL+'products/alluwinnproducts/getsubcategoryData',
            type:'post',
            data:{category_id:category_id,sub_category_id:sub_category_id},
            success:function(data){
                $('#sub_category_data').html(data);
            }
        });
    });
</script>
<script type="text/javascript">
  $(function(){create_editor_for_textarea('description')});
  $(function(){create_editor_for_textarea('image')});
</script>
<script>
    $(document).ready(function(){
        $('#is_color').click(function(){
            if($('#is_color').prop('checked')==true){
                $('#Color_N_Size').css('display','block');
            } else {
                $('#Color_N_Size').css('display','none');
            }
        });
    });
</script>

<script>
    $(function(){ 
      var scntDiv   =   $('#currentPageForm #Datalocation');
      var pi      =   $('#currentPageForm #Datalocation > span').length; 

      $(document).on('click', '#currentPageForm .addMoreData', function() { 

        var i     =   parseInt($('#currentPageForm #TotalDataCount').val());
        i++;
        pi++;
        $('<span><div class="row"><div class="form-group-inner col-lg-10 col-md-10 col-sm-10 col-xs-10"><hr><label><b>Select your color</b> : </label><input type="color" class="form-group" id="color'+i+'" name="color'+i+'" value="#ff0000" style="margin-left: 2%;"><label style="margin-left: 2.5%;"><b>Size </b>: </label><input type="checkbox" id="is_color" name="S'+i+'" value="S" style="margin-left: 2%;margin-top:-1.5%"><b>&nbspS</b><input type="checkbox" id="is_color" name="M'+i+'" value="M" style="margin-left: 2%;margin-top:-1.5%"><b>&nbspM</b><input type="checkbox" id="is_color" name="L'+i+'" value="L" style="margin-left: 2%;margin-top:-1.5%"><b>&nbspL</b><input type="checkbox" id="is_color" name="XL'+i+'" value="XL" style="margin-left: 2%;margin-top:-1.5%"><b>&nbspXL</b><input type="checkbox" id="is_color" name="XXL'+i+'" value="XXL" style="margin-left: 2%;margin-top:-1.5%"><b>&nbspXXL</b><input type="checkbox" id="is_color" name="FRS'+i+'" value="FRS" style="margin-left: 2%;margin-top:-1.5%"><b>&nbspFree Size</b><label style="margin-left: 10%;"><b>Image</b> : &nbsp&nbsp</label><input type="file" class="form-group required" id="color_img'+i+'" name="color_img'+i+'" ></div> <div class="form-group-inner col-lg-2 col-md-2 col-sm-2 col-xs-2" style="text-align:right;"><hr /><label>&nbsp;</label><label>&nbsp;</label><a href="javascript:void(0);" class="removeMoreData" id="RemoveData_'+i+'" style="float:right;display:none;"><img src="<?php echo base_url(); ?>assets/admin/image/cross.png" alt="Remove" /></a><a href="javascript:void(0);" class="addMoreData" id="AddData_'+i+'" style="float:right;display:block;margin-right: 10px;"><img src="<?php echo base_url(); ?>assets/admin/image/addmore.png" alt="Add more" /></a></div></div></span>').appendTo(scntDiv);
        $('#currentPageForm #TotalData').val(pi);
        $('#currentPageForm #TotalDataCount').val(i);

        $(this).closest('#Datalocation').find('a.removeMoreData').show();
        $(this).closest('#Datalocation').find('a.addMoreData').hide();
        $('#currentPageForm #RemoveData_'+i).hide();
        $('#currentPageForm #AddData_'+i).show();

        return false;
    });

      $(document).on('click', '#currentPageForm .removeMoreData', function() {  
        if( pi > 1 ) {
          $(this).parents('span').remove();
          pi--;
          $('#currentPageForm #TotalData').val(pi);
      }
      return false;
  });
  });
</script>

<script>

  function ImageDelete(imageName,id,typ)
  {
    // alert(imageName);
    // alert(id);

    if(confirm("Sure to delete?"))
    {//alert(CURRENTCLASS);
      $.ajax({
            type: 'post',
            url: FULLSITEURL+'products/'+CURRENTCLASS+'/imageDelete',
            data: {imageName:imageName,id:id,typ:typ},
            success: function(rdata) { 
              if(parseInt(rdata.status) == 1) {
                $('#image').val('');
                $('#ImageDiv2').html('');
              }else{
                $('#image').val('');
                $('#ImageDiv2').html('');

              }
              return false;
            }
      });
    }

   
  }
</script>
