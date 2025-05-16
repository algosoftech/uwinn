<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script>
$(function(){
   $("#date").datepicker({dateFormat:'yy-mm-dd',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
});
</script>
<style>
    #per_btn:hover{ color: black !important; }
</style>
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
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLRECHARGEDATA',getCurrentControllerPath('index')); ?>">Recharge</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Recharge</a></li>
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
                        <h5><?=$EDITDATA?'Edit':'Add'?> Recharge</h5>
                        <a href="<?php echo correctLink('ALLRECHARGEDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                        <a href="javaScriptcript:void{0}" class="btn btn-sm btn-primary pull-right mr-2" data-toggle="modal" data-target="#bulkRecharge">Bulk Recharge</a>
                    </div>
                    <div class="card-body">
                        <div class="basic-login-inner">
                            <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="recharge_id"/>
                                <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['recharge_id']?>"/>
                                <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['recharge_id']?>"/>
                                <input type="hidden" name="userID" id="userID" value="<?=$EDITDATA['userID']?>"/>
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">

                                <div class="row">
                                     <div class="form-group-inner col-lg-2 col-md-2 col-sm-2 col-xs-12 <?php if(form_error('user')): ?>error<?php endif; ?>">
                                        <label>Rechange For <span class="required">*</span></label>
                                        <select class="form-control required rechange_for" name="rechange_for">
                                            <option>Email</option>
                                            <option>Mobile No.</option>
                                        </select>
                                        <?php if(form_error('user')): ?>
                                        <span for="user" generated="user" class="help-inline"><?php echo form_error('user'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('user')): ?>error<?php endif; ?>">
                                        <label class="rechare_for_label">Email ID<span class="required">*</span></label>
                                        <input type="text" name="user" id="user" class="form-control required" value="<?php if(set_value('user')): echo set_value('user'); else: echo stripslashes($EDITDATA['user']);endif; ?>" placeholder="Email Id / Mobile No. ">
                                        <span id="availableArabianPoints" style="color: blue;"></span>
                                        <?php if(form_error('user')): ?>
                                        <span for="user" generated="user" class="help-inline"><?php echo form_error('user'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('ipoints')): ?>error<?php endif; ?>">
                                        <label>Add UPOINT <span class="required">*</span></label>
                                        <input type="number" min="1" name="addUpoints" id="addUpoints" class="form-control required" value="<?php if(set_value('addUpoints')): echo set_value('addUpoints'); else: echo stripslashes($EDITDATA['addUpoints']);endif; ?>" placeholder="Add UPOINT">
                                        <?php if(form_error('addUpoints')): ?>
                                        <span for="addUpoints" generated="true" class="help-inline"><?php echo form_error('addUpoints'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <a class="btn btn-primary mb-4 per_btn" id="per_btn" data-percentage="5" style="color: white;">5%</a>
                                        <a class="btn btn-primary mb-4 per_btn" id="per_btn" data-percentage="10" style="color: white;">10%</a>
                                        <a class="btn btn-primary mb-4 per_btn" id="per_btn" data-percentage="15" style="color: white;">15%</a>
                                        <a class="btn btn-primary mb-4 per_btn" id="per_btn" data-percentage="20" style="color: white;">20%</a>
                                    </div>
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                        <input type="number" name="percentage" id="percentage" class="form-control" value="<?php if(set_value('percentage')): echo set_value('percentage'); endif;  ?>"/>
                                    </div>

                                    <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('remarks')): ?>error<?php endif; ?>">
                                        <label>Remarks </label>
                                        <textarea name="remarks" id="remarks" class="form-control"><?php if(set_value('remarks')): echo set_value('remarks'); else: echo stripslashes($EDITDATA['remarks']);endif; ?></textarea>
                                        <?php if(form_error('remarks')): ?>
                                        <span for="remarks" generated="true" class="help-inline"><?php echo form_error('remarks'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="inline-remember-me mt-4">
                                            <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                            <button class="btn btn-primary mb-4 recharge_btn">Recharge</button>
                                            <a href="<?php echo correctLink('ALLRECHARGEDATA',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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

<div class="modal fade" id="bulkRecharge" tabindex="-1" role="dialog" aria-labelledby="bulkRechargeModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bulkRechargeModalLabel">Upload Bulk Rechage CSV</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?=getCurrentControllerPath('checkpreview');?>" method="post" enctype="multipart/form-data" autocomplete="off">
          <div class="modal-body">
           <div class="row">
                <div class="col-sm-12 col-md-12">
                    <h6>
                      Uplaod Draw List CSV 
                      <sup><a href="<?=fileBaseUrl.'assets/topup.csv'?>">Sample</a></sup>
                    <div class="upload-btn-wrapper">
                      <input type="file" name="csvFile"  id="csvFile" accept=".csv" />
                    </div>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="uplaod-btn btn btn-primary" id="recharge-upload-btn" disabled title="Upload CSV file first" >Check Preview</button>
          </div>
      </form>
    </div>
  </div>
</div>

 
<script>

     $('#csvFile').on('change', function(){
       let file = $(this).val();
       $('#recharge-upload-btn').attr('disabled' , false);
    });

    $('.per_btn').click(function(){
        var percentage = $(this).data('percentage');
        $('#percentage').val(percentage);
    });

    $(".rechange_for").on('change' , function(){
        var rechange_for = $(this).val();
        //on change removeing previews value.
        $('#user').val('');
        $('#availableArabianPoints').empty();  


        if(rechange_for == 'Email'){
            $('.rechare_for_label').empty().append('Email ID <span class="required">*</span>');
        }else{
            $('.rechare_for_label').empty().append('Mobile No. <span class="required">*</span>');

        }
        // console.log(rechange_for);

    });

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


</script>