<?php

require_once 'phpmailer/class.phpmailer.php';
require_once 'load_config_file.php';

function send($message, $to, $user, $user_email, $admin_email, $admin_name) {

  $mail_config = load_config_file(dirname(__FILE__) . '/mail_config.secrets.php', array('username', 'password', 'smtp_host', 'smtp_port'));
  $smtp_host = $mail_config['smtp_host'];
  $smtp_port = $mail_config['smtp_port'];
  $username = $mail_config['username'];
  $password = $mail_config['password'];

  $message = wordwrap($message, 160, "<br />\n");

  $mail = new PHPMailer(true); 
  $mail->IsSMTP();
  try {
    $mail->SMTPDebug  = 0;                     
    $mail->SMTPAuth   = true; 

    $nume="Message from $user";

    $mail->SMTPSecure = "ssl";                 
    $mail->Host       = $smtp_host;
    $mail->Port       = $smtp_port;    
    $mail->Username   = $username;
    $mail->Password   = $password;
    $mail->AddReplyTo($user_email, $user);
    $mail->AddAddress($admin_email, $admin_name);
  
    $mail->SetFrom($user_email, $user);
    $mail->Subject = $nume;
    $mail->AltBody = 'To view this post you need a compatible HTML viewer!'; 
    $mail->MsgHTML($message);
    $mail->Send();
    echo "Message Sent OK</p>\n";
  } catch (phpmailerException $e) {
    echo $e->errorMessage(); //error from PHPMailer
  } catch (Exception $e) {
    echo $e->getMessage(); //error from anything else!
  }

}
