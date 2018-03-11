<?php

namespace MNC\Bundle\RestBundle\Manager;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use MNC\Bundle\RestBundle\Security\OwnableInterface;
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
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var ServiceEntityRepository
     */
    private $repository;

    public function __construct(ServiceEntityRepository $repository, ManagerRegistry $registry, FormFactoryInterface $formFactory)
    {
        $this->repository = $repository;
        $this->formFactory = $formFactory;
        $this->entityClass = $this->repository->getClassName();
        $this->em = $registry->getManagerForClass($this->entityClass);
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
        return $this->em;
    }

    /**
     * This method overrides the Entity Manager Method for getting a repository.
     * @param null $className
     * @return ServiceEntityRepository|\Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository($className = null)
    {
        if (!$className) {
            return $this->repository;
        }
        return $this->em->getRepository($className);
    }

    /**
     * @param null $className
     * @return ServiceEntityRepository|\Doctrine\Common\Persistence\Mapping\ClassMetadata|\Doctrine\Common\Persistence\ObjectRepository
     */
    public function getClassMetadata($className = null)
    {
        if (!$className) {
            return $this->repository;
        }
        return $this->em->getRepository($className);
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }

    public function getTransformerClass()
    {
        return $this->transformerClass;
    }

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
                throw ResourceManagerException::cannotShowMultipleResources();
            }
            return $this->repository->findBy([$this->identifier => explode(',', $value)]);
        }
        return $this->repository->{'findOneBy'.ucfirst($this->identifier)}($value);
    }
}