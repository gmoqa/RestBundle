<?php

namespace MNC\Bundle\RestBundle\EntityFactory;

/**
 * Class EntityFactoryException
 * @package MNC\Bundle\RestBundle\EntityFactory
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class EntityFactoryException extends \Exception
{
    /**
     * @param $classname
     * @return EntityFactoryException
     */
    public static function classDoesNotExist($classname)
    {
        return new self(sprintf('Cannot create a factory for %s beacuse class does not exist.', $classname));
    }

    public static function definitionDoesNotExist($classname)
    {
        return new self(sprintf('No definition found for %s class.', $classname));
    }

    public static function propertyNotWritable($prop, $classname)
    {
        return new self(sprintf('Cannot write in %s property of %s class.', $prop, $classname));
    }
}