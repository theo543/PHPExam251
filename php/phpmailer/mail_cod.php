<?php

require_once 'class.phpmailer.php';

function email_pwd($message, $to) {
  $credentials = require 'mail_config.php';
  $username = $credentials['username'];
  $password = $credentials['password'];

  // Mesajul
  $message = wordwrap($message, 160, "<br />\n");
  $mail = new PHPMailer(true);
  $mail->IsSMTP();

  try {
    $mail->SMTPDebug  = 0;
    $mail->SMTPAuth   = true;
    $nume='User';
    $mail->SMTPSecure = "ssl";
    $mail->Host       = "smtp.gmail.com";
    $mail->Port       = 465;
    $mail->Username   = $username;
    $mail->Password   = $password;
    $mail->AddReplyTo('phpclassfmi@gmail.com', 'Exam PHP');
    $mail->AddAddress($to, $nume);
    $mail->SetFrom('phpclassfmi@gmail.com', 'Exam PHP');
    $mail->Subject = 'Exam PHP';
    $mail->AltBody = 'To view this post you need a compatible HTML viewer!';
    $mail->MsgHTML($message);
    $mail->Send();
    echo "Message Sent OK</p>\n";
  } catch (phpmailerException $e) {
    die($e->errorMessage()); //error from PHPMailer
  } catch (Exception $e) {
    die($e->getMessage()); //error from anything else!
  }
}
