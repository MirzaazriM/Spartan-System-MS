<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 7/16/18
 * Time: 7:26 PM
 */

namespace Application\Controller;


use Model\Service\MigrationService;

class MigrationController
{

    private $migrationService;

    public function __construct(MigrationService $migrationService)
    {
        $this->migrationService = $migrationService;
    }


    public function get(){

        $this->migrationService->startMigration();
    }
}