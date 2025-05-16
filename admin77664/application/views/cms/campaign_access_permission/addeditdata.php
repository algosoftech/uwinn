<!-- <link rel="stylesheet" href="{ASSET_INCLUDE_URL}canvasCrop/user.image.canvasCrop.css">
 --><link rel="stylesheet" href="{ASSET_INCLUDE_URL}canvasCrop/about.image.canvasCrop.css">
<script type="text/javascript" src="{ASSET_INCLUDE_URL}canvasCrop/jquery.canvasCrop.js"></script>
<style type="text/css">
  input#show_vat {
    margin-right: 30%;
    margin-left: 6px;
}
.AdduserBtn{
    cursor: pointer;
}

</style>
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
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('CMSCAMPAIGNACCESSPERMISSION',getCurrentControllerPath('index')); ?>"> CMS</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Campaign Access Permission</a></li>
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
                <h5><?=$EDITDATA?'Edit':'Add'?> Campaign Access Permission</h5>
                <a href="<?php echo correctLink('CMSCAMPAIGNACCESSPERMISSION',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
              </div>
              <div class="card-body">
                <div class="basic-login-inner">
                  <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="allowd_user_id"/>
                    <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['allowd_user_id']?>"/>
                    <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['allowd_user_id']?>"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                  
                      
                    <div class="row">
                        <div class="form-group-inner col-lg-3 col-md-3 col-sm-12 col-xs-12 <?php if(form_error('seleted_campaign')): ?>error<?php endif; ?>">
                           <label>Active Campaign List</label>
                           <select name="seleted_campaign[]" id="seleted_campaign" class="form-control" multiple  required>
                            <?php foreach ($productDetails as  $items): ?>
                              <option value="<?=$items['products_id'];?>" <?php if($EDITDATA['seleted_campaign']): echo in_array($items['products_id'],$EDITDATA['seleted_campaign'])?'selected':''; endif; ?> ><?=$items['title'];?></option>
                            <?php endforeach; ?>
                           </select>
                           <?php if(form_error('seleted_campaign')): ?>
                            <span for="seleted_campaign" generated="true" class="help-inline"><?php echo form_error('seleted_campaign'); ?></span>
                           <?php endif; ?>
                        </div>
                        <div class="form-group-inner col-lg-2 col-md-2 col-sm-2 col-xs-12 <?php if(form_error('user')): ?>error<?php endif; ?>">
                          <label>Rechange For <span class="required">*</span></label>
                          <select class="form-control required rechange_for" name="rechange_for">
                              <option>Email</option>
                              <option>Mobile No.</option>
                          </select>
                          <?php if(form_error('user')): ?>
                          <span for="user" generated="user" class="help-inline"><?php echo form_error('user'); ?></span>
                          <?php endif; ?>
                          <div class="mt-2 AdduserBtn btn btn-primary">Add User</div>
                        </div>
                        <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('user')): ?>error<?php endif; ?>">
                          <label class="rechare_for_label">User  <?php if(empty($EDITDATA['seleted_users'])){ echo '<span class="required">*</span>'; }  ?>  </label>
                          <input type="text" name="user" id="user" class="form-control <?php if(empty($EDITDATA['seleted_users'])){ echo 'required'; }  ?> " value="<?php if(set_value('user')): echo set_value('user'); else: echo stripslashes($EDITDATA['user']);endif; ?>" placeholder="Email Id / Mobile No. ">
                          <span id="availableArabianPoints" style="color: blue;"></span>
                          <?php if(form_error('user')): ?>
                          <span for="user" generated="user" class="help-inline"><?php echo form_error('user'); ?></span>
                          <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group-inner col-lg-3 col-md-3 col-sm-12 col-xs-12 <?php if(form_error('seleted_users')): ?>error<?php endif; ?>">
                          <label>Selected Users</label>
                          <select name="seleted_users[]" id="seleted_users" class="form-control" multiple  required>
                              <option>Select</option>
                             <?php foreach ($EDITDATA['seleted_users'] as  $Uitem): ?>

                              <?php
                                $tblName       =  'uw_users';
                                $Fields        =  array('users_name','users_id');
                                $wcon['where'] =  array('users_id' => (int)$Uitem);
                                $shortField    =   array('seq_order' => 1);
                                $userDetails   =  $this->common_model->getDataByNewQuery($Fields,'multiple',$tblName,$wcon,$shortField);
                              ?>

                              <?php if($userDetails[0]['users_id']): ?>
                                <option value="<?=$userDetails[0]['users_id'];?>" selected><?=$userDetails[0]['users_name'];?></option>
                              <?php endif;?>
                            <?php endforeach; ?>
                          </select>
                        </div>
                        
                    </div>

                   
                    <div class="row">
                      <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="inline-remember-me mt-4">
                          <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                          <button class="btn btn-primary mb-4">Submit</button>
                          <a href="<?php echo correctLink('CMSPRIVACYPOLICY',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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
  $(function(){create_editor_for_textarea('description')});

   // mobile number validation..
    $("#user").on("keyup", function(){

       var rechange_for = $('.rechange_for').val();
       
       userData = $(this).val();
      

        if(rechange_for == 'Mobile No.') {


            console.log($.isNumeric( userData ));
                
            // console.log(userData.slice(0,1));

            if(parseInt(userData.slice(0,1)) == 0){
                
                $('#availableArabianPoints').empty().append('First number should not be zero.');
                $('.recharge_btn').attr("disabled", true);
                
            }else if($.isNumeric( userData ) == false){

                $('#availableArabianPoints').empty().append('Mobile no. should be in number.');
                $('.recharge_btn').attr("disabled", true);

            }else if( userData.length >15 ){
                
                $('#availableArabianPoints').empty().append('Enter valid mobile number');
                $('.recharge_btn').attr("disabled", true);

            }else if( userData.length >=8 ){

                var user = $(this).val();
                var ur      = '<?=base_url().'/recharge/allrecharge/checkDeplicacy'?>';
                $.ajax({
                    url : ur,
                    method: "POST", 
                    data: {user: user},
                    success: function(data){
                    var data1 = data.split("__");

                    if(data1[1] != ""){
                        $('#availableArabianPoints').empty().append(data1[0]);
                        $('.recharge_btn').attr("disabled", false);
                    }else{
                        $('#availableArabianPoints').empty().append('Enter valid mobile number');
                        $('.recharge_btn').attr("disabled", true);
                    }
                    $('#userID').empty().val(data1[1]);
                  }
                });

            }else{
                $('#availableArabianPoints').empty();
            }
        }

    });

     // Email id validation..
    $("#user").on("change", function(){

       var rechange_for = $('.rechange_for').val();
       userData = $(this).val();

        if(rechange_for == 'Email'){
            var inputvalues = $(this).val();    
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;    
            if(!regex.test(inputvalues)){    
                $('#availableArabianPoints').empty().append('Enter valid email id');  
                $('.recharge_btn').attr("disabled", true);
            }else{


                var user = $(this).val();
                var ur      = '<?=base_url().'/recharge/allrecharge/checkDeplicacy'?>';
                $.ajax({
                    url : ur,
                    method: "POST", 
                    data: {user: user},
                    success: function(data){
                    var data1 = data.split("__");

                    if(data1[1] != ""){
                        $('#availableArabianPoints').empty().append(data1[0]);
                        $('.recharge_btn').attr("disabled", false);
                    }else{
                        $('#availableArabianPoints').empty().append('Email id not exist.');
                        $('.recharge_btn').attr("disabled", true);
                    }
                    $('#userID').empty().val(data1[1]);


                  }
                });

            } 
        }


    });

    $(".AdduserBtn").on('click',function(){

        VarifieldUser = $('#availableArabianPoints').text();

        if( VarifieldUser == "" ||VarifieldUser == "Enter valid mobile number" || VarifieldUser == "Mobile no. should be in number." || VarifieldUser =="Enter valid email id"){
            alert('Please enter correct details.');
        }else{
            let user = $('#user').val();
            var ur      = '<?=base_url().'/recharge/allrecharge/checkDeplicacy'?>';
            $.ajax({
                url : ur,
                method: "POST", 
                data: {user: user},
                success: function(data){
                var dataArray = data.split("__");

                let Option = "<option value="+dataArray[1]+" selected >"+user+"</option>"
                console.log(Option);

                $('#seleted_users').append(Option)

                
              }
            });


        }

    });


</script>