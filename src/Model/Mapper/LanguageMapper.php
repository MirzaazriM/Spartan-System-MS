<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 6/28/18
 * Time: 11:36 AM
 */

namespace Model\Mapper;

use Model\Entity\Language;
use Model\Entity\LanguageCollection;
use Model\Entity\Shared;
use PDO;
use PDOException;
use Component\DataMapper;

class LanguageMapper extends DataMapper
{

    public function getConfiguration()
    {
        return $this->configuration;
    }


    /**
     * Fetch specified language
     *
     * @param Language $language
     * @return Language
     */
    public function getLanguage(Language $language):Language {

        // create response object
        $response = new Language();

        try {
            // set database instructions
            $sql = "SELECT * FROM language WHERE id = ?";
            $statement = $this->connection->prepare($sql);
            $statement->execute([
                $language->getId()
            ]);

            // fetch data
            $data = $statement->fetch();
            //die(print_r($data));

            // set response  values
            $response->setId($data['id']);
            $response->setName($data['name']);
            $response->setIso($data['code']);

        }catch(PDOException $e){
            // send monolog record
            $this->monologHelper->sendMonologRecord($this->configuration, $e->errorInfo[1], "Get language mapper: " . $e->getMessage());
        }

        return $response;
    }


    /**
     * Delete language
     *
     * @param Language $language
     * @param Shared $shared
     * @return Shared
     */
    public function deleteLanguage(Language $language):int {

        try {
            // set database instructions
            $sql = "DELETE FROM language WHERE id = ?";
            $statement = $this->connection->prepare($sql);
            $statement->execute([
                $language->getId()
            ]);

            // set status code
            if($statement->rowCount() > 0){
                $code = 200;
            }else {
                $code = 304;
            }

        }catch (PDOException $e){
            $code = 304;

            // send monolog record
            $this->monologHelper->sendMonologRecord($this->configuration, $e->errorInfo[1], "Delete language mapper: " . $e->getMessage());
        }

        // return response
        return $code;
    }


    /**
     * Get all languages mapper
     *
     * @param Language $language
     * @return LanguageCollection
     */
    public function getLanguages(Language $language):LanguageCollection {

        // response object
        $collection = new LanguageCollection();

        try {

            $sql = "SELECT * FROM language";
            $statement = $this->connection->prepare($sql);
            $statement->execute();

            // fetch data
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);

            // set response values
            foreach($data as $lang){
                // create language object
                $language = new Language();

                $language->setId($lang['id']);
                $language->setName($lang['name']);
                $language->setIso($lang['code']);

                $collection->addEntity($language);
            }

            if($statement->rowCount() > 0){
                $collection->setStatusCode(200);
            }else {
                $collection->setStatusCode(204);
            }

        }catch(PDOException $e){
            $collection->setStatusCode(204);
            // send monolog record
            $this->monologHelper->sendMonologRecord($this->configuration, $e->errorInfo[1], "Get languages mapper: " . $e->getMessage());
        }

        return $collection;
    }


    /**
     * Add language mapper
     *
     * @param Language $language
     * @return Shared
     */
    public function addLanguage(Language $language):Shared {

        // create response object
        $response = new Shared();

        try {

            // insert language
            $sql = "INSERT INTO language (name, code) VALUES (?,?)";
            $statement = $this->connection->prepare($sql);
            $statement->execute([
                $language->getName(),
                $language->getIso()
            ]);

            // set response
            if($statement->rowCount() > 0){
                $response->setResponse([200]);
            }else {
                $response->setResponse([304]);
            }

        }catch(PDOException $e){
            $response->setResponse([304]);
            // send monolog record
            $this->monologHelper->sendMonologRecord($this->configuration, $e->errorInfo[1], "Add language mapper: " . $e->getMessage());
        }

        return $response;
    }


    /**
     * Edit language mapper
     *
     * @param Language $language
     * @return Shared
     */
    public function editLanguage(Language $language):Shared {
        // create response object
        $response = new Shared();

        try {

            $sql = "UPDATE language SET name = ?, code = ? WHERE id = ?";
            $statement = $this->connection->prepare($sql);
            $statement->execute([
                $language->getName(),
                $language->getIso(),
                $language->getId()
            ]);

            // set response
            if($statement->rowCount() > 0){
                $response->setResponse([200]);
            }else {
                $response->setResponse([304]);
            }

        }catch(PDOException $e){
            $response->setResponse([304]);
            // send monolog record
            $this->monologHelper->sendMonologRecord($this->configuration, $e->errorInfo[1], "Edit language mapper: " . $e->getMessage());
        }

        return $response;
    }

}