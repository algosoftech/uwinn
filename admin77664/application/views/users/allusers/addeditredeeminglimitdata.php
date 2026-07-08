<div class="pcoded-main-container">
    <div class="pcoded-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="<?= getCurrentControllerPath('maindashboard'); ?>"><i class="feather icon-home"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="<?= getCurrentControllerPath('redeeminglimits'); ?>">Redeem Limits</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="javascript:void(0);">Configure Limit</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Add/Edit - Redeeming Limit</h5>
                        <a href="<?= getCurrentControllerPath('redeeminglimits'); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>
                    <div class="card-body">
                        <div class="basic-login-inner">
                            <?php
                            $edit = is_array($EDITDATA) ? $EDITDATA : array();
                            $defaultLimit = isset($defaultRedeemLimit) ? (float) $defaultRedeemLimit : 499;
							$currentLimit = (isset($edit['redeeming_amount_limit']) && $edit['redeeming_amount_limit'] !== '' && $edit['redeeming_amount_limit'] !== null)
                                ? (float) $edit['redeeming_amount_limit']
                                : $defaultLimit;
                            $currentMode = 'fixed';
                            if (set_value('redeem_limit_mode')) {
                                $currentMode = set_value('redeem_limit_mode');
                            } elseif (!empty($edit['redeem_limit_mode'])) {
                                $currentMode = strtolower((string) $edit['redeem_limit_mode']);
                            }
                            if ($currentMode !== 'global') {
                                $currentMode = 'fixed';
                            }
                            ?>
                            <div class="alert alert-light border mb-4">
                                <div class="row">
                                    <div class="col-md-3"><strong>User ID:</strong> <?php echo htmlspecialchars((string) ($edit['users_id'] ?? '')); ?></div>
                                    <div class="col-md-3"><strong>Name:</strong> <?php echo htmlspecialchars(stripslashes((string) ($edit['users_name'] ?? ''))); ?></div>
                                    <div class="col-md-3"><strong>Mobile:</strong> <?php echo htmlspecialchars((string) ($edit['users_mobile'] ?? '')); ?></div>
                                    <div class="col-md-3"><strong>Type:</strong> <?php echo htmlspecialchars((string) ($edit['users_type'] ?? '')); ?></div>
                                    <div class="col-md-3 mt-2"><strong>POS No.:</strong> <?php echo htmlspecialchars((string) ($edit['pos_number'] ?? '')); ?></div>
                                    <div class="col-md-3 mt-2"><strong>System Default:</strong> <?php echo number_format($defaultLimit, 2, '.', ''); ?> AED</div>
                                </div>
                            </div>
                            <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="users_id"/>
                                <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?php echo (int) ($edit['users_id'] ?? 0); ?>"/>
                                <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?php echo (int) ($edit['users_id'] ?? 0); ?>"/>
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                                <div class="row">
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if (form_error('redeeming_amount_limit')): ?>error<?php endif; ?>">
                                        <label>Redeeming Amount Limit <span class="required">*</span></label>
                                        <input type="number" step="0.01" min="0.01" name="redeeming_amount_limit" id="redeeming_amount_limit" class="form-control required" value="<?php if (set_value('redeeming_amount_limit')): echo set_value('redeeming_amount_limit'); else: echo htmlspecialchars((string) $currentLimit); endif; ?>" placeholder="Fixed redeem limit for this customer">
                                        <!-- <small class="text-muted">Prizes above this amount are treated as big wins and require special handling.</small> -->
                                        <?php if (form_error('redeeming_amount_limit')): ?>
                                        <span for="redeeming_amount_limit" generated="true" class="help-inline"><?php echo form_error('redeeming_amount_limit'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if (form_error('redeem_limit_mode')): ?>error<?php endif; ?>">
                                        <label>Redeem Limit Type <span class="required">*</span></label>
                                        <div class="mt-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="redeem_limit_mode" id="redeem_limit_mode_global" value="global" <?php echo $currentMode === 'global' ? 'checked="checked"' : ''; ?>>
                                                <label class="form-check-label" for="redeem_limit_mode_global">Global</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="redeem_limit_mode" id="redeem_limit_mode_fixed" value="fixed" <?php echo $currentMode === 'fixed' ? 'checked="checked"' : ''; ?>>
                                                <label class="form-check-label" for="redeem_limit_mode_fixed">Fixed</label>
                                            </div>
                                        </div>
                                        <small class="text-muted d-block mt-1">
                                            <strong>Global:</strong> If the limit is above 499, the same amount will remain after redeem.<br>
                                            <strong>Fixed:</strong> After redeem, the limit will reset to <?php echo number_format($defaultLimit, 2, '.', ''); ?> AED.
                                        </small>
                                        <?php if (form_error('redeem_limit_mode')): ?>
                                        <span for="redeem_limit_mode" generated="true" class="help-inline"><?php echo form_error('redeem_limit_mode'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="inline-remember-me mt-4">
                                            <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                            <button class="btn btn-primary mb-4 submit-btn">Submit</button>
                                            <a href="<?= getCurrentControllerPath('redeeminglimits'); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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
    </div>
</div>
