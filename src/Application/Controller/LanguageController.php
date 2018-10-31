<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 6/28/18
 * Time: 11:35 AM
 */

namespace Application\Controller;


use Model\Entity\ResponseBootstrap;
use Model\Service\LanguageService;
use Symfony\Component\HttpFoundation\Request;

class LanguageController
{

    private $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }


    /**
     * Get language by id or all
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function get(Request $request):ResponseBootstrap {

        // get id if exists
        $id = $request->get('id');

        // check which service to call
        if(isset($id)){
            return $this->languageService->getLanguage($id);
        }else {
            return $this->languageService->getLanguages();
        }

    }


    /**
     * Delete language
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function delete(Request $request):ResponseBootstrap {
        // get data
        $id = $request->get('id');

        // create response object
        $response = new ResponseBootstrap();

        // check if data is set
        if(isset($id)){
            return $this->languageService->deleteLanguage($id);
        }else {
           $response->setStatus(404);
           $response->setMessage('Bad request');
        }

        // return response
        return $response;
    }


    /**
     * Add language
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function post(Request $request):ResponseBootstrap {
        // get data
        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $iso = $data['iso'];

        // create response object in case of failure
        $response = new ResponseBootstrap();

        if(isset($name) && isset($iso)){
            return $this->languageService->addLanguage($name, $iso);
        }else {
            $response->setStatus(404);
            $response->setMessage('Bad request');
        }

        return $response;
    }


    /**
     * Edit language
     *
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function put(Request $request):ResponseBootstrap {

        $data = json_decode($request->getContent(), true);
        $id = $data['id'];
        $name = $data['name'];
        $iso = $data['iso'];

        // create response object in case of failure
        $response = new ResponseBootstrap();

        if(isset($id) && isset($name) && isset($iso)){
            return $this->languageService->editLanguage($id, $name, $iso);
        }else {
            $response->setStatus(404);
            $response->setMessage('Bad request');
        }

        return $response;
    }

}