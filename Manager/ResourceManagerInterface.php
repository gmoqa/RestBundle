<?php

namespace MNC\Bundle\RestBundle\Manager;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use League\Fractal\TransformerAbstract;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface ResourceManagerInterface
 * @package MNC\Bundle\RestBundle\Manager
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
interface ResourceManagerInterface
{
    /**
     * @return string
     */
    public function getEntityClass();

    /**
     * @return string
     */
    public function getTransformerClass();

    /**
     * @return TransformerAbstract
     */
    public function getTransformer();

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @return string
     */
    public function getFormClass();

    /**
     * Returns the Doctrine Registry.
     * @return ManagerRegistry
     */
    public function getDoctrine();

    /**
     * Returns the ObjectManager for this class.
     * @return ObjectManager
     */
    public function getManager();

    /**
     * Returns this manager's class repository.
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository();

    /**
     * Finds an object by its configured identifier.
     * @param $identifier
     * @return object
     */
    public function find($identifier);

    /**
     * @param $identifier
     * @return mixed
     */
    public function findOne($identifier);

    /**
     * @param array $identifiers
     * @return null
     */
    public function findMany(array $identifiers);

    /**
     * @param array $criteria
     * @return array
     */
    public function findCollectionBy(array $criteria = []);

    /**
     * @param $object
     * @return void
     */
    public function persist($object);

    /**
     * @param object $object
     * @return void
     */
    public function remove($object);

    /**
     * @return void
     */
    public function flush();

    /**
     * @return \Doctrine\Common\Persistence\Mapping\ClassMetadata
     */
    public function getClassMetadata();

    /**
     * @param object $object
     * @return object
     */
    public function merge(object $object);

    /**
     * @param object $object
     * @return bool
     */
    public function contains(object $object);

    /**
     * @param bool $onlyThese
     * @return void
     */
    public function clear($onlyThese = true);

    /**
     * @param object $object
     */
    public function detach(object $object);

    /**
     * @return \Doctrine\Common\Persistence\Mapping\ClassMetadataFactory
     */
    public function getMetadataFactory();

    /**
     * @param object $object
     * @return void
     */
    public function initializeObject(object $object);

    /**
     * @param object $object
     * @return void
     */
    public function refresh(object $object);

    /**
     * Creates a new instance of the entity managed.
     * Populates it's properties if array passed.
     * @param array $props
     * @return mixed
     */
    public function newElement(array $props = []);

    /**
     * Creates a form.
     * @param null $entity
     * @return mixed
     */
    public function createForm($entity = null);

    /**
     * Process a form.
     * @param FormInterface      $form
     * @param UserInterface|null $user
     * @return mixed|null
     */
    public function processForm(FormInterface $form, UserInterface $user = null);

    /**
     * Gets a resource manager by its class name.
     * @param $className
     * @return ResourceManagerInterface|null
     */
    public function getResourceManager($className);

    /**
     * Checks wether a given resource manager exists.
     * @param $className
     * @return bool
     */
    public function resourceManagerExists($className);

    /**
     * Gets a service from the service container.
     * @param $id
     * @return object
     */
    public function get($id);

    /**
     * Gets a parameter from the service container.
     * @param $param
     * @return mixed
     */
    public function getParameter(string $param);
}