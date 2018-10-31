<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 6/28/18
 * Time: 9:02 AM
 */

namespace Application\Controller;

use Model\Entity\ResponseBootstrap;
use Model\Service\SystemService;
use Symfony\Component\HttpFoundation\Request;

class SystemController
{

    private $systemService;

    public function __construct(SystemService $systemService)
    {
        $this->systemService = $systemService;
    }

    public function get(Request $request):ResponseBootstrap {

        die("get");
    }
    public function post(Request $request):ResponseBootstrap {
        die("post");
    }
    public function put(Request $request):ResponseBootstrap {
        die("put");
    }
    public function delete(Request $request):ResponseBootstrap {
        die("delete");
    }

}