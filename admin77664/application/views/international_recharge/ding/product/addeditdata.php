<?php
$editData = !empty($EDITDATA) ? json_decode(json_encode($EDITDATA), true) : array();
$paymentTypes = !empty($editData['PaymentTypes']) && is_array($editData['PaymentTypes']) ? implode(', ', $editData['PaymentTypes']) : 'N/A';
$benefits = !empty($editData['Benefits']) && is_array($editData['Benefits']) ? implode(', ', $editData['Benefits']) : 'N/A';
?>
<div class="pcoded-main-container">
    <div class="pcoded-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLDINGPRODUCTSDATA', getCurrentControllerPath('index')); ?>">Ding</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Edit Product</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Edit Ding Product</h5>
                        <a href="<?php echo correctLink('ALLDINGPRODUCTSDATA', getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>
                    <div class="card-body">
                        <fieldset>
                            <legend>Product Information</legend>
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" autocomplete="off">
                                <input type="hidden" name="Action" value="Edit">
                                <div class="row">
                                    <div class="form-group-inner col-lg-3 col-md-12 col-sm-12 col-xs-12">
                                        <label>Provider Code</label>
                                        <p><strong><?php echo $editData['ProviderCode'] ?? 'N/A'; ?></strong></p>
                                    </div>
                                    <div class="form-group-inner col-lg-3 col-md-12 col-sm-12 col-xs-12">
                                        <label>SKU Code</label>
                                        <p><strong><?php echo $editData['SkuCode'] ?? 'N/A'; ?></strong></p>
                                    </div>
                                    <div class="form-group-inner col-lg-3 col-md-12 col-sm-12 col-xs-12">
                                        <label>Region Code</label>
                                        <p><strong><?php echo $editData['RegionCode'] ?? 'N/A'; ?></strong></p>
                                    </div>
                                    <div class="form-group-inner col-lg-3 col-md-12 col-sm-12 col-xs-12">
                                        <label>Status</label>
                                        <p><strong><?php echo showStatus($editData['status'] ?? 'I'); ?></strong></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group-inner col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                        <label>Display Text</label>
                                        <p><strong><?php echo $editData['DefaultDisplayText'] ?? 'N/A'; ?></strong></p>
                                    </div>
                                    <div class="form-group-inner col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                        <label>Payment Types / Benefits</label>
                                        <p><strong><?php echo $paymentTypes; ?> / <?php echo $benefits; ?></strong></p>
                                    </div>
                                </div>
                            </form>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
