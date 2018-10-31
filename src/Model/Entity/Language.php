<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 6/28/18
 * Time: 11:11 AM
 */

namespace Model\Entity;


use Model\Contract\HasId;

class Language implements  HasId
{

    private $id;
    private $name;
    private $iso;


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getIso()
    {
        return $this->iso;
    }

    /**
     * @param mixed $iso
     */
    public function setIso($iso): void
    {
        $this->iso = $iso;
    }


}