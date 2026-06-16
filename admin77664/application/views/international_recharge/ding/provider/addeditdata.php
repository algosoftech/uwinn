<?php
$editData = !empty($EDITDATA) ? json_decode(json_encode($EDITDATA), true) : array();
$regionCodes = !empty($editData['RegionCodes']) && is_array($editData['RegionCodes']) ? $editData['RegionCodes'] : array();
$paymentTypes = !empty($editData['PaymentTypes']) && is_array($editData['PaymentTypes']) ? $editData['PaymentTypes'] : array();
?>
<div class="pcoded-main-container">
    <div class="pcoded-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLDINGPROVIDERSDATA', getCurrentControllerPath('index')); ?>">Ding</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Edit Provider</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Edit Ding Provider</h5>
                        <a href="<?php echo correctLink('ALLDINGPROVIDERSDATA', getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>
                    <div class="card-body">
                        <fieldset>
                            <legend>Provider Information</legend>
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" autocomplete="off">
                                <input type="hidden" name="Action" value="Edit">
                                <div class="row">
                                    <div class="form-group-inner col-lg-3 col-md-12 col-sm-12 col-xs-12">
                                        <img src="<?php echo $editData['LogoUrl'];?>" alt="Logo" width="100" height="100">
                                        <h5><?=$editData['Name']??'N/A';?> (<?=$editData['CountryIso']??'N/A';?>)</h5>
                                        <p></p>
                                    </div>
                                </div>
                                <div class="row">

                                    <div class="form-group-inner col-lg-6 col-md-4 col-sm-12 col-xs-12">
                                        <label>Markup Commission <span class="required">*</span></label>
                                        <input type="text" name="markup_commission" class="form-control required" value="<?=$editData['markup_commission']??'0';?>" placeholder="Enter Markup Commission" required>
                                    </div>
                                    <div class="form-group-inner col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                        <label>Agent Commission <span class="required">*</span></label>
                                        <input type="text" name="agent_commission" class="form-control required" value="<?=$editData['agent_commission']??'0';?>" placeholder="Enter Agent Commission" required>
                                    </div>
                                </div>
                                <input type="hidden" name="SaveChanges" id="data-type" value="Yes">
                                <button class="btn btn-primary mt-2 submit-btn">Submit</button>
                                <a href="<?php echo correctLink('ALLDINGPROVIDERSDATA', getCurrentControllerPath('index')); ?>" class="btn btn-info has-ripple mt-2">Cancel</a>
                                <span class="tools mb-4" style="margin-left: auto;">Note:- <strong><span style="color:#FF0000;">*</span> Indicates Required Fields</strong></span>
                            </form>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
