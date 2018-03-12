<?php

namespace MNC\Bundle\RestBundle\EntityFactory;

use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use MNC\Bundle\RestBundle\Doctrine\Fixtures\FixtureCollection;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class EntityFactory
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
     * @var array
     */
    private $definitions = [];
    /**
     * @var FactoryDefinitionLoader
     */
    private $loader;

    public function __construct(EntityManagerInterface $manager, FactoryDefinitionLoader $loader)
    {
        $this->manager = $manager;
        $this->loader = $loader;
    }

    /**
     * @throws EntityFactoryException
     */
    public function loadDefinitions()
    {
        /** @var FactoryDefinitionInterface[] $definitions */
        $definitions = $this->loader->getLoadedDefinitions();
        foreach ($definitions as $definition) {
            $this->define($definition->defineName(), function ($faker) use ($definition) {
                return $definition->defineStructure($faker);
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public function define($className, callable $callable)
    {
        if (!class_exists($className)) {
            throw EntityFactoryException::classDoesNotExist($className);
        }
        $this->definitions[$className] = $callable;
    }

    /**
     * {@inheritdoc}
     */
    public function make($classname, $number, callable $callable = null)
    {
        if (!array_key_exists($classname, $this->definitions)) {
            throw EntityFactoryException::definitionDoesNotExist($classname);
        }

        $collection = [];

        $faker = Factory::create();
        $pa = PropertyAccess::createPropertyAccessor();

        if ($number === 1) {
            $data = $this->call($classname, $faker);
            return $this->createObject($classname, $data, $callable, $pa);
        }

        for ($i = 1; $i <= $number; $i++) {
            $data = $this->call($classname, $faker);
            $collection[] = $this->createObject($classname, $data, $callable, $pa);
        }
        $collection = new FixtureCollection($collection);
        return $collection;
    }

    /**
     * @param $classname
     * @param $faker
     * @return mixed
     */
    private function call($classname, $faker)
    {
        return call_user_func_array($this->definitions[$classname], [$faker]);
    }

    /**
     * @param $object
     * @param $callable
     * @return mixed
     */
    private function applyCallback($callable, $object)
    {
        call_user_func_array($callable, [$object]);
        return $object;
    }

    /**
     * @param                  $classname
     * @param                  $data
     * @param                  $callable
     * @param PropertyAccessor $pa
     * @return mixed
     * @throws EntityFactoryException
     * @throws \TypeError
     */
    private function createObject($classname, $data, callable $callable, PropertyAccessor $pa)
    {
        $object = new $classname;
        foreach ($data as $prop => $value) {
            if (!$pa->isWritable($object, $prop)) {
                throw EntityFactoryException::propertyNotWritable($prop, $classname);
            }
            $pa->setValue($object, $prop, $value);
        }
        if ($callable !== null) {
            $object = $this->applyCallback($callable, $object);
        }
        return $object;
    }
}