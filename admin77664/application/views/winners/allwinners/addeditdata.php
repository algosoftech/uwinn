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
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLWINNERSDATA',getCurrentControllerPath('index')); ?>"> Winners</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Winners</a></li>
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
                        <h5><?=$EDITDATA?'Edit':'Add'?> Winners</h5>
                        <a href="<?php echo correctLink('ALLWINNERSDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>
                    <div class="card-body">
                        <div class="basic-login-inner">
                            <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="winners_id"/>
                                <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['winners_id']?>"/>
                                <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['winners_id']?>"/>
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">

                                <div class="row">
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('name')): ?>error<?php endif; ?>">
                                        <label>Winner's Name<span class="required">*</span></label>
                                        <input type="text" name="name" id="name" class="form-control required" value="<?php if(set_value('name')): echo set_value('name'); else: echo stripslashes($EDITDATA['name']);endif; ?>" placeholder="Winner's Name">
                                        <?php if(form_error('name')): ?>
                                        <span for="name" generated="true" class="help-inline"><?php echo form_error('name'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('title')): ?>error<?php endif; ?>">
                                        <label>Title<span class="required">*</span></label>
                                        <input type="text" name="title" id="title" class="form-control required" value="<?php if(set_value('title')): echo set_value('title'); else: echo stripslashes($EDITDATA['title']);endif; ?>" placeholder="Title">
                                        <?php if(form_error('title')): ?>
                                        <span for="title" generated="true" class="help-inline"><?php echo form_error('title'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('coupon')): ?>error<?php endif; ?>">
                                        <label>Coupon Code<span class="required">*</span></label>
                                        <input type="text" name="coupon" id="coupon" class="form-control required" value="<?php if(set_value('coupon')): echo set_value('coupon'); else: echo stripslashes($EDITDATA['coupon']);endif; ?>" placeholder="Coupon Code">
                                        <span for="coupon" generated="true" class="help-inline" id="couponA" style="color: blue;"></span>
                                        <?php if(form_error('coupon')): ?>
                                        <span for="coupon" generated="true" class="help-inline"><?php echo form_error('coupon'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <input type="hidden" name="coupon_id" id="coupon_id" class="form-control required" value="<?php if(set_value('coupon_id')): echo set_value('coupon_id'); else: echo stripslashes($EDITDATA['coupon_id']);endif; ?>">

                                    <input type="hidden" name="user_id" id="users_id" class="form-control required" value="<?php if(set_value('user_id')): echo set_value('user_id'); else: echo stripslashes($EDITDATA['user_id']);endif; ?>">

                                    <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('aDate')): ?>error<?php endif; ?>">
                                        <label>Announcement Date<span class="required">*</span></label>
                                        <input type="date" name="announcedDate" id="announcedDate" class="form-control required" value="<?php if(set_value('announcedDate')): echo set_value('announcedDate'); else: echo stripslashes($EDITDATA['announcedDate']);endif; ?>">
                                        <?php if(form_error('announcedDate')): ?>
                                        <span for="announcedDate" generated="true" class="help-inline"><?php echo form_error('announcedDate'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('announcedTime')): ?>error<?php endif; ?>">
                                        <label>Announcement Time<span class="required">*</span></label>
                                        <input type="time" name="announcedTime" id="announcedTime" class="form-control required" value="<?php if(set_value('announcedTime')): echo set_value('announcedTime'); else: echo stripslashes($EDITDATA['announcedTime']);endif; ?>">
                                        <?php if(form_error('announcedTime')): ?>
                                        <span for="announcedTime" generated="true" class="help-inline"><?php echo form_error('announcedTime'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                     <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('country')): ?>error<?php endif; ?>">
                                        <label>Country</label>
                                        <input type="text" name="country" id="country" class="form-control" value="<?php if(set_value('country')): echo set_value('country'); else: echo stripslashes($EDITDATA['country']);endif; ?>">
                                        <?php if(form_error('country')): ?>
                                        <span for="country" generated="true" class="help-inline"><?php echo form_error('country'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 winner-position-section <?php echo  empty($EDITDATA['winner_position']) ? 'd-none':'' ;?> ">
                                         <label>Winner Position:</label>
                                        <select name="winner_position" class="form-control prize_type">
                                            <?php if ($EDITDATA['winner_position'] == 'first' ): ?>
                                                <option value="first" <?php echo $EDITDATA['winner_position'] =='first'? 'selected' :'' ?> > Prize 1</option>
                                             <?php elseif ($EDITDATA['winner_position'] == 'second' ): ?>
                                                <option value="second" <?php echo $EDITDATA['winner_position'] =='second'? 'selected' :'' ?>> Prize 2</option>
                                             <?php elseif ($EDITDATA['winner_position'] == 'third' ): ?>
                                                <option value="third" <?php echo $EDITDATA['winner_position'] =='third'? 'selected' :'' ?>> Prize 3</option>
                                            <?php endif ?>
                                        </select>
                                    </div>

                                    
                                    
                                </div>
                                <!-- <div class="row">
                                    <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('Description')): ?>error<?php endif; ?>">
                                        <label>Description</label>
                                        <textarea id="description" placeholder="Description" class="form-control" name="description"><?php if(set_value('description')): echo set_value('description'); else: echo stripslashes($EDITDATA['description']);endif; ?></textarea>
                                    </div>
                                </div> -->
                                
                                <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('winners_image')): ?>error<?php endif; ?>">
                        <label>Image</label><br>
                        <input type="file" name="winners_image" id="winners_image" class="" value="<?php if(set_value('winners_image')): echo set_value('winners_image'); endif; ?>" accept="image/png, image/jpeg" <?php if(empty($EDITDATA['winners_image'])){?>
                            required
                        <?php } ?> >
                       <p style="font-family:italic; color:red;">[Image Size : 241 x 136 px in jpg/jpeg/png]</p>
                        <?php if($EDITDATA['winners_image']): ?>
                           <div id="ImageDiv2"><img src="<?php echo fileBaseUrl.$EDITDATA['winners_image']; ?>" width="50" border="0" alt="">&nbsp;
                           <a href="javascript:void(0);" onclick="ImageDelete('<?php echo $EDITDATA['winners_image']; ?>','<?php echo $EDITDATA['shop_category_id']; ?>','app');"> 
                              <img src="{ASSET_INCLUDE_URL}image/cross.png" border="0" alt="">
                            </a></div>
                          <?php endif; ?>
                        <?php if(form_error('winners_image')): ?>
                          <span for="winners_image" generated="true" class="help-inline"><?php echo form_error('winners_image'); ?></span>
                        <?php endif; ?>
                      </div>
                      <!-- <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('stock')): ?>error<?php endif; ?>">
                        <label>Arabian Points<span class="required">*</span></label>
                        <input type="number" min="0" name="adepoints" id="adepoints" class="form-control required" value="<?php if(set_value('adepoints')): echo set_value('adepoints'); else: echo stripslashes($EDITDATA['adepoints']);endif; ?>" placeholder="Arabian Points" readonly>
                        <?php if(form_error('adepoints')): ?>
                        <span for="adepoints" generated="true" class="help-inline"><?php echo form_error('adepoints'); ?></span>
                        <?php endif; ?>
                    </div> -->
                    </div>
                    
                                <div class="row">
                                    <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="inline-remember-me mt-4">
                                            <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                            <button class="btn btn-primary mb-4">Submit</button>
                                            <a href="<?php echo correctLink('ALLWINNERSDATA',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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
});
</script>
<script type="text/javascript">
  $(function(){create_editor_for_textarea('description')});
  $(function(){create_editor_for_textarea('image')});
