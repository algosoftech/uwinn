<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>
<script>
    $(function(){
     // Use datetimepicker (not jQuery UI datepicker) so seconds are preserved/handled.
     $("#start_date").datetimepicker({
        format: 'Y-m-d H:i:s',
        formatTime: 'H:i:s',
        timepicker: true,
        yearStart: 1970,
        yearEnd: <?= (int)date('Y'); ?>,
        step: 1,
        mask: false,
        validateOnBlur: false
     });
     $("#expiry_date").datetimepicker({
        format: 'Y-m-d H:i:s',
        formatTime: 'H:i:s',
        timepicker: true,
        yearStart: 1970,
        yearEnd: <?= (int)date('Y'); ?>,
        step: 1,
        mask: false,
        validateOnBlur: false
     });
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
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Lotto Games</a></li>
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
                        <h5><?=$EDITDATA?'Edit':'Add'?> Lotto Games</h5>
                        <a href="<?php echo correctLink('ALLLOTOGAMEDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>

                    <div class="card-body">
                        <div class="basic-login-inner">
                            <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                                <div class="row">
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('category_oid')): ?>error<?php endif; ?>">
                                        <label>Category<span class="required">*</span></label>
                                        <select name="category_oid" id="category_oid" class="form-control required">
                                            <?php echo $this->admin_model->getCampaignCategory($EDITDATA['category_oid']); ?>
                                        </select>
                                        <?php if(form_error('category_oid')): ?>
                                            <span for="category_oid" generated="true" class="help-inline"><?php echo form_error('category_oid'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('sub_category_oid')): ?>error<?php endif; ?>">
                                      <label>Sub Category<span class="required">*</span></label>
                                      <select id="sub_category_data" name="sub_category_oid" class="form-control required">
                                        <?php echo $this->admin_model->getCampaignSubCategory($EDITDATA['category_oid'],$EDITDATA['sub_category_oid']); ?>
                                      </select>
                                      <?php if(form_error('sub_category_oid')): ?>
                                        <span for="sub_category_oid" generated="true" class="help-inline"><?php echo form_error('sub_category_oid'); ?></span>
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
                                    <div class="form-group-inner col-lg-2 col-md-2 col-sm-2 col-xs-12 <?php if(form_error('game_image')): ?>error<?php endif; ?>">
                                        <label>Game Image</label><br>
                                        <input type="file" name="game_image" id="game_image" class="" value="<?php if(set_value('game_image')): echo set_value('game_image'); endif; ?>" accept="image/png, image/jpeg, image/webp" <?php if(empty($EDITDATA['game_image'])){ ?> required <?php } ?> >
                                        <p style="font-family:italic; color:red;">[Image Size : 241 x 136 px in jpg/jpeg/png]</p>
                                        <?php if($EDITDATA['game_image']): ?>
                                            <div id="ImageDiv2">
                                            <img src="<?php echo fileBaseUrl.$EDITDATA['game_image']; ?>" width="50" border="0" alt=""> 
                                            </div>
                                        <?php endif; ?>
                                        <?php if(form_error('game_image')): ?>
                                            <span for="game_image" generated="true" class="help-inline"><?php echo form_error('game_image'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-2 col-md-2 col-sm-2 col-xs-12 <?php if(form_error('price')): ?>error<?php endif; ?>">
                                        <label>Price ( ADE / iPoints )<span class="required">*</span></label>
                                        <input type="number" min="0" name="price" id="price" class="form-control required" value="<?php if(set_value('price')): echo set_value('price'); else: echo stripslashes($EDITDATA['price']);endif; ?>" placeholder="ADE / iPoints">
                                        <?php if(form_error('price')): ?>
                                            <span for="price" generated="true" class="help-inline"><?php echo form_error('price'); ?></span>
                                        <?php endif; ?>
                                    </div>

 

                                    <div class="form-group-inner col-lg-2 col-md-2 col-sm-2 col-xs-12 <?php if(form_error('seq_order')): ?>error<?php endif; ?>">
                                        <label>Sequence Order</label>
                                        <input type="number" min="1" name="seq_order" id="seq_order" class="form-control" value="<?php if(set_value('seq_order')): echo set_value('seq_order'); elseif($EDITDATA['seq_order']): echo stripslashes($EDITDATA['seq_order']); endif; ?>" placeholder="Sequence Order">
                                        <?php if(form_error('seq_order')): ?>
                                            <span for="seq_order" generated="true" class="help-inline"><?php echo form_error('seq_order'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                        
                                    <div class="form-group-inner col-lg-2 col-md-2 col-sm-2 col-xs-12 <?php if(form_error('start_date')): ?>error<?php endif; ?>">
                                        <?php $start_date = date('Y-m-d H:i:s',$EDITDATA['start_date'] ?? strtotime(date('Y-m-d H:i:01'))); ?>
                                        <label>Start Date<span class="required">*</span></label>
                                        <input type="text" name="start_date" id="start_date" class="form-control required" value="<?php if(set_value('start_date')): echo set_value('start_date'); else:  echo $start_date;endif; ?>">
                                        <?php if(form_error('start_date')): ?>
                                            <span for="start_date" generated="true" class="help-inline"><?php echo form_error('start_date'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-2 col-md-2 col-sm-2 col-xs-12 <?php if(form_error('expiry_date')): ?>error<?php endif; ?>">
                                        <?php $expiry_date = date('Y-m-d H:i:s',$EDITDATA['expiry_date'] ?? strtotime(date('Y-m-d H:i:59',strtotime('+1 hour')))); ?>
                                        <label>Expiry Date<span class="required">*</span></label>
                                        <input type="text" name="expiry_date" id="expiry_date" class="form-control required" value="<?php if(set_value('expiry_date')): echo set_value('expiry_date'); else:  echo $expiry_date;endif; ?>">
                                        <?php if(form_error('expiry_date')): ?>
                                            <span for="expiry_date" generated="true" class="help-inline"><?php echo form_error('expiry_date'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                                    
                                            
                                
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
                                <div class="row">
                                    <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="inline-remember-me mt-4">
                                            <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                            <button class="btn btn-primary mb-4">Submit</button>
                                            <a href="<?php echo correctLink('ALLLOTOTambola GamesSDATA',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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
            $('.number_range_prefix_section').removeClass('d-none');
        }else{
            $('.number_range_prefix_section').addClass('d-none');
        }

    });

    $('#category_oid').on('change',function(){
        var category_oid =  $(this).val();   
        $.ajax({
            url:  '<?=getCurrentControllerPath('getsubcategoryData');?>',
            type:'post',
            data:{category_oid:category_oid},
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
       $('#category_oid').on('change',function(){
        var category_oid =  $(this).val();
        $.ajax({
            url:  '<?=getCurrentControllerPath('getsubcategoryData');?>',
            type:'post',
            data:{category_oid:category_oid},
            success:function(data){
                $('#sub_category_data').html(data);
            }
        });
       });
    });
</script>
<script type="text/javascript">
  $(function(){create_editor_for_textarea('description')});
  $(function(){create_editor_for_textarea('image')});
</script>
 
 
 
