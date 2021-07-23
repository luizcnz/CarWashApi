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
use Api\utils\status\CodeStatus;

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

    public function  getAllVehicles(Request $request, Response $response, $args){
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
        WHERE idUsuario=".$args["idusuario"];
        $array=[];
        $codeStatus=0;

        try
        {
            $db = $this->conteiner->get("db");
            $resultado = $db->query($sql);

            if ($resultado->rowCount() > 0)
            {
                $codeStatus=CodeStatus::CREATE;
                array_push($array, $resultado->fetchAll(\PDO::FETCH_CLASS,Vehicles::class));
            }
            else
            {
                $codeStatus=CodeStatus::NO_CONTENT;
                array_push($array,["msg" =>"No hay registros en la base de datos"]);
                //json_encode("po existen registros en la BBDD.");
            }
        }
        catch(Exception $e)
        {
            array_push($array,["error" => $e->getMessage()]);
        }
         return $response->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withJson($array)
                        ->withStatus($codeStatus);
    }

    public function  getVehicleByUserId(Request $request, Response $response, $args){

        // $uri = $request->getUri();
        // $uri= $request->getQueryParams();

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
        WHERE idUsuario=".$args["idusuario"]." and idVehiculos =".$args["idvehiculo"];
        $array=[];
        $codeStatus=0;


        try
        {
            $db = $this->conteiner->get("db");
            $resultado = $db->query($sql);

            if ($resultado->rowCount() > 0)
            {
                $codeStatus=CodeStatus::CREATE;
                array_push($array, $resultado->fetchAll(\PDO::FETCH_CLASS,Vehicles::class));
            }
            else
            {
                $codeStatus=CodeStatus::NO_CONTENT;
                array_push($array,["msg" =>"No hay registros en la base de datos"]);
                //json_encode("po existen registros en la BBDD.");
            }
        }
        catch(Exception $e)
        {
            array_push($array,["error" => $e->getMessage()]);
        }
         return $response->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withJson($array)
                        ->withStatus($codeStatus);
    }

    public function  addVehicle(Request $request, Response $response, $args){
        $reqPost = json_decode($request->getBody(), true);


        //$imgRoute=$this->url.$convert->convertImage($valor["foto"]);//obtenemos el ruta de la imagen
        
        $convert = new ConvertImages();
        $imgRoute= "imagen.jpg";//$this->url.$convert->convertImage($valor["foto"]);

        $sql = "INSERT INTO  Vehiculos(idVehiculos, numeroPlaca, anio, fotoRuta, idMarcaVehiculos, idModeloVehiculos, idTipoCombustible) 
                VALUES (:idvehiculos, :numeroplaca, :anio, :fotoruta, :idmarcavehiculos, :idmodelovehiculos, :idtipocombustible)";
         $respuesta=[];

         $codeStatus=0;
         try
        {
            $db = $this->conteiner->get("db");
            $stament=$db->prepare($sql);
            $stament->bindParam(":idvehiculos",$reqPost["idvehiculos"]);
            $stament->bindParam(":numeroplaca",$reqPost["numeroplaca"]);
            $stament->bindParam(":anio",$reqPost["anio"]);
            $stament->bindParam(":fotoruta",$imgRoute);
            $stament->bindParam(":idmarcavehiculos",$reqPost["idmarcavehiculos"]);
            $stament->bindParam(":idmodelovehiculos",$reqPost["idmodelovehiculos"]);
            $stament->bindParam(":idtipocombustible",$reqPost["idtipocombustible"]);
            $res = $stament->execute();

            if($res){
                $codeStatus=CodeStatus::CREATE;
               $respuesta=["status" => "ok","msg"=>"Guardado con exito"];
            }
        }
        catch(\PDOException $e)
        {
            $respuesta=["status" =>"error", "msg"=>$e->getMessage()];
        }
        return $response->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withJson($respuesta)
                        ->withStatus($codeStatus);
    }


    public  function  updateVehicle(Request $request, Response $response, array $arg)
    {
        $reqPost = json_decode($request->getBody(),true);

        $sql = "UPDATE Vehiculos SET numeroPlaca=:numeroplaca, anio=:anio, fotoRuta=:, idMarcaVehiculos=:idmarcavehiculos, idModeloVehiculos=:idmodelovehiculos, idTipoCombustible=:idtipocombustible
                WHERE idVehiculo=".$arg["id"];
         $respuesta=[];
         $codeStatus=0;
        try
        {
            $db = $this->conteiner->get("db");
            $stament=$db->prepare($sql);
            $stament->bindParam(":idvehiculos",$reqPost["idvehiculos"]);
            $stament->bindParam(":numeroplaca",$reqPost["numeroplaca"]);
            $stament->bindParam(":anio",$reqPost["anio"]);
            $stament->bindParam(":fotoruta",$imgRoute);
            $stament->bindParam(":idmarcavehiculos",$reqPost["idmarcavehiculos"]);
            $stament->bindParam(":idmodelovehiculos",$reqPost["idmodelovehiculos"]);
            $stament->bindParam(":idtipocombustible",$reqPost["idtipocombustible"]);

            if($stament->rowCount() > 0)
            {
                $codeStatus=CREATE;
                $respuesta=["status" => "ok","msg"=>"Registro Actualizado"];
            }
            else
            {
                $codeStatus=NO_CONTENT;
                $respuesta=["msg"=>"No se econtro registro para este id"];
            }

        }
        catch(\PDOException $e)
        {
            $respuesta=["status" =>"error", "msg"=>$e->getMessage()];
        }
        return $response->withHeader('Content-type', 'application/json')
            ->withJson($respuesta)
            ->withStatus($codeStatus);

    }

//        $valores= $this->conteiner->get("db_settings");
//     echo var_dump($valores);
//    $response->getBody()->write("Hola");
//    return $response;
//}

}