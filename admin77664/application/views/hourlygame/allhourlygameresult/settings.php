<style type="text/css">
.form-check-input {
     position: unset; 
     margin-top: unset;
     margin-left: unset; 
}

#range_output {
    display: inline-block;
    margin-left: 10px;
    font-weight: bold;
    color: #495057;
}

</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
 
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
                        <h5><?=isset($EDITDATA) && !empty($EDITDATA) ? 'Edit' : 'Add'?> Result Settings</h5>
                        <a href="<?php echo correctLink('ALLRESULTSDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>

                    <div class="card-body">
                        <div class="basic-login-inner">
                            <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="_id"/>
                                <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=isset($EDITDATA['result_id']) ? $EDITDATA['result_id'] : ''?>"/>
                                <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=isset($EDITDATA['_id']) && is_object($EDITDATA['_id']) ? $EDITDATA['_id']->{'$id'} : (isset($EDITDATA['_id']) ? $EDITDATA['_id'] : '')?>"/>
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">

                                <div class="row">

                                    <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('range_value')): ?>error<?php endif; ?>">
                                        <label for="range_value">Range Value</label>
                                        <input type="range" name="range_value" id="range_value" class="form-control" min="0" max="100" step="1" value="<?php if(set_value('range_value')): echo set_value('range_value'); else: echo isset($EDITDATA['range_value']) ? stripslashes($EDITDATA['range_value']) : '50';endif; ?>">
                                        <output for="range_value" id="range_output"><?php if(set_value('range_value')): echo set_value('range_value'); else: echo isset($EDITDATA['range_value']) ? stripslashes($EDITDATA['range_value']) : '50';endif; ?></output>
                                        <?php if(form_error('range_value')): ?>
                                            <span for="range_value" generated="true" class="help-inline"><?php echo form_error('range_value'); ?></span>
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

<script type="text/javascript">
$(document).ready(function(){
    // Update range output when slider changes
    $('#range_value').on('input change', function(){
        $('#range_output').text($(this).val());
    });
    
    // Ensure min and max are properly set as numbers
    var $rangeInput = $('#range_value');
    if($rangeInput.length) {
        $rangeInput.attr('min', '0');
        $rangeInput.attr('max', '100');
        
        // Ensure the value is within valid range
        var currentVal = parseInt($rangeInput.val()) || 50;
        if(currentVal < 0) currentVal = 0;
        if(currentVal > 100) currentVal = 100;
        $rangeInput.val(currentVal);
        $('#range_output').text(currentVal);
    }
    
    // Initialize form validation with explicit rules for range_value
    if($("#currentPageForm").length) {
        // Remove any existing validation
        if($("#currentPageForm").data('validator')) {
            $("#currentPageForm").data('validator', null);
        }
        
        $("#currentPageForm").validate({
            rules: {
                range_value: { 
                    required: true,
                    range: [0, 100],
                    number: true
                }
            },
            messages: {
                range_value: {
                    required: "Range value is required.",
                    range: "Please enter a value between 0 and 100.",
                    number: "Please enter a valid number."
                }
            },
            errorClass: "error",
            errorElement: "span",
            errorPlacement: function(error, element) {
                if(element.attr("name") == "range_value") {
                    error.insertAfter(element.siblings("#range_output"));
                } else {
                    error.insertAfter(element);
                }
            }
        });
    }
});
</script>