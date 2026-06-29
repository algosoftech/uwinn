<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>
<script>
$(function(){
    var drawTimeOpts = {
        datepicker: false,
        timepicker: true,
        format: 'H:i:s',
        formatTime: 'H:i:s',
        step: 1,
        mask: false,
        validateOnBlur: false
    };
    $("#draw_time_start").datetimepicker(drawTimeOpts);
    $("#draw_time_end").datetimepicker(drawTimeOpts);
});
</script>
<div class="pcoded-main-container">
    <div class="pcoded-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?=base_url('/'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?=correctLink('ALLHOURLYGAMEDATA', getCurrentControllerPath('index')); ?>">Hourly Games</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Manage Draw Time</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Manage Draw Time</h5>
                        <a href="<?=correctLink('ALLHOURLYGAMEDATA', getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>
                    <div class="card-body">
                        <div class="basic-login-inner">
                            <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="">
                                <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>">
                                <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=!empty($EDITDATA['_id']) ? $EDITDATA['_id']->{'$id'} : '';?>">

                                <div class="row">
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php if(form_error('draw_time_start')): ?>error<?php endif; ?>">
                                        <label>Draw Time Start <span class="required">*</span></label>
                                        <input type="text" name="draw_time_start" id="draw_time_start" class="form-control required"
                                            value="<?php
                                                $drawTimeStart = set_value('draw_time_start') ?: stripslashes($EDITDATA['draw_time_start'] ?? '');
                                                if($drawTimeStart && preg_match('/^\d{2}:\d{2}$/', $drawTimeStart)) { $drawTimeStart .= ':00'; }
                                                echo $drawTimeStart;
                                            ?>"
                                            placeholder="HH:mm:ss">
                                        <?php if(form_error('draw_time_start')): ?>
                                            <span for="draw_time_start" generated="true" class="help-inline"><?=form_error('draw_time_start');?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php if(form_error('draw_time_end')): ?>error<?php endif; ?>">
                                        <label>Draw Time End <span class="required">*</span></label>
                                        <input type="text" name="draw_time_end" id="draw_time_end" class="form-control required"
                                            value="<?php
                                                $drawTimeEnd = set_value('draw_time_end') ?: stripslashes($EDITDATA['draw_time_end'] ?? '');
                                                if($drawTimeEnd && preg_match('/^\d{2}:\d{2}$/', $drawTimeEnd)) { $drawTimeEnd .= ':00'; }
                                                echo $drawTimeEnd;
                                            ?>"
                                            placeholder="HH:mm:ss">
                                        <?php if(form_error('draw_time_end')): ?>
                                            <span for="draw_time_end" generated="true" class="help-inline"><?=form_error('draw_time_end');?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="inline-remember-me mt-2 mb-4">
                                            <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                            <button class="btn btn-primary mb-2"><?=!empty($EDITDATA) ? 'Update' : 'Save'?></button>
                                            <span class="tools pull-right">Note:- <strong><span style="color:#FF0000;">*</span> Indicates Required Fields</strong></span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>24 Hours Campaigns</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered nowrap dataTable">
                                <thead style="text-align: center;">
                                    <tr>
                                        <th width="5%">S.No.</th>
                                        <th width="10%">Product ID</th>
                                        <th width="30%">Campaign Name</th>
                                        <th width="20%">Draw Start Date</th>
                                        <th width="20%">Draw Expiry Date</th>
                                        <th width="15%">Status</th>
                                    </tr>
                                </thead>
                                <tbody style="text-align: center;">
                                    <?php if(!empty($CAMPAIGNS24H)): $i = 1; foreach($CAMPAIGNS24H as $campaign): ?>
                                        <tr>
                                            <td><?=$i++?></td>
                                            <td><?=$campaign['products_id'] ?? ''?></td>
                                            <td><?=stripslashes($campaign['title'] ?? '')?></td>
                                            <td><?=!empty($campaign['start_date']) ? date('d F Y H:i', $campaign['start_date']) : '-'?></td>
                                            <td><?=!empty($campaign['expiry_date']) ? date('d F Y H:i', $campaign['expiry_date']) : '-'?></td>
                                            <td><?=showStatus($campaign['status'] ?? 'A')?></td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                        <tr>
                                            <td colspan="6" style="text-align:center;">No 24 hours campaigns found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
