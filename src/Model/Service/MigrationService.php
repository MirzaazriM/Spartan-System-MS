<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 7/16/18
 * Time: 7:27 PM
 */

namespace Model\Service;


use Model\Mapper\MigrationMapper;

class MigrationService
{

    private $migrationMapper;

    public function __construct(MigrationMapper $migrationMapper)
    {
        $this->migrationMapper = $migrationMapper;
    }

    public function startMigration(){
        $this->migrationMapper->migration();
    }
}