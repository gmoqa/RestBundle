<?php

namespace MNC\Bundle\RestBundle\EntityFactory;

use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use MNC\Bundle\RestBundle\Doctrine\Fixtures\FixtureCollection;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * This class is responsible for creating collections of items from Entity Factories.
 * @package MNC\Bundle\RestBundle\EntityFactory
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
class EntityFactory implements EntityFactoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var FactoryDefinitionLoader
     */
    private $loader;
    /**
     * @var \Faker\Generator
     */
    private $faker;
    /**
     * @var PropertyAccessor
     */
    private $pa;
    /**
     * @var FactoryDefinitionInterface[]
     */
    private $definitions = [];

    public function __construct(EntityManagerInterface $manager, FactoryDefinitionLoader $loader)
    {
        $this->manager = $manager;
        $this->loader = $loader;
        $this->faker = Factory::create();
        $this->pa = PropertyAccess::createPropertyAccessor();
    }

    public function loadDefinitions()
    {
        /** @var FactoryDefinitionInterface[] $definitions */
        $definitions = $this->loader->getLoadedDefinitions();
        foreach ($definitions as $definition)
        {
            $this->definitions[$definition->getEntityClassName()] = $definition;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function make($classname, $number, callable $callable = null)
    {
        if (!array_key_exists($classname, $this->definitions)) {
            throw EntityFactoryException::definitionDoesNotExist($classname);
        }

        $definition = $this->definitions[$classname];

        // If is just one record, we just return a single object.
        if ($number === 1) {
            $data = $definition->getData($this->faker);
            return $this->createObject($classname, $data, $callable);
        }

        // Now we are dealing with a collection.
        $collection = [];

        for ($i = 1; $i <= $number; $i++) {
            $data = $definition->getData($this->faker);
            $collection[] = $this->createObject($classname, $data, $callable);
        }

        $collection = new FixtureCollection($collection);

        return $collection;
    }

    /**
     * @param $object
     * @param $callable
     * @return mixed
     */
    private function applyCallback($callable, &$object)
    {
        call_user_func_array($callable, [&$object]);
        return $object;
    }

    /**
     * @param                  $classname
     * @param                  $data
     * @param callable         $callable
     * @return mixed
     * @throws EntityFactoryException
     * @throws \TypeError
     */
    private function createObject($classname, $data, callable $callable = null)
    {
        $object = new $classname;
        foreach ($data as $prop => $value) {
            if (!$this->pa->isWritable($object, $prop)) {
                throw EntityFactoryException::propertyNotWritable($prop, $classname);
            }
            $this->pa->setValue($object, $prop, $value);
        }
        if ($callable !== null) {
            $object = $this->applyCallback($callable, $object);
        }
        return $object;
    }
}