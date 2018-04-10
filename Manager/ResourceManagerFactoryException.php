<?php

namespace MNC\Bundle\RestBundle\Manager;

/**
 * Class ResourceManagerFactoryException
 * @package MNC\Bundle\RestBundle\Manager
 */
class ResourceManagerFactoryException extends \Exception
{
    /**
     * @param $id
     * @return ResourceManagerFactoryException
     */
    public static function managerIdDoesNotExist(string $id)
    {
        return new self(
            sprintf('Unable to fecth the resource manager with id %s. It does not exist.', $id)
        );
    }
}