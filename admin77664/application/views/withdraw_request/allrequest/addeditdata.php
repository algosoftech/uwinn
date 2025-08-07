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
                            <?php /* ?>
                            <h5 class="m-b-10">Welcome <?=sessionData('HCAP_ADMIN_FIRST_NAME')?></h5>
                            <?php */ ?>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('WITHDRAWALDATA',getCurrentControllerPath('index')); ?>"> Withdraw Request</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"> Request </a></li>
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
                        <h5> View Request</h5>
                        <a href="<?php echo correctLink('WITHDRAWALDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>
                    <div class="card-body">
                        <div class="basic-login-inner">
                            
                            <table class="table table-bordered w-50">
                                <tbody>
                                    <tr>
                                        <td><strong>Order ID:</strong></td>
                                        <td><strong>Amount </strong></td>
                                    </tr>

                                    <?php if(!empty($ALLDATA['orderData']->order_id)): 
                                        $loopCount = count($ALLDATA['orderData']->order_id);
                                        for ($i=0; $i<$loopCount; $i++): ?>
                                            <tr>
                                                <td><?=$ALLDATA['orderData']->order_id[$i];?></td>
                                                <td><?=$ALLDATA['orderData']->total_amount[$i];?></td>
                                            </tr>
                                         <?php endfor; ?>
                                            <tr>
                                                <td><strong>Total:</strong></td>
                                                <td><?=$ALLDATA['amount'];?></td>
                                            </tr>
                                    <?php endif; ?>
                                        
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
<script type="text/javascript">
$('#category_id').on('change',function(){
var category_id =  $(this).val();   
$.ajax({
url:FULLSITEURL+'products/allproducts/getsubcategoryData',
type:'post',
data:{category_id:category_id},
success:function(data){
$('#sub_category_data').html(data);
}
});
});

$(document).ready(function(){
var category_id =  $('#category_id').val();
var sub_category_id  =  '<?php echo $EDITDATA['sub_category_id']?>';
$.ajax({
url:FULLSITEURL+'products/allproducts/getsubcategoryData',
type:'post',
data:{category_id:category_id,sub_category_id:sub_category_id},
success:function(data){
$('#sub_category_data').html(data);
}
});
});
</script>
<script type="text/javascript">
  $(function(){create_editor_for_textarea('description')});
  $(function(){create_editor_for_textarea('image')});
</script>