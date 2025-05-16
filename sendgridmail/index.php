<?php
// Uncomment next line if you're not using a dependency loader (such as Composer)
require_once 'library/sendgrid-php.php';

use SendGrid\Mail\Mail;

$email = new Mail();
$email->setFrom("renu@rewiseme.com", "reWiseMe");
$email->setSubject("Sending with Twilio SendGrid is Fun");
$email->addTo("manoj.kumar@algosoft.co", "Manoj Kumar");
//$email->addContent("text/plain", "and easy to do anywhere, even with PHP");
$email->addContent(
    "text/html", '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>RSR Global</title>
</head>
<body bgcolor="#FFFFFF" style="font-family: \'Raleway\', Verdana, Helvetica, Arial, sans-serif;margin: 0; padding: 0;-webkit-font-smoothing: antialiased;-webkit-text-size-adjust: none;width: 100%!important; height: 100%;background-color: #d2eefb;">
<table>
	<tbody>
		<tr>
			<td>
			<table>
				<tbody>
					<tr>
						<td>
						<p>Dear&nbsp;Manoj,</p>
						<p>New&nbsp;user&nbsp;have&nbsp;made&nbsp;payment.</p>
						<p>User&nbsp;Name:&nbsp;{PAYER_NAME}</p>
						<p>User&nbsp;Email:&nbsp;{PAYER_EMAIL}</p>
						<p>Amount:&nbsp;{PAID_CURRENCY}&nbsp;{PAID_AMOUNT}</p>
						<p>Transaction&nbsp;Id:&nbsp;{TRANSACTION_ID}</p>
						<p>Thanks,</p>
						</td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
	</tbody>
</table>
</body>
</html>'
);

// $file_encoded = base64_encode(file_get_contents('my_file.txt'));
// $email->addAttachment(
//     $file_encoded,
//     "application/text",
//     "my_file.txt",
//     "attachment"
// );

//$sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
$sendgrid = new \SendGrid('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');
try {
    $response = $sendgrid->send($email);
    print $response->statusCode() . "\n";
    //print_r($response->headers());
    //print $response->body() . "\n";
} catch (Exception $e) {
    echo 'Caught exception: '.  $e->getMessage(). "\n";
}