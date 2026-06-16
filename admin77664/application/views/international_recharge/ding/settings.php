<style>
  .btn-danger{
   
    position: absolute;
    right: 0px;
    transform: translate(-20px, 0px);

  }
  .has-ripple{
    transform: unset !important;
  }
   .flex-view{
    display: flex;
    align-items: center;
    gap: 8px;
   }
   .flex-view .form-control {
    flex: 1;
   }
   .flex-view .btn-danger {
    min-width: 35px;
    padding: 5px 8px;
    flex-shrink: 0;
   }
   
  </style>
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
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('DINGRECHARGEDATA',getCurrentControllerPath('index')); ?>"> Ding</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Settings</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        <div class="row" style="overflow-x: hidden;">
            <div class="col-sm-12">
                <div class="card" style="overflow: visible;">
                    <div class="card-header">
                        <h5><?=$EDITDATA?'Edit':'Add'?> Ding Settings</h5>
                        <a href="<?php echo correctLink('ALLTOMBOLAGAMEDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>
                    <div class="card-body" style="">
                        <fieldset>   
                            <legend>Ding Settings</legend>
                            <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="form-group-inner col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                        <label for="country_list">Api Key <span class="required">*</span></label>
                                        <input type="text" name="api_key" id="api_key" class="form-control required" placeholder="Enter API KEY" value="<?php echo $EDITDATA['api_key']; ?>">
                                        <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                        <button class="btn btn-primary mt-2 submit-btn">Submit</button>
                                        <a href="<?php echo correctLink('DINGRECHARGEDATA',getCurrentControllerPath('index')); ?>" class="btn btn-info has-ripple mt-2">Cancel</a>  
                                        <span class="tools mb-4" style="margin-left: auto;">Note:- <strong><span style="color:#FF0000;">*</span> Indicates Required Fields</strong></span> 
                                    </div>
                                    <div class="form-group-inner col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                        <label for="markup_commission_percentage">Markup Commission Percentage <span class="required">*</span></label>
                                        <input type="text" name="markup_commission_percentage" id="markup_commission_percentage" class="form-control required" placeholder="Enter Markup Commission Percentage" value="<?php echo $EDITDATA['markup_commission_percentage']; ?>">
                                        <span class="tools mb-4" style="margin-left: auto;"> <strong><span style="color:#FF0000;">*</span> Please enter the markup commission percentage</strong></span> 
                                    </div>

                                    <div class="form-group-inner col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                        <label for="api_mode"> API Mode (Test/Live) <span class="required">*</span></label>
                                        <select name="api_mode" id="api_mode" class="form-control required">
                                            <option value="test" <?php echo $EDITDATA['api_mode'] == 'test' ? 'selected' : ''; ?>>Test</option>
                                            <option value="live" <?php echo $EDITDATA['api_mode'] == 'live' ? 'selected' : ''; ?>>Live</option>
                                        </select>
                                        <span class="tools mb-4" style="margin-left: auto;"> <strong><span style="color:#FF0000;">*
                                        </span> Please select the API Mode</strong></span> 
                                    </div>
                                </div>
                            </form>
                                
                            <div class="row">
                                <div class="form-group-inner col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                    <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                        <fieldset>   
                                            <legend>Sync Currency List</legend>
                                            <input type="hidden" name="SaveChanges" id="data-type" value="currency_list">
                                            <button class="btn btn-primary mt-2 submit-btn">Sync</button>
                                        </fieldset>
                                    </form>
                                </div>
                                <div class="form-group-inner col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                    <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                        <fieldset>   
                                            <legend>Sync Country Code</legend>
                                            <input type="hidden" name="SaveChanges" id="data-type" value="country_code">
                                            <button class="btn btn-primary mt-2 submit-btn">Sync</button>
                                        </fieldset>
                                    </form>
                                </div>

                                <div class="form-group-inner col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                    <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                        <fieldset>   
                                            <legend>Sync Regions List</legend>
                                            <input type="hidden" name="SaveChanges" id="data-type" value="region_list">
                                            <button class="btn btn-primary mt-2 submit-btn">Sync</button>
                                        </fieldset>
                                    </form>
                                </div>




                                <div class="form-group-inner col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                    <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                        <fieldset>   
                                            <legend>Sync Providers List</legend>
                                            <input type="hidden" name="SaveChanges" id="data-type" value="provider_list">
                                            <button class="btn btn-primary submit-btn mt-2">Sync</button>
                                        </fieldset>
                                    </form>
                                </div>

                                <div class="form-group-inner col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                    <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                        <fieldset>   
                                            <legend>Sync Products List</legend>
                                            <input type="hidden" name="SaveChanges" id="data-type" value="products_list">
                                            <button class="btn btn-primary submit-btn mt-2">Sync</button>
                                        </fieldset>
                                    </form>
                                </div>

                                <div class="form-group-inner col-lg-4 col-md-6 col-sm-12 col-xs-12">
                                    <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                        <fieldset>   
                                            <legend>Sync Product Description List</legend>
                                            <input type="hidden" name="SaveChanges" id="data-type" value="product_description_list">
                                            <button class="btn btn-primary submit-btn mt-2">Sync</button>
                                        </fieldset>
                                    </form>
                                </div>
                                
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        <!-- [ Main Content ] end -->
        </div>
    </div>
</div>


<link href="{ASSET_INCLUDE_URL}dist/css/fSelect.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="{ASSET_INCLUDE_URL}dist/js/fSelect.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
 
