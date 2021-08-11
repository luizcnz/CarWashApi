<?php
/**
 * Created by PhpStorm.
 * User: Dany_Hernandez
 * Date: 14/7/2021
 * Time: 14:17
 */

namespace Api\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Api\models\vehicles\Vehicles;
use Api\models\vehicles\Models;
use Api\models\vehicles\Brands;
use Api\models\vehicles\Gas;
use Api\models\vehicles\Types;
use Api\utils\status\Constants;
use Api\utils\ResponseServer;
use Api\utils\Images;
use Api\utils\UploadFile;

class  VehiclesController extends BaseController {

    public function  getAllVehicles(Request $request, Response $response, array $args){

        //$datos = $request->getQueryParams();
        // echo json_encode($datos);
        $respuesta = new ResponseServer();
        $sql = "SELECT 
        Vehiculos.idVehiculos,
        Vehiculos.numeroPlaca,
        Vehiculos.anio,
        Vehiculos.fotoRuta,
        Vehiculos.observacion,
        MarcasVehiculos.marca,
        ModelosVehiculos.modelo,
        CONCAT(MarcasVehiculos.marca,' ',ModelosVehiculos.modelo,'-', Vehiculos.numeroPlaca) as vehiculo,
        Combustible.tipoCombustible
        from Vehiculos 
        INNER JOIN MarcasVehiculos
        on Vehiculos.idMarcaVehiculos = MarcasVehiculos.idMarcaVehiculos
        INNER JOIN ModelosVehiculos
        on Vehiculos.idModeloVehiculos = ModelosVehiculos.idModeloVehiculos
        INNER JOIN Combustible
        on Vehiculos.idTipoCombustible = Combustible.idTipoCombustible
        WHERE idUsuario=".$args["idUsuario"];
        $array=[];
        $codeStatus=0;

        try
        {
            
            $db = $this->conteiner->get("db");
            $resultado = $db->query($sql);
            

            if ($resultado->rowCount() > 0)
            {
                $codeStatus = Constants::CREATE;
                $respuesta->status=Constants::Ok;
                $respuesta->message ="Consulta realizada con exito";
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
                $array_data = $resultado->fetchAll(\PDO::FETCH_CLASS,Vehicles::class);
                
            }
            else
            {
                $codeStatus=Constants::CREATE;
                $respuesta->status=Constants::NO_EXIST;
                $respuesta->message ="No hay registros en la base de datos";
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
                //json_encode("po existen registros en la BBDD.");
            }
        }
        catch(Exception $e)
        {
            $codeStatus=Constants::SERVER_ERROR;
                $respuesta->status=Constants::ERROR;
                $respuesta->message ="Error" .$e->getMessage();
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
        }


        $array_response['vehiculos'] = $array_data;

        $array_response['respuesta'] = $respuesta;

        
        $response->getBody()->write(json_encode($array_response,JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withStatus($codeStatus);
    }

    public function  getOneVehicle(Request $request, Response $response, $args){
        
        $respuesta = new ResponseServer();
        $datos = $request->getQueryParams();

        $sql = "SELECT 
        Vehiculos.idVehiculos,
        Vehiculos.numeroPlaca,
        Vehiculos.anio,
        Vehiculos.fotoRuta,
        Vehiculos.observacion,
        MarcasVehiculos.marca,
        ModelosVehiculos.modelo,
        Combustible.tipoCombustible
        from Vehiculos 
        INNER JOIN MarcasVehiculos
        on Vehiculos.idMarcaVehiculos = MarcasVehiculos.idMarcaVehiculos
        INNER JOIN ModelosVehiculos
        on Vehiculos.idModeloVehiculos = ModelosVehiculos.idModeloVehiculos
        INNER JOIN Combustible
        on Vehiculos.idTipoCombustible = Combustible.idTipoCombustible
        WHERE Vehiculos.idUsuario=".$datos["idUsuario"]." and Vehiculos.idVehiculos =".$datos["idVehiculos"];
        $array=[];
        $codeStatus=0;


        try
        {
            $db = $this->conteiner->get("db");
            $resultado = $db->query($sql);

            if ($resultado->rowCount() > 0)
            {
                $codeStatus = Constants::CREATE;
                $respuesta->status=Constants::Ok;
                $respuesta->message ="Consulta realizada con exito";
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
                $array_data = $resultado->fetchAll(\PDO::FETCH_CLASS,Vehicles::class);
            }
            else
            {
                $codeStatus=Constants::CREATE;
                $respuesta->status=Constants::NO_EXIST;
                $respuesta->message ="No hay registros en la base de datos";
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
                //json_encode("po existen registros en la BBDD.");
            }
        }
        catch(Exception $e)
        {
            $codeStatus=Constants::SERVER_ERROR;
                $respuesta->status=Constants::ERROR;
                $respuesta->message ="Error" .$e->getMessage();
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
        }

        $array_response['vehiculo'] = $array_data;

        $array_response['respuesta'] = $respuesta;

        $response->getBody()->write(json_encode($array_response,JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withStatus($codeStatus);
    }

    public function  addVehicle(Request $request, Response $response){
        
        $respuesta = new ResponseServer();
        $datos = $request->getParsedBody();

        $sql = "INSERT INTO  Vehiculos(numeroPlaca, anio, fotoRuta, observacion, idMarcaVehiculos, idUsuario, idModeloVehiculos, idTipoCombustible) 
                VALUES (:numeroPlaca, :anio, :fotoRuta, :observacion, :idMarcaVehiculos, :idUsuario, :idModeloVehiculos, :idTipoCombustible)";
         

         $uploadedFiles = $request->getUploadedFiles();//Obtiene los archivo
         $upload= new UploadFile();

         $urlFoto = $upload->UploadOneFile($uploadedFiles, Constants::DIR_IMG, Constants::IMG_CAR_DEFAULT);

         $codeStatus=0;
         try
        {
            $db = $this->conteiner->get("db");
            $stament=$db->prepare($sql);
            $stament->bindParam(":numeroPlaca",$datos["numeroPlaca"]);
            $stament->bindParam(":anio",$datos["anio"]);
            $stament->bindParam(":fotoRuta",$urlFoto);
            $stament->bindParam(":observacion",$datos["observacion"]);
            $stament->bindParam(":idMarcaVehiculos",$datos["idMarcaVehiculos"]);
            $stament->bindParam(":idUsuario",$datos["idUsuario"]);
            $stament->bindParam(":idModeloVehiculos",$datos["idModeloVehiculos"]);
            $stament->bindParam(":idTipoCombustible",$datos["idTipoCombustible"]);
            $res = $stament->execute();

            if($res){

                $codeStatus=Constants::CREATE;
                $respuesta->status=Constants::Ok;
                $respuesta->message ="Guardado con exito";
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
            }
            

        }
        catch(\PDOException $e)
        {
            $codeStatus=Constants::SERVER_ERROR;
            $respuesta->status=Constants::ERROR;
            $respuesta->message ="Error" .$e->getMessage();
            $respuesta->codeStatus=$codeStatus;
            $respuesta->statusSession=true;
            $respuesta->token=null;
        }
        $response->getBody()->write(json_encode($respuesta,JSON_NUMERIC_CHECK));
            return $response->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withStatus($codeStatus);
    }

    public  function  updateVehicle(Request $request, Response $response, array $arg)
    {
        //$reqPost = json_decode($request->getParsedBody(),true);
        $datos = $request->getParsedBody();
        $respuesta = new ResponseServer();        
        $db = $this->conteiner->get("db");
        $uploadedFiles = $request->getUploadedFiles();//Obtiene los archivo
        $codeStatus=0;

        $upload= new UploadFile();
        
        if($upload->isFileUploaded( $uploadedFiles[Constants::IMG_UPLOAD_NAME]))//valida si se cambio footo
        {
            $url = $upload->UploadOneFile($uploadedFiles, Constants::DIR_IMG, Constants::IMG_CAR_DEFAULT);
            $sql = "UPDATE Vehiculos SET numeroPlaca=:numeroPlaca, anio=:anio, observacion=:observacion, fotoRuta='$url',
            idMarcaVehiculos=:idMarcaVehiculos, idModeloVehiculos=:idModeloVehiculos, 
            idTipoCombustible=:idTipoCombustible
            WHERE idVehiculos=".$datos["idVehiculos"];
        }
        else
        {
            $sql = "UPDATE Vehiculos SET numeroPlaca=:numeroPlaca, anio=:anio, observacion=:observacion,
            idMarcaVehiculos=:idMarcaVehiculos, idModeloVehiculos=:idModeloVehiculos, 
            idTipoCombustible=:idTipoCombustible
            WHERE idVehiculos=".$datos["idVehiculos"];
        }
        

        try
        {
            
            $stament=$db->prepare($sql);
            $stament->bindParam(":numeroPlaca",$datos["numeroPlaca"]);
            $stament->bindParam(":anio",$datos["anio"]);
            $stament->bindParam(":observacion",$datos["observacion"]);
            $stament->bindParam(":idMarcaVehiculos",$datos["idMarcaVehiculos"]);
            $stament->bindParam(":idModeloVehiculos",$datos["idModeloVehiculos"]);
            $stament->bindParam(":idTipoCombustible",$datos["idTipoCombustible"]);
            $res = $stament->execute();

            if($res){

                $codeStatus=Constants::CREATE;
                $respuesta->status=Constants::Ok;
                $respuesta->message ="Modificacion Exitosa";
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
            }
           

        }
        catch(\PDOException $e)
        {
            $codeStatus=Constants::SERVER_ERROR;
            $respuesta->status=Constants::ERROR;
            $respuesta->message ="Error" .$e->getMessage();
            $respuesta->codeStatus=$codeStatus;
            $respuesta->statusSession=true;
            $respuesta->token=null;
        }
        $response->getBody()->write(json_encode($respuesta,JSON_NUMERIC_CHECK));
            return $response->withHeader('Content-type', 'application/json;charset=utf-8')
                ->withStatus($codeStatus);

    }

    public function  getModel(Request $request, Response $response, $args){
        
        $respuesta = new ResponseServer();
        //$datos = $request->getQueryParams();

        $sql = "SELECT 	
        idModeloVehiculos, 
        modelo 
        from ModelosVehiculos
        where idMarcaVehiculos=".$args["idMarcaVehiculos"];
        $array=[];
        $codeStatus=0;


        try
        {
            $db = $this->conteiner->get("db");
            $resultado = $db->query($sql);

            if ($resultado->rowCount() > 0)
            {
                
                $codeStatus = Constants::CREATE;
                $respuesta->status=Constants::Ok;
                $respuesta->message ="Consulta realizada con exito";
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
                $array_data = $resultado->fetchAll(\PDO::FETCH_CLASS,Models::class);
            }
            else
            {
                $codeStatus=Constants::CREATE;
                $respuesta->status=Constants::NO_EXIST;
                $respuesta->message ="No hay registros en la base de datos";
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
                //json_encode("po existen registros en la BBDD.");
            }
        }
        catch(Exception $e)
        {
            $codeStatus=Constants::SERVER_ERROR;
                $respuesta->status=Constants::ERROR;
                $respuesta->message ="Error" .$e->getMessage();
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
        }
        $array_response['modelo_vehiculo'] = $array_data;

        $array_response['respuesta'] = $respuesta;

        $response->getBody()->write(json_encode($array_response,JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withStatus($codeStatus);
    }

    public function  getBrand(Request $request, Response $response, $args){
        
        #$datos = $request->getQueryParams();
        $respuesta = new ResponseServer();
        $sql = "SELECT 	
        idMarcaVehiculos, 
        marca 
        from MarcasVehiculos";
        $array=[];
        $codeStatus=0;


        try
        {
            $db = $this->conteiner->get("db");
            $resultado = $db->query($sql);

            if ($resultado->rowCount() > 0)
            {
                $codeStatus = Constants::CREATE;
                $respuesta->status=Constants::Ok;
                $respuesta->message ="Consulta realizada con exito";
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
                $array_data = $resultado->fetchAll(\PDO::FETCH_CLASS,Brands::class);
            }
            else
            {
                $codeStatus=Constants::CREATE;
                $respuesta->status=Constants::NO_EXIST;
                $respuesta->message ="No hay registros en la base de datos";
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
                //json_encode("po existen registros en la BBDD.");
            }
        }
        catch(Exception $e)
        {
            $codeStatus=Constants::SERVER_ERROR;
                $respuesta->status=Constants::ERROR;
                $respuesta->message ="Error" .$e->getMessage();
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
        }
        $array_response['marca_vehiculo'] = $array_data;

        $array_response['respuesta'] = $respuesta;

        $response->getBody()->write(json_encode($array_response,JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withStatus($codeStatus);
    }

    public function  getType(Request $request, Response $response, $args){
        $respuesta = new ResponseServer();
        //$datos = $request->getQueryParams();

        $sql = "SELECT 	
        idTipoVehiculos, 
        tipo_vehiculo 
        from TiposVehiculos
        where TiposVehiculos.idTipoVehiculos=".$args["idTipoVehiculos"];
        $array=[];
        $codeStatus=0;


        try
        {
            $db = $this->conteiner->get("db");
            $resultado = $db->query($sql);

            if ($resultado->rowCount() > 0)
            {
                $codeStatus = Constants::CREATE;
                $respuesta->status=Constants::Ok;
                $respuesta->message ="Consulta realizada con exito";
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
                $array_data = $resultado->fetchAll();
            }
            else
            {
                $codeStatus=Constants::CREATE;
                $respuesta->status=Constants::NO_EXIST;
                $respuesta->message ="No hay registros en la base de datos";
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
                //json_encode("po existen registros en la BBDD.");
            }
        }
        catch(Exception $e)
        {
            $codeStatus=Constants::SERVER_ERROR;
                $respuesta->status=Constants::ERROR;
                $respuesta->message ="Error" .$e->getMessage();
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
        }
        $array_response['tipo_vehiculo'] = $array_data;

        $array_response['respuesta'] = $respuesta;

        $response->getBody()->write(json_encode($array_response,JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withStatus($codeStatus);
    }

    public function  getGas(Request $request, Response $response, $args){
        $respuesta = new ResponseServer();
        #$datos = $request->getQueryParams();

        $sql = "SELECT 	
        idTipoCombustible, 
        tipoCombustible 
        from Combustible";
        $array=[];
        $codeStatus=0;


        try
        {
            $db = $this->conteiner->get("db");
            $resultado = $db->query($sql);

            if ($resultado->rowCount() > 0)
            {
                $codeStatus = Constants::CREATE;
                $respuesta->status=Constants::Ok;
                $respuesta->message ="Consulta realizada con exito";
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
                $array_data = $resultado->fetchAll(\PDO::FETCH_CLASS,Gas::class);
            }
            else
            {
                $codeStatus=Constants::CREATE;
                $respuesta->status=Constants::NO_EXIST;
                $respuesta->message ="No hay registros en la base de datos";
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
                //json_encode("po existen registros en la BBDD.");
            }
        }
        catch(Exception $e)
        {
            $codeStatus=Constants::SERVER_ERROR;
                $respuesta->status=Constants::ERROR;
                $respuesta->message ="Error" .$e->getMessage();
                $respuesta->codeStatus=$codeStatus;
                $respuesta->statusSession=true;
                $respuesta->token=null;
        }
        $array_response['combustible'] = $array_data;

        $array_response['respuesta'] = $respuesta;

        $response->getBody()->write(json_encode($array_response,JSON_NUMERIC_CHECK));
        return $response->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withStatus($codeStatus);
    }

// public function  getAllVehicles(Request $request, Response $response, $args){
    //     $sql = "SELECT 
    //     Vehiculos.idVehiculos,
    //     Vehiculos.numeroPlaca,
    //     Vehiculos.anio,
    //     Vehiculos.fotoRuta,
    //     Vehiculos.observacion,
    //     MarcasVehiculos.marca,
    //     ModelosVehiculos.modelo,
    //     Combustible.tipoCombustible
    //     from Vehiculos 
    //     INNER JOIN MarcasVehiculos
    //     on Vehiculos.idMarcaVehiculos = MarcasVehiculos.idMarcaVehiculos
    //     INNER JOIN ModelosVehiculos
    //     on Vehiculos.idModeloVehiculos = ModelosVehiculos.idModeloVehiculos
    //     INNER JOIN Combustible
    //     on Vehiculos.idTipoCombustible = Combustible.idTipoCombustible";
    //     $array=[];

    //     $codeStatus=0;
    //     try
    //     {
    //         $db = $this->conteiner->get("db");
    //         $resultado = $db->query($sql);

    //         if ($resultado->rowCount() > 0)
    //         {
    //             $codeStatus=CodeStatus::CREATE;
    //             array_push($array, $resultado->fetchAll(\PDO::FETCH_CLASS,Vehicles::class));
    //         }
    //         else
    //         {
    //             $codeStatus=CodeStatus::NO_CONTENT;
    //             array_push($array,["msg" =>"No hay registros en la base de datos"]);
    //             //json_encode("po existen registros en la BBDD.");
    //         }
    //     }
    //     catch(Exception $e)
    //     {
    //         array_push($array,["error" => $e->getMessage()]);
    //     }
    //      return $response->withHeader('Content-type', 'application/json;charset=utf-8')
    //         ->withJson($array)
    //                     ->withStatus($codeStatus);
    // }
}