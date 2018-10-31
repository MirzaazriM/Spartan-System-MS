<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 7/26/18
 * Time: 12:04 PM
 */

namespace Model\Service;


use Model\Entity\ResponseBootstrap;
use Model\Mapper\DownloadMapper;

class DownloadService
{

    private $downloadMapper;

    public function __construct(DownloadMapper $downloadMapper)
    {
        $this->downloadMapper = $downloadMapper;
    }


    /**
     * Get android zip data
     *
     * @param string $app
     * @param string $type
     * @param string $version
     * @param string $lang
     * @param string $mobile
     * @return ResponseBootstrap
     */
    public function getData(string $app, string $type, string $version, string $lang, string $mobile):ResponseBootstrap {

        // get data from mobile MS
        $url = "http://staging.api.mobile.diamondappgroup.com/mobile?app=$app&type=$type&version=$version&lang=$lang";

        // Get raw Json
        $jsonRaw =  file_get_contents($url);
        // Decode json
        $json = json_decode($jsonRaw, true);

        // Save json to file
        $myfile = fopen("mobile_data/data.json", "w") or die("Unable to open file!");
        fwrite($myfile, $jsonRaw);

        // Store resources
        $images = [];
        $package_images = [];
        $thumbnails = [];
        $musclesInvolved = [];
        // $gif = [];
        $mp4 = [];

        // Get images from packages
        foreach($json['response']['packages'] as $packages){
            array_push($package_images, $packages['thumbnail']);  // $packages['thumbnail']
        }

        // Get images from workotu plans
        foreach($json['response']['training_plans'] as $workoutPlan){
            array_push($images, $workoutPlan['thumbnail']);
        }

        // Get images from nutrition plans
        foreach($json['response']['nutrition_plans'] as $nutritionPlan){
            array_push($images, $nutritionPlan['thumbnail']);
        }

        // Get resources from exercises
        foreach($json['response']['exercises'] as $exercise){
            // Store thumbnail
            array_push($thumbnails, $exercise['thumbnail']);

            // Store muscles involved
            array_push($musclesInvolved, $exercise['muscles_involved']);

            // Get other formats
            foreach($exercise['formats'] as $format){
//                if($format['type'] === 'gif'){
//                    array_push($gif, $format['source']);
//                }
                if($format['type'] === 'mp4'){
                    array_push($mp4, $format['source']);
                }
            }
        }
        
        // Store packages to folder
        foreach($package_images as $image){
            
            $name = explode("/",$image);
            $name = sizeof($name);
            $name = explode("/",$image)[$name-1];
            
            // find -
            $rawNameEnd = strpos($name, '-');
            
            // check if there is more - than one
            if(strrpos($name, '-', $rawNameEnd)){
                // if yes set new raw name end
                $rawNameEnd = strrpos($name, '-', $rawNameEnd);
            }
            
            $rawName = substr($name, 0, $rawNameEnd);
            
            // extract extension
            $extStart = strpos($name, '.');
            $ext = substr($name, $extStart);
            
            $newName = $rawName . $ext;
            
            $content = file_get_contents($image);
            //Store in the filesystem.
            $fp = fopen("mobile_data/packages/".$newName, "w") or die("Unable to open file!");
            fwrite($fp, $content);
            fclose($fp);
        }


        // Store images to folder
        foreach($images as $image){

            $name = explode("/",$image);
            $name = sizeof($name);
            $name = explode("/",$image)[$name-1];

            // find -
            $rawNameEnd = strpos($name, '-');

            // check if there is more - than one
            if(strrpos($name, '-', $rawNameEnd)){
                // if yes set new raw name end
                $rawNameEnd = strrpos($name, '-', $rawNameEnd);
            }

            $rawName = substr($name, 0, $rawNameEnd);

            // extract extension
            $extStart = strpos($name, '.');
            $ext = substr($name, $extStart);

            $newName = $rawName . $ext;

            $content = file_get_contents($image);
            //Store in the filesystem.
            $fp = fopen("mobile_data/images/".$newName, "w") or die("Unable to open file!");
            fwrite($fp, $content);
            fclose($fp);
        }


        // Store thumbnails to folder
        foreach($thumbnails as $thumbnail){

            $name = explode("/",$thumbnail);
            $name = sizeof($name);
            $name = explode("/",$thumbnail)[$name-1];

            // find -
            $rawNameEnd = strpos($name, '-');

            // check if there is more - than one
            if(strrpos($name, '-', $rawNameEnd)){
                // if yes set new raw name end
                $rawNameEnd = strrpos($name, '-', $rawNameEnd);
            }

            $rawName = substr($name, 0, $rawNameEnd);

            // extract extension
            $extStart = strpos($name, '.');
            $ext = substr($name, $extStart);

            $newName = $rawName . $ext;

            //Get the file
            $content = file_get_contents($thumbnail);

            //Store in the filesystem.
            $fp = fopen("mobile_data/thumbnails/".$newName, "w") or die("Unable to open file!");
            fwrite($fp, $content);
            fclose($fp);
        }


        // Store muscles involved to folder
        foreach($musclesInvolved as $musclesInvolved){
            $name = explode("/",$musclesInvolved);
            $name = sizeof($name);
            $name = explode("/",$musclesInvolved)[$name-1];

            // find -
            $rawNameEnd = strpos($name, '-');

            // check if there is more - than one
            if(strrpos($name, '-', $rawNameEnd)){
                // if yes set new raw name end
                $rawNameEnd = strrpos($name, '-', $rawNameEnd);
            }

            if($mobile == 'ios'){
                $rawNameEnd = $rawNameEnd + 1;
            }

            $rawName = substr($name, 0, $rawNameEnd);

            // extract extension
            $extStart = strpos($name, '.');
            $ext = substr($name, $extStart);

            $newName = $rawName . $ext;

            //Get the file
            $content = file_get_contents($musclesInvolved);


            //Store in the filesystem.
            $fp = fopen("mobile_data/muscles_involved/".$newName, "w") or die("Unable to open file!");
            fwrite($fp, $content);
            fclose($fp);
        }

        // Store gif to folder
//        foreach($gif as $gif){
//            $name = explode("/",$gif);
//            $name = sizeof($name);
//            $name = explode("/",$gif)[$name-1];
//
//            // find -
//            $rawNameEnd = strpos($name, '-');
//
//            // check if there is more - than one
//            if(strrpos($name, '-', $rawNameEnd)){
//                // if yes set new raw name end
//                $rawNameEnd = strrpos($name, '-', $rawNameEnd);
//            }
//
//            $rawName = substr($name, 0, $rawNameEnd);
//
//            // extract extension
//            $extStart = strpos($name, '.');
//            $ext = substr($name, $extStart);
//
//            $newName = $rawName . $ext;
//
//            //Get the file
//            $content = file_get_contents($gif);
//
//            //Store in the filesystem.
//            $fp = fopen("mobile_data/gifs/".$newName, "w") or die("Unable to open file!");
//            fwrite($fp, $content);
//            fclose($fp);
//        }

        // Store mp4 to folder
        foreach($mp4 as $mp4){
            $name = explode("/",$mp4);
            $name = sizeof($name);
            $name = explode("/",$mp4)[$name-1];

            // find -
            $rawNameEnd = strpos($name, '-');

            // check if there is more - than one
            if(strrpos($name, '-', $rawNameEnd)){
                // if yes set new raw name end
                $rawNameEnd = strrpos($name, '-', $rawNameEnd);
            }

            $rawName = substr($name, 0, $rawNameEnd);

            // extract extension
            $extStart = strpos($name, '.');
            $ext = substr($name, $extStart);

            $newName = $rawName . $ext;

            //Get the file
            $content = file_get_contents($mp4);

            //Store in the filesystem.
            $fp = fopen("mobile_data/mp4/".$newName, "w") or die("Unable to open file!");
            fwrite($fp, $content);
            fclose($fp);
        }


        // create zip file and add folder to it
        $zip = new \ZipArchive();

        $zipFile = 'mobile_data/data_' . $app . '.zip';

        $zip->open($zipFile, \ZipArchive::CREATE);
        foreach (glob("mobile_data/thumbnails/*") as $file) {
            $zip->addFile($file);
        }
        foreach (glob("mobile_data/images/*") as $file) {
            $zip->addFile($file);
        }
        foreach (glob("mobile_data/packages/*") as $file) {
            $zip->addFile($file);
        }
        foreach (glob("mobile_data/muscles_involved/*") as $file) {
            $zip->addFile($file);
        }
//        foreach (glob("mobile_data/gifs/*") as $file) {
//            $zip->addFile($file);
//        }
        foreach (glob("mobile_data/mp4/*") as $file) {
            $zip->addFile($file);
        }

        // add json response
        $zip->addFile('mobile_data/data.json');


        $zip->close();

        // delete all files from the folders
        $files = glob('mobile_data/thumbnails/*'); //get all file names
        foreach($files as $file){
            unlink($file); //delete file
        }
        
        // delete all files from the folders
        $files = glob('mobile_data/packages/*'); //get all file names
        foreach($files as $file){
            unlink($file); //delete file
        }
        // delete all files from the folders
        $files = glob('mobile_data/images/*'); //get all file names
        foreach($files as $file){
            unlink($file); //delete file
        }
        $files = glob('mobile_data/muscles_involved/*'); //get all file names
        foreach($files as $file){
            unlink($file); //delete file
        }
//        $files = glob('mobile_data/gifs/*'); //get all file names
//        foreach($files as $file){
//            unlink($file); //delete file
//        }
        $files = glob('mobile_data/mp4/*'); //get all file names
        foreach($files as $file){
            unlink($file); //delete file
        }


        // download zip file
        if (file_exists($zipFile)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($zipFile).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($zipFile));
            readfile($zipFile);
        }

        // delete zip file
        unlink($zipFile);
        // delete data rom data.json
        file_put_contents("mobile_data/data.json", "");

        // create response object
        $response = new ResponseBootstrap();
        $response->setStatus(200);
    }

}