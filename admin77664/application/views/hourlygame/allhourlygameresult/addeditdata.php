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
        minDate: -3,   // previous 3 days
        maxDate: 0     // today
    });
});

function hourlyDrawTimeLabel(slot) {
    if (!slot) {
        return '';
    }
    var match = String(slot).trim().match(/(\d{2}:\d{2}:\d{2})$/);
    return match ? match[1] : slot;
}

function updateHourlyGameTimeOptions() {
    var $productSelect = $("#product_details");
    var $timeSelect = $("#hourly_game_time");
    var selectedTime = $timeSelect.data("selected-time") || '';
    var resultDate = $("#result_date").val() || '';
    var selectedOption = $productSelect.find("option:selected");
    var drawOptionsRaw = selectedOption.attr("data-draw-options") || '[]';
    var drawOptions = [];

    try {
        drawOptions = JSON.parse(drawOptionsRaw);
    } catch (e) {
        drawOptions = [];
    }

    $timeSelect.html('<option value="">Select Hourly Game Time</option>');

    if (!Array.isArray(drawOptions) || drawOptions.length === 0) {
        return;
    }

    for (var i = 0; i < drawOptions.length; i++) {
        var slot = drawOptions[i];
        if (!slot) {
            continue;
        }
        if (resultDate && String(slot).indexOf(resultDate) !== 0) {
            continue;
        }
        var timeLabel = hourlyDrawTimeLabel(slot);
        var isSelected = selectedTime === slot ? ' selected' : '';
        $timeSelect.append('<option value="' + slot + '"' + isSelected + '>' + timeLabel + '</option>');
    }
}

$(document).ready(function() {
    $("#product_details").on("change", function() {
        $("#hourly_game_time").data("selected-time", '');
        updateHourlyGameTimeOptions();
    });
    $("#result_date").on("change", function() {
        $("#hourly_game_time").data("selected-time", '');
        updateHourlyGameTimeOptions();
    });
    updateHourlyGameTimeOptions();
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
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLRESULTSDATA',getCurrentControllerPath('index')); ?>"> Hourly Game</a></li>
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
                        <a href="<?php echo correctLink('ALLHOURLYGAMERESULT',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>

                    <div class="card-body">
                        <div class="basic-login-inner">
                            <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                <?php
                                if(is_object($EDITDATA)):
                                    $EDITDATA = (array)$EDITDATA;
                                endif;
                                $currentDataId = '';
                                if(!empty($EDITDATA['_id'])):
                                    if(is_object($EDITDATA['_id']) && method_exists($EDITDATA['_id'], '__toString')):
                                        $currentDataId = (string)$EDITDATA['_id'];
                                    elseif(is_object($EDITDATA['_id']) && isset($EDITDATA['_id']->{'$id'})):
                                        $currentDataId = $EDITDATA['_id']->{'$id'};
                                    elseif(is_array($EDITDATA['_id']) && !empty($EDITDATA['_id']['$id'])):
                                        $currentDataId = $EDITDATA['_id']['$id'];
                                    endif;
                                endif;
                                ?>
                                <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="_id"/>
                                <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=!empty($EDITDATA['result']) ? $EDITDATA['result'] : '';?>"/>
                                <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$currentDataId;?>"/>
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                                <?php
                                $date = date('Y-m-d');
                                $selectedHourlyDateTime = '';
                                $editProductsId = !empty($EDITDATA['products_id']) ? (int)$EDITDATA['products_id'] : 0;
                                if(!empty($EDITDATA['result_date'])):
                                $date = date('Y-m-d',$EDITDATA['result_date']);
                                elseif(!empty($this->session->userdata('resultDate'))):
                                $date = $this->session->userdata('resultDate');
                                endif; 

                                if(set_value('hourly_game_time')):
                                    $selectedHourlyDateTime = set_value('hourly_game_time');
                                elseif(!empty($EDITDATA['draw_result_time']) && !empty($date)):
                                    $selectedHourlyDateTime = $date.' '.stripslashes($EDITDATA['draw_result_time']);
                                endif;

                                $defaultHourlyTimeOptions = array();
                                if(!empty($allproduct)):
                                    $selectedProductMeta = array();
                                    foreach($allproduct as $product):
                                        if((int)$editProductsId === (int)$product['products_id']):
                                            $selectedProductMeta = $product;
                                            break;
                                        endif;
                                    endforeach;

                                    if(!empty($selectedProductMeta['draw_time_options']) && is_array($selectedProductMeta['draw_time_options'])):
                                        $defaultHourlyTimeOptions = $selectedProductMeta['draw_time_options'];
                                    endif;
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
                                                    <option
                                                        value="<?=$product['products_id'].'_____'.$product['title'];?>"
                                                        data-draw-options='<?=htmlspecialchars(json_encode(!empty($product['draw_time_options']) ? $product['draw_time_options'] : array()), ENT_QUOTES, "UTF-8");?>'
                                                        <?=$editProductsId === (int)$product['products_id'] ? 'selected' : '';?>>
                                                        <?=$product['title'];?>  
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

                                    <?php if(!empty($allproduct)  ): ?>
                                        <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('product_details')): ?>error<?php endif; ?>">
                                            <label>Hourly Game Time <span class="required">*</span></label>
                                            <select 
                                                name="hourly_game_time"
                                                id="hourly_game_time"
                                                class="hourly_game_time form-control required"
                                                data-selected-time="<?=$selectedHourlyDateTime;?>">
                                                <option value="">Select Hourly Game Time</option>
                                                <?php if(!empty($defaultHourlyTimeOptions)): ?>
                                                    <?php foreach($defaultHourlyTimeOptions as $hourlyOption): ?>
                                                        <?php if(strpos($hourlyOption, $date) !== 0) continue; ?>
                                                        <?php $hourlyOptionTime = date('H:i:s', strtotime($hourlyOption)); ?>
                                                        <option value="<?=$hourlyOption;?>" <?=$selectedHourlyDateTime === $hourlyOption ? 'selected' : '';?>><?=$hourlyOptionTime;?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>

                                            <?php if(form_error('hourly_game_time')): ?>
                                                <span for="hourly_game_time" generated="true" class="help-inline">
                                                <?php echo form_error('hourly_game_time'); ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>  
                                    <?php endif; ?>

                                    <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('draw_result')): ?>error<?php endif; ?>">
                                        <label>Draw Result<span class="required">*</span></label>
                                        <input type="text" name="draw_result" id="draw_result" class="form-control required" value="<?php if(set_value('draw_result')): echo set_value('draw_result'); else: echo !empty($EDITDATA['draw_result']) ? stripslashes($EDITDATA['draw_result']) : ''; endif; ?>">
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