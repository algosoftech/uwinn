    <?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    $logo = base_url('assets/img/u-winn.png');

    $od = isset($orderData) ? $orderData : array();

    $campaign = null;
    if (!empty($od['campaignData'])) {
        $campaign = is_object($od['campaignData']) ? $od['campaignData'] : (object) $od['campaignData'];
    }

    $product_title = !empty($od['products_name']) ? $od['products_name'] : '';
    if ($campaign && !empty($campaign->title)) {
        $product_title = $campaign->title;
    }

    $game_image = '';
    if ($campaign && !empty($campaign->game_image)) {
        $game_image = preg_replace('#^\./#', '', (string) $campaign->game_image);
    }

    $unit_price = '';
    if ($campaign && isset($campaign->price)) {
        $unit_price = $campaign->price;
    }

    $created_raw = isset($od['created_at']) ? $od['created_at'] : '';
    $created_ts = is_numeric($created_raw) ? (int) $created_raw : strtotime((string) $created_raw);
    $purchased_on = $created_ts ? date('d M, Y H:i A', $created_ts) : '';

    $draw_raw = isset($od['draw_time_string']) ? $od['draw_time_string'] : '';
    if ($draw_raw === '' && !empty($od['draw_time']) && is_numeric($od['draw_time'])) {
        $draw_raw = date('Y-m-d H:i:s', (int) $od['draw_time']);
    }
    $draw_display = $draw_raw !== '' ? date('d.m.Y h:i A', strtotime((string) $draw_raw)) : '';

    $order_id = isset($od['order_id']) ? htmlspecialchars((string) $od['order_id'], ENT_QUOTES, 'UTF-8') : '';
    $qty = isset($od['qty']) ? (int) $od['qty'] : 0;
    $total_price = isset($od['total_price']) ? htmlspecialchars((string) $od['total_price'], ENT_QUOTES, 'UTF-8') : '0';

    $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?data=' . rawurlencode((string) $order_id) . '&size=150x150';

    $tickets = !empty($od['ticketData']) && is_array($od['ticketData']) ? $od['ticketData'] : array();
    $enabled_mode_count = 0;
    if ($campaign) {
        if (isset($campaign->is_chance_enable) && strtoupper((string) $campaign->is_chance_enable) === 'Y') {
            $enabled_mode_count++;
        }
        if (isset($campaign->is_rumble_enable) && strtoupper((string) $campaign->is_rumble_enable) === 'Y') {
            $enabled_mode_count++;
        }
        if (isset($campaign->is_straight_enable) && strtoupper((string) $campaign->is_straight_enable) === 'Y') {
            $enabled_mode_count++;
        }
    }

    $html = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title>Hourly Game Invoice | UWINN</title>
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
    <body>
        <div style="width: 533px;margin: auto;background-color: #fff;padding: 19px 15px 19px;">
            <div class="row" style="margin: 0px 35px;">
                <div style="text-align: center;">
                    <img class="logo" src="' . $logo . '" alt="logo">
                </div>
            </div>
            <table>
                <tr>
                    <td class="order-title">Order ID</td>
                    <td class="order-details">' . $order_id . '</td>
                </tr>
                <tr>
                    <td class="order-title">Price <span>(inclusive VAT 5%)</span></td>
                    <td class="order-details">AED ' . $total_price . '</td>
                </tr>
                <tr>
                    <td class="order-title">Purchased On</td>
                    <td class="order-details">' . htmlspecialchars($purchased_on, ENT_QUOTES, 'UTF-8') . '</td>
                </tr>
                <tr>
                    <td class="order-title">Product</td>
                    <td class="order-details">' . (int) $qty . ' x ' . htmlspecialchars($product_title, ENT_QUOTES, 'UTF-8') . '</td>
                </tr>';
    if ($unit_price !== '') {
        $html .= '
                <tr>
                    <td class="order-title">Unit price</td>
                    <td class="order-details">AED ' . htmlspecialchars((string) $unit_price, ENT_QUOTES, 'UTF-8') . '</td>
                </tr>';
    }
    $html .= '
            </table>
            <hr>
            <div style="text-align: center;">';
    if ($game_image !== '') {
        $html .= '<img src="' . base_url($game_image) . '" class="product_image" alt="">';
    }
    $html .= '<div class="coupon-section">';

    foreach ($tickets as $row) {
        $t = is_object($row) ? $row : (object) $row;
        $type_label = isset($t->type) ? ucfirst((string) $t->type) : 'Ticket';
        $nums = isset($t->ticket) ? preg_split('/\s*,\s*/', (string) $t->ticket) : array();
        $html .= '<div class="coupon-container">';
        foreach ($nums as $n) {
            if ($n === '' || $n === null) {
                continue;
            }
            $html .= '<span class="coupon-code-circle">' . htmlspecialchars((string) $n, ENT_QUOTES, 'UTF-8') . '</span> ';
        }
        $html .= '</div>';
        if ($enabled_mode_count > 1) {
            $html .= '<div class="ticket-type-label">' . htmlspecialchars($type_label, ENT_QUOTES, 'UTF-8') . '</div>';
        }
    }

    $html .= '
            </div>
            <table>
                <tr>
                    <td class="order-title coupon-heading"><b>Ticket ID</b></td>
                    <td class="order-details"><b>' . $order_id . '</b></td>
                </tr>
            </table>
            <hr>
            <table>
                <tr>
                    <td class="order-title">Merchant Name</td>
                    <td class="order-details">' . htmlspecialchars($od['userData']->store_name, ENT_QUOTES, 'UTF-8') . '</td>
                </tr>
                <tr>
                    <td class="order-title">Draw Date</td>
                    <td class="order-details">' . htmlspecialchars($draw_display, ENT_QUOTES, 'UTF-8') . '</td>
                </tr>
            </table>
            <hr>
            <div style="text-align: center;">
                <div class="prize-section">
                    <h2 class="prize-heading">' . htmlspecialchars($product_title, ENT_QUOTES, 'UTF-8') . '</h2>
                    <p style="color:#333;font-size:14px;margin:8px 0;">Hourly game entry</p>
                </div>
            </div>
            <hr>
            <div class="row" style="margin: 0px 35px;">
                <div style="text-align: center;">
                    <img class="qr" src="' . htmlspecialchars($qrCodeUrl, ENT_QUOTES, 'UTF-8') . '" alt="QR Code">
                </div>
            </div>
            <div class="footer-container">
                <div class="heading-section">
                    <h1>U WINN L.L.C</h1>
                </div>
            </div>
            <div class="footer-container">
                <p>For more information,</p>
                <p>visit www.u-winn.com <br>
                    or Call us @ <a href="tel:+971554691351">+971 55 469 1351</a> <br> info@u-winn.com
                </p>
            </div>
        </div>
    ';

    if (! class_exists('Mpdf', false)) {
        require_once APPPATH . 'libraries/Mpdf/Mpdf.php';
    }

    echo $html; die();


    $headerpdf = '';
    $footerpdf = '';
    $mpdf = new Mpdf('', 'A4');
    $mpdf->SetHTMLHeader($headerpdf);
    $mpdf->SetHTMLFooter($footerpdf);
    $mpdf->SetDisplayMode('fullpage');
    $mpdf->AddPage();
    $mpdf->WriteHTML($html);
    $mpdf->Output($order_id . '.pdf', 'D');