</script>

<script type="text/javascript">
  $(function(){create_editor_for_textarea('description')});
  $(function(){create_editor_for_textarea('image')});
</script>

<script type="text/javascript">
$(document).ready(function(){
$('#coupon').change(function(){
var coupon = $(this).val();
var ur      = '<?=base_url().'winners/allwinners/checkDeplicacy'?>';
$.ajax({
url : ur,
method: "POST", 
data: {coupon: coupon},
success: function(data1){
var data2 = data1.split("__");
$('#couponA').empty().append(data2[0]);
$('#coupon_id').val(data2[1]);
$('#adepoints').val(data2[2]);
$('#users_id').val(data2[3]);

    console.log(data2);
  

    if(data2[4] == 'Cash'){

        $(".winner-position-section").removeClass('d-none');

        $('.prize_type').find("option").remove().end();
        

        if ( data2[8] =="first" || data2[9] =="first" || data2[10] =="first" ){
            
            if(data2[5] !='' && data2[5] > 0 ){
                $('.prize_type').append('<option value="first" disabled > Prize 1</option>');
            }

        }else{

            if(data2[5] !='' && data2[5] > 0 ){
                $('.prize_type').append('<option value="first" > Prize 1</option>');
            }

        }
            

         if( data2[8] =="second" || data2[9] =="second" || data2[10] =="second" ){
            
            if(data2[6] !='' && data2[6] > 0){
                $('.prize_type').append(`<option value="second" disabled  > Prize 2</option>`);
            }
         }else{

            if(data2[6] !='' && data2[6] > 0){
                $('.prize_type').append(`<option value="second" > Prize 2</option>`);
            }
         }
        
         if( data2[8] =="third" || data2[9] =="third" || data2[10] =="third" ){

            if(data2[7] !='' && data2[7] > 0){
                $('.prize_type').append(`<option value="third" disabled > Prize 3</option>`);
            }

        }else{

            if(data2[7] !='' && data2[7] > 0){
                $('.prize_type').append(`<option value="third" > Prize 3</option>`);
            }

        }

    }else{
         $(".winner-position-section").addClass('d-none');
         $('.prize_type').find("option").remove().end();

    }

}
});
});
});
</script>