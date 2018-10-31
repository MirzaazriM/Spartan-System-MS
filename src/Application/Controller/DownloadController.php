<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 7/26/18
 * Time: 12:04 PM
 */

namespace Application\Controller;


use Model\Entity\ResponseBootstrap;
use Model\Service\DownloadService;
use Symfony\Component\HttpFoundation\Request;

class DownloadController
{

    private $downloadService;

    public function __construct(DownloadService $downloadService)
    {
        $this->downloadService = $downloadService;
    }


    public function get(Request $request):ResponseBootstrap {
        // get data
        $app = $request->get('app');
        $type = $request->get('type');
        $version = $request->get('version');
        $lang = $request->get('lang');
        $mobile = $request->get('mobile');

        // create response object
        $response = new ResponseBootstrap();

        if(isset($app) && isset($type) && isset($version) && isset($lang) && isset($mobile)){
            //$mobile = strtolower($mobile);
            if($mobile == 'android' or $mobile == 'ios'){
                return $this->downloadService->getData($app, $type, $version, $lang, $mobile);
            }else {
                $response->setStatus(404);
                $response->setMessage('Bad request');
            }

        }else {
            $response->setStatus(404);
            $response->setMessage('Bad request');
        }

        // return response
        return $response;
    }
}