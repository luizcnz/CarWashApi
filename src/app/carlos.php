<?php
/**
 * Created by PhpStorm.
 * User: Dany_Hernandez
 * Date: 14/7/2021
 * Time: 17:53
 */

use Slim\Routing\RouteCollectorProxy;
use API\controllers;
$app -> group("/v1/vehicle",function (RouteCollectorProxy $group){
    //$group->get("","Api\controllers\VehiclesController:getAllVehicles");
    $group->get("/all/{idUsuario}","Api\controllers\VehiclesController:getAllVehicles");
    $group->get("/one","Api\controllers\VehiclesController:getOneVehicle");
    $group->get("/model","Api\controllers\VehiclesController:getModel");
    $group->get("/brand","Api\controllers\VehiclesController:getBrand");
    $group->get("/gas","Api\controllers\VehiclesController:getGas");
    $group->post("/add","Api\controllers\VehiclesController:addVehicle");
    $group->put("/update","Api\controllers\VehiclesController:updateVehicle");
});

$app -> group("/v1/price",function (RouteCollectorProxy $group){
    $group->get("/all/{idTipoVehiculos}","Api\controllers\PricesController:getAllPrices");
    $group->get("/one","Api\controllers\PricesController:getOnePrice");
    //$group->get("/usuario={idusuario}&tipovehiculo={idtipovehiculo}","Api\controllers\PricesController:getPricesByUserAndVehicle");
    
});

$app -> group("/v1/quote",function (RouteCollectorProxy $group){
    $group->post("/add","Api\controllers\QuotesController:addQuote");
    $group->get("/oil","Api\controllers\QuotesController:getOil");
    //$group->get("/usuario={idusuario}&tipovehiculo={idtipovehiculo}","Api\controllers\PricesController:getPricesByUserAndVehicle");
    
});


//$app -> group("/v1/users",function (RouteCollectorProxy $group2){
//    $group2->get("/","Api\controllers\UsuariosController:getAllUsers");
//});

// $app -> group("/v1/models",function (RouteCollectorProxy $group){
//     $group->get("","Api\controllers\ModelsController:getAllModels");
// });