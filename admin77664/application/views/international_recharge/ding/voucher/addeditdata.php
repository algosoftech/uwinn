<?php
$editData = !empty($EDITDATA) ? json_decode(json_encode($EDITDATA), true) : array();
$isEdit = !empty($editData);
$selectedProviders = array();
if (!empty($editData['provider_codes']) && is_array($editData['provider_codes'])) {
    $selectedProviders = $editData['provider_codes'];
} elseif (!empty($editData['provider_code'])) {
    $selectedProviders = array($editData['provider_code']);
}
?>
<div class="pcoded-main-container">
    <div class="pcoded-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentControllerPath('index'); ?>">Voucher</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?php echo $isEdit ? 'Edit' : 'Add'; ?> Voucher</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5><?php echo $isEdit ? 'Edit' : 'Add'; ?> Voucher</h5>
                        <a href="<?php echo getCurrentControllerPath('index'); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <form method="post" action="" enctype="multipart/form-data" autocomplete="off">
                            <div class="row">
                                <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>Voucher Name <span class="required">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control required" value="<?php echo htmlspecialchars($editData['name'] ?? ''); ?>" placeholder="Enter voucher name">
                                </div>

                                <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>Providers <span class="required">*</span></label>
                                    <select name="provider_codes[]" id="provider_codes" class="form-control required select-search" multiple>
                                        <?php
                                        if (!empty($PROVIDERS)):
                                            foreach ($PROVIDERS as $code => $name):
                                        ?>
                                                <option value="<?php echo htmlspecialchars($code); ?>" <?php if (in_array($code, $selectedProviders)) echo 'selected="selected"'; ?>>
                                                    <?php echo htmlspecialchars($name . ' (' . $code . ')'); ?>
                                                </option>
                                        <?php
                                            endforeach;
                                        endif;
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>Message <span class="required">*</span></label>
                                    <textarea name="message" id="message" class="form-control required" rows="3" placeholder="Enter voucher message"><?php echo htmlspecialchars($editData['message'] ?? ''); ?></textarea>
                                </div>

                                <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <label>Image</label>
                                    <input type="file" name="image" id="image" class="form-control" accept="image/*">
                                    <?php if (!empty($editData['image'])): ?>
                                        <div class="mt-2">
                                            <?php
                                            $previewImg = trim((string)$editData['image']);
                                            $previewFallback = '';
                                            if (!preg_match('/^https?:\/\//i', $previewImg)) {
                                                $rootBase = rtrim(base_url(), '/') . '/';
                                                $publicBase = str_replace('/admin77664/', '/', $rootBase);
                                                $previewFallback = $publicBase . ltrim($previewImg, '/');
                                                $previewImg = $rootBase . ltrim($previewImg, '/');
                                            }
                                            ?>
                                            <img src="<?php echo htmlspecialchars($previewImg); ?>" alt="voucher-image" width="80" height="80" onerror="this.onerror=null;<?php if ($previewFallback !== ''): ?>this.src='<?php echo htmlspecialchars($previewFallback); ?>';this.onerror=function(){this.outerHTML='N/A';};<?php else: ?>this.outerHTML='N/A';<?php endif; ?>">
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group-inner col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                    <label>Status <span class="required">*</span></label>
                                    <select name="status" id="status" class="form-control required">
                                        <option value="A" <?php if (($editData['status'] ?? 'A') == 'A') echo 'selected="selected"'; ?>>Active</option>
                                        <option value="I" <?php if (($editData['status'] ?? '') == 'I') echo 'selected="selected"'; ?>>Inactive</option>
                                    </select>
                                </div>

                                <div class="form-group-inner col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                    <label>Sequence Order <span class="required">*</span></label>
                                    <input type="number" min="0" name="sequence_order" id="sequence_order" class="form-control required" value="<?php echo (int)($editData['sequence_order'] ?? 0); ?>" placeholder="0">
                                </div>
                            </div>

                            <div class="mt-3">
                                <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="<?php echo getCurrentControllerPath('index'); ?>" class="btn btn-danger has-ripple">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<link href="{ASSET_INCLUDE_URL}dist/css/fSelect.css" rel="stylesheet">
<script src="{ASSET_INCLUDE_URL}dist/js/fSelect.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('#provider_codes').fSelect({
        placeholder: 'Select Providers',
        numDisplayed: 3,
        overflowText: '{n} selected',
        searchText: 'Search',
        noResultsText: 'No results found',
        showSearch: true
    });
});
</script>
