<?php
	class Mail_model extends CI_Model {
	 function __construct()
	 {
		parent::__construct();	
	}

	 public function sendmail($to = '', $subject = '', $data = array(), $message = '')
    {
		$from = 'no-reply@storeyourcode.com';
		$fromname = 'SRJ CRM';
	   	$header = '<table cellpadding="0" cellspacing="0" width="570" align="center" style="font-family: Verdana, Geneva, sans-serif"><tr><td><table cellpadding="0" cellspacing="0" bgcolor="#fff" width="100%" align="center"><tr><td><div style="position: relative;padding: 13px 0px 13px 12px;background-color: #CCCCCC; left: 0px;width: 98%;border-top-right-radius: 8px;border-top-left-radius: 8px; margin: 0px"><img src="'.base_url().'/assets/logo.png" alt="Header banner" width=150/></div></td></tr><tr><td bgcolor="#f3f3f3" style="padding:25px; color: #444444; font-family: Verdana, Geneva, sans-serif; font-size: 16px; font-weight: normal; line-height: 20px;" >';
			  
		$footer = '</td></tr></table></td></tr><tr><td bgcolor="#CCCCCC" align="center" height="85" style="font-size:12px;padding-top: 8px; padding-bottom: 6px;"><strong>Contact Us</strong><br />E-mail: support@srjsolution.com <br />M: +91 971 52 842 2122</td></tr></table>';
       $full_msg = '';
	   
	     
       $full_msg .= $header . ' ' . $message . ' ' . $footer;
        /************************************/
        include(APPPATH . "libraries/phpmailer/PHPMailerAutoload.php");
        $mail = new PHPMailer;
		//print_r($mail);
        $mail->isSMTP();
		$mail->SMTPDebug = 0; # 0 off, 1 client, 2 client y server
		$mail->CharSet  = 'UTF-8';
		$mail->Host = 'storeyourcode.com';
	   $mail->Port = 25;
		$mail->SMTPSecure = 'tls'; # SSL is deprecated
		
		$mail->SMTPAuth = true;
		$mail->Username = 'no-reply@storeyourcode.com';
		$mail->Password = 'noreply@123#';	
		
        $mail->setFrom('no-reply@storeyourcode.com','SRJ CRM');
        $mail->addReplyTo($from, $fromname);
        $mail->addAddress($to, $fromname);
        $mail->Subject = $subject;
        $mail->msgHTML($full_msg);
        $mail->AltBody = 'This is a plain-text message body';
        if (!$mail->send()) {
            return "Mailer Error: " . $mail->ErrorInfo;
        } else {
            return "ok";
        }
    }
	
	public function load_mailer()
	{
	 include(APPPATH . "libraries/phpmailer/PHPMailerAutoload.php");
	}  
	
	

}
?>