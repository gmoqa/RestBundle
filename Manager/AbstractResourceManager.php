<?php

namespace MNC\Bundle\RestBundle\Manager;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use League\Fractal\TransformerAbstract;
use MNC\Bundle\RestBundle\Security\OwnableInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @package MNC\Bundle\RestBundle\Manager
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
abstract class AbstractResourceManager implements ResourceManagerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * AbstractResourceManager constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns the Doctrine Registry.
     * @return ManagerRegistry
     */
    public function getDoctrine()
    {
        return $this->container->get('doctrine');
    }

    /**
     * Returns the ObjectManager for this class.
     * @return ObjectManager
     */
    public function getManager()
    {
        return $this->getDoctrine()->getManagerForClass($this->getEntityClass());
    }

    /**
     * Returns this manager's class repository.
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository()
    {
        return $this->getManager()->getRepository($this->getEntityClass());
    }

     /**
      * Finds an object by its configured identifier.
      * @param $identifier
      * @return object
      */
    public function find($identifier)
    {
        $identifier = explode(',', $identifier);
        if (sizeof($identifier) === 1) {
            return $this->findOne($identifier[0]);
        }
        return $this->findMany($identifier);
    }

    /**
     * Finds a single object
     * @param $identifier
     * @return mixed
     */
    public function findOne($identifier)
    {
        return $this->getRepository()->findOneBy([$this->getIdentifier() => $identifier]);
    }

    /**
      * @param array $identifiers
      * @return null
      */
    public function findMany(array $identifiers)
    {
        return $this->getRepository()->findBy([$this->getIdentifier() => $identifiers]);
    }

    /**
     * @param array $criteria
     * @return array
     */
    public function findCollectionBy(array $criteria = [])
    {
        /** @var QueryParser $parser */
        $parser = $this->get(QueryParser::class);
        return $this
            ->getRepository()
            ->findBy(
                $criteria,
                $parser->getOrderBy(),
                $parser->getLimit(),
                $parser->getOffset()
            );
    }

    /**
     * @param $object
     * @return void
     */
    public function persist($object)
    {
        $this->getManager()->persist($object);
    }

     /**
      * @param object $object
      * @return void
      */
    public function remove($object)
    {
        $this->getManager()->remove($object);
    }

    /**
     * @return void
     */
    public function flush()
    {
        $this->getManager()->flush();
    }

    /**
     * @return \Doctrine\Common\Persistence\Mapping\ClassMetadata
     */
    public function getClassMetadata()
    {
        return $this->getManager()->getClassMetadata($this->getEntityClass());
    }

    /**
     * @param object $object
     * @return object
     */
    public function merge(object $object)
    {
        return $this->getManager()->merge($object);
    }

    /**
     * @param object $object
     * @return bool
     */
    public function contains(object $object)
    {
        return $this->getManager()->contains($object);
    }

    /**
     * @param bool $onlyThese
     * @return void
     */
    public function clear($onlyThese = true)
    {
        if ($onlyThese) {
            $this->getManager()->clear($this->getEntityClass());
        }
        $this->getManager()->clear($this->getEntityClass());
    }

    /**
     * @param object $object
     */
    public function detach(object $object)
    {
        $this->getManager()->detach($object);
    }

    /**
     * @return \Doctrine\Common\Persistence\Mapping\ClassMetadataFactory
     */
    public function getMetadataFactory()
    {
        return $this->getManager()->getMetadataFactory();
    }

    /**
     * @param object $object
     * @return void
     */
    public function initializeObject(object $object)
    {
        $this->getManager()->initializeObject($object);
    }

    /**
     * @param object $object
     * @return void
     */
    public function refresh(object $object)
    {
        $this->getManager()->refresh($object);
    }

    /**
     * @inheritdoc
     */
    public function newElement(array $props = [])
    {
        $className = $this->getEntityClass();
        $instance = new $className();
        if (!empty($props)) {
            $pa = PropertyAccess::createPropertyAccessor();
            foreach ($props as $name => $value) {
                $pa->setValue($instance, $name, $value);
            }
        }
        return $instance;
    }

    /**
     * @inheritdoc
     */
    public function getFormFactory() : FormFactoryInterface
    {
        return $this->get('form.factory');
    }

    /**
     * @return TransformerAbstract|object
     */
    public function getTransformer()
    {
        return $this->container->get($this->getTransformerClass());
    }

    /**
     * @inheritdoc
     */
    public function createForm($entity = null) : FormInterface
    {
        $groups = ['Default'];
        if ($entity === null) {
            $groups[] = 'New';
            $entity = $this->newElement();
        } else {
            $groups[] = 'Edit';
        }

        $form = $this->getFormFactory()
            ->create(
                $this->getFormClass(),
                $entity, [
                    'validation_groups' => $groups,
                    'csrf_protection' => false
                ]
            );

        return $form;
    }

    /**
     * @param FormInterface      $form
     * @param UserInterface|null $user
     * @return mixed|null
     */
    public function processForm(FormInterface $form, UserInterface $user = null)
    {
        if ($form->isValid() && $form->isSubmitted()) {

            $entity = $form->getData();

            if ($entity instanceof OwnableInterface AND $user !== null AND $entity->getOwner() === null) {
                $entity->setOwner($user);
            }

            $this->persist($entity);
            $this->flush();

            return $entity;
        }
        return null;
    }

    /**
     * @param $id
     * @return ResourceManagerInterface|null
     */
    public function getResourceManager($id)
    {
        try {
            return $this->container->get(ResourceManagerFactory::class)->get($id);
        } catch (ResourceManagerFactoryException $exception) {
            return null;
        }
    }

    /**
     * @param $id
     * @return bool
     */
    public function resourceManagerExists($id)
    {
        return $this->container->get(ResourceManagerFactory::class)->has($id);
    }

    /**
     * @param $id
     * @return object
     */
    public function get($id)
    {
        return $this->container->get($id);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getParameter(string $name)
    {
        return $this->container->getParameter($name);
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return 'id';
    }
}