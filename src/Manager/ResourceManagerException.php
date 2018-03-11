<?php

namespace MNC\Bundle\RestBundle\Manager;

/**
 * Class ResourceManagerException
 * @package MNC\Bundle\RestBundle\Manager
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class ResourceManagerException extends \Exception
{
    /**
     * @return ResourceManagerException
     */
    public static function cannotShowMultipleResources()
    {
        return new self('Cannot call multiple comma separated resources here.');
    }
}