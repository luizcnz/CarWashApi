<?php
/**
 * Created by PhpStorm.
 * User: Dany_Hernandez
 * Date: 14/7/2021
 * Time: 17:39
 */

namespace Api\controllers;
use Api\utils\Authentication;
use Api\utils\Images;
use Api\utils\ResponseServer;
use Api\utils\UploadFile;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PHPMailer\PHPMailer\Exception;
use Api\utils\status\Constants;

class UsuariosController extends BaseController
{

    public function addUser(Request $request, Response $response, $args)
    {
        $datos = $request->getParsedBody();

        $sql = "INSERT INTO Usuarios (`nombre`, `apellido`, `direccion`, `correo`, `telefono`, `usuario`, `contrasena`,urlFoto,estadoSesion,playerId) VALUES 
                (:nombre,:apellido, :direccion,:mail,:telefono,:usuario,:pass,:urlFoto,:estado,:playerId)";

        $respuesta = new ResponseServer();
        $codeStatus=0;
         /*
          * Antes de ingresar valida el que el token enviado sea correcto primero
          * se valida cual es el metodo de verificación que uso el usuario y se envian
          * a la funcion validar.
          */
         if($datos["metodoVerificacion"]=="mail")
                $auth=$this->Verify($datos["correo"],$datos["codigo"],$datos["token"]);
         else  if($datos["metodoVerificacion"]=="phoneNumber")
                $auth=$this->Verify($datos["telefono"],$datos["codigo"],$datos["token"]);

        //Si la verificación es correcta se procede a registrar al usuario
         if($auth)
         {
              $estado=true;
             //Se obtienen los archivos y en caso de haber asignado una foto la sube
             //de lo contrarios asigna una por defecto
             $uploadedFiles = $request->getUploadedFiles();//Obtiene los archivo
             $upload= new UploadFile();

             $urlFoto = $upload->UploadOneFile($uploadedFiles, Constants::DIR_IMG, Constants::IMG_USER_DEFAULT);
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
                 $stament->bindParam(":playerId", $datos["playerId"] );
                 $res = $stament->execute();

                 if ($res)
                 {
                     $codeStatus = Constants::CREATE;
                     $respuesta->status=Constants::Ok;
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
                 $respuesta->status=Constants::ERROR;
                 $respuesta->message= "Error al registrar usuario: ".$e->getMessage();
                 $respuesta->codeStatus=$codeStatus;
                 $respuesta->statusSession=false;
             }
         }
         else{
             $codeStatus = Constants::NO_AUTHORIZE;
             $respuesta->status=Constants::FAIL_AUTH;
             $respuesta->message= "Error  al autenticar, el codigo de verificación, no coincide.";
             $respuesta->codeStatus=$codeStatus;
             $respuesta->statusSession=false;
         }

        $response->getBody()->write(json_encode($respuesta,JSON_NUMERIC_CHECK));
           return $response->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withStatus($codeStatus);

    }
    public function updateUser(Request $request, Response $response, $args)
    {
        $datos=$request->getParsedBody();//Obtengo los parametros
        $respuesta= new ResponseServer();//Crea respuesta del servidor
        $db = $this->conteiner->get("db");
        $uploadedFiles = $request->getUploadedFiles();//Obtiene los archivo
        $msg="";
        $upload= new UploadFile();
        if($this->existUser($datos["usuario"],$datos["contrasena"],$db)) //Valida si existe un usuario
        {
            if($upload->isFileUploaded( $uploadedFiles[Constants::IMG_UPLOAD_NAME]))//valida si se cambio footo
            {
                $url = $upload->UploadOneFile($uploadedFiles, Constants::DIR_IMG, Constants::IMG_USER_DEFAULT);
                $sql = "UPDATE Usuarios SET nombre=:nombre,correo=:correo,telefono='" . $datos["telefono"]."',urlFoto='$url'
                        WHERE usuario='" . $datos["usuario"] . "' and contrasena='" . $datos["contrasena"] . "'";
                $msg=$url;
            }
           else
           {
               $sql = "UPDATE Usuarios SET nombre=:nombre,correo=:correo,telefono='" . $datos["telefono"] . "'
                         WHERE usuario='" . $datos["usuario"] . "' and contrasena='" . $datos["contrasena"] . "'";
                $msg=$datos["urlFoto"];
           }
               try
            {
                $stament = $db->prepare($sql);
                $stament->bindParam(":nombre", $datos["nombre"]);
                $stament->bindParam(":correo", $datos["correo"]);
                $stament->execute();

                if ($stament->rowCount()>0)
                {
                    $codeStatus = Constants::CREATE;
                    $respuesta->status=Constants::Ok;
                    $respuesta->message=$msg;
                    $respuesta->codeStatus=$codeStatus;
                    $respuesta->statusSession=true;
                }

                else
                {
                    $codeStatus = Constants::SERVER_ERROR;
                    $respuesta->status=Constants::ERROR;
                    $respuesta->message ="No se pudo actualizar";
                    $respuesta->codeStatus=$codeStatus;
                    $respuesta->statusSession=false;
                }


            }
            catch(\PDOException $e)
            {
                $codeStatus = Constants::SERVER_ERROR;
                $respuesta->status=Constants::ERROR;
                $respuesta->message= "Error al actualizar datos".$e->getMessage();
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=false;
            }

        }
        else
        {
            $codeStatus = Constants::CREATE;
            $respuesta->status=Constants::NO_EXIST;
            $respuesta->message= "Usuario no registrado";
            $respuesta->codeStatus=$codeStatus;
            $respuesta->statusSession=false;
            $respuesta->token=null;
        }



        $response->getBody()->write(json_encode($respuesta,JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-type', 'application/json;charset=utf-8')
               ->withStatus($codeStatus);
    }
    public function  getUser(Request $request, Response $response, $args){
        $args = $request->getParsedBody();
        $sql = "SELECT idUsuario,nombre,apellido,direccion,correo,telefono,usuario,contrasena,urlFoto,estadoSesion  FROM Usuarios  where usuario=:usuario and contrasena=:contrasena";
        $array=[];
        $codeStatus=0;
        $respuesta=false;
        $respuesta = new ResponseServer();
        try
        {

            $db = $this->conteiner->get("db");
            $stament = $db->prepare($sql);
            $stament->bindParam(":usuario",$args["usuario"]);
            $stament->bindParam(":contrasena",$args["contrasena"]);
            $stament->execute();

            if ($stament->rowCount() > 0)
            {
                $array=get_object_vars($stament->fetch());
                if($array["estadoSesion"])//valida si el estado esta en false entonces reenvia un mensaje
                {
                    $codeStatus = Constants::CREATE;
                    $respuesta->status=Constants::Ok;
                    $respuesta->message=Constants::Ok;
                    $respuesta->codeStatus=$codeStatus;
                    $respuesta->statusSession=true;
                    $respuesta->token=null;
                    $array["respuesta"] = $respuesta;
                }
                else
                {
                    $codeStatus = Constants::CREATE;
                    $respuesta->status=Constants::Ok;
                    $respuesta->message=Constants::SESSION_CLOSED;
                    $respuesta->codeStatus=$codeStatus;
                    $respuesta->statusSession=false;
                    $respuesta->token=null;
                    $array["respuesta"] = $respuesta;
                }


            }
            else
            {
                $codeStatus = Constants::CREATE;
                $respuesta->status=Constants::NO_EXIST;
                $respuesta->message="El usuario no existe";
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=false ;
                $respuesta->token=null;
                $array["respuesta"] = $respuesta;
                //json_encode("po existen registros en la BBDD.");
            }
        }
        catch(Exception $e)
        {
            $codeStatus = Constants::SERVER_ERROR;
            $respuesta->status=Constants::ERROR;
            $respuesta->message="error ".$e->getMessage();
            $respuesta->codeStatus=$codeStatus;
            $respuesta->statusSession=true;
            $respuesta->token=null;
            $array["respuesta"] = $respuesta;
        }
        $response->getBody()->write(json_encode($array,JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withStatus($codeStatus);


    }

    public function getNumberVefication(Request $request, Response $response,$args)
    {
        $generatorCode=new Authentication();
        $code= $generatorCode->generateCodeAuth();



        return $response->getBody()->write("");

    }

    public function verifyNumberPhone(Request $request, Response $response,$args){
        $datos = $request->getParsedBody();

        $respuesta = new ResponseServer();
        $codeStatus=0;
       // se genera el codigo de verifiacion
        //Valid si existe usuarios registrados
        $existeUsuario=$this->isUserRegistered($datos["destinatario"],$this->conteiner->get("db"));

        if(!$existeUsuario) {
            $auth=new Authentication();
            $code= $auth->generateCodeAuth(4,Constants::GENERADOR_NUMERICO);
            $tokenAuht=$auth->generateCodeAuth(10,Constants::GENERADOR_FULL);

            try
            {
                if($datos["metodoVerificacion"] =="mail")
                {
                    $wasSended= $auth->sendMailAuth("Verifcación de cuenta.","Su codigo de verificacion es : ".$code,$datos["destinatario"]);
                }
                else if($datos["metodoVerificacion"] =="phoneNumber")
                {
                    $wasSended= $auth->sendMessage("Su codigo de verificacion es : ".$code,$datos["destinatario"]);
                }
                //Se crea la consulta
                $sql = "INSERT INTO ValidarCuenta (formaVerificacion, codigoVerificacion,token) values (:phoneOMail,:code,:token)
                       ON DUPLICATE KEY UPDATE codigoVerificacion=:code, formaVerificacion=:phoneOMail";
                if($wasSended)
                {
                    // $auth->sendMessage("Su codigo de verificación es: ".$code,"+50495079139");
                    $db = $this->conteiner->get("db");
                    $stament = $db->prepare($sql);
                    $stament->bindParam(":phoneOMail", $datos["destinatario"]);
                    $stament->bindParam(":code", $code);
                    $stament->bindParam(":token",$tokenAuht);
                    $res = $stament->execute();

                    if ($res)
                        $codeStatus = Constants::CREATE;
                    $respuesta->status="ok";
                    $respuesta->message="Código de verificación enviado";
                    $respuesta->codeStatus=$codeStatus;
                    $respuesta->statusSession=false;
                    $respuesta->token=$tokenAuht;
                }
                else
                {
                    $codeStatus = Constants::SERVER_ERROR;
                    $respuesta->status=Constants::FAIL_AUTH;
                    $respuesta->message ="No se ha podido código de verificación: ". $auth->getMessageError();
                    $respuesta->codeStatus=$codeStatus;
                    $respuesta->statusSession=false;
                    $respuesta->token=null;
                }

            }
            catch(\PDOException $e)
            {
                $codeStatus = Constants::SERVER_ERROR;
                $respuesta->status=Constants::FAIL_AUTH;
                $respuesta->message= "Error al solicitar recuperacion de contraseña: ".$e->getMessage();
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=false;
                $respuesta->token=null;
            }
        }
         else
         {
             $codeStatus = Constants::CREATE;
             $respuesta->status=Constants::USER_EXIST;
             $respuesta->message= "Ya existe un usuario registrado";
             $respuesta->codeStatus=$codeStatus;
             $respuesta->statusSession=false;
             $respuesta->token=null;
         }
       $response->getBody()->write(json_encode($respuesta,JSON_NUMERIC_CHECK));
       return $response->withHeader('Content-type', 'application/json;charset=utf-8')
           ->withStatus($codeStatus);
   }

    private  function  Verify($method,$code,$token)
   {

       $sql = "SELECT codigoVerificacion as code, formaVerificacion as method  FROM ValidarCuenta WHERE token='".$token."'";

       $codigo=0;
       $respuesta=false;
       try
       {
           $db = $this->conteiner->get("db");
           $resultado = $db->query($sql);

           if ($resultado->rowCount() > 0)
           {
                $array= get_object_vars($resultado->fetch());

              //  echo strval($array[0])."=".$code."  ".strval($array[0])."=".$method;
                if(($array["code"])==$code and  $array["method"]==$method)
                {
                    $respuesta = true;
                }
                else
                {
                    echo "hola";
                    $respuesta = false;
                }

           }
           else
           {
               $respuesta=false;

           }
       }
       catch(Exception $e)
       {
           $respuesta=false;
       }
        return $respuesta;
   }

    public function resendVerify(Request $request, Response $response,$args)
    {

        $datos = $request->getParsedBody();
        // se genera el codigo de verifiacion
        $respuesta = new ResponseServer();

        $codeStatus=0;
        //Valid si existe usuarios registrados
        $existeUsuario=$this->isUserRegistered($datos["destinatario"],$this->conteiner->get("db"));
        if(!$existeUsuario)
        {
            $auth=new Authentication();
            $code= $auth->generateCodeAuth(4,Constants::GENERADOR_NUMERICO);
            $tokenAuht=$datos["token"];

            try {
                if ($datos["metodoVerificacion"] == "mail") {
                    $wasSended = $auth->sendMailAuth("Verifcación de cuenta.", "Su codigo de verificacion es : " . $code, $datos["destinatario"]);
                } else if ($datos["metodoVerificacion"] == "phoneNumber") {
                    $wasSended = $auth->sendMessage("", $datos["destinatario"], "69958");
                    $wasSended = true;
                }
                //Se crea la consulta
                $sql = "UPDATE ValidarCuenta set formaVerificacion=:phoneOMail, codigoVerificacion=:code WHERE token=:token";

                if ($wasSended) {
                    // $auth->sendMessage("Su codigo de verificación es: ".$code,"+50495079139");
                    $db = $this->conteiner->get("db");
                    $stament = $db->prepare($sql);
                    $stament->bindParam(":phoneOMail", $datos["destinatario"]);
                    $stament->bindParam(":code", $code);
                    $stament->bindParam(":token", $tokenAuht);
                    $res = $stament->execute();

                    if ($res)
                        $codeStatus = Constants::CREATE;
                    $respuesta->status = "ok";
                    $respuesta->message = "Código de verificación renviado";
                    $respuesta->codeStatus = $codeStatus;
                    $respuesta->statusSession = false;
                    $respuesta->token = $tokenAuht;
                } else {
                    $codeStatus = Constants::SERVER_ERROR;
                    $respuesta->status = Constants::FAIL_AUTH;
                    $respuesta->message = "No se ha podido reenviar código de verificación" . $auth->getMessageError();
                    $respuesta->codeStatus = $codeStatus;
                    $respuesta->statusSession = false;
                    $respuesta->token = null;
                }

            } catch (\PDOException $e) {
                $codeStatus = Constants::SERVER_ERROR;
                $respuesta->status = Constants::FAIL_AUTH;
                $respuesta->message = "Error al envirar verificacion: " . $e->getMessage();
                $respuesta->codeStatus = $codeStatus;
                $respuesta->statusSession = false;
                $respuesta->token = null;
            }
        }
        else
        {
            $codeStatus = Constants::CREATE;
            $respuesta->status=Constants::USER_EXIST;
            $respuesta->message= "Ya existe un usuario registrado";
            $respuesta->codeStatus=$codeStatus;
            $respuesta->statusSession=false;
            $respuesta->token=null;
        }
       $response->getBody()->write(json_encode($respuesta,JSON_NUMERIC_CHECK));
       return $response->withHeader('Content-type', 'application/json;charset=utf-8')
           ->withStatus($codeStatus);
    }

    public function sessionStart(Request $request, Response $response,$args){

        $datos=$request->getParsedBody();

        $sql = "SELECT idUsuario, contrasena as pass, estadoSesion as status FROM Usuarios WHERE usuario=:usuario";

        $codeStatus=0;
        $respuesta=false;
        $respuesta = new ResponseServer();
        try
        {
            $db = $this->conteiner->get("db");
            $stament =$db->prepare($sql);
            $stament->bindParam(":usuario",$datos["usuario"]);
            $stament->execute();
            if ($stament->rowCount() > 0)
            {
                $array= get_object_vars($stament->fetch());

                if($array["pass"]==$datos["contrasena"])
                {
                   // $url=Constants::URL_BASE."/users/".$datos["usuario"]."/".$datos["contrasena"];
                    $codeStatus = Constants::CREATE;
                    $respuesta->status=Constants::Ok;
                    $respuesta->message=$array["idUsuario"];
                    $respuesta->codeStatus=$codeStatus;
                    $respuesta->statusSession=true;
                    $respuesta->token=null;
                    $this->updateState($array["idUsuario"],$db,1);
                }
                else
                {
                    $codeStatus = Constants::NO_AUTHORIZE;
                    $respuesta->status=Constants::FAIL_AUTH;
                    $respuesta->message="Usuario/contraseña incorrectos";
                    $respuesta->codeStatus=$codeStatus;
                    $respuesta->statusSession=$array["status"];
                    $respuesta->token=null;
                }
            }
            else
            {
                $codeStatus = Constants::CREATE;
                $respuesta->status=Constants::NO_EXIST;
                $respuesta->message="Usuario no existe";
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=false;
                $respuesta->token=null;
            }
        }
        catch(Exception $e)
        {
            $codeStatus = Constants::SERVER_ERROR;
            $respuesta->status=Constants::ERROR;
            $respuesta->message= "Error al solicitar inicio de sesión: ".$e->getMessage();
            $respuesta->codeStatus=$codeStatus;
            $respuesta->statusSession=false;
            $respuesta->token=null;
        }


        $response->getBody()->write(json_encode($respuesta,JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withStatus($codeStatus);
    }

    public function logout(Request $request, Response $response,$args){

        $datos=$request->getParsedBody();

        $sql = "SELECT idUsuario,contrasena as pass,estadoSesion as state FROM Usuarios WHERE usuario='".$datos["usuario"]."'";

        $codeStatus=0;
        $respuesta=true;
        $respuesta = new ResponseServer();
        try
        {
            $db = $this->conteiner->get("db");
            $resultado = $db->query($sql);

            if ($resultado->rowCount() > 0)
            {
                $array= get_object_vars($resultado->fetch());

                if($array["pass"]==$datos["contrasena"])
                {
                    $this->updateState($array["idUsuario"],$db,0);
                    $codeStatus = Constants::CREATE;
                    $respuesta->status=Constants::Ok;
                    $respuesta->message="Sesión finalizado";
                    $respuesta->codeStatus=$codeStatus;
                    $respuesta->statusSession=false;
                    $respuesta->token=null;
                }
                else
                {
                    $codeStatus = Constants::NO_AUTHORIZE;
                    $respuesta->status=Constants::FAIL_AUTH;
                    $respuesta->message="Usuario/contraseña incorrectos";
                    $respuesta->codeStatus=$codeStatus;
                    $respuesta->statusSession=false;
                    $respuesta->token=null;
                }
            }
            else
            {
                $codeStatus = Constants::CREATE;
                $respuesta->status=Constants::NO_EXIST;
                $respuesta->message="Usuario no existe";
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
            }
        }
        catch(Exception $e)
        {
            $codeStatus = Constants::SERVER_ERROR;
            $respuesta->status=Constants::ERROR;
            $respuesta->message= "Error al cerrar la sesion: ".$e->getMessage();
            $respuesta->codeStatus=$codeStatus;
            $respuesta->statusSession=true;
            $respuesta->token=null;
        }
        $response->getBody()->write(json_encode($respuesta,JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withStatus($codeStatus);
    }

    public function resetPassword(Request $request, Response $response,$args){

        $datos = $request->getParsedBody();
        // se genera el codigo de verifiacion
        $auth=new Authentication();
        $newPass= $auth->generateCodeAuth(8,Constants::GENERADOR_FULL);
        $respuesta = new ResponseServer();

        $codeStatus=0;
        try
        {
            $db = $this->conteiner->get("db");

            //Valida que exista el usuario
            $sql = "SELECT correo FROM Usuarios WHERE usuario=:usuario";
            $stament = $db->prepare($sql);
            $stament->bindParam(":usuario", $datos["usuario"]);
            $resultado=$stament->execute();

            //Valida si se devolvio aunque sea una fila
            if($stament->rowCount() > 0)
            {

                $arrayRes=[];
                $arrayRes = get_object_vars($stament->fetch());//Devuelve los valores en un array

                $msg ="Esta es su nueva contraseña: <b>".$newPass."</b></br></br> Le recomendamos que una vez que inicie sesión la cambie ya que esta es autogenerada.";
                $wasSended= $auth->sendMailAuth("Recuperación de contraseña",$msg,$arrayRes["correo"]);
                if($wasSended)
                {
                    //Si se envio el correo se procede a guardar la nueva contraseña autogenerada
                    // $auth->sendMessage("Su codigo de verificación es: ".$code,"+50495079139");
                    $sql = "UPDATE Usuarios set resetContrasena=:newPass WHERE usuario=:usuario";
                    $stament = $db->prepare($sql);
                    $stament->bindParam(":usuario", $datos["usuario"]);
                    $stament->bindParam(":newPass", $newPass);
                    $res = $stament->execute();

                    if ($res)
                        $codeStatus = Constants::CREATE;
                    $respuesta->status=Constants::Ok;
                    $respuesta->message="Su contraseña se ha reseteado";
                    $respuesta->codeStatus=$codeStatus;
                    $respuesta->statusSession=false;
                    $respuesta->token=null;
                }
                else
                {
                    $codeStatus = Constants::SERVER_ERROR;
                    $respuesta->status=Constants::FAIL_AUTH;
                    $respuesta->message ="No se ha podido verificar su identidad". $auth->getMessageError();
                    $respuesta->codeStatus=$codeStatus;
                    $respuesta->statusSession=false;
                    $respuesta->token=null;
                }

            }
         ///En caso de no existir el usuario no envia un mensaje de falla
            else
            {
                $codeStatus = Constants::SERVER_ERROR;
                $respuesta->status=Constants::FAIL_AUTH;
                $respuesta->message ="No se ha podido verificar su identidad";
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=false;
                $respuesta->token=null;
            }

        }
        catch(\PDOException $e)
        {
            $codeStatus = Constants::SERVER_ERROR;
            $respuesta->status=Constants::FAIL_AUTH;
            $respuesta->message= "Error al solicitar verificacion: ".$e->getMessage();
            $respuesta->codeStatus=$codeStatus;
            $respuesta->statusSession=false;
            $respuesta->token=null;
        }
        $response->getBody()->write(json_encode($respuesta,JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withStatus($codeStatus);
    }


    /*Actualiza el estado de la conexion*/
    public function updateState($idUser,$db,$state)
    {
        $sql = "UPDATE Usuarios SET estadoSesion='$state' WHERE idUsuario=".$idUser;

        try
        {
            $stament=$db->prepare($sql);
            $stament->execute();
            return true;
        }
        catch(\PDOException $e)
        {
            $respuesta=["status" =>"error", "msg"=>$e->getMessage()];
            $codeStatus=Constants::SERVER_ERROR;
              return false;
        }
    }
/*Valida si existe un usuario registrado
*/
    private function isUserRegistered($destinatario, $db)
    {
        $sql = "SELECT * FROM Usuarios WHERE telefono='".$destinatario."' OR correo='".$destinatario."'";
        $respuesta=false;
        try
        {
            $db = $this->conteiner->get("db");
            $resultado = $db->query($sql);

            if ($resultado->rowCount() > 0)
            {
                    $respuesta = true;
            }
            else
            {
                $respuesta=false;
            }
        }
        catch(Exception $e)
        {
            $respuesta=false;
        }
        return $respuesta;
    }
    private function existUser($user,$pasword,$db)
    {
        $sql = "SELECT * FROM Usuarios WHERE usuario='".$user."' and contrasena='".$pasword."'";
        $respuesta=false;
        try
        {
            $db = $this->conteiner->get("db");
            $resultado = $db->query($sql);

            if ($resultado->rowCount() > 0)
            {
                $respuesta = true;
            }
            else
            {
                $respuesta=false;
            }
        }
        catch(Exception $e)
        {
            $respuesta=false;
        }
        return $respuesta;
    }





    public function stateSession(Request $request, Response $response, $args)
    {
        $args=$request->getQueryParams();
        $sql = "SELECT estadoSesion as estado FROM Usuarios WHERE usuario='".$args["usuario"]."'";
        $respuesta = new ResponseServer();
        try
        {
            $db = $this->conteiner->get("db");
            $resultado = $db->query($sql);

            if ($resultado->rowCount() > 0)
            {
                 $arrayResult = get_object_vars($resultado->fetch());
                $codeStatus = Constants::CREATE;
                $respuesta->status=Constants::Ok;
                $respuesta->message="ok";
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=$arrayResult["estado"];
                $respuesta->token=null;

            }
            else
            {
                $codeStatus = Constants::CREATE;
                $respuesta->status=Constants::NO_EXIST;
                $respuesta->message="No existe";
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=false ;
                $respuesta->token=null;
            }
        }
        catch(Exception $e)
        {
            $codeStatus = Constants::SERVER_ERROR;
            $respuesta->status=Constants::ERROR;
            $respuesta->message="error ".$e->getMessage();
            $respuesta->codeStatus=$codeStatus;
            $respuesta->statusSession=false;
            $respuesta->token=null;
        }
        $response->getBody()->write(json_encode($respuesta,JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withStatus($codeStatus);
    }

    public function changePassword(Request $request, Response $response, $args)
    {
        $datos = $request->getParsedBody();
        $respuesta = new ResponseServer();
        $db = $this->conteiner->get("db");
        $res=$this->getOldPassword($datos["usuario"],$datos["tipo"],$db);

        if($res!="no")//valida si exsite la contraseña del usuario
        {
            if($res["contrasena"] == $datos["contrasenaVieja"])
            {
                try
                {

                    $sql = "UPDATE Usuarios set contrasena=:newPass,resetContrasena=null WHERE usuario=:usuario";
                    $stament = $db->prepare($sql);
                    $stament->bindParam(":usuario", $datos["usuario"]);
                    $stament->bindParam(":newPass", $datos["contrasenaNueva"]);
                    $stament->execute();

                    $codeStatus = Constants::CREATE;
                    $respuesta->status=Constants::Ok;
                    $respuesta->message= "Actualizado correctamente";
                    $respuesta->codeStatus=$codeStatus;
                    $respuesta->statusSession=true;
                    $respuesta->token=null;

                }

                catch(\PDOException $e)
                {
                    $codeStatus = Constants::SERVER_ERROR;
                    $respuesta->status=Constants::FAIL_AUTH;
                    $respuesta->message= "Error, no se pudo actualizar la contraseña ".$e->getMessage();
                    $respuesta->codeStatus=$codeStatus;
                    $respuesta->statusSession=false;
                    $respuesta->token=null;
                }

            }
            else
            {
                $codeStatus = Constants::CREATE;
                $respuesta->status=Constants::NO_MATCH;
                $respuesta->message="Las contraseñas no coinciden";
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=false;
            }


        }
        else
        {
            $codeStatus = Constants::CREATE;
            $respuesta->status=Constants::NO_EXIST;
            $respuesta->message="Usuario/contraseña no existen";
            $respuesta->codeStatus=$codeStatus;
            $respuesta->statusSession=false;
        }


        $response->getBody()->write(json_encode($respuesta,JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withStatus($codeStatus);

    }


    public function getOldPassword($user,$tipo,$db)
    {
        if($tipo =="reset")
        $sql = "SELECT resetContrasena as contrasena From Usuarios where usuario='$user'";
        else
            $sql = "SELECT contrasena From Usuarios where usuario='$user'";

        $stament = $db->prepare($sql);
        $stament->execute();
        if($stament->rowCount()>0)
        {
            $respuesta = get_object_vars($stament->fetch());

            return $respuesta;
        }
        else
        {
            return "no";
        }
    }



}