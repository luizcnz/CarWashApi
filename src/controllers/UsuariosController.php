<?php
/**
 * Created by PhpStorm.
 * User: Dany_Hernandez
 * Date: 14/7/2021
 * Time: 17:39
 */

namespace Api\controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class UsuariosController extends BaseController
{


    public function addUser(Request $request, Response $response, $args){
        
    }

    public function  getAllUsers(Request $request, Response $response, $args){
        $sql = "SELECT * FROM Usuarios";
        $array=[];
        try
        {
            $db = $this->conteiner->get("db");
            $resultado = $db->query($sql);

            if ($resultado->rowCount() > 0)
            {
                array_push($array, $resultado->fetchAll());
            }
            else
            {
                array_push($array,["msg" =>"No hay registros en la base de datos"]);
                //json_encode("po existen registros en la BBDD.");
            }
        }
        catch(Exception $e)
        {
            array_push($array,["error" => $e->getMessage()]);
        }
        return $response->withJson($array);


    }
    public function getNumberVefication(Request $request, Response $response,$args)
    {
        $factory = new \RandomLib\Factory;
        $generator = $factory->getMediumStrengthGenerator();
        $bytes = $generator->generateString(6);
        echo $bytes;
        
        $mail = new PHPMailer(true);
        try {
        $mail->SMTPDebug = 2; // Sacar esta línea para no mostrar salida debug
        $mail->isSMTP();
        $mail->Host = 'smtp.mail.yahoo.com'; // Host de conexión SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'soporte_ccatracho@yahoo.com'; // Usuario SMTP
        $mail->Password = 'zbvsvduzkhafmyum'; // Password SMTP
        $mail->SMTPSecure = 'tls'; // Activar seguridad TLS
        $mail->Port = 587; // Puerto SMTP
        
        #$mail->SMTPOptions = ['ssl'=> ['allow_self_signed' => true]]; // Descomentar si el servidor SMTP tiene un certificado autofirmado
        #$mail->SMTPSecure = false; // Descomentar si se requiere desactivar cifrado (se suele usar en conjunto con la siguiente línea)
        #$mail->SMTPAutoTLS = false; // Descomentar si se requiere desactivar completamente TLS (sin cifrado)
        
        $mail->setFrom('soporte_ccatracho@yahoo.com'); // Mail del remitente
        $mail->addAddress('dany1999hernan@gmail.com'); // Mail del destinatario
        
        $mail->isHTML(true);
        $mail->Subject = 'Confirmarción de cuenta'; // Asunto del mensaje
        $mail->Body = 'Este es el contenido del mensaje <b>en negrita!</b>'; // Contenido del mensaje (acepta HTML)
        $mail->AltBody = 'Este es el contenido del mensaje en texto plano'.$bytes; // Contenido del mensaje alternativo (texto plano)
        
        $mail->send();
        echo 'El mensaje ha sido enviado';
        } catch (\Exception $e) {
        echo 'El mensaje no se ha podido enviar, error: ', $mail->ErrorInfo;
        }
                return $response->getBody()->write($bytes);

    }
}