<?php

namespace MNC\Bundle\RestBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * Trait EntityManagerWrapperTrait
 * @package MNC\Bundle\RestBundle\Manager
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
trait EntityManagerWrapperTrait
{
    /**
     * @var ObjectManager
     */
    private $em;

    public function getEntityManager()
    {
        return $this->em;
    }

    public function find($className, $id)
    {
        return $this->em->find($className, $id);
    }

    public function persist($object)
    {
        return $this->em->persist($object);
    }

    public function remove($object)
    {
        return $this->em->remove($object);
    }

    public function merge($object)
    {
        return $this->em->merge($object);
    }

    public function clear($objectName = null)
    {
        return $this->em->clear($objectName = null);
    }

    public function detach($object)
    {
        return $this->em->detach($object);
    }

    public function refresh($object)
    {
        return $this->em->refresh($object);
    }

    public function flush()
    {
        return $this->em->flush();
    }

    public function getRepository($className)
    {
        return $this->em->getRepository($className);
    }

    public function getClassMetadata($className)
    {
        return $this->em->getClassMetadata($className);
    }

    public function getMetadataFactory()
    {
        return $this->em->getMetadataFactory();
    }

    public function initializeObject($obj)
    {
        return $this->initializeObject($obj);
    }

    public function contains($object)
    {
        return $this->em->contains($object);
    }
}