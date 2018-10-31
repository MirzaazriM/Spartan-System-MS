<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 7/18/18
 * Time: 9:20 AM
 */

namespace Model\Service;


use Model\Core\Helper\Monolog\MonologSender;
use Model\Entity\ResponseBootstrap;
use Model\Mapper\UploadMapper;

class UploadService
{

    private $uploadMapper;
    private $monologHelper;
    private $configuration;

    public function __construct(UploadMapper $uploadMapper)
    {
        $this->uploadMapper = $uploadMapper;
        $this->configuration = $uploadMapper->getConfiguration();
        $this->monologHelper = new MonologSender();
    }


    /**
     * Upload file
     *
     * @param $filename
     * @param $filetype
     * @param $filesize
     * @param $tempName
     * @return ResponseBootstrap
     */
    public function uploadFile($filename, $filetype, $filesize, $tempName):ResponseBootstrap {

        try {
            // create response object
            $response = new ResponseBootstrap();

            // set allowerd type extensions
            $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png", "mp4" => "video/mp4", "m4v" => "video/x-m4v");

            // check if file extension is ok
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if(!array_key_exists($ext, $allowed)) {
                $response->setStatus(304);
                $response->setMessage('Extension not acceptable');
            }

            // check file size
            $maxsize = 5 * 1024 * 1024;
            if($filesize > $maxsize) {
                $response->setStatus(304);
                $response->setMessage('File size too big');
            }

            // check if type is ok
            if(in_array($filetype, $allowed)){
                // extract file name without extension
//                $fileEndIndex = substr(strpos( $filename, '.'), 0);
//                $fileNoExtension = substr($filename, 0, $fileEndIndex);
//
//                // generate random number and add it to end of the file name
//                $randomNumber = rand(100000, 1000000);
//                $filename = $fileNoExtension . '-' . $randomNumber . '.' . $ext;

                // move uploaded file to destination folder
                move_uploaded_file($tempName, "resources/" . $filename);
                $response->setStatus(200);
                $response->setMessage('File uploaded');
            } else{
                $response->setStatus(304);
                $response->setMessage('File type not supported');
            }

            // return response
            return $response;

        }catch (\Exception $e){
            // send monolog record
            $this->monologHelper->sendMonologRecord($this->configuration, 1000, "Upload file service: " . $e->getMessage());

            $response->setStatus(404);
            $response->setMessage('Invalid data');
            return $response;
        }

    }
}