<?php

namespace MNC\Bundle\RestBundle\EntityFactory;

/**
 * This class defines an interface.
 * @package MNC\Bundle\RestBundle\EntityFactory
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
interface EntityFactoryInterface
{
    /**
     * Registers a entity factory definition.
     * @param          $className
     * @param callable $callable
     * @throws EntityFactoryException If the class does not exist.
     * @return mixed
     */
    public function define($className, callable $callable);

    /**
     * Creates a factory collection.
     * @param               $classname
     * @param int           $number The number of elements to make.
     * @param callable|null $callable A callback to be applied to each element.
     * @return mixed
     */
    public function make($classname, $number, callable $callable = null);
}