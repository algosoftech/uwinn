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
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLTicketsDATA',getCurrentControllerPath('index')); ?>"> Tickets</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Tickets</a></li>
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
                        <h5><?=$EDITDATA?'Edit':'Add'?> Tickets</h5>
                        <a href="<?php echo correctLink('ALLTicketsDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>
                    <div class="card-body">
                        <div class="basic-login-inner">
                            <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="tickets_seq_id"/>
                                <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['tickets_seq_id']?>"/>
                                <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['tickets_seq_id']?>"/>
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">

                                <div class="row">
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('tickets_prefix')): ?>error<?php endif; ?>">
                                        <label>Ticket prefix</label>
                                        <input type="text" name="tickets_prefix" id="tickets_prefix" class="form-control" value="<?php if(set_value('tickets_prefix')): echo set_value('tickets_prefix'); else: echo stripslashes($EDITDATA['tickets_prefix']);endif; ?>" placeholder="Tickets prefix">
                                        <?php if(form_error('tickets_prefix')): ?>
                                            <span for="tickets_prefix" generated="true" class="help-inline"><?php echo form_error('tickets_prefix'); ?></span>
                                        <?php endif; ?>
                                        <p style="font-family:italic; color:red;" class="d-none ticket-error">Ticket prifix is already in use for different product.</p>

                                    </div>
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('tickets_sequence_start')): ?>error<?php endif; ?>">
                                        <label>Ticket sequence start<span class="required">*</span></label>
                                        <input type="number" name="tickets_sequence_start" id="tickets_sequence_start" class="form-control required" value="<?php if(set_value('tickets_sequence_start')): echo set_value('tickets_sequence_start'); else: echo stripslashes($EDITDATA['tickets_sequence_start']);endif; ?>" placeholder="Tickets sequence start">
                                        <span for="tickets_sequence_start" generated="true" class="help-inline" id="tickets_sequence_startA" style="color: blue;"></span>
                                        <?php if(form_error('tickets_sequence_start')): ?>
                                            <span for="tickets_sequence_start" generated="true" class="help-inline"><?php echo form_error('tickets_sequence_start'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('tickets_sequence_end')): ?>error<?php endif; ?>">
                                        <label>Ticket sequence end<span class="required">*</span></label>
                                        <input type="number" name="tickets_sequence_end" id="tickets_sequence_end" class="form-control required" value="<?php if(set_value('tickets_sequence_end')): echo set_value('tickets_sequence_end'); else: echo stripslashes($EDITDATA['tickets_sequence_end']);endif; ?>" placeholder="Tickets sequence end">
                                        <span for="tickets_sequence_end" generated="true" class="help-inline" id="tickets_sequence_endA" style="color: blue;"></span>
                                        <?php if(form_error('tickets_sequence_end')): ?>
                                            <span for="tickets_sequence_end" generated="true" class="help-inline"><?php echo form_error('tickets_sequence_end'); ?></span>
                                        <?php endif; ?>
                                    </div>
                              
                                    <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="inline-remember-me mt-4">
                                            <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                            <button class="btn btn-primary mb-4 submit-btn">Submit</button>
                                            <a href="<?php echo correctLink('ALLTicketsDATA',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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
    $("#tickets_prefix").on('keyup' , function(){
        
        let tickets_prefix  =  $(this).val();
        let product_id      =  "<?=$this->session->userdata('productID4tickets');?>";

        $.ajax({
           type: 'POST',
           url: "<?=base_url('/tickets/alltickets/check_tickets_prefix');?>",
           data: { tickets_prefix:tickets_prefix,product_id:product_id },
           dataType: "text",
           success: function(result) { 

            var parsedData = $.parseJSON(result);
            
            console.log(parsedData.status)
           
            if(parsedData.status === "Y"){
                $(".submit-btn").attr('disabled',true);
                $(".ticket-error").show();

            }else{
                $(".submit-btn").attr('disabled',false);
                $(".ticket-error").removeClass('d-none');
                $(".ticket-error").hide();

            }

           }
        })


    });
</script>