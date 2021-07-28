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
        WHERE idUsuario=".$args["iu"];
        $array=[];
        $codeStatus=0;

        try
        {
            $db = $this->conteiner->get("db");
            $resultado = $db->query($sql);

            if ($resultado->rowCount() > 0)
            {
                $codeStatus=CodeStatus::CREATE;
                $response->getBody()->write(json_encode($resultado->fetchAll(\PDO::FETCH_CLASS,Vehicles::class),JSON_NUMERIC_CHECK));
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
            $codeStatus=CodeStatus::SERVER_ERROR;
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
        WHERE Vehiculos.idUsuario=".$datos["iu"]." and Vehiculos.idVehiculos =".$datos["iv"];
        $array=[];
        $codeStatus=0;


        try
        {
            $db = $this->conteiner->get("db");
            $resultado = $db->query($sql);

            if ($resultado->rowCount() > 0)
            {
                $codeStatus=CodeStatus::CREATE;
                $response->getBody()->write(json_encode($resultado->fetchAll(\PDO::FETCH_CLASS,Vehicles::class),JSON_NUMERIC_CHECK));
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
            $codeStatus=CodeStatus::SERVER_ERROR;
        }
         return $response->withHeader('Content-type', 'application/json;charset=utf-8')
            
                        ->withStatus($codeStatus);
    }

    public function  addVehicle(Request $request, Response $response){
        
        $datos = $request->getParsedBody();
        //$imgRoute=$this->url.$convert->convertImage($valor["foto"]);//obtenemos el ruta de la imagen
        //$convert = new ConvertImages();
        $imgRoute= "imagen.jpg";//$this->url.$convert->convertImage($valor["foto"]);

        //echo json_encode($datos);
        // $val1=$datos["numeroplaca"];
        // $val2=$datos["anio"];
        // $val3=$datos["idmarcavehiculos"];
        // $val4=$datos["idusuario"];
        // $val5=$datos["idmodelovehiculos"];
        // $val6=$datos["idtipocombustible"];

        // echo json_encode($datos);

        // $response ->write("placa".$val1."|anio".$val2."|idmarca".$val3."|idusuario".$val4."|idmodelo".$val5."\idcombustible".$val6);
        // return $response;

        $sql = "INSERT INTO  Vehiculos(numeroPlaca, anio, fotoRuta, idMarcaVehiculos, idUsuario, idModeloVehiculos, idTipoCombustible) 
                VALUES (:numeroplaca, :anio, :fotoruta, :idmarcavehiculos, :idusuario, :idmodelovehiculos, :idtipocombustible)";
         $respuesta=[];

         $codeStatus=0;
         try
        {
            $db = $this->conteiner->get("db");
            $stament=$db->prepare($sql);
            $stament->bindParam(":numeroplaca",$datos["numeroplaca"]);
            $stament->bindParam(":anio",$datos["anio"]);
            $stament->bindParam(":fotoruta",$imgRoute);
            $stament->bindParam(":idmarcavehiculos",$datos["idmarcavehiculos"]);
            $stament->bindParam(":idusuario",$datos["iu"]);
            $stament->bindParam(":idmodelovehiculos",$datos["idmodelovehiculos"]);
            $stament->bindParam(":idtipocombustible",$datos["idtipocombustible"]);
            $res = $stament->execute();

            if($res){
                
                $codeStatus=CodeStatus::CREATE;
                $respuesta=["status" => "ok","msg"=>"Guardado con exito"];
            }
            

        }
        catch(\PDOException $e)
        {
            $respuesta=["status" =>"error", "msg"=>$e->getMessage()];
            $codeStatus=CodeStatus::SERVER_ERROR;
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

        $sql = "UPDATE Vehiculos SET numeroPlaca=:numeroplaca, anio=:anio, fotoRuta=:fotoruta, idMarcaVehiculos=:idmarcavehiculos, idModeloVehiculos=:idmodelovehiculos, idTipoCombustible=:idtipocombustible
                WHERE idVehiculo=".$datos["idvehiculo"];
         $respuesta=[];
         $codeStatus=0;
        try
        {
            $db = $this->conteiner->get("db");
            $stament=$db->prepare($sql);
            $stament->bindParam(":numeroplaca",$datos["numeroplaca"]);
            $stament->bindParam(":anio",$datos["anio"]);
            $stament->bindParam(":fotoruta",$imgRoute);
            $stament->bindParam(":idmarcavehiculos",$datos["idmarcavehiculos"]);
            $stament->bindParam(":idmodelovehiculos",$datos["idmodelovehiculos"]);
            $stament->bindParam(":idtipocombustible",$datos["idtipocombustible"]);

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
            $codeStatus=CodeStatus::SERVER_ERROR;
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