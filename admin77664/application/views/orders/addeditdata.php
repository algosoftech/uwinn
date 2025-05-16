<style>
  .fa-map-marker:before {
    content: "\f041";
    color: #031b26;
    padding-right: 5px;
}
.user_list{ overflow-x: auto;
    padding-top: 21px;}
.my-profile .btn:hover{
background: #e72d2e;
    color: #ffffff;
}
.my-profile .btn {
    background: #ffffff;
    border: 1px solid #e72d2e;
    padding: 8px 13px;
    border-radius: 8px;
    color: #e72d2e;
    font-family: 'Open Sans', sans-serif;
    font-size: 15px;
    font-weight: 400;
    margin-top: 0px;
}
.box {
    width: 8%;
    position: relative;
    top: -30px;
}

 .box h2 {
  display: block;
  text-align: center;
  color: black;
  position: relative;
    top: -23px;
    left: 13px;

}
.pagination {
    margin: 0;
    padding: 0;
    text-align: center;
    margin-bottom: 18px !important;
    padding-top: 15px;
}
.pagination li{
    margin: 0;
    padding: 0;
    text-align: center;
    margin-bottom: 18px !important;
    padding-right: 5px;
}
.pagination .active {
    display: inline;
    background-color: #eeeeee9e !important;
    padding-right: 5px;
    margin-right: 5px;
    border-radius: 7px;
}
.pagination li a {
    display: inline-block;
    text-decoration: none;
    padding: 5px 10px;
    color: #000;
}
.textes {
    font-size: 11px;
    position: relative;
    top: -21px;
    left: -13px;
    font-family: 'Open Sans', sans-serif;
    font-size: 13px;
    font-weight: 400;
}
 .box .chart {
    position: relative;
    width: 100%;
    height: 100%;
    text-align: center;
    font-size: 12px;
    line-height: 108px;
    height: 160px;
    color: #300808;
}
.user-earningblock .cart-box {
    margin-top: 18px;
    border: 1px solid #eaeaea;
    border-radius: 8px;
}
.user-cart .cart-box table h5 {
    font-family: 'Open Sans', sans-serif;
    font-size: 13px;
   color: #6c757d;
    margin-bottom: 20px;
    line-height: 20px;
    
}
.box .chart1 {
    position: relative;
    width: 100%;
    height: 100%;
    text-align: center;
    font-size: 12px;
    line-height: 108px;
    height: 160px;
    color: #300808;
    top: 18px;
}

.box canvas {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  width: 100%;
}
  .my-profile .user_profile p {
    margin: 0px;
    font-family: 'Open Sans', sans-serif;
    font-weight: 600;
    font-size: 12px;
    color: black;
}
  .my-profile .user_profile h2 {
    font-size: 14px !important;
    /* padding-bottom: 6px; */
    color: black;
    color: black !important;
    margin: 0px;
    font-weight: 700;
}
  .ordered_list a {
    font-family: 'Open Sans', sans-serif;
    font-size: 13px;
    color: #d12a2b;
    margin-bottom: 20px;
    line-height: 20px;
    font-weight: 400;
}
.user-cart .cart-box table p {
    font-family: 'Open Sans', sans-serif;
    font-size: 13px;
    font-weight: 400;
    margin: 0;
    margin-bottom: 5px;
    color: #6c757d;
}
  .ordered_list {
    display: flex;
    justify-content: space-around;
}
table {
    border-collapse: collapse;
}
.user-earningblock{
    padding-top: 14px;
}
tr{
    border: 1px solid #ddd;
}
table th {
     padding: 0.75rem;
    vertical-align: top;
    border: 1px solid #dee2e6;
    font-size: 14px;
    line-height: 22px;
    font-weight: 600;
    text-align: center;
    color: #6c757d;
}
table td {
   border: 1px solid #eee;
    border-top: 0;
    font-weight: 400;
    text-align:center;
    padding: 0.75rem;
    vertical-align: top;
    color: #6c757d;
    font-size: 14px;
    line-height: 22px;
  
    text-align: center;
}
  </style>
  <style>
 .users_form .add-newaddress {
    border: 2px solid #d12a2b;
    padding: 4px 20px;
    border-radius: 8px;
}
.users_form .add-newaddress a {
    font-family: 'Open Sans', sans-serif;
    font-size: 15px;
    font-weight: 400;
    color: #000;
}
   
