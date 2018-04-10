<?php

namespace MNC\Bundle\RestBundle\Manager;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use League\Fractal\TransformerAbstract;
use MNC\Bundle\RestBundle\Exception\ResourceException;
use MNC\Bundle\RestBundle\Security\OwnableInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * This class is a base class for a resource Manager. It wraps Doctrine's Entity
 * Manager inside it, and also creates some common operations for objects.
 * @package MNC\Bundle\RestBundle\Manager
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
abstract class AbstractResourceManager implements ResourceManagerInterface
{
    use EntityManagerWrapperTrait;

    /**
     * @var string
     */
    protected $entityClass;
    /**
     * @var string
     */
    protected $formClass;
    /**
     * @var string
     */
    protected $transformerClass;
    /**
     * @var string
     */
    protected $identifier;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * AbstractResourceManager constructor.
     * @param ContainerInterface   $container
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        ContainerInterface $container,
        FormFactoryInterface $formFactory
    ) {
        $this->container = $container;
        $this->formFactory = $formFactory;
    }

    /**
     * @inheritdoc
     */
    public function create()
    {
        return new $this->entityClass;
    }

    /**
     * @inheritdoc
     */
    public function getFormFactory() : FormFactoryInterface
    {
        return $this->formFactory;
    }

    /**
     * @inheritdoc
     */
    public function getEntityManager() : ObjectManager
    {
        return $this->getManager();
    }

    /**
     * This method overrides the Entity Manager Method for getting a repository.
     * @param null $className
     * @return ServiceEntityRepository|\Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository($className = null)
    {
        if (!$className) {
            $className = $this->entityClass;
        }
        return $this->getManager()->getRepository($className);
    }

    /**
     * @param null $className
     * @return ServiceEntityRepository|\Doctrine\Common\Persistence\Mapping\ClassMetadata|\Doctrine\Common\Persistence\ObjectRepository
     */
    public function getClassMetadata($className = null)
    {
        if (!$className) {
            $className = $this->entityClass;
        }
        return $this->getManager()->getRepository($className);
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @return string
     */
    public function getTransformerClass()
    {
        return $this->transformerClass;
    }

    /**
     * @return TransformerAbstract|object
     */
    public function getTransformer()
    {
        return $this->container->get($this->transformerClass);
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @inheritdoc
     */
    public function createForm($entity = null) : FormInterface
    {
        $groups = ['Default'];
        if ($entity === null) {
            $groups[] = 'New';
            $entity = $this->create();
        } else {
            $groups[] = 'Edit';
        }

        $form = $this->getFormFactory()->create($this->formClass, $entity, [
            'validation_groups' => $groups,
            'csrf_protection' => false
        ]);

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
     * @inheritdoc
     */
    public function showResource($value, bool $justOne = false)
    {
        if (strpos($value, ',') !== false) {
            if ($justOne) {
                throw ResourceException::cannotRequestMultipleResources();
            }
            $collection = $this->getRepository()->findBy([$this->identifier => explode(',', $value)]);
            if (sizeof($collection) === 0) {
                throw ResourceException::resourcesNotFound($value);
            }
            return $collection;
        }
        $item = $this->getRepository()->{'findOneBy'.ucfirst($this->identifier)}($value);
        if ($item === null) {
            throw ResourceException::resourceNotFound($value);
        }
        return $item;
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
     * @return ManagerRegistry
     */
    public function getDoctrine()
    {
        return $this->container->get('doctrine');
    }

    /**
     * @return ObjectManager|null
     */
    public function getManager()
    {
        return $this->getDoctrine()->getManagerForClass($this->entityClass);
    }
}