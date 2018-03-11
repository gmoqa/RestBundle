<?php

namespace MNC\Bundle\RestBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Interface ResourceManagerInterface
 * @package MNC\Bundle\RestBundle\Manager
 * @author Matías Navarro Carter <mnavarro@option.cl>
 */
interface ResourceManagerInterface extends ObjectManager
{
    /**
     * Creates an instance of the entity the manager is associated with.
     * @return object
     */
    public function create();
    /**
     * Returns the FormFactory service.
     * @return FormFactoryInterface
     */
    public function getFormFactory() : FormFactoryInterface;

    /**
     * Returns Doctrine's Entity Manager.
     * @return ObjectManager
     */
    public function getEntityManager( ) : ObjectManager;

    /**
     * @return string
     */
    public function getEntityClass();

    /**
     * @return string
     */
    public function getTransformerClass();

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * Creates a form for the given entity.
     * @param null $entity
     * @return FormInterface
     */
    public function createForm($entity = null) : FormInterface;

    /**
     * Process and validates a form.
     * @param FormInterface      $form
     * @param UserInterface|null $user
     * @return mixed
     */
    public function processForm(FormInterface $form, UserInterface $user = null);

    /**
     * This method gets a collection of resources. You can pass it an array of
     * filters to be created.
     * It must return an instance of QueryBuilder.
     * @param Request $request
     * @param array   $filters
     * @return QueryBuilder
     */
    public function indexResource(Request $request, $filters = []) : QueryBuilder;

    /**
     * Shows a resource based on it's identifier.
     * @param      $value
     * @param bool $justOne
     * @throws ResourceManagerException When asking multiple resources when just
     *                                  one option is enabled.
     * @return mixed
     */
    public function showResource($value, bool $justOne = false);
}