.fa-ellipsis-v:before {
    content: "\f142";
    color: rgb(209 209 209);
    
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
                        <div class="user_list">
                            <div class="row" style="margin: 20px 0px;">
                                <div class="col-sm-12 col-sm-offset-12">
                                    <table style="width: 100%">
                                        <thead>
                                            <tr>
                                                <td class="text-left" colspan="5"><span class="order-deatils">Order Id:</span> #<?php echo $orderData['order_id']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" colspan="5"><span class="order-deatils">Date Added :</span> <?=date('d M Y', strtotime($orderData['created_at']))?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" colspan="5"><span class="order-deatils">Payment Method :</span> <?php echo $orderData['payment_mode']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" colspan="5"><span class="order-deatils">Customer Email :</span> <?php echo $orderData['user_email']; ?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" colspan="5"><span class="order-deatils">Customer Phone :</span> <?php echo $orderData['user_phone']; ?></td>
                                            </tr>

                                            <?php if($couponDetails): ?>
                                                <tr>
                                                    <?php foreach ($couponDetails as $key => $items): ?>
                                                        <?php $coupon_code[] = $items['coupon_code']; ?> ,
                                                    <?php endforeach; ?>

                                                    <td class="text-left" colspan="5"><span class="order-deatils">Coupons :</span>   <?php echo @implode(',', $coupon_code); ?> </td>
                                                </tr>
                                            <?php endif; ?>
                                            <tr>
                                                <td class="text-left">Collection Point </td>
                                                <td class="text-left" colspan="4"><?php echo $orderData['collection_point_name'].' - '.$orderData['emirate_name']; ?></td>                                  
                                            </tr>
                                        </thead>
                                        <tbody>    
                                            <tr>
                                                <th style="text-align:center;">Product Name </th>
                                                <th style="text-align:center;">Donated </th>
                                                <th style="text-align:center;">Draw Date </th>
                                                <th style="text-align:center;">Quantity</th>
                                                <th style="text-align:center;">Price</th>
                                                <th style="text-align:right;">Total</th>
                                            </tr>
                                            <?php if($orderDetails): $subTotal  = 0;
                                                    foreach($orderDetails as $orderInfo): 
                                                        $subTotal   =   ($subTotal+$orderInfo['subtotal']);

                                                        $where['where']     =   array('products_id'=> (int)$orderInfo['product_id']);
                                                        $shortField         =   array('_id'=>-1);
                                                        $products           =   $this->common_model->getData('single','da_products', $where, $shortField);
                                            ?>
                                                <tr>
                                                    <td width="30%"><?php echo $orderInfo['product_name']; ?></td>
                                                    <td width="20%"><?php echo $orderInfo['is_donated']=='Y'?'Yes':'No'; ?></td>
                                                    <td width="20%"><?php echo $products['draw_date'].' '.$products['draw_time'];; ?></td>
                                                    <td width="10%"><?php echo $orderInfo['quantity']; ?></td>
                                                    <td width="35%">AED<?php echo $orderInfo['quantity']*number_format($orderInfo['price'],2); ?></td>
                                                    <td width="25%" class="text-right">AED<?php echo number_format( $orderInfo['quantity']*$orderInfo['price'],2); ?></td>
                                                </tr>
                                            <?php endforeach; endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row" style="margin: 20px 0px;">
                                <div class="col-sm-6 col-sm-offset-6">
                                </div>
                                <div class="col-sm-6 col-sm-offset-6">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <!-- <tr>
                                                <td class="text-right"><strong>Shipping fee</strong></td>
                                                <td class="text-right">AED<?=@number_format($orderData['shipping_charge'],2)?></td>
                                            </tr> -->
                                            <tr>
                                                <td class="text-right"><strong>Inclusice of VAT:</strong></td>
                                                <td class="text-right">AED<?=@number_format($orderData['inclusice_of_vat'],2)?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-right"><strong>Subtotal:</strong></td>
                                                <td class="text-right">AED<?=@number_format($orderData['subtotal'],2)?></td>
                                            </tr>
                                            <tr>
                                                <td class="text-right"><strong>VAT:</strong></td>
                                                <td class="text-right">AED<?=@number_format($orderData['vat_amount'],2)?></td>
                                            </tr>
                                        </tbody>
                                    </table>
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