<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 7/18/18
 * Time: 9:21 AM
 */

namespace Model\Mapper;

use Component\DataMapper;

class UploadMapper extends DataMapper
{

    public function getConfiguration()
    {
        return $this->configuration;
    }
}