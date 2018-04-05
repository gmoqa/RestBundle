<?php

namespace MNC\Bundle\RestBundle\EntityFactory;
use Faker\Generator;

/**
 * Interface FactoryDefinitionInterface
 * @package MNC\Bundle\RestBundle\EntityFactory
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
interface FactoryDefinitionInterface
{
    /**
     * Returns the name of the Entity this class define a structure for.
     * @return string
     */
    public function getEntityClassName() : string;

    /**
     * Returns the defintion for this factory. It must be an array whose keys map
     * to the class properties required.
     * @param Generator $factory
     * @return array
     */
    public function getData(Generator $factory) : array;
}