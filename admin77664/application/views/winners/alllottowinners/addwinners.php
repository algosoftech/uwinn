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
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLLOTTOWINNERSDATA',getCurrentControllerPath('index')); ?>"> Winners</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Winners</a></li>
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
                    <h5><?=$EDITDATA?'Edit':'Add'?> Winners</h5>
                    <a href="<?php echo correctLink('ALLLOTTOWINNERSDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                  </div>
                     <div class="card-body">
                        <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                            <div class="basic-login-inner">
                                <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="winners_id"/>
                                <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['winners_id']?>"/>
                                <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['winners_id']?>"/>
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                            </div>

                            <div class="row">
                                 <div class="col-sm-12">
                                  
                                    <div class="table-responsive">
                                        <h3 class="">

                                        <?php
                                            if($macthedtype == 'straight'):
                                                echo "Straight";
                                            elseif($macthedtype == 'rumblemix'):
                                                echo "Rumble Mix";
                                            elseif($macthedtype == 'reverse'):
                                                echo "Reverse";
                                            endif;
                                        ?>
                                         Coupon List</h3>
                                        <table id="simpletable" class="table table-striped table-bordered nowrap dataTable" role="grid" aria-describedby="simpletable_info">
                                            <thead style="text-align: center;">
                                                  <tr role="row">
                                                    <th width="5%" style="text-align: center;">S.No.</th>
                                                    <th width="20%">Matches</th>
                                                    <th width="20%">Order ID</th>
                                                    <th width="20%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody style="text-align: center;">
                                                <?php if($matchedCoupons <> ""): $j=1; foreach($matchedCoupons as  $ALLDATAINFO): 
                                                    if($ALLDATAINFO['matched_count'] == $matchedLottoType ):
                                                    if($j%2==0): $rowClass = 'odd'; else: $rowClass = 'even'; endif;
                                                ?>

                                                <tr role="row" class="<?php echo $rowClass; ?>">
                                                  <td style="text-align: center;"><?=$j++?></td>

                                                  <td width="5%"><?='Match of '.$matchedLottoType.'/'.$lotto_type; ?></td>
                                                  <td width="5%"><?= $ALLDATAINFO['orderID']; ?></td>
                                                  <td width="5%"> 
                                                        <a class="btn btn-primary"  href="<?php echo getCurrentControllerPath('winstatus/'.$macthedtype.'/'.$ALLDATAINFO['orderID'].'/'.$matchedLottoType.'/approve')?>" onClick="return confirm('Do you want to Approve?');">
                                                            <i class="fas fa-thumbs-up"></i> Approve
                                                        </a>
                                                        <a class="btn btn-secondary" href="<?php echo getCurrentControllerPath('winstatus/'.$macthedtype.'/'.$ALLDATAINFO['orderID'].'/'.$matchedLottoType.'/reject')?>" onClick="return confirm('Do you want to Reject');">
                                                            <i class="fas fa-thumbs-down"></i> Reject
                                                        </a>
                                                  </td>
                                                 
                                                  <!-- <td><?=date('d-F-Y h:i A',strtotime($ALLDATAINFO['created_at']))?></td> -->
                                                  
                                                </tr>
                                                <?php $j++; endif;  endforeach; else: ?>
                                                <tr>
                                                  <td colspan="6" style="text-align:center;">No Data Available In Table</td>
                                                </tr>
                                                <?php endif; ?>
                                              </tbody>
                                        </table>
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

<script type="text/javascript">
  $(function(){create_editor_for_textarea('description')});
  $(function(){create_editor_for_textarea('image')});
</script>

<script type="text/javascript">
$(document).ready(function(){
$('#coupon').change(function(){
var coupon = $(this).val();
var ur      = '<?=base_url().'winners/allwinners/checkDeplicacy'?>';
$.ajax({
url : ur,
method: "POST", 
data: {coupon: coupon},
success: function(data1){
var data2 = data1.split("__");
$('#couponA').empty().append(data2[0]);
$('#coupon_id').val(data2[1]);
$('#adepoints').val(data2[2]);
$('#users_id').val(data2[3]);

    console.log(data2);
  

    if(data2[4] == 'Cash'){

        $(".winner-position-section").removeClass('d-none');

        $('.prize_type').find("option").remove().end();
        

        if ( data2[8] =="first" || data2[9] =="first" || data2[10] =="first" ){
            
            if(data2[5] !='' && data2[5] > 0 ){
                $('.prize_type').append('<option value="first" disabled > Prize 1</option>');
            }

        }else{

            if(data2[5] !='' && data2[5] > 0 ){
                $('.prize_type').append('<option value="first" > Prize 1</option>');
            }

        }
            

         if( data2[8] =="second" || data2[9] =="second" || data2[10] =="second" ){
            
            if(data2[6] !='' && data2[6] > 0){
                $('.prize_type').append(`<option value="second" disabled  > Prize 2</option>`);
            }
         }else{

            if(data2[6] !='' && data2[6] > 0){
                $('.prize_type').append(`<option value="second" > Prize 2</option>`);
            }
         }
        
         if( data2[8] =="third" || data2[9] =="third" || data2[10] =="third" ){

            if(data2[7] !='' && data2[7] > 0){
                $('.prize_type').append(`<option value="third" disabled > Prize 3</option>`);
            }

        }else{

            if(data2[7] !='' && data2[7] > 0){
                $('.prize_type').append(`<option value="third" > Prize 3</option>`);
            }

        }

    }else{
         $(".winner-position-section").addClass('d-none');
         $('.prize_type').find("option").remove().end();

    }

}
});
});
});
</script>
