<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 6/28/18
 * Time: 11:35 AM
 */

namespace Model\Service;


use Model\Core\Helper\Monolog\MonologSender;
use Model\Entity\Language;
use Model\Entity\ResponseBootstrap;
use Model\Entity\Shared;
use Model\Mapper\LanguageMapper;

class LanguageService
{

    private $languageMapper;
    private $configuration;
    private $monologHelper;

    public function __construct(LanguageMapper $languageMapper)
    {
        $this->languageMapper = $languageMapper;
        $this->configuration = $languageMapper->getConfiguration();
        $this->monologHelper = new MonologSender();
    }


    /**
     * Get language
     *
     * @param int $id
     * @return ResponseBootstrap
     */
    public function getLanguage(int $id):ResponseBootstrap {

        try {
            // create response object
            $response = new ResponseBootstrap();

            // create entity and set its values
            $entity = new Language();
            $entity->setId($id);

            $data = $this->languageMapper->getLanguage($entity);

            if(!empty($data->getId())){
                $response->setStatus(200);
                $response->setMessage('Success');
                $response->setData(
                    [
                        'id' => $data->getId(),
                        'name' => $data->getName(),
                        'iso' => $data->getIso()
                    ]
                );
            }else {
                $response->setStatus(204);
                $response->setMessage('No content');
            }

            // return data
            return $response;

        }catch (\Exception $e){
            // send monolog record
            $this->monologHelper->sendMonologRecord($this->configuration, 1000, "Get language service: " . $e->getMessage());

            $response->setStatus(404);
            $response->setMessage('Invalid data');
            return $response;
        }
    }


    /**
     * Delete language
     *
     * @param int $id
     * @return ResponseBootstrap
     */
    public function deleteLanguage(int $id):ResponseBootstrap {

        try {
            // create response object
            $response = new ResponseBootstrap();

            // create entity and set its values
            $entity = new Language();
            $entity->setId($id);

            // call mapper
            $data = $this->languageMapper->deleteLanguage($entity);

            // set response
            if($data == 200){
                $response->setStatus(200);
                $response->setMessage('Success');
            }else {
                $response->setStatus(304);
                $response->setMessage('Not modified');
            }

            // return data
            return $response;

        }catch (\Exception $e){
            // send monolog record
            $this->monologHelper->sendMonologRecord($this->configuration, 1000, "Delete language service: " . $e->getMessage());

            $response->setStatus(404);
            $response->setMessage('Invalid data');
            return $response;
        }
    }


    /**
     * Get all languages
     *
     * @return ResponseBootstrap
     */
    public function getLanguages():ResponseBootstrap {

        try {
            // create response object
            $response = new ResponseBootstrap();

            // create entity and set its values
            $entity = new Language();

            // get response
            $res = $this->languageMapper->getLanguages($entity);

            // convert data to array for appropriate response
            $data = [];

            for($i = 0; $i < count($res); $i++){
                $data[$i]['id'] = $res[$i]->getId();
                $data[$i]['name'] = $res[$i]->getName();
                $data[$i]['iso'] = $res[$i]->getIso();
            }

            // check data and set response
            if($res->getStatusCode() == 200){
                $response->setStatus(200);
                $response->setMessage('Success');
                $response->setData(
                    $data
                );
            }else {
                $response->setStatus(204);
                $response->setMessage('No content');
            }

            // return data
            return $response;

        }catch (\Exception $e){
            // send monolog record
            $this->monologHelper->sendMonologRecord($this->configuration, 1000, "Get languages service: " . $e->getMessage());

            $response->setStatus(404);
            $response->setMessage('Invalid data');
            return $response;
        }
    }


    /**
     * Add language service
     *
     * @param string $name
     * @param string $iso
     * @return ResponseBootstrap
     */
    public function addLanguage(string $name, string $iso):ResponseBootstrap {

        try {
            // create response object
            $response = new ResponseBootstrap();

            // create entity and set its values
            $entity = new Language();
            $entity->setName($name);
            $entity->setIso($iso);

            $data = $this->languageMapper->addLanguage($entity);

            if($data->getResponse()[0] == 200){
                $response->setStatus(200);
                $response->setMessage('Success');
            }else {
                $response->setStatus(304);
                $response->setMessage('Not modified');
            }

            // return response
            return $response;

        }catch (\Exception $e){
            // send monolog record
            $this->monologHelper->sendMonologRecord($this->configuration, 1000, "Add language service: " . $e->getMessage());

            $response->setStatus(404);
            $response->setMessage('Invalid data');
            return $response;
        }
    }


    /**
     * Edit language service
     *
     * @param int $id
     * @param string $name
     * @param string $iso
     * @return ResponseBootstrap
     */
    public function editLanguage(int $id, string $name, string $iso):ResponseBootstrap {

        try {
            // create response object
            $response = new ResponseBootstrap();

            // create entity and set its values
            $entity = new Language();
            $entity->setId($id);
            $entity->setName($name);
            $entity->setIso($iso);

            $data = $this->languageMapper->editLanguage($entity);

            if($data->getResponse()[0] == 200){
                $response->setStatus(200);
                $response->setMessage('Success');
            }else {
                $response->setStatus(304);
                $response->setMessage('Not modified');
            }

            // return response
            return $response;

        }catch (\Exception $e){
            // send monolog record
            $this->monologHelper->sendMonologRecord($this->configuration, 1000, "Edit language service: " . $e->getMessage());

            $response->setStatus(404);
            $response->setMessage('Invalid data');
            return $response;
        }
    }

}