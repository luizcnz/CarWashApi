<?php
/**
 * Created by PhpStorm.
 * User: Dany_Hernandez
 * Date: 14/7/2021
 * Time: 13:51
 */

use Slim\Factory\AppFactory;

require __DIR__ . '/../../vendor/autoload.php';
//Creamos el contendor principal que setearemos a slim para crear contendores dentro del
$auxContainer = new \DI\Container();
AppFactory::setContainer($auxContainer);

$app = AppFactory::create();

$mainContainer = $app->getContainer();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

require __DIR__."/Routes.php";
require __DIR__ . "/config.php";

// Add routes
//$app->get('/', function (Request $request, Response $response) {
//    $response->getBody()->write('<a href="/hello/world">Try /hello/world</a>');
//    return $response;
//});
//

$app->run();
