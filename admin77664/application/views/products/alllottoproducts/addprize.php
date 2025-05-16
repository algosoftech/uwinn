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
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLLOTTOPRIZEDATA',getCurrentControllerPath('index')); ?>">Prize</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Prize</a></li>
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
                        <h5><?=$EDITDATA?'Edit':'Add'?> Prize</h5>
                        <a href="<?php echo correctLink('ALLLOTTOPRIZEDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>
                    <div class="card-body">
                        <div class="basic-login-inner">
                            <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="prize_id"/>
                                <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['prize_id']?>"/>
                                <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['prize_id']?>"/>
                                <input type="hidden" name="lotto_type" id="CurrentDataID" value="<?=$lotto_type;?>"/>
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                                <input type="hidden" name="productID" value="<?=$this->session->userdata('productID4Prize')?>">
                                
                                <?php echo validation_errors(); ?>

                                <div class="row">
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-6 <?php if(form_error('enable_title')): ?>error<?php endif; ?>">
                                        <label>Enable/Disable Heading<span class="required">*</span></label>
                                        <select name="enable_title" id="enable_title" class="form-control required">
                                            <?php if(stripslashes($EDITDATA['enable_title']) == 'Y'): ?>
                                                <option value="Y" hidden selected>Yes</option>
                                            <?php else: ?>
                                                <option value="N" hidden selected>No</option>
                                            <?php endif; ?>
                                            <option value="Y" >Yes</option>
                                            <option value="N">No</option>
                                        </select>
                                        <?php if(form_error('enable_title')): ?>
                                           <span for="enable_title" generated="true" class="help-inline">
                                            <?php echo form_error('enable_title'); ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-6 <?php if(form_error('title')): ?>error<?php endif; ?>">
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
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('prize_image')): ?>error<?php endif; ?>">
                                        <label>Image</label><br>
                                        <input type="file" name="prize_image" id="prize_image" class="" value="<?php if(set_value('prize_image')): echo set_value('prize_image'); endif; ?>" accept="image/png, image/jpeg, image/webp" <?php if(empty($EDITDATA['prize_image'])){?>
                                            required
                                            <?php } ?> >
                                            <p style="font-family:italic; color:red;">[Image Size : 834 x 513 px in jpg/jpeg/png]</p>
                                            <?php if($EDITDATA['prize_image']): ?>
                                             <div id="ImageDiv2"><img src="<?php echo fileBaseUrl.$EDITDATA['prize_image']; ?>" width="50" border="0" alt="">&nbsp;
                                                 <a href="javascript:void(0);" onclick="ImageDelete('<?php echo $EDITDATA['prize_image']; ?>','<?php echo $EDITDATA['prize_id']; ?>','app');"> 
                                                  <img src="{ASSET_INCLUDE_URL}image/cross.png" border="0" alt="">
                                              </a></div>
                                          <?php endif; ?>
                                          <?php if(form_error('prize_image')): ?>
                                              <span for="prize_image" generated="true" class="help-inline"><?php echo form_error('prize_image'); ?></span>
                                          <?php endif; ?>
                                      </div>
                                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('prize_image_alt')): ?>error<?php endif; ?>">
                                            <label>Alt</label>
                                            <input type="text" name="prize_image_alt" id="prize_image_alt" class="form-control" value="<?php if(set_value('prize_image_alt')): echo set_value('prize_image_alt'); else: echo stripslashes($EDITDATA['prize_image_alt']);endif; ?>">
                                            <?php if(form_error('prize_image_alt')): ?>
                                              <span for="prize_image_alt" generated="true" class="help-inline"><?php echo form_error('prize_image_alt'); ?></span>
                                          <?php endif; ?>
                                      </div>
                                </div>  
                                <div class="row prize-section">
                                        <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <label style="font-family:italic; color:red;">[ If cash prize not given enter 0 in cash prize. ]</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                             <!-- Prize 1 winner inputs -->
                                            <fieldset>
                                                <legend>Straight Module</legend>
                                                
                                                <div class="row">
                                                    <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('enable_stright_prize_heading')): ?>error<?php endif; ?>">
                                                        <label>Enable/Disable Heading<span class="required">*</span></label>
                                                        <select name="enable_stright_prize_heading" id="enable_stright_prize_heading" class="form-control required">
                                                            <?php if(stripslashes($EDITDATA['enable_stright_prize_heading']) == 'Y'): ?>
                                                            <option value="Y" hidden selected>Yes</option>
                                                            <?php else: ?>
                                                            <option value="N" hidden selected>No</option>
                                                            <?php endif; ?>
                                                            <option value="Y" >Yes</option>
                                                            <option value="N">No</option>
                                                        </select>
                                                        <?php if(form_error('enable_stright_prize_heading')): ?>
                                                         <span for="enable_stright_prize_heading" generated="true" class="help-inline">
                                                            <?php echo form_error('enable_stright_prize_heading'); ?>
                                                        </span>
                                                        <?php endif; ?>
                                                    </div>
                                                     <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('stright_prize_heading')): ?>error<?php endif; ?>">
                                                        <label>Straight Heading<span class="required"></span></label>
                                                        <input type="text" name="stright_prize_heading" id="stright_prize_heading" class="form-control" value="<?php if(set_value('stright_prize_heading')): echo set_value('stright_prize_heading'); else: echo stripslashes($EDITDATA['stright_prize_heading']);endif; ?>" placeholder="Straight Heading" required>
                                                        <?php if(form_error('stright_prize_heading')): ?>
                                                            <span for="stright_prize_heading" generated="true" class="help-inline"><?php echo form_error('stright_prize_heading'); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                      <label> Select Shared Prize List</label>
                                                      <select name="stright_prize_type[]" class="form-control stright_prize_type " multiple>
                                                        <?php for ($i=1; $i <=$lotto_type ; $i++): ?>    
                                                          <option value="<?='Prize'.$i;?>"  <?php if($EDITDATA['stright_prize_type']):  echo (in_array('Prize'.$i,$EDITDATA['stright_prize_type']) == 1 ) ? 'selected' : ''; endif; ?>><?='Prize'.$i;?></option>
                                                        <?php endfor; ?>
                                                      </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <?php for ($i=1; $i <=$lotto_type ; $i++): ?>   
                                                        <!-- stright_prize start -->
                                                        <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('stright_prize'.$i)): ?>error<?php endif; ?>">
                                                            <label>Straight Prize Prize <?=$i.'/'.$lotto_type; ?> (Main Prize)<span class="required"></span></label>
                                                            <input type="number" min="0" name="stright_prize<?=$i?>" id="stright_prize<?=$i;?>" class="form-control" value="<?php if(set_value('stright_prize'.$i)): echo set_value('stright_prize'.$i); else: echo stripslashes($EDITDATA['stright_prize'.$i]);endif; ?>" placeholder="Straight Prize Prize <?=$i.'/'.$lotto_type; ?>" required>
                                                            <?php if(form_error('stright_prize'.$i)): ?>
                                                                <span for="prize1" generated="true" class="help-inline"><?php echo form_error('stright_prize'.$i); ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <!-- stright_prize end -->
                                                    <?php endfor; ?>
                                                </div>
                                            </fieldset>
                                            <!-- END -->
                                            <!-- Prize 2 winner inputs -->
                                            <?php if($rumble_settings == 'Enable'): ?>
                                                <fieldset>
                                                    <legend>Rumble Mix Module</legend>
                                                    <div class="row">
                                                        <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('enable_rumble_mix_prize_heading')): ?>error<?php endif; ?>">
                                                            <label>Enable/Disable Heading<span class="required">*</span></label>
                                                            <select name="enable_rumble_mix_prize_heading" id="enable_rumble_mix_prize_heading" class="form-control required">
                                                                <?php if(stripslashes($EDITDATA['enable_rumble_mix_prize_heading']) == 'Y'): ?>
                                                                <option value="Y" hidden selected>Yes</option>
                                                                <?php else: ?>
                                                                <option value="N" hidden selected>No</option>
                                                                <?php endif; ?>
                                                                <option value="Y" >Yes</option>
                                                                <option value="N">No</option>
                                                            </select>
                                                            <?php if(form_error('enable_rumble_mix_prize_heading')): ?>
                                                             <span for="enable_rumble_mix_prize_heading" generated="true" class="help-inline">
                                                                <?php echo form_error('enable_rumble_mix_prize_heading'); ?>
                                                            </span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('rumble_mix_prize_heading')): ?>error<?php endif; ?>">
                                                            <label>Rumble Mix Heading<span class="required"></span></label>
                                                            <input type="text" name="rumble_mix_prize_heading" id="rumble_mix_prize_heading" class="form-control" value="<?php if(set_value('rumble_mix_prize_heading')): echo set_value('rumble_mix_prize_heading'); else: echo stripslashes($EDITDATA['rumble_mix_prize_heading']);endif; ?>" placeholder="Rumble Mix Heading" required>
                                                            <?php if(form_error('rumble_mix_prize_heading')): ?>
                                                                <span for="rumble_mix_prize_heading" generated="true" class="help-inline"><?php echo form_error('rumble_mix_prize_heading'); ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                          <label> Select Shared Prize List</label>
                                                          <select name="rumble_mix_prize_type[]" class="form-control rumble_mix_prize_type " multiple>
                                                            <?php for ($i=1; $i <=$lotto_type ; $i++): ?>    
                                                              <option value="<?='Prize'.$i;?>"  <?php if($EDITDATA['rumble_mix_prize_type']):  echo (in_array('Prize'.$i,$EDITDATA['rumble_mix_prize_type']) == 1 ) ? 'selected' : ''; endif; ?>><?='Prize'.$i;?></option>
                                                            <?php endfor; ?>
                                                          </select>

                                                      </div>
                                                    </div>

                                                    <div class="row">
                                                        <?php for ($i=1; $i <=$lotto_type ; $i++): ?>   
                                                            <!-- rumble_mix_prize start -->
                                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('rumble_mix_prize'.$i)): ?>error<?php endif; ?>">
                                                                <label>Rumble Mix Prize Prize <?=$i.'/'.$lotto_type; ?> (Main Prize)<span class="required"></span></label>
                                                                <input type="number" min="0" name="rumble_mix_prize<?=$i?>" id="rumble_mix_prize<?=$i;?>" class="form-control" value="<?php if(set_value('rumble_mix_prize'.$i)): echo set_value('rumble_mix_prize'.$i); else: echo stripslashes($EDITDATA['rumble_mix_prize'.$i]);endif; ?>" placeholder="Rumble Mix Prize Prize <?=$i.'/'.$lotto_type; ?>" required>
                                                                <?php if(form_error('rumble_mix_prize'.$i)): ?>
                                                                    <span for="prize1" generated="true" class="help-inline"><?php echo form_error('rumble_mix_prize'.$i); ?></span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <!-- rumble_mix_prize end -->
                                                        <?php endfor; ?>
                                                    </div>
                                                </fieldset>
                                            <?php endif;?>
                                            <!-- END -->
                                            <!-- Prize 3 winner inputs -->
                                            <?php if($reverse_settings == 'Enable'): ?>
                                                <fieldset>
                                                    <legend>Chance Module</legend>
                                                    <div class="row">
                                                        <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-4 <?php if(form_error('enable_reverse_prize_heading')): ?>error<?php endif; ?>">
                                                            <label>Enable/Disable Heading<span class="required">*</span></label>
                                                            <select name="enable_reverse_prize_heading" id="enable_reverse_prize_heading" class="form-control required">
                                                                <?php if(stripslashes($EDITDATA['enable_reverse_prize_heading']) == 'Y'): ?>
                                                                <option value="Y" hidden selected>Yes</option>
                                                                <?php else: ?>
                                                                <option value="N" hidden selected>No</option>
                                                                <?php endif; ?>
                                                                <option value="Y" >Yes</option>
                                                                <option value="N">No</option>
                                                            </select>
                                                            <?php if(form_error('enable_reverse_prize_heading')): ?>
                                                             <span for="enable_reverse_prize_heading" generated="true" class="help-inline">
                                                                <?php echo form_error('enable_reverse_prize_heading'); ?>
                                                            </span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('reverse_prize_heading')): ?>error<?php endif; ?>">
                                                            <label>Chance Prize Heading<span class="required"></span></label>
                                                            <input type="text" name="reverse_prize_heading" id="reverse_prize_heading" class="form-control" value="<?php if(set_value('reverse_prize_heading')): echo set_value('reverse_prize_heading'); else: echo stripslashes($EDITDATA['reverse_prize_heading']);endif; ?>" placeholder="Reverse Heading" required>
                                                            <?php if(form_error('reverse_prize_heading')): ?>
                                                                <span for="reverse_prize_heading" generated="true" class="help-inline"><?php echo form_error('reverse_prize_heading'); ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                                          <label> Select Shared Prize List</label>
                                                          <select name="reverse_prize_type[]" class="form-control reverse_prize_type " multiple>
                                                            <?php for ($i=1; $i <=$lotto_type ; $i++): ?>    
                                                              <option value="<?='Prize'.$i;?>"  <?php if($EDITDATA['reverse_prize_type']):  echo (in_array('Prize'.$i,$EDITDATA['reverse_prize_type']) == 1 ) ? 'selected' : ''; endif; ?>><?='Prize'.$i;?></option>
                                                            <?php endfor; ?>
     
                                                          </select>
                                                      </div>
                                                    </div>
                                                    
                                                    <div class="row">
                                                        <?php for ($i=1; $i <=$lotto_type ; $i++): ?>   
                                                            <!-- reverse_prize start -->
                                                            <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('reverse_prize'.$i)): ?>error<?php endif; ?>">
                                                                <label>Chance Prize <?=$i.'/'.$lotto_type; ?> (Main Prize)<span class="required"></span></label>
                                                                <input type="number" min="0" name="reverse_prize<?=$i?>" id="reverse_prize<?=$i;?>" class="form-control" value="<?php if(set_value('reverse_prize'.$i)): echo set_value('reverse_prize'.$i); else: echo stripslashes($EDITDATA['reverse_prize'.$i]);endif; ?>" placeholder="Chance Prize <?=$i.'/'.$lotto_type; ?>" required>
                                                                <?php if(form_error('reverse_prize'.$i)): ?>
                                                                    <span for="prize1" generated="true" class="help-inline"><?php echo form_error('reverse_prize'.$i); ?></span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <!-- reverse_prize end -->
                                                        <?php endfor; ?>
                                                    </div>
                                                </fieldset>
                                            <!-- END -->
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            <fieldset>
                                                    <legend>BTC Module</legend>
                                                    <div class="row">
                                                        
                                                        <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('btc_heading')): ?>error<?php endif; ?>">
                                                            <label>BTC Heading<span class="required"></span></label>
                                                            <input type="text" name="btc_heading" id="btc_heading" class="form-control" value="<?php if(set_value('btc_heading')): echo set_value('btc_heading'); else: echo stripslashes($EDITDATA['btc_heading']);endif; ?>" placeholder="BTC Heading" required>
                                                            <?php if(form_error('btc_heading')): ?>
                                                                <span for="btc_heading" generated="true" class="help-inline"><?php echo form_error('btc_heading'); ?></span>
                                                            <?php endif; ?>
                                                        </div>

                                                        <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('btc_prize_text')): ?>error<?php endif; ?>">
                                                            <label>BTC Prize Text<span class="required"></span></label>
                                                            <input type="text" name="btc_prize_text" id="btc_prize_text" class="form-control" value="<?php if(set_value('btc_prize_text')): echo set_value('btc_prize_text'); else: echo stripslashes($EDITDATA['btc_prize_text']);endif; ?>" placeholder="BTC Prize Text" required>
                                                            <?php if(form_error('btc_prize_text')): ?>
                                                                <span for="btc_prize_text" generated="true" class="help-inline"><?php echo form_error('btc_prize_text'); ?></span>
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
                                            <a href="<?php echo correctLink('ALLPRIZEDATA',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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
    $('#category_id').on('change',function(){
        var category_id =  $(this).val();   
        $.ajax({
            url:FULLSITEURL+'products/allproducts/getsubcategoryData',
            type:'post',
            data:{category_id:category_id},
            success:function(data){
                $('#sub_category_data').html(data);
            }
        });
    });

    $(document).ready(function(){
        var category_id =  $('#category_id').val();
        var sub_category_id  =  '<?php echo $EDITDATA['sub_category_id']?>';
        $.ajax({
            url:FULLSITEURL+'products/allproducts/getsubcategoryData',
            type:'post',
            data:{category_id:category_id,sub_category_id:sub_category_id},
            success:function(data){
                $('#sub_category_data').html(data);
            }
        });

        $(".prize_type").on('click' , function(){

            var prize_type = $(this).val();
    // console.log(prize_type);

            if(prize_type == 'Cash'){
                $('.prize-section').removeClass('d-none');
            }else if(prize_type == 'Other'){
                $('.prize-section').addClass('d-none');
            }else{
                $('.prize-section').addClass('d-none');
            }   

        });

    });
</script>
<script type="text/javascript">
  $(function(){create_editor_for_textarea('description')});
  $(function(){create_editor_for_textarea('image')});
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
            url: FULLSITEURL+'products/'+CURRENTCLASS+'/imagePrizeDelete',
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