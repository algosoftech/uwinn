  
<div class="pcoded-main-container">
    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <?php /* ?><h5 class="m-b-10">Welcome <?=sessionData('HCAP_ADMIN_FIRST_NAME')?></h5><?php */ ?>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLORDERSDATA',getCurrentControllerPath('index')); ?>"> Manage Orders</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">View Order Details</a></li>
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
                <h5>View Order Details</h5>
                <a href="<?php echo correctLink('ALLORDERSDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
              </div>
              <div class="card-body">
                <div class="basic-login-inner">
                  <div class="users_form user-cart_1 ">
                    <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                      <input type="hidden" name="order_id" id="order_id" value="<?= isset($orderData['_id']->{'$id'}) ? $orderData['_id']->{'$id'} : (isset($orderData['_id']['$id']) ? $orderData['_id']['$id'] : ''); ?>" />
                    <div class="row">
                      <div class="col-sm-4 col-md-4 col-lg-4">
                        
                        <!-- Send Order Confirmation SMS / WhatsApp -->
                        <p class="text-muted small">Select an SMS gateway and send order confirmation to retailer via SMS or WhatsApp.</p>
                            <div class="form-group  mr-3 mb-2">
                                <label for="sms_gateway" class="mr-2">SMS Gateway</label>
                                <select name="gateway" id="sms_gateway"method="post" class="form-control" required>
                                    <option value="">-- Select Gateway --</option>
                                    <?php if (!empty($gateways)):
                                        foreach ($gateways as $key => $label): ?>
                                            <option value="<?= htmlspecialchars($key); ?>"><?= htmlspecialchars($label); ?></option>
                                    <?php endforeach;
                                    endif; ?>
                                </select>
                            </div>
                      </div>

                      <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="inline-remember-me mt-4">
                          <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                          <button class="btn btn-primary mb-4">Submit</button>
                          <a href="<?php echo correctLink('ALLORDERSDATA',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
                          <span class="tools pull-right">Note:- <strong><span style="color:#FF0000;">*</span> Indicates Required Fields</strong> </span> 
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
          </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
 