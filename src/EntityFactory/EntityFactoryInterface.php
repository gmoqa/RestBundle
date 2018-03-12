<?php

namespace MNC\Bundle\RestBundle\EntityFactory;
use MNC\Bundle\RestBundle\Doctrine\Fixtures\FixtureCollection;

/**
 * This class defines an interface.
 * @package MNC\Bundle\RestBundle\EntityFactory
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
interface EntityFactoryInterface
{
    /**
     * Creates a fixture collection from a entity factory.
     * @param               $classname
     * @param int           $number The number of elements to make.
     * @param callable|null $callable A callback to be applied to each element.
     * @return object|FixtureCollection
     */
    public function make($classname, $number, callable $callable = null);

    /**
     * Loads the factory definitions into the service.
     * @return void
     */
    public function loadDefinitions();
}