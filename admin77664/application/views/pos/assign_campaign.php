
<style type="text/css">
        .camoaign-details img {
            margin-right: 10px;
        }

        .campaign-section {
            display: flex;
            padding: 10px;
            box-shadow: rgb(60 64 67 / 24%) 0px 1px 5px 0px, rgb(60 64 67 / 2%) 0px 1px 5px 1px;
            border-radius: 8px;
            min-height: 100%;
            min-width: 100%;
        }

</style>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script>
$(function(){
   $("#date").datepicker({dateFormat:'yy-mm-dd',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
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
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLSALESDATA',getCurrentControllerPath('index')); ?>"> Pos</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA['assign_campaihn']?'Edit Assigned':'Assign'?> Campain </a></li>
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
                        <!-- <h5><?=$EDITDATA['selected_campaign_list']?'Edit Assigned':'Assign'?>  Campaign</h5> -->
                        <h5>  All Campaign List</h5>
                        <a href="<?php echo correctLink('ALLSALESDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>
                    <div class="card-body">
                        <div class="basic-login-inner">
                            <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="users_id"/>
                                <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['users_id']?>"/>
                                <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['users_id']?>"/>
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                                    <div class="campaign-container"> 
                                        <div class="row">
                                            <?php foreach ($productlist as $key => $items):?>
                                             <div class="col-lg-4 col-md-6 col-sm-6 form-group-inner">
                                                <div class="campaign-section">
                                                    <div class="camoaign-details">
                                                            <img src="<?php echo fileBaseUrl.$items['product_image']; ?>" width="100" border="0" alt="">
                                                    </div>
                                                    <div class="camoaign-details">
                                                       <p> <b> product Name : </b> <?=$items['title'];?> </p> 
                                                       <p> <b> Draw Date : </b>    <?=$items['draw_date'];?> </p>
                                                       <p> 
                                                        <div class="form-check">
                                                          <input class="form-check-input" type="checkbox" name="selected_campaign_list[]" value="<?=$items['products_id'];?>" id="selected_campaign_list<?=$key;?>"
                                                           <?php if($EDITDATA['selected_campaign_list']):  if(in_array($items['products_id'],$EDITDATA['selected_campaign_list'])): echo "checked"; endif; endif; ?>>
                                                          <label class="form-check-label" for="selected_campaign_list<?=$key;?>">Assign</label>
                                                        </div>
                                                    </div>
                                                </div>
                                             </div>
                                            <?php endforeach;?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="inline-remember-me mt-4">
                                                <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                                <button class="btn btn-primary mb-4 submit-btn">Submit</button>
                                                <a href="<?php echo correctLink('ALLPOSUSERSDATA',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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


 