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
        h1.ticket-heading, .my-order-section {
            text-align: center;
            font-family: sans-serif;
            font-weight: 400;
            font-size: 25px;
            border-top: 2px dashed #808080bd;
            border-bottom: 2px dashed #808080bd;
            padding: 5px 0px;
        }
        table, tr {
            width: 100%;
            line-height: 1.6;
        }
        .order-title, .order-details {
            width: 50%;
            font-family: sans-serif;
        }
        .order-title { text-align: left; }
        .order-details { text-align: right; }
        .heading-section h1 {
            font-size: 24px;
            text-align: center;
            font-weight: 700;
            font-family: sans-serif;
        }
        .footer-container {
            text-align: center;
            font-family: sans-serif;
        }
        a:link, a:active { color: #000 !important; text-decoration: none; }
        a { color: #000 !important; font-weight: 600; }
        .logo { max-width: 220px; width: 145px; }
        table { margin: 20px auto; }
        .coupon-heading { vertical-align: baseline; }
        .coupon-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .prize-section { justify-content: center; font-family: sans-serif; color: #B31251; }
        .prize-heading { color: #000000; font-size: 20px; }
        .coupon-container {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            justify-content: center;
            width: 100%;
            margin-bottom: 10px;
        }
        .coupon-code-circle {
            border: 1px solid #B31251;
            color: #B31251;
            border-radius: 50%;
            padding: 12px;
            font-weight: 900;
            height: 15px;
            width: 15px;
            margin: 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .ticket-type-label {
            font-family: sans-serif;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            width: 100%;
            text-align: center;
            margin-top: 6px;
        }
        hr {
            border-top: 1px solid #80808017;
            border-right: none;
            border-left: none;
            border-bottom: 0px;
        }
        .product_image { max-width: 270px; }
        .qr { width: 150px; }
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

                

                
                // Parse super ball tickets: one per ticket, e.g. [[4,9,8],[4,9,8]] => sb_tickect [3,7] => ticket1 + 3, ticket2 + 7
                $sbTicketList = array();
                if (!empty($orderData['super_ball_mode']) && (strtoupper($orderData['super_ball_mode']) == 'Y') && !empty($orderData['sb_tickect'])) {
                    $sbRaw = trim($orderData['sb_tickect']);
                    $sbRaw = preg_replace('/^\[|\]$/', '', $sbRaw);
                    $sbTicketList = array_values(array_filter(array_map('trim', explode(',', $sbRaw))));
                }

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
                    // When super_ball_mode is Y, show this ticket + this ticket's super ball (sb_tickect at same index)
                    if (!empty($orderData['super_ball_mode']) && (strtoupper($orderData['super_ball_mode']) == 'Y') && isset($sbTicketList[$i])) {
                        $html .= '<span class="coupon-plus">+</span>';
                        $html .= '<span class="coupon-code-circle">'.$sbTicketList[$i].'</span> ';
                    }
                    $html .= '</div>';
                    $showStraight = (isset($orderData['stright_prize_text']) && strtoupper(trim($orderData['stright_prize_text'])) !== 'DISABLE');
                    $showRumble   = (isset($orderData['rumble_prize_text']) && strtoupper(trim($orderData['rumble_prize_text'])) !== 'DISABLE');
                    $showChance   = (isset($orderData['chance_prize_text']) && strtoupper(trim($orderData['chance_prize_text'])) !== 'DISABLE');
                    $showGameMode = $orderData['product_lotto_type'] < 6 && ($showStraight || $showRumble || $showChance);
                    if($showGameMode):
                        $html .= '
                        <div class="game_mode">';
                        if($showStraight):
                            $html .= '
                           <span> 
                               <input type="checkbox" class="custom-checkbox" '.(in_array(0, $nonZeroIndexes) ? 'checked' : '').'  disabled >Straight
                           </span>';
                        endif;
                        if($showRumble):
                            $html .= '
                           <span> 
                               <input type="checkbox" class="custom-checkbox" '.(in_array(1, $nonZeroIndexes) ? 'checked' : '').' disabled>Rumble
                           </span>';
                        endif;
                        if($showChance):
                            $html .= '
                           <span> 
                               <input type="checkbox" class="custom-checkbox" '.(in_array(2, $nonZeroIndexes) ? 'checked' : '').' disabled>Chance
                           </span>';
                        endif;
                        $html .= '
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
                <td class="order-details">'. $orderData['store_name'].'</td>
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