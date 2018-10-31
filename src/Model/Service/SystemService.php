<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 6/28/18
 * Time: 9:03 AM
 */

namespace Model\Service;


use Model\Entity\Language;
use Model\Entity\ResponseBootstrap;
use Model\Mapper\SystemMapper;

class SystemService
{

    private $systemMapper;

    public function __construct(SystemMapper $systemMapper)
    {
        $this->systemMapper = $systemMapper;
    }



}