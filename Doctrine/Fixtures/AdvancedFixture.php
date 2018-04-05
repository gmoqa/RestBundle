<?php

namespace MNC\Bundle\RestBundle\Doctrine\Fixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use MNC\Bundle\RestBundle\EntityFactory\EntityFactory;

/**
 * This provides some functionality to create fixtures more efficiently, faking
 * some data and improving entity management and collection reference.
 * @package RestBundle
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
abstract class AdvancedFixture extends Fixture
{
    /**
     * @var FixtureCollection[]
     */
    protected $collectionRepository = [];
    /**
     * @var EntityFactory
     */
    private $factory;

    /**
     * AdvancedFixture constructor.
     * @param EntityFactory $factory
     */
    public function __construct(EntityFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param                        $classname
     * @param                        $number
     * @param callable|null          $callback
     * @return FixtureCollection
     * @throws \MNC\Bundle\RestBundle\EntityFactory\EntityFactoryException
     */
    public function make($classname, $number, callable $callback = null)
    {
        return $this->factory->make($classname, $number, $callback);
    }

    /**
     * @param ObjectManager          $manager
     * @param                        $class
     * @return FixtureCollection
     */
    public function getCollection(ObjectManager $manager, $class)
    {
        $repo = $manager->getRepository($class);
        $items = $repo->findAll();
        return new FixtureCollection($items);
    }

    /**
     * @param                        $collection
     * @param ObjectManager          $manager
     * @param bool                   $flush
     */
    public function persistCollection($collection, ObjectManager $manager, $flush = true)
    {
        foreach ($collection as $item) {
            $manager->persist($item);
        }
        if ($flush) {
            $manager->flush();
        }
    }
}