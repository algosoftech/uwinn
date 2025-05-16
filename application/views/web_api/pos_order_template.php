<?php 

// 
$logo = base_url('assets/img/u-winn.png');

// Generate QR Code URL
$order_id  = $orderData['order_id'];
$qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?data=$order_id&size=150x150";
$html = ' 
<!DOCTYPE html>
<html lang="en">

<head>
    <title> Order Pdf | UWINN</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>

    h1.ticket-heading ,.my-order-section{
        text-align: center;
        font-family: sans-serif;
        font-weight: 400;
        font-size: 25px;
        border-top: 2px dashed #808080bd;
        border-bottom: 2px dashed #808080bd;
        padding: 5px 0px;
    }

    .order-details-container {
        width:100%;
    }

    .order-deatail-section {
        width: 100%;
        display: inline-block;

    }

    table,tr {
        width:100%;
        line-height :1.6;
    }

    .order-title ,.order-details {
        width: 50%;
        font-family: sans-serif;
    }

    .order-title{
        text-align: left;
    }

    .order-details{
        text-align: right;
    }

    .my-order-section{
        margin-top: 21px;
    }

    .font-bold{
        font-weight:600;
    }


    .ticket-heading span {
        font-weight: 600;
    }


    .heading-section h1 {
        font-size: 24px;
        text-align: center;
        font-weight: 700;
        font-family: sans-serif;
    }
    .footer-container{
        text-align: center;
        font-family: sans-serif;
    }

    a:link, a:active {
        color: #000 !important;
        text-decoration: none;
    }
    a{
        color:#000 !important;
        font-weight: 600;

    }
    @media screen and (min-device-width: 360px) and (max-device-width: 600px) {
        body {
            background-color: #fff !important;
            margin: 0px;
        }
    }


    .company-heading-section {
      font-family: sans-serif;  
      text-align: center;
      line-height: 0.3;
    }

    .logo{
        // min-width: 160px;
        max-width: 220px;
        width: 145px;
    }
    
    .promotional-section ,.border-top-bottom{
         text-align: center;
        font-family: sans-serif;
        font-weight: 400;
        font-size: 16px;
        border-top: 2px dashed #808080bd;
        border-bottom: 2px dashed #808080bd;
        padding: 5px 0px;
    }
    .border-bottom{
        text-align: center;
        font-family: sans-serif;
        font-weight: 400;
        font-size: 16px;
        border-bottom: 2px dashed #808080bd;
        padding: 5px 0px; 
    }
    table {
        margin: 20px auto;
    }

    .coupon-heading {
        vertical-align: baseline;
    }
    .qr{
        // width: 200px;
    }

    // .coupon-section {
    //     display : flex;
    //     justify-content : center;
    // }

    // .coupon-section {
    //     display : flex;
    //     justify-content : center;
    // }

    // .coupon-container{
    //     display: inline-flex;
    // }
    // .coupon-code-circle {
    //     border: 1px solid #B31251;
    //     color: #B31251;
    //     border-radius: 50%;
    //     padding: 12px;
    //     font-weight: 900;
    //     height: 10px;
    //     width: 10px;
    //     margin: 5px;
    // }

    .coupon-section {
    // display: flex;
    justify-content: center;
    }

    .prize-section {
        justify-content: center;
        font-family: sans-serif;
    }

    .prize-heading {
        color: #000000;
        font-size: 20px;
    }

    .prize-section {
    color: #B31251;
    }

    .coupon-container {
        display: inline-flex;
    }

    .coupon-code-circle {
        border: 1px solid #B31251;
        color: #B31251;
        border-radius: 50%;
        padding: 12px;
        font-weight: 900;
        height:15px; 
        width: 15px;  
        margin: 5px;
       
    }

    .game_mode {
        display: flex;
        justify-content: space-evenly;
        margin: 10px;
        color: #B31251;
    }


    .custom-checkbox {
        accent-color  : #B31251;  
        width         : 16px;
        height        : 16px;
        border        : 2px solid #B31251;  
        border-radius : 4px;  
        // appearance    : none;  
        cursor        : pointer;
        display       : inline-block;
    }

    .custom-checkbox:checked {
        background-color: #B31251;
        border-color: #B31251;
        color: white;
        display: inline-block;
    }
    
    hr {
        border-top: 1px solid #80808017;
        border-right: none;
        border-left: none;
        border-bottom: 0px;
    }

    .product_image{
        max-width: 270px;
    }
    </style>
