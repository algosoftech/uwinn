<?php
$editData = isset($EDITDATA) ? json_decode(json_encode($EDITDATA), true) : array();
$providerCodeValue = $editData['ProviderCode'] ?? '';
$isEdit = !empty($providerCodeValue);
$paymentTypes = isset($editData['PaymentTypes']) && is_array($editData['PaymentTypes']) ? $editData['PaymentTypes'] : array();
?>
<div class="pcoded-main-container">
    <div class="pcoded-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('DINGRECHARGEDATA',getCurrentControllerPath('index')); ?>">Ding</a></li>
                            <li class="breadcrumb-item"><?php echo $isEdit ? 'Edit' : 'Add'; ?> Ding Provider</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="overflow-x: hidden;">
            <div class="col-sm-12">
                <div class="card" style="overflow: visible;">
                    <div class="card-header">
                        <h5><?php echo $isEdit ? 'Edit' : 'Add'; ?> Ding Provider</h5>
                        <a href="<?php echo correctLink('DINGRECHARGEDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>

                    <div class="card-body">
                        <form id="providerForm" name="providerForm" method="post" action="" autocomplete="off" enctype="multipart/form-data">
                            <div class="row">
                                <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>Provider Code <span class="required">*</span></label>
                                    <input type="text"
                                           name="ProviderCode"
                                           id="ProviderCode"
                                           class="form-control required"
                                           placeholder="ProviderCode"
                                           value="<?php echo htmlspecialchars($providerCodeValue); ?>"
                                           <?php echo $isEdit ? 'readonly' : ''; ?>>
                                </div>

                                <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>Country ISO <span class="required">*</span></label>
                                    <input type="text"
                                           name="CountryIso"
                                           id="CountryIso"
                                           class="form-control required"
                                           placeholder="e.g. PR"
                                           value="<?php echo htmlspecialchars($editData['CountryIso'] ?? ''); ?>">
                                </div>

                                <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label>Provider Name <span class="required">*</span></label>
                                    <input type="text"
                                           name="Name"
                                           id="Name"
                                           class="form-control required"
                                           placeholder="Provider name"
                                           value="<?php echo htmlspecialchars($editData['Name'] ?? ''); ?>">
                                </div>

                                <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <label>Validation Regex <span class="required">*</span></label>
                                    <input type="text"
                                           name="ValidationRegex"
                                           id="ValidationRegex"
                                           class="form-control required"
                                           placeholder="Regex (used to validate MSISDN/PIN)"
                                           value="<?php echo htmlspecialchars($editData['ValidationRegex'] ?? ''); ?>">
                                </div>

                                <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>Customer Care Number</label>
                                    <input type="text"
                                           name="CustomerCareNumber"
                                           id="CustomerCareNumber"
                                           class="form-control"
                                           placeholder="+1(800)..."
                                           value="<?php echo htmlspecialchars($editData['CustomerCareNumber'] ?? ''); ?>">
                                </div>

                                <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>Logo URL</label>
                                    <input type="text"
                                           name="LogoUrl"
                                           id="LogoUrl"
                                           class="form-control"
                                           placeholder="https://..."
                                           value="<?php echo htmlspecialchars($editData['LogoUrl'] ?? ''); ?>">
                                </div>

                                <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>Region Codes (comma separated)</label>
                                    <input type="text"
                                           name="RegionCodes"
                                           id="RegionCodes"
                                           class="form-control"
                                           placeholder="e.g. PR"
                                           value="<?php echo isset($editData['RegionCodes']) && is_array($editData['RegionCodes']) ? htmlspecialchars(implode(',', $editData['RegionCodes'])) : ''; ?>">
                                </div>

                                <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>Payment Types</label>
                                    <div class="mt-2">
                                        <label class="mr-3">
                                            <input type="checkbox" name="PaymentTypes[]" value="Prepaid" <?php echo in_array('Prepaid', $paymentTypes) ? 'checked' : ''; ?>>
                                            Prepaid
                                        </label>
                                        <label>
                                            <input type="checkbox" name="PaymentTypes[]" value="Postpaid" <?php echo in_array('Postpaid', $paymentTypes) ? 'checked' : ''; ?>>
                                            Postpaid
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                                <a href="<?php echo correctLink('DINGRECHARGEDATA',getCurrentControllerPath('index')); ?>" class="btn btn-info has-ripple ml-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

