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
     * @return string
     */
    public function defineName() : string;

    /**
     * @param Generator $factory
     * @return array
     */
    public function defineStructure(Generator $factory) : array;
}