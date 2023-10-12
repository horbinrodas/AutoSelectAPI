<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require '../vendor/autoload.php';

    
class ContactGateWay{
  private $mail;
  public function __construct(){
    $this->mail = new PHPMailer(true);
    $this->mail->isSMTP();
    $this->mail->SMTPDebug = 0;
    $this->mail->Host = 'smtp.elasticemail.com';
    $this->mail->SMTPAuth = true;
    $this->mail->Username = 'horbinrodas@gmail.com';
    $this->mail->Password = 'E7C614FA19567DC023D66DB28A098F682C02';
    $this->mail->SMTPSecure = 'tls';
    $this->mail->Port = 2525;
  }
  function sendMailInternal($name, $destino, $subject, $mensaje){
    $this->__construct();
    $this->mail->setFrom('autoselect@autoselect.online', $name);
    $this->mail->addAddress($destino); 
    $this->mail->Subject = $subject;
    $this->mail->Body = $mensaje;
    if(!$this->mail->send()){
      return $this->mail->ErrorInfo;
    }
    return true;
  }
  function sendMailExternal($email){
    $this->__construct();
    $this->mail->setFrom('autoselect@autoselect.online', 'AutoSelect');
    $this->mail->addAddress($email); 
    $this->mail->Subject = 'Gracias por contactarnos';
    $this->mail->Body = "Gracias por contactarnos, pronto estaremos en contacto.";
    if(!$this->mail->send()){
      return $this->mail->ErrorInfo;
    }
    return true;
  }
}