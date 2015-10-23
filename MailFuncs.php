<?php

require_once('libphp-phpmailer/class.phpmailer.php');
require_once('settings.php');

	function sendMail($body, $subject, $recipient, $recipientName)
	{
		global $mailHost, $mailPort, $mailUsername, $mailPassword, $mailSetFrom, $mailAddReplyTo;
		//include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded
		$mail             = new PHPMailer();
		//$body             = file_get_contents('contents.html');
		//$body             = eregi_replace("[\]",'',$body);
		//$body = "Esto es un mail de prueba";

		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->Host       = "smtp.ismsolar.com"; // SMTP server
		$mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing)
		// 1 = errors and messages
		// 2 = messages only
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		
		$mail->Host       = $mailHost;  //"smtp.ismsolar.com"; // sets the SMTP server
		$mail->Port       = $mailPort;  //587;                    // set the SMTP port for the GMAIL server
		$mail->Username   = $mailUsername;  //"uea984c"; // SMTP account username
		$mail->Password   = $mailPassword;  //"Amljp77s";        // SMTP account password

		$mail->SetFrom($mailSetFrom, "Soporte");  //('soporte@ismsolar.com', 'First Last');
		$mail->AddReplyTo($mailAddReplyTo, "Soporte"); //("soporte@ismsolar.com","First Last");
		
		$mail->Subject    = $subject;
		$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		$mail->MsgHTML($body);
		
		$address = $recipient;    //"p.jimenez@ismsolar.com";
		$mail->AddAddress($address, $recipientName);
		$mail->AddCC('soporte@ismsolar.com', 'SOPORTE ISM');

		//$mail->AddAttachment("images/phpmailer.gif");      // attachment
		//$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment

		if(!$mail->Send()) {
			echo "Mailer Error: " . $mail->ErrorInfo;
			return false;
		} else {
			echo "Message sent!";
			return true;
		}
	}


	
?>