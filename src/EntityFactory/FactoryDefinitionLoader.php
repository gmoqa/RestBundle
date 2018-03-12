<?php

namespace MNC\Bundle\RestBundle\EntityFactory;

/**
 * Class FactoryDefinitionLoader
 * @package MNC\Bundle\RestBundle\EntityFactory
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class FactoryDefinitionLoader
{
    /**
     * @var array
     */
    private $loadedDefinitions = [];

    /**
     * @param array $definitions
     */
    public function addDefinitions(array $definitions)
    {
        // Store all loaded factory definitions so that we can resolve the dependencies correctly.
        foreach ($definitions as $definition) {
            $this->loadedDefinitions[get_class($definition)] = $definition;
        }

        // Now load all the factory definitions
        foreach ($this->loadedDefinitions as $definition) {
            $this->addDefinition($definition);
        }
    }

    /**
     * @param FactoryDefinitionInterface $definition
     */
    private function addDefinition(FactoryDefinitionInterface $definition)
    {
        $class = get_class($definition);
        if (!isset($this->loadedDefinitions[$class])) {
            $this->loadedDefinitions[$class] = $definition;
        }
    }

    /**
     * @return array
     */
    public function getLoadedDefinitions()
    {
        return $this->loadedDefinitions;
    }
}