<html>
<head>
<title>Mail Test</title>
</head>
<body>
<?php

//error_reporting(c);
error_reporting(mail.livemail.co.uk);

date_default_timezone_set('America/Toronto');

include('class.phpmailer.php');
//include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

$mail                = new PHPMailer();

$body                = 'TEST EMAIL';
$mail->IsSMTP(); // telling the class to use SMTP
$mail->Host          = "smtp.livemail.co.uk";
$mail->SMTPAuth      = true;                  // enable SMTP authentication
$mail->SMTPSecure 	= "ssl";    
$mail->SMTPKeepAlive = true;                  // SMTP connection will not close after each email sent
$mail->Port          = 465;                    // set the SMTP port for the GMAIL server
$mail->Username      = "admin@dev2.interviewerr.co"; // SMTP account username
$mail->Password      = "3EE93dyRWB9G6YRboS9624@4$";        // SMTP account password
$mail->SetFrom('admin@dev2.interviewerr.co', 'INTERVIEWERR');
$mail->AddReplyTo('noreply@dev2.interviewerr.co', 'INTERVIEWERR');

$mail->Subject		= "PHPMailer Test Subject via smtp, basic with authentication";

$mail->AltBody    	= "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
$mail->MsgHTML($body);
$mail->AddAddress('vipin.gupta@algosoft.co', 'VIPIN GUPTA');
$mail->AddAttachment("images/phpmailer.gif");      // attachment
$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment
	
  if(!$mail->Send()) {
    echo "Mailer Error";
  } else {
    echo "Message sent!";
  }
  // Clear all addresses and attachments for next loop
  $mail->ClearAddresses();
  $mail->ClearAttachments();
//}
?>

</body>
</html>
