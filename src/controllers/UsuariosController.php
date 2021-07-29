<?php
/**
 * Created by PhpStorm.
 * User: Dany_Hernandez
 * Date: 14/7/2021
 * Time: 17:39
 */

namespace Api\controllers;
use Api\utils\Authentication;
use Api\utils\ConvertImages;
use Api\utils\ResponseServer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PHPMailer\PHPMailer\Exception;
use Api\utils\status\Constants;
class UsuariosController extends BaseController
{


        public function addUser(Request $request, Response $response, $args)
    {
        $datos = $request->getParsedBody();

        $sql = "INSERT INTO suarios (`nombre`, `apellido`, `direccion`, `correo`, `telefono`, `usuario`, `contrasena`,urlFoto,estadoSesion) 
                  VALUES (:nombre,:apellido, :direccion,:mail,:telefono,:usuario,:pass,:urlFoto,:estado)";

        $respuesta = new ResponseServer();
        $codeStatus=0;

        //Se verifica que el codigo que envio sea el que recibio por el correo
         if($this->Verify($datos["correo"],$datos["codigo"]))
         {
              $estado=true;
             if($datos["foto"]==""||$datos["foto"]==null)
                $urlFoto=Constants::URL_BASE."/image/default.jpg";
             else
             {

                 $converter = new ConvertImages();
                 $urlFoto = $converter->convertImage($datos["foto"],$datos["nombre"]);
                 $urlFoto=Constants::URL_BASE."/image/".$urlFoto;
             }

             try
             {
                 // $auth->sendMessage("Su codigo de verificación es: ".$code,"+50495079139");
                 $db = $this->conteiner->get("db");
                 $stament = $db->prepare($sql);
                 $stament->bindParam(":nombre", $datos["nombre"]);
                 $stament->bindParam(":apellido", $datos["apellido"]);
                 $stament->bindParam(":direccion", $datos["direccion"]);
                 $stament->bindParam(":mail", $datos["correo"]);
                 $stament->bindParam(":telefono", $datos["telefono"] );
                 $stament->bindParam(":usuario", $datos["usuario"] );
                 $stament->bindParam(":pass", $datos["contrasena"] );
                 $stament->bindParam(":urlFoto", $urlFoto );
                 $stament->bindParam(":estado", $estado );
                 $res = $stament->execute();

                 if ($res)
                 {
                     $codeStatus = Constants::CREATE;
                     $respuesta->status="ok";
                     $respuesta->message="Usuario creado con exito.";
                     $respuesta->codeStatus=$codeStatus;
                     $respuesta->statusSession=true;
                 }

                 else
                 {
                     $codeStatus = Constants::SERVER_ERROR;
                     $respuesta->codeStatus="error";
                     $respuesta->message ="No se ha podido registrar ";
                     $respuesta->codeStatus=$codeStatus;
                     $respuesta->statusSession=false;
                 }


             }
             catch(\PDOException $e)
             {
                 $codeStatus = Constants::SERVER_ERROR;
                 $respuesta->status="error";
                 $respuesta->message= "Error al registrar usuario: ".$e->getMessage();
                 $respuesta->codeStatus=$codeStatus;
                 $respuesta->statusSession=false;
             }
         }

        $response->getBody()->write(json_encode($respuesta,JSON_NUMERIC_CHECK));
           return $response->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withStatus($codeStatus);
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
        $generatorCode=new Authentication();
        $code= $generatorCode->generateCode();



        return $response->getBody()->write("");

    }

   public function verifyNumberPhone(Request $request, Response $response,$args){
        $datos = $request->getParsedBody();

       $sql = "INSERT INTO ValidarCuenta (formaVerificacion, codigoVerificacion) VALUES(:phoneOMail,:code) ON DUPLICATE KEY UPDATE codigoVerificacion=:code";
       // se genera el codigo de verifiacion
       $auth=new Authentication();
       $code= $auth->generateCode();
       $respuesta = new ResponseServer();

       $codeStatus=0;
       try
       {
            $wasSended= $auth->sendMailAuth("Su codigo de verificacion es : ".$code,$datos["VericationMethod"]);
           if($wasSended)
           {
               // $auth->sendMessage("Su codigo de verificación es: ".$code,"+50495079139");
               $db = $this->conteiner->get("db");
               $stament = $db->prepare($sql);
               $stament->bindParam(":phoneOMail", $datos["VericationMethod"]);
               $stament->bindParam(":code", $code);
               $res = $stament->execute();

               if ($res)
                   $codeStatus = Constants::CREATE;
               $respuesta->status="ok";
               $respuesta->message="Código de verificación enviado";
               $respuesta->codeStatus=$codeStatus;
               $respuesta->statusSession=false;
           }
           else
           {
               $codeStatus = Constants::SERVER_ERROR;
               $respuesta->codeStatus="error";
               $respuesta->message ="No se ha podido enviar ". $auth->getMessageError();
               $respuesta->codeStatus=$codeStatus;
               $respuesta->statusSession=false;
           }


       }
       catch(\PDOException $e)
       {
           $codeStatus = Constants::SERVER_ERROR;
           $respuesta->codeStatus="error";
           $respuesta->message= "Error al solicitar verificacion: ".$e->getMessage();
           $respuesta->codeStatus=$codeStatus;
           $respuesta->statusSession=false;
       }
       $response->getBody()->write(json_encode($respuesta,JSON_NUMERIC_CHECK));
       return $response->withHeader('Content-type', 'application/json;charset=utf-8')
           ->withStatus($codeStatus);
   }


   private  function  Verify($methodVerification,$code)
   {
       $sql = "SELECT codigoVerificacion FROM ValidarCuenta WHERE formaVerificacion='".$methodVerification."'";
       $codigo=0;
       $respuesta=false;
       $respuesta=new ResponseServer();
       try
       {
           $db = $this->conteiner->get("db");
           $resultado = $db->query($sql);

           if ($resultado->rowCount() > 0)
           {
                $array = $resultado->fetchAll();
                if($array["codigoVerificacion"]==$code)
                    $respuesta=true;
           }
       }
       catch(Exception $e)
       {
           $respuesta=false;
       }
        return $respuesta;
   }

}