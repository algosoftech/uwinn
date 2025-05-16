<?php 

// 
$logo = base_url('assets/img/u-winn.png');
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
        width: 200px;
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
            <div>Tel: +971 55 469 1351</div>

        </div>
        <table class="my-order-section">
            <tr>
                <td class="order-title">My Order # </td>
                <td class="order-details">'.$orderData['order_id'].'</td>
            </tr> 
        </table>
        <table>
            <tr>
                <td class="order-title"> POS </td>
                <td class="order-details">'.$name. '   </td>
            </tr>
            <tr>
                <td class="order-title"> Purchased On  </td>
                <td class="order-details">'.date('d M, Y H:i A ', strtotime($orderData['created_at'])).'</td>
            </tr>
        </table>
        <table class="border-top-bottom">
            <tr>
                <td class="order-title"> Description </td>
                <td class="order-details">Amount</td>
            </tr>
        </table>

        <table class="border-bottom">
            <tr>
                <td class="order-title">'.$orderData['product_title'].'</td>
                <td class="order-details">'.$orderData['total_price'].' AED</td>
            </tr>
            <tr>
                <td class="order-title coupon-heading"> Coupon Code</td>';
                if($orderData['raffle_mode'] == 'Y'):
                   $CouponsList[] = implode('<br>', $orderData['raffle_tickets']);
                else:
                   $tickets = json_decode($orderData['ticket']);
                   foreach ($tickets as $Mainkey => $ticketsList):
                    $CouponsList[] = implode(',', $ticketsList);
                   endforeach;
                endif;
            $html .='<td class="order-details">'. implode('<br>', $CouponsList).'</td>
            </tr>
        </table>
        <table class="border-bottom"> 
            <tr>
                <td class="order-title"> Total Qty.</td>
                <td class="order-details">'.$orderData['product_qty'].'</td>
            </tr>
            <tr>
                <td class="order-title">NetAmount <br> <span>(inclusive Vat)</span></td>
                <td class="order-details">'.$orderData['total_price'].' AED</td>
            </tr>
            <tr>
                <td class="order-title">Total AED<br> <span>(Cash)</span></td>
                <td class="order-details">'.$orderData['total_price'].' AED</td>
            </tr>
        </table>

        <div class="footer-container">
            <div class="heading-section">
                <h1>Thank you for Shopping</h1>
            </div>
            <div>
                <p> A Promotional Coupon has been issued with the Purchase</p>
            </div>
        </div>
        <div class="footer-container">
            <div class="heading-section">
                <h1>For more details</h1>
            </div>
            <div>
                <p class="information-section"> visit <a href='.$base_url.'> info@u-winn.com</a> <br>
                    call us @ <a href="tel:+971554691351">+971 55 469 1351</a> 
                </p>
                <p> Email: info@u-winn.com</p>
            </div>

        </div>

        <div class="row" style="margin: 0px 35px;">
            <div style="text-align: center;">
                <img class="qr" src='.base_url('/assets/img/ap_help_qr_code.png').' alt="QR">
            </div>
        </div>';

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