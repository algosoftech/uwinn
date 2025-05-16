<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script>
$(function() {
    $("#date").datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        yearRange: "1970:<?php echo date('Y')?>"
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
                            <?php /* ?>
                            <h5 class="m-b-10">Welcome <?=sessionData('HCAP_ADMIN_FIRST_NAME')?></h5>
                            <?php */ ?>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a
                                    href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i
                                        class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a
                                    href="<?php echo correctLink('ALLPRODUCTSDATA',getCurrentControllerPath('index')); ?>">
                                    Currency Conversion</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?>
                                    Conversion</a></li>
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
                        <h5><?=$EDITDATA?'Edit':'Add'?> Conversion</h5>
                        <a href="<?php echo correctLink('ALLPRODUCTSDATA',getCurrentControllerPath('index')); ?>"
                            class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>
                    <div class="card-body">
                        <div class="basic-login-inner">
                            <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post"
                                action="" enctype="multipart/form-data">
                                <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique"
                                    value="seq_id" />
                                <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique"
                                    value="<?=$EDITDATA['seq_id']?>" />
                                <input type="hidden" name="CurrentDataID" id="CurrentDataID"
                                    value="<?=$EDITDATA['seq_id']?>" />
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>"
                                    value="<?php echo $this->security->get_csrf_hash();?>">

                                <div class="row">
                                    <div
                                        class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('category_id')): ?>error<?php endif; ?>">
                                        <label>Select Country<span class="required">*</span></label>
                                        <select name="country" id="country" class="form-control required">
                                            <option value='United Arab Emirates' <?=$EDITDATA['country']=='United Arab Emirates'?'selected':''?> >United Arab Emirates</option>
                                            <option value='Saudi Arabia' <?=$EDITDATA['country']=='Saudi Arabia'?'selected':''?> >Saudi Arabia</option>
                                            <option value='Qatar' <?=$EDITDATA['country']=='Qatar'?'selected':''?> >Qatar</option>
                                            <option value='Oman' <?=$EDITDATA['country']=='Oman'?'selected':''?> >Oman</option>
                                            <option value='Kuwait' <?=$EDITDATA['country']=='Kuwait'?'selected':''?> >Kuwait</option>
                                            <option value='Bahrain' <?=$EDITDATA['country']=='Bahrain'?'selected':''?> >Bahrain</option>
                                            <option value='India' <?=$EDITDATA['country']=='India'?'selected':''?> >India</option>
                                            <option value='Global' <?=$EDITDATA['country']=='Global'?'selected':''?> >Global</option>
                                        </select>
                                        <?php if(form_error('country')): ?>
                                        <span for="country" generated="true"
                                            class="help-inline"><?php echo form_error('country'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div
                                        class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('country_code')): ?>error<?php endif; ?>">
                                        <label>Country Code<span class="required">*</span></label>
                                        <input type='text' id="country_code" name="country_code" value='<?=stripslashes($EDITDATA['country_code'])?>' class="form-control required" >
                                        <?php if(form_error('country_code')): ?>
                                        <span for="country_code" generated="true"
                                            class="help-inline"><?php echo form_error('country_code'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div
                                        class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('phone_code')): ?>error<?php endif; ?>">
                                        <label>Phone Code<span class="required">*</span></label>
                                        <input type='text' id="phone_code" name="phone_code" value='<?=stripslashes($EDITDATA['phone_code'])?>' class="form-control required" >
                                        <?php if(form_error('phone_code')): ?>
                                        <span for="phone_code" generated="true"
                                            class="help-inline"><?php echo form_error('phone_code'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div
                                        class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('currency')): ?>error<?php endif; ?>">
                                        <label>Currency<span class="required">*</span></label>
                                        <input type='text' id="currency" name="currency" value='<?=stripslashes($EDITDATA['currency'])?>' class="form-control required" >
                                        <?php if(form_error('currency')): ?>
                                        <span for="currency" generated="true"
                                            class="help-inline"><?php echo form_error('currency'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div
                                        class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('currency_symbol')): ?>error<?php endif; ?>">
                                        <label>Currency Symbol<span class="required">*</span></label>
                                        <input type='text' id="currency_symbol" name="currency_symbol" value='<?=stripslashes($EDITDATA['currency_symbol'])?>' class="form-control required" >
                                        <?php if(form_error('currency_symbol')): ?>
                                        <span for="currency_symbol" generated="true"
                                            class="help-inline"><?php echo form_error('currency_symbol'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div
                                        class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('conversion_rate')): ?>error<?php endif; ?>">
                                        <label>Conversion<span class="required">*</span></label>
                                        <input id="conversion_rate" type='number' name="conversion_rate" value='<?=stripslashes($EDITDATA['conversion_rate'])?>' class="form-control required" >
                                        <?php if(form_error('conversion_rate')): ?>
                                            <span for="conversion_rate" generated="true"
                                            class="help-inline"><?php echo form_error('conversion_rate'); ?></span>
                                        <?php endif; ?>
                                        <label>1 AED = <span class="conversion_rate_span"><?=$EDITDATA['conversion_rate']?$EDITDATA['conversion_rate'].' '.$EDITDATA['currency_symbol']:'___'?></span></label>
                                    </div>

                                    <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('image')): ?>error<?php endif; ?>">
                                        <label>Flag Image</label><br>
                                        <input type="file" name="image" id="image" class="" value="<?php if(set_value('image')): echo set_value('image'); endif; ?>" accept="image/png, image/jpeg">
                                        <p style="font-family:italic; color:red;">[Image Size : 241 x 136 px in jpg/jpeg/png]</p>
                                        <?php if($EDITDATA['image']): ?>
                                        <div id="ImageDiv2">
                                            <img src="<?php echo fileBaseUrl.$EDITDATA['image']; ?>" width="50" border="0" alt="">
                                            &nbsp;
                                        </div>
                                        <?php endif; ?>
                                        <?php if(form_error('image')): ?>
                                        <span for="image" generated="true" class="help-inline"><?php echo form_error('image'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('time_zone')): ?>error<?php endif; ?>" id="time_zone_block">
                                        <label>Time Zone<span class="required">*</span></label>
                                        <input type="text" name="time_zone" id="time_zone" class="form-control required" value="<?php if(set_value('time_zone')): echo set_value('time_zone'); else: echo stripslashes($EDITDATA['time_zone']);endif; ?>" placeholder="Time Zone">
                                        <?php if(form_error('time_zone')): ?>
                                        <span for="name" generated="true" class="help-inline"><?php echo form_error('time_zone'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="inline-remember-me mt-4">
                                            <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                            <button class="btn btn-primary mb-4">Submit</button>
                                            <a href="<?php echo correctLink('ALLPRODUCTSDATA',getCurrentControllerPath('index')); ?>"
                                                class="btn btn-danger has-ripple mb-4">Cancel</a>
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
        $('#country').on('change', function() {
            var country = $(this).val();
            // alert(country);
        });
        $('#conversion_rate').on('keyup', function() {
            var val = $(this).val();
            if(val > 0){
                $('.conversion_rate_span').empty();
                $('.conversion_rate_span').append(parseInt(val));
            }else{
                $('.conversion_rate_span').empty();
                $('.conversion_rate_span').append('__');
            }
        });
    })

</script>