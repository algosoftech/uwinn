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
                     <h5 class="m-b-10">Welcome <?=sessionData('SCHW_ADMIN_FIRST_NAME')?></h5>
                     <?php */ ?>
                  </div>
                  <ul class="breadcrumb">
                     <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                     <li class="breadcrumb-item"><a href="<?php echo correctLink('CITYDATA',getCurrentControllerPath('index')); ?>"> Region</a></li>
                     <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> City</a></li>
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
                  <h5><?=$EDITDATA?'Edit':'Add'?> City</h5>
                  <a href="<?php echo correctLink('CITYDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
               </div>
               <div class="card-body">
                  <div class="basic-login-inner">
                     <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                        <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="city_id"/>
                        <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['city_id']?>"/>
                        <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['city_id']?>"/>
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                        <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('state_id')): ?>error<?php endif; ?>">
                        <label>State<span class="required">*</span></label>
                        <?php if(set_value('state_id')): $categoryiddata = explode('_____',set_value('state_id')); $state_id = $categoryiddata[0]; elseif($EDITDATA['state_id']): $state_id = stripslashes($EDITDATA['state_id']); else: $state_id = ''; endif; ?>
                        <select name="state_id" id="state_id" class="form-control required">
                          <?php echo $this->admin_model->getStateList($state_id); ?>
                        </select>
                        <?php if(form_error('state_id')): ?>
                          <span for="state_id" generated="true" class="help-inline"><?php echo form_error('state_id'); ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('city_name')): ?>error<?php endif; ?>">
                              <label>City<span class="required">*</span></label>
                              <input type="text" name="city_name" id="city_name" class="form-control required" value="<?php if(set_value('city_name')): echo set_value('city_name'); else: echo stripslashes($EDITDATA['city_name']);endif; ?>" placeholder="Title">
                              <?php if(form_error('city_name')): ?>
                              <span for="city_name" generated="true" class="help-inline"><?php echo form_error('city_name'); ?></span>
                              <?php endif; ?>
                           </div>
                    </div>
                        
                        
                        
                        
                        
                        <div class="row">
                           <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                              <div class="inline-remember-me mt-4">
                                 <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                 <button class="btn btn-primary mb-4">Submit</button>
                                 <a href="<?php echo correctLink('CITYDATA',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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
<script>
   function ImageDelete(imageName,id,typ)
   {//alert(id);
     if(confirm("Sure to delete?"))
     {//alert(CURRENTCLASS);
       $.ajax({
             type: 'post',
              url: FULLSITEURL+'masterdata/'+CURRENTCLASS+'/imageDelete',
             data: {imageName:imageName,id:id,typ:typ},
          success: function(rdata) { 
               if(parseInt(rdata.status) == 1) {
                 $('#image').val('');
                 $('#ImageDiv').html('');
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
<script type="text/javascript">
   $(function(){create_editor_for_textarea('description')});
   $(function(){create_editor_for_textarea('description2')});
</script>