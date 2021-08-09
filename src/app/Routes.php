
<?php
/**
 * Created by PhpStorm.
 * User: Dany_Hernandez
 * Date: 14/7/2021
 * Time: 14:03
 */
use Slim\Routing\RouteCollectorProxy;
use API\controllers;
use Api\Controllers\UsuariosController;


//include __DIR__."/carlos.php";

//require __DIR__."/dany.php";


$app -> group("/v1/vehicle",function (RouteCollectorProxy $group){
    //$group->get("","Api\controllers\VehiclesController:getAllVehicles");
    $group->get("/all/{idUsuario}","Api\controllers\VehiclesController:getAllVehicles");
    $group->get("/one","Api\controllers\VehiclesController:getOneVehicle");
    $group->get("/model/{idMarcaVehiculos}","Api\controllers\VehiclesController:getModel");
    $group->get("/brand","Api\controllers\VehiclesController:getBrand");
    $group->get("/type/{idTipoVehiculos}","Api\controllers\VehiclesController:getType");
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
// }
//------------------------------Dany------------------------------------------------------------
$app -> group("/v1/users",function (RouteCollectorProxy $group1){

    $group1->post("","Api\controllers\UsuariosController:getUser");
    $group1->get("/status","Api\controllers\UsuariosController:stateSession");

    $group1->post("/update","Api\controllers\UsuariosController:updateUser");

    $group1->post("/verify","Api\controllers\UsuariosController:verifyNumberPhone");
    $group1->post("/verify/resend","Api\controllers\UsuariosController:resendVerify");
    $group1->post("/add","Api\controllers\UsuariosController:addUser");
    $group1->post("/login","Api\controllers\UsuariosController:sessionStart");
    $group1->post("/logout","Api\controllers\UsuariosController:logout");
    $group1->put("/resetpass","Api\controllers\UsuariosController:resetPassword");
    $group1->put("/changepass","Api\controllers\UsuariosController:changePassword");

});

$app->get("/v1/media/{root}/{name}","Api\utils\Images:getImage");



