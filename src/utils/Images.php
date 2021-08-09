<?php

/**
 * Created by PhpStorm.
 * User: Dany_Hernandez
 * Date: 16/7/2021
 * Time: 19:10
 */
namespace Api\utils;
use Api\utils\status\Constants;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Images
{
    private $rutaDirectorio = __DIR__ . "/../img/";

    public function convertImage($imagen, $nombre, $directorio)
    {
        if (!empty($imagen)) {

            $suffix = $this->createRandomID();
            $image_name = $nombre . "_" . $suffix . "_" . date("Y-m-d-H-m-s") . ".jpg";

// base64 encoded utf-8 string
            $binary = base64_decode(strval($imagen));
// binary, utf-8 by
            header("Content-Type: bitmap; charset=utf-8");

            $file = fopen($this->rutaDirectorio . "/" . $directorio . "/" . $image_name, "wb");

            fwrite($file, $binary);

            fclose($file);
            return $image_name;
        } else {

            return "default.jpg";
        }
    }

    public function createRandomID()
    {
        $chars = "abcdefghijkmnopqrstuvwxyz0123456789?";
        $i = 0;

        $pass = "";

        while ($i <= 3) {

            $num = rand() % 33;

            $tmp = substr($chars, $num, 1);

            $pass = $pass . $tmp;

            $i++;
        }
        return $pass;
    }
    public function  getImage(Request $request, Response $response,array $args)
    {

        $datos= $args;

        if ($datos["root"]=="user")
        {
            $file = "/src/img/user/".$datos["name"];


            if (file_exists($file))
            {

                return $response->withHeader("Location",  "/src/img/user/" . $datos["name"])
                    ->withStatus(Constants::REDIRECT_FOUND);

            } else

                return $response->withHeader("Location", "/src/img/user/".Constants::IMG_USER_DEFAULT)
                    ->withStatus(Constants::REDIRECT_FOUND);
        }

        else
        {
            $file = __DIR__ . "/../img/auto/" . $datos["name"];
            echo $file;
            if (file_exists($file)) {
                return $response->withHeader("Location", "/src/img/auto" . $datos["name"])
                    ->withStatus(Constants::REDIRECT_FOUND);

            } else
                return $response->withHeader("Location", "/src/img/auto/".Constants::IMG_CAR_DEFAULT)
                    ->withStatus(Constants::REDIRECT_FOUND);
        }


    }

}

