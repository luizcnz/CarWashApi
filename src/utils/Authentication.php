<?php

/**
 * Created by PhpStorm.
 * User: Dany_Hernandez
 * Date: 26/7/2021
 * Time: 17:19
 */

namespace Api\utils;

use Twilio\Rest\Client;
use RandomLib\Factory;
use PHPMailer\PHPMailer\PHPMailer;
class Authentication
{
    private $messageError;
    public function  generateCodeAuth($length,$chars)
    {
        $factory = new Factory();

        $generator =$factory->getMediumStrengthGenerator();
        $code = $generator->generateString($length,$chars);
        return $code;
    }

//  public  function sendMessage($msg,$to)
//  {
//
//
//$curl = curl_init();
//
//curl_setopt_array($curl, array(
//    CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
//    CURLOPT_RETURNTRANSFER => true,
//    CURLOPT_ENCODING => "",
//    CURLOPT_MAXREDIRS => 10,
//    CURLOPT_TIMEOUT => 30,
//    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//    CURLOPT_CUSTOMREQUEST => "POST",
//    CURLOPT_POSTFIELDS => "{\n  \"flow_id\": \"60ffae2f6dbbe134657f0e6d\",\n  \"mobiles\": \"95079139\",\n  \"code\": \"1245\",\n  \"hashkey\": \"fgfgfg55gg\"\n}",
//    CURLOPT_HTTPHEADER => array(
//        "authkey: 364859AHmz0wDS4HuZ60ff9520P1",
//        "content-type: application/JSON"
//    ),
//));
//
//$response = curl_exec($curl);
//$err = curl_error($curl);
//
//curl_close($curl);
//
//if ($err) {
//    $this->messageError=$err;
//    return false;
//} else {
//  return true;
//}
//  }


  public function sendMailAuth($subject,$msg, $sendTo){
      $mail = new PHPMailer(true);
      try {
         // $mail->SMTPDebug = 2; // Sacar esta línea para no mostrar salida debug
          $mail->isSMTP();
          $mail->Host = 'smtp.mail.yahoo.com'; // Host de conexión SMTP
          $mail->SMTPAuth = true;
          $mail->Username = 'soporte_ccatracho@yahoo.com'; // Usuario SMTP
          $mail->Password = 'zbvsvduzkhafmyum'; // Password SMTP
          $mail->SMTPSecure = 'tls'; // Activar seguridad TLS
          $mail->Port = 587; // Puerto SMTP
          $mail->CharSet="UTF-8";
          $mail->setFrom('soporte_ccatracho@yahoo.com'); // Mail del remitente
          $mail->addAddress($sendTo); // Mail del destinatario

          $mail->isHTML(true);
          $mail->Subject = $subject; // Asunto del mensaje
          $mail->Body = $msg; // Contenido del mensaje (acepta HTML)
          // Contenido del mensaje alternativo (texto plano)
          $mail->send();
           return true;
      }
      catch (\Exception $e)
      {
          $this->setMessageError($e->getMessage());
          return false;
       //  return 'El mensaje no se ha podido enviar, error: ', $mail->ErrorInfo;
      }
  }


    public function sendMessage($msg,$to){

        $account_sid = 'AC9dfa02114209e9e8c4eab3cc9b5c6f0a';
        $auth_token = '731727d57eda0cbddc554d669750b166';

        $twilio_number = "+18322415140";

        $client = new Client($account_sid, $auth_token);
        $result =$client->messages->create(
        // Where to send a text message (your cell phone?)
            '+504'.$to,
            array(
                'from' => $twilio_number,
                'body' => $msg
            )
        );
        if($result->errorCode==null)
            return true;
        else
            return false;
    }



    /**
     * @return mixed
     */
    public function getMessageError()
    {
        return $this->messageError;
    }

    /**
     * @param mixed $messageError
     */
    public function setMessageError($messageError)
    {
        $this->messageError = $messageError;
    }


}