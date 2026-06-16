<?php
$editData = !empty($EDITDATA) ? json_decode(json_encode($EDITDATA), true) : array();
?>
<div class="pcoded-main-container">
    <div class="pcoded-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLDINGPRODUCTSDESCRIPTIONDATA', getCurrentControllerPath('index')); ?>">Ding</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Edit Product Description</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Edit Ding Product Description</h5>
                        <a href="<?php echo correctLink('ALLDINGPRODUCTSDESCRIPTIONDATA', getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>
                    <div class="card-body">
                        <fieldset>
                            <legend>Description Information</legend>
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                            <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" autocomplete="off">
                                <div class="row">
                                    <div class="form-group-inner col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                        <label>Localization Key</label>
                                        <p><strong><?php echo htmlspecialchars($editData['LocalizationKey'] ?? 'N/A'); ?></strong></p>
                                    </div>
                                    <div class="form-group-inner col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                        <label>Language Code <span class="required">*</span></label>
                                        <input type="text" name="LanguageCode" class="form-control required" value="<?php echo set_value('LanguageCode', $editData['LanguageCode'] ?? 'en'); ?>" required>
                                    </div>
                                    <div class="form-group-inner col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                        <label>Status <span class="required">*</span></label>
                                        <select name="status" class="form-control required" required>
                                            <option value="A" <?php echo (($editData['status'] ?? '') == 'A') ? 'selected' : ''; ?>>Active</option>
                                            <option value="I" <?php echo (($editData['status'] ?? '') == 'I') ? 'selected' : ''; ?>>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <label>Display Text <span class="required">*</span></label>
                                        <input type="text" name="DisplayText" class="form-control required" value="<?php echo set_value('DisplayText', $editData['DisplayText'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <label>Description Markdown</label>
                                        <textarea name="DescriptionMarkdown" class="form-control" rows="4"><?php echo set_value('DescriptionMarkdown', $editData['DescriptionMarkdown'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <label>Read More Markdown</label>
                                        <textarea name="ReadMoreMarkdown" class="form-control" rows="6"><?php echo set_value('ReadMoreMarkdown', $editData['ReadMoreMarkdown'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                <input type="hidden" name="SaveChanges" value="Yes">
                                <a href="<?php echo correctLink('ALLDINGPRODUCTSDESCRIPTIONDATA', getCurrentControllerPath('index')); ?>" class="btn btn-info has-ripple mt-2">Cancel</a>
                                <span class="tools mb-4" style="margin-left: auto;">Note:- <strong><span style="color:#FF0000;">*</span> Indicates Required Fields</strong></span>
                            </form>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
