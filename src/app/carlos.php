<?php
/**
 * Created by PhpStorm.
 * User: Dany_Hernandez
 * Date: 14/7/2021
 * Time: 17:53
 */

use Slim\Routing\RouteCollectorProxy;
use API\controllers;
$app -> group("/v1/vehicles",function (RouteCollectorProxy $group){
    //$group->get("","Api\controllers\VehiclesController:getAllVehicles");
    $group->get("/getall","Api\controllers\VehiclesController:getAllVehicles");
    $group->get("/getvehicle","Api\controllers\VehiclesController:getVehicleByUserId");
    $group->post("/add","Api\controllers\VehiclesController:addVehicle");
    $group->put("/update","Api\controllers\VehiclesController:updateVehicle");
});

$app -> group("/v1/prices",function (RouteCollectorProxy $group){
    $group->get("/getallprices","Api\controllers\PricesController:getPricesByVehicleType");
    $group->get("/getprice","Api\controllers\PricesController:getPricesByUserAndVehicle");
    //$group->get("/usuario={idusuario}&tipovehiculo={idtipovehiculo}","Api\controllers\PricesController:getPricesByUserAndVehicle");
    
});


//$app -> group("/v1/users",function (RouteCollectorProxy $group2){
//    $group2->get("/","Api\controllers\UsuariosController:getAllUsers");
//});

// $app -> group("/v1/models",function (RouteCollectorProxy $group){
//     $group->get("","Api\controllers\ModelsController:getAllModels");
// });