</head>
<body >

    <div style="width: 533px;margin: auto;background-color: #fff;padding: 19px 15px 19px;">
        <div class="row" style="margin: 0px 35px;">
            <div style="text-align: center;">
                <img class="logo" src='.$logo.' alt="logo">
            </div>
        </div>
        <table>
            <tr>
                <td class="order-title"> Verification Code </td>
                <td class="order-details">'.base64_decode($orderData['order_code']).'</td>
            </tr>
            <tr>
                <td class="order-title">Price <span>(inclusive Vat 5%)</span></td>
                <td class="order-details"> AED '.$orderData['total_price'].'</td>
            </tr>
            <tr>
                <td class="order-title"> Purchased On  </td>
                <td class="order-details">'.date('d M, Y H:i A ', strtotime($orderData['created_at'])).'</td>
            </tr>
            <tr>
                <td class="order-title"> Product </td>
                <td class="order-details">'.$orderData['product_qty'].' x '.$orderData['product_title'].'</td>
            </tr>
        </table>
        <hr>
        <div style="text-align: center;">
            <img src='.base_url($orderData['product_image']).' class="product_image">
            <div class="coupon-section">';
            if($orderData['ticket']):
                $Tickect = str_replace('[[', '', $orderData['ticket']);
                $Tickect = str_replace(']]', '/', $Tickect);
                $Tickect = str_replace('],[', '/', $Tickect);
                $Tickect = array_filter(explode('/', $Tickect));


                $SelectionValues = str_replace('[[', '', $orderData['selection_values']);
                $SelectionValues = str_replace(']]', '/', $SelectionValues);
                $SelectionValues = str_replace('], [', '/', $SelectionValues);
                $SelectionValues = str_replace('],[', '/', $SelectionValues);
                $SelectionValues = array_filter(explode('/', $SelectionValues));

                

                
                for ($i=0; $i < count($Tickect) ; $i++):
                    $ticket = $Tickect[$i];
                    $couponList = explode(',', $ticket);

                    $gameModeArrasy = explode(', ',$SelectionValues[$i]);

                    $nonZeroIndexes = array();
                        foreach ($gameModeArrasy as $index => $value):
                            if ($value != 0):
                                $nonZeroIndexes[] = $index;
                            endif;
                        endforeach;
                    
                    $html .='<div class="coupon-container">';
                    foreach ($couponList as $cpnkey => $coupons): 
                    $html .='<span class="coupon-code-circle">'.$coupons.'</span> ';

                    endforeach;
                    $html .= '</div>';
                    if($orderData['product_lotto_type'] < 6):
                        $html .= '
                        <div class="game_mode">
                           <span> 
                               <input type="checkbox" class="custom-checkbox" '.(in_array(0, $nonZeroIndexes) ? 'checked' : '').'  disabled >Straight
                           </span>
                           <span> 
                               <input type="checkbox" class="custom-checkbox" '.(in_array(1, $nonZeroIndexes) ? 'checked' : '').' disabled>Rumble
                           </span>
                           <span> 
                               <input type="checkbox" class="custom-checkbox" '.(in_array(2, $nonZeroIndexes) ? 'checked' : '').' disabled>Chance
                           </span>
                        </div>';
                    endif;
                endfor;
            endif;


        $html .='</div>
        <table>
            <tr>
                <td class="order-title coupon-heading"> <b>Ticket ID</b> </td>
                <td class="order-details"><b>'.$orderData['order_id'] .'</b></td>
            </tr>
        </table>
        <hr>
        <table>
            <tr>
                <td class="order-title"> Merchant Name </td>
                <td class="order-details">'. $orderData['full_name'].'</td>
            </tr>
            <tr>
                <td class="order-title"> Draw Date </td>
                <td class="order-details"> '.date('d.m.Y h:i A',strtotime($orderData['draw_date'])).'</td>
            </tr>
        </table>
         <hr>

         <div style="text-align: center;">
            <div class="prize-section">
               <h2 class="prize-heading">'. $orderData['product_title'].'</h2>';

               if($orderData['enable_prize_heading'] == 'Y'):
                $html .= '<div class="heading-section"><h1>'.$orderData['prize_heading'].'</h1></div>';
               endif;

               if($orderData['enable_stright_prize_heading'] == 'Y'):
                $html .= '<div class="heading-section"><h1>'.$orderData['stright_prize_heading'].'</h1></div>';
               endif;

               if($orderData['enable_rumble_mix_prize_heading'] == 'Y'):
                $html .= '<div class="heading-section"><h1>'.$orderData['rumble_mix_prize_heading'].'</h1></div>';
               endif;

               if($orderData['enable_reverse_prize_heading'] == 'Y'):
                $html .= '<div class="heading-section"><h1>'.$orderData['reverse_prize_heading'].'</h1></div>';
               endif;
            $html .='</div>
        </div>
        <hr>

        <div class="row" style="margin: 0px 35px;">
            <div style="text-align: center;">
                <img class="qr" src="'.$qrCodeUrl.'" alt="QR Code">
            </div>
        </div>


        <div class="footer-container">
            <div class="heading-section">
                <h1>U WINN L.L.C</h1>
            </div>
            
        </div>
        <div class="footer-container">
            <div class="heading-section">
                <p>For more information ,</p>
            </div>
            <div>
                <p class="information-section"> visit www.u-winn.com <br>
                    or Call us @ <a href="tel:+971554691351">+971 55 469 1351</a> <br> info@u-winn.com
                </p>
                
            </div>

        </div>

        ';

        echo $html; die();

  $headerpdf='';
  $footerpdf='';
  $mpdf = new \Mpdf(['debug' => true]);
  $mpdf->SetHTMLHeader($headerpdf);
  $mpdf->SetHTMLFooter($footerpdf);
  $mpdf->SetDisplayMode('fullpage');
  $mpdf->AddPage();
  $mpdf->WriteHTML($html);
  $mpdf->Output($orderData['order_id'].'.pdf','D');
?>     