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
     * @return ObjectManager
     */
    public function getEntityManager()
    {
        return $this->getManager();
    }

    /**
     * @param $className
     * @param $id
     * @return object
     */
    public function find($className, $id)
    {
        return $this->getEntityManager()->find($className, $id);
    }

    /**
     * @param $object
     * @return void
     */
    public function persist($object)
    {
        $this->getEntityManager()->persist($object);
    }

    /**
     * @param $object
     * @return void
     */
    public function remove($object)
    {
        $this->getEntityManager()->remove($object);
    }

    /**
     * @param $object
     * @return object
     */
    public function merge($object)
    {
        return $this->getEntityManager()->merge($object);
    }

    /**
     * @param null $objectName
     * @return void
     */
    public function clear($objectName = null)
    {
        $this->getEntityManager()->clear($objectName = null);
    }

    /**
     * @param $object
     * @return void
     */
    public function detach($object)
    {
        $this->getEntityManager()->detach($object);
    }

    /**
     * @param $object
     * @return void
     */
    public function refresh($object)
    {
        $this->getEntityManager()->refresh($object);
    }

    /**
     * @return void
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @param $className
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository($className)
    {
        return $this->getEntityManager()->getRepository($className);
    }

    /**
     * @param $className
     * @return \Doctrine\Common\Persistence\Mapping\ClassMetadata
     */
    public function getClassMetadata($className)
    {
        return $this->getEntityManager()->getClassMetadata($className);
    }

    /**
     * @return \Doctrine\Common\Persistence\Mapping\ClassMetadataFactory
     */
    public function getMetadataFactory()
    {
        return $this->getEntityManager()->getMetadataFactory();
    }

    /**
     * @param $obj
     * @return void
     */
    public function initializeObject($obj)
    {
        $this->getEntityManager()->initializeObject($obj);
    }

    /**
     * @param $object
     * @return bool
     */
    public function contains($object)
    {
        return $this->getEntityManager()->contains($object);
    }
}