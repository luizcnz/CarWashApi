<?php
/**
 * Created by PhpStorm.
 * User: Dany_Hernandez
 * Date: 8/8/2021
 * Time: 17:41
 */

namespace Api\utils;
use Api\utils\status\Constants;
use Psr\Http\Message\UploadedFileInterface;

class UploadFile
{
   public function moveUploadedFile($directory, UploadedFileInterface $uploadedFile)
    {
        //Se obtiene el archivo, su extensión, y se genera el nombre para guardarlo y por ultimo se retorna
        //su nombre
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
        return $filename;
    }

    public function  UploadOneFile($uploadedFiles,$directory,$defaultImage)
    {
        $url=$defaultImage;
        $type=$defaultImage==Constants::IMG_USER_DEFAULT?"user":"auto";//Valida donde va guardar
        $directory= $_SERVER['DOCUMENT_ROOT'].$directory."/".$type;
        /*Valida que si se este subiendo una imagen*/
        if(isset($uploadedFiles[Constants::IMG_UPLOAD_NAME]))
        {
            $uploadedFile = $uploadedFiles[Constants::IMG_UPLOAD_NAME];
            //Valida si se sube la imagen o el archivo
            if ($this->isFileUploaded($uploadedFile))
            {
                $filename = $this->moveUploadedFile($directory, $uploadedFile);
                $url= Constants::DOMAIN."/img/".$type."/".$filename;
            }
        }

        return $url;
    }

    public function  isFileUploaded($uploadedFile)
    {
        if($uploadedFile->getError() === UPLOAD_ERR_OK)
            return true;
        else
            return false;
    }
}
