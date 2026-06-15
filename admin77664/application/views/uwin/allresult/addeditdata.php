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
$(function() {
    $("#result_date").datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        yearRange: "1970:<?= date('Y') ?>",
        minDate: -1,   // yesterday
        maxDate: 0     // today
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
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLRESULTSDATA',getCurrentControllerPath('index')); ?>"> Product</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Result</a></li>
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
                        <h5><?=$EDITDATA?'Edit':'Add'?> Result</h5>
                        <a href="<?php echo correctLink('ALLRESULTSDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>

                    <div class="card-body">
                        <div class="basic-login-inner">
                            <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="_id"/>
                                <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['result']?>"/>
                                <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['_id']->{'$id'}?>"/>
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                                <?php
                                
                                $date = date('Y-m-d');
                                if(!empty($EDITDATA['result_date'])):
                                $date = date('Y-m-d',$EDITDATA['result_date']);
                                elseif(!empty($this->session->userdata('resultDate'))):
                                $date = $this->session->userdata('resultDate');
                                endif; 
                                ?>
                                <div class="row">
                                    <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('result_date')): ?>error<?php endif; ?>">
                                        <label for="result_date">Result Date <span class="required">*</span></label>
                                        <input type="text" name="result_date" id="result_date" class="form-control required" value="<?php if(set_value('result_date')): echo set_value('result_date'); else: echo $date;endif; ?>">
                                        <?php if(form_error('result_date')): ?>
                                            <span for="result_date" generated="true" class="help-inline"><?php echo form_error('result_date'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if(!empty($allproduct)  ): ?>
                                        <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('product_details')): ?>error<?php endif; ?>">
                                            <label>Active Campaigns <span class="required">*</span></label>
                                            <select name="product_details" id="product_details" class="product_details form-control required">
                                                <option value="">Select Campaign</option>   
                                                <?php foreach($allproduct as $product): ?>
                                                    <option value="<?=$product['products_id'].'_____'.$product['title'].'_____'.$product['draw_time'];?>" <?=stripslashes($EDITDATA['products_id']) == $product['products_id'] ? 'selected' : '';?>> 
                                                        <?=$product['title'].' ('.date('d-m-Y H:i',strtotime($product['draw_date'].' '.$product['draw_time'])).')'; ?>  
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>

                                            <?php if(form_error('product_details')): ?>
                                                <span for="product_details" generated="true" class="help-inline">
                                                <?php echo form_error('product_details'); ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>  
                                    <?php endif; ?>

                                    <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('draw_result')): ?>error<?php endif; ?>">
                                        <label>Draw Result<span class="required">*</span></label>
                                        <input type="text" name="draw_result" id="draw_result" class="form-control required" value="<?php if(set_value('draw_result')): echo set_value('draw_result'); else: echo stripslashes($EDITDATA['draw_result']);endif; ?>">
                                        <?php if(form_error('draw_result')): ?>
                                            <span for="draw_result" generated="true" class="help-inline"><?php echo form_error('draw_result'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                 
                                <div class="row">
                                    <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="inline-remember-me mt-4">
                                            <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                            <button class="btn btn-primary mb-4">Submit</button>
                                            <a href="<?php echo correctLink('ALLRESULTSDATA',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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