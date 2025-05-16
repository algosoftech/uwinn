<?php 

// 
$logo = base_url('assets/img/u-winn.png');
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
        margin: 8px;
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
        margin: 15px auto;
    }

    .coupon-heading {
        vertical-align: baseline;
    }
    .qr{
        margin-top:10px;
        width: 150px;
    }
    .company_name{
        margin-top: 8px;
        margin-bottom: 0px;
    }

    .prize-heading{
        margin:0px;
    }

    .footer-container p{
        margin: 6px;
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
        <div class="company-heading-section">
            <h3 class="ticket-heading"> U WINN L.L.C </h3>
        </div>
        <table class="my-order-section">
            <tr>
                <td class="order-title">Order ID : </td>
                <td class="order-details">'.$orderData['order_id'].'</td>
            </tr> 

        </table>
        <table>
            <tr>
                <td class="order-title">Verification Code : </td>
                <td class="order-details">'.base64_decode($orderData['order_code']).'</td>
            </tr> 
            <tr>
                <td class="order-title"> Purchased On  </td>
                <td class="order-details">'.date('d M, Y H:i A ', strtotime($orderData['created_at'])).'</td>
            </tr>

            <tr>
                <td class="order-title"> Merchant Name : </td>
                <td class="order-details">'.$orderData['full_name'].'</td>
            </tr>

            <tr>
                <td class="order-title"> Product : </td>
                <td class="order-details">'.$orderData['product_qty'] .' x '.$orderData['product_title'].'</td>
            </tr>

            <tr>
                <td class="order-title"> Price (Inc. VAT 5%) : </td>
                <td class="order-details">AED '.$orderData['total_price'].'</td>
            </tr>
        </table>
        <table class="border-top-bottom">
            <tr>
                <td class="order-title"> Raffle Coupon :</td>
                <td class="order-details">';
                    foreach($orderData['raffle_tickets'] as $tickectItem): 
                        $html .=$tickectItem.'</br>';
                      endforeach;  
                $html .='</td>
            </tr>
            <tr>
                <td class="order-title"> Draw Date : </td>
                <td class="order-details">'.date('d.m.Y h:i A',strtotime($orderData['created_at'])).'</td>
            </tr>
        </table>

         <div style="text-align: center;" class="border-bottom">
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
         <div class="row" style="margin: 0px 35px;">
            <div class="qr-section" style="text-align: center;">
                <img class="qr" src='.$qrCodeUrl.' alt="QR">
                <h3 class="company_name">U WIN L.L.C.</h3>
            </div>
        </div>
        <div class="footer-container">
            <div>
                <p>For more Information,</p>
                <p class="information-section"> visit <a href='.$base_url.'> info@u-winn.com</a> <br>
                    or call us @ <a href="tel:+971554691351">+971 55 469 1351</a> 
                </p>
                <p> Email: info@u-winn.com</p>
            </div>
        </div>
        <div class="footer-container">----- Thank You -----</div>
       ';

        // echo $html; die();

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