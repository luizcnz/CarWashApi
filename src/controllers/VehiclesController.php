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

class  VehiclesController extends BaseController {

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

    public function  getAllVehicles(Request $request, Response $response, array $args){
        
        
        //$datos = $request->getQueryParams();

        // echo json_encode($datos);
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
        WHERE idUsuario=".$args["idUsuario"];
        $array=[];
        $codeStatus=0;

        try
        {
            $db = $this->conteiner->get("db");
            $resultado = $db->query($sql);

            if ($resultado->rowCount() > 0)
            {
                $codeStatus=Constants::CREATE;
                $response->getBody()->write(json_encode($resultado->fetchAll(\PDO::FETCH_CLASS,Vehicles::class),JSON_NUMERIC_CHECK));
            }
            else
            {
                $codeStatus=Constants::NO_CONTENT;
                array_push($array,["msg" =>"No hay registros en la base de datos"]);
                //json_encode("po existen registros en la BBDD.");
            }
        }
        catch(Exception $e)
        {
            array_push($array,["error" => $e->getMessage()]);
            $codeStatus=Constants::SERVER_ERROR;
        }
         return $response->withHeader('Content-type', 'application/json;charset=utf-8')

                        ->withStatus($codeStatus);
    }

    public function  getOneVehicle(Request $request, Response $response, $args){
        
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
                $codeStatus=Constants::CREATE;
                $response->getBody()->write(json_encode($resultado->fetchAll(\PDO::FETCH_CLASS,Vehicles::class),JSON_NUMERIC_CHECK));
            }
            else
            {
                $codeStatus=Constants::NO_CONTENT;
                array_push($array,["msg" =>"No hay registros en la base de datos"]);
                //json_encode("po existen registros en la BBDD.");
            }
        }
        catch(Exception $e)
        {
            array_push($array,["error" => $e->getMessage()]);
            $codeStatus=Constants::SERVER_ERROR;
        }
         return $response->withHeader('Content-type', 'application/json;charset=utf-8')

                        ->withStatus($codeStatus);
    }

    public function  addVehicle(Request $request, Response $response){
        
        $datos = $request->getParsedBody();
        //$imgRoute=$this->url.$convert->convertImage($valor["foto"]);//obtenemos el ruta de la imagen
        //$convert = new ConvertImages();
        $imgRoute= "imagen.jpg";//$this->url.$convert->convertImage($valor["foto"]);

        

        $sql = "INSERT INTO  Vehiculos(numeroPlaca, anio, fotoRuta, observacion, idMarcaVehiculos, idUsuario, idModeloVehiculos, idTipoCombustible) 
                VALUES (:numeroPlaca, :anio, :fotoRuta, :observacion, :idMarcaVehiculos, :idUsuario, :idModeloVehiculos, :idTipoCombustible)";
         $respuesta=[];

         $codeStatus=0;
         try
        {
            $db = $this->conteiner->get("db");
            $stament=$db->prepare($sql);
            $stament->bindParam(":numeroPlaca",$datos["numeroPlaca"]);
            $stament->bindParam(":anio",$datos["anio"]);
            $stament->bindParam(":fotoRuta",$imgRoute);
            $stament->bindParam(":observacion",$datos["observacion"]);
            $stament->bindParam(":idMarcaVehiculos",$datos["idMarcaVehiculos"]);
            $stament->bindParam(":idUsuario",$datos["idUsuario"]);
            $stament->bindParam(":idModeloVehiculos",$datos["idModeloVehiculos"]);
            $stament->bindParam(":idTipoCombustible",$datos["idTipoCombustible"]);
            $res = $stament->execute();

            if($res){

                $codeStatus=Constants::CREATE;
                $respuesta=["status" => "ok","msg"=>"Guardado con exito"];
            }
            

        }
        catch(\PDOException $e)
        {
            $respuesta=["status" =>"error", "msg"=>$e->getMessage()];
            $codeStatus=Constants::SERVER_ERROR;
        }
        return $response->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withJson($respuesta)
                        ->withStatus($codeStatus);
    }


    public  function  updateVehicle(Request $request, Response $response, array $arg)
    {
        //$reqPost = json_decode($request->getParsedBody(),true);

        $datos = $request->getParsedBody();

        $imgRoute= "imagen.jpg";

        $sql = "UPDATE Vehiculos SET numeroPlaca=:numeroPlaca, anio=:anio, fotoRuta=:fotoRuta, observacion=:observacion, idMarcaVehiculos=:idMarcaVehiculos, idModeloVehiculos=:idModeloVehiculos, idTipoCombustible=:idTipoCombustible
                WHERE idVehiculo=".$datos["idVehiculo"];
         $respuesta=[];
         $codeStatus=0;
        try
        {
            $db = $this->conteiner->get("db");
            $stament=$db->prepare($sql);
            $stament->bindParam(":numeroPlaca",$datos["numeroPlaca"]);
            $stament->bindParam(":anio",$datos["anio"]);
            $stament->bindParam(":fotoRuta",$imgRoute);
            $stament->bindParam(":observacion",$datos["observacion"]);
            $stament->bindParam(":idMarcaVehiculos",$datos["idMarcaVehiculos"]);
            $stament->bindParam(":idModeloVehiculos",$datos["idModeloVehiculos"]);
            $stament->bindParam(":idTipoCombustible",$datos["idTipoCombustible"]);

            if($stament->rowCount() > 0)
            {
                $codeStatus=Constants::CREATE;
                $respuesta=["status" => "ok","msg"=>"Registro Actualizado"];
            }
            else
            {
                $codeStatus=Constants::NO_CONTENT;
                $respuesta=["msg"=>"No se econtro registro para este id"];
            }

        }
        catch(\PDOException $e)
        {
            $respuesta=["status" =>"error", "msg"=>$e->getMessage()];
            $codeStatus=Constants::SERVER_ERROR;
        }
        return $response->withHeader('Content-type', 'application/json')
            ->withJson($respuesta)
            ->withStatus($codeStatus);

    }

    public function  getModel(Request $request, Response $response, $args){
        
        #$datos = $request->getQueryParams();

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
                $codeStatus=Constants::CREATE;
                $response->getBody()->write(json_encode($resultado->fetchAll(\PDO::FETCH_CLASS,Models::class),JSON_NUMERIC_CHECK));
            }
            else
            {
                $codeStatus=Constants::NO_CONTENT;
                array_push($array,["msg" =>"No hay registros en la base de datos"]);
                //json_encode("po existen registros en la BBDD.");
            }
        }
        catch(Exception $e)
        {
            array_push($array,["error" => $e->getMessage()]);
            $codeStatus=Constants::SERVER_ERROR;
        }
         return $response->withHeader('Content-type', 'application/json;charset=utf-8')

                        ->withStatus($codeStatus);
    }

    public function  getBrand(Request $request, Response $response, $args){
        
        #$datos = $request->getQueryParams();

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
                $codeStatus=Constants::CREATE;
                $response->getBody()->write(json_encode($resultado->fetchAll(\PDO::FETCH_CLASS,Brands::class),JSON_NUMERIC_CHECK));
            }
            else
            {
                $codeStatus=Constants::NO_CONTENT;
                array_push($array,["msg" =>"No hay registros en la base de datos"]);
                //json_encode("po existen registros en la BBDD.");
            }
        }
        catch(Exception $e)
        {
            array_push($array,["error" => $e->getMessage()]);
            $codeStatus=Constants::SERVER_ERROR;
        }
         return $response->withHeader('Content-type', 'application/json;charset=utf-8')

                        ->withStatus($codeStatus);
    }

    public function  getType(Request $request, Response $response, $args){
        
        #$datos = $request->getQueryParams();

        $sql = "SELECT 	
        TiposVehiculos.idTipoVehiculos, 
        TiposVehiculos.tipo_vehiculo 
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
                $codeStatus=Constants::CREATE;
                $response->getBody()->write(json_encode($resultado->fetchAll(\PDO::FETCH_CLASS,Types::class),JSON_NUMERIC_CHECK));
            }
            else
            {
                $codeStatus=Constants::NO_CONTENT;
                array_push($array,["msg" =>"No hay registros en la base de datos"]);
                //json_encode("po existen registros en la BBDD.");
            }
        }
        catch(Exception $e)
        {
            array_push($array,["error" => $e->getMessage()]);
            $codeStatus=Constants::SERVER_ERROR;
        }
         return $response->withHeader('Content-type', 'application/json;charset=utf-8')

                        ->withStatus($codeStatus);
    }

    public function  getGas(Request $request, Response $response, $args){
        
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
                $codeStatus=Constants::CREATE;
                $response->getBody()->write(json_encode($resultado->fetchAll(\PDO::FETCH_CLASS,Gas::class),JSON_NUMERIC_CHECK));
            }
            else
            {
                $codeStatus=Constants::NO_CONTENT;
                array_push($array,["msg" =>"No hay registros en la base de datos"]);
                //json_encode("po existen registros en la BBDD.");
            }
        }
        catch(Exception $e)
        {
            array_push($array,["error" => $e->getMessage()]);
            $codeStatus=Constants::SERVER_ERROR;
        }
         return $response->withHeader('Content-type', 'application/json;charset=utf-8')

                        ->withStatus($codeStatus);
    }

//        $valores= $this->conteiner->get("db_settings");
//     echo var_dump($valores);
//    $response->getBody()->write("Hola");
//    return $response;
//}

}