<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 7/18/18
 * Time: 9:20 AM
 */

namespace Application\Controller;


use Model\Entity\ResponseBootstrap;
use Model\Service\UploadService;
use Symfony\Component\HttpFoundation\Request;

class UploadController
{

    private $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Add file
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function post(Request $request):ResponseBootstrap {
        // get all required data is set
        //$file = $_FILES["file"];
        $filename = $_FILES["file"]["name"];
        $filetype = $_FILES["file"]["type"];
        $filesize = $_FILES["file"]["size"];
        $tempName = $_FILES["file"]["tmp_name"];

        // create response object
        $response = new ResponseBootstrap();

        // check if required data is set
        if(isset($filename) && isset($filetype) && isset($filesize) && isset($tempName)){
            return $this->uploadService->uploadFile($filename, $filetype, $filesize, $tempName);
        }else {
            $response->setStatus(404);
            $response->setMessage('Bad request');
        }

        // return response in case of incomplete data
        return $response;
    }